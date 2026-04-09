<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('users', [UserManagementController::class, 'index'])
    ->middleware('can:users.view')
    ->name('users.index');
Route::get('users/create', [UserManagementController::class, 'create'])
    ->middleware('can:users.create')
    ->name('users.create');
Route::post('users', [UserManagementController::class, 'store'])
    ->middleware('can:users.create')
    ->name('users.store');
Route::get('users/{user}', [UserManagementController::class, 'show'])
    ->middleware('can:users.view')
    ->name('users.show');
Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])
    ->middleware('can:users.edit')
    ->name('users.edit');
Route::put('users/{user}', [UserManagementController::class, 'update'])
    ->middleware('can:users.edit')
    ->name('users.update');
Route::delete('users/{user}', [UserManagementController::class, 'destroy'])
    ->middleware('can:users.delete')
    ->name('users.destroy');

Route::get('audit-logs', [AuditLogController::class, 'index'])
    ->middleware('can:audit-logs.view')
    ->name('audit-logs.index');
Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])
    ->middleware('can:audit-logs.view')
    ->name('audit-logs.show');

