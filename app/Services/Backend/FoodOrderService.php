<?php

namespace App\Services\Backend;

use App\Models\FoodOrder;
use App\Models\Product;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FoodOrderService
{
    public function upsert(array $data, ?FoodOrder $order = null): FoodOrder
    {
        return DB::transaction(function () use ($data, $order) {
            $items = Arr::pull($data, 'items', []);
            $payment = Arr::pull($data, 'payment', []);
            $actorId = auth()->id();

            $order ??= new FoodOrder();
            $order->fill($data);

            if (($order->order_type?->value ?? $order->order_type) !== 'delivery') {
                $order->delivery_boy_id = null;
            }

            if (! $order->exists) {
                $order->order_no = 'ORD-'.strtoupper((string) str()->random(8));
                $order->order_source = $order->order_source ?: 'pos';
                $order->created_by = $order->created_by ?: $actorId;
                $order->receipt_token = $order->receipt_token ?: str()->random(40);
            }

            $subtotal = 0;
            foreach ($items as $item) {
                $product = Product::query()->findOrFail($item['product_id']);
                $variant = $product->variants()->find($item['product_variant_id'] ?? null);
                $price = $variant?->price ?? $product->base_price;
                $subtotal += $price * $item['quantity'];
            }

            $order->subtotal = $subtotal;
            $order->tax_amount = 0;
            $order->discount_amount = 0;
            $order->total_amount = $subtotal;
            $order->save();

            $order->items()->delete();
            foreach ($items as $item) {
                $product = Product::query()->findOrFail($item['product_id']);
                $variant = $product->variants()->find($item['product_variant_id'] ?? null);
                $price = $variant?->price ?? $product->base_price;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name_snapshot' => $product->name,
                    'unit_price' => $price,
                    'quantity' => $item['quantity'],
                    'line_total' => $price * $item['quantity'],
                ]);
            }

            if (! empty($payment)) {
                $gatewayCode = $payment['gateway_code'] ?? $order->payment_gateway ?? null;
                $method = $gatewayCode
                    ? PaymentGatewayCatalog::methodFor((string) $gatewayCode)
                    : ($payment['method'] ?? 'cash');

                $order->payments()->updateOrCreate(
                    ['food_order_id' => $order->id],
                    [
                        'method' => $method,
                        'gateway_code' => $gatewayCode,
                        'gateway_country' => $payment['gateway_country'] ?? $order->billing_country,
                        'gateway_payload' => $payment['gateway_payload'] ?? null,
                        'status' => $payment['status'] ?? 'pending',
                        'amount' => $payment['amount'] ?? $order->total_amount,
                        'transaction_ref' => $payment['transaction_ref'] ?? null,
                        'paid_at' => ($payment['status'] ?? null) === 'paid' ? now() : null,
                    ]
                );

                if ($gatewayCode) {
                    $order->payment_gateway = $gatewayCode;
                    $order->save();
                }
            }

            if (($payment['status'] ?? null) === 'paid' && empty($order->bill_no)) {
                $order->bill_no = 'BILL-'.strtoupper((string) str()->random(8));
                $order->bill_status = 'billed';
                $order->billed_at = now();
                $order->billed_by = $actorId ?: $order->billed_by;
                $order->save();
            }

            return $order->fresh(['items', 'payments']);
        });
    }
}

