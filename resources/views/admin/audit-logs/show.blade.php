@extends('admin.layout.index')

@section('title', 'Audit Log Details')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Audit Log Details</h3>
        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tr><th style="width:220px;">Date/Time</th><td>{{ $log->created_at->format('d M Y h:i A') }}</td></tr>
                <tr><th>User</th><td>{{ $log->user?->name ?? 'System' }}</td></tr>
                <tr><th>Action</th><td>{{ $log->action }}</td></tr>
                <tr><th>Entity Type</th><td>{{ $log->entity_type ?: '-' }}</td></tr>
                <tr><th>Entity ID</th><td>{{ $log->entity_id ?: '-' }}</td></tr>
                <tr><th>Description</th><td>{{ $log->description ?: '-' }}</td></tr>
                <tr><th>IP</th><td>{{ $log->ip_address ?: '-' }}</td></tr>
                <tr><th>Meta</th><td><pre class="mb-0">{{ json_encode($log->meta ?? [], JSON_PRETTY_PRINT) }}</pre></td></tr>
            </table>
        </div>
    </div>
@endsection


