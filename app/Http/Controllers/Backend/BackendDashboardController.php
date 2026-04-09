<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FoodOrder;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\View\View;

class BackendDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('backend.dashboard', [
            'restaurantsCount' => Restaurant::query()->count(),
            'categoriesCount' => Category::query()->count(),
            'productsCount' => Product::query()->count(),
            'ordersCount' => FoodOrder::query()->count(),
        ]);
    }
}

