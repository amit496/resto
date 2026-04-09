<?php

namespace App\Services\Frontend;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\FoodOrder;
use App\Models\LoyaltyTransaction;
use App\Models\Restaurant;
use App\Support\CountryCatalog;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FrontendOrderService
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function place(array $data, ?AppSetting $setting = null, ?Customer $authenticatedCustomer = null): FoodOrder
    {
        if (! empty($data['coupon_code'])) {
            $this->cartService->applyCoupon((string) $data['coupon_code'], (float) $this->cartService->summary($setting)['subtotal']);
        }
        if (array_key_exists('loyalty_points', $data)) {
            $this->cartService->setLoyaltyPoints((int) ($data['loyalty_points'] ?? 0));
        }

        $cart = $this->cartService->summary($setting, $authenticatedCustomer, $data['order_type'] ?? OrderTypeEnum::DELIVERY->value);
        abort_if($cart['items']->isEmpty(), 422, 'Cart is empty.');

        return DB::transaction(function () use ($data, $cart, $setting, $authenticatedCustomer) {
            $restaurant = Restaurant::query()->oldest('id')->firstOrFail();
            $authenticatedCustomer = $authenticatedCustomer ?: Auth::guard('customer')->user();

            $customer = ($authenticatedCustomer instanceof Customer)
                ? $authenticatedCustomer
                : Customer::query()->firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $data['name'],
                        'phone' => $data['phone'] ?? null,
                        'address' => $data['address'] ?? null,
                    ]
                );

            $customer->update([
                'restaurant_id' => $customer->restaurant_id ?: $restaurant->id,
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
            ]);

            $selectedAddress = null;
            if (! empty($data['customer_address_id'])) {
                $selectedAddress = CustomerAddress::query()
                    ->where('customer_id', $customer->id)
                    ->whereKey((int) $data['customer_address_id'])
                    ->first();
            }

            $order = FoodOrder::query()->create([
                'restaurant_id' => $restaurant->id,
                'branch_id' => $data['branch_id'],
                'customer_id' => $customer->id,
                'customer_address_id' => $selectedAddress?->id,
                'receipt_token' => Str::random(40),
                'billing_country' => CountryCatalog::normalize($data['billing_country'] ?? null),
                'billing_currency' => $setting?->currency ?: 'INR',
                'payment_gateway' => $data['payment_gateway'] ?? null,
                'delivery_name' => $selectedAddress?->recipient_name ?: ($data['delivery_name'] ?? $data['name']),
                'delivery_phone' => $selectedAddress?->phone ?: ($data['delivery_phone'] ?? ($data['phone'] ?? null)),
                'delivery_address_line_1' => $selectedAddress?->address_line_1 ?: ($data['delivery_address_line_1'] ?? ($data['address'] ?? null)),
                'delivery_address_line_2' => $selectedAddress?->address_line_2 ?: ($data['delivery_address_line_2'] ?? null),
                'delivery_landmark' => $selectedAddress?->landmark ?: ($data['delivery_landmark'] ?? null),
                'delivery_city' => $selectedAddress?->city ?: ($data['delivery_city'] ?? null),
                'delivery_state' => $selectedAddress?->state ?: ($data['delivery_state'] ?? null),
                'delivery_postal_code' => $selectedAddress?->postal_code ?: ($data['delivery_postal_code'] ?? null),
                'delivery_country_code' => $selectedAddress?->country_code ?: CountryCatalog::normalize($data['billing_country'] ?? null),
                'delivery_instructions' => $selectedAddress?->instructions ?: ($data['delivery_instructions'] ?? null),
                'coupon_id' => $cart['coupon']?->id,
                'coupon_code' => $cart['coupon']?->code,
                'order_no' => 'ORD-'.Str::upper(Str::random(8)),
                'order_type' => $data['order_type'] ?? OrderTypeEnum::DELIVERY->value,
                'order_source' => 'online',
                'status' => OrderStatusEnum::PENDING->value,
                'subtotal' => $cart['subtotal'],
                'tax_amount' => $cart['tax_amount'],
                'service_charge' => $cart['service_charge'],
                'delivery_fee' => $cart['delivery_fee'],
                'packing_fee' => $cart['packing_fee'],
                'discount_amount' => $cart['discount_amount'],
                'loyalty_points_used' => $cart['loyalty_points_used'],
                'loyalty_discount_amount' => $cart['loyalty_discount'],
                'total_amount' => $cart['total'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cart['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_variant_id' => $item['variant']?->id,
                    'product_name_snapshot' => $item['product']->name,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'line_total' => $item['line_total'],
                ]);
            }

            $order->payments()->create([
                'method' => $data['payment_method'],
                'gateway_code' => $data['payment_gateway'] ?? null,
                'gateway_country' => CountryCatalog::normalize($data['billing_country'] ?? null),
                'gateway_payload' => ! empty($data['payment_gateway'])
                    ? PaymentGatewayCatalog::receiptPayload(
                        $setting,
                        (string) $data['payment_gateway'],
                        (float) $cart['total'],
                        (string) $order->order_no,
                        $data['billing_country'] ?? null,
                        $setting?->currency ?: 'INR'
                    )
                    : null,
                'status' => PaymentStatusEnum::PENDING->value,
                'amount' => $cart['total'],
                'transaction_ref' => $data['transaction_ref'] ?? null,
                'paid_at' => null,
            ]);

            if ($cart['loyalty_points_used'] > 0) {
                LoyaltyTransaction::query()->create([
                    'customer_id' => $customer->id,
                    'food_order_id' => $order->id,
                    'type' => 'debit',
                    'points' => $cart['loyalty_points_used'],
                    'note' => 'Redeemed at checkout',
                    'created_by' => null,
                ]);
            }

            $meta = $setting?->meta ?? [];
            $earnPerAmount = (float) (($meta['loyalty_earn_per_amount'] ?? null) ?: 100);
            $earnedPoints = (int) floor($cart['subtotal'] / max(1, $earnPerAmount));
            if ($earnedPoints > 0) {
                LoyaltyTransaction::query()->create([
                    'customer_id' => $customer->id,
                    'food_order_id' => $order->id,
                    'type' => 'credit',
                    'points' => $earnedPoints,
                    'note' => 'Earned from order',
                    'created_by' => null,
                ]);
            }

            $this->cartService->clear();

            return $order->fresh(['items', 'payments', 'branch', 'customer']);
        });
    }
}

