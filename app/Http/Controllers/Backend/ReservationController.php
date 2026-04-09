<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        return view('backend.reservations.index', [
            'reservations' => Reservation::query()
                ->with(['restaurant', 'branch', 'customer'])
                ->latest('reserved_for')
                ->paginate(20),
            'restaurants' => Restaurant::query()->orderBy('name')->get(['id', 'name']),
            'branches' => Branch::query()->orderBy('name')->get(['id', 'name']),
            'customers' => Customer::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'restaurant_id' => ['nullable', 'exists:restaurants,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'guest_name' => ['required', 'string', 'max:120'],
            'guest_phone' => ['nullable', 'string', 'max:30'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:100'],
            'reserved_for' => ['required', 'date'],
            'status' => ['required', 'in:pending,confirmed,seated,completed,cancelled,no_show'],
            'special_request' => ['nullable', 'string'],
        ]);

        Reservation::query()->create([
            ...$data,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Reservation created.');
    }

    public function updateStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,seated,completed,cancelled,no_show'],
        ]);

        $reservation->update($data);

        return back()->with('success', 'Reservation status updated.');
    }
}

