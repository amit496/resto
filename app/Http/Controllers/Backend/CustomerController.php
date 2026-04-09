<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\UpsertCustomerRequest;
use App\Models\Customer;
use App\Models\Restaurant;
use App\Services\Backend\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $service)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $restaurantId = $request->integer('restaurant_id');
        $email = trim((string) $request->string('email'));

        return view('backend.customers.index', [
            'customers' => Customer::query()
                ->with('restaurant')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('restaurant', fn ($restaurant) => $restaurant->where('name', 'like', "%{$search}%"));
                    });
                })
                ->when($restaurantId > 0, fn ($query) => $query->where('restaurant_id', $restaurantId))
                ->when($email !== '', fn ($query) => $query->where('email', 'like', "%{$email}%"))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'restaurants' => Restaurant::query()->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function show(Customer $customer): View
    {
        return view('backend.customers.show', [
            'customer' => $customer->load(['restaurant', 'orders', 'addresses']),
        ]);
    }

    public function create(): View
    {
        return view('backend.customers.form', [
            'customer' => new Customer(),
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
        ]);
    }

    public function store(UpsertCustomerRequest $request): RedirectResponse
    {
        $this->service->upsert($request->validated());

        return redirect()->route('admin.customers.index')->with('success', 'Customer created.');
    }

    public function edit(Customer $customer): View
    {
        return view('backend.customers.form', [
            'customer' => $customer,
            'restaurants' => Restaurant::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpsertCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->service->upsert($request->validated(), $customer);

        return redirect()
            ->route('admin.customers.index', ['page' => $request->integer('redirect_page', 1)])
            ->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return back()->with('success', 'Customer deleted.');
    }
}

