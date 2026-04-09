<?php

namespace Database\Seeders\Modules;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Database\Seeder;

class CustomerAddressSeeder extends Seeder
{
    public function run(): void
    {
        $countries = ['IN'];
        $cities = ['Lucknow', 'Noida', 'Delhi', 'Kanpur', 'Mumbai', 'Bhopal', 'Jaipur', 'Indore', 'Pune', 'Bengaluru'];
        $areas = ['Hazratganj', 'Gomti Nagar', 'Indira Nagar', 'Aliganj', 'Rajajipuram', 'Civil Lines', 'Nehru Place', 'Sector 18', 'Bandra West', 'Koramangala'];

        Customer::query()
            ->select(['id', 'name', 'phone'])
            ->orderBy('id')
            ->chunk(200, function ($customers) use ($countries, $cities, $areas) {
                foreach ($customers as $customer) {
                    $base = [
                        'recipient_name' => $customer->name,
                        'phone' => $customer->phone,
                        'city' => fake()->randomElement($cities),
                        'state' => fake()->state(),
                        'postal_code' => fake()->postcode(),
                        'country_code' => fake()->randomElement($countries),
                    ];

                    $addresses = [
                        [
                            'type' => 'home',
                            'label' => 'Home',
                            'is_default' => true,
                            'address_line_1' => sprintf('House %d, %s', fake()->numberBetween(10, 999), fake()->randomElement($areas)),
                            'address_line_2' => fake()->boolean(40) ? 'Near '.fake()->company() : null,
                            'landmark' => fake()->boolean(55) ? 'Near '.fake()->streetName() : null,
                            'instructions' => fake()->boolean(35) ? 'Call on arrival, deliver at main gate.' : null,
                        ],
                        [
                            'type' => 'work',
                            'label' => 'Work',
                            'is_default' => false,
                            'address_line_1' => sprintf('Office %d, %s', fake()->numberBetween(1, 200), fake()->randomElement($areas)),
                            'address_line_2' => fake()->boolean(45) ? 'Building '.fake()->buildingNumber() : null,
                            'landmark' => fake()->boolean(55) ? 'Opp. '.fake()->company() : null,
                            'instructions' => fake()->boolean(40) ? 'Reception delivery. Ask for security.' : null,
                        ],
                        [
                            'type' => 'other',
                            'label' => 'Other',
                            'is_default' => false,
                            'address_line_1' => sprintf('Flat %d, %s', fake()->numberBetween(1, 500), fake()->randomElement($areas)),
                            'address_line_2' => fake()->boolean(55) ? fake()->streetAddress() : null,
                            'landmark' => fake()->boolean(55) ? 'Near '.fake()->colorName().' store' : null,
                            'instructions' => fake()->boolean(30) ? 'Leave with neighbor if unavailable.' : null,
                        ],
                    ];

                    foreach ($addresses as $row) {
                        CustomerAddress::query()->updateOrCreate(
                            [
                                'customer_id' => $customer->id,
                                'type' => $row['type'],
                                'label' => $row['label'],
                            ],
                            [
                                ...$base,
                                ...$row,
                            ]
                        );
                    }

                    // Ensure exactly one default address.
                    $default = CustomerAddress::query()
                        ->where('customer_id', $customer->id)
                        ->where('is_default', true)
                        ->orderByDesc('updated_at')
                        ->first();

                    if (! $default) {
                        $default = CustomerAddress::query()
                            ->where('customer_id', $customer->id)
                            ->orderBy('id')
                            ->first();
                        if ($default) {
                            $default->update(['is_default' => true]);
                        }
                    }

                    if ($default) {
                        CustomerAddress::query()
                            ->where('customer_id', $customer->id)
                            ->where('id', '!=', $default->id)
                            ->update(['is_default' => false]);
                    }
                }
            });
    }
}

