@extends('admin.layout.index')
@section('title', 'Order Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Order Details</h3>
        <div class="d-flex gap-2">
            @if($order->bill_no)
                <a href="{{ route('admin.orders.bill', $order) }}" class="btn btn-outline-primary"><i class="fa-solid fa-receipt me-1"></i>Print Bill</a>
            @endif
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
        </div>
    </div>

    <div class="card mb-3"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Order No</th><td>{{ $order->order_no }}</td></tr>
            <tr><th>Restaurant</th><td>{{ $order->restaurant?->name }}</td></tr>
            <tr><th>Branch</th><td>{{ $order->branch?->name ?: '-' }}</td></tr>
            <tr><th>Customer</th><td>{{ $order->customer?->name ?: '-' }}</td></tr>
            <tr><th>Billing Country</th><td>{{ $order->billing_country ? \App\Support\CountryCatalog::displayName($order->billing_country) : '-' }}</td></tr>
            <tr><th>Delivery Boy</th><td>{{ $order->deliveryBoy?->name ?: '-' }}</td></tr>
            <tr><th>Order Type</th><td>{{ $order->order_type->value }}</td></tr>
            <tr><th>Order Source</th><td>{{ $order->order_source ?? '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $order->status->value }}</td></tr>
            <tr><th>Created By</th><td>{{ $order->createdBy?->name ?: '-' }}</td></tr>
            <tr><th>Bill No</th><td>{{ $order->bill_no ?: '-' }}</td></tr>
            <tr><th>Bill Status</th><td>{{ $order->bill_status ?? '-' }}</td></tr>
            <tr><th>Billed At</th><td>{{ $order->billed_at?->format('d M Y h:i A') ?: '-' }}</td></tr>
            <tr><th>Billed By</th><td>{{ $order->billedBy?->name ?: '-' }}</td></tr>
            <tr><th>Subtotal</th><td>{{ $order->subtotal }}</td></tr>
            <tr><th>Tax</th><td>{{ $order->tax_amount }}</td></tr>
            <tr><th>Discount</th><td>{{ $order->discount_amount }}</td></tr>
            <tr><th>Total</th><td>{{ $order->total_amount }}</td></tr>
            <tr><th>Notes</th><td>{{ $order->notes ?: '-' }}</td></tr>
        </table>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5 class="mb-3">Order Items</h5>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Product</th><th>Variant</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name_snapshot ?: $item->product?->name }}</td>
                            <td>{{ $item->variant?->name ? $item->variant->name.' - '.$item->variant->value : '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_price }}</td>
                            <td>{{ $item->line_total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-3">No items.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <h5 class="mb-3">Payments</h5>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Method</th><th>Gateway</th><th>Status</th><th>Amount</th><th>Reference</th><th>Paid At</th><th class="text-end">Slip</th></tr></thead>
                <tbody>
                    @forelse($order->payments as $payment)
                        <tr>
                            <td>{{ $payment->method->value }}</td>
                            <td>{{ $payment->gateway_code ? \App\Support\PaymentGatewayCatalog::displayLabel($payment->gateway_code) : '-' }}</td>
                            <td>{{ $payment->status->value }}</td>
                            <td>{{ $payment->amount }}</td>
                            <td>{{ $payment->transaction_ref ?: '-' }}</td>
                            <td>{{ $payment->paid_at?->format('d M Y h:i A') ?: '-' }}</td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-dark" href="{{ route('admin.payments.slip', $payment) }}"><i class="fa-solid fa-print me-1"></i>Print</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-3">No payments.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>
@endsection


