@extends('admin.layout.index')
@section('title', $restaurant->exists ? 'Edit Restaurant' : 'Add Restaurant')
@section('content')
    <div class="mb-4">
        <h3 class="mb-0">{{ $restaurant->exists ? 'Edit Restaurant' : 'Add Restaurant' }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="{{ $restaurant->exists ? route('admin.restaurants.update', $restaurant) : route('admin.restaurants.store') }}">
                @csrf
                @if($restaurant->exists) @method('PUT') @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input name="name" class="form-control" value="{{ old('name', $restaurant->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug</label>
                        <input name="slug" class="form-control" value="{{ old('slug', $restaurant->slug) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control" value="{{ old('email', $restaurant->email) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input name="phone" class="form-control" value="{{ old('phone', $restaurant->phone) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="active" @selected(old('status', $restaurant->status?->value ?? 'active') === 'active')>active</option>
                            <option value="inactive" @selected(old('status', $restaurant->status?->value) === 'inactive')>inactive</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control">{{ old('address', $restaurant->address) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Restaurant Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*" data-preview-single="#restaurant-logo-preview" data-preview-empty="#restaurant-logo-empty">
                        <div class="mt-2 d-flex align-items-center gap-2">
                            @if($restaurant->logo_path)
                                <img id="restaurant-logo-preview" src="{{ \App\Support\ImagePath::thumbUrl($restaurant->logo_path, 'admin/assets/img/logo.svg') }}" style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="remove-logo">
                                    <label class="form-check-label" for="remove-logo">Remove logo</label>
                                </div>
                            @else
                                <img id="restaurant-logo-preview" src="" style="width:56px;height:56px;object-fit:cover;border-radius:8px;display:none;">
                                <p id="restaurant-logo-empty" class="text-muted mb-0">No logo uploaded.</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cover Image</label>
                        <input type="file" name="cover_image_file" class="form-control" accept="image/*" data-preview-single="#restaurant-cover-preview" data-preview-empty="#restaurant-cover-empty">
                        <div class="mt-2 d-flex align-items-center gap-2">
                            @if($restaurant->cover_image)
                                <img id="restaurant-cover-preview" src="{{ \App\Support\ImagePath::thumbUrl($restaurant->cover_image, 'admin/assets/img/logo.svg') }}" style="width:80px;height:56px;object-fit:cover;border-radius:8px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_cover_image" value="1" id="remove-cover-image">
                                    <label class="form-check-label" for="remove-cover-image">Remove cover</label>
                                </div>
                            @else
                                <img id="restaurant-cover-preview" src="" style="width:80px;height:56px;object-fit:cover;border-radius:8px;display:none;">
                                <p id="restaurant-cover-empty" class="text-muted mb-0">No cover uploaded.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>
@endsection

