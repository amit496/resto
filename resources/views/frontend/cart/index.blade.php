@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Cart')

@section('content')
    <section class="page-hero">
        <div class="container page-hero-box">
            <h1 class="section-title">Review your order before checkout.</h1>
            <p class="section-copy">This cart now follows a cleaner commerce layout with image-led items, quick quantity control and a tighter summary panel.</p>
        </div>
    </section>

    <section class="section-sm">
        <div class="container order-banner" style="margin-bottom: 22px;">
            <div class="order-banner-card primary">
                <div class="eyebrow">Ready To Checkout</div>
                <h2 class="section-title" style="font-size:42px;margin-bottom:10px;">Everything selected in one clean basket.</h2>
                <p class="section-copy" style="margin-bottom:0;">Update quantities, remove items and move to checkout without losing the menu flow.</p>
            </div>
            <div class="order-banner-card dark">
                <div class="eyebrow" style="background: rgba(255,255,255,0.12); color:#fff;">Why It Works</div>
                <div class="trust-row">
                    <div class="trust-chip">Quick edits</div>
                    <div class="trust-chip">Branch checkout</div>
                    <div class="trust-chip">Real admin order</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-sm" style="padding-top: 0;">
        <div class="container commerce-shell">
            <div class="commerce-card cart-items-card">
                @if(session('success'))
                    <div class="flash success">{{ session('success') }}</div>
                @endif

                @forelse($cart['items'] as $item)
                    @php
                        $cartImage = is_array($item['product']->images) && isset($item['product']->images[0]) ? $item['product']->images[0] : null;
                    @endphp
                    <div class="commerce-item cart-item">
                        <a href="{{ route('frontend.menu.show', $item['product']) }}" class="cart-thumb-link" aria-label="View {{ $item['product']->name }}">
                            <img src="{{ \App\Support\ImagePath::thumbUrl($cartImage, \App\Support\FoodImageResolver::product($item['product']->name, 1)) }}" alt="{{ $item['product']->name }}" class="commerce-thumb">
                        </a>
                        <div class="cart-item-body">
                            <div>
                                <h3 class="cart-item-title">{{ $item['product']->name }}</h3>
                                <p class="muted">{{ $item['variant'] ? $item['variant']->name.' - '.$item['variant']->value : 'Base Item' }}</p>
                                <div class="meta-inline">
                                    <span class="pill">Unit: {{ number_format((float) $item['unit_price'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</span>
                                    <span class="pill">Line: {{ number_format((float) $item['line_total'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</span>
                                </div>
                            </div>
                            <div class="inline-actions">
                                <form method="POST" action="{{ route('frontend.cart.items.update', $item['key']) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="qty-control">
                                        <button type="submit" name="delta" value="-1" class="icon-btn" aria-label="Decrease quantity" title="Decrease quantity">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M5 12h14"></path>
                                            </svg>
                                        </button>
                                        <input class="input qty-field" type="number" min="0" max="20" name="quantity" value="{{ $item['quantity'] }}" readonly>
                                        <button type="submit" name="delta" value="1" class="icon-btn" aria-label="Increase quantity" title="Increase quantity">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M12 5v14"></path>
                                                <path d="M5 12h14"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('frontend.cart.items.destroy', $item['key']) }}" class="cart-remove-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn danger" aria-label="Remove item" title="Remove item">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M3 6h18"></path>
                                            <path d="M8 6V4h8v2"></path>
                                            <path d="M6 6l1 14h10l1-14"></path>
                                            <path d="M10 11v5"></path>
                                            <path d="M14 11v5"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="feature-card">Your cart is empty.</div>
                @endforelse
            </div>

            <div class="commerce-sticky">
                <div class="commerce-card cart-summary-card">
                    <div class="cart-summary-head">
                        <div class="section-title" style="margin-top:0;">Cart Summary</div>
                        <span class="pill">{{ $cart['count'] }} items</span>
                    </div>
                    @if(($setting?->enable_coupons ?? true) === true)
                        <form method="POST" action="{{ route('frontend.cart.coupon.apply') }}" class="cart-summary-form">
                            @csrf
                            <div class="cart-input-row">
                                <input class="input" type="text" name="code" placeholder="Coupon Code" value="{{ old('code', $cart['coupon_code']) }}">
                                <button type="submit" class="btn-outline">Apply</button>
                            </div>
                        </form>
                        @if($cart['coupon_code'])
                            <form method="POST" action="{{ route('frontend.cart.coupon.clear') }}" class="cart-summary-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-outline">Remove Coupon</button>
                            </form>
                        @endif
                        @if($cart['coupon_error'])
                            <div class="flash error" style="margin-bottom:12px;">{{ $cart['coupon_error'] }}</div>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('frontend.cart.loyalty.update') }}" class="cart-summary-form">
                        @csrf
                        <div class="cart-input-row">
                            <input class="input" type="number" min="0" name="loyalty_points" value="{{ $cart['loyalty_points_used'] }}">
                            <button type="submit" class="btn-outline">Use Points</button>
                        </div>
                        <small class="muted">Available: {{ $cart['loyalty_balance'] }} pts</small>
                    </form>
                    <div class="summary-stack cart-summary-stack">
                        <div class="summary-row">
                            <span class="muted">Total Items</span>
                            <strong>{{ $cart['count'] }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Subtotal</span>
                            <strong>{{ number_format((float) $cart['subtotal'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Tax</span>
                            <strong>{{ number_format((float) $cart['tax_amount'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Service Charge</span>
                            <strong>{{ number_format((float) $cart['service_charge'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Packing Fee</span>
                            <strong>{{ number_format((float) $cart['packing_fee'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Delivery Fee</span>
                            <strong>{{ number_format((float) $cart['delivery_fee'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Discounts</span>
                            <strong>-{{ number_format((float) $cart['discount_amount'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                        </div>
                        <div class="summary-row is-total">
                            <span class="summary-total">Estimated Total</span>
                            <span class="summary-total">{{ number_format((float) $cart['total'], 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</span>
                        </div>
                    </div>
                    <div class="cart-summary-footer">
                        <div class="cart-actions">
                            <a href="{{ route('frontend.menu.index') }}" class="btn-outline">Continue Shopping</a>
                            @if($cart['count'] > 0)
                                <a href="{{ route('frontend.checkout.create') }}" class="btn">Proceed To Checkout</a>
                            @endif
                        </div>
                        <div class="cart-trust">
                            <div class="trust-chip">Freshly prepared</div>
                            <div class="trust-chip">Secure payment flow</div>
                            <div class="trust-chip">Slip generated in admin</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
