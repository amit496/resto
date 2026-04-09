@extends('admin.layout.index')
@section('title', 'Reservations')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Reservations</h3></div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.reservations.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-2">
                    <select name="restaurant_id" class="form-control">
                        <option value="">Restaurant</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="branch_id" class="form-control">
                        <option value="">Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><input class="form-control" name="guest_name" placeholder="Guest Name" required></div>
                <div class="col-md-2"><input class="form-control" name="guest_phone" placeholder="Phone"></div>
                <div class="col-md-1"><input class="form-control" name="guest_count" type="number" min="1" value="2" required></div>
                <div class="col-md-2"><input class="form-control" name="reserved_for" type="datetime-local" required></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Add</button></div>
                <input type="hidden" name="status" value="pending">
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Guest</th><th>Phone</th><th>Count</th><th>Time</th><th>Status</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->guest_name }}</td>
                    <td>{{ $reservation->guest_phone ?: '-' }}</td>
                    <td>{{ $reservation->guest_count }}</td>
                    <td>{{ $reservation->reserved_for?->format('d M Y H:i') }}</td>
                    <td>{{ $reservation->status }}</td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('admin.reservations.status', $reservation) }}" class="d-inline-flex gap-2">
                            @csrf @method('PATCH')
                            <select name="status" class="form-control form-control-sm">
                                <option value="pending">pending</option>
                                <option value="confirmed">confirmed</option>
                                <option value="seated">seated</option>
                                <option value="completed">completed</option>
                                <option value="cancelled">cancelled</option>
                                <option value="no_show">no_show</option>
                            </select>
                            <button class="btn btn-sm btn-outline-primary">Update</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4">No reservations found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $reservations->links() }}</div>
    </div>
@endsection


