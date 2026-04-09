<?php

use App\Http\Controllers\Frontend\CartController;
use Illuminate\Support\Facades\Route;

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.items.store');
Route::patch('/cart/items/{itemKey}', [CartController::class, 'update'])->name('cart.items.update');
Route::delete('/cart/items/{itemKey}', [CartController::class, 'destroy'])->name('cart.items.destroy');
Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/cart/coupon', [CartController::class, 'clearCoupon'])->name('cart.coupon.clear');
Route::post('/cart/loyalty', [CartController::class, 'updateLoyalty'])->name('cart.loyalty.update');

