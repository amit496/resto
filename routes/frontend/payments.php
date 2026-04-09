<?php

use App\Http\Controllers\Frontend\StripeWebhookController;
use App\Http\Controllers\Frontend\RazorpayWebhookController;
use App\Http\Controllers\Frontend\RazorpayCallbackController;
use App\Http\Controllers\Frontend\PayPalReturnController;
use App\Http\Controllers\Frontend\PayPalCancelController;
use App\Http\Controllers\Frontend\PayPalWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::post('/razorpay/webhook', [RazorpayWebhookController::class, 'handle'])->name('razorpay.webhook');
Route::get('/razorpay/callback/{order}/receipt/{token}', RazorpayCallbackController::class)->name('razorpay.callback');

Route::get('/paypal/return/{order}/receipt/{token}', PayPalReturnController::class)->name('paypal.return');
Route::get('/paypal/cancel/{order}/receipt/{token}', PayPalCancelController::class)->name('paypal.cancel');
Route::post('/paypal/webhook', [PayPalWebhookController::class, 'handle'])->name('paypal.webhook');
