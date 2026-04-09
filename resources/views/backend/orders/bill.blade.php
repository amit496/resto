<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bill {{ $order->bill_no ?: $order->order_no }}</title>
    <style>
        *{box-sizing:border-box}
        body{font-family:Arial, sans-serif;color:#111;margin:0;padding:24px;background:#f5f5f5}
        .bill{max-width:820px;margin:0 auto;background:#fff;border:1px solid #e5e5e5;border-radius:12px;padding:24px}
        .bill-header{display:flex;justify-content:space-between;gap:20px;align-items:flex-start;margin-bottom:20px}
        .brand h1{margin:0;font-size:24px}
        .brand p{margin:4px 0;color:#555;font-size:13px}
        .meta{font-size:13px;color:#555}
        table{width:100%;border-collapse:collapse}
        th,td{padding:10px;border-bottom:1px solid #e8e8e8;text-align:left;font-size:13px}
        th{background:#fafafa}
        .totals{margin-top:16px;display:flex;justify-content:flex-end}
        .totals table{width:320px}
        .totals td{border:none;padding:6px 10px}
        .totals tr:last-child td{font-weight:700;font-size:14px}
        .footer{margin-top:18px;font-size:12px;color:#777;text-align:center}
        @media print{body{background:#fff;padding:0}.bill{border:none;border-radius:0;box-shadow:none}}
    </style>
</head>
<body>
    <div class="bill">
        <div class="bill-header">
            <div class="brand">
                <h1>{{ $order->restaurant?->name ?? 'FoodiHub' }}</h1>
                <p>{{ $order->branch?->address ?? $order->restaurant?->address }}</p>
                <p>Branch: {{ $order->branch?->name ?? '-' }}</p>
            </div>
            <div class="meta">
                <div><strong>Bill No:</strong> {{ $order->bill_no ?: '-' }}</div>
                <div><strong>Order No:</strong> {{ $order->order_no }}</div>
                <div><strong>Date:</strong> {{ $order->billed_at?->format('d M Y, h:i A') ?: now()->format('d M Y, h:i A') }}</div>
                <div><strong>Created By:</strong> {{ $order->createdBy?->name ?: '-' }}</div>
                <div><strong>Billed By:</strong> {{ $order->billedBy?->name ?: '-' }}</div>
                <div><strong>Customer:</strong> {{ $order->customer?->name ?: 'Walk-in' }}</div>
            </div>
        </div>

        @php
            $subtotal = (float) $order->subtotal;
            $tax = (float) $order->tax_amount;
        @endphp

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>GST</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @php
                        $line = (float) $item->line_total;
                        $itemTax = $subtotal > 0 ? round($tax * ($line / $subtotal), 2) : 0;
                    @endphp
                    <tr>
                        <td>{{ $item->product_name_snapshot ?: $item->product?->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                        <td>₹{{ number_format($itemTax, 2) }}</td>
                        <td>₹{{ number_format($line + $itemTax, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr><td>Subtotal</td><td style="text-align:right;">₹{{ number_format($order->subtotal, 2) }}</td></tr>
                <tr><td>GST / Tax</td><td style="text-align:right;">₹{{ number_format($order->tax_amount, 2) }}</td></tr>
                <tr><td>Service Charge</td><td style="text-align:right;">₹{{ number_format($order->service_charge ?? 0, 2) }}</td></tr>
                <tr><td>Delivery Fee</td><td style="text-align:right;">₹{{ number_format($order->delivery_fee ?? 0, 2) }}</td></tr>
                <tr><td>Packing Fee</td><td style="text-align:right;">₹{{ number_format($order->packing_fee ?? 0, 2) }}</td></tr>
                <tr><td>Discount</td><td style="text-align:right;">-₹{{ number_format($order->discount_amount ?? 0, 2) }}</td></tr>
                <tr><td>Loyalty Discount</td><td style="text-align:right;">-₹{{ number_format($order->loyalty_discount_amount ?? 0, 2) }}</td></tr>
                <tr><td>Total</td><td style="text-align:right;">₹{{ number_format($order->total_amount, 2) }}</td></tr>
            </table>
        </div>

        <div class="footer">Thank you for dining with {{ $order->restaurant?->name ?? 'FoodiHub' }}.</div>
    </div>
</body>
</html>
