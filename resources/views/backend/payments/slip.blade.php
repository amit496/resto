<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payment Slip</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; margin: 18px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .title { font-size: 22px; font-weight: 700; }
        .muted { color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 13px; }
        th { background: #f5f5f5; width: 190px; }
        .item-table th { width: auto; }
        .actions { margin-bottom: 12px; }
        .btn { padding: 7px 12px; border: 1px solid #ccc; text-decoration: none; color: #111; border-radius: 5px; font-size: 12px; }
        @media print {
            .actions { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button class="btn" onclick="window.print()">Print Slip</button>
        <a class="btn" href="{{ route('admin.payments.show', $payment) }}">Back</a>
    </div>
    <div class="header">
        <div>
            <div class="title">Payment Slip</div>
            <div class="muted">Slip ID: PAY-{{ $payment->id }}</div>
        </div>
        <div class="muted">{{ now()->format('d M Y h:i A') }}</div>
    </div>

    <table>
        <tr><th>Order No</th><td>{{ $payment->order?->order_no }}</td></tr>
        <tr><th>Restaurant</th><td>{{ $payment->order?->restaurant?->name ?: '-' }}</td></tr>
        <tr><th>Branch</th><td>{{ $payment->order?->branch?->name ?: '-' }}</td></tr>
        <tr><th>Customer</th><td>{{ $payment->order?->customer?->name ?: 'Walk-in' }}</td></tr>
        <tr><th>Order Type</th><td>{{ $payment->order?->order_type?->value ?: '-' }}</td></tr>
        <tr><th>Payment Method</th><td>{{ $payment->method->value }}</td></tr>
        <tr><th>Gateway</th><td>{{ $payment->gateway_code ? \App\Support\PaymentGatewayCatalog::displayLabel($payment->gateway_code) : '-' }}</td></tr>
        <tr><th>Gateway Country</th><td>{{ $payment->gateway_country ? \App\Support\CountryCatalog::displayName($payment->gateway_country) : '-' }}</td></tr>
        <tr><th>Payment Status</th><td>{{ $payment->status->value }}</td></tr>
        <tr><th>Amount</th><td>{{ number_format((float) $payment->amount, 2) }}</td></tr>
        <tr><th>Transaction Ref</th><td>{{ $payment->transaction_ref ?: '-' }}</td></tr>
        <tr><th>Paid At</th><td>{{ $payment->paid_at?->format('d M Y h:i A') ?: '-' }}</td></tr>
    </table>

    <table class="item-table">
        <thead><tr><th>Item</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
        <tbody>
            @forelse($payment->order?->items ?? [] as $item)
                <tr>
                    <td>{{ $item->product_name_snapshot ?: $item->product?->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td>{{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;">No order items.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

