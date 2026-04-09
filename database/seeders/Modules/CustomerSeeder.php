<?php

namespace Database\Seeders\Modules;

use App\Models\Customer;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantIds = Restaurant::query()->pluck('id')->all();
        $localities = ['Hazratganj', 'Gomti Nagar', 'Indira Nagar', 'Aliganj', 'Rajajipuram', 'Civil Lines', 'Nehru Place', 'Sector 18', 'Bandra West', 'Koramangala'];
        $cities = ['Lucknow', 'Noida', 'Delhi', 'Kanpur', 'Mumbai', 'Bhopal', 'Jaipur', 'Indore', 'Pune', 'Bengaluru'];

        for ($i = 1; $i <= 120; $i++) {
            $name = fake()->name();

            Customer::query()->updateOrCreate(
                ['email' => "customer{$i}@demo.test"],
                [
                    'restaurant_id' => fake()->randomElement($restaurantIds),
                    'name' => $name,
                    'phone' => (string) fake()->numberBetween(7000000000, 9999999999),
                    'image' => 'https://i.pravatar.cc/400?img='.(($i % 70) + 1),
                    'address' => sprintf(
                        'House %d, %s, %s, %s %s',
                        fake()->numberBetween(10, 999),
                        fake()->randomElement($localities),
                        fake()->randomElement($cities),
                        fake()->stateAbbr(),
                        fake()->postcode()
                    ),
                    'password' => Hash::make('password'),
                    'email_verified_at' => now()->subDays(fake()->numberBetween(2, 240)),
                    'remember_token' => Str::random(10),
                ]
            );
        }
    }
}
