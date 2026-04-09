<?php

use App\Http\Controllers\Frontend\PwaManifestController;
use Illuminate\Support\Facades\Route;

Route::name('frontend.')->group(function () {
    Route::get('/manifest.webmanifest', PwaManifestController::class)->name('manifest');
    require __DIR__.'/pages.php';
    require __DIR__.'/menu.php';
    require __DIR__.'/cart.php';
    require __DIR__.'/orders.php';
    require __DIR__.'/payments.php';
    require __DIR__.'/account.php';
});

require __DIR__.'/auth.php';

Route::get('/home', fn () => redirect()->route('frontend.home'))->name('home');

