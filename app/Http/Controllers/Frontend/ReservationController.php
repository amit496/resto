<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Services\Frontend\FrontendPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(private readonly FrontendPageService $pageService)
    {
    }

    public function create(): View
    {
        $site = $this->pageService->siteData();

        return view('frontend.reservations.create', [
            ...$site,
            'branches' => Branch::query()->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = auth('customer')->user();

        $data = $request->validate([
            'guest_name' => ['required', 'string', 'max:120'],
            'guest_phone' => ['nullable', 'string', 'max:30'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:20'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'reserved_for' => ['required', 'date'],
            'special_request' => ['nullable', 'string', 'max:500'],
        ]);

        $restaurant = Restaurant::query()->oldest('id')->first();

        Reservation::query()->create([
            'restaurant_id' => $restaurant?->id,
            'branch_id' => $data['branch_id'] ?? null,
            'customer_id' => $customer?->id,
            'guest_name' => $data['guest_name'],
            'guest_phone' => $data['guest_phone'] ?? null,
            'guest_count' => $data['guest_count'],
            'reserved_for' => $data['reserved_for'],
            'status' => 'pending',
            'special_request' => $data['special_request'] ?? null,
        ]);

        return back()->with('success', 'Reservation request submitted.');
    }
}
