<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FoodOrder;
use Illuminate\Http\RedirectResponse;

class RazorpayCallbackController extends Controller
{
    public function __invoke(FoodOrder $order, string $token): RedirectResponse
    {
        return redirect()->route('frontend.orders.receipt', [
            'order' => $order,
            'token' => $token,
        ]);
    }
}

