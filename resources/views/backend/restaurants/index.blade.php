@extends('admin.layout.index')
@section('title', 'Restaurants')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Restaurants</h3>
        @if($canCreateRestaurant)
            <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Restaurant</a>
        @elseif($singleRestaurantMode && $primaryRestaurant)
            <a href="{{ route('admin.restaurants.edit', $primaryRestaurant) }}" class="btn btn-primary"><i class="fa-solid fa-edit me-1"></i>Manage Restaurant</a>
        @endif
    </div>
    @if($singleRestaurantMode)
        <div class="alert alert-info">Single restaurant mode is enabled. Use <strong>Branches</strong> for multi-location operations.</div>
    @endif
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-6"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, slug, email, phone"></div>
            <div class="col-md-3">
                <select name="status" class="form-control js-filter-select">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')==='active')>active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>inactive</option>
                </select>
            </div>
            <div class="col-md-3 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.restaurants.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Phone</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($restaurants as $restaurant)
                        <tr>
                            <td>{{ $restaurant->name }}</td>
                            <td>{{ $restaurant->status->value }}</td>
                            <td>{{ $restaurant->phone }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye me-1"></i>View</a>
                                <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                                @if(! $singleRestaurantMode)
                                    <form method="POST" action="{{ route('admin.restaurants.destroy', $restaurant) }}" class="d-inline-block js-confirm-action" data-confirm-title="Archive Restaurant?" data-confirm-text="This will set the restaurant to inactive." data-confirm-button="Archive">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-archive me-1"></i>Archive</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $restaurants->links() }}</div>
    </div>
@endsection


