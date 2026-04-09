@extends('admin.layout.index')
@section('title', 'Payment Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Payment Details</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.payments.slip', $payment) }}" class="btn btn-outline-dark"><i class="fa-solid fa-print me-1"></i>Print Slip</a>
            <a href="{{ route('admin.payments.slip', ['payment' => $payment, 'pdf' => 1]) }}" class="btn btn-outline-danger"><i class="fa-solid fa-file-pdf me-1"></i>PDF Slip</a>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
        </div>
    </div>

    <div class="card mb-3"><div class="card-body">
        <h5 class="mb-3">Update Payment</h5>
        <form method="POST" action="{{ route('admin.payments.update', $payment) }}">
            @csrf @method('PUT')
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="method" class="form-control">
                        <option value="cash" @selected($payment->method->value === 'cash')>cash</option>
                        <option value="card" @selected($payment->method->value === 'card')>card</option>
                        <option value="upi" @selected($payment->method->value === 'upi')>upi</option>
                        <option value="online" @selected($payment->method->value === 'online')>online</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="gateway_code" class="form-control">
                        <option value="">gateway</option>
                        @foreach($paymentGatewayDefinitions ?? [] as $code => $gateway)
                            <option value="{{ $code }}" @selected($payment->gateway_code === $code)>{{ $gateway['label'] ?? $code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="gateway_country" class="form-control">
                        <option value="">country</option>
                        @foreach($countries ?? [] as $country)
                            <option value="{{ $country['code'] }}" @selected($payment->gateway_country === $country['code'])>{{ $country['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="pending" @selected($payment->status->value === 'pending')>pending</option>
                        <option value="paid" @selected($payment->status->value === 'paid')>paid</option>
                        <option value="failed" @selected($payment->status->value === 'failed')>failed</option>
                        <option value="refunded" @selected($payment->status->value === 'refunded')>refunded</option>
                    </select>
                </div>
                <div class="col-md-3"><input class="form-control" type="number" step="0.01" name="amount" value="{{ $payment->amount }}" required></div>
                <div class="col-md-2"><input class="form-control" name="transaction_ref" value="{{ $payment->transaction_ref }}" placeholder="Txn Ref"></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Save</button></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Order No</th><td>{{ $payment->order?->order_no }}</td></tr>
            <tr><th>Restaurant</th><td>{{ $payment->order?->restaurant?->name ?: '-' }}</td></tr>
            <tr><th>Branch</th><td>{{ $payment->order?->branch?->name ?: '-' }}</td></tr>
            <tr><th>Customer</th><td>{{ $payment->order?->customer?->name ?: '-' }}</td></tr>
            <tr><th>Method</th><td>{{ $payment->method->value }}</td></tr>
            <tr><th>Gateway</th><td>{{ $payment->gateway_code ? (\App\Support\PaymentGatewayCatalog::displayLabel($payment->gateway_code)) : '-' }}</td></tr>
            <tr><th>Gateway Country</th><td>{{ $payment->gateway_country ? \App\Support\CountryCatalog::displayName($payment->gateway_country) : '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $payment->status->value }}</td></tr>
            <tr><th>Amount</th><td>{{ $payment->amount }}</td></tr>
            <tr><th>Transaction Ref</th><td>{{ $payment->transaction_ref ?: '-' }}</td></tr>
            <tr><th>Paid At</th><td>{{ $payment->paid_at?->format('d M Y h:i A') ?: '-' }}</td></tr>
            <tr><th>Created At</th><td>{{ $payment->created_at->format('d M Y h:i A') }}</td></tr>
        </table>
    </div></div>
@endsection


