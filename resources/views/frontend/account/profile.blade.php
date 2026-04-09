@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | My Profile')

@section('content')
    <section class="page-hero">
        <div class="container page-hero-box">
            <div class="eyebrow">My Account</div>
            <h1 class="section-title">Manage your profile and account settings.</h1>
            <p class="section-copy">Update contact details, password, and keep your customer account information current.</p>
        </div>
    </section>

    <section class="section-sm">
        <div class="container commerce-shell">
            <div class="commerce-card">
                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (! $customer->hasVerifiedEmail())
                    <div class="feature-card" style="margin-bottom: 16px;">
                        <strong>Email not verified.</strong>
                        <p class="muted">Verify your email to unlock refunds, loyalty, and tracking features.</p>
                        <a class="btn-outline" href="{{ route('customer.verification.notice') }}">Verify Email</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('frontend.account.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input id="name" class="input" type="text" name="name" value="{{ old('name', $customer->name) }}" required>
                        </div>
                        <div class="form-field">
                            <label for="email">Email</label>
                            <input id="email" class="input" type="email" name="email" value="{{ old('email', $customer->email) }}" required>
                        </div>
                        <div class="form-field">
                            <label for="phone">Phone</label>
                            <input id="phone" class="input" type="text" name="phone" value="{{ old('phone', $customer->phone) }}">
                        </div>
                        <div class="form-field full">
                            <label for="address">Address</label>
                            <textarea id="address" class="textarea" name="address">{{ old('address', $customer->address) }}</textarea>
                        </div>
                        <div class="form-field">
                            <label for="password">New Password</label>
                            <input id="password" class="input" type="password" name="password">
                        </div>
                        <div class="form-field">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" class="input" type="password" name="password_confirmation">
                        </div>
                    </div>
                    <div class="hero-actions">
                        <button class="btn" type="submit">Update Profile</button>
                        <a class="btn-outline" href="{{ route('frontend.orders.index') }}">View Orders</a>
                        <a class="btn-outline" href="{{ route('frontend.loyalty.index') }}">Loyalty</a>
                        <a class="btn-outline" href="{{ route('frontend.account.addresses.index') }}">Addresses</a>
                    </div>
                </form>
            </div>

            <div class="commerce-sticky">
                <div class="commerce-card">
                    <div class="section-title" style="margin-top:0;">Account Quick Links</div>
                    <div class="summary-stack">
                        <div class="summary-row">
                            <span class="muted">Orders</span>
                            <a class="chip-link" href="{{ route('frontend.orders.index') }}">Order History</a>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Loyalty</span>
                            <a class="chip-link" href="{{ route('frontend.loyalty.index') }}">View Points</a>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Reservations</span>
                            <a class="chip-link" href="{{ route('frontend.reservations.create') }}">Book Table</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
