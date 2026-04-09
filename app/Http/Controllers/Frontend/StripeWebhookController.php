<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Services\Payments\StripeGatewayService;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeWebhookController extends Controller
{
    public function __construct(private readonly StripeGatewayService $stripeGatewayService)
    {
    }

    public function handle(Request $request): JsonResponse
    {
        $setting = AppSetting::query()->first();
        $gateway = PaymentGatewayCatalog::gatewaySettings($setting, 'stripe');
        $secret = (string) ($gateway['credentials']['webhook_secret'] ?? '');
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        if (! $this->stripeGatewayService->verifyWebhookSignature($payload, $signature, $secret)) {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        $event = json_decode($payload, true);
        if (! is_array($event) || empty($event['type'])) {
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        $eventId = (string) ($event['id'] ?? '');
        if ($eventId !== '') {
            $inserted = DB::table('payment_webhook_events')->insertOrIgnore([
                'gateway_code' => 'stripe',
                'event_id' => $eventId,
                'payment_id' => null,
                'payload' => json_encode($event),
                'received_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            if (! $inserted) {
                return response()->json(['received' => true]);
            }
        }

        $type = (string) $event['type'];
        $object = $event['data']['object'] ?? [];

        return match ($type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($object),
            'checkout.session.async_payment_failed', 'payment_intent.payment_failed' => $this->handlePaymentFailed($object, $type),
            default => response()->json(['received' => true]),
        };
    }

    private function handleCheckoutCompleted(array $object): JsonResponse
    {
        $sessionId = (string) ($object['id'] ?? '');
        $paymentIntentId = (string) ($object['payment_intent'] ?? '');
        if ($sessionId === '') {
            return response()->json(['message' => 'Missing session id.'], 422);
        }

        $payment = Payment::query()
            ->where('stripe_checkout_session_id', $sessionId)
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        $eventId = (string) data_get($object, 'metadata.webhook_event_id', '');
        if ($eventId === '') {
            // keep existing row if inserted at top-level
        } else {
            DB::table('payment_webhook_events')
                ->where('gateway_code', 'stripe')
                ->where('event_id', $eventId)
                ->update(['payment_id' => $payment->id, 'updated_at' => now()]);
        }

        return DB::transaction(function () use ($payment, $sessionId, $paymentIntentId) {
            $payment->forceFill([
                'status' => PaymentStatusEnum::PAID->value,
                'paid_at' => $payment->paid_at ?? now(),
                'transaction_ref' => $paymentIntentId ?: $payment->transaction_ref,
                'stripe_payment_intent_id' => $paymentIntentId ?: $payment->stripe_payment_intent_id,
                'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                    'checkout_session_id' => $sessionId,
                    'payment_intent_id' => $paymentIntentId ?: $payment->stripe_payment_intent_id,
                    'webhook_event' => 'checkout.session.completed',
                    'paid_at' => now()->toIso8601String(),
                ]),
            ])->save();

            $order = $payment->order;
            if ($order && (($order->status?->value ?? $order->status) === OrderStatusEnum::PENDING->value)) {
                $order->status = OrderStatusEnum::PREPARING->value;
                $order->save();
            }

            return response()->json(['received' => true]);
        });
    }

    private function handlePaymentFailed(array $object, string $eventType): JsonResponse
    {
        $sessionId = (string) data_get($object, 'id', '');
        $paymentIntentId = (string) data_get($object, 'payment_intent', '');

        $payment = Payment::query()
            ->when($eventType === 'checkout.session.async_payment_failed' && $sessionId !== '', fn ($query) => $query->where('stripe_checkout_session_id', $sessionId))
            ->when($eventType === 'payment_intent.payment_failed' && $sessionId !== '', fn ($query) => $query->where('stripe_payment_intent_id', $sessionId))
            ->when($paymentIntentId !== '', fn ($query) => $query->orWhere('stripe_payment_intent_id', $paymentIntentId))
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        $payment->forceFill([
            'status' => PaymentStatusEnum::FAILED->value,
            'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                'webhook_event' => 'payment_failed',
                'payment_intent_id' => $paymentIntentId ?: ($payment->stripe_payment_intent_id ?? null),
            ]),
        ])->save();

        return response()->json(['received' => true]);
    }
}
