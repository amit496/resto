@php
    $gatewayDefinitions = config('payment_gateways.definitions', []);
    $paymentGatewaySettings = $meta['payment_gateways'] ?? [];
    $defaultCountry = old('payment_default_country', $meta['payment_default_country'] ?? config('payment_gateways.default_country', 'IN'));
@endphp

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Default Checkout Country</label>
        <select class="form-control" name="payment_default_country" required>
            @foreach($countries as $country)
                <option value="{{ $country['code'] }}" @selected($defaultCountry === $country['code'])>
                    {{ $country['name'] }} ({{ $country['code'] }})
                </option>
            @endforeach
        </select>
        <small class="text-muted">Frontend checkout will open with this country selected first.</small>
    </div>
</div>

<div class="row g-3">
    @foreach($gatewayDefinitions as $code => $gateway)
        @php
            $stored = $paymentGatewaySettings[$code] ?? [];
            $gatewayEnabled = old("payment_gateways.$code.enabled", $stored['enabled'] ?? $gateway['enabled'] ?? false);
            $gatewayLabel = old("payment_gateways.$code.label", $stored['label'] ?? $gateway['label'] ?? $code);
            $gatewayMode = old("payment_gateways.$code.mode", $stored['mode'] ?? 'live');
            $gatewayCountries = old("payment_gateways.$code.countries", implode(', ', $stored['countries'] ?? $gateway['countries'] ?? ['*']));
            $gatewayCredentials = $stored['credentials'] ?? [];
        @endphp
        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="mb-1">{{ $gateway['label'] ?? $code }}</h5>
                            <div class="text-muted small">{{ $gateway['description'] ?? 'Payment gateway configuration.' }}</div>
                        </div>
                        <span class="badge bg-light text-dark text-uppercase">{{ $gateway['channel'] ?? 'online' }}</span>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Enable</label>
                            <select class="form-control" name="payment_gateways[{{ $code }}][enabled]">
                                <option value="1" @selected((bool) $gatewayEnabled)>Yes</option>
                                <option value="0" @selected(! (bool) $gatewayEnabled)>No</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Label</label>
                            <input class="form-control" name="payment_gateways[{{ $code }}][label]" value="{{ $gatewayLabel }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Mode</label>
                            <select class="form-control" name="payment_gateways[{{ $code }}][mode]">
                                <option value="live" @selected($gatewayMode === 'live')>Live</option>
                                <option value="test" @selected($gatewayMode === 'test')>Test</option>
                                <option value="manual" @selected($gatewayMode === 'manual')>Manual</option>
                            </select>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Supported Countries</label>
                            <input class="form-control" name="payment_gateways[{{ $code }}][countries]" value="{{ $gatewayCountries }}" placeholder="IN, US, GB">
                            <small class="text-muted">Comma separated ISO country codes. Use <code>*</code> for all countries.</small>
                        </div>
                    </div>

                    <div class="row">
                        @foreach($gateway['fields'] ?? [] as $field)
                            @php
                                $fieldValue = old("payment_gateways.$code.credentials.".$field['key'], $gatewayCredentials[$field['key']] ?? '');
                            @endphp
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ $field['label'] }}</label>
                                <input
                                    class="form-control"
                                    type="{{ $field['type'] ?? 'text' }}"
                                    name="payment_gateways[{{ $code }}][credentials][{{ $field['key'] }}]"
                                    value="{{ $fieldValue }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                >
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
