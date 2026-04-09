@extends('frontend.layouts.app')

@section('title', 'Order '.$order->order_no.' | '.($setting->app_name ?? config('app.name', 'FoodiHub')))

@section('content')
    <section class="page-hero">
        <div class="container order-banner">
            <div class="order-banner-card primary">
                <div class="eyebrow">Order Confirmed</div>
                <h1 class="section-title">Your order {{ $order->order_no }} is now in the system.</h1>
                <p class="section-copy">This confirmation page follows the same commerce design language and shows operational details clearly for the customer.</p>
            </div>
            <div class="order-banner-card dark">
                <div class="eyebrow" style="background: rgba(255,255,255,0.12); color:#fff;">Next Step</div>
                <p style="margin:10px 0 0;color:rgba(255,255,255,0.78);">Branch and payment details are already attached to the admin workflow, so support and kitchen teams are reading the same order.</p>
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container commerce-shell">
            <div class="commerce-card">
                @if(session('success'))
                    <div class="flash-success">{{ session('success') }}</div>
                @endif
                <div class="grid-2">
                    <div class="feature-card"><h3>Status</h3><p class="muted">{{ $order->status->value }}</p></div>
                    <div class="feature-card"><h3>Order Type</h3><p class="muted">{{ $order->order_type->value }}</p></div>
                    <div class="feature-card"><h3>Branch</h3><p class="muted">{{ $order->branch?->name ?: '-' }}</p></div>
                    <div class="feature-card"><h3>Customer</h3><p class="muted">{{ $order->customer?->name ?: '-' }}</p></div>
                </div>
            </div>

            <div class="commerce-card">
                <div class="section-title">Payment</div>
                @foreach($order->payments as $payment)
                    <div class="feature-card" style="margin-bottom: 14px;">
                        <h3>{{ $payment->gateway_code ? \App\Support\PaymentGatewayCatalog::displayLabel($payment->gateway_code) : $payment->method->value }}</h3>
                        <p class="muted">Status: {{ $payment->status->value }}</p>
                        <p class="muted">Country: {{ $order->billing_country ? \App\Support\CountryCatalog::displayName($order->billing_country) : '-' }}</p>
                        <p class="muted">Gateway: {{ $payment->gateway_code ? \App\Support\PaymentGatewayCatalog::displayLabel($payment->gateway_code) : '-' }}</p>
                        <p class="muted">Amount: {{ number_format((float) $payment->amount, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</p>
                        @if(($payment->gateway_payload['payment_url'] ?? null) || ($payment->gateway_payload['checkout_url'] ?? null))
                            <a href="{{ $payment->gateway_payload['payment_url'] ?? $payment->gateway_payload['checkout_url'] }}" class="btn" style="margin-top:10px;" target="_blank" rel="noopener">Open Payment Link</a>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="commerce-card">
                <div class="section-title">Order Summary</div>
                <div class="summary-stack">
                    <div class="summary-row">
                        <span class="muted">Subtotal</span>
                        <strong>{{ number_format((float) $order->subtotal, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                    </div>
                    <div class="summary-row">
                        <span class="muted">Tax</span>
                        <strong>{{ number_format((float) $order->tax_amount, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                    </div>
                    <div class="summary-row">
                        <span class="muted">Service Charge</span>
                        <strong>{{ number_format((float) $order->service_charge, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                    </div>
                    <div class="summary-row">
                        <span class="muted">Packing Fee</span>
                        <strong>{{ number_format((float) $order->packing_fee, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                    </div>
                    <div class="summary-row">
                        <span class="muted">Delivery Fee</span>
                        <strong>{{ number_format((float) $order->delivery_fee, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                    </div>
                    <div class="summary-row">
                        <span class="muted">Discount</span>
                        <strong>-{{ number_format((float) $order->discount_amount, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</strong>
                    </div>
                    <div class="summary-row">
                        <span class="summary-total">Total</span>
                        <span class="summary-total">{{ number_format((float) $order->total_amount, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</span>
                    </div>
                </div>
                @if($order->coupon_code)
                    <div class="pill" style="margin-top:10px;">Coupon: {{ $order->coupon_code }}</div>
                @endif
                @if($order->loyalty_points_used > 0)
                    <div class="pill" style="margin-top:10px;">Loyalty used: {{ $order->loyalty_points_used }} pts</div>
                @endif
            </div>

            <div class="commerce-card">
                <div class="section-title">Delivery Tracking</div>
                @forelse($trackingEvents as $event)
                    <div class="feature-card" style="margin-bottom: 12px;">
                        <div class="menu-meta">
                            <span class="pill">{{ $event->status }}</span>
                            <span class="pill">{{ $event->event_at?->format('d M Y, h:i A') }}</span>
                        </div>
                        <p class="muted">{{ $event->message ?: 'Tracking update received.' }}</p>
                        @if($event->location)
                            <small class="muted">Location: {{ $event->location }}</small>
                        @endif
                    </div>
                @empty
                    <div class="feature-card">No delivery updates yet.</div>
                @endforelse
            </div>

            <div class="commerce-card">
                <div class="section-title">Refunds</div>
                @forelse($refunds as $refund)
                    <div class="feature-card" style="margin-bottom: 12px;">
                        <div class="menu-meta">
                            <span class="pill">{{ strtoupper($refund->status) }}</span>
                            <span class="pill">{{ number_format((float) $refund->amount, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</span>
                        </div>
                        <p class="muted">{{ $refund->reason ?: 'Refund request' }}</p>
                    </div>
                @empty
                    <div class="feature-card">No refunds requested.</div>
                @endforelse

                @if($canRequestRefund)
                    <form method="POST" action="{{ route('frontend.orders.refund', $order) }}" style="margin-top: 16px;">
                        @csrf
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="refund_amount">Refund Amount</label>
                                <input id="refund_amount" class="input" type="number" step="0.01" name="amount" required>
                            </div>
                            <div class="form-field full">
                                <label for="refund_reason">Reason</label>
                                <textarea id="refund_reason" class="textarea" name="reason"></textarea>
                            </div>
                        </div>
                        <div class="hero-actions">
                            <button class="btn-outline" type="submit">Request Refund</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container">
            <div class="section-title">Ordered Items</div>
            <div class="grid-3">
                @foreach($order->items as $item)
                    @php
                        $orderedProduct = $item->product;
                        $orderedImage = $orderedProduct && is_array($orderedProduct->images) && isset($orderedProduct->images[0]) ? $orderedProduct->images[0] : null;
                        $orderedName = $item->product_name_snapshot ?: $orderedProduct?->name;
                    @endphp
                    <div class="menu-card menu-result-card">
                        <div class="menu-image">
                            <img src="{{ \App\Support\ImagePath::thumbUrl($orderedImage, \App\Support\FoodImageResolver::product($orderedName, 1)) }}" alt="{{ $orderedName }}">
                            <div class="image-badge">Ordered</div>
                        </div>
                        <h3>{{ $orderedName }}</h3>
                        <div class="meta-inline">
                            <span class="pill">Qty: {{ $item->quantity }}</span>
                            <span class="pill">Unit: {{ number_format((float) $item->unit_price, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</span>
                            <span class="pill">Line: {{ number_format((float) $item->line_total, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

