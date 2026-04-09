<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('frontend.auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $customer = Customer::query()->where('email', $request->string('email'))->first();
        if (! $customer) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('We could not find an account with that email address.')]);
        }

        $token = Password::broker('customers')->createToken($customer);
        $customer->sendPasswordResetNotification($token);

        $response = redirect()->back()->with('status', __('Password reset instructions have been prepared for this account.'));

        if (app()->environment('local') || in_array(config('mail.default'), ['log', 'array'], true)) {
            $response->with('dev_reset_url', url(route('customer.password.reset', [
                'token' => $token,
                'email' => $customer->getEmailForPasswordReset(),
            ], false)));
        }

        return $response;
    }
}
