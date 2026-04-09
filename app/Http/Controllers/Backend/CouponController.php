<?php

namespace App\Http\Controllers\Backend;

use App\Enums\CouponStatusEnum;
use App\Enums\CouponTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertCouponRequest;
use App\Models\Coupon;
use App\Models\Restaurant;
use App\Services\Backend\CouponService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function __construct(private readonly CouponService $service)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = trim((string) $request->string('status'));
        $type = trim((string) $request->string('type'));
        $restaurantId = $request->integer('restaurant_id');

        return view('backend.coupons.index', [
            'coupons' => Coupon::query()
                ->with('restaurant')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('code', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('restaurant', fn ($restaurant) => $restaurant->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($status !== '', fn ($query) => $query->where('status', $status))
                ->when($type !== '', fn ($query) => $query->where('type', $type))
                ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'restaurants' => Restaurant::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(Coupon $coupon): View
    {
        return view('backend.coupons.show', [
            'coupon' => $coupon->load('restaurant'),
        ]);
    }

    public function create(): View
    {
        return view('backend.coupons.form', [
            'coupon' => new Coupon(),
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
            'typeOptions' => CouponTypeEnum::cases(),
            'statusOptions' => CouponStatusEnum::cases(),
        ]);
    }

    public function store(UpsertCouponRequest $request): RedirectResponse
    {
        $this->service->upsert($request->validated());

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('backend.coupons.form', [
            'coupon' => $coupon,
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
            'typeOptions' => CouponTypeEnum::cases(),
            'statusOptions' => CouponStatusEnum::cases(),
        ]);
    }

    public function update(UpsertCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $this->service->upsert($request->validated(), $coupon);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated.');
    }

    public function toggleStatus(Coupon $coupon): RedirectResponse
    {
        $current = $coupon->status?->value ?? (string) $coupon->status;
        $coupon->update([
            'status' => $current === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Coupon status updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['status' => 'inactive']);

        return back()->with('success', 'Coupon archived.');
    }
}

