@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Order History')

@section('content')
    <section class="page-hero">
        <div class="container page-hero-box">
            <div class="eyebrow">Orders</div>
            <h1 class="section-title">Your recent orders in one place.</h1>
            <p class="section-copy">Track status, view payment details, and access delivery updates quickly.</p>
        </div>
    </section>

    <section class="section-sm">
        <div class="container">
            @if($orders->count() > 0)
                <div class="grid-3">
                    @foreach($orders as $order)
                        <div class="menu-card menu-result-card">
                            <div class="menu-meta" style="margin-bottom:10px;">
                                <span class="pill">{{ $order->status->value }}</span>
                                <span class="pill">{{ $order->order_type->value }}</span>
                            </div>
                            <h3>{{ $order->order_no }}</h3>
                            <p class="muted">Branch: {{ $order->branch?->name ?: '-' }}</p>
                            <p class="muted">Total: {{ number_format((float) $order->total_amount, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</p>
                            <div class="hero-actions">
                                <a class="btn-outline" href="{{ route('frontend.orders.show', $order) }}">View Order</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pagination-shell">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="empty-state-card">
                    <h3 class="home-card-title">No orders yet.</h3>
                    <p class="home-card-copy">Browse the menu and place your first order.</p>
                    <a class="btn" href="{{ route('frontend.menu.index') }}">Browse Menu</a>
                </div>
            @endif
        </div>
    </section>
@endsection
