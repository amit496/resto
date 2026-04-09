@extends('admin.layout.index')
@section('title', 'Refund Center')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Refund Center</h3></div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.refunds.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-5">
                    <select class="form-control" name="payment_id" required>
                        <option value="">Select Paid Transaction</option>
                        @foreach($payments as $payment)
                            <option value="{{ $payment->id }}">{{ $payment->transaction_ref ?: 'PAY-'.$payment->id }} ({{ $payment->amount }}) / {{ $payment->order?->order_no }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" name="amount" type="number" step="0.01" placeholder="Refund Amount" required></div>
                <div class="col-md-4"><input class="form-control" name="reason" placeholder="Reason"></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Create</button></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order</th><th>Payment</th><th>Amount</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($refunds as $refund)
                <tr>
                    <td>{{ $refund->order?->order_no }}</td>
                    <td>{{ $refund->payment?->transaction_ref ?: 'PAY-'.$refund->payment_id }}</td>
                    <td>{{ $refund->amount }}</td>
                    <td>{{ $refund->status }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.refunds.status', $refund) }}" class="d-inline-flex gap-2">
                            @csrf @method('PATCH')
                            <select name="status" class="form-control form-control-sm">
                                <option value="pending">pending</option>
                                <option value="approved">approved</option>
                                <option value="rejected">rejected</option>
                                <option value="processed">processed</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary">Update</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No refunds found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $refunds->links() }}</div></div>
@endsection


