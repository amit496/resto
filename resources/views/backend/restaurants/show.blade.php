@extends('admin.layout.index')
@section('title', 'Restaurant Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Restaurant Details</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="btn btn-primary"><i class="fa-solid fa-edit me-1"></i>Edit Profile</a>
            <form method="POST" action="{{ route('admin.restaurants.toggle-status', $restaurant) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Restaurant Status?" data-confirm-text="This will update overall restaurant operational status." data-confirm-button="{{ $restaurant->status->value === 'active' ? 'Set Inactive' : 'Set Active' }}">
                @csrf @method('PATCH')
                <button class="btn {{ $restaurant->status->value === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                    {{ $restaurant->status->value === 'active' ? 'Inactive' : 'Active' }}
                </button>
            </form>
            <a href="{{ route('admin.restaurants.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
        </div>
    </div>

    <div class="card"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr>
                <th style="width:220px;">Logo</th>
                <td>
                    <img src="{{ \App\Support\ImagePath::thumbUrl($restaurant->logo_path, 'admin/assets/img/logo.svg') }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                </td>
            </tr>
            <tr>
                <th>Cover Image</th>
                <td>
                    <img src="{{ \App\Support\ImagePath::thumbUrl($restaurant->cover_image, 'admin/assets/img/logo.svg') }}" style="width:140px;height:70px;object-fit:cover;border-radius:8px;">
                </td>
            </tr>
            <tr><th style="width:220px;">Name</th><td>{{ $restaurant->name }}</td></tr>
            <tr><th>Slug</th><td>{{ $restaurant->slug }}</td></tr>
            <tr><th>Email</th><td>{{ $restaurant->email ?: '-' }}</td></tr>
            <tr><th>Phone</th><td>{{ $restaurant->phone ?: '-' }}</td></tr>
            <tr><th>Address</th><td>{{ $restaurant->address ?: '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $restaurant->status->value }}</td></tr>
            <tr><th>Total Categories</th><td>{{ $restaurant->categories_count }}</td></tr>
            <tr><th>Total Products</th><td>{{ $restaurant->products_count }}</td></tr>
            <tr><th>Total Customers</th><td>{{ $restaurant->customers_count }}</td></tr>
            <tr><th>Total Delivery Boys</th><td>{{ $restaurant->delivery_boys_count }}</td></tr>
            <tr><th>Total Coupons</th><td>{{ $restaurant->coupons_count }}</td></tr>
        </table>
    </div></div>
@endsection


