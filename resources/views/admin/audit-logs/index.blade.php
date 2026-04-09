@extends('admin.layout.index')

@section('title', 'Audit Logs')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Audit Logs</h3>
            <p class="text-muted mb-0">Track user actions and system events.</p>
        </div>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search description, entity, user">
            </div>
            <div class="col-md-3">
                <input type="text" name="action" value="{{ request('action') }}" class="form-control" placeholder="Search action">
            </div>
            <div class="col-md-2">
                <select name="user_id" class="form-control js-filter-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((int) request('user_id') === $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search"></i></button>
                <a class="btn btn-light w-100" href="{{ route('admin.audit-logs.index') }}"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP</th>
                            <th class="text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                <td>{{ $log->user?->name ?? 'System' }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->description ?? '-' }}</td>
                                <td>{{ $log->ip_address ?? '-' }}</td>
                                <td class="text-end"><a href="{{ route('admin.audit-logs.show', $log) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye me-1"></i>View</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
@endsection


