<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/auth.php';

    Route::middleware(['auth:web', 'audit.log'])->group(function () {
        require __DIR__.'/dashboard.php';
        require __DIR__.'/management.php';
        require __DIR__.'/catalog.php';
        require __DIR__.'/sales.php';
        require __DIR__.'/operations.php';
        require __DIR__.'/engagement.php';
        require __DIR__.'/settings.php';
    });
});

