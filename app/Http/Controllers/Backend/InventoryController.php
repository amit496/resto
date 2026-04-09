<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $restaurantId = $request->integer('restaurant_id');

        $stocks = InventoryStock::query()
            ->with(['restaurant', 'product'])
            ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('backend.inventory.index', [
            'stocks' => $stocks,
            'restaurants' => Restaurant::query()->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->orderBy('name')->get(['id', 'name', 'restaurant_id']),
            'recentMovements' => StockMovement::query()->with('product')->latest('moved_at')->limit(15)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'restaurant_id' => ['nullable', 'exists:restaurants,id'],
            'product_id' => ['required', 'exists:products,id'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'reorder_level' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:30'],
        ]);

        $stock = InventoryStock::query()->updateOrCreate(
            [
                'restaurant_id' => $data['restaurant_id'] ?? null,
                'product_id' => $data['product_id'],
            ],
            [
                'current_stock' => $data['current_stock'],
                'reorder_level' => $data['reorder_level'],
                'unit' => $data['unit'],
                'last_restocked_at' => now(),
            ]
        );

        StockMovement::query()->create([
            'inventory_stock_id' => $stock->id,
            'product_id' => $stock->product_id,
            'type' => 'adjustment',
            'quantity' => $stock->current_stock,
            'reference_type' => 'inventory_stock',
            'reference_id' => $stock->id,
            'notes' => 'Initial or update stock set from panel.',
            'created_by' => auth()->id(),
            'moved_at' => now(),
        ]);

        return back()->with('success', 'Inventory saved.');
    }

    public function adjust(Request $request, InventoryStock $inventoryStock): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($inventoryStock, $data) {
            $qty = (float) $data['quantity'];
            $current = (float) $inventoryStock->current_stock;

            if ($data['type'] === 'in') {
                $current += $qty;
            } elseif ($data['type'] === 'out') {
                $current = max(0, $current - $qty);
            } else {
                $current = $qty;
            }

            $inventoryStock->update([
                'current_stock' => $current,
                'last_restocked_at' => now(),
            ]);

            StockMovement::query()->create([
                'inventory_stock_id' => $inventoryStock->id,
                'product_id' => $inventoryStock->product_id,
                'type' => $data['type'],
                'quantity' => $qty,
                'reference_type' => 'inventory_stock',
                'reference_id' => $inventoryStock->id,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'moved_at' => now(),
            ]);
        });

        return back()->with('success', 'Stock adjusted.');
    }
}

