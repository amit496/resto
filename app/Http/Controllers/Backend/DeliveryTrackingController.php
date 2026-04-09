<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;
use App\Models\DeliveryTrackingEvent;
use App\Models\FoodOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryTrackingController extends Controller
{
    public function index(Request $request): View
    {
        $orderId = $request->integer('food_order_id');

        return view('backend.delivery-tracking.index', [
            'orders' => FoodOrder::query()
                ->where('order_type', 'delivery')
                ->with(['customer', 'deliveryBoy'])
                ->latest()
                ->limit(100)
                ->get(),
            'deliveryBoys' => DeliveryBoy::query()->orderBy('name')->get(['id', 'name']),
            'events' => DeliveryTrackingEvent::query()
                ->with(['order', 'deliveryBoy'])
                ->when($orderId > 0, fn ($query) => $query->where('food_order_id', $orderId))
                ->latest('event_at')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'food_order_id' => ['required', 'exists:food_orders,id'],
            'delivery_boy_id' => ['nullable', 'exists:delivery_boys,id'],
            'status' => ['required', 'in:assigned,picked_up,on_the_way,arrived,delivered,failed'],
            'message' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        DeliveryTrackingEvent::query()->create([
            ...$data,
            'event_at' => now(),
            'created_by' => auth()->id(),
        ]);

        if (($data['status'] ?? '') === 'delivered') {
            FoodOrder::query()
                ->whereKey($data['food_order_id'])
                ->update(['status' => 'delivered']);
        }

        return back()->with('success', 'Delivery event logged.');
    }
}

