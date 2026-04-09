@extends('admin.layout.index')
@section('title', $coupon->exists ? 'Edit Coupon' : 'Add Coupon')
@section('content')
    <div class="mb-4"><h3 class="mb-0">{{ $coupon->exists ? 'Edit Coupon' : 'Add Coupon' }}</h3></div>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}">
            @csrf
            @if($coupon->exists) @method('PUT') @endif
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Restaurant</label>
                    <select class="form-control" name="restaurant_id" required>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" @selected(old('restaurant_id', $coupon->restaurant_id) == $restaurant->id)>{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3"><label class="form-label">Code</label><input class="form-control" name="code" value="{{ old('code', $coupon->code) }}" required></div>
                <div class="col-md-4 mb-3"><label class="form-label">Value</label><input type="number" step="0.01" class="form-control" name="value" value="{{ old('value', $coupon->value) }}" required></div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Type</label>
                    <select class="form-control" name="type">
                        @foreach($typeOptions as $option)
                            <option value="{{ $option->value }}" @selected(old('type', $coupon->type?->value ?? 'percent') === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3"><label class="form-label">Min Order</label><input type="number" step="0.01" class="form-control" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? 0) }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">Start Date</label><input type="date" class="form-control" name="start_date" value="{{ old('start_date', optional($coupon->start_date)->format('Y-m-d')) }}"></div>
                <div class="col-md-3 mb-3"><label class="form-label">End Date</label><input type="date" class="form-control" name="end_date" value="{{ old('end_date', optional($coupon->end_date)->format('Y-m-d')) }}"></div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status">
                        @foreach($statusOptions as $option)
                            <option value="{{ $option->value }}" @selected(old('status', $coupon->status?->value ?? 'active') === $option->value)>{{ $option->value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div></div>
@endsection

