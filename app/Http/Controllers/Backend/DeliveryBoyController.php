<?php

namespace App\Http\Controllers\Backend;

use App\Enums\DeliveryBoyStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertDeliveryBoyRequest;
use App\Models\DeliveryBoy;
use App\Models\FoodOrder;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Services\Backend\DeliveryBoyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DeliveryBoyController extends Controller
{
    public function __construct(private readonly DeliveryBoyService $service)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $restaurantId = $request->integer('restaurant_id');
        $hasActiveOrders = trim((string) $request->string('has_active_orders'));

        return view('backend.delivery-boys.index', [
            'deliveryBoys' => DeliveryBoy::query()
                ->with('restaurant')
                ->withCount([
                    'orders as active_orders_count' => fn ($query) => $query->whereNotIn('status', ['delivered', 'cancelled']),
                    'orders as delivered_orders_count' => fn ($query) => $query->where('status', 'delivered'),
                ])
                ->addSelect([
                    'collected_amount' => Payment::query()
                        ->selectRaw('COALESCE(SUM(payments.amount), 0)')
                        ->join('food_orders', 'food_orders.id', '=', 'payments.food_order_id')
                        ->whereColumn('food_orders.delivery_boy_id', 'delivery_boys.id')
                        ->where('payments.status', 'paid')
                        ->limit(1),
                ])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('vehicle_no', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('restaurant', fn ($restaurant) => $restaurant->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
                ->when($hasActiveOrders === 'yes', fn ($query) => $query->whereHas('orders', fn ($orderQuery) => $orderQuery->whereNotIn('status', ['delivered', 'cancelled'])))
                ->when($hasActiveOrders === 'no', fn ($query) => $query->whereDoesntHave('orders', fn ($orderQuery) => $orderQuery->whereNotIn('status', ['delivered', 'cancelled'])))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'restaurants' => Restaurant::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(DeliveryBoy $delivery_boy): View
    {
        return view('backend.delivery-boys.show', [
            'deliveryBoy' => $delivery_boy->load([
                'restaurant',
                'orders' => fn ($query) => $query->latest()->limit(10),
                'orders.customer',
            ]),
            'activeOrdersCount' => FoodOrder::query()
                ->where('delivery_boy_id', $delivery_boy->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->count(),
            'deliveredOrdersCount' => FoodOrder::query()
                ->where('delivery_boy_id', $delivery_boy->id)
                ->where('status', 'delivered')
                ->count(),
            'collectedAmount' => Payment::query()
                ->join('food_orders', 'food_orders.id', '=', 'payments.food_order_id')
                ->where('food_orders.delivery_boy_id', $delivery_boy->id)
                ->where('payments.status', 'paid')
                ->sum('payments.amount'),
        ]);
    }

    public function create(): View
    {
        return view('backend.delivery-boys.form', [
            'deliveryBoy' => new DeliveryBoy(),
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
            'statusOptions' => DeliveryBoyStatusEnum::cases(),
        ]);
    }

    public function store(UpsertDeliveryBoyRequest $request): RedirectResponse
    {
        $this->service->upsert($request->validated());

        return redirect()->route('admin.delivery-boys.index')->with('success', 'Delivery boy created.');
    }

    public function edit(DeliveryBoy $delivery_boy): View
    {
        return view('backend.delivery-boys.form', [
            'deliveryBoy' => $delivery_boy,
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
            'statusOptions' => DeliveryBoyStatusEnum::cases(),
        ]);
    }

    public function update(UpsertDeliveryBoyRequest $request, DeliveryBoy $delivery_boy): RedirectResponse
    {
        $this->service->upsert($request->validated(), $delivery_boy);

        return redirect()
            ->route('admin.delivery-boys.index', ['page' => $request->integer('redirect_page', 1)])
            ->with('success', 'Delivery boy updated.');
    }

    public function toggleStatus(DeliveryBoy $delivery_boy): RedirectResponse
    {
        $current = $delivery_boy->status?->value ?? (string) $delivery_boy->status;
        $delivery_boy->update([
            'status' => $current === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Delivery boy status updated.');
    }

    public function destroy(DeliveryBoy $delivery_boy): RedirectResponse
    {
        $delivery_boy->update(['status' => 'inactive']);

        return back()->with('success', 'Delivery boy archived.');
    }
}

