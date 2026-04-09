<?php

namespace App\Http\Controllers\Backend;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\FoodOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->string('status');

        return view('backend.kitchen.index', [
            'status' => $status,
            'orders' => FoodOrder::query()
                ->with(['restaurant', 'customer', 'items'])
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->whereIn('status', [
                    OrderStatusEnum::PENDING->value,
                    OrderStatusEnum::PREPARING->value,
                    OrderStatusEnum::READY->value,
                ])
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function updateStatus(Request $request, FoodOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,preparing,ready,delivered,cancelled'],
        ]);

        $order->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Kitchen status updated.');
    }
}

