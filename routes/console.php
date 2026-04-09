<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\NotificationLog;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('offers:send', function () {
    $today = now()->toDateString();

    $activeCoupons = Coupon::query()
        ->where('status', 'active')
        ->where(function ($query) use ($today) {
            $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
        })
        ->where(function ($query) use ($today) {
            $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
        })
        ->limit(5)
        ->get();

    if ($activeCoupons->isEmpty()) {
        $this->warn('No active coupons to send.');
        return;
    }

    $offerText = $activeCoupons
        ->map(fn ($coupon) => $coupon->code.' ('.$coupon->type->value.' '.$coupon->value.')')
        ->implode(', ');

    $customers = Customer::query()
        ->whereNotNull('email')
        ->where('email', '!=', '')
        ->get();

    $sentCount = 0;
    foreach ($customers as $customer) {
        $title = 'New Offers Available';
        $message = "Hi {$customer->name}, today's offers: {$offerText}";

        NotificationLog::query()->create([
            'title' => $title,
            'message' => $message,
            'channel' => 'email',
            'audience' => 'customer',
            'is_read' => false,
            'sent_at' => now(),
            'created_by' => null,
        ]);

        try {
            Mail::raw($message, function ($mail) use ($customer, $title) {
                $mail->to($customer->email)->subject($title);
            });
            $sentCount++;
        } catch (\Throwable $exception) {
            Log::warning('Offer email failed', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    $this->info("Offers processed for {$customers->count()} customers; sent: {$sentCount}");
})->purpose('Send active coupon offers to registered customers via email');

Schedule::command('offers:send')->dailyAt('10:00');

