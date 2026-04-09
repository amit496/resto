@extends('admin.layout.index')

@section('title', 'Create User')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Create User</h3>
            <p class="text-muted mb-0">Add new staff/admin account.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="profile_image" class="form-control" accept="image/*" data-preview-single="#new-user-profile-preview" data-preview-empty="#new-user-profile-empty">
                    <div class="mt-2">
                        <img id="new-user-profile-preview" src="" alt="Profile preview" style="width:48px;height:48px;object-fit:cover;border-radius:999px;display:none;">
                        <p id="new-user-profile-empty" class="text-muted mb-0">No image selected.</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Roles</label>
                    <select class="form-control" name="roles[]" multiple>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ in_array($role->name, old('roles', []), true) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Permissions (optional)</label>
                    <select class="form-control" name="permissions[]" multiple>
                        @foreach ($permissions as $permission)
                            <option value="{{ $permission->name }}"
                                {{ in_array($permission->name, old('permissions', []), true) ? 'selected' : '' }}>
                                {{ $permission->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Create User</button>
            </form>
        </div>
    </div>
@endsection


