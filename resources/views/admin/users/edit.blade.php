@extends('admin.layout.index')

@section('title', 'Edit User')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Edit User</h3>
            <p class="text-muted mb-0">Update account details and role access.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password (optional)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="profile_image" class="form-control" accept="image/*" data-preview-single="#edit-user-profile-preview" data-preview-empty="#edit-user-profile-empty">
                    @if($user->profile_image)
                        @php
                            $profileImageUrl = \App\Support\ImagePath::thumbUrl($user->profile_image, 'admin/assets/img/profiles/avator1.jpg');
                        @endphp
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <img id="edit-user-profile-preview" src="{{ $profileImageUrl }}" alt="{{ $user->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:999px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_profile_image" value="1" id="remove-profile-image">
                                <label class="form-check-label" for="remove-profile-image">Remove current image</label>
                            </div>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <img id="edit-user-profile-preview" src="" alt="{{ $user->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:999px;display:none;">
                            <p id="edit-user-profile-empty" class="text-muted mb-0">No image uploaded.</p>
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="form-label">Roles</label>
                    <select class="form-control" name="roles[]" multiple>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ in_array($role->name, old('roles', $user->roles->pluck('name')->all()), true) ? 'selected' : '' }}>
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
                                {{ in_array($permission->name, old('permissions', $user->permissions->pluck('name')->all()), true) ? 'selected' : '' }}>
                                {{ $permission->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Update User</button>
            </form>
        </div>
    </div>
@endsection


