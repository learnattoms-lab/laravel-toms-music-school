<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\StripeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Webhook Controller
 *
 * Handles webhook events from Stripe and other services.
 */
class WebhookController extends Controller
{
    private StripeServiceInterface $stripeService;

    public function __construct(StripeServiceInterface $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhooks.
     */
    public function stripe(Request $request): JsonResponse
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('Stripe-Signature');

            if (!$signature) {
                Log::error('Stripe webhook signature missing');
                return response()->json([
                    'message' => 'Signature missing',
                ], 400);
            }

            // Verify webhook signature
            $event = $this->stripeService->verifyWebhookSignature($payload, $signature);

            // Handle the event
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->stripeService->handleCheckoutSessionCompleted($event->data->object);
                    break;

                case 'payment_intent.succeeded':
                    $this->stripeService->handlePaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->stripeService->handlePaymentIntentFailed($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->stripeService->handleChargeRefunded($event->data->object);
                    break;

                default:
                    Log::info('Received unhandled Stripe event', [
                        'event_type' => $event->type,
                    ]);
            }

            Log::info('Stripe webhook processed successfully', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            return response()->json([
                'message' => 'Webhook received',
            ]);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Invalid Stripe webhook signature', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Invalid signature',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Failed to process Stripe webhook', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

