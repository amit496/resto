@extends('admin.layout.index')

@section('title', 'User Management')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">User Management</h3>
            <p class="text-muted mb-0">Manage admin and staff accounts.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i>Add User
        </a>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-8">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search user name, email">
            </div>
            <div class="col-md-2">
                <select name="role" class="form-control js-filter-select">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.users.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Permissions</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    @php
                                        $profileImageUrl = $user->profile_image
                                            ? (\Illuminate\Support\Str::startsWith($user->profile_image, 'backend/')
                                                ? asset('storage/'.$user->profile_image)
                                                : asset($user->profile_image))
                                            : asset('admin/assets/img/profiles/avator1.jpg');
                                    @endphp
                                    <img src="{{ $profileImageUrl }}" alt="{{ $user->name }}" style="width:40px;height:40px;object-fit:cover;border-radius:999px;">
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span class="badge bg-secondary me-1">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">No role</span>
                                    @endforelse
                                </td>
                                <td>
                                    @forelse ($user->permissions as $permission)
                                        <span class="badge bg-light text-dark border me-1">{{ $permission->name }}</span>
                                    @empty
                                        <span class="text-muted">Inherited by role</span>
                                    @endforelse
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye me-1"></i>View</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                        class="d-inline-block js-confirm-action"
                                        data-confirm-title="Delete User Account?"
                                        data-confirm-text="This action will permanently remove the user and cannot be undone."
                                        data-confirm-button="Yes, Delete User">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
@endsection


