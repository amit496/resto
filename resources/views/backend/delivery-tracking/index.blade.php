@extends('admin.layout.index')
@section('title', 'Delivery Tracking')
@section('content')
    <div class="mb-4"><h3 class="mb-0">Delivery Tracking</h3></div>

    <div class="card mb-3"><div class="card-body">
        <form method="POST" action="{{ route('admin.delivery-tracking.store') }}">
            @csrf
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="food_order_id" class="form-control" required>
                        <option value="">Select Delivery Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}">{{ $order->order_no }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="delivery_boy_id" class="form-control">
                        <option value="">Rider</option>
                        @foreach($deliveryBoys as $boy)
                            <option value="{{ $boy->id }}">{{ $boy->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control" required>
                        <option value="assigned">assigned</option>
                        <option value="picked_up">picked_up</option>
                        <option value="on_the_way">on_the_way</option>
                        <option value="arrived">arrived</option>
                        <option value="delivered">delivered</option>
                        <option value="failed">failed</option>
                    </select>
                </div>
                <div class="col-md-2"><input name="location" class="form-control" placeholder="Location"></div>
                <div class="col-md-2"><input name="message" class="form-control" placeholder="Message"></div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Log</button></div>
            </div>
        </form>
    </div></div>

    <div class="card"><div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Order</th><th>Status</th><th>Rider</th><th>Location</th><th>Message</th><th>Time</th></tr></thead>
            <tbody>
            @forelse($events as $event)
                <tr>
                    <td>{{ $event->order?->order_no }}</td>
                    <td>{{ $event->status }}</td>
                    <td>{{ $event->deliveryBoy?->name ?: '-' }}</td>
                    <td>{{ $event->location ?: '-' }}</td>
                    <td>{{ $event->message ?: '-' }}</td>
                    <td>{{ $event->event_at?->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-4">No tracking events found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div><div class="card-footer">{{ $events->links() }}</div></div>
@endsection


