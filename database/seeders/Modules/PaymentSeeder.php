<?php

namespace Database\Seeders\Modules;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\FoodOrder;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $orders = FoodOrder::query()->get();

        foreach ($orders as $order) {
            $status = fake()->randomElement(PaymentStatusEnum::cases());
            $order->payments()->updateOrCreate(
                ['food_order_id' => $order->id],
                [
                    'method' => fake()->randomElement(PaymentMethodEnum::cases()),
                    'status' => $status,
                    'amount' => $order->total_amount,
                    'transaction_ref' => strtoupper('TXN'.fake()->bothify('####??##')),
                    'paid_at' => $status === PaymentStatusEnum::PAID ? now()->subDays(fake()->numberBetween(0, 15)) : null,
                ]
            );
        }
    }
}
