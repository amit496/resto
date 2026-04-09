@extends('admin.layout.index')
@section('title', 'Delivery Boy Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Delivery Boy Details</h3>
        <a href="{{ route('admin.delivery-boys.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Image</th><td><img src="{{ \App\Support\ImagePath::thumbUrl($deliveryBoy->image, 'admin/assets/img/avatar/avatar-1.jpg') }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;"></td></tr>
            <tr><th>Name</th><td>{{ $deliveryBoy->name }}</td></tr>
            <tr><th>Phone</th><td>{{ $deliveryBoy->phone ?: '-' }}</td></tr>
            <tr><th>Vehicle No</th><td>{{ $deliveryBoy->vehicle_no ?: '-' }}</td></tr>
            <tr><th>Status</th><td>{{ $deliveryBoy->status->value }}</td></tr>
            <tr><th>Restaurant</th><td>{{ $deliveryBoy->restaurant?->name }}</td></tr>
            <tr><th>Active Orders</th><td>{{ $activeOrdersCount }}</td></tr>
            <tr><th>Delivered Orders</th><td>{{ $deliveredOrdersCount }}</td></tr>
            <tr><th>Total Collection</th><td>{{ number_format((float) $collectedAmount, 2) }}</td></tr>
        </table>
    </div></div>

    <div class="card mt-3"><div class="card-body">
        <h5 class="mb-3">Recent Assigned Orders</h5>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Order No</th><th>Customer</th><th>Status</th><th>Total</th><th>Created</th></tr></thead>
                <tbody>
                    @forelse($deliveryBoy->orders as $order)
                        <tr>
                            <td>{{ $order->order_no }}</td>
                            <td>{{ $order->customer?->name ?: '-' }}</td>
                            <td>{{ $order->status->value }}</td>
                            <td>{{ $order->total_amount }}</td>
                            <td>{{ $order->created_at?->format('d M Y h:i A') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-3">No assigned orders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div></div>
@endsection


