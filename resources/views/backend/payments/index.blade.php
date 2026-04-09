@extends('admin.layout.index')
@section('title', 'Payments')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Payments</h3></div>
    <div class="card mb-3"><div class="card-body">
        <h5 class="mb-3">Add Payment (Online / Dine-in / Takeaway)</h5>
        <form method="POST" action="{{ route('admin.payments.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-4">
                    <select class="form-control" name="food_order_id" required>
                        <option value="">Select Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}">{{ $order->order_no }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="method" class="form-control">
                        <option value="cash">cash</option>
                        <option value="card">card</option>
                        <option value="upi">upi</option>
                        <option value="online">online</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="gateway_code" class="form-control">
                        <option value="">gateway</option>
                        @foreach($paymentGatewayDefinitions as $code => $gateway)
                            <option value="{{ $code }}">{{ $gateway['label'] ?? $code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="gateway_country" class="form-control">
                        <option value="">country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control">
                        <option value="pending">pending</option>
                        <option value="paid">paid</option>
                        <option value="failed">failed</option>
                        <option value="refunded">refunded</option>
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" name="amount" type="number" step="0.01" placeholder="Amount" required></div>
                <div class="col-md-2"><input class="form-control" name="transaction_ref" placeholder="Txn Ref"></div>
            </div>
            <div class="mt-2"><button class="btn btn-primary">Add Payment</button></div>
        </form>
    </div></div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-5"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search order no, transaction ref"></div>
            <div class="col-md-2">
                <select name="method" class="form-control js-filter-select">
                    <option value="">All Methods</option>
                    <option value="cash" @selected(request('method')==='cash')>cash</option>
                    <option value="card" @selected(request('method')==='card')>card</option>
                    <option value="upi" @selected(request('method')==='upi')>upi</option>
                    <option value="online" @selected(request('method')==='online')>online</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="gateway" class="form-control js-filter-select">
                    <option value="">All Gateways</option>
                    @foreach($paymentGatewayDefinitions as $code => $gateway)
                        <option value="{{ $code }}" @selected(request('gateway') === $code)>{{ $gateway['label'] ?? $code }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control js-filter-select">
                    <option value="">All Status</option>
                    <option value="pending" @selected(request('status')==='pending')>pending</option>
                    <option value="paid" @selected(request('status')==='paid')>paid</option>
                    <option value="failed" @selected(request('status')==='failed')>failed</option>
                    <option value="refunded" @selected(request('status')==='refunded')>refunded</option>
                </select>
            </div>
            <div class="col-md-3 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.payments.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>
    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order</th><th>Branch</th><th>Method</th><th>Gateway</th><th>Country</th><th>Status</th><th>Amount</th><th>Txn Ref</th><th>Date</th><th class="text-end">Action</th></tr></thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->order?->order_no }}</td>
                        <td>{{ $payment->order?->branch?->name ?: '-' }}</td>
                        <td>{{ $payment->method->value }}</td>
                        <td>{{ $payment->gateway_code ? ($paymentGatewayDefinitions[$payment->gateway_code]['label'] ?? $payment->gateway_code) : '-' }}</td>
                        <td>{{ $payment->gateway_country ? \App\Support\CountryCatalog::displayName($payment->gateway_country) : '-' }}</td>
                        <td>{{ $payment->status->value }}</td>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->transaction_ref }}</td>
                        <td>{{ $payment->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye me-1"></i>View</a>
                            <a href="{{ route('admin.payments.slip', $payment) }}" class="btn btn-sm btn-outline-dark"><i class="fa-solid fa-print me-1"></i>Slip</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center py-4">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $payments->links() }}</div></div>
@endsection


