<?php

use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\CustomerAddressController;
use App\Http\Controllers\Frontend\LoyaltyController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:customer')->group(function () {
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::get('/account/addresses', [CustomerAddressController::class, 'index'])->name('account.addresses.index');
    Route::post('/account/addresses', [CustomerAddressController::class, 'store'])->name('account.addresses.store');
    Route::put('/account/addresses/{address}', [CustomerAddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/account/addresses/{address}', [CustomerAddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::patch('/account/addresses/{address}/default', [CustomerAddressController::class, 'makeDefault'])->name('account.addresses.default');
    Route::get('/loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
});
