<?php

namespace App\Services\Frontend;

use App\Models\AppSetting;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CartService
{
    private const SESSION_KEY = 'frontend_cart';
    private const META_KEY = 'frontend_cart_meta';

    public function items(): Collection
    {
        $items = collect(session(self::SESSION_KEY, []));

        return $items->map(function (array $item) {
            $product = Product::query()->with('variants')->find($item['product_id']);
            if (! $product) {
                return null;
            }

            $variant = $product->variants->firstWhere('id', $item['product_variant_id'] ?? null);
            $unitPrice = (float) ($variant?->price ?? $product->base_price);
            $quantity = max(1, (int) $item['quantity']);

            return [
                'key' => $item['key'],
                'product' => $product,
                'variant' => $variant,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * $quantity, 2),
            ];
        })->filter()->values();
    }

    public function summary(?AppSetting $setting = null, ?Customer $customer = null, ?string $orderType = null): array
    {
        $items = $this->items();
        $subtotal = (float) $items->sum('line_total');
        $meta = $this->meta();
        $couponCode = $meta['coupon_code'] ?? null;
        if ($setting && $setting->enable_coupons === false) {
            $couponCode = null;
        }
        $couponCheck = $this->resolveCoupon($couponCode, $subtotal);
        $couponDiscount = $couponCheck['discount'];

        $meta = $setting?->meta ?? [];
        $taxPercent = (float) ($setting?->tax_percent ?? 0);
        $taxAmount = round($subtotal * $taxPercent / 100, 2);
        $serviceCharge = (float) ($setting?->service_charge ?? 0);
        $packingFee = (float) ($setting?->packing_fee ?? 0);
        $deliveryFee = $orderType === 'delivery' ? (float) ($setting?->delivery_fee ?? 0) : 0;

        $loyaltyBalance = $customer ? $this->loyaltyBalance($customer->id) : 0;
        $requestedPoints = max(0, (int) ($meta['loyalty_points'] ?? 0));
        $usablePoints = min($requestedPoints, $loyaltyBalance);
        $pointValue = (float) (($meta['loyalty_point_value'] ?? null) ?: 1);
        $loyaltyDiscount = round(min($usablePoints * $pointValue, max(0, $subtotal - $couponDiscount)), 2);
        $discountAmount = round($couponDiscount + $loyaltyDiscount, 2);
        $total = round(max(0, $subtotal + $taxAmount + $serviceCharge + $packingFee + $deliveryFee - $discountAmount), 2);

        return [
            'items' => $items,
            'count' => (int) $items->sum('quantity'),
            'subtotal' => round($subtotal, 2),
            'tax_percent' => $taxPercent,
            'tax_amount' => $taxAmount,
            'service_charge' => round($serviceCharge, 2),
            'packing_fee' => round($packingFee, 2),
            'delivery_fee' => round($deliveryFee, 2),
            'coupon' => $couponCheck['coupon'],
            'coupon_code' => $meta['coupon_code'] ?? null,
            'coupon_discount' => round($couponDiscount, 2),
            'coupon_error' => $couponCheck['error'],
            'loyalty_balance' => $loyaltyBalance,
            'loyalty_points_used' => $usablePoints,
            'loyalty_discount' => $loyaltyDiscount,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ];
    }

    public function add(Product $product, ?int $variantId, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);
        $key = $this->makeKey($product->id, $variantId);
        $items = collect(session(self::SESSION_KEY, []))->keyBy('key');

        $existing = $items->get($key);
        if ($existing) {
            $existing['quantity'] = max(1, (int) $existing['quantity'] + $quantity);
            $items->put($key, $existing);
        } else {
            $items->put($key, [
                'key' => $key,
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        session([self::SESSION_KEY => $items->values()->all()]);
    }

    public function update(string $itemKey, int $quantity): void
    {
        $items = collect(session(self::SESSION_KEY, []))->keyBy('key');
        if (! $items->has($itemKey)) {
            return;
        }

        if ($quantity <= 0) {
            $items->forget($itemKey);
        } else {
            $item = $items->get($itemKey);
            $item['quantity'] = $quantity;
            $items->put($itemKey, $item);
        }

        session([self::SESSION_KEY => $items->values()->all()]);
    }

    public function remove(string $itemKey): void
    {
        $items = collect(session(self::SESSION_KEY, []))
            ->reject(fn (array $item) => $item['key'] === $itemKey)
            ->values()
            ->all();

        session([self::SESSION_KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
        session()->forget(self::META_KEY);
    }

    private function makeKey(int $productId, ?int $variantId): string
    {
        return $productId.':'.($variantId ?: 'base');
    }

    public function applyCoupon(string $code, float $subtotal): array
    {
        $code = strtoupper(trim($code));
        $result = $this->resolveCoupon($code, $subtotal);
        if ($result['coupon']) {
            $meta = $this->meta();
            $meta['coupon_code'] = $code;
            $meta['coupon_id'] = $result['coupon']->id;
            session([self::META_KEY => $meta]);
        }

        return $result;
    }

    public function clearCoupon(): void
    {
        $meta = $this->meta();
        $meta['coupon_code'] = null;
        $meta['coupon_id'] = null;
        session([self::META_KEY => $meta]);
    }

    public function setLoyaltyPoints(int $points): void
    {
        $meta = $this->meta();
        $meta['loyalty_points'] = max(0, $points);
        session([self::META_KEY => $meta]);
    }

    public function clearLoyaltyPoints(): void
    {
        $meta = $this->meta();
        $meta['loyalty_points'] = 0;
        session([self::META_KEY => $meta]);
    }

    public function meta(): array
    {
        return session(self::META_KEY, [
            'coupon_code' => null,
            'coupon_id' => null,
            'loyalty_points' => 0,
        ]);
    }

    private function resolveCoupon(?string $code, float $subtotal): array
    {
        if (! $code) {
            return ['coupon' => null, 'discount' => 0, 'error' => null];
        }

        $coupon = Coupon::query()
            ->where('code', strtoupper($code))
            ->where('status', 'active')
            ->first();

        if (! $coupon) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Invalid coupon code.'];
        }

        $now = now()->toDateString();
        if ($coupon->start_date && $coupon->start_date->toDateString() > $now) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Coupon is not active yet.'];
        }
        if ($coupon->end_date && $coupon->end_date->toDateString() < $now) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Coupon has expired.'];
        }
        if ($subtotal < (float) $coupon->min_order_amount) {
            return ['coupon' => null, 'discount' => 0, 'error' => 'Order total does not meet the coupon minimum.'];
        }

        $discount = $coupon->type->value === 'percent'
            ? round($subtotal * ((float) $coupon->value) / 100, 2)
            : round((float) $coupon->value, 2);

        return [
            'coupon' => $coupon,
            'discount' => min($discount, $subtotal),
            'error' => null,
        ];
    }

    private function loyaltyBalance(int $customerId): int
    {
        $row = DB::table('loyalty_transactions')
            ->selectRaw("SUM(CASE WHEN type IN ('credit','adjustment') THEN points ELSE 0 END) - SUM(CASE WHEN type = 'debit' THEN points ELSE 0 END) as balance")
            ->where('customer_id', $customerId)
            ->first();

        return (int) ($row->balance ?? 0);
    }
}

