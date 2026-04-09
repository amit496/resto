<?php

use App\Http\Controllers\Backend\CouponController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\DeliveryBoyController;
use App\Http\Controllers\Backend\FoodOrderController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\RefundController;
use Illuminate\Support\Facades\Route;

Route::resource('orders', FoodOrderController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
Route::get('orders/{order}/bill', [FoodOrderController::class, 'bill'])->name('orders.bill');
Route::resource('customers', CustomerController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);

Route::resource('delivery-boys', DeliveryBoyController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
Route::patch('delivery-boys/{delivery_boy}/toggle-status', [DeliveryBoyController::class, 'toggleStatus'])->name('delivery-boys.toggle-status');

Route::resource('coupons', CouponController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
Route::patch('coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');

Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
Route::get('payments/{payment}/slip', [PaymentController::class, 'slip'])->name('payments.slip');

Route::get('refunds', [RefundController::class, 'index'])->name('refunds.index');
Route::post('refunds', [RefundController::class, 'store'])->name('refunds.store');
Route::patch('refunds/{refund}/status', [RefundController::class, 'updateStatus'])->name('refunds.status');

