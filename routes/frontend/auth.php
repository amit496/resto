<?php

use App\Http\Controllers\Frontend\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Frontend\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Frontend\Auth\NewPasswordController;
use App\Http\Controllers\Frontend\Auth\PasswordResetLinkController;
use App\Http\Controllers\Frontend\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::prefix('customer')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('customer.login');
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('customer.login.store');
        Route::get('/register', [RegisteredUserController::class, 'create'])->name('customer.register');
        Route::post('/register', [RegisteredUserController::class, 'store'])->name('customer.register.store');
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('customer.password.request');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('customer.password.email');
        Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('customer.password.reset');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('customer.password.store');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('customer.logout');
        Route::get('/verify-email', EmailVerificationPromptController::class)->name('customer.verification.notice');
        Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('customer.verification.verify');
        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('customer.verification.send');
    });
});

