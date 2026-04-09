<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FoodOrder;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(): View
    {
        return view('backend.loyalty.index', [
            'transactions' => LoyaltyTransaction::query()
                ->with(['customer', 'order'])
                ->latest()
                ->paginate(20),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
            'orders' => FoodOrder::query()->latest()->limit(100)->get(['id', 'order_no']),
            'balances' => LoyaltyTransaction::query()
                ->select(
                    'customer_id',
                    DB::raw("SUM(CASE WHEN type IN ('credit','adjustment') THEN points ELSE 0 END) - SUM(CASE WHEN type = 'debit' THEN points ELSE 0 END) as balance")
                )
                ->groupBy('customer_id')
                ->with('customer:id,name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'food_order_id' => ['nullable', 'exists:food_orders,id'],
            'type' => ['required', 'in:credit,debit,adjustment'],
            'points' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        LoyaltyTransaction::query()->create([
            ...$data,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Loyalty transaction added.');
    }
}

