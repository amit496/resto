<?php

namespace Database\Seeders\Modules;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Models\Customer;
use App\Models\DeliveryBoy;
use App\Models\FoodOrder;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::query()->with('branches')->get();

        for ($i = 1; $i <= 70; $i++) {
            $restaurant = $restaurants->random();
            $products = Product::query()->where('restaurant_id', $restaurant->id)->with('variants')->inRandomOrder()->take(3)->get();
            $customer = Customer::query()->where('restaurant_id', $restaurant->id)->inRandomOrder()->first();
            $deliveryBoy = DeliveryBoy::query()->where('restaurant_id', $restaurant->id)->inRandomOrder()->first();
            $branch = $restaurant->branches->isNotEmpty() ? $restaurant->branches->random() : null;
            $orderType = fake()->randomElement(OrderTypeEnum::cases());
            $staffUser = $branch
                ? User::query()
                    ->where('branch_id', $branch->id)
                    ->inRandomOrder()
                    ->first()
                : null;
            $orderSource = fake()->randomElement(['online', 'pos']);

            if ($products->isEmpty()) {
                continue;
            }

            $order = FoodOrder::query()->updateOrCreate(
                ['order_no' => 'ORD'.str_pad((string) $i, 5, '0', STR_PAD_LEFT)],
                [
                    'restaurant_id' => $restaurant->id,
                    'branch_id' => $branch?->id,
                    'customer_id' => $customer?->id,
                    'created_by' => $orderSource === 'pos' ? $staffUser?->id : null,
                    'delivery_boy_id' => $orderType === OrderTypeEnum::DELIVERY ? $deliveryBoy?->id : null,
                    'order_type' => $orderType,
                    'order_source' => $orderSource,
                    'status' => fake()->randomElement(OrderStatusEnum::cases()),
                    'notes' => fake()->boolean(35) ? fake()->sentence() : null,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                    'bill_no' => $orderSource === 'pos' ? 'BILL-'.strtoupper((string) str()->random(8)) : null,
                    'bill_status' => $orderSource === 'pos' ? 'billed' : 'unbilled',
                    'billed_at' => $orderSource === 'pos' ? now()->subMinutes(fake()->numberBetween(10, 500)) : null,
                    'billed_by' => $orderSource === 'pos' ? $staffUser?->id : null,
                ]
            );

            $order->items()->delete();
            $subtotal = 0;
            foreach ($products as $product) {
                $variant = $product->variants->isNotEmpty() ? $product->variants->random() : null;
                $price = $variant?->price ?? $product->base_price;
                $qty = fake()->numberBetween(1, 3);
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant?->id,
                    'product_name_snapshot' => $product->name,
                    'unit_price' => $price,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => round($subtotal * 0.05, 2),
                'discount_amount' => 0,
                'total_amount' => round($subtotal * 1.05, 2),
            ]);
        }
    }
}
