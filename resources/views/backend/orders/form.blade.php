@extends('admin.layout.index')
@section('title', $order->exists ? 'Edit Order' : 'Add Order')
@section('content')
    <div class="mb-4"><h3 class="mb-0">{{ $order->exists ? 'Edit Order' : 'Add Order' }}</h3></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ $order->exists ? route('admin.orders.update', $order) : route('admin.orders.store') }}">
            @csrf
            @if($order->exists) @method('PUT') @endif

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Restaurant</label>
                    @if($singleRestaurantMode && $defaultRestaurantId)
                        @php $selectedRestaurant = $restaurants->firstWhere('id', $defaultRestaurantId); @endphp
                        <input type="hidden" name="restaurant_id" value="{{ $defaultRestaurantId }}">
                        <input class="form-control" value="{{ $selectedRestaurant?->name ?: 'Primary Restaurant' }}" readonly>
                    @else
                        <select class="form-control" name="restaurant_id" required>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" @selected(old('restaurant_id', $order->restaurant_id) == $restaurant->id)>{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch</label>
                    <select class="form-control" name="branch_id">
                        <option value="">Select</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(old('branch_id', $order->branch_id) == $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Customer</label>
                    <select class="form-control" name="customer_id">
                        <option value="">Walk-in</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id', $order->customer_id) == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Billing Country</label>
                    <select class="form-control" name="billing_country">
                        <option value="">Select</option>
                        @foreach($countries as $country)
                            <option value="{{ $country['code'] }}" @selected(old('billing_country', $order->billing_country) === $country['code'])>{{ $country['name'] }} ({{ $country['code'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Delivery Boy (For Delivery Orders)</label>
                    <select class="form-control" name="delivery_boy_id">
                        <option value="">Not assigned</option>
                        @foreach($deliveryBoys as $deliveryBoy)
                            <option value="{{ $deliveryBoy->id }}" @selected(old('delivery_boy_id', $order->delivery_boy_id) == $deliveryBoy->id)>{{ $deliveryBoy->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Order Type</label>
                    <select class="form-control" name="order_type">
                        @foreach($orderTypeOptions as $option)
                            <option value="{{ $option->value }}" @selected(old('order_type', $order->order_type?->value ?? 'dine_in') === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        @foreach($orderStatusOptions as $option)
                            <option value="{{ $option->value }}" @selected(old('status', $order->status?->value ?? 'pending') === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h5>Order Items</h5>
            @php
                $items = old('items');
                if (! is_array($items)) {
                    $items = $order->exists
                        ? $order->items->map(fn ($item) => [
                            'product_id' => $item->product_id,
                            'product_variant_id' => $item->product_variant_id,
                            'quantity' => $item->quantity,
                        ])->values()->all()
                        : [];
                }
                if (empty($items)) {
                    $items = [['product_id' => null, 'product_variant_id' => null, 'quantity' => 1]];
                }
            @endphp

            <div id="order-items-container">
                @foreach($items as $index => $item)
                    <div class="row border rounded p-2 mb-2 order-item-row" data-index="{{ $index }}">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label">Product</label>
                            <select class="form-control" name="items[{{ $index }}][product_id]" required>
                                <option value="">Select product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" @selected(($item['product_id'] ?? null) == $product->id)>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label class="form-label">Variant</label>
                            <select class="form-control" name="items[{{ $index }}][product_variant_id]">
                                <option value="">No variant</option>
                                @foreach($products as $product)
                                    @foreach($product->variants as $variant)
                                        <option value="{{ $variant->id }}" @selected(($item['product_variant_id'] ?? null) == $variant->id)>{{ $product->name }} - {{ $variant->name }} {{ $variant->value }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="form-label">Qty</label>
                            <input class="form-control" name="items[{{ $index }}][quantity]" type="number" min="1" value="{{ (int) ($item['quantity'] ?? 1) }}" required>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger w-100 remove-item-btn" title="Remove item">X</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-light mb-3" id="add-order-item-btn">
                <i class="fa-solid fa-plus me-1"></i>Add Item
            </button>

            <h5 class="mt-3">Payment</h5>
            @php
                $existingPayment = $order->payments->first();
                $paymentMethod = old('payment.method', $existingPayment?->method?->value ?? 'cash');
                $paymentGateway = old('payment.gateway_code', $existingPayment?->gateway_code ?? '');
                $paymentGatewayCountry = old('payment.gateway_country', $existingPayment?->gateway_country ?? $order->billing_country ?? '');
                $paymentStatus = old('payment.status', $existingPayment?->status?->value ?? 'pending');
                $paymentAmount = old('payment.amount', $existingPayment?->amount);
                $paymentTransactionRef = old('payment.transaction_ref', $existingPayment?->transaction_ref);
            @endphp
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Method</label>
                    <select class="form-control" name="payment[method]">
                        @foreach($paymentMethodOptions as $option)
                            <option value="{{ $option->value }}" @selected($paymentMethod === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Gateway</label>
                    <select class="form-control" name="payment[gateway_code]">
                        <option value="">Select gateway</option>
                        @foreach($paymentGatewayDefinitions as $code => $gateway)
                            <option value="{{ $code }}" @selected($paymentGateway === $code)>{{ $gateway['label'] ?? $code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Gateway Country</label>
                    <select class="form-control" name="payment[gateway_country]">
                        <option value="">Select country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country['code'] }}" @selected($paymentGatewayCountry === $country['code'])>{{ $country['name'] }} ({{ $country['code'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="payment[status]">
                        @foreach($paymentStatusOptions as $option)
                            <option value="{{ $option->value }}" @selected($paymentStatus === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3"><label class="form-label">Amount</label><input class="form-control" name="payment[amount]" type="number" step="0.01" value="{{ $paymentAmount }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Txn Ref</label><input class="form-control" name="payment[transaction_ref]" value="{{ $paymentTransactionRef }}"></div>
            </div>

            <button class="btn btn-primary">Save</button>
        </form>
    </div></div>

    <template id="order-item-template">
        <div class="row border rounded p-2 mb-2 order-item-row" data-index="__INDEX__">
            <div class="col-md-4 mb-2 mb-md-0">
                <label class="form-label">Product</label>
                <select class="form-control" name="items[__INDEX__][product_id]" required>
                    <option value="">Select product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <label class="form-label">Variant</label>
                <select class="form-control" name="items[__INDEX__][product_variant_id]">
                    <option value="">No variant</option>
                    @foreach($products as $product)
                        @foreach($product->variants as $variant)
                            <option value="{{ $variant->id }}">{{ $product->name }} - {{ $variant->name }} {{ $variant->value }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="form-label">Qty</label>
                <input class="form-control" name="items[__INDEX__][quantity]" type="number" min="1" value="1" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger w-100 remove-item-btn" title="Remove item">X</button>
            </div>
        </div>
    </template>

    <script>
        (function () {
            const container = document.getElementById('order-items-container');
            const addButton = document.getElementById('add-order-item-btn');
            const template = document.getElementById('order-item-template');

            let index = container.querySelectorAll('.order-item-row').length;

            const refreshRemoveButtonState = () => {
                const rows = container.querySelectorAll('.order-item-row');
                rows.forEach((row) => {
                    const button = row.querySelector('.remove-item-btn');
                    button.disabled = rows.length <= 1;
                });
            };

            addButton.addEventListener('click', () => {
                const html = template.innerHTML.replaceAll('__INDEX__', index);
                container.insertAdjacentHTML('beforeend', html);
                index += 1;
                refreshRemoveButtonState();
            });

            container.addEventListener('click', (event) => {
                const button = event.target.closest('.remove-item-btn');
                if (!button) {
                    return;
                }

                const rows = container.querySelectorAll('.order-item-row');
                if (rows.length <= 1) {
                    return;
                }

                button.closest('.order-item-row')?.remove();
                refreshRemoveButtonState();
            });

            refreshRemoveButtonState();
        })();
    </script>
@endsection

