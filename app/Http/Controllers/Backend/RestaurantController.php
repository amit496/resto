<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertRestaurantRequest;
use App\Models\Restaurant;
use App\Services\Backend\RestaurantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    public function __construct(private readonly RestaurantService $service)
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $singleRestaurantMode = (bool) config('app.single_restaurant_mode', true);
        if ($singleRestaurantMode) {
            $primaryRestaurant = Restaurant::query()->oldest('id')->first();
            if ($primaryRestaurant) {
                return redirect()->route('admin.restaurants.show', $primaryRestaurant);
            }
        }

        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));

        $restaurants = Restaurant::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString();

        return view('backend.restaurants.index', [
            'restaurants' => $restaurants,
            'singleRestaurantMode' => $singleRestaurantMode,
            'canCreateRestaurant' => ! $singleRestaurantMode || Restaurant::query()->count() === 0,
            'primaryRestaurant' => Restaurant::query()->oldest('id')->first(),
        ]);
    }

    public function show(Restaurant $restaurant): View|RedirectResponse
    {
        if ((bool) config('app.single_restaurant_mode', true)) {
            $primaryRestaurant = Restaurant::query()->oldest('id')->first();
            if ($primaryRestaurant && $restaurant->id !== $primaryRestaurant->id) {
                return redirect()->route('admin.restaurants.show', $primaryRestaurant);
            }
        }

        return view('backend.restaurants.show', [
            'restaurant' => $restaurant->loadCount(['categories', 'products', 'customers', 'deliveryBoys', 'coupons']),
        ]);
    }

    public function create(): View
    {
        if ((bool) config('app.single_restaurant_mode', true)) {
            $existingRestaurant = Restaurant::query()->oldest('id')->first();
            if ($existingRestaurant) {
                return redirect()
                    ->route('admin.restaurants.edit', $existingRestaurant)
                    ->with('info', 'Single restaurant mode is enabled. You can update the existing restaurant profile.');
            }
        }

        return view('backend.restaurants.form', ['restaurant' => new Restaurant()]);
    }

    public function store(UpsertRestaurantRequest $request): RedirectResponse
    {
        if ((bool) config('app.single_restaurant_mode', true) && Restaurant::query()->exists()) {
            return redirect()
                ->route('admin.restaurants.index')
                ->with('error', 'Single restaurant mode is enabled. Creating multiple restaurants is disabled.');
        }

        $this->service->upsert($request->validated());

        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant created.');
    }

    public function edit(Restaurant $restaurant): View
    {
        return view('backend.restaurants.form', compact('restaurant'));
    }

    public function update(UpsertRestaurantRequest $request, Restaurant $restaurant): RedirectResponse
    {
        $this->service->upsert($request->validated(), $restaurant);

        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant updated.');
    }

    public function toggleStatus(Restaurant $restaurant): RedirectResponse
    {
        if ((bool) config('app.single_restaurant_mode', true)) {
            $primary = Restaurant::query()->oldest('id')->first();
            if ($primary && $restaurant->id !== $primary->id) {
                return redirect()->route('admin.restaurants.show', $primary);
            }
        }

        $current = $restaurant->status?->value ?? (string) $restaurant->status;
        $restaurant->update([
            'status' => $current === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Restaurant status updated.');
    }

    public function destroy(Restaurant $restaurant): RedirectResponse
    {
        if ((bool) config('app.single_restaurant_mode', true)) {
            return back()->with('error', 'Single restaurant mode is enabled. Archive is disabled.');
        }

        $restaurant->update(['status' => 'inactive']);

        return back()->with('success', 'Restaurant archived.');
    }
}

