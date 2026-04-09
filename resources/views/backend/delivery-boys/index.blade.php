@extends('admin.layout.index')
@section('title', 'Delivery Boys')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Delivery Boys</h3>
        <a href="{{ route('admin.delivery-boys.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Delivery Boy</a>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, phone, vehicle"></div>
            <div class="col-md-2">
                <select name="restaurant_id" class="form-control js-filter-select">
                    <option value="">All Restaurants</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}" @selected((int) request('restaurant_id') === $restaurant->id)>{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control js-filter-select">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')==='active')>active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="has_active_orders" class="form-control js-filter-select">
                    <option value="">All Assignments</option>
                    <option value="yes" @selected(request('has_active_orders')==='yes')>Assigned</option>
                    <option value="no" @selected(request('has_active_orders')==='no')>Unassigned</option>
                </select>
            </div>
            <div class="col-md-3 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.delivery-boys.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>
    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Image</th><th>Name</th><th>Phone</th><th>Vehicle</th><th>Status</th><th>Active</th><th>Delivered</th><th>Collection</th><th class="text-end">Action</th></tr></thead>
            <tbody>
                @forelse($deliveryBoys as $boy)
                    <tr>
                        <td>
                            @php $imageUrl = \App\Support\ImagePath::thumbUrl($boy->image, 'admin/assets/img/avatar/avatar-1.jpg'); @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $boy->name }}" style="width:42px;height:42px;object-fit:cover;border-radius:8px;">
                        </td>
                        <td>{{ $boy->name }}</td>
                        <td>{{ $boy->phone }}</td>
                        <td>{{ $boy->vehicle_no }}</td>
                        <td>{{ $boy->status->value }}</td>
                        <td>{{ $boy->active_orders_count }}</td>
                        <td>{{ $boy->delivered_orders_count }}</td>
                        <td>{{ number_format((float) $boy->collected_amount, 2) }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.delivery-boys.show', $boy) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye me-1"></i>View</a>
                            <a href="{{ route('admin.delivery-boys.edit', ['delivery_boy' => $boy, 'page' => $deliveryBoys->currentPage()]) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                            <form method="POST" action="{{ route('admin.delivery-boys.toggle-status', $boy) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Delivery Boy Status?" data-confirm-text="This will update assignment availability for this delivery boy." data-confirm-button="{{ $boy->status->value === 'active' ? 'Set Inactive' : 'Set Active' }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $boy->status->value === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    {{ $boy->status->value === 'active' ? 'Inactive' : 'Active' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.delivery-boys.destroy', $boy) }}" class="d-inline-block js-confirm-action" data-confirm-title="Archive Delivery Boy?" data-confirm-text="This will set the delivery boy to inactive." data-confirm-button="Archive">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-archive me-1"></i>Archive</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-4">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $deliveryBoys->links() }}</div></div>
@endsection


