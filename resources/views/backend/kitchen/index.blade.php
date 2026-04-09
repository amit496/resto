@extends('admin.layout.index')
@section('title', 'Kitchen / KOT')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Kitchen / KOT Board</h3>
    </div>

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">All Active</option>
                    <option value="pending" @selected($status === 'pending')>pending</option>
                    <option value="preparing" @selected($status === 'preparing')>preparing</option>
                    <option value="ready" @selected($status === 'ready')>ready</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Filter</button></div>
        </div>
    </form>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order</th><th>Restaurant</th><th>Customer</th><th>Items</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_no }}</td>
                    <td>{{ $order->restaurant?->name }}</td>
                    <td>{{ $order->customer?->name ?: 'Walk-in' }}</td>
                    <td>{{ $order->items->sum('quantity') }}</td>
                    <td>{{ $order->status->value }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.kitchen.orders.status', $order) }}" class="d-inline-flex gap-2">
                            @csrf @method('PATCH')
                            <select class="form-control form-control-sm" name="status">
                                <option value="pending">pending</option>
                                <option value="preparing">preparing</option>
                                <option value="ready">ready</option>
                                <option value="delivered">delivered</option>
                                <option value="cancelled">cancelled</option>
                            </select>
                            <button class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4">No kitchen orders found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $orders->links() }}</div>
    </div>
@endsection


