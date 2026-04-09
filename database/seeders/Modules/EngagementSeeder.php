<?php

namespace Database\Seeders\Modules;

use App\Models\Branch;
use App\Models\BranchReview;
use App\Models\Customer;
use App\Models\DeliveryTrackingEvent;
use App\Models\FoodOrder;
use App\Models\FoodReview;
use App\Models\LoyaltyTransaction;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class EngagementSeeder extends Seeder
{
    public function run(): void
    {
        $restaurant = Restaurant::query()->first();
        $branch = Branch::query()->first();
        $branches = Branch::query()->get();
        $customer = Customer::query()->first();
        $product = Product::query()->first();
        $order = FoodOrder::query()->first();
        $payment = Payment::query()->where('status', 'paid')->first();

        if ($customer && $product) {
            FoodReview::query()->firstOrCreate(
                ['customer_id' => $customer->id, 'product_id' => $product->id],
                [
                    'branch_id' => $branch?->id,
                    'rating' => 5,
                    'title' => 'Amazing taste',
                    'comment' => 'Fresh, delicious and well packed.',
                    'status' => 'approved',
                    'is_published' => true,
                ]
            );
        }

        if ($customer && $branches->isNotEmpty()) {
            foreach ($branches as $index => $branchItem) {
                $rating = 3 + (($index + 1) % 3);

                BranchReview::query()->firstOrCreate(
                    ['customer_id' => $customer->id, 'branch_id' => $branchItem->id],
                    [
                        'rating' => $rating,
                        'title' => 'Great service',
                        'comment' => 'Staff was friendly and quick.',
                        'status' => 'approved',
                        'is_published' => true,
                    ]
                );
            }
        }

        if ($customer && $restaurant) {
            Reservation::query()->firstOrCreate(
                ['customer_id' => $customer->id, 'reserved_for' => now()->addDays(2)->startOfHour()],
                [
                    'restaurant_id' => $restaurant->id,
                    'branch_id' => $branch?->id,
                    'guest_name' => $customer->name,
                    'guest_phone' => $customer->phone,
                    'guest_count' => 4,
                    'status' => 'pending',
                    'special_request' => 'Window seat if available.',
                ]
            );
        }

        if ($order) {
            DeliveryTrackingEvent::query()->firstOrCreate(
                ['food_order_id' => $order->id, 'status' => 'picked_up'],
                [
                    'delivery_boy_id' => $order->delivery_boy_id,
                    'message' => 'Order picked up from the branch.',
                    'location' => $branch?->address,
                    'event_at' => now()->subMinutes(30),
                ]
            );
        }

        if ($customer && $order) {
            LoyaltyTransaction::query()->firstOrCreate(
                ['customer_id' => $customer->id, 'food_order_id' => $order->id, 'type' => 'credit'],
                [
                    'points' => 10,
                    'note' => 'Welcome points',
                ]
            );
        }

        if ($payment) {
            Refund::query()->firstOrCreate(
                ['payment_id' => $payment->id],
                [
                    'food_order_id' => $payment->food_order_id,
                    'amount' => min(50, (float) $payment->amount),
                    'status' => 'pending',
                    'reason' => 'Sample refund request',
                ]
            );
        }
    }
}
