@extends('admin.layout.index')
@section('title', 'Coupons')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Coupons</h3>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Coupon</a>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search code"></div>
            <div class="col-md-3">
                <select name="restaurant_id" class="form-control js-filter-select">
                    <option value="">All Restaurants</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}" @selected((int) request('restaurant_id') === $restaurant->id)>{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-control js-filter-select">
                    <option value="">All Types</option>
                    <option value="percent" @selected(request('type')==='percent')>percent</option>
                    <option value="fixed" @selected(request('type')==='fixed')>fixed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control js-filter-select">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')==='active')>active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>inactive</option>
                </select>
            </div>
            <div class="col-md-2 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.coupons.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>
    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->code }}</td>
                        <td>{{ $coupon->type->value }}</td>
                        <td>{{ $coupon->value }}</td>
                        <td>{{ $coupon->status->value }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye me-1"></i>View</a>
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                            <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Coupon Status?" data-confirm-text="This will enable or disable coupon usage for customers." data-confirm-button="{{ $coupon->status->value === 'active' ? 'Set Inactive' : 'Set Active' }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $coupon->status->value === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    {{ $coupon->status->value === 'active' ? 'Inactive' : 'Active' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="d-inline-block js-confirm-action" data-confirm-title="Archive Coupon?" data-confirm-text="This will set the coupon to inactive." data-confirm-button="Archive">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-archive me-1"></i>Archive</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $coupons->links() }}</div></div>
@endsection


