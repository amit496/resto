@extends('admin.layout.index')
@section('title', 'Branch Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Branch Details</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-primary"><i class="fa-solid fa-edit me-1"></i>Edit</a>
            <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
        </div>
    </div>

    <div class="card"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Restaurant</th><td>{{ $branch->restaurant?->name ?: '-' }}</td></tr>
            <tr><th>Name</th><td>{{ $branch->name }}</td></tr>
            <tr>
                <th>Image</th>
                <td>
                    @if($branch->image)
                        <img src="{{ \App\Support\ImagePath::thumbUrl($branch->image, 'admin/assets/img/logo.svg') }}" alt="{{ $branch->name }}" style="width:90px;height:64px;object-fit:cover;border-radius:8px;">
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Manager Photo</th>
                <td>
                    @if($branch->manager_photo)
                        <img src="{{ \App\Support\ImagePath::thumbUrl($branch->manager_photo, 'admin/assets/img/logo.svg') }}" alt="{{ $branch->manager_name ?: $branch->name }}" style="width:90px;height:64px;object-fit:cover;border-radius:8px;">
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr><th>Code</th><td>{{ $branch->code ?: '-' }}</td></tr>
            <tr><th>Phone</th><td>{{ $branch->phone ?: '-' }}</td></tr>
            <tr><th>Manager Name</th><td>{{ $branch->manager_name ?: '-' }}</td></tr>
            <tr><th>Manager Phone</th><td>{{ $branch->manager_phone ?: '-' }}</td></tr>
            <tr><th>Manager Email</th><td>{{ $branch->manager_email ?: '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $branch->status->value }}</td></tr>
            <tr><th>Address</th><td>{{ $branch->address ?: '-' }}</td></tr>
            <tr><th>Opening Time</th><td>{{ $branch->opening_time ?: '-' }}</td></tr>
            <tr><th>Closing Time</th><td>{{ $branch->closing_time ?: '-' }}</td></tr>
            <tr><th>Weekly Off</th><td>{{ $branch->weekly_off ?: '-' }}</td></tr>
            <tr><th>Delivery Radius (KM)</th><td>{{ $branch->delivery_radius_km ?: '-' }}</td></tr>
            <tr><th>Latitude</th><td>{{ $branch->latitude ?: '-' }}</td></tr>
            <tr><th>Longitude</th><td>{{ $branch->longitude ?: '-' }}</td></tr>
            <tr><th>GST No</th><td>{{ $branch->gst_no ?: '-' }}</td></tr>
            <tr><th>FSSAI No</th><td>{{ $branch->fssai_no ?: '-' }}</td></tr>
        </table>
    </div></div>
@endsection

