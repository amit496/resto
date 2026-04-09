@extends('admin.layout.index')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="mb-1">Admin Dashboard</h3>
            <p class="text-muted mb-0">Branch performance, staff mix, notifications, and audit activity in one place.</p>
        </div>
        <a href="{{ route('admin.overview') }}" class="btn btn-outline-primary">
            <i class="ti ti-layout-grid me-1"></i> Backend Overview
        </a>
    </div>

    <form class="card card-body mb-4" method="GET">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Restaurant</label>
                <select class="form-control js-filter-select" name="restaurant_id">
                    <option value="">All Restaurants</option>
                    @foreach ($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}" @selected($selectedRestaurant === $restaurant->id)>{{ $restaurant->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label">Branch</label>
                <select class="form-control js-filter-select" name="branch_id">
                    <option value="">All Branches</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($selectedBranch === $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Apply</button>
            </div>
        </div>
    </form>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="text-muted mb-2">Total Users</p>
                    <h3 class="mb-0">{{ number_format($userCount) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="text-muted mb-2">Customers</p>
                    <h3 class="mb-0">{{ number_format($customerCount) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="text-muted mb-2">Orders</p>
                    <h3 class="mb-0">{{ number_format($orderCount) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="text-muted mb-2">Revenue</p>
                    <h3 class="mb-0">₹{{ number_format($revenueTotal, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="text-muted mb-2">Branches</p>
                    <h3 class="mb-0">{{ number_format($branchCount) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="text-muted mb-2">Admins</p>
                    <h3 class="mb-0">{{ number_format($adminCount) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="text-muted mb-2">Roles</p>
                    <h3 class="mb-0">{{ number_format($roleCount) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <p class="text-muted mb-2">Audit Logs</p>
                    <h3 class="mb-0">{{ number_format($auditCount) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Monthly Orders & Revenue</h5>
                    <span class="text-muted">Last 12 months</span>
                </div>
                <div class="card-body">
                    <canvas id="ordersRevenueChart" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Weekly Orders</h5>
                    <span class="text-muted">Last 7 days</span>
                </div>
                <div class="card-body">
                    <canvas id="weeklyOrdersChart" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Branch Performance</h5>
                    <span class="text-muted">Revenue & ratings</span>
                </div>
                <div class="card-body">
                    <canvas id="branchRevenueChart" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5 d-flex">
            <div class="card flex-fill">
                <div class="card-header">
                    <h5 class="card-title mb-0">Best Branch Snapshot</h5>
                </div>
                <div class="card-body">
                    @if ($bestBranch)
                        <h4 class="mb-2">{{ $bestBranch['branch']->name }}</h4>
                        <p class="text-muted mb-3">Top rating and revenue combo across selected filters.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <div>
                                <div class="text-muted">Avg Rating</div>
                                <div class="fw-bold">{{ number_format($bestBranch['avg_rating'], 1) }} / 5</div>
                            </div>
                            <div>
                                <div class="text-muted">Monthly Revenue</div>
                                <div class="fw-bold">₹{{ number_format($bestBranch['month_revenue'], 2) }}</div>
                            </div>
                            <div>
                                <div class="text-muted">Year Orders</div>
                                <div class="fw-bold">{{ number_format($bestBranch['year_orders']) }}</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-muted mb-2">Staff mix</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($bestBranch['staff'] as $label => $count)
                                    <span class="badge bg-light text-dark">{{ $label }}: {{ $count }}</span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">No branch data available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Branch Operations Overview</h5>
            <span class="text-muted">Monthly, yearly & staff details</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                            <th>Avg Rating</th>
                            <th>Monthly</th>
                            <th>Yearly</th>
                            <th>Staff Mix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branchMetrics as $metric)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $metric['branch']->name }}</div>
                                    <div class="text-muted small">{{ $metric['branch']->address }}</div>
                                </td>
                                <td>{{ number_format($metric['orders']) }}</td>
                                <td>₹{{ number_format($metric['revenue'], 2) }}</td>
                                <td>
                                    <div class="fw-semibold">{{ number_format($metric['avg_rating'], 1) }}/5</div>
                                    <div class="text-muted small">{{ number_format($metric['reviews']) }} reviews</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">₹{{ number_format($metric['month_revenue'], 2) }}</div>
                                    <div class="text-muted small">{{ number_format($metric['month_orders']) }} orders</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">₹{{ number_format($metric['year_revenue'], 2) }}</div>
                                    <div class="text-muted small">{{ number_format($metric['year_orders']) }} orders</div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($metric['staff'] as $label => $count)
                                            <span class="badge bg-light text-dark">{{ $label }} {{ $count }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No branch performance data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Latest Web Notifications</h5>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                </div>
                <div class="card-body">
                    @forelse ($latestNotifications as $notification)
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="fw-semibold">{{ $notification->title }}</div>
                                <div class="text-muted small">{{ $notification->message }}</div>
                                <div class="text-muted small">{{ $notification->sent_at?->format('d M Y h:i A') }}</div>
                            </div>
                            <span class="badge bg-light text-dark">{{ $notification->channel }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No notifications logged.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-flex">
            <div class="card flex-fill">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Latest Audit Activity</h5>
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestLogs as $log)
                                    <tr>
                                        <td>{{ $log->user?->name ?? 'System' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $log->action }}</span></td>
                                        <td>{{ $log->created_at?->format('d M Y h:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No audit activity found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('admin/assets/plugins/chartjs/chart.min.js') }}"></script>
    <script>
        (function () {
            const monthlyLabels = @json($monthlyLabels);
            const monthlyOrders = @json($monthlyOrders);
            const monthlyRevenue = @json($monthlyRevenue);
            const dailyLabels = @json($dailyLabels);
            const dailyOrders = @json($dailyOrders);
            const branchLabels = @json($branchMetrics->pluck('branch.name'));
            const branchRevenue = @json($branchMetrics->pluck('revenue'));
            const branchRatings = @json($branchMetrics->pluck('avg_rating'));

            const revenueChart = document.getElementById('ordersRevenueChart');
            if (revenueChart) {
                new Chart(revenueChart, {
                    type: 'line',
                    data: {
                        labels: monthlyLabels,
                        datasets: [
                            {
                                label: 'Orders',
                                data: monthlyOrders,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13,110,253,0.15)',
                                tension: 0.35,
                                yAxisID: 'y',
                            },
                            {
                                label: 'Revenue (₹)',
                                data: monthlyRevenue,
                                borderColor: '#f9185a',
                                backgroundColor: 'rgba(249,24,90,0.15)',
                                tension: 0.35,
                                yAxisID: 'y1',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: { display: false }
                            }
                        }
                    }
                });
            }

            const weeklyChart = document.getElementById('weeklyOrdersChart');
            if (weeklyChart) {
                new Chart(weeklyChart, {
                    type: 'bar',
                    data: {
                        labels: dailyLabels,
                        datasets: [
                            {
                                label: 'Orders',
                                data: dailyOrders,
                                backgroundColor: '#0d6efd',
                                borderRadius: 8,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            const branchChart = document.getElementById('branchRevenueChart');
            if (branchChart) {
                new Chart(branchChart, {
                    type: 'bar',
                    data: {
                        labels: branchLabels,
                        datasets: [
                            {
                                label: 'Revenue (₹)',
                                data: branchRevenue,
                                backgroundColor: '#198754',
                                borderRadius: 10,
                            },
                            {
                                label: 'Avg Rating',
                                data: branchRatings,
                                backgroundColor: 'rgba(249,24,90,0.4)',
                                borderRadius: 10,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }
        })();
    </script>
@endpush
