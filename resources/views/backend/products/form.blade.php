@extends('admin.layout.index')
@section('title', $product->exists ? 'Edit Product' : 'Add Product')
@section('content')
    <div class="mb-4"><h3 class="mb-0">{{ $product->exists ? 'Edit Product' : 'Add Product' }}</h3></div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
            @csrf
            @if($product->exists) @method('PUT') @endif
            <input type="hidden" name="redirect_page" value="{{ old('redirect_page', request('page', 1)) }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Restaurant</label>
                    <select class="form-control" name="restaurant_id" required>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" @selected(old('restaurant_id', $product->restaurant_id) == $restaurant->id)>{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-control" name="category_id">
                        <option value="">Select</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Subcategory</label>
                    <select class="form-control" name="subcategory_id">
                        <option value="">Select</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" @selected(old('subcategory_id', $product->subcategory_id) == $subcategory->id)>{{ $subcategory->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-control" name="type">
                        @foreach($typeOptions as $option)
                            <option value="{{ $option->value }}" @selected(old('type', $product->type?->value ?? 'veg') === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $product->name) }}" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $product->slug) }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Base Price</label><input class="form-control" type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price ?? 0) }}" required></div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        @foreach($statusOptions as $option)
                            <option value="{{ $option->value }}" @selected(old('status', $product->status?->value ?? 'active') === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description">{{ old('description', $product->description) }}</textarea></div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Food Item Images</label>
                    <input class="form-control" type="file" name="product_images[]" accept="image/*" multiple data-preview-multiple="#new-product-images-preview">
                    <small class="text-muted">Multiple images allowed.</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label d-block">New Selected Images Preview</label>
                    <div id="new-product-images-preview" class="row g-2"></div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label d-block">Image Review Section</label>
                    @php $existingImages = old('existing_images', $product->images ?? []); @endphp
                    @if(!empty($existingImages))
                        <div class="row g-2">
                            @foreach($existingImages as $index => $existingImage)
                                @php
                                    $imageUrl = \App\Support\ImagePath::thumbUrl($existingImage, \App\Support\FoodImageResolver::product(old('name', $product->name), $index + 1));
                                @endphp
                                <div class="col-md-2 col-6">
                                    <div class="border rounded p-2">
                                        <img src="{{ $imageUrl }}" alt="Product Image {{ $index + 1 }}" style="width:100%;height:90px;object-fit:cover;border-radius:6px;">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_images[]" value="{{ $existingImage }}" id="remove-image-{{ $index }}">
                                            <label class="form-check-label small" for="remove-image-{{ $index }}">Remove</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No existing images.</p>
                    @endif
                </div>
            </div>

            <h5 class="mt-3">Variants (Pizza size / crust etc.)</h5>
            <div id="variant-list">
                @php $variants = old('variants', $product->variants->toArray() ?? []); @endphp
                @forelse($variants as $i => $variant)
                    <div class="row border rounded p-2 mb-2">
                        <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant['id'] ?? '' }}">
                        <div class="col-md-3"><input class="form-control" name="variants[{{ $i }}][name]" placeholder="Size" value="{{ $variant['name'] ?? '' }}"></div>
                        <div class="col-md-3"><input class="form-control" name="variants[{{ $i }}][value]" placeholder="Large" value="{{ $variant['value'] ?? '' }}"></div>
                        <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="variants[{{ $i }}][price]" placeholder="Price" value="{{ $variant['price'] ?? '' }}"></div>
                        <div class="col-md-2"><input class="form-control" name="variants[{{ $i }}][sku]" placeholder="SKU" value="{{ $variant['sku'] ?? '' }}"></div>
                        <div class="col-md-2">
                            <select class="form-control" name="variants[{{ $i }}][status]">
                                <option value="active">active</option>
                                <option value="inactive" @selected(($variant['status'] ?? '') === 'inactive')>inactive</option>
                            </select>
                        </div>
                    </div>
                @empty
                    <div class="row border rounded p-2 mb-2">
                        <div class="col-md-3"><input class="form-control" name="variants[0][name]" placeholder="Size"></div>
                        <div class="col-md-3"><input class="form-control" name="variants[0][value]" placeholder="Large"></div>
                        <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="variants[0][price]" placeholder="Price"></div>
                        <div class="col-md-2"><input class="form-control" name="variants[0][sku]" placeholder="SKU"></div>
                        <div class="col-md-2"><select class="form-control" name="variants[0][status]"><option value="active">active</option><option value="inactive">inactive</option></select></div>
                    </div>
                @endforelse
            </div>

            <button class="btn btn-primary mt-3">Save</button>
        </form>
    </div></div>
@endsection

