@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Loyalty')

@section('content')
    @php
        $loyaltyMeta = $setting?->meta ?? [];
        $pointValue = $loyaltyMeta['loyalty_point_value'] ?? 1;
        $earnRate = $loyaltyMeta['loyalty_earn_per_amount'] ?? 100;
    @endphp
    <section class="page-hero">
        <div class="container page-hero-box">
            <div class="eyebrow">Loyalty</div>
            <h1 class="section-title">Track points and rewards from your orders.</h1>
            <p class="section-copy">Earn points on every order and redeem them at checkout for instant discounts.</p>
        </div>
    </section>

    <section class="section-sm">
        <div class="container">
            <div class="stats-strip">
                <div class="stat-card-lite">
                    <div class="eyebrow">Total Points</div>
                    <strong>{{ $balance }}</strong>
                    <span>Available loyalty balance</span>
                </div>
                <div class="stat-card-lite">
                    <div class="eyebrow">Redeem Value</div>
                    <strong>{{ number_format($balance * $pointValue, 0) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                    <span>Based on current point value</span>
                </div>
                <div class="stat-card-lite">
                    <div class="eyebrow">Earn Rate</div>
                    <strong>1 pt / {{ $earnRate }}</strong>
                    <span>Earned on order subtotal</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container commerce-shell">
            <div class="commerce-card">
                <div class="section-title">Transaction History</div>
                @forelse($transactions as $transaction)
                    <div class="feature-card" style="margin-bottom: 12px;">
                        <div class="menu-meta">
                            <span class="pill">{{ strtoupper($transaction->type) }}</span>
                            <span class="pill">{{ $transaction->points }} pts</span>
                        </div>
                        <p class="muted">{{ $transaction->note ?: 'Loyalty transaction' }}</p>
                        <small class="muted">{{ $transaction->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                @empty
                    <div class="feature-card">No loyalty transactions yet.</div>
                @endforelse

                <div class="pagination-shell">
                    {{ $transactions->links() }}
                </div>
            </div>

            <div class="commerce-sticky">
                <div class="commerce-card">
                    <div class="section-title" style="margin-top:0;">Redeem Tips</div>
                    <div class="summary-stack">
                        <div class="summary-row">
                            <span class="muted">Redeem at</span>
                            <strong>Checkout</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Point value</span>
                            <strong>{{ $pointValue }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Earn rate</span>
                            <strong>1 pt / {{ $earnRate }}</strong>
                        </div>
                    </div>
                    <div class="hero-actions">
                        <a class="btn" href="{{ route('frontend.menu.index') }}">Shop Menu</a>
                        <a class="btn-outline" href="{{ route('frontend.account.profile') }}">Account</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
