<?php

namespace Database\Seeders\Modules;

use App\Enums\CouponStatusEnum;
use App\Enums\CouponTypeEnum;
use App\Models\Coupon;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantIds = Restaurant::query()->pluck('id')->all();

        for ($i = 1; $i <= 50; $i++) {
            Coupon::query()->updateOrCreate(
                ['code' => 'SAVE'.str_pad((string) $i, 3, '0', STR_PAD_LEFT)],
                [
                    'restaurant_id' => fake()->randomElement($restaurantIds),
                    'type' => fake()->randomElement(CouponTypeEnum::cases()),
                    'value' => fake()->numberBetween(10, 250),
                    'min_order_amount' => fake()->numberBetween(100, 1000),
                    'start_date' => now()->subDays(fake()->numberBetween(1, 30))->toDateString(),
                    'end_date' => now()->addDays(fake()->numberBetween(30, 120))->toDateString(),
                    'status' => fake()->randomElement(CouponStatusEnum::cases()),
                ]
            );
        }
    }
}
