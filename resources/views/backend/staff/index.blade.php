@extends('admin.layout.index')
@section('title', 'Staff Management')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Staff Management</h3></div>

    <div class="card mb-3"><div class="card-body">
        <h5 class="mb-3">Add Staff</h5>
        <form method="POST" action="{{ route('admin.staff.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-2"><input class="form-control" name="name" placeholder="Name" required></div>
                <div class="col-md-2"><input class="form-control" name="email" type="email" placeholder="Email" required></div>
                <div class="col-md-2"><input class="form-control" name="phone" placeholder="Phone"></div>
                <div class="col-md-2">
                    <select class="form-control" name="branch_id">
                        <option value="">Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="staff_type">
                        <option value="">Staff Type</option>
                        @foreach($staffTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <select class="form-control" name="role" required>
                        @foreach($staffRoles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" name="password" type="password" placeholder="Password" required></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div>
            </div>
        </form>
    </div></div>

    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name/email/phone"></div>
            <div class="col-md-3">
                <select class="form-control js-filter-select" name="branch_id">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((int) request('branch_id') === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control js-filter-select" name="staff_type">
                    <option value="">All Staff Types</option>
                    @foreach($staffTypes as $type)
                        <option value="{{ $type->value }}" @selected(request('staff_type') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control js-filter-select" name="status">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status') === 'active')>active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>inactive</option>
                </select>
            </div>
            <div class="col-md-1 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit">Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.staff.index') }}">Reset</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Branch</th><th>Role</th><th>Staff Type</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse($staffUsers as $staff)
                    <tr>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>{{ $staff->phone ?: '-' }}</td>
                        <td>{{ $staff->branch?->name ?: '-' }}</td>
                        <td>{{ $staff->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                        <td>{{ $staff->staff_type ? \App\Enums\StaffTypeEnum::from($staff->staff_type)->label() : '-' }}</td>
                        <td>{{ $staff->is_active ? 'active' : 'inactive' }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-info" href="{{ route('admin.staff.show', $staff) }}"><i class="fa-solid fa-eye me-1"></i>View</a>
                            <form method="POST" action="{{ route('admin.staff.toggle-status', $staff) }}" class="d-inline-block js-confirm-action" data-confirm-title="Change Staff Status?" data-confirm-text="This will update staff account access." data-confirm-button="{{ $staff->is_active ? 'Set Inactive' : 'Set Active' }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $staff->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                    {{ $staff->is_active ? 'Inactive' : 'Active' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" class="d-inline-block js-confirm-action" data-confirm-title="Archive Staff?" data-confirm-text="This will set the staff user to inactive." data-confirm-button="Archive">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-archive me-1"></i>Archive</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4">No staff found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $staffUsers->links() }}</div></div>
@endsection

