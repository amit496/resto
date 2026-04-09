<?php

use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\BranchController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\OfferController;
use App\Http\Controllers\Frontend\ReservationController;
use App\Http\Controllers\Frontend\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/about', AboutController::class)->name('about');
Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
Route::get('/branches/{branch}', [BranchController::class, 'show'])->name('branches.show');
Route::get('/offers', OfferController::class)->name('offers');
Route::get('/reviews', ReviewController::class)->name('reviews');
Route::post('/reviews/food', [ReviewController::class, 'storeFood'])->middleware('auth:customer')->name('reviews.food.store');
Route::post('/reviews/branch', [ReviewController::class, 'storeBranch'])->middleware('auth:customer')->name('reviews.branch.store');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/reservations', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

