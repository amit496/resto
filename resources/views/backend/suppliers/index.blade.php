@extends('admin.layout.index')
@section('title', 'Suppliers')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Suppliers</h3></div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-2">
                    <select class="form-control" name="restaurant_id">
                        <option value="">Restaurant</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" name="name" placeholder="Supplier Name" required></div>
                <div class="col-md-2"><input class="form-control" name="contact_person" placeholder="Contact Person"></div>
                <div class="col-md-2"><input class="form-control" name="phone" placeholder="Phone"></div>
                <div class="col-md-2"><input class="form-control" name="email" type="email" placeholder="Email"></div>
                <div class="col-md-1">
                    <select class="form-control" name="status">
                        <option value="active">active</option>
                        <option value="inactive">inactive</option>
                    </select>
                </div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div>
                <div class="col-md-12"><textarea class="form-control" name="address" rows="2" placeholder="Address"></textarea></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Contact</th><th>Email</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->phone ?: '-' }}</td>
                    <td>{{ $supplier->email ?: '-' }}</td>
                    <td>{{ $supplier->status }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.suppliers.toggle-status', $supplier) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Supplier Status?" data-confirm-text="This will update supplier availability in purchase operations." data-confirm-button="{{ $supplier->status === 'active' ? 'Set Inactive' : 'Set Active' }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $supplier->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                {{ $supplier->status === 'active' ? 'Inactive' : 'Active' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No suppliers found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $suppliers->links() }}</div></div>
@endsection

