<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\Interfaces\StripeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Checkout Controller
 *
 * Handles payment processing and checkout operations.
 */
class CheckoutController extends Controller
{
    private StripeServiceInterface $stripeService;
    private CourseRepositoryInterface $courseRepository;
    private OrderRepositoryInterface $orderRepository;
    private EnrollmentRepositoryInterface $enrollmentRepository;

    public function __construct(
        StripeServiceInterface $stripeService,
        CourseRepositoryInterface $courseRepository,
        OrderRepositoryInterface $orderRepository,
        EnrollmentRepositoryInterface $enrollmentRepository
    ) {
        $this->stripeService = $stripeService;
        $this->courseRepository = $courseRepository;
        $this->orderRepository = $orderRepository;
        $this->enrollmentRepository = $enrollmentRepository;
    }

    /**
     * Start checkout process for a course.
     */
    public function start(Request $request, int $courseId): JsonResponse
    {
        try {
            $user = $request->user();
            $course = $this->courseRepository->find($courseId);

            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }

            // Check if user is already enrolled
            if ($this->enrollmentRepository->isEnrolled($user, $course)) {
                return response()->json([
                    'message' => 'You are already enrolled in this course',
                ], 422);
            }

            // Validate course is published
            if (!$course->isPublished()) {
                return response()->json([
                    'message' => 'This course is not available for purchase',
                ], 422);
            }

            // Generate success and cancel URLs
            $successUrl = config('app.frontend_url', config('app.url')) . '/checkout/success?session_id={CHECKOUT_SESSION_ID}&course_id=' . $courseId;
            $cancelUrl = config('app.frontend_url', config('app.url')) . '/checkout/cancel?course_id=' . $courseId;

            // Create Stripe checkout session
            $checkoutSession = $this->stripeService->createCheckoutSession(
                $course,
                $user,
                $successUrl,
                $cancelUrl
            );

            // Create order record
            $order = $this->stripeService->createOrder($checkoutSession, $user, $course);

            Log::info('Checkout session created', [
                'order_id' => $order->id,
                'session_id' => $checkoutSession->id,
                'user_id' => $user->id,
                'course_id' => $courseId,
            ]);

            return response()->json([
                'message' => 'Checkout session created',
                'checkout_url' => $checkoutSession->url,
                'session_id' => $checkoutSession->id,
                'order' => new OrderResource($order),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create checkout session', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'course_id' => $courseId,
            ]);

            return response()->json([
                'message' => 'Failed to create checkout session',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle successful checkout.
     */
    public function success(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'session_id' => ['required', 'string'],
                'course_id' => ['required', 'integer'],
            ]);

            $sessionId = $request->input('session_id');
            $courseId = $request->input('course_id');

            // Retrieve checkout session from Stripe
            $checkoutSession = $this->stripeService->retrieveCheckoutSession($sessionId);

            if ($checkoutSession->payment_status !== 'paid') {
                return response()->json([
                    'message' => 'Payment not completed',
                ], 422);
            }

            // Find the order
            $order = $this->orderRepository->findByStripeSessionId($sessionId);

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found',
                ], 404);
            }

            // Verify course matches
            if ($order->course_id != $courseId) {
                return response()->json([
                    'message' => 'Order course mismatch',
                ], 422);
            }

            // Update order status
            $this->orderRepository->update($order, [
                'status' => 'paid',
                'stripe_payment_intent_id' => $checkoutSession->payment_intent,
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

                Log::info('Enrollment created after payment', [
                    'order_id' => $order->id,
                ]);
            }

            return response()->json([
                'message' => 'Payment successful! You are now enrolled in the course.',
                'order' => new OrderResource($order),
                'enrollment' => $existingEnrollment ?? $this->enrollmentRepository->findByStudentAndCourse($order->user, $order->course),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process successful checkout', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to process payment confirmation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle cancelled checkout.
     */
    public function cancel(Request $request): JsonResponse
    {
        try {
            $courseId = $request->input('course_id');

            return response()->json([
                'message' => 'Checkout was cancelled',
                'course_id' => $courseId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process cancelled checkout', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error processing cancellation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

