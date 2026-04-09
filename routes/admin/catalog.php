<?php

use App\Http\Controllers\Backend\BranchController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\RestaurantController;
use App\Http\Controllers\Backend\SubcategoryController;
use Illuminate\Support\Facades\Route;

Route::resource('restaurants', RestaurantController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
Route::patch('restaurants/{restaurant}/toggle-status', [RestaurantController::class, 'toggleStatus'])->name('restaurants.toggle-status');

Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
Route::get('branches/{branch}', [BranchController::class, 'show'])->name('branches.show');
Route::get('branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
Route::put('branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
Route::patch('branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
Route::delete('branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');

Route::resource('categories', CategoryController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

Route::resource('subcategories', SubcategoryController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
Route::patch('subcategories/{subcategory}/toggle-status', [SubcategoryController::class, 'toggleStatus'])->name('subcategories.toggle-status');

Route::resource('products', ProductController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');

