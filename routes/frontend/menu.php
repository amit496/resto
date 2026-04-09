<?php

use App\Http\Controllers\Frontend\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/category/{category:slug}', [MenuController::class, 'index'])->name('menu.category');
Route::get('/menu/category/{category:slug}/{subcategory:slug}', [MenuController::class, 'index'])->name('menu.subcategory');
Route::get('/menu/feed', [MenuController::class, 'feed'])->name('menu.feed');
Route::get('/menu/{product:slug}', [MenuController::class, 'show'])->name('menu.show');

