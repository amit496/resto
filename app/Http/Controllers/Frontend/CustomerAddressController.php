<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Services\Frontend\FrontendPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerAddressController extends Controller
{
    public function __construct(private readonly FrontendPageService $pageService)
    {
    }

    public function index(): View
    {
        $customer = auth('customer')->user();
        abort_if(! $customer, 403);

        return view('frontend.account.addresses.index', [
            ...$this->pageService->siteData(),
            'customer' => $customer,
            'addresses' => $customer->addresses()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = auth('customer')->user();
        abort_if(! $customer, 403);

        $data = $this->validateAddress($request);

        $address = $customer->addresses()->create([
            ...$data,
            'is_default' => (bool) ($data['is_default'] ?? false),
        ]);

        if ($address->is_default) {
            $customer->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        } elseif ($customer->addresses()->where('is_default', true)->doesntExist()) {
            $address->update(['is_default' => true]);
        }

        return back()->with('success', 'Address saved.');
    }

    public function update(Request $request, CustomerAddress $address): RedirectResponse
    {
        $customer = auth('customer')->user();
        abort_if(! $customer || $address->customer_id !== $customer->id, 403);

        $data = $this->validateAddress($request);

        $address->update([
            ...$data,
            'is_default' => (bool) ($data['is_default'] ?? false),
        ]);

        if ($address->is_default) {
            $customer->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        return back()->with('success', 'Address updated.');
    }

    public function destroy(CustomerAddress $address): RedirectResponse
    {
        $customer = auth('customer')->user();
        abort_if(! $customer || $address->customer_id !== $customer->id, 403);

        $wasDefault = (bool) $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = $customer->addresses()->oldest('id')->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Address deleted.');
    }

    public function makeDefault(CustomerAddress $address): RedirectResponse
    {
        $customer = auth('customer')->user();
        abort_if(! $customer || $address->customer_id !== $customer->id, 403);

        $customer->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated.');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'type' => ['required', 'string', Rule::in(['home', 'work', 'other'])],
            'label' => ['nullable', 'string', 'max:60'],
            'recipient_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address_line_1' => ['required', 'string', 'max:500'],
            'address_line_2' => ['nullable', 'string', 'max:500'],
            'landmark' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:80'],
            'state' => ['nullable', 'string', 'max:80'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country_code' => ['nullable', 'string', 'size:2'],
            'instructions' => ['nullable', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }
}

