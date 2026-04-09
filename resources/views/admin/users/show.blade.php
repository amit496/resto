@extends('admin.layout.index')

@section('title', 'User Details')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">User Details</h3>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tr><th style="width:220px;">Image</th><td><img src="{{ \App\Support\ImagePath::thumbUrl($user->profile_image, 'admin/assets/img/profiles/avator1.jpg') }}" style="width:64px;height:64px;object-fit:cover;border-radius:999px;"></td></tr>
                <tr><th>Name</th><td>{{ $user->name }}</td></tr>
                <tr><th>Email</th><td>{{ $user->email }}</td></tr>
                <tr><th>Roles</th><td>{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td></tr>
                <tr><th>Direct Permissions</th><td>{{ $user->permissions->pluck('name')->join(', ') ?: '-' }}</td></tr>
                <tr><th>Created</th><td>{{ $user->created_at->format('d M Y h:i A') }}</td></tr>
            </table>
        </div>
    </div>
@endsection


