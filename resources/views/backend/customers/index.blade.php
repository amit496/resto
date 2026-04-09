@extends('admin.layout.index')
@section('title', 'Customers')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Customers</h3>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Customer</a>
    </div>
    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, phone, restaurant"></div>
            <div class="col-md-3"><input type="text" name="email" value="{{ request('email') }}" class="form-control" placeholder="Search by email"></div>
            <div class="col-md-3">
                <select name="restaurant_id" class="form-control js-filter-select">
                    <option value="">All Restaurants</option>
                    @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}" @selected((int) request('restaurant_id') === $restaurant->id)>{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit"><i class="fa-solid fa-search me-1"></i>Search</button>
                <a class="btn btn-light w-100" href="{{ route('admin.customers.index') }}"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Image</th><th>Name</th><th>Phone</th><th>Email</th><th>Restaurant</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                @php $imageUrl = \App\Support\ImagePath::thumbUrl($customer->image, 'admin/assets/img/customer/customer11.jpg'); @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $customer->name }}" style="width:42px;height:42px;object-fit:cover;border-radius:8px;">
                            </td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->restaurant?->name }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye me-1"></i>View</a>
                                <a href="{{ route('admin.customers.edit', ['customer' => $customer, 'page' => $customers->currentPage()]) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit me-1"></i>Edit</a>
                                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="d-inline-block js-confirm-action" data-confirm-title="Delete Customer?" data-confirm-text="This will permanently remove the customer account." data-confirm-button="Delete">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash me-1"></i>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $customers->links() }}</div>
    </div>
@endsection


