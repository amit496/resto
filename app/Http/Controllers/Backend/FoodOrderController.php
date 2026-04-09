<?php

namespace App\Http\Controllers\Backend;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertFoodOrderRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\DeliveryBoy;
use App\Models\FoodOrder;
use App\Models\Product;
use App\Models\Restaurant;
use App\Services\Backend\FoodOrderService;
use App\Support\CountryCatalog;
use App\Support\PaymentGatewayCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class FoodOrderController extends Controller
{
    public function __construct(private readonly FoodOrderService $service)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $orderType = trim((string) $request->string('order_type'));
        $restaurantId = $request->integer('restaurant_id');
        $branchId = $request->integer('branch_id');
        $customerId = $request->integer('customer_id');
        $deliveryBoyId = $request->integer('delivery_boy_id');

        return view('backend.orders.index', [
            'orders' => FoodOrder::query()
                ->with(['restaurant', 'branch', 'customer', 'deliveryBoy'])
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('order_no', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('restaurant', fn ($restaurant) => $restaurant->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($orderType !== '', fn ($query) => $query->where('order_type', $orderType))
                ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
                ->when($branchId > 0, fn ($query) => $query->where('branch_id', $branchId))
                ->when($customerId > 0, fn ($query) => $query->where('customer_id', $customerId))
                ->when($deliveryBoyId > 0, fn ($query) => $query->where('delivery_boy_id', $deliveryBoyId))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'restaurants' => Restaurant::query()->select('id', 'name')->orderBy('name')->get(),
            'branches' => Branch::query()->select('id', 'name')->orderBy('name')->get(),
            'customers' => Customer::query()->select('id', 'name')->orderBy('name')->get(),
            'deliveryBoys' => DeliveryBoy::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(FoodOrder $order): View
    {
        return view('backend.orders.show', [
            'order' => $order->load(['restaurant', 'branch', 'customer', 'deliveryBoy', 'items.product', 'payments', 'createdBy', 'billedBy']),
        ]);
    }

    public function bill(FoodOrder $order): View
    {
        $order->load(['restaurant', 'branch', 'customer', 'items.product', 'payments', 'createdBy', 'billedBy']);

        return view('backend.orders.bill', [
            'order' => $order,
        ]);
    }

    public function create(): View
    {
        return view('backend.orders.form', $this->formData(new FoodOrder()));
    }

    public function store(UpsertFoodOrderRequest $request): RedirectResponse
    {
        $this->service->upsert($request->validated());

        return redirect()->route('admin.orders.index')->with('success', 'Order created.');
    }

    public function edit(FoodOrder $order): View
    {
        return view('backend.orders.form', $this->formData($order->load(['items', 'payments'])));
    }

    public function update(UpsertFoodOrderRequest $request, FoodOrder $order): RedirectResponse
    {
        $this->service->upsert($request->validated(), $order);

        return redirect()->route('admin.orders.index')->with('success', 'Order updated.');
    }

    public function destroy(FoodOrder $order): RedirectResponse
    {
        $order->delete();

        return back()->with('success', 'Order deleted.');
    }

    private function formData(FoodOrder $order): array
    {
        $singleRestaurantMode = (bool) config('app.single_restaurant_mode', true);
        $defaultRestaurantId = Restaurant::query()->value('id');

        return [
            'order' => $order,
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
            'branches' => Branch::query()->orderBy('name')->get(),
            'customers' => Customer::query()->orderBy('name')->get(),
            'deliveryBoys' => DeliveryBoy::query()->orderBy('name')->get(),
            'products' => Product::query()->with('variants')->orderBy('name')->get(),
            'orderTypeOptions' => OrderTypeEnum::cases(),
            'orderStatusOptions' => OrderStatusEnum::cases(),
            'paymentMethodOptions' => PaymentMethodEnum::cases(),
            'paymentStatusOptions' => PaymentStatusEnum::cases(),
            'countries' => CountryCatalog::options(),
            'paymentGatewayDefinitions' => PaymentGatewayCatalog::definitions(),
            'singleRestaurantMode' => $singleRestaurantMode,
            'defaultRestaurantId' => $defaultRestaurantId,
        ];
    }
}

