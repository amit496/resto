@extends('admin.layout.index')
@section('title', 'Categories')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Categories</h3>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Category</a>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search category, slug"></div>
            <div class="col-md-3">
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
            <div class="col-md-3 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.categories.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Image</th><th>Name</th><th>Restaurant</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            @php $imageUrl = \App\Support\ImagePath::thumbUrl($category->image, \App\Support\FoodImageResolver::category($category->name)); @endphp
                            <img src="{{ $imageUrl }}" alt="{{ $category->name }}" style="width:42px;height:42px;object-fit:cover;border-radius:8px;">
                        </td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->restaurant?->name }}</td>
                        <td>{{ $category->status->value }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-info" href="{{ route('admin.categories.show', $category) }}"><i class="fa-solid fa-eye me-1"></i>View</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.categories.edit', ['category' => $category, 'page' => $categories->currentPage()]) }}"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                            <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Category Status?" data-confirm-text="This will update category visibility for products." data-confirm-button="{{ $category->status->value === 'active' ? 'Set Inactive' : 'Set Active' }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $category->status->value === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    <i class="fa-solid {{ $category->status->value === 'active' ? 'fa-toggle-off' : 'fa-toggle-on' }} me-1"></i>
                                    {{ $category->status->value === 'active' ? 'Inactive' : 'Active' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline-block js-confirm-action" data-confirm-title="Archive Category?" data-confirm-text="This will set the category to inactive." data-confirm-button="Archive">
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
        </div>
        <div class="card-footer">{{ $categories->links() }}</div>
    </div>
@endsection


