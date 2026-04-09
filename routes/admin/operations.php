<?php

use App\Http\Controllers\Backend\DeliveryTrackingController;
use App\Http\Controllers\Backend\ExpenseController;
use App\Http\Controllers\Backend\AdminEmailController;
use App\Http\Controllers\Backend\InventoryController;
use App\Http\Controllers\Backend\KitchenController;
use App\Http\Controllers\Backend\LoyaltyController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\PurchaseController;
use App\Http\Controllers\Backend\ReservationController;
use App\Http\Controllers\Backend\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
Route::patch('kitchen/orders/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.orders.status');

Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::post('inventory', [InventoryController::class, 'store'])->name('inventory.store');
Route::post('inventory/{inventoryStock}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('reservations.status');

Route::get('delivery-tracking', [DeliveryTrackingController::class, 'index'])->name('delivery-tracking.index');
Route::post('delivery-tracking', [DeliveryTrackingController::class, 'store'])->name('delivery-tracking.store');

Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::patch('suppliers/{supplier}/status', [SupplierController::class, 'updateStatus'])->name('suppliers.status');
Route::patch('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');

Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
Route::post('purchases', [PurchaseController::class, 'store'])->name('purchases.store');
Route::patch('purchases/{purchase}/status', [PurchaseController::class, 'updateStatus'])->name('purchases.status');

Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');

Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('notifications', [NotificationController::class, 'store'])->name('notifications.store');
Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

Route::get('emails', [AdminEmailController::class, 'index'])->name('emails.index');
Route::post('emails', [AdminEmailController::class, 'send'])->name('emails.send');

Route::get('loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');
Route::post('loyalty', [LoyaltyController::class, 'store'])->name('loyalty.store');

