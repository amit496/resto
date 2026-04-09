<?php

use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('reviews/food', [ReviewController::class, 'storeFood'])->name('reviews.food.store');
Route::post('reviews/branch', [ReviewController::class, 'storeBranch'])->name('reviews.branch.store');
Route::patch('reviews/food/{foodReview}/status', [ReviewController::class, 'updateFoodStatus'])->name('reviews.food.status');
Route::patch('reviews/branch/{branchReview}/status', [ReviewController::class, 'updateBranchStatus'])->name('reviews.branch.status');
Route::patch('reviews/food/{foodReview}/publish', [ReviewController::class, 'toggleFoodPublish'])->name('reviews.food.publish');
Route::patch('reviews/branch/{branchReview}/publish', [ReviewController::class, 'toggleBranchPublish'])->name('reviews.branch.publish');
Route::delete('reviews/food/{foodReview}', [ReviewController::class, 'destroyFood'])->name('reviews.food.destroy');
Route::delete('reviews/branch/{branchReview}', [ReviewController::class, 'destroyBranch'])->name('reviews.branch.destroy');

