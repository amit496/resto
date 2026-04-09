@extends('admin.layout.index')
@section('title', 'Loyalty Program')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Loyalty Program</h3></div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.loyalty.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-control" name="customer_id" required>
                        <option value="">Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="food_order_id">
                        <option value="">Order (Optional)</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}">{{ $order->order_no }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="type">
                        <option value="credit">credit</option>
                        <option value="debit">debit</option>
                        <option value="adjustment">adjustment</option>
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" name="points" type="number" min="1" placeholder="Points" required></div>
                <div class="col-md-2"><input class="form-control" name="note" placeholder="Note"></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div>
            </div>
        </form>
    </div></div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card"><div class="card-body">
                <h5>Customer Balances</h5>
                <div class="d-flex flex-wrap gap-3">
                    @forelse($balances as $balance)
                        <span class="badge bg-light text-dark">{{ $balance->customer?->name }}: {{ (int) $balance->balance }} pts</span>
                    @empty
                        <span class="text-muted">No balance data available.</span>
                    @endforelse
                </div>
            </div></div>
        </div>
    </div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Customer</th><th>Order</th><th>Type</th><th>Points</th><th>Note</th></tr></thead>
            <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->customer?->name }}</td>
                    <td>{{ $transaction->order?->order_no ?: '-' }}</td>
                    <td>{{ $transaction->type }}</td>
                    <td>{{ $transaction->points }}</td>
                    <td>{{ $transaction->note ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No loyalty transactions found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $transactions->links() }}</div></div>
@endsection


