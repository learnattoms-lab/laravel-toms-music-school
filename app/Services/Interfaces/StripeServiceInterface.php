<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;

/**
 * Stripe Service Interface
 *
 * Defines the contract for Stripe payment operations.
 */
interface StripeServiceInterface
{
    /**
     * Create a checkout session for a course.
     *
     * @param Course $course The course to purchase
     * @param User $user The user purchasing the course
     * @param string $successUrl The success redirect URL
     * @param string $cancelUrl The cancel redirect URL
     * @return Session The Stripe checkout session
     */
    public function createCheckoutSession(
        Course $course,
        User $user,
        string $successUrl,
        string $cancelUrl
    ): Session;

    /**
     * Retrieve a checkout session by ID.
     *
     * @param string $sessionId The Stripe checkout session ID
     * @return Session The Stripe checkout session
     */
    public function retrieveCheckoutSession(string $sessionId): Session;

    /**
     * Create an order record for a checkout session.
     *
     * @param Session $checkoutSession The Stripe checkout session
     * @param User $user The user who initiated the checkout
     * @param Course $course The course being purchased
     * @return Order The created order
     */
    public function createOrder(Session $checkoutSession, User $user, Course $course): Order;

    /**
     * Verify webhook signature.
     *
     * @param string $payload The raw webhook payload
     * @param string $signature The Stripe signature header
     * @return \Stripe\Event The verified Stripe event
     * @throws \Stripe\Exception\SignatureVerificationException
     */
    public function verifyWebhookSignature(string $payload, string $signature): \Stripe\Event;

    /**
     * Handle checkout session completed event.
     *
     * @param Session $session The checkout session
     * @return void
     */
    public function handleCheckoutSessionCompleted(Session $session): void;

    /**
     * Handle payment intent succeeded event.
     *
     * @param PaymentIntent $paymentIntent The payment intent
     * @return void
     */
    public function handlePaymentIntentSucceeded(PaymentIntent $paymentIntent): void;

    /**
     * Handle payment intent failed event.
     *
     * @param PaymentIntent $paymentIntent The payment intent
     * @return void
     */
    public function handlePaymentIntentFailed(PaymentIntent $paymentIntent): void;

    /**
     * Handle charge refunded event.
     *
     * @param \Stripe\Charge $charge The charge object
     * @return void
     */
    public function handleChargeRefunded(\Stripe\Charge $charge): void;

    /**
     * Create or retrieve a refund.
     *
     * @param Order $order The order to refund
     * @param int|null $amountCents The amount to refund in cents (null = full refund)
     * @return \Stripe\Refund The refund object
     */
    public function createRefund(Order $order, ?int $amountCents = null): \Stripe\Refund;
}

