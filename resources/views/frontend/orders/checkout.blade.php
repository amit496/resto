@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Checkout')

@section('content')
    @php
        $currencySymbol = $setting->currency_symbol ?? 'Rs';
        $availableGateways = collect($paymentGateways ?? [])->filter(fn ($gateway) => ($gateway['enabled'] ?? false))->values();
    @endphp
    <section class="page-hero">
        <div class="container order-banner">
            <div class="order-banner-card primary">
                <div class="eyebrow">Checkout</div>
                <h1 class="section-title">Complete the order with branch, payment and customer details.</h1>
                <p class="section-copy">This checkout now follows a cleaner commercial template structure with a guided form on the left and a sticky summary on the right.</p>
            </div>
            <div class="order-banner-card dark">
                <div class="eyebrow" style="background: rgba(255,255,255,0.12); color:#fff;">Order Flow</div>
                <div class="trust-row">
                    <div class="trust-chip">Branch routed</div>
                    <div class="trust-chip">Payment logged</div>
                    <div class="trust-chip">Admin visible instantly</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container commerce-shell">
            <div class="commerce-card">
                @if($errors->any())
                    <div class="error-box">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('frontend.checkout.store') }}">
                    @csrf
                    <div class="form-grid">
                        @if($customer)
                            @php
                                $addresses = collect($customerAddresses ?? []);
                                $defaultAddress = $addresses->firstWhere('is_default', true) ?: $addresses->first();
                            @endphp
                            <div class="form-field full">
                                <label for="customer_address_id">Saved Address</label>
                                <div class="address-pick">
                                    <label class="address-card address-card-empty">
                                        <input
                                            type="radio"
                                            name="customer_address_id"
                                            value=""
                                            @checked(old('customer_address_id', $defaultAddress?->id) === null)
                                            data-address-radio
                                            data-recipient=""
                                            data-phone=""
                                            data-line1=""
                                            data-line2=""
                                            data-landmark=""
                                            data-city=""
                                            data-state=""
                                            data-postal=""
                                            data-country=""
                                            data-instructions=""
                                        >
                                        <div class="address-card-body">
                                            <strong>Enter a new address</strong>
                                            <div class="muted" style="font-size:12px;">Fill the delivery address fields below.</div>
                                        </div>
                                    </label>
                                    @foreach($addresses as $addr)
                                        <label class="address-card">
                                            <input
                                                type="radio"
                                                name="customer_address_id"
                                                value="{{ $addr->id }}"
                                                @checked((int) old('customer_address_id', $defaultAddress?->id) === $addr->id)
                                                data-address-radio
                                                data-recipient="{{ $addr->recipient_name }}"
                                                data-phone="{{ $addr->phone }}"
                                                data-line1="{{ $addr->address_line_1 }}"
                                                data-line2="{{ $addr->address_line_2 }}"
                                                data-landmark="{{ $addr->landmark }}"
                                                data-city="{{ $addr->city }}"
                                                data-state="{{ $addr->state }}"
                                                data-postal="{{ $addr->postal_code }}"
                                                data-country="{{ $addr->country_code }}"
                                                data-instructions="{{ $addr->instructions }}"
                                            >
                                            <div class="address-card-body">
                                                <div class="address-card-top">
                                                    <div class="menu-meta">
                                                        <span class="pill">{{ strtoupper($addr->type) }}</span>
                                                        @if($addr->is_default)
                                                            <span class="pill" style="background: rgba(245,158,11,0.14); color:#92400e;">DEFAULT</span>
                                                        @endif
                                                    </div>
                                                    <span class="muted" style="font-size:12px;">{{ $addr->label ?: ucfirst($addr->type) }}</span>
                                                </div>
                                                <strong style="display:block;margin-top:6px;">
                                                    {{ $addr->recipient_name ?: $customer->name }}
                                                    @if($addr->phone || $customer->phone)
                                                        <span class="muted" style="font-weight:700;"> · {{ $addr->phone ?: $customer->phone }}</span>
                                                    @endif
                                                </strong>
                                                <div class="muted" style="font-size:13px;line-height:1.5;margin-top:6px;">{{ $addr->formatted() }}</div>
                                                @if($addr->instructions)
                                                    <div class="muted" style="font-size:12px;margin-top:6px;">Instructions: {{ $addr->instructions }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <small class="muted">
                                    Manage addresses in <a href="{{ route('frontend.account.addresses.index') }}">My Addresses</a>.
                                </small>
                            </div>
                        @endif
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input id="name" class="input" type="text" name="name" value="{{ old('name', $customer?->name) }}" required>
                        </div>
                        <div class="form-field">
                            <label for="email">Email</label>
                            <input id="email" class="input" type="email" name="email" value="{{ old('email', $customer?->email) }}" required>
                        </div>
                    <div class="form-field">
                        <label for="phone">Phone</label>
                        <input id="phone" class="input" type="text" name="phone" value="{{ old('phone', $customer?->phone) }}">
                    </div>
                    <div class="form-field">
                        <label for="delivery_name">Delivery Name</label>
                        <input id="delivery_name" class="input" type="text" name="delivery_name" value="{{ old('delivery_name') }}" placeholder="Receiver name (optional)">
                    </div>
                    <div class="form-field">
                        <label for="delivery_phone">Delivery Contact</label>
                        <input id="delivery_phone" class="input" type="text" name="delivery_phone" value="{{ old('delivery_phone') }}" placeholder="Receiver phone (optional)">
                    </div>
                    <div class="form-field">
                        <label for="billing_country">Country</label>
                        <select id="billing_country" class="select" name="billing_country" required>
                            @foreach($countries as $country)
                                <option value="{{ $country['code'] }}" @selected(old('billing_country', $selectedCountry ?? null) === $country['code'])>
                                    {{ $country['name'] }} ({{ $country['code'] }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="branch_id">Branch</label>
                        <select id="branch_id" class="select" name="branch_id" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected((int) old('branch_id') === $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-field">
                            <label for="order_type">Order Type</label>
                            <select id="order_type" class="select" name="order_type" required>
                                <option value="delivery" @selected(old('order_type') === 'delivery')>Delivery</option>
                            <option value="takeaway" @selected(old('order_type') === 'takeaway')>Takeaway</option>
                            <option value="dine_in" @selected(old('order_type') === 'dine_in')>Dine In</option>
                        </select>
                    </div>
                        <div class="form-field full">
                            <label>Payment Gateway</label>
                            <div class="gateway-grid" id="gateway-grid">
                                @forelse($availableGateways as $gateway)
                                    @php
                                        $gatewayCountries = implode(',', $gateway['countries'] ?? ['*']);
                                        $gatewayCode = $gateway['code'] ?? '';
                                    @endphp
                                    <label class="gateway-card" data-gateway-card data-countries="{{ $gatewayCountries }}" data-gateway-code="{{ $gatewayCode }}" data-channel="{{ $gateway['channel'] ?? 'online' }}">
                                        <input type="radio" name="payment_gateway" value="{{ $gatewayCode }}" @checked(old('payment_gateway', $selectedGateway ?? null) === $gatewayCode)>
                                        <div class="gateway-card-body">
                                            <div class="menu-meta" style="margin-bottom:8px;">
                                                <span class="pill">{{ strtoupper($gateway['channel'] ?? 'online') }}</span>
                                                @if(($gateway['countries'] ?? ['*']) !== ['*'])
                                                    <span class="pill">{{ count($gateway['countries'] ?? []) }} countries</span>
                                                @else
                                                    <span class="pill">Global</span>
                                                @endif
                                            </div>
                                            <h3 style="margin:0 0 4px;">{{ $gateway['label'] ?? $gatewayCode }}</h3>
                                            <p class="muted" style="margin:0;">{{ $gateway['description'] ?? 'Payment gateway' }}</p>
                                        </div>
                                    </label>
                                @empty
                                    <div class="feature-card">No payment gateways are enabled for this country yet.</div>
                                @endforelse
                            </div>
                            <small class="muted">Country selection decides which payment gateways appear here.</small>
                        </div>
                        @if(($setting?->enable_coupons ?? true) === true)
                            <div class="form-field">
                                <label for="coupon_code">Coupon Code</label>
                                <input id="coupon_code" class="input" type="text" name="coupon_code" value="{{ old('coupon_code', $cart['coupon_code']) }}">
                                @if($cart['coupon_error'])
                                    <small class="muted">{{ $cart['coupon_error'] }}</small>
                                @endif
                            </div>
                        @endif
                        <div class="form-field">
                            <label for="loyalty_points">Use Loyalty Points</label>
                            <input id="loyalty_points" class="input" type="number" min="0" name="loyalty_points" value="{{ old('loyalty_points', $cart['loyalty_points_used']) }}">
                            <small class="muted">Available: {{ $cart['loyalty_balance'] }} pts</small>
                        </div>
                        <div class="form-field full">
                            <label for="delivery_address_line_1">Address Line 1</label>
                            <textarea id="delivery_address_line_1" class="textarea" name="delivery_address_line_1" required>{{ old('delivery_address_line_1', old('address', $customer?->address)) }}</textarea>
                        </div>
                        <div class="form-field full">
                            <label for="delivery_address_line_2">Address Line 2</label>
                            <textarea id="delivery_address_line_2" class="textarea" name="delivery_address_line_2">{{ old('delivery_address_line_2') }}</textarea>
                        </div>
                        <div class="form-field">
                            <label for="delivery_landmark">Landmark</label>
                            <input id="delivery_landmark" class="input" type="text" name="delivery_landmark" value="{{ old('delivery_landmark') }}" placeholder="Near ...">
                        </div>
                        <div class="form-field">
                            <label for="delivery_city">City</label>
                            <input id="delivery_city" class="input" type="text" name="delivery_city" value="{{ old('delivery_city') }}">
                        </div>
                        <div class="form-field">
                            <label for="delivery_state">State</label>
                            <input id="delivery_state" class="input" type="text" name="delivery_state" value="{{ old('delivery_state') }}">
                        </div>
                        <div class="form-field">
                            <label for="delivery_postal_code">Pincode</label>
                            <input id="delivery_postal_code" class="input" type="text" name="delivery_postal_code" value="{{ old('delivery_postal_code') }}">
                        </div>
                        <div class="form-field">
                            <label for="transaction_ref">Transaction Reference</label>
                            <input id="transaction_ref" class="input" type="text" name="transaction_ref" value="{{ old('transaction_ref') }}">
                        </div>
                        <div class="form-field full">
                            <label for="notes">Notes</label>
                            <textarea id="notes" class="textarea" name="notes">{{ old('notes') }}</textarea>
                        </div>
                        <div class="form-field full">
                            <label for="delivery_instructions">Delivery Instructions</label>
                            <textarea id="delivery_instructions" class="textarea" name="delivery_instructions">{{ old('delivery_instructions') }}</textarea>
                        </div>
                    </div>
                    <div class="hero-actions">
                        <button type="submit" class="btn">Place Order</button>
                    </div>
                </form>
            </div>

            <div class="commerce-sticky">
                <div class="commerce-card">
                    <div class="section-title" style="margin-top:0;">Order Summary</div>
                    <div class="feature-card" style="margin-bottom: 16px;">
                        <h3 style="margin-top:0;">Receipt Preview</h3>
                        <p class="muted" style="margin-bottom:0;">Your checkout receipt will be generated right after order placement and shown on the confirmation page.</p>
                    </div>
                    @foreach($cart['items'] as $item)
                        @php
                            $checkoutImage = is_array($item['product']->images) && isset($item['product']->images[0]) ? $item['product']->images[0] : null;
                        @endphp
                        <div class="commerce-item">
                            <img src="{{ \App\Support\ImagePath::thumbUrl($checkoutImage, \App\Support\FoodImageResolver::product($item['product']->name, 1)) }}" alt="{{ $item['product']->name }}" class="commerce-thumb">
                            <div style="display:grid;gap:8px;">
                                <h3 style="margin:0;">{{ $item['product']->name }}</h3>
                                <div class="meta-inline">
                                    <span class="pill">Qty: {{ $item['quantity'] }}</span>
                                    <span class="pill">Unit: {{ number_format((float) $item['unit_price'], 2) }} {{ $currencySymbol }}</span>
                                    <span class="pill">Line: {{ number_format((float) $item['line_total'], 2) }} {{ $currencySymbol }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="summary-stack" style="margin-top:16px;">
                        <div class="summary-row">
                            <span class="muted">Items</span>
                            <strong>{{ $cart['count'] }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Subtotal</span>
                            <strong>{{ number_format((float) $cart['subtotal'], 2) }} {{ $currencySymbol }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Tax ({{ $cart['tax_percent'] }}%)</span>
                            <strong>{{ number_format((float) $cart['tax_amount'], 2) }} {{ $currencySymbol }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Service Charge</span>
                            <strong>{{ number_format((float) $cart['service_charge'], 2) }} {{ $currencySymbol }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Packing Fee</span>
                            <strong>{{ number_format((float) $cart['packing_fee'], 2) }} {{ $currencySymbol }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Delivery Fee</span>
                            <strong>{{ number_format((float) $cart['delivery_fee'], 2) }} {{ $currencySymbol }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="muted">Discounts</span>
                            <strong>-{{ number_format((float) $cart['discount_amount'], 2) }} {{ $currencySymbol }}</strong>
                        </div>
                        <div class="summary-row">
                            <span class="summary-total">Total</span>
                            <span class="summary-total">{{ number_format((float) $cart['total'], 2) }} {{ $currencySymbol }}</span>
                        </div>
                    </div>
                    <div class="trust-row">
                        <div class="trust-chip">Country aware checkout</div>
                        <div class="trust-chip">Gateway filtered by region</div>
                        <div class="trust-chip">Receipt on confirmation</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .gateway-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-top: 10px;
        }
        .gateway-card {
            display: block;
            position: relative;
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 18px;
            padding: 16px;
            background: #fff;
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }
        .gateway-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
            border-color: rgba(226, 55, 68, 0.38);
        }
        .gateway-card input {
            position: absolute;
            inset: 14px auto auto 14px;
        }
        .gateway-card-body {
            padding-left: 26px;
        }
        .gateway-card.is-hidden {
            display: none;
        }
        .gateway-card.is-selected {
            border-color: #e23744;
            box-shadow: 0 14px 32px rgba(226, 55, 68, 0.18);
        }
    </style>

    <script>
        (function () {
            const countrySelect = document.getElementById('billing_country');
            const gatewayCards = Array.from(document.querySelectorAll('[data-gateway-card]'));
            const addressRadios = Array.from(document.querySelectorAll('[data-address-radio]'));

            const normalize = (value) => (value || '').trim().toUpperCase();
            const setValue = (id, value) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.value = value == null ? '' : String(value);
            };

            const syncSelection = () => {
                const country = normalize(countrySelect ? countrySelect.value : '');
                let firstVisible = null;

                gatewayCards.forEach((card) => {
                    const countries = (card.dataset.countries || '*')
                        .split(',')
                        .map((entry) => normalize(entry))
                        .filter(Boolean);
                    const supported = countries.includes('*') || countries.includes(country) || country === '';
                    card.classList.toggle('is-hidden', !supported);

                    const input = card.querySelector('input[type="radio"]');
                    if (!input) {
                        return;
                    }

                    if (supported && !firstVisible) {
                        firstVisible = card;
                    }
                });

                const selected = gatewayCards.find((card) => {
                    const input = card.querySelector('input[type="radio"]');
                    return input && input.checked && !card.classList.contains('is-hidden');
                });

                const target = selected || firstVisible;
                if (target) {
                    const input = target.querySelector('input[type="radio"]');
                    if (input && !input.checked) {
                        input.checked = true;
                    }
                }

                gatewayCards.forEach((card) => {
                    const input = card.querySelector('input[type="radio"]');
                    card.classList.toggle('is-selected', !!input && input.checked);
                });
            };

            const syncAddress = () => {
                if (!addressRadios.length) return;
                const selected = addressRadios.find((radio) => radio.checked);
                addressRadios.forEach((radio) => {
                    const card = radio.closest('.address-card');
                    if (!card) return;
                    card.classList.toggle('is-selected', radio.checked);
                });
                if (!selected) return;
                if (!selected.value) {
                    setValue('delivery_name', '');
                    setValue('delivery_phone', '');
                    setValue('delivery_address_line_1', '');
                    setValue('delivery_address_line_2', '');
                    setValue('delivery_landmark', '');
                    setValue('delivery_city', '');
                    setValue('delivery_state', '');
                    setValue('delivery_postal_code', '');
                    setValue('delivery_instructions', '');
                    return;
                }
                setValue('delivery_name', selected.dataset.recipient || '');
                setValue('delivery_phone', selected.dataset.phone || '');
                setValue('delivery_address_line_1', selected.dataset.line1 || '');
                setValue('delivery_address_line_2', selected.dataset.line2 || '');
                setValue('delivery_landmark', selected.dataset.landmark || '');
                setValue('delivery_city', selected.dataset.city || '');
                setValue('delivery_state', selected.dataset.state || '');
                setValue('delivery_postal_code', selected.dataset.postal || '');
                setValue('delivery_instructions', selected.dataset.instructions || '');
                if (countrySelect && selected.dataset.country) {
                    countrySelect.value = selected.dataset.country;
                }
                syncSelection();
            };

            gatewayCards.forEach((card) => {
                const input = card.querySelector('input[type="radio"]');
                if (!input) {
                    return;
                }

                input.addEventListener('change', syncSelection);
            });

            if (countrySelect) {
                countrySelect.addEventListener('change', syncSelection);
            }
            addressRadios.forEach((radio) => radio.addEventListener('change', syncAddress));

            syncSelection();
            syncAddress();
        })();
    </script>
@endsection
