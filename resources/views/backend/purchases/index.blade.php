@extends('admin.layout.index')
@section('title', 'Purchase Orders')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Suppliers & Purchase</h3></div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.purchases.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-2">
                    <select class="form-control" name="supplier_id">
                        <option value="">Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <select class="form-control" name="status">
                        <option value="pending">pending</option>
                        <option value="ordered">ordered</option>
                        <option value="received">received</option>
                        <option value="cancelled">cancelled</option>
                    </select>
                </div>
                <div class="col-md-2"><input type="date" class="form-control" name="order_date" value="{{ now()->toDateString() }}" required></div>
                <div class="col-md-2"><input type="date" class="form-control" name="expected_date"></div>
                <div class="col-md-2"><input class="form-control" name="item_name" placeholder="Item Name" required></div>
                <div class="col-md-1"><input class="form-control" name="quantity" type="number" step="0.01" placeholder="Qty" required></div>
                <div class="col-md-1"><input class="form-control" name="unit_cost" type="number" step="0.01" placeholder="Cost" required></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div>
                <div class="col-md-12"><textarea class="form-control" rows="2" name="notes" placeholder="Notes"></textarea></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>PO</th><th>Supplier</th><th>Status</th><th>Date</th><th>Total</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->po_number }}</td>
                    <td>{{ $purchase->supplier?->name ?: '-' }}</td>
                    <td>{{ $purchase->status }}</td>
                    <td>{{ optional($purchase->order_date)->format('d M Y') }}</td>
                    <td>{{ $purchase->total_amount }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.purchases.status', $purchase) }}" class="d-inline-flex gap-2">
                            @csrf @method('PATCH')
                            <select class="form-control form-control-sm" name="status">
                                <option value="pending">pending</option>
                                <option value="ordered">ordered</option>
                                <option value="received">received</option>
                                <option value="cancelled">cancelled</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary">Update</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4">No purchase orders found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $purchases->links() }}</div></div>
@endsection


