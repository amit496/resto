<?php

namespace Database\Seeders\Modules;

use App\Enums\RestaurantStatusEnum;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $singleRestaurantMode = (bool) config('app.single_restaurant_mode', true);

        if ($singleRestaurantMode) {
            $restaurant = Restaurant::query()->updateOrCreate(
                ['slug' => Str::slug('FoodiHub')],
                [
                    'name' => 'FoodiHub',
                    'email' => 'admin@foodihub.test',
                    'phone' => '9000000001',
                    'address' => 'Lucknow, Uttar Pradesh',
                    'status' => RestaurantStatusEnum::ACTIVE,
                ]
            );

            for ($b = 1; $b <= 5; $b++) {
                $restaurant->branches()->updateOrCreate(
                    ['name' => "Branch {$b}"],
                    [
                        'code' => "FDH-B{$b}",
                        'phone' => '8'.str_pad((string) $b, 9, '0', STR_PAD_LEFT),
                        'manager_name' => fake()->name(),
                        'manager_phone' => '9'.str_pad((string) ($b + 10), 9, '0', STR_PAD_LEFT),
                        'manager_email' => "manager{$b}@foodihub.test",
                        'address' => fake()->streetAddress().', Lucknow',
                        'opening_time' => '10:00',
                        'closing_time' => '23:00',
                        'weekly_off' => fake()->randomElement(['Sunday', 'None']),
                        'latitude' => 26.8467000 + ($b / 1000),
                        'longitude' => 80.9462000 + ($b / 1000),
                        'delivery_radius_km' => fake()->randomFloat(2, 2, 12),
                        'gst_no' => strtoupper(Str::random(15)),
                        'fssai_no' => (string) fake()->numerify('##############'),
                        'status' => RestaurantStatusEnum::ACTIVE,
                    ]
                );
            }

            Restaurant::query()
                ->where('id', '!=', $restaurant->id)
                ->delete();

            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $name = "Restaurant {$i}";
            $restaurant = Restaurant::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'email' => "restaurant{$i}@demo.test",
                    'phone' => '9'.str_pad((string) $i, 9, '0', STR_PAD_LEFT),
                    'address' => fake()->address(),
                    'status' => fake()->randomElement(RestaurantStatusEnum::cases()),
                ]
            );

            for ($b = 1; $b <= 2; $b++) {
                $restaurant->branches()->updateOrCreate(
                    ['name' => "Branch {$b}"],
                    [
                        'code' => "R{$restaurant->id}B{$b}",
                        'phone' => '8'.str_pad((string) ($i * 10 + $b), 9, '0', STR_PAD_LEFT),
                        'manager_name' => fake()->name(),
                        'manager_phone' => '9'.str_pad((string) ($i * 10 + $b), 9, '0', STR_PAD_LEFT),
                        'manager_email' => "manager{$restaurant->id}{$b}@demo.test",
                        'address' => fake()->streetAddress(),
                        'opening_time' => '10:00',
                        'closing_time' => '23:00',
                        'weekly_off' => fake()->randomElement(['Sunday', 'None']),
                        'latitude' => fake()->latitude(20, 30),
                        'longitude' => fake()->longitude(70, 90),
                        'delivery_radius_km' => fake()->randomFloat(2, 2, 12),
                        'gst_no' => strtoupper(Str::random(15)),
                        'fssai_no' => (string) fake()->numerify('##############'),
                        'status' => RestaurantStatusEnum::ACTIVE,
                    ]
                );
            }
        }
    }
}
