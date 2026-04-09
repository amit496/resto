<?php

use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
Route::get('staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
Route::put('staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
Route::patch('staff/{staff}/toggle-status', [StaffController::class, 'toggleStatus'])->name('staff.toggle-status');
Route::delete('staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

