@extends('admin.layout.index')
@section('title', 'Orders')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Food Orders</h3>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Order</a>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search order no, customer"></div>
            <div class="col-md-3">
                <select name="restaurant_id" class="form-control js-filter-select">
                    <option value="">All Restaurants</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}" @selected((int) request('restaurant_id') === $restaurant->id)>{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="customer_id" class="form-control js-filter-select">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected((int) request('customer_id') === $customer->id)>{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="branch_id" class="form-control js-filter-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((int) request('branch_id') === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="delivery_boy_id" class="form-control js-filter-select">
                    <option value="">All Delivery Boys</option>
                    @foreach($deliveryBoys as $deliveryBoy)
                        <option value="{{ $deliveryBoy->id }}" @selected((int) request('delivery_boy_id') === $deliveryBoy->id)>{{ $deliveryBoy->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="order_type" class="form-control js-filter-select">
                    <option value="">All Types</option>
                    <option value="dine_in" @selected(request('order_type')==='dine_in')>dine_in</option>
                    <option value="takeaway" @selected(request('order_type')==='takeaway')>takeaway</option>
                    <option value="delivery" @selected(request('order_type')==='delivery')>delivery</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control js-filter-select">
                    <option value="">All Status</option>
                    <option value="pending" @selected(request('status')==='pending')>pending</option>
                    <option value="preparing" @selected(request('status')==='preparing')>preparing</option>
                    <option value="ready" @selected(request('status')==='ready')>ready</option>
                    <option value="delivered" @selected(request('status')==='delivered')>delivered</option>
                    <option value="cancelled" @selected(request('status')==='cancelled')>cancelled</option>
                </select>
            </div>
            <div class="col-md-3 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.orders.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Order No</th><th>Restaurant</th><th>Branch</th><th>Customer</th><th>Delivery Boy</th><th>Status</th><th>Total</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_no }}</td>
                            <td>{{ $order->restaurant?->name }}</td>
                            <td>{{ $order->branch?->name ?: '-' }}</td>
                            <td>{{ $order->customer?->name }}</td>
                            <td>{{ $order->deliveryBoy?->name ?: '-' }}</td>
                            <td>{{ $order->status->value }}</td>
                            <td>{{ $order->total_amount }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.orders.show', $order) }}"><i class="fa-solid fa-eye me-1"></i>View</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.orders.edit', $order) }}"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="d-inline-block js-confirm-action" data-confirm-title="Delete Order?" data-confirm-text="This will permanently remove the order and its items." data-confirm-button="Delete">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $orders->links() }}</div>
    </div>
@endsection


