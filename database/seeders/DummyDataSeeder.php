<?php

namespace Database\Seeders;

use App\Enums\CouponStatusEnum;
use App\Enums\CouponTypeEnum;
use App\Enums\DeliveryBoyStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\RestaurantStatusEnum;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\DeliveryBoy;
use App\Models\FoodOrder;
use App\Models\AppSetting;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $restaurant = Restaurant::query()->firstOrCreate(
            ['slug' => 'demo-restaurant'],
            [
                'name' => 'Demo Multi Restaurant',
                'email' => 'demo@restaurant.test',
                'phone' => '9999999999',
                'address' => 'Demo Address',
                'status' => RestaurantStatusEnum::ACTIVE,
            ]
        );

        $pizzas = $restaurant->categories()->firstOrCreate(
            ['slug' => 'pizzas'],
            ['name' => 'Pizzas', 'status' => RestaurantStatusEnum::ACTIVE]
        );
        $burgers = $restaurant->categories()->firstOrCreate(
            ['slug' => 'burgers'],
            ['name' => 'Burgers', 'status' => RestaurantStatusEnum::ACTIVE]
        );

        $vegSub = $pizzas->subcategories()->firstOrCreate(
            ['slug' => 'veg-pizza'],
            ['name' => 'Veg Pizza', 'status' => RestaurantStatusEnum::ACTIVE]
        );
        $nonVegSub = $pizzas->subcategories()->firstOrCreate(
            ['slug' => 'non-veg-pizza'],
            ['name' => 'Non Veg Pizza', 'status' => RestaurantStatusEnum::ACTIVE]
        );

        $margherita = Product::query()->firstOrCreate(
            ['slug' => 'margherita-pizza'],
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => $pizzas->id,
                'subcategory_id' => $vegSub->id,
                'name' => 'Margherita Pizza',
                'sku' => 'PIZ-MARG',
                'type' => ProductTypeEnum::VEG,
                'base_price' => 199,
                'status' => ProductStatusEnum::ACTIVE,
                'has_variants' => true,
            ]
        );
        $margherita->variants()->updateOrCreate(['name' => 'Size Small'], ['value' => 'Small', 'price' => 199, 'sku' => 'PIZ-MARG-S', 'is_default' => true, 'status' => ProductStatusEnum::ACTIVE]);
        $margherita->variants()->updateOrCreate(['name' => 'Size Medium'], ['value' => 'Medium', 'price' => 299, 'sku' => 'PIZ-MARG-M', 'status' => ProductStatusEnum::ACTIVE]);
        $margherita->variants()->updateOrCreate(['name' => 'Size Large'], ['value' => 'Large', 'price' => 399, 'sku' => 'PIZ-MARG-L', 'status' => ProductStatusEnum::ACTIVE]);

        Product::query()->firstOrCreate(
            ['slug' => 'pepperoni-pizza'],
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => $pizzas->id,
                'subcategory_id' => $nonVegSub->id,
                'name' => 'Pepperoni Pizza',
                'sku' => 'PIZ-PEP',
                'type' => ProductTypeEnum::NON_VEG,
                'base_price' => 349,
                'status' => ProductStatusEnum::ACTIVE,
            ]
        );

        Product::query()->firstOrCreate(
            ['slug' => 'cheese-burger'],
            [
                'restaurant_id' => $restaurant->id,
                'category_id' => $burgers->id,
                'name' => 'Cheese Burger',
                'sku' => 'BRG-CHS',
                'type' => ProductTypeEnum::NON_VEG,
                'base_price' => 149,
                'status' => ProductStatusEnum::ACTIVE,
            ]
        );

        $customer1 = Customer::query()->firstOrCreate(
            ['email' => 'john@example.com'],
            ['restaurant_id' => $restaurant->id, 'name' => 'John Doe', 'phone' => '8888888888', 'address' => 'City Center']
        );
        Customer::query()->firstOrCreate(
            ['email' => 'riya@example.com'],
            ['restaurant_id' => $restaurant->id, 'name' => 'Riya Singh', 'phone' => '7777777777', 'address' => 'Sector 10']
        );

        DeliveryBoy::query()->firstOrCreate(
            ['phone' => '7000000001'],
            ['restaurant_id' => $restaurant->id, 'name' => 'Amit Rider', 'vehicle_no' => 'DL01AB1234', 'status' => DeliveryBoyStatusEnum::ACTIVE]
        );
        DeliveryBoy::query()->firstOrCreate(
            ['phone' => '7000000002'],
            ['restaurant_id' => $restaurant->id, 'name' => 'Rahul Rider', 'vehicle_no' => 'DL01AB5678', 'status' => DeliveryBoyStatusEnum::ACTIVE]
        );

        Coupon::query()->firstOrCreate(
            ['code' => 'WELCOME50'],
            [
                'restaurant_id' => $restaurant->id,
                'type' => CouponTypeEnum::PERCENT,
                'value' => 50,
                'min_order_amount' => 300,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(2)->toDateString(),
                'status' => CouponStatusEnum::ACTIVE,
            ]
        );

        Coupon::query()->firstOrCreate(
            ['code' => 'FREEDEL'],
            [
                'restaurant_id' => $restaurant->id,
                'type' => CouponTypeEnum::FIXED,
                'value' => 40,
                'min_order_amount' => 249,
                'start_date' => now()->subDays(2)->toDateString(),
                'end_date' => now()->addMonths(1)->toDateString(),
                'status' => CouponStatusEnum::ACTIVE,
            ]
        );

        Coupon::query()->firstOrCreate(
            ['code' => 'COMBO25'],
            [
                'restaurant_id' => $restaurant->id,
                'type' => CouponTypeEnum::PERCENT,
                'value' => 25,
                'min_order_amount' => 499,
                'start_date' => now()->subDays(1)->toDateString(),
                'end_date' => now()->addMonths(2)->toDateString(),
                'status' => CouponStatusEnum::ACTIVE,
            ]
        );

        AppSetting::query()->updateOrCreate(
            ['restaurant_id' => null],
            [
                'meta' => [
                    'loyalty_point_value' => 1,
                    'loyalty_earn_per_amount' => 100,
                    'home_sliders' => [
                        [
                            'title' => 'Food delivery homepage with slider managed from admin panel.',
                            'description' => 'Desktop/mobile slider content is seeded so the feature shows immediately on the frontend.',
                            'button_label' => 'Order now',
                            'button_url' => '/menu',
                            'desktop_image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=1600&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Promo-first ordering flow with banners, coupons, and category discovery.',
                            'description' => 'This demo seed mirrors app-style campaign sections used by delivery platforms.',
                            'button_label' => 'View promos',
                            'button_url' => '/offers',
                            'desktop_image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=1600&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                    ],
                    'home_banners' => [
                        [
                            'title' => 'Flat delivery discount',
                            'description' => 'Use this banner block for delivery-fee waivers, partner campaigns, or first-order promotions.',
                            'button_label' => 'Use FREEDEL',
                            'button_url' => '/offers',
                            'desktop_image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1200&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'Combo deals and meal bundles',
                            'description' => 'Seeded banner content for high-conversion combo offers similar to aggregator apps.',
                            'button_label' => 'Use COMBO25',
                            'button_url' => '/offers',
                            'desktop_image' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=1200&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                        [
                            'title' => 'First order savings',
                            'description' => 'Promote first-order discounts, free delivery, or branch launch campaigns on the homepage.',
                            'button_label' => 'Use WELCOME50',
                            'button_url' => '/offers',
                            'desktop_image' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=1200&q=80',
                            'mobile_image' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=900&q=80',
                            'is_active' => true,
                        ],
                    ],
                ],
            ]
        );

        $order = FoodOrder::query()->firstOrCreate(
            ['order_no' => 'ORD-DEMO-001'],
            [
                'restaurant_id' => $restaurant->id,
                'customer_id' => $customer1->id,
                'order_type' => OrderTypeEnum::DELIVERY,
                'status' => OrderStatusEnum::DELIVERED,
                'subtotal' => 499,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 499,
            ]
        );

        $order->items()->delete();
        $order->items()->create([
            'product_id' => $margherita->id,
            'product_variant_id' => $margherita->variants()->where('value', 'Medium')->value('id'),
            'product_name_snapshot' => $margherita->name,
            'unit_price' => 299,
            'quantity' => 1,
            'line_total' => 299,
        ]);
        $order->items()->create([
            'product_id' => $margherita->id,
            'product_variant_id' => $margherita->variants()->where('value', 'Small')->value('id'),
            'product_name_snapshot' => $margherita->name,
            'unit_price' => 199,
            'quantity' => 1,
            'line_total' => 199,
        ]);

        $order->payments()->updateOrCreate(
            ['food_order_id' => $order->id],
            [
                'method' => PaymentMethodEnum::UPI,
                'status' => PaymentStatusEnum::PAID,
                'amount' => 499,
                'transaction_ref' => 'TXN-DEMO-001',
                'paid_at' => now(),
            ]
        );
    }
}
