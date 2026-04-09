<?php

namespace Database\Seeders\Modules;

use App\Enums\DeliveryBoyStatusEnum;
use App\Models\DeliveryBoy;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class DeliveryBoySeeder extends Seeder
{
    public function run(): void
    {
        $restaurantIds = Restaurant::query()->pluck('id')->all();
        $avatarImages = collect(range(1, 24))
            ->map(fn (int $index): string => sprintf('admin/assets/img/avatar/avatar-%d.jpg', $index))
            ->all();

        for ($i = 1; $i <= 50; $i++) {
            DeliveryBoy::query()->updateOrCreate(
                ['phone' => '6'.str_pad((string) $i, 9, '0', STR_PAD_LEFT)],
                [
                    'restaurant_id' => fake()->randomElement($restaurantIds),
                    'name' => fake()->name(),
                    'vehicle_no' => 'DL'.fake()->numberBetween(10, 99).'AB'.fake()->numberBetween(1000, 9999),
                    'image' => fake()->randomElement($avatarImages),
                    'status' => fake()->randomElement(DeliveryBoyStatusEnum::cases()),
                ]
            );
        }
    }
}
