<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ff8c00">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ \Illuminate\Support\Str::limit($setting?->app_name ?? config('app.name', 'Foodhub'), 40) }}">
    <title>@yield('title', config('app.name', 'FoodiHub'))</title>
    <link rel="manifest" href="{{ route('frontend.manifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/pwa-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|outfit:500,600,700,800|manrope:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/app.css') }}">
    @yield('styles')
</head>
<body class="figma-store">
    @php
        $customer = auth('customer')->user();
        $appName = $setting?->app_name ?? config('app.name', 'FoodiHub');
        $brandBase = rtrim($appName, "+ \t\n\r\0\x0B");
        $homeMeta = is_array($setting?->meta) ? $setting->meta : [];
        $promoPopup = is_array($homeMeta['home_promo_popup'] ?? null) ? $homeMeta['home_promo_popup'] : [];
        $promoActive = filter_var($promoPopup['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $promoHasContent = trim((string) ($promoPopup['title'] ?? '')) !== '' || trim((string) ($promoPopup['body'] ?? '')) !== '';
        $promoShow = $promoActive && $promoHasContent;
        $promoDelay = max(0, min(600, (int) ($promoPopup['delay_seconds'] ?? 2)));
        $footerApps = is_array($homeMeta['footer_app_links'] ?? null) ? $homeMeta['footer_app_links'] : [];
        $iosAppUrl = trim((string) ($footerApps['ios_url'] ?? ''));
        $androidAppUrl = trim((string) ($footerApps['android_url'] ?? ''));
    @endphp

    <div class="site-shell">
        <header class="site-header">
            <div class="container nav-row nav-row--orderplus">
                <div class="nav-actions">
                    <a href="{{ route('frontend.home') }}" class="brand brand--figma" aria-label="{{ $brandBase }} home">
                        <span class="brand-text">{{ $brandBase }}</span>
                    </a>
                </div>

                <div class="header-search">
                    <form action="{{ route('frontend.menu.index') }}" method="GET" role="search">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search for food, grocery..." autocomplete="off" aria-label="Search menu">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <nav class="nav-links nav-links--desktop" aria-label="Primary">
                    <a href="{{ route('frontend.home') }}" class="nav-link {{ request()->routeIs('frontend.home') ? 'is-active' : '' }}">Home</a>
                    <a href="{{ route('frontend.menu.index') }}" class="nav-link {{ request()->routeIs('frontend.menu.*') ? 'is-active' : '' }}">Menu</a>
                    <a href="{{ route('frontend.offers') }}" class="nav-link {{ request()->routeIs('frontend.offers') ? 'is-active' : '' }}">Offer</a>
                    <a href="{{ route('frontend.reservations.create') }}" class="nav-link {{ request()->routeIs('frontend.reservations.*') ? 'is-active' : '' }}">Service</a>
                    <a href="{{ route('frontend.about') }}" class="nav-link {{ request()->routeIs('frontend.about') ? 'is-active' : '' }}">About</a>
                </nav>

                <div class="nav-actions nav-actions--desktop-wide">
                    <a href="{{ route('frontend.cart.index') }}" class="site-action">
                        <span class="nav-action-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M6 6h15l-2 7H8L6 6Z"></path>
                                <circle cx="9" cy="19" r="1.5"></circle>
                                <circle cx="17" cy="19" r="1.5"></circle>
                                <path d="M6 6 5 3H2"></path>
                            </svg>
                        </span>
                        <span>Cart</span>
                        <span class="nav-count">{{ $cartSummary['count'] ?? 0 }}</span>
                    </a>
                    @if ($customer)
                        <a href="{{ route('frontend.orders.index') }}" class="site-action">
                            <span class="nav-action-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <path d="M6 4h12v16H6z"></path>
                                    <path d="M9 8h6M9 12h6M9 16h4"></path>
                                </svg>
                            </span>
                            <span>Orders</span>
                        </a>
                        <a href="{{ route('frontend.account.profile') }}" class="site-action">
                            <span class="nav-action-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="8" r="4"></circle>
                                    <path d="M4 20c1.8-3.2 4.8-5 8-5s6.2 1.8 8 5"></path>
                                </svg>
                            </span>
                            <span>{{ \Illuminate\Support\Str::limit($customer->name, 16) }}</span>
                        </a>
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit" class="site-action">
                                <span class="nav-action-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M10 17l5-5-5-5"></path>
                                        <path d="M15 12H3"></path>
                                        <path d="M21 4v16"></path>
                                    </svg>
                                </span>
                                <span>Logout</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('customer.login') }}" class="site-action site-action--outline-header">
                            <span>Login</span>
                        </a>
                        <a href="{{ route('customer.register') }}" class="btn-header-signup">Sign Up</a>
                    @endif
                </div>

                <div class="nav-actions nav-actions--mobile-only">
                    <a href="{{ route('frontend.cart.index') }}" class="site-action" aria-label="Cart">
                        <span class="nav-action-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M6 6h15l-2 7H8L6 6Z"></path>
                                <circle cx="9" cy="19" r="1.5"></circle>
                                <circle cx="17" cy="19" r="1.5"></circle>
                                <path d="M6 6 5 3H2"></path>
                            </svg>
                        </span>
                        <span class="nav-count">{{ $cartSummary['count'] ?? 0 }}</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="page-main">
            <div class="container">
                <div class="flash-stack">
                    @if (session('success'))
                        <div class="flash success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="flash error">{{ session('error') }}</div>
                    @endif
                </div>
            </div>

            @yield('content')
        </main>

        <footer class="site-footer">
            <div class="footer-shell">
                <div class="footer-cta">
                    <div class="container footer-cta-inner">
                        <div>
                            <h2 class="footer-cta-title">Stay close to fresh offers, branch updates, and new menu launches.</h2>
                            <p class="footer-cta-copy">A cleaner footer with a direct branch link, softer spacing, and the key restaurant details people actually need.</p>
                        </div>
                        <div class="footer-cta-actions">
                            @guest('customer')
                                <a href="{{ route('customer.register') }}" class="btn figma-footer-signup">Sign Up</a>
                            @endguest
                            <a href="{{ route('frontend.offers') }}" class="btn-outline figma-footer-secondary">View Offers</a>
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <div class="container footer-inner">
                        <div class="footer-grid">
                            <div class="footer-brand">
                                <div class="footer-brand-top">
                                    <span class="brand-text brand-text--footer">{{ $brandBase }}</span>
                                </div>
                                <p class="footer-copy">Fast food delivery style browsing, offers, branches, and checkout—content for sliders, banners, and blocks is managed from the admin panel.</p>
                                @if($iosAppUrl !== '' || $androidAppUrl !== '')
                                    <div class="footer-app-badges">
                                        @if($iosAppUrl !== '')
                                            <a href="{{ $iosAppUrl }}" rel="noopener noreferrer" target="_blank">App Store</a>
                                        @endif
                                        @if($androidAppUrl !== '')
                                            <a href="{{ $androidAppUrl }}" rel="noopener noreferrer" target="_blank">Google Play</a>
                                        @endif
                                    </div>
                                @endif
                                <div class="footer-contact">
                                    <span>{{ $restaurant?->branches_count ? $restaurant->branches_count.' active branches' : 'Branch-first restaurant experience' }}</span>
                                    <span>{{ $setting?->support_phone ?: '+91 90000 00000' }}</span>
                                </div>
                            </div>

                            <div>
                                <h3 class="footer-heading">Company</h3>
                                <div class="footer-links">
                                    <a href="{{ route('frontend.about') }}">About Us</a>
                                    <a href="{{ route('frontend.contact') }}">Contact</a>
                                    <a href="{{ route('frontend.branches.index') }}">Branches</a>
                                    <a href="{{ route('frontend.menu.index') }}">Menu</a>
                                </div>
                            </div>

                            <div>
                                <h3 class="footer-heading">Explore</h3>
                                <div class="footer-links">
                                    <a href="{{ route('frontend.home') }}">Home</a>
                                    <a href="{{ route('frontend.offers') }}">Offers</a>
                                    <a href="{{ route('frontend.reviews') }}">Reviews</a>
                                    <a href="{{ route('frontend.reservations.create') }}">Service</a>
                                </div>
                            </div>

                            <div>
                                <h3 class="footer-heading">Help</h3>
                                <div class="footer-links">
                                    <a href="{{ route('frontend.contact') }}">Help Center</a>
                                    <a href="{{ route('frontend.contact') }}">Support</a>
                                    <a href="{{ route('frontend.cart.index') }}">Your Cart</a>
                                    <a href="{{ route('admin.login') }}">Admin</a>
                                </div>
                            </div>

                            <div>
                                <h3 class="footer-heading">Contact</h3>
                                <div class="footer-contact">
                                    <span>{{ $setting?->support_phone ?: '+91 90000 00000' }}</span>
                                    <span>{{ $setting?->support_email ?: 'support@foodihub.com' }}</span>
                                    <span>{{ $setting?->timezone ?: 'Asia/Calcutta' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="footer-bottom">
                            <div>&copy; {{ now()->year }} {{ $appName }}. All rights reserved.</div>
                            <div class="footer-bottom-links">
                                <a href="{{ route('frontend.menu.index') }}">Browse Menu</a>
                                <a href="{{ route('frontend.branches.index') }}">Branches</a>
                                <a href="{{ route('frontend.branches.index') }}">Find Branch</a>
                                <a href="{{ route('frontend.contact') }}">Help</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <nav class="mobile-bottom-nav" aria-label="Mobile">
            <div class="mobile-bottom-nav-inner">
                <a href="{{ route('frontend.home') }}" class="mobile-nav-item {{ request()->routeIs('frontend.home') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"></path></svg>
                    Home
                </a>
                <a href="{{ route('frontend.menu.index') }}" class="mobile-nav-item {{ request()->routeIs('frontend.menu.*') ? 'is-active' : '' }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg>
                    Browse
                </a>
                <a href="{{ route('frontend.cart.index') }}" class="mobile-nav-item {{ request()->routeIs('frontend.cart.*') ? 'is-active' : '' }}">
                    @if(($cartSummary['count'] ?? 0) > 0)
                        <span class="mobile-nav-badge">{{ $cartSummary['count'] }}</span>
                    @endif
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6h15l-2 7H8L6 6Z"></path><circle cx="9" cy="19" r="1.5"></circle><circle cx="17" cy="19" r="1.5"></circle><path d="M6 6 5 3H2"></path></svg>
                    Cart
                </a>
                @if ($customer)
                    <a href="{{ route('frontend.orders.index') }}" class="mobile-nav-item {{ request()->routeIs('frontend.orders.*') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v16H6z"></path><path d="M9 8h6M9 12h6M9 16h4"></path></svg>
                        Orders
                    </a>
                    <a href="{{ route('frontend.account.profile') }}" class="mobile-nav-item {{ request()->routeIs('frontend.account.*') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M4 20c1.8-3.2 4.8-5 8-5s6.2 1.8 8 5"></path></svg>
                        Profile
                    </a>
                @else
                    <a href="{{ route('customer.login') }}" class="mobile-nav-item {{ request()->routeIs('customer.login') ? 'is-active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M4 20c1.8-3.2 4.8-5 8-5s6.2 1.8 8 5"></path></svg>
                        Login
                    </a>
                @endif
            </div>
        </nav>
    </div>

    @if($promoShow)
        <div id="site-promo-modal" class="promo-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="site-promo-title" hidden>
            <div class="promo-modal">
                <h3 id="site-promo-title">{{ $promoPopup['title'] }}</h3>
                @if(!empty($promoPopup['body']))
                    <p>{{ $promoPopup['body'] }}</p>
                @endif
                <div class="promo-modal-actions">
                    <button type="button" class="btn-outline" data-promo-dismiss>Close</button>
                    @if(!empty($promoPopup['button_label']) && !empty($promoPopup['button_url']))
                        <a href="{{ $promoPopup['button_url'] }}" class="btn">{{ $promoPopup['button_label'] }}</a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <script>
        (function () {
            const root = document.documentElement;
            const storageKey = 'foodi-theme';
            const themeToggleIcon = () => {
                return root.getAttribute('data-theme') === 'dark'
                    ? '<svg viewBox="0 0 24 24"><path d="M21 12.5A8.5 8.5 0 1 1 11.5 3a6.5 6.5 0 0 0 9.5 9.5Z"></path></svg>'
                    : '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path></svg>';
            };
            const applyTheme = (theme) => {
                root.setAttribute('data-theme', theme);
                document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                    const icon = button.querySelector('[data-theme-toggle-icon]');
                    const label = button.querySelector('[data-theme-toggle-label]');
                    if (icon) {
                        icon.innerHTML = themeToggleIcon();
                    }
                    if (label) {
                        label.textContent = theme === 'dark' ? 'Light' : 'Dark';
                    }
                });
            };

            const savedTheme = localStorage.getItem(storageKey);
            applyTheme(savedTheme === 'dark' ? 'dark' : 'light');

            document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const nextTheme = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    localStorage.setItem(storageKey, nextTheme);
                    applyTheme(nextTheme);
                });
            });
        })();
    </script>
    @if($promoShow)
        <script>
            (function () {
                const el = document.getElementById('site-promo-modal');
                if (!el) {
                    return;
                }
                const storageKey = 'orderplus-promo-dismissed';
                const id = {{ json_encode(md5(($promoPopup['title'] ?? '').'|'.($promoPopup['body'] ?? ''))) }};
                const delay = {{ (int) $promoDelay }} * 1000;
                const open = () => {
                    el.hidden = false;
                    requestAnimationFrame(() => el.classList.add('is-open'));
                };
                const close = () => {
                    el.classList.remove('is-open');
                    setTimeout(() => {
                        el.hidden = true;
                    }, 260);
                };
                const dismissed = sessionStorage.getItem(storageKey);
                if (dismissed === id) {
                    return;
                }
                setTimeout(open, delay);
                el.addEventListener('click', (e) => {
                    if (e.target === el) {
                        sessionStorage.setItem(storageKey, id);
                        close();
                    }
                });
                el.querySelectorAll('[data-promo-dismiss]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        sessionStorage.setItem(storageKey, id);
                        close();
                    });
                });
            })();
        </script>
    @endif
    <script>
        (function () {
            if (!('serviceWorker' in navigator)) {
                return;
            }
            const swUrl = @json(asset('sw.js'));
            window.addEventListener('load', function () {
                navigator.serviceWorker.register(swUrl).catch(function () {});
            });
        })();
    </script>
    @yield('scripts')
</body>
</html>
