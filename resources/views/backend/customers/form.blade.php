@extends('admin.layout.index')
@section('title', $customer->exists ? 'Edit Customer' : 'Add Customer')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">{{ $customer->exists ? 'Edit Customer' : 'Add Customer' }}</h3>
        <a href="{{ route('admin.customers.index', ['page' => request('page', 1)]) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-list me-1"></i>Back To List
        </a>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ $customer->exists ? route('admin.customers.update', $customer) : route('admin.customers.store') }}">
            @csrf
            @if($customer->exists) @method('PUT') @endif
            <input type="hidden" name="redirect_page" value="{{ old('redirect_page', request('page', 1)) }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Restaurant</label>
                    <select class="form-control" name="restaurant_id" required>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" @selected(old('restaurant_id', $customer->restaurant_id) == $restaurant->id)>{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3"><label class="form-label">Name</label><input name="name" class="form-control" value="{{ old('name', $customer->name) }}" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Email</label><input name="email" class="form-control" value="{{ old('email', $customer->email) }}"></div>
                <div class="col-md-6 mb-3"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address', $customer->address) }}"></div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Customer Image</label>
                    <input type="file" name="image" accept="image/*" class="form-control" data-preview-single="#customer-image-preview" data-preview-empty="#customer-image-empty">
                </div>
                <div class="col-md-6 mb-3">
                    @php
                        $customerImageUrl = $customer->image ? \App\Support\ImagePath::thumbUrl($customer->image, 'admin/assets/img/customer/customer11.jpg') : null;
                    @endphp
                    <label class="form-label d-block">Image Preview</label>
                    <img id="customer-image-preview" src="{{ $customerImageUrl ?? '' }}" alt="{{ $customer->name }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;{{ $customerImageUrl ? '' : 'display:none;' }}">
                    @if($customerImageUrl)
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="remove-customer-image" name="remove_image">
                            <label class="form-check-label" for="remove-customer-image">Remove current image</label>
                        </div>
                    @else
                        <p id="customer-image-empty" class="text-muted mb-0">No image uploaded.</p>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary">Save</button>
                <a class="btn btn-light" href="{{ route('admin.customers.index', ['page' => request('page', 1)]) }}">Cancel</a>
            </div>
        </form>
    </div></div>
@endsection

