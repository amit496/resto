@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Menu')

@section('styles')
    <style>
        .menu-shell {
            display: grid;
            gap: 26px;
        }
        .full-bleed {
            width: 100%;
            max-width: 100%;
            padding-left: var(--page-gutter);
            padding-right: var(--page-gutter);
        }
        .menu-stage {
            display: grid;
            gap: 24px;
            padding-top: 8px;
        }
        .menu-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(340px, 0.7fr);
            gap: 22px;
        }
        .menu-banner,
        .filter-box,
        .category-slider-wrap,
        .results-bar {
            border-radius: 32px;
            box-shadow: var(--shadow);
        }
        .menu-banner,
        .filter-box,
        .category-slider-wrap,
        .subcat-rail-card,
        .filter-stat,
        .results-bar {
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(255,255,255,0.76);
        }
        [data-theme="dark"] .menu-banner,
        [data-theme="dark"] .filter-box,
        [data-theme="dark"] .category-slider-wrap,
        [data-theme="dark"] .subcat-rail-card,
        [data-theme="dark"] .filter-stat,
        [data-theme="dark"] .results-bar {
            background: rgba(24, 20, 18, 0.92);
            border-color: var(--line);
        }
        .menu-banner {
            min-height: 500px;
            padding: 28px;
            color: #fff;
            background:
                linear-gradient(118deg, rgba(17, 17, 17, 0.96), rgba(226, 55, 68, 0.56)),
                var(--hero-image, linear-gradient(135deg, #111111, #e23744));
            background-size: cover;
            background-position: center;
            align-content: space-between;
        }
        .hero-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .menu-banner-copy {
            max-width: min(56rem, 100%);
            color: rgba(255,255,255,0.82);
            font-size: 15px;
            line-height: 1.7;
        }
        .hero-metrics {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .hero-metric {
            min-width: 120px;
            padding: 14px 16px;
            border-radius: 20px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.14);
        }
        .hero-metric strong {
            display: block;
            font-size: 24px;
            font-family: 'Outfit', sans-serif;
        }
        .hero-metric span {
            font-size: 12px;
            color: rgba(255,255,255,0.76);
        }
        .hero-search {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 180px auto;
            gap: 12px;
            padding: 12px;
            border-radius: 26px;
            background: rgba(255,255,255,0.92);
        }
        .hero-search .input {
            border: 0;
            background: transparent;
            padding: 10px 12px;
        }
        .hero-search .select {
            background: rgba(17, 10, 7, 0.04);
        }
        .hero-strip {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .hero-strip-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.12);
            font-size: 13px;
            font-weight: 700;
        }
        .category-slider-wrap {
            display: grid;
            gap: 14px;
            padding: 22px;
            background: linear-gradient(180deg, rgba(255,255,255,0.68), rgba(255,255,255,0.24));
        }
        [data-theme="dark"] .category-slider-wrap {
            background: linear-gradient(180deg, rgba(25,20,17,0.92), rgba(16,13,11,0.88));
        }
        .category-slider {
            display: flex;
            gap: 18px;
            overflow-x: auto;
            padding-bottom: 6px;
        }
        .category-slider-card {
            min-width: 140px;
            display: grid;
            gap: 10px;
            justify-items: center;
            padding: 14px 12px;
            border-radius: 26px;
            background: rgba(255,255,255,0.94);
            border: 1px solid var(--line);
            text-align: center;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }
        [data-theme="dark"] .category-slider-card {
            background: rgba(31, 25, 22, 0.94);
        }
        .category-slider-card:hover,
        .category-slider-card.is-active {
            transform: translateY(-4px);
            border-color: rgba(239, 90, 41, 0.36);
            box-shadow: 0 18px 36px rgba(118, 63, 30, 0.12);
        }
        .category-slider-image {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            padding: 5px;
            background: linear-gradient(135deg, rgba(239, 90, 41, 0.18), rgba(255, 210, 186, 0.94));
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.85);
        }
        .category-slider-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .menu-page-grid {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 22px;
            align-items: start;
            --menu-sidebar-sticky-top: 88px;
        }
        /* Prevent grid row stretch: without this, sticky sidebar "jumps" when the results column grows (infinite scroll). */
        .filter-shell {
            position: sticky;
            top: var(--menu-sidebar-sticky-top);
            align-self: start;
            width: 100%;
            max-height: none;
        }
        .filter-box {
            display: flex;
            flex-direction: column;
            gap: 14px;
            padding: 26px;
            max-height: calc(100vh - var(--menu-sidebar-sticky-top) - 24px);
            overflow: hidden;
            min-height: 0;
        }
        .filter-sidebar-head {
            flex-shrink: 0;
        }
        .filter-subcat-scroll {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-right: 2px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .filter-subcat-scroll::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }
        .filter-subcat-scroll:focus {
            outline: none;
        }
        .filter-subcat-scroll:focus-visible {
            outline: 2px solid rgba(255, 140, 0, 0.45);
            outline-offset: 2px;
        }
        .filter-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }
        .filter-stat {
            padding: 14px;
            border-radius: 18px;
        }
        .filter-stat strong {
            display: block;
            font-size: 24px;
            font-family: 'Outfit', sans-serif;
        }
        .filter-stat span {
            font-size: 12px;
            color: var(--muted);
        }
        .filter-group {
            display: grid;
            gap: 12px;
        }
        .filter-group h3 {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            letter-spacing: -.04em;
        }
        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.96);
            border: 1px solid var(--line);
            font-size: 13px;
            font-weight: 700;
        }
        [data-theme="dark"] .filter-chip {
            background: rgba(255,255,255,0.03);
        }
        .filter-chip input {
            accent-color: var(--accent);
        }
        .filter-row,
        .active-filter-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .results-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .results-bar {
            display: grid;
            gap: 18px;
            padding: 22px;
            margin-bottom: 20px;
            background: rgba(255,255,255,0.78);
        }
        .active-filter {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
        }
        .results-grid .home-card-title {
            font-size: 24px;
        }
        .results-grid .home-card-copy {
            font-size: 13px;
            line-height: 1.6;
        }
        .subcat-rail {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 6px;
            margin-bottom: 18px;
        }
        .subcat-rail-card {
            min-width: 220px;
            display: grid;
            grid-template-columns: 72px 1fr;
            gap: 12px;
            align-items: center;
            padding: 12px;
            border-radius: 22px;
        }
        .subcat-rail-card img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 18px;
        }
        @media (max-width: 1180px) {
            .menu-hero,
            .menu-page-grid {
                grid-template-columns: 1fr;
            }
            .filter-shell {
                position: static;
                align-self: stretch;
                max-width: none;
            }
            .filter-box {
                max-height: none;
                overflow: visible;
            }
            .filter-subcat-scroll {
                overflow-y: visible;
                min-height: 0;
                max-height: none;
                padding-right: 0;
            }
            .filter-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            .filter-stat strong {
                font-size: 20px;
            }
        }
        @media (max-width: 760px) {
            .menu-banner,
            .filter-box,
            .category-slider-wrap,
            .results-bar {
                padding: 16px;
                border-radius: 24px;
            }
            .menu-banner {
                min-height: 360px;
                padding: 18px;
            }
            .hero-metrics {
                display: none;
            }
            .menu-banner-copy {
                font-size: 13px;
            }
            .hero-search,
            .filter-grid {
                grid-template-columns: 1fr;
            }
            .filter-stats {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 8px;
            }
            .filter-stat {
                padding: 10px;
                border-radius: 16px;
            }
            .filter-stat strong {
                font-size: 18px;
            }
            .filter-group h3 {
                font-size: 18px;
            }
            .filter-chip {
                padding: 8px 10px;
                font-size: 12px;
            }
            .category-slider-card {
                min-width: 128px;
                border-radius: 22px;
            }
            .category-slider-image {
                width: 82px;
                height: 82px;
            }
            .subcat-rail-card {
                min-width: 200px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $heroCategory = $categories->firstWhere('id', $selectedCategoryIds[0] ?? null) ?? $categories->first();
        $heroImage = $heroCategory
            ? \App\Support\ImagePath::thumbUrl($heroCategory->image, \App\Support\FoodImageResolver::category($heroCategory->name))
            : \App\Support\FoodImageResolver::category('Pizza');
        $menuBaseUrl = $currentSubcategory && $currentCategory
            ? \App\Support\MenuUrl::subcategory($currentCategory, $currentSubcategory)
            : ($currentCategory ? \App\Support\MenuUrl::category($currentCategory) : route('frontend.menu.index'));
        $visibleSubcategories = $subcategories
            ->when($selectedCategoryIds !== [], fn ($collection) => $collection->whereIn('category_id', $selectedCategoryIds))
            ->values();
        $searchInLabels = [
            'all' => 'All fields',
            'name' => 'Item name',
            'description' => 'Description',
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'type' => 'Food type',
        ];
    @endphp

    <div class="menu-shell">
        <section class="full-bleed">
            <div class="container menu-stage">
                <div class="menu-hero">
                    <div class="menu-banner" style="--hero-image:url('{{ $heroImage }}');">
                        <div class="hero-topline">
                            <div class="eyebrow" style="background:rgba(255,255,255,0.12);color:#fff;">Menu Discovery</div>
                            <div class="hero-metrics">
                                <div class="hero-metric">
                                    <strong>{{ $products->total() }}</strong>
                                    <span>Live items</span>
                                </div>
                                <div class="hero-metric">
                                    <strong>{{ $categories->count() }}</strong>
                                    <span>Categories</span>
                                </div>
                                <div class="hero-metric">
                                    <strong>{{ $subcategories->count() }}</strong>
                                    <span>Subcategories</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h1 class="section-title" style="font-size:clamp(40px, 5vw, 62px);margin:14px 0 10px;">What’s on your mind?</h1>
                            <p class="menu-banner-copy">Search dishes, filter by category, and add items in seconds.</p>
                        </div>

                        <div>
                            <form method="GET" action="{{ $menuBaseUrl }}" class="hero-search js-clean-query-form" style="margin-top:18px;max-width:min(100%,1080px);">
                                <input class="input" type="text" name="q" value="{{ request('q') }}" placeholder="Search biryani, pizza, noodles, cold coffee">
                                <select class="select" name="search_in">
                                    @foreach($searchInLabels as $value => $label)
                                        <option value="{{ $value }}" @selected(($selectedSearchIn ?? 'all') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @foreach($selectedTypes as $type)
                                    <input type="hidden" name="types[]" value="{{ $type }}">
                                @endforeach
                                @if($selectedSort !== 'latest')
                                    <input type="hidden" name="sort" value="{{ $selectedSort }}">
                                @endif
                                @if(($selectedMinPrice ?? null) !== null)
                                    <input type="hidden" name="min_price" value="{{ $selectedMinPrice }}">
                                @endif
                                @if(($selectedMaxPrice ?? null) !== null)
                                    <input type="hidden" name="max_price" value="{{ $selectedMaxPrice }}">
                                @endif
                                <button class="btn" type="submit">Search</button>
                            </form>

                            <div class="hero-strip">
                                <span class="hero-strip-pill">Fast checkout</span>
                                <span class="hero-strip-pill">Popular dishes</span>
                                <span class="hero-strip-pill">Live offers</span>
                            </div>
                        </div>
                    </div>

                    <form method="GET" action="{{ $menuBaseUrl }}" class="filter-box js-clean-query-form">
                        <div class="results-head" style="margin-bottom:0;">
                            <div>
                                <div class="eyebrow">Refine results</div>
                                <h2 class="section-title" style="font-size:34px;margin-top:12px;">Filter menu</h2>
                            </div>
                            <a href="{{ route('frontend.menu.index') }}" class="btn-outline">Reset</a>
                        </div>

                        <div class="filter-stats">
                            <div class="filter-stat">
                                <strong>{{ $products->total() }}</strong>
                                <span>Total items</span>
                            </div>
                            <div class="filter-stat">
                                <strong>{{ count($selectedTypes) }}</strong>
                                <span>Type filters</span>
                            </div>
                            <div class="filter-stat">
                                <strong>{{ count($selectedCategoryIds) + count($selectedSubcategoryIds) }}</strong>
                                <span>Category filters</span>
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="q-filter" style="font-weight:700;">Search keyword</label>
                            <input id="q-filter" class="input" type="text" name="q" value="{{ request('q') }}" placeholder="Search product or category">
                        </div>

                        <div class="filter-grid">
                            <div class="filter-group">
                                <label for="search-in" style="font-weight:700;">Search in</label>
                                <select id="search-in" class="select" name="search_in">
                                    @foreach($searchInLabels as $value => $label)
                                        <option value="{{ $value }}" @selected(($selectedSearchIn ?? 'all') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="sort" style="font-weight:700;">Sort By</label>
                                <select id="sort" class="select" name="sort">
                                    <option value="latest" @selected($selectedSort === 'latest')>Latest</option>
                                    <option value="popular" @selected($selectedSort === 'popular')>Trending</option>
                                    <option value="name" @selected($selectedSort === 'name')>Name</option>
                                    <option value="price_low" @selected($selectedSort === 'price_low')>Price Low to High</option>
                                    <option value="price_high" @selected($selectedSort === 'price_high')>Price High to Low</option>
                                </select>
                            </div>
                        </div>

                        <div class="filter-grid">
                            <div class="filter-group">
                                <label for="min-price" style="font-weight:700;">Min price</label>
                                <input id="min-price" class="input" type="number" min="0" step="1" name="min_price" value="{{ $selectedMinPrice }}">
                            </div>
                            <div class="filter-group">
                                <label for="max-price" style="font-weight:700;">Max price</label>
                                <input id="max-price" class="input" type="number" min="0" step="1" name="max_price" value="{{ $selectedMaxPrice }}">
                            </div>
                        </div>

                        @if($currentCategory || $currentSubcategory)
                            <div class="filter-group">
                                <h3>Browse Categories</h3>
                                <div class="filter-row">
                                    @foreach($categories as $category)
                                        <a href="{{ \App\Support\MenuUrl::category($category) }}" class="filter-chip">{{ $category->name }}</a>
                                    @endforeach
                                </div>
                            </div>

                            @if($visibleSubcategories->isNotEmpty())
                                <div class="filter-group">
                                    <h3>Browse Subcategories</h3>
                                    <div class="filter-row">
                                        @foreach($visibleSubcategories as $subcategory)
                                            <a href="{{ $subcategory->category ? \App\Support\MenuUrl::subcategory($subcategory->category, $subcategory) : route('frontend.menu.index') }}" class="filter-chip">{{ $subcategory->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="filter-group">
                                <h3>Categories</h3>
                                <div class="filter-row">
                                    @foreach($categories as $category)
                                        <label class="filter-chip">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, $selectedCategoryIds, true))>
                                            {{ $category->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="filter-group">
                                <h3>Subcategories</h3>
                                <div class="filter-row">
                                    @foreach($subcategories as $subcategory)
                                        <label class="filter-chip">
                                            <input type="checkbox" name="subcategories[]" value="{{ $subcategory->id }}" @checked(in_array($subcategory->id, $selectedSubcategoryIds, true))>
                                            {{ $subcategory->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="filter-group">
                            <h3>Types</h3>
                            <div class="filter-row">
                                @foreach(['veg', 'non_veg', 'egg', 'beverage'] as $type)
                                    <label class="filter-chip">
                                        <input type="checkbox" name="types[]" value="{{ $type }}" @checked(in_array($type, $selectedTypes, true))>
                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button class="btn" type="submit">Apply filters</button>
                    </form>
                </div>

                <div class="category-slider-wrap">
                    <div class="results-head" style="margin-bottom:0;">
                        <div>
                            <div class="eyebrow">Category slider</div>
                            <h2 class="section-title" style="font-size:34px;margin-top:12px;">Browse menu by visual categories</h2>
                        </div>
                    </div>

                    <div class="category-slider">
                        <a href="{{ route('frontend.menu.index') }}" class="category-slider-card {{ empty($selectedCategoryIds) && ! $currentCategory ? 'is-active' : '' }}">
                            <div class="category-slider-image">
                                <img src="{{ \App\Support\FoodImageResolver::category('Menu') }}" alt="All menu">
                            </div>
                            <strong>All Items</strong>
                            <span class="muted" style="font-size:12px;">Full menu</span>
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ \App\Support\MenuUrl::category($category) }}" class="category-slider-card {{ ($currentCategory?->id === $category->id || in_array($category->id, $selectedCategoryIds, true)) ? 'is-active' : '' }}">
                                <div class="category-slider-image">
                                    <img src="{{ \App\Support\ImagePath::thumbUrl($category->image, \App\Support\FoodImageResolver::category($category->name)) }}" alt="{{ $category->name }}">
                                </div>
                                <strong>{{ $category->name }}</strong>
                                <span class="muted" style="font-size:12px;">Visual browse</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="container menu-page-grid">
            <div class="filter-shell">
                <div class="filter-box">
                    <div class="filter-sidebar-head">
                        <div class="eyebrow">Quick browse</div>
                        <h2 class="section-title" style="font-size:34px;margin-top:12px;">Subcategory shortcuts</h2>
                    </div>

                    <div class="filter-subcat-scroll" role="region" aria-label="Subcategory shortcuts list">
                        @forelse($visibleSubcategories as $subcategory)
                            <a href="{{ $subcategory->category ? \App\Support\MenuUrl::subcategory($subcategory->category, $subcategory) : route('frontend.menu.index') }}" class="subcat-rail-card">
                                <img src="{{ \App\Support\ImagePath::thumbUrl($subcategory->image, \App\Support\FoodImageResolver::subcategory($subcategory->name)) }}" alt="{{ $subcategory->name }}">
                                <div>
                                    <strong style="display:block;font-size:15px;">{{ $subcategory->name }}</strong>
                                    <span class="muted" style="font-size:12px;">{{ $subcategory->category?->name ?: 'Menu' }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="muted">Selected category ke hisaab se subcategory shortcuts yahan show honge.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div>
                <div class="results-bar">
                    <div class="results-head">
                        <div>
                            <div class="eyebrow">Results</div>
                            <h2 class="section-title" style="font-size:40px;margin-top:12px;">Menu items</h2>
                        </div>
                        <div class="menu-meta">
                            <span class="pill">{{ $products->total() }} items</span>
                            <span class="pill">{{ $searchInLabels[$selectedSearchIn ?? 'all'] ?? 'All fields' }}</span>
                            @if(($selectedMinPrice ?? null) !== null || ($selectedMaxPrice ?? null) !== null)
                                <span class="pill">Price filtered</span>
                            @endif
                        </div>
                    </div>

                    <div class="active-filter-row">
                        @if(request('q'))
                            <span class="active-filter">Query: {{ request('q') }}</span>
                        @endif
                        @if($selectedCategoryIds)
                            <span class="active-filter">{{ count($selectedCategoryIds) }} categories</span>
                        @endif
                        @if($selectedSubcategoryIds)
                            <span class="active-filter">{{ count($selectedSubcategoryIds) }} subcategories</span>
                        @endif
                        @if($selectedTypes)
                            <span class="active-filter">Types: {{ implode(', ', array_map(fn ($type) => ucfirst(str_replace('_', ' ', $type)), $selectedTypes)) }}</span>
                        @endif
                        @if(($selectedMinPrice ?? null) !== null)
                            <span class="active-filter">Min {{ $setting->currency_symbol ?? 'Rs' }} {{ number_format((float) $selectedMinPrice, 0) }}</span>
                        @endif
                        @if(($selectedMaxPrice ?? null) !== null)
                            <span class="active-filter">Max {{ $setting->currency_symbol ?? 'Rs' }} {{ number_format((float) $selectedMaxPrice, 0) }}</span>
                        @endif
                        @if(! request('q') && ! $selectedCategoryIds && ! $selectedSubcategoryIds && ! $selectedTypes && ($selectedMinPrice ?? null) === null && ($selectedMaxPrice ?? null) === null)
                            <span class="active-filter">Showing all menu items</span>
                        @endif
                    </div>
                </div>

                @if($visibleSubcategories->isNotEmpty())
                    <div class="subcat-rail">
                        @foreach($visibleSubcategories as $subcategory)
                            <a href="{{ $subcategory->category ? \App\Support\MenuUrl::subcategory($subcategory->category, $subcategory) : route('frontend.menu.index') }}" class="subcat-rail-card">
                                <img src="{{ \App\Support\ImagePath::thumbUrl($subcategory->image, \App\Support\FoodImageResolver::subcategory($subcategory->name)) }}" alt="{{ $subcategory->name }}">
                                <div>
                                    <strong style="display:block;font-size:15px;">{{ $subcategory->name }}</strong>
                                    <span class="muted" style="font-size:12px;">{{ $subcategory->category?->name ?: 'Menu' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div id="menu-results" class="results-grid menu-results-grid">
                    @include('frontend.menu.partials.items', ['products' => $products, 'setting' => $setting])
                </div>
                <div id="menu-loading" class="section-sm muted" style="display:none;">Loading more items...</div>
                <div
                    id="menu-sentinel"
                    data-next-page="{{ $products->nextPageUrl() }}"
                    data-category-slug="{{ $currentCategory?->slug }}"
                    data-subcategory-slug="{{ $currentSubcategory?->slug }}"
                ></div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            document.querySelectorAll('.js-clean-query-form').forEach((form) => {
                form.addEventListener('submit', () => {
                    form.querySelectorAll('input[name="q"]').forEach((input) => {
                        if (!input.value.trim()) {
                            input.disabled = true;
                        }
                    });

                    form.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach((input) => {
                        if (!input.value.trim()) {
                            input.disabled = true;
                        }
                    });

                    form.querySelectorAll('select[name="sort"]').forEach((select) => {
                        if (select.value === 'latest') {
                            select.disabled = true;
                        }
                    });

                    form.querySelectorAll('select[name="search_in"]').forEach((select) => {
                        if (select.value === 'all') {
                            select.disabled = true;
                        }
                    });
                });
            });

            const sentinel = document.getElementById('menu-sentinel');
            const results = document.getElementById('menu-results');
            const loading = document.getElementById('menu-loading');

            if (!sentinel || !results) {
                return;
            }

            let loadingState = false;

            const loadMore = async () => {
                const nextPage = sentinel.dataset.nextPage;
                if (!nextPage || loadingState) {
                    return;
                }

                loadingState = true;
                loading.style.display = 'block';

                const url = new URL(nextPage, window.location.origin);
                url.pathname = '{{ route('frontend.menu.feed', [], false) }}';
                if (sentinel.dataset.categorySlug && !url.searchParams.has('categories[]')) {
                    url.searchParams.set('category_slug', sentinel.dataset.categorySlug);
                }
                if (sentinel.dataset.subcategorySlug && !url.searchParams.has('subcategories[]')) {
                    url.searchParams.set('subcategory_slug', sentinel.dataset.subcategorySlug);
                }

                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    loadingState = false;
                    loading.style.display = 'none';
                    return;
                }

                const data = await response.json();
                if (data.html) {
                    results.insertAdjacentHTML('beforeend', data.html);
                }

                sentinel.dataset.nextPage = data.next_page || '';
                loadingState = false;
                loading.style.display = 'none';
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        loadMore();
                    }
                });
            }, { rootMargin: '0px 0px 260px 0px' });

            observer.observe(sentinel);
        })();
    </script>
@endsection
