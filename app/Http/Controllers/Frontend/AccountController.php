<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\FrontendPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(private readonly FrontendPageService $pageService)
    {
    }

    public function profile(): View
    {
        $customer = auth('customer')->user();
        abort_if(! $customer, 403);

        return view('frontend.account.profile', [
            ...$this->pageService->siteData(),
            'customer' => $customer,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $customer = auth('customer')->user();
        abort_if(! $customer, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:customers,email,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $emailChanged = $data['email'] !== $customer->email;

        $customer->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => $data['password'] ? Hash::make($data['password']) : $customer->password,
            'email_verified_at' => $emailChanged ? null : $customer->email_verified_at,
        ]);

        if ($emailChanged) {
            $customer->sendEmailVerificationNotification();
        }

        return back()->with('success', 'Profile updated.');
    }
}
