<?php

use App\Http\Controllers\Frontend\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/checkout', [OrderController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
Route::get('/orders/{order}/receipt/{token}', [OrderController::class, 'receipt'])->name('orders.receipt');
Route::middleware('auth:customer')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/refund', [OrderController::class, 'requestRefund'])->name('orders.refund');
});
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

