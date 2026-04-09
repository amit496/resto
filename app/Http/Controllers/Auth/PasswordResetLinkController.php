<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()->where('email', $request->string('email'))->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('We could not find an account with that email address.')]);
        }

        if (! $user->is_active) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('This account is inactive. Contact the administrator.')]);
        }

        $token = Password::broker()->createToken($user);
        $user->sendPasswordResetNotification($token);

        $response = redirect()->back()->with('status', __('Password reset instructions have been prepared for this account.'));

        if (app()->environment('local') || in_array(config('mail.default'), ['log', 'array'], true)) {
            $response->with('dev_reset_url', url(route('admin.password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ], false)));
        }

        return $response;
    }
}

