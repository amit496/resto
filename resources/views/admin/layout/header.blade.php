<div class="header">
    <div class="main-header">
        @php
            $authUser = auth()->user();
            $profileImageUrl = \App\Support\ImagePath::thumbUrl($authUser?->profile_image, 'admin/assets/img/profiles/avator1.jpg');
            $notificationAudience = $authUser && $authUser->hasAnyRole(['manager', 'staff'])
                ? ['staff', 'all']
                : ['admin', 'all'];
            $headerNotifications = \App\Models\NotificationLog::query()
                ->whereIn('audience', $notificationAudience)
                ->latest()
                ->take(5)
                ->get();
            $unreadNotifications = \App\Models\NotificationLog::query()
                ->whereIn('audience', $notificationAudience)
                ->where('is_read', false)
                ->count();
        @endphp
        <div class="header-left active">
            <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
                <img src="{{ asset('admin/assets/img/logo.svg') }}" alt="Img">
            </a>
            <a href="{{ route('admin.dashboard') }}" class="logo logo-white">
                <img src="{{ asset('admin/assets/img/logo-white.svg') }}" alt="Img">
            </a>
            <a href="{{ route('admin.dashboard') }}" class="logo-small">
                <img src="{{ asset('admin/assets/img/logo.svg') }}" alt="Img">
            </a>
            <a href="{{ route('admin.dashboard') }}" class="logo-small-white">
                <img src="{{ asset('admin/assets/img/logo-white.svg') }}" alt="Img">
            </a>
        </div>

        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <ul class="nav user-menu">
            <li class="nav-item nav-searchinputs">
                <div class="top-nav-search">
                    <a href="javascript:void(0);" class="responsive-search">
                        <i class="fa-solid fa-search"></i>
                    </a>
                    <form action="#" class="dropdown">
                        <div class="searchinputs input-group dropdown-toggle" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside">
                            <input type="text" placeholder="Search">
                            <div class="search-addon">
                                <span><i class="ti ti-search"></i></span>
                            </div>
                            <span class="input-group-text">
                                <kbd class="d-flex align-items-center">
                                    <img src="{{ asset('admin/assets/img/icons/command.svg') }}" alt="img" class="me-1">K
                                </kbd>
                            </span>
                        </div>
                    </form>
                </div>
            </li>

            <li class="nav-item dropdown has-arrow main-drop select-store-dropdown">
                <a href="javascript:void(0);" class="dropdown-toggle nav-link select-store" data-bs-toggle="dropdown">
                    <span class="user-info">
                        <span class="user-letter">
                            <img src="{{ asset('admin/assets/img/store/store-01.png') }}" alt="Store Logo" class="img-fluid">
                        </span>
                        <span class="user-detail">
                            <span class="user-name">Foodihub</span>
                        </span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="javascript:void(0);" class="dropdown-item">
                        <img src="{{ asset('admin/assets/img/store/store-01.png') }}" alt="Store Logo" class="img-fluid">Foodihub
                    </a>
                </div>
            </li>

            <li class="nav-item dropdown link-nav">
                <a href="javascript:void(0);" class="btn btn-primary btn-md d-inline-flex align-items-center"
                    data-bs-toggle="dropdown">
                    <i class="ti ti-circle-plus me-1"></i>Add New
                </a>
            </li>

            <li class="nav-item pos-nav">
                <a href="javascript:void(0);" class="btn btn-dark btn-md d-inline-flex align-items-center">
                    <i class="ti ti-device-laptop me-1"></i>POS
                </a>
            </li>

            <li class="nav-item nav-item-box"><a href="javascript:void(0);"><i class="ti ti-maximize"></i></a></li>
            <li class="nav-item nav-item-box"><a href="javascript:void(0);"><i class="ti ti-mail"></i></a></li>
            <li class="nav-item dropdown nav-item-box">
                <a href="javascript:void(0);" class="nav-link" data-bs-toggle="dropdown" style="position:relative;">
                    <i class="ti ti-bell"></i>
                    @if ($unreadNotifications > 0)
                        <span class="badge bg-danger" style="position:absolute;top:-2px;right:-2px;">{{ $unreadNotifications }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:320px;">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Web Notifications</span>
                        <a href="{{ route('admin.notifications.index') }}" class="small text-decoration-none">Manage</a>
                    </div>
                    <div class="px-3 pb-2" style="max-height:280px;overflow:auto;">
                        @forelse($headerNotifications as $notification)
                            <div class="py-2 border-bottom">
                                <div class="fw-semibold">{{ $notification->title }}</div>
                                <div class="text-muted small">{{ $notification->message }}</div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="small text-muted">{{ $notification->sent_at?->format('d M Y h:i A') }}</span>
                                    @if(! $notification->is_read)
                                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-outline-primary">Read</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-muted">No notifications yet.</div>
                        @endforelse
                    </div>
                </div>
            </li>

            <li class="nav-item dropdown has-arrow main-drop profile-nav">
                <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                    <span class="user-info p-0">
                        <span class="user-letter">
                            <img src="{{ $profileImageUrl }}" alt="Img" class="img-fluid">
                        </span>
                    </span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profileset d-flex align-items-center">
                        <span class="user-img me-2">
                            <img src="{{ $profileImageUrl }}" alt="Img">
                        </span>
                        <div>
                            <h6 class="fw-medium">{{ auth()->user()->name }}</h6>
                            <p>{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="ti ti-user-circle me-2"></i>My Profile
                    </a>
                    <hr class="my-2">
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item logout">
                            <i class="ti ti-logout me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>


