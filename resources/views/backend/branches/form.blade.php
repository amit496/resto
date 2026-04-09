@extends('admin.layout.index')
@section('title', 'Edit Branch')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Edit Branch</h3>
        <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
    </div>

    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.branches.update', $branch) }}">
            @csrf @method('PUT')
            <div class="row">
                @if(! $singleRestaurantMode)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Restaurant</label>
                        <select class="form-control" disabled>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" @selected($branch->restaurant_id === $restaurant->id)>{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name</label>
                    <input class="form-control" name="name" value="{{ old('name', $branch->name) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Code</label>
                    <input class="form-control" name="code" value="{{ old('code', $branch->code) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Phone</label>
                    <input class="form-control" name="phone" value="{{ old('phone', $branch->phone) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        <option value="active" @selected(old('status', $branch->status->value) === 'active')>active</option>
                        <option value="inactive" @selected(old('status', $branch->status->value) === 'inactive')>inactive</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Manager Name</label>
                    <input class="form-control" name="manager_name" value="{{ old('manager_name', $branch->manager_name) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Manager Phone</label>
                    <input class="form-control" name="manager_phone" value="{{ old('manager_phone', $branch->manager_phone) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Manager Email</label>
                    <input class="form-control" type="email" name="manager_email" value="{{ old('manager_email', $branch->manager_email) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Manager Photo</label>
                    <input type="file" name="manager_photo" class="form-control" accept="image/*" data-preview-single="#manager-photo-preview" data-preview-empty="#manager-photo-empty">
                    <div class="mt-2 d-flex align-items-center gap-2">
                        @if($branch->manager_photo)
                            <img id="manager-photo-preview" src="{{ \App\Support\ImagePath::thumbUrl($branch->manager_photo, 'admin/assets/img/logo.svg') }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_manager_photo" value="1" id="remove-manager-photo">
                                <label class="form-check-label" for="remove-manager-photo">Remove photo</label>
                            </div>
                        @else
                            <img id="manager-photo-preview" src="" style="width:64px;height:64px;object-fit:cover;border-radius:8px;display:none;">
                            <p id="manager-photo-empty" class="text-muted mb-0">No manager photo uploaded.</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="address">{{ old('address', $branch->address) }}</textarea>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Opening Time</label>
                    <input class="form-control" type="time" name="opening_time" value="{{ old('opening_time', $branch->opening_time) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Closing Time</label>
                    <input class="form-control" type="time" name="closing_time" value="{{ old('closing_time', $branch->closing_time) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Weekly Off</label>
                    <input class="form-control" name="weekly_off" value="{{ old('weekly_off', $branch->weekly_off) }}" placeholder="Sunday">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Delivery Radius (KM)</label>
                    <input class="form-control" type="number" step="0.01" name="delivery_radius_km" value="{{ old('delivery_radius_km', $branch->delivery_radius_km) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Latitude</label>
                    <input class="form-control" type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $branch->latitude) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Longitude</label>
                    <input class="form-control" type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $branch->longitude) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">GST No</label>
                    <input class="form-control" name="gst_no" value="{{ old('gst_no', $branch->gst_no) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">FSSAI No</label>
                    <input class="form-control" name="fssai_no" value="{{ old('fssai_no', $branch->fssai_no) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Branch Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" data-preview-single="#branch-image-preview" data-preview-empty="#branch-image-empty">
                    <div class="mt-2 d-flex align-items-center gap-2">
                        @if($branch->image)
                            <img id="branch-image-preview" src="{{ \App\Support\ImagePath::thumbUrl($branch->image, 'admin/assets/img/logo.svg') }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove-branch-image">
                                <label class="form-check-label" for="remove-branch-image">Remove image</label>
                            </div>
                        @else
                            <img id="branch-image-preview" src="" style="width:64px;height:64px;object-fit:cover;border-radius:8px;display:none;">
                            <p id="branch-image-empty" class="text-muted mb-0">No image uploaded.</p>
                        @endif
                    </div>
                </div>
            </div>
            <button class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Save Changes</button>
        </form>
    </div></div>
@endsection

