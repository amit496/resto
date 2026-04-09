@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Contact')

@section('content')
    <section class="page-hero">
        <div class="container page-hero-box">
            <div class="eyebrow">Contact Us</div>
            <h1 class="section-title">Talk to the restaurant team about orders, catering, branch support or partnerships.</h1>
            <p class="section-copy">This contact form stores messages directly in the application so frontend enquiries are not lost.</p>
        </div>
    </section>

    <section class="section-sm">
        <div class="container grid-2">
            <div class="contact-card">
                <div class="section-title">Send A Message</div>

                @if(session('success'))
                    <div class="flash-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="error-box">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('frontend.contact.store') }}">
                    @csrf
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input id="name" class="input" type="text" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-field">
                            <label for="email">Email</label>
                            <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-field">
                            <label for="phone">Phone</label>
                            <input id="phone" class="input" type="text" name="phone" value="{{ old('phone') }}">
                        </div>
                        <div class="form-field">
                            <label for="subject">Subject</label>
                            <input id="subject" class="input" type="text" name="subject" value="{{ old('subject') }}" required>
                        </div>
                        <div class="form-field full">
                            <label for="message">Message</label>
                            <textarea id="message" class="textarea" name="message" required>{{ old('message') }}</textarea>
                        </div>
                    </div>
                    <div class="hero-actions">
                        <button type="submit" class="btn">Submit Message</button>
                    </div>
                </form>
            </div>

            <div class="contact-card">
                <div class="section-title">Restaurant Contact</div>
                <div class="grid-2">
                    <div class="feature-card">
                        <h3>Support Phone</h3>
                        <p class="muted">{{ $setting->support_phone ?? $restaurant?->phone ?? '-' }}</p>
                    </div>
                    <div class="feature-card">
                        <h3>Support Email</h3>
                        <p class="muted">{{ $setting->support_email ?? $restaurant?->email ?? '-' }}</p>
                    </div>
                    <div class="feature-card">
                        <h3>Restaurant Address</h3>
                        <p class="muted">{{ $restaurant?->address ?? 'Managed from admin settings.' }}</p>
                    </div>
                    <div class="feature-card">
                        <h3>Brand Name</h3>
                        <p class="muted">{{ $restaurant?->name ?? ($setting->app_name ?? config('app.name', 'FoodiHub')) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

