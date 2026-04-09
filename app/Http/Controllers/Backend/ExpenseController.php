<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(): View
    {
        return view('backend.expenses.index', [
            'expenses' => Expense::query()->with('restaurant')->latest('expense_date')->paginate(20),
            'restaurants' => Restaurant::query()->orderBy('name')->get(['id', 'name']),
            'monthExpenseTotal' => Expense::query()
                ->whereBetween('expense_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->sum('amount'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'restaurant_id' => ['nullable', 'exists:restaurants,id'],
            'category' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string'],
        ]);

        Expense::query()->create([
            ...$data,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Expense added.');
    }
}

