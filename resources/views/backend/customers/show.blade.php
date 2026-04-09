@extends('admin.layout.index')
@section('title', 'Customer Details')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Customer Details</h3>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-list me-1"></i>Back To List</a>
    </div>

    <div class="card"><div class="card-body">
        <table class="table table-bordered mb-0">
            <tr><th style="width:220px;">Image</th><td><img src="{{ \App\Support\ImagePath::thumbUrl($customer->image, 'admin/assets/img/customer/customer11.jpg') }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;"></td></tr>
            <tr><th>Name</th><td>{{ $customer->name }}</td></tr>
            <tr><th>Phone</th><td>{{ $customer->phone ?: '-' }}</td></tr>
            <tr><th>Email</th><td>{{ $customer->email ?: '-' }}</td></tr>
            <tr><th>Address</th><td>{{ $customer->address ?: '-' }}</td></tr>
            <tr><th>Restaurant</th><td>{{ $customer->restaurant?->name }}</td></tr>
            <tr><th>Total Orders</th><td>{{ $customer->orders->count() }}</td></tr>
        </table>
    </div></div>

    <div class="card mt-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Saved Addresses</h5>
            <span class="text-muted">Total: {{ $customer->addresses?->count() ?? 0 }}</span>
        </div>
        <div class="card-body">
            @if(($customer->addresses?->count() ?? 0) === 0)
                <div class="text-muted">No saved addresses found for this customer.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th style="width:140px;">Type</th>
                                <th style="width:180px;">Label</th>
                                <th>Recipient</th>
                                <th style="width:160px;">Phone</th>
                                <th>Address</th>
                                <th style="width:140px;">Landmark</th>
                                <th style="width:120px;">Pincode</th>
                                <th style="width:120px;">Country</th>
                                <th>Instructions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->addresses->sortByDesc('is_default') as $address)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark text-uppercase">{{ $address->type }}</span>
                                        @if($address->is_default)
                                            <span class="badge bg-warning text-dark ms-1">Default</span>
                                        @endif
                                    </td>
                                    <td>{{ $address->label ?: '-' }}</td>
                                    <td>{{ $address->recipient_name ?: $customer->name }}</td>
                                    <td>{{ $address->phone ?: ($customer->phone ?: '-') }}</td>
                                    <td>
                                        <div>{{ $address->address_line_1 }}</div>
                                        @if($address->address_line_2)
                                            <small class="text-muted">{{ $address->address_line_2 }}</small>
                                        @endif
                                        <div class="text-muted">
                                            <small>
                                                {{ collect([$address->city, $address->state])->filter()->implode(', ') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>{{ $address->landmark ?: '-' }}</td>
                                    <td>{{ $address->postal_code ?: '-' }}</td>
                                    <td>{{ $address->country_code ?: '-' }}</td>
                                    <td>{{ $address->instructions ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection


