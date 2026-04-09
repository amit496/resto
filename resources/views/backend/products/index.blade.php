@extends('admin.layout.index')
@section('title', 'Products')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Food Products</h3>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Product</a>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, SKU, slug"></div>
            <div class="col-md-3">
                <select name="restaurant_id" class="form-control js-filter-select">
                    <option value="">All Restaurants</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}" @selected((int) request('restaurant_id') === $restaurant->id)>{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-control js-filter-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-control js-filter-select">
                    <option value="">All Types</option>
                    <option value="veg" @selected(request('type')==='veg')>veg</option>
                    <option value="non_veg" @selected(request('type')==='non_veg')>non_veg</option>
                    <option value="egg" @selected(request('type')==='egg')>egg</option>
                    <option value="beverage" @selected(request('type')==='beverage')>beverage</option>
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
                <a class="btn btn-light w-100" href="{{ route('admin.products.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Type</th><th>Status</th><th>Price</th><th>Variants</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @php
                                    $firstImage = is_array($product->images) && ! empty($product->images) ? $product->images[0] : null;
                                    $firstImageUrl = \App\Support\ImagePath::thumbUrl($firstImage, \App\Support\FoodImageResolver::product($product->name, 1));
                                @endphp
                                <img src="{{ $firstImageUrl }}" alt="{{ $product->name }}" style="width:42px;height:42px;object-fit:cover;border-radius:8px;">
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category?->name }} / {{ $product->subcategory?->name }}</td>
                            <td>{{ $product->type->value }}</td>
                            <td>{{ $product->status->value }}</td>
                            <td>{{ $product->base_price }}</td>
                            <td>{{ $product->variants->count() }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.products.show', $product) }}"><i class="fa-solid fa-eye me-1"></i>View</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.products.edit', ['product' => $product, 'page' => $products->currentPage()]) }}"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Product Status?" data-confirm-text="This will control if the product is available for ordering." data-confirm-button="{{ $product->status->value === 'active' ? 'Set Inactive' : 'Set Active' }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $product->status->value === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                        <i class="fa-solid {{ $product->status->value === 'active' ? 'fa-toggle-off' : 'fa-toggle-on' }} me-1"></i>
                                        {{ $product->status->value === 'active' ? 'Inactive' : 'Active' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="d-inline-block js-confirm-action" data-confirm-title="Archive Product?" data-confirm-text="This will set the product to inactive." data-confirm-button="Archive">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-archive me-1"></i>Archive</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $products->links() }}</div>
    </div>
@endsection


