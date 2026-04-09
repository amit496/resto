@extends('admin.layout.index')
@section('title', $category->exists ? 'Edit Category' : 'Add Category')
@section('content')
    <div class="mb-4"><h3 class="mb-0">{{ $category->exists ? 'Edit Category' : 'Add Category' }}</h3></div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
            @csrf
            @if($category->exists) @method('PUT') @endif
            <input type="hidden" name="redirect_page" value="{{ old('redirect_page', request('page', 1)) }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Restaurant</label>
                    <select name="restaurant_id" class="form-control" required>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" @selected(old('restaurant_id', $category->restaurant_id) == $restaurant->id)>{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name', $category->name) }}" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Slug</label><input name="slug" class="form-control" value="{{ old('slug', $category->slug) }}"></div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Category Image</label>
                    <input type="file" name="image" accept="image/*" class="form-control" data-preview-single="#category-image-preview" data-preview-empty="#category-image-empty">
                </div>
                <div class="col-md-4 mb-3">
                    @php
                        $categoryImage = old('existing_image', $category->image);
                        $categoryImageUrl = $categoryImage ? \App\Support\ImagePath::thumbUrl($categoryImage, \App\Support\FoodImageResolver::category(old('name', $category->name))) : null;
                    @endphp
                    <label class="form-label d-block">Image Preview</label>
                    <img id="category-image-preview" src="{{ $categoryImageUrl ?? '' }}" alt="Category" style="width:72px;height:72px;object-fit:cover;border-radius:10px;{{ $categoryImageUrl ? '' : 'display:none;' }}">
                    @if($categoryImageUrl)
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="remove-image" name="remove_image">
                            <label class="form-check-label" for="remove-image">Remove current image</label>
                        </div>
                    @else
                        <p id="category-image-empty" class="text-muted mb-0">No image uploaded.</p>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" @selected(old('status', $category->status?->value ?? 'active') === 'active')>active</option>
                        <option value="inactive" @selected(old('status', $category->status?->value) === 'inactive')>inactive</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div></div>
@endsection

