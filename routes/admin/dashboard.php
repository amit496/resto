<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Backend\BackendDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)
    ->middleware('can:dashboard.view')
    ->name('dashboard');

Route::get('/overview', BackendDashboardController::class)->name('overview');

