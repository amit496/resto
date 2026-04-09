@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Reservations')

@section('content')
    <section class="page-hero">
        <div class="container page-hero-box">
            <div class="eyebrow">Reservations</div>
            <h1 class="section-title">Book a table in advance.</h1>
            <p class="section-copy">Choose branch, guest count, and time. Our team will confirm your reservation shortly.</p>
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

                <form method="POST" action="{{ route('frontend.reservations.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="guest_name">Name</label>
                            <input id="guest_name" class="input" type="text" name="guest_name" value="{{ old('guest_name', auth('customer')->user()?->name) }}" required>
                        </div>
                        <div class="form-field">
                            <label for="guest_phone">Phone</label>
                            <input id="guest_phone" class="input" type="text" name="guest_phone" value="{{ old('guest_phone', auth('customer')->user()?->phone) }}">
                        </div>
                        <div class="form-field">
                            <label for="guest_count">Guest Count</label>
                            <input id="guest_count" class="input" type="number" min="1" max="20" name="guest_count" value="{{ old('guest_count', 2) }}" required>
                        </div>
                        <div class="form-field">
                            <label for="reserved_for">Reservation Date & Time</label>
                            <input id="reserved_for" class="input" type="datetime-local" name="reserved_for" value="{{ old('reserved_for') }}" required>
                        </div>
                        <div class="form-field">
                            <label for="branch_id">Branch</label>
                            <select id="branch_id" class="select" name="branch_id">
                                <option value="">Any Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected((int) old('branch_id') === $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field full">
                            <label for="special_request">Special Request</label>
                            <textarea id="special_request" class="textarea" name="special_request">{{ old('special_request') }}</textarea>
                        </div>
                    </div>
                    <div class="hero-actions">
                        <button type="submit" class="btn">Request Reservation</button>
                        <a href="{{ route('frontend.menu.index') }}" class="btn-outline">Browse Menu</a>
                    </div>
                </form>
            </div>

            <div class="commerce-sticky">
                <div class="commerce-card">
                    <div class="section-title" style="margin-top:0;">Reservation Notes</div>
                    <div class="summary-stack">
                        <div class="summary-row">
                            <span class="muted">Confirmation</span>
                            <strong>Within 24 hours</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Support</span>
                            <strong>{{ $setting->support_phone ?? 'Contact branch' }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Email</span>
                            <strong>{{ $setting->support_email ?? 'support@restaurant.test' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
