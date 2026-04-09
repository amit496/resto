@extends('admin.layout.index')
@section('title', $deliveryBoy->exists ? 'Edit Delivery Boy' : 'Add Delivery Boy')
@section('content')
    <div class="mb-4"><h3 class="mb-0">{{ $deliveryBoy->exists ? 'Edit Delivery Boy' : 'Add Delivery Boy' }}</h3></div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ $deliveryBoy->exists ? route('admin.delivery-boys.update', $deliveryBoy) : route('admin.delivery-boys.store') }}">
            @csrf
            @if($deliveryBoy->exists) @method('PUT') @endif
            <input type="hidden" name="redirect_page" value="{{ old('redirect_page', request('page', 1)) }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Restaurant</label>
                    <select class="form-control" name="restaurant_id" required>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" @selected(old('restaurant_id', $deliveryBoy->restaurant_id) == $restaurant->id)>{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3"><label class="form-label">Name</label><input class="form-control" name="name" value="{{ old('name', $deliveryBoy->name) }}" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ old('phone', $deliveryBoy->phone) }}"></div>
                <div class="col-md-4 mb-3"><label class="form-label">Vehicle No</label><input class="form-control" name="vehicle_no" value="{{ old('vehicle_no', $deliveryBoy->vehicle_no) }}"></div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        @foreach($statusOptions as $option)
                            <option value="{{ $option->value }}" @selected(old('status', $deliveryBoy->status?->value ?? 'active') === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Delivery Boy Image</label>
                    <input type="file" name="image" accept="image/*" class="form-control" data-preview-single="#delivery-image-preview" data-preview-empty="#delivery-image-empty">
                </div>
                <div class="col-md-4 mb-3">
                    @php
                        $deliveryImageUrl = $deliveryBoy->image ? \App\Support\ImagePath::thumbUrl($deliveryBoy->image, 'admin/assets/img/avatar/avatar-1.jpg') : null;
                    @endphp
                    <label class="form-label d-block">Image Preview</label>
                    <img id="delivery-image-preview" src="{{ $deliveryImageUrl ?? '' }}" alt="{{ $deliveryBoy->name }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;{{ $deliveryImageUrl ? '' : 'display:none;' }}">
                    @if($deliveryImageUrl)
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="remove-delivery-image" name="remove_image">
                            <label class="form-check-label" for="remove-delivery-image">Remove current image</label>
                        </div>
                    @else
                        <p id="delivery-image-empty" class="text-muted mb-0">No image uploaded.</p>
                    @endif
                </div>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div></div>
@endsection

