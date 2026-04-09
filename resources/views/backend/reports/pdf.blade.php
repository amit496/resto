<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Report Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 20px; margin-bottom: 6px; }
        .muted { color: #666; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .grid td { width: 25%; }
    </style>
</head>
<body>
    <h1>Food Ordering Report</h1>
    <div class="muted">
        Date: {{ $from->toDateString() }} to {{ $to->toDateString() }}
        @if($branchId) | Branch Filter Applied @endif
    </div>

    <table class="grid">
        <tr>
            <td><strong>Orders:</strong> {{ $ordersCount }}</td>
            <td><strong>Delivered:</strong> {{ $deliveredOrdersCount }}</td>
            <td><strong>Cancelled:</strong> {{ $cancelledOrdersCount }}</td>
            <td><strong>Revenue:</strong> {{ number_format((float) $revenue, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Avg Order Value:</strong> {{ number_format((float) $avgOrderValue, 2) }}</td>
            <td><strong>Order Success %:</strong> {{ number_format((float) $orderSuccessRate, 2) }}%</td>
            <td><strong>Cancellation %:</strong> {{ number_format((float) $cancellationRate, 2) }}%</td>
            <td><strong>Payments:</strong> {{ $paymentsCount }}</td>
        </tr>
    </table>

    <h3>Orders By Status</h3>
    <table>
        <thead><tr><th>Status</th><th>Orders</th></tr></thead>
        <tbody>
            @forelse($ordersByStatus as $row)
                @php $statusValue = $row->status instanceof \BackedEnum ? $row->status->value : $row->status; @endphp
                <tr><td>{{ ucfirst((string) $statusValue) }}</td><td>{{ $row->total }}</td></tr>
            @empty
                <tr><td colspan="2">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Payments By Method</h3>
    <table>
        <thead><tr><th>Method</th><th>Transactions</th><th>Paid Amount</th></tr></thead>
        <tbody>
            @forelse($paymentsByMethod as $row)
                @php $methodValue = $row->method instanceof \BackedEnum ? $row->method->value : $row->method; @endphp
                <tr><td>{{ strtoupper((string) $methodValue) }}</td><td>{{ $row->total_transactions }}</td><td>{{ number_format((float) $row->paid_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="3">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Top Selling Items</h3>
    <table>
        <thead><tr><th>Item</th><th>Qty</th><th>Sales</th></tr></thead>
        <tbody>
            @forelse($topProducts as $row)
                <tr><td>{{ $row->name }}</td><td>{{ $row->total_qty }}</td><td>{{ number_format((float) $row->total_sales, 2) }}</td></tr>
            @empty
                <tr><td colspan="3">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Top Selling Restaurants</h3>
    <table>
        <thead><tr><th>Restaurant</th><th>Sales</th></tr></thead>
        <tbody>
            @forelse($topRestaurants as $row)
                <tr><td>{{ $row->name }}</td><td>{{ number_format((float) $row->total_sales, 2) }}</td></tr>
            @empty
                <tr><td colspan="2">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Top Selling Branches</h3>
    <table>
        <thead><tr><th>Branch</th><th>Sales</th></tr></thead>
        <tbody>
            @forelse($topBranches as $row)
                <tr><td>{{ $row->name }}</td><td>{{ number_format((float) $row->total_sales, 2) }}</td></tr>
            @empty
                <tr><td colspan="2">No data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

