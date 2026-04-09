<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        return view('backend.purchases.index', [
            'purchases' => PurchaseOrder::query()->with(['supplier', 'items'])->latest()->paginate(20),
            'suppliers' => Supplier::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'status' => ['required', 'in:pending,ordered,received,cancelled'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'item_name' => ['required', 'string', 'max:120'],
            'product_id' => ['nullable', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            $lineTotal = (float) $data['quantity'] * (float) $data['unit_cost'];

            $purchase = PurchaseOrder::query()->create([
                'restaurant_id' => null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'po_number' => 'PO-'.strtoupper((string) str()->random(8)),
                'status' => $data['status'],
                'order_date' => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'subtotal' => $lineTotal,
                'total_amount' => $lineTotal,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $purchase->items()->create([
                'product_id' => $data['product_id'] ?? null,
                'item_name' => $data['item_name'],
                'quantity' => $data['quantity'],
                'unit_cost' => $data['unit_cost'],
                'line_total' => $lineTotal,
            ]);
        });

        return back()->with('success', 'Purchase order created.');
    }

    public function updateStatus(Request $request, PurchaseOrder $purchase): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,ordered,received,cancelled'],
        ]);

        $purchase->update($data);

        return back()->with('success', 'Purchase status updated.');
    }
}

