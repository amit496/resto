@extends('admin.layout.index')
@section('title', 'Product Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Product Details</h3>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card mb-3"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Name</th><td>{{ $product->name }}</td></tr>
            <tr><th>Slug</th><td>{{ $product->slug }}</td></tr>
            <tr><th>SKU</th><td>{{ $product->sku ?: '-' }}</td></tr>
            <tr><th>Restaurant</th><td>{{ $product->restaurant?->name }}</td></tr>
            <tr><th>Category</th><td>{{ $product->category?->name ?: '-' }}</td></tr>
            <tr><th>Subcategory</th><td>{{ $product->subcategory?->name ?: '-' }}</td></tr>
            <tr><th>Type</th><td>{{ $product->type->value }}</td></tr>
            <tr><th>Status</th><td>{{ $product->status->value }}</td></tr>
            <tr><th>Base Price</th><td>{{ $product->base_price }}</td></tr>
            <tr><th>Description</th><td>{{ $product->description ?: '-' }}</td></tr>
        </table>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5 class="mb-3">Images</h5>
        <div class="row g-2">
            @forelse(($product->images ?? []) as $image)
                <div class="col-md-2 col-6">
                    <img src="{{ \App\Support\ImagePath::thumbUrl($image, \App\Support\FoodImageResolver::product($product->name, $loop->iteration)) }}" style="width:100%;height:90px;object-fit:cover;border-radius:8px;">
                </div>
            @empty
                <p class="text-muted mb-0">No images.</p>
            @endforelse
        </div>
    </div></div>

    <div class="card"><div class="card-body">
        <h5 class="mb-3">Variants</h5>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Name</th><th>Value</th><th>Price</th><th>SKU</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($product->variants as $variant)
                        <tr>
                            <td>{{ $variant->name }}</td>
                            <td>{{ $variant->value }}</td>
                            <td>{{ $variant->price }}</td>
                            <td>{{ $variant->sku }}</td>
                            <td>{{ $variant->status->value }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-3">No variants.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>
@endsection


