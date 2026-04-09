@extends('admin.layout.index')
@section('title', 'Staff Details')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Staff Details</h3>
            <p class="text-muted mb-0">View staff profile and update role, branch, and status.</p>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Profile Overview</h5>
                    <table class="table table-bordered mb-0">
                        <tr><th style="width:180px;">Name</th><td>{{ $staff->name }}</td></tr>
                        <tr><th>Email</th><td>{{ $staff->email }}</td></tr>
                        <tr><th>Phone</th><td>{{ $staff->phone ?: '-' }}</td></tr>
                        <tr><th>Branch</th><td>{{ $staff->branch?->name ?: '-' }}</td></tr>
                        <tr><th>Role</th><td>{{ $staff->roles->pluck('name')->implode(', ') ?: '-' }}</td></tr>
                        <tr><th>Staff Type</th><td>{{ $staff->staff_type ? \App\Enums\StaffTypeEnum::from($staff->staff_type)->label() : '-' }}</td></tr>
                        <tr><th>Status</th><td>{{ $staff->is_active ? 'active' : 'inactive' }}</td></tr>
                        <tr><th>Created</th><td>{{ $staff->created_at?->format('d M Y, h:i A') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Update Staff Details</h5>
                    <form method="POST" action="{{ route('admin.staff.update', $staff) }}">
                        @csrf @method('PUT')
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input class="form-control" name="phone" value="{{ old('phone', $staff->phone) }}" placeholder="Phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Branch</label>
                                <select class="form-control" name="branch_id">
                                    <option value="">Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected(old('branch_id', $staff->branch_id) == $branch->id)>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Staff Type</label>
                                <select class="form-control" name="staff_type">
                                    <option value="">Staff Type</option>
                                    @foreach($staffTypes as $type)
                                        <option value="{{ $type->value }}" @selected(old('staff_type', $staff->staff_type) === $type->value)>{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                                <select class="form-control" name="role">
                                    <option value="manager" @selected(old('role', $staff->hasRole('manager') ? 'manager' : 'staff') === 'manager')>manager</option>
                                    <option value="staff" @selected(old('role', $staff->hasRole('manager') ? 'manager' : 'staff') === 'staff')>staff</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="is_active">
                                    <option value="1" @selected(old('is_active', $staff->is_active ? 1 : 0) == 1)>active</option>
                                    <option value="0" @selected(old('is_active', $staff->is_active ? 1 : 0) == 0)>inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary">Save Updates</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.staff.toggle-status', $staff) }}" class="js-confirm-action" data-confirm-title="Change Staff Status?" data-confirm-text="This will update staff account access." data-confirm-button="{{ $staff->is_active ? 'Set Inactive' : 'Set Active' }}">
                    @csrf @method('PATCH')
                    <button class="btn {{ $staff->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        {{ $staff->is_active ? 'Set Inactive' : 'Set Active' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" class="js-confirm-action" data-confirm-title="Archive Staff?" data-confirm-text="This will set the staff user to inactive." data-confirm-button="Archive">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger"><i class="fa-solid fa-archive me-1"></i>Archive</button>
                </form>
            </div>
        </div>
    </div>
@endsection
