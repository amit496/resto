<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Payment;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $setting = AppSetting::query()->first();
        $gateway = PaymentGatewayCatalog::gatewaySettings($setting, 'razorpay');
        $secret = (string) ($gateway['credentials']['webhook_secret'] ?? '');

        $signature = (string) $request->header('X-Razorpay-Signature', '');
        $payload = $request->getContent();

        if ($secret === '' || $signature === '') {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        $computed = hash_hmac('sha256', $payload, $secret);
        if (! hash_equals($computed, $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 400);
        }

        $event = json_decode($payload, true);
        if (! is_array($event) || empty($event['event'])) {
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        $eventId = (string) ($event['id'] ?? '');
        $eventId = $eventId !== '' ? $eventId : sha1($payload);
        $inserted = DB::table('payment_webhook_events')->insertOrIgnore([
            'gateway_code' => 'razorpay',
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

        $eventType = (string) ($event['event'] ?? '');
        $entity = $event['payload']['payment_link']['entity'] ?? null;
        if (! is_array($entity)) {
            return response()->json(['received' => true]);
        }

        $paymentLinkId = (string) ($entity['id'] ?? '');
        $status = (string) ($entity['status'] ?? '');
        $reference = (string) ($entity['reference_id'] ?? '');

        if ($paymentLinkId === '') {
            return response()->json(['message' => 'Missing payment link id.'], 422);
        }

        $payment = Payment::query()
            ->where('gateway_code', 'razorpay')
            ->where(function ($query) use ($paymentLinkId, $reference) {
                $query->where('transaction_ref', $paymentLinkId)
                    ->orWhere('gateway_payload->payment_link_id', $paymentLinkId);

                if ($reference !== '') {
                    $query->orWhereHas('order', fn ($order) => $order->where('order_no', $reference));
                }
            })
            ->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment not found.'], 404);
        }

        DB::table('payment_webhook_events')
            ->where('gateway_code', 'razorpay')
            ->where('event_id', $eventId)
            ->update(['payment_id' => $payment->id, 'updated_at' => now()]);

        return DB::transaction(function () use ($payment, $eventType, $status, $paymentLinkId, $event) {
            if (in_array($status, ['paid', 'completed'], true) || $eventType === 'payment_link.paid') {
                $payment->forceFill([
                    'status' => PaymentStatusEnum::PAID->value,
                    'paid_at' => $payment->paid_at ?? now(),
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'webhook_event' => $eventType,
                        'payment_link_id' => $paymentLinkId,
                        'status' => $status,
                    ]),
                ])->save();

                $order = $payment->order;
                if ($order && (($order->status?->value ?? $order->status) === OrderStatusEnum::PENDING->value)) {
                    $order->status = OrderStatusEnum::PREPARING->value;
                    $order->save();
                }

                return response()->json(['received' => true]);
            }

            if (in_array($status, ['cancelled', 'expired', 'failed'], true)) {
                $payment->forceFill([
                    'status' => PaymentStatusEnum::FAILED->value,
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'webhook_event' => $eventType,
                        'payment_link_id' => $paymentLinkId,
                        'status' => $status,
                    ]),
                ])->save();
            } else {
                $payment->forceFill([
                    'gateway_payload' => array_merge($payment->gateway_payload ?? [], [
                        'webhook_event' => $eventType,
                        'payment_link_id' => $paymentLinkId,
                        'status' => $status,
                    ]),
                ])->save();
            }

            return response()->json(['received' => true]);
        });
    }
}

