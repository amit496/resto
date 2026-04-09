@extends('admin.layout.index')
@section('title', 'Subcategories')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Subcategories</h3>
        <a href="{{ route('admin.subcategories.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Subcategory</a>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-5"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search subcategory, slug"></div>
            <div class="col-md-3">
                <select name="category_id" class="form-control js-filter-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->name }}</option>
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
            <div class="col-md-2 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.subcategories.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Category</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse($subcategories as $subcategory)
                        <tr>
                            <td>{{ $subcategory->name }}</td>
                            <td>{{ $subcategory->category?->name }}</td>
                            <td>{{ $subcategory->status->value }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.subcategories.show', $subcategory) }}"><i class="fa-solid fa-eye me-1"></i>View</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.subcategories.edit', $subcategory) }}"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.subcategories.toggle-status', $subcategory) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Subcategory Status?" data-confirm-text="This will update subcategory visibility for products." data-confirm-button="{{ $subcategory->status->value === 'active' ? 'Set Inactive' : 'Set Active' }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $subcategory->status->value === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                        {{ $subcategory->status->value === 'active' ? 'Inactive' : 'Active' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.subcategories.destroy', $subcategory) }}" class="d-inline-block js-confirm-action" data-confirm-title="Archive Subcategory?" data-confirm-text="This will set the subcategory to inactive." data-confirm-button="Archive">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-archive me-1"></i>Archive</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $subcategories->links() }}</div>
    </div>
@endsection


