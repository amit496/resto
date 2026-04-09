@extends('admin.layout.index')
@section('title', 'Notifications')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Notification Center</h3></div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.notifications.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-2"><input class="form-control" name="title" placeholder="Title" required></div>
                <div class="col-md-4"><input class="form-control" name="message" placeholder="Message" required></div>
                <div class="col-md-2">
                    <select class="form-control" name="channel">
                        <option value="dashboard">dashboard</option>
                        <option value="email">email</option>
                        <option value="sms">sms</option>
                        <option value="push">push</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="audience">
                        <option value="admin">admin</option>
                        <option value="staff">staff</option>
                        <option value="customer">customer</option>
                        <option value="all">all</option>
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Send</button></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Title</th><th>Channel</th><th>Audience</th><th>Read</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($notifications as $notification)
                <tr>
                    <td>{{ $notification->title }}</td>
                    <td>{{ $notification->channel }}</td>
                    <td>{{ $notification->audience }}</td>
                    <td>{{ $notification->is_read ? 'yes' : 'no' }}</td>
                    <td class="text-end">
                        @if(! $notification->is_read)
                            <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-outline-primary">Mark Read</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No notifications found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $notifications->links() }}</div></div>
@endsection


