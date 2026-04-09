@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Offers')

@section('content')
    <section class="page-hero">
        <div class="container page-hero-box">
            <div class="eyebrow">Coupons & Offers</div>
            <h1 class="section-title">Promotions built to increase order frequency and branch conversion.</h1>
            <p class="section-copy">Publicly showcase active coupons so registered and repeat customers see clear value before placing orders.</p>
        </div>
    </section>

    <section class="section-sm">
        <div class="container grid-3">
            @forelse($offers as $offer)
                <div class="offer-card">
                    <div class="eyebrow">Promo Code</div>
                    <h3>{{ $offer->code }}</h3>
                    <p class="muted">Offer value: {{ number_format((float) $offer->value, 2) }} | Minimum order: {{ number_format((float) $offer->min_order_amount, 2) }}</p>
                    <div class="offer-meta">
                        <span class="pill">{{ $offer->type->value }}</span>
                        <span class="pill">{{ $offer->start_date?->format('d M Y') }} - {{ $offer->end_date?->format('d M Y') }}</span>
                    </div>
                </div>
            @empty
                <div class="contact-card">No active offers are available right now.</div>
            @endforelse
        </div>
        <div class="section-sm">{{ $offers->links() }}</div>
    </section>
@endsection

