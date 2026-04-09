<div class="sidebar" id="sidebar">
    <div class="sidebar-logo active">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
            <img src="{{ asset('admin/assets/img/logo.svg') }}" alt="{{ config('app.name') }}">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo logo-white">
            <img src="{{ asset('admin/assets/img/logo-white.svg') }}" alt="{{ config('app.name') }}">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo-small">
            <img src="{{ asset('admin/assets/img/logo.svg') }}" alt="{{ config('app.name') }}">
        </a>
        <a href="{{ route('admin.dashboard') }}" class="logo-small-white">
            <img src="{{ asset('admin/assets/img/logo-white.svg') }}" alt="{{ config('app.name') }}">
        </a>
        <a id="toggle_btn" href="javascript:void(0);">
            <i data-feather="chevrons-left" class="feather-16"></i>
        </a>
    </div>

    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Foodihub</h6>
                    <ul>
                        <li>
                            <span class="d-block px-2 pt-2 pb-1 text-uppercase text-muted fw-semibold">Core</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="ti ti-layout-grid fs-16 me-2"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <span class="d-block px-2 pt-2 pb-1 text-uppercase text-muted fw-semibold">Operations</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.restaurants.index') }}"
                                class="{{ request()->routeIs('admin.restaurants.*') ? 'active' : '' }}">
                                <i class="ti ti-building-store fs-16 me-2"></i>
                                <span>Restaurant Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.branches.index') }}"
                                class="{{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-code-branch fs-16 me-2"></i>
                                <span>Branches</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}"
                                class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="ti ti-list-details fs-16 me-2"></i>
                                <span>Food Categories</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.index') }}"
                                class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <i class="ti ti-tools-kitchen-2 fs-16 me-2"></i>
                                <span>Food Items</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.orders.index') }}"
                                class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                <i class="ti ti-basket fs-16 me-2"></i>
                                <span>Orders</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.kitchen.index') }}"
                                class="{{ request()->routeIs('admin.kitchen.*') ? 'active' : '' }}">
                                <i class="ti ti-chef-hat fs-16 me-2"></i>
                                <span>Kitchen / KOT</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.inventory.index') }}"
                                class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                                <i class="ti ti-box fs-16 me-2"></i>
                                <span>Inventory & Stock</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reservations.index') }}"
                                class="{{ request()->routeIs('admin.reservations.*') ? 'active' : '' }}">
                                <i class="ti ti-calendar-event fs-16 me-2"></i>
                                <span>Reservations</span>
                            </a>
                        </li>

                        <li>
                            <span class="d-block px-2 pt-2 pb-1 text-uppercase text-muted fw-semibold">CRM & Delivery</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.customers.index') }}"
                                class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                                <i class="ti ti-user fs-16 me-2"></i>
                                <span>Customers</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.delivery-boys.index') }}"
                                class="{{ request()->routeIs('admin.delivery-boys.*') ? 'active' : '' }}">
                                <i class="ti ti-bike fs-16 me-2"></i>
                                <span>Delivery Boys</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.delivery-tracking.index') }}"
                                class="{{ request()->routeIs('admin.delivery-tracking.*') ? 'active' : '' }}">
                                <i class="ti ti-map-pin-bolt fs-16 me-2"></i>
                                <span>Delivery Tracking</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.coupons.index') }}"
                                class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                                <i class="ti ti-ticket fs-16 me-2"></i>
                                <span>Coupons</span>
                            </a>
                        </li>

                        <li>
                            <span class="d-block px-2 pt-2 pb-1 text-uppercase text-muted fw-semibold">Finance</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.payments.index') }}"
                                class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                <i class="ti ti-cash fs-16 me-2"></i>
                                <span>Payments</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.refunds.index') }}"
                                class="{{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}">
                                <i class="ti ti-receipt-refund fs-16 me-2"></i>
                                <span>Refund Center</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.purchases.index') }}"
                                class="{{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                                <i class="ti ti-building-warehouse fs-16 me-2"></i>
                                <span>Suppliers & Purchase</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.suppliers.index') }}"
                                class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                                <i class="ti ti-users-group fs-16 me-2"></i>
                                <span>Suppliers</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.expenses.index') }}"
                                class="{{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
                                <i class="ti ti-wallet fs-16 me-2"></i>
                                <span>Expense Manager</span>
                            </a>
                        </li>

                        <li>
                            <span class="d-block px-2 pt-2 pb-1 text-uppercase text-muted fw-semibold">Analytics</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports.index') }}"
                                class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <i class="ti ti-chart-bar fs-16 me-2"></i>
                                <span>Reports & Analytics</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reviews.index') }}"
                                class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-star-half-stroke fs-16 me-2"></i>
                                <span>Reviews</span>
                            </a>
                        </li>

                        <li>
                            <span class="d-block px-2 pt-2 pb-1 text-uppercase text-muted fw-semibold">System</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.edit') }}"
                                class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="ti ti-settings fs-16 me-2"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.edit') }}#tab-homepage-media"
                                class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="ti ti-photo fs-16 me-2"></i>
                                <span>Homepage Media</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.staff.index') }}"
                                class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                                <i class="ti ti-users fs-16 me-2"></i>
                                <span>Staff Management</span>
                            </a>
                        </li>

                        @can('users.view')
                            <li>
                                <a href="{{ route('admin.users.index') }}"
                                    class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <i class="ti ti-users fs-16 me-2"></i>
                                    <span>User Management</span>
                                </a>
                            </li>
                        @endcan

                        @can('audit-logs.view')
                            <li>
                                <a href="{{ route('admin.audit-logs.index') }}"
                                    class="{{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                                    <i class="ti ti-history fs-16 me-2"></i>
                                    <span>Audit Logs</span>
                                </a>
                            </li>
                        @endcan

                        <li>
                            <span class="d-block px-2 pt-2 pb-1 text-uppercase text-muted fw-semibold">Advanced</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.notifications.index') }}"
                                class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                                <i class="ti ti-bell fs-16 me-2"></i>
                                <span>Notification Center</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.emails.index') }}"
                                class="{{ request()->routeIs('admin.emails.*') ? 'active' : '' }}">
                                <i class="ti ti-mail fs-16 me-2"></i>
                                <span>Email Center</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.loyalty.index') }}"
                                class="{{ request()->routeIs('admin.loyalty.*') ? 'active' : '' }}">
                                <i class="ti ti-gift fs-16 me-2"></i>
                                <span>Loyalty Program</span>
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>

