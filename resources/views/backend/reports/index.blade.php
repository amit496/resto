@extends('admin.layout.index')
@section('title', 'Reports')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h3 class="mb-0">Reports & Analytics</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export.pdf', request()->query()) }}" class="btn btn-outline-danger">
                <i class="fa-solid fa-file-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('admin.reports.export.excel', request()->query()) }}" class="btn btn-outline-success">
                <i class="fa-solid fa-file-excel me-1"></i>Excel
            </a>
        </div>
    </div>

    <form method="GET" class="mb-3 module-filter-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="q" value="{{ request('q', $search) }}" class="form-control" placeholder="Order no, customer, txn, item">
            </div>
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from" value="{{ request('from', $from->toDateString()) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to" value="{{ request('to', $to->toDateString()) }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Branch</label>
                <select name="branch_id" class="form-control js-filter-select">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((int) request('branch_id', $branchId) === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 module-filter-actions">
                <button class="btn btn-outline-secondary w-100" type="submit">
                    <i class="fa-solid fa-filter me-1"></i>Apply Filter
                </button>
                <a class="btn btn-light w-100" href="{{ route('admin.reports.index') }}">
                    <i class="fa-solid fa-rotate-left me-1"></i>Reset
                </a>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Orders</h6><h3>{{ $ordersCount }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Delivered</h6><h3>{{ $deliveredOrdersCount }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Cancelled</h6><h3>{{ $cancelledOrdersCount }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Revenue</h6><h3>{{ number_format($revenue, 2) }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Avg Order Value</h6><h3>{{ number_format($avgOrderValue, 2) }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Order Success %</h6><h3>{{ number_format($orderSuccessRate, 2) }}%</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Cancellation %</h6><h3>{{ number_format($cancellationRate, 2) }}%</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Payments</h6><h3>{{ $paymentsCount }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Customers</h6><h3>{{ $customersCount }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Products</h6><h3>{{ $productsCount }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Delivery Boys</h6><h3>{{ $deliveryBoysCount }}</h3></div></div></div>
        <div class="col-md-3 d-flex"><div class="card flex-fill"><div class="card-body"><h6>Coupons</h6><h3>{{ $couponsCount }}</h3></div></div></div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-4 d-flex">
            <div class="card flex-fill">
                <div class="card-header"><h5 class="mb-0">Orders By Status</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Status</th><th class="text-end">Orders</th></tr></thead>
                            <tbody>
                                @forelse($ordersByStatus as $row)
                                    @php
                                        $statusValue = $row->status instanceof \BackedEnum ? $row->status->value : $row->status;
                                    @endphp
                                    <tr>
                                        <td>{{ ucfirst((string) $statusValue) }}</td>
                                        <td class="text-end">{{ $row->total }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center py-4">No data for selected range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex">
            <div class="card flex-fill">
                <div class="card-header"><h5 class="mb-0">Payments By Method</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Method</th><th class="text-end">Txn</th><th class="text-end">Paid</th></tr></thead>
                            <tbody>
                                @forelse($paymentsByMethod as $row)
                                    @php
                                        $methodValue = $row->method instanceof \BackedEnum ? $row->method->value : $row->method;
                                    @endphp
                                    <tr>
                                        <td>{{ strtoupper((string) $methodValue) }}</td>
                                        <td class="text-end">{{ $row->total_transactions }}</td>
                                        <td class="text-end">{{ number_format((float) $row->paid_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4">No data for selected range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex">
            <div class="card flex-fill">
                <div class="card-header"><h5 class="mb-0">Top Selling Items</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Sales</th></tr></thead>
                            <tbody>
                                @forelse($topProducts as $row)
                                    <tr>
                                        <td>{{ $row->name }}</td>
                                        <td class="text-end">{{ $row->total_qty }}</td>
                                        <td class="text-end">{{ number_format((float) $row->total_sales, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-4">No data for selected range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header"><h5 class="mb-0">Top Selling Restaurants</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Restaurant</th><th class="text-end">Sales</th></tr></thead>
                            <tbody>
                                @forelse($topRestaurants as $row)
                                    <tr>
                                        <td>{{ $row->name }}</td>
                                        <td class="text-end">{{ number_format((float) $row->total_sales, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center py-4">No data for selected range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header"><h5 class="mb-0">Top Selling Branches</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead><tr><th>Branch</th><th class="text-end">Sales</th></tr></thead>
                            <tbody>
                                @forelse($topBranches as $row)
                                    <tr>
                                        <td>{{ $row->name }}</td>
                                        <td class="text-end">{{ number_format((float) $row->total_sales, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center py-4">No data for selected range.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

