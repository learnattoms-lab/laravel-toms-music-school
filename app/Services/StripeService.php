<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\Interfaces\StripeServiceInterface;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

/**
 * Stripe Service
 *
 * Handles all Stripe payment operations including checkout sessions,
 * webhooks, and refunds.
 *
 * Note: Requires stripe/stripe-php package
 * Install via: composer require stripe/stripe-php
 */
class StripeService implements StripeServiceInterface
{
    private OrderRepositoryInterface $orderRepository;
    private EnrollmentRepositoryInterface $enrollmentRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        EnrollmentRepositoryInterface $enrollmentRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->enrollmentRepository = $enrollmentRepository;

        // Initialize Stripe with API key from config
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a checkout session for a course.
     */
    public function createCheckoutSession(
        Course $course,
        User $user,
        string $successUrl,
        string $cancelUrl
    ): Session {
        try {
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $course->title,
                            'description' => $course->description ?? '',
                        ],
                        'unit_amount' => $course->price_cents,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'course_id' => (string) $course->id,
                    'user_id' => (string) $user->id,
                ],
            ]);

            Log::info('Stripe checkout session created', [
                'session_id' => $checkoutSession->id,
                'course_id' => $course->id,
                'user_id' => $user->id,
            ]);

            return $checkoutSession;
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe checkout session', [
                'error' => $e->getMessage(),
                'course_id' => $course->id,
                'user_id' => $user->id,
            ]);
            throw new \Exception('Failed to create checkout session: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a checkout session by ID.
     */
    public function retrieveCheckoutSession(string $sessionId): Session
    {
        try {
            return Session::retrieve($sessionId);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Stripe checkout session', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to retrieve checkout session: ' . $e->getMessage());
        }
    }

    /**
     * Create an order record for a checkout session.
     */
    public function createOrder(Session $checkoutSession, User $user, Course $course): Order
    {
        try {
            $order = $this->orderRepository->create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount_cents' => $course->price_cents,
                'currency' => 'usd',
                'status' => 'pending',
                'stripe_session_id' => $checkoutSession->id,
            ]);

            Log::info('Order created for checkout session', [
                'order_id' => $order->id,
                'session_id' => $checkoutSession->id,
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            return $order;
        } catch (\Exception $e) {
            Log::error('Failed to create order', [
                'session_id' => $checkoutSession->id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): \Stripe\Event
    {
        $endpointSecret = config('services.stripe.webhook_secret');

        if (empty($endpointSecret)) {
            Log::error('Stripe webhook secret not configured');
            throw new \Exception('Webhook secret not configured');
        }

        try {
            return Webhook::constructEvent($payload, $signature, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle checkout session completed event.
     */
    public function handleCheckoutSessionCompleted(Session $session): void
    {
        try {
            Log::info('Processing checkout session completed', [
                'session_id' => $session->id,
            ]);

            // Find the order
            $order = $this->orderRepository->findByStripeSessionId($session->id);

            if (!$order) {
                Log::error('Order not found for session', [
                    'session_id' => $session->id,
                ]);
                return;
            }

            // Update order status
            $this->orderRepository->update($order, [
                'status' => 'paid',
                'stripe_payment_intent_id' => $session->payment_intent,
            ]);

            // Create enrollment if it doesn't exist
            $existingEnrollment = $this->enrollmentRepository->findByStudentAndCourse(
                $order->user,
                $order->course
            );

            if (!$existingEnrollment) {
                $this->enrollmentRepository->create([
                    'student_id' => $order->user_id,
                    'course_id' => $order->course_id,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'progress_pct' => 0,
                ]);

                Log::info('Enrollment created from checkout session', [
                    'order_id' => $order->id,
                    'session_id' => $session->id,
                ]);
            }

            Log::info('Order and enrollment processed successfully', [
                'session_id' => $session->id,
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to handle checkout session completed', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle payment intent succeeded event.
     */
    public function handlePaymentIntentSucceeded(PaymentIntent $paymentIntent): void
    {
        try {
            Log::info('Processing payment intent succeeded', [
                'payment_intent_id' => $paymentIntent->id,
            ]);

            // Find the order
            $order = $this->orderRepository->findOneBy([
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);

            if (!$order) {
                Log::error('Order not found for payment intent', [
                    'payment_intent_id' => $paymentIntent->id,
                ]);
                return;
            }

            // Update order status
            $this->orderRepository->update($order, [
                'status' => 'paid',
            ]);

            Log::info('Order status updated to paid', [
                'payment_intent_id' => $paymentIntent->id,
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to handle payment intent succeeded', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle payment intent failed event.
     */
    public function handlePaymentIntentFailed(PaymentIntent $paymentIntent): void
    {
        try {
            Log::info('Processing payment intent failed', [
                'payment_intent_id' => $paymentIntent->id,
            ]);

            // Find the order
            $order = $this->orderRepository->findOneBy([
                'stripe_payment_intent_id' => $paymentIntent->id,
            ]);

            if (!$order) {
                Log::error('Order not found for payment intent', [
                    'payment_intent_id' => $paymentIntent->id,
                ]);
                return;
            }

            // Update order status
            $this->orderRepository->update($order, [
                'status' => 'failed',
                'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Payment failed',
            ]);

            Log::info('Order status updated to failed', [
                'payment_intent_id' => $paymentIntent->id,
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to handle payment intent failed', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle charge refunded event.
     */
    public function handleChargeRefunded(\Stripe\Charge $charge): void
    {
        try {
            Log::info('Processing charge refunded', [
                'charge_id' => $charge->id,
            ]);

            // Find the order by payment intent ID
            $order = $this->orderRepository->findOneBy([
                'stripe_payment_intent_id' => $charge->payment_intent,
            ]);

            if (!$order) {
                Log::error('Order not found for charge', [
                    'charge_id' => $charge->id,
                ]);
                return;
            }

            // Update order status
            $this->orderRepository->update($order, [
                'status' => 'refunded',
            ]);

            // Cancel enrollment if it exists
            $enrollment = $this->enrollmentRepository->findByStudentAndCourse(
                $order->user,
                $order->course
            );

            if ($enrollment) {
                $this->enrollmentRepository->update($enrollment, [
                    'status' => 'cancelled',
                ]);

                Log::info('Enrollment cancelled due to refund', [
                    'enrollment_id' => $enrollment->id,
                ]);
            }

            Log::info('Order status updated to refunded', [
                'charge_id' => $charge->id,
                'order_id' => $order->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to handle charge refunded', [
                'charge_id' => $charge->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create or retrieve a refund.
     */
    public function createRefund(Order $order, ?int $amountCents = null): \Stripe\Refund
    {
        try {
            if (!$order->stripe_payment_intent_id) {
                throw new \Exception('Order does not have a payment intent ID');
            }

            $refundParams = [
                'payment_intent' => $order->stripe_payment_intent_id,
            ];

            if ($amountCents !== null) {
                $refundParams['amount'] = $amountCents;
            }

            $refund = \Stripe\Refund::create($refundParams);

            // Update order status
            $this->orderRepository->update($order, [
                'status' => 'refunded',
            ]);

            Log::info('Refund created', [
                'refund_id' => $refund->id,
                'order_id' => $order->id,
                'amount_cents' => $amountCents ?? $order->amount_cents,
            ]);

            return $refund;
        } catch (\Exception $e) {
            Log::error('Failed to create refund', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to create refund: ' . $e->getMessage());
        }
    }
}

