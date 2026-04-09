@extends('frontend.layouts.app')

@section('title', ($setting->app_name ?? config('app.name', 'FoodiHub')).' | Home')

@section('styles')
    <style>
        :root { --home-section-gap: 34px; }

        body.figma-store .page-main {
            padding-top: 0;
        }

        .home-app {
            display: grid;
            gap: var(--home-section-gap);
            width: 100%;
            max-width: 100%;
            margin-left: 0;
            margin-right: 0;
            padding: var(--home-section-gap) var(--page-gutter) var(--home-section-gap);
        }
        .home-full-bleed {
            width: 100%;
            max-width: 100%;
        }
        .home-band-shell {
            padding: 0;
        }
        .home-promo-section {
            padding-top: var(--home-section-gap);
            padding-bottom: var(--home-section-gap);
            padding-left: var(--page-gutter);
            padding-right: var(--page-gutter);
        }
        .home-slider-layout { display: grid; }
        .home-slider-stage {
            position: relative;
            min-height: calc(100vh - 78px);
            overflow: hidden;
            background: #170d09;
            border-radius: 0;
        }
        .home-slider-track {
            position: relative;
            height: 100%;
        }
        .home-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            pointer-events: none;
            transition: opacity .45s ease;
        }
        .home-slide.is-active {
            opacity: 1;
            pointer-events: auto;
        }
        .home-slide-media,
        .home-banner-media {
            display: block;
            width: 100%;
            height: 100%;
        }
        .home-slide-media picture,
        .home-banner-media picture {
            display: block;
            width: 100%;
            height: 100%;
        }
        .home-slide-media img,
        .home-banner-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .home-slide-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            padding: clamp(22px, 4vw, 60px);
            background: linear-gradient(90deg, rgba(0, 0, 34, 0.88) 0%, rgba(0, 0, 34, 0.45) 42%, rgba(0, 0, 34, 0.1) 100%);
            color: #fff;
        }
        .home-slide-copy {
            max-width: 760px;
            display: grid;
            gap: 16px;
        }
        .home-slide-description {
            max-width: 560px;
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 16px;
            line-height: 1.7;
        }
        .home-slide-dots {
            position: absolute;
            left: 50%;
            bottom: 24px;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            gap: 10px;
        }
        .home-slide-dot {
            width: 12px;
            height: 12px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
        }
        .home-slide-dot.is-active {
            background: #fff;
        }
        .home-hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }
        .home-hero-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .home-hero-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: var(--radius-md, 10px);
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.22);
            color: #fff;
            font-weight: 600;
            font-size: 13px;
        }
        .hero-locate {
            display: flex;
            align-items: stretch;
            max-width: 560px;
            background: #fff;
            border-radius: var(--radius-lg, 12px);
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
        }
        .hero-locate .input {
            flex: 1;
            border: 0;
            padding: 16px 18px;
            font-size: 15px;
            color: #1a1a1a;
            min-width: 0;
        }
        .hero-locate .input::placeholder {
            color: rgba(26,26,26,0.45);
        }
        .hero-locate-btn {
            flex-shrink: 0;
            padding: 0 24px;
            border: 0;
            background: linear-gradient(180deg, #ff8c00, #e67e00);
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
        }
        .hero-quick-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .hero-quick-pills a {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: var(--radius-md, 10px);
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            font-weight: 600;
            font-size: 13px;
        }
        .hero-quick-pills a:hover {
            background: rgba(255,140,0,0.35);
            border-color: rgba(255,140,0,0.5);
        }
        .home-after-hero { padding: var(--home-section-gap) 0; }
        .top-selling-row {
            display: grid;
            gap: 14px;
        }
        .top-selling-list {
            display: grid;
            gap: 12px;
        }
        .figma-ts-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
            gap: 24px;
            align-items: stretch;
        }
        .figma-ts-feature {
            background: #fff;
            border-radius: var(--radius-lg, 12px);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
            padding: clamp(28px, 4vw, 48px);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 280px;
        }
        .figma-ts-feature h2 {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-size: clamp(28px, 3.5vw, 40px);
            font-weight: 700;
            color: var(--text);
            line-height: 1.15;
            letter-spacing: -0.02em;
        }
        .top-selling-item {
            display: grid;
            grid-template-columns: 56px 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 14px 16px;
            border-radius: var(--radius-lg, 12px);
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
        }
        .top-selling-item img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
        }
        .figma-ts-add {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm, 8px);
            border: 0;
            background: linear-gradient(180deg, #ff8c00, #e67e00);
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            line-height: 1;
            cursor: pointer;
            display: grid;
            place-items: center;
            box-shadow: 0 4px 12px rgba(255,140,0,0.35);
        }
        .top-selling-price {
            font-weight: 900;
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.03em;
        }
        .home-banner-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-top: 16px;
        }
        .home-banner-card {
            display: grid;
            gap: 12px;
            padding: 12px;
            border-radius: var(--radius-lg, 12px);
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
        }
        .home-banner-media {
            height: 160px;
            overflow: hidden;
            border-radius: var(--radius-md, 10px);
            background: #f0f0f0;
        }
        .home-banner-content {
            display: grid;
            gap: 8px;
        }
        .home-banner-content p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }
        .home-content-shell {
            width: min(1500px, calc(100% - 32px));
            margin: 0 auto;
        }
        .home-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(360px, .8fr);
            gap: 22px;
            align-items: stretch;
        }
        .hero-surface {
            position: relative;
            overflow: hidden;
            padding: 34px;
            border-radius: 36px;
            min-height: 480px;
            background:
                linear-gradient(120deg, rgba(10, 10, 10, 0.86), rgba(226, 55, 68, 0.55)),
                var(--hero-image, linear-gradient(135deg, #111111, #e23744));
            background-size: cover;
            background-position: center;
            color: #fff;
            box-shadow: var(--shadow);
        }
        .hero-surface::after {
            content: "";
            position: absolute;
            inset: 18px;
            border-radius: 28px;
            border: 1px solid rgba(255,255,255,0.14);
        }
        .hero-content {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 18px;
            align-content: space-between;
            min-height: 100%;
        }
        .hero-heading {
            max-width: 720px;
            font-family: 'Poppins', 'Outfit', sans-serif;
            font-size: clamp(32px, 4.2vw, 52px);
            line-height: 1.12;
            letter-spacing: -0.03em;
            font-weight: 700;
            margin: 0;
        }
        .hero-search {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
            max-width: 720px;
            padding: 12px;
            border-radius: 28px;
            background: rgba(255,255,255,0.92);
        }
        .hero-search .input {
            border: 0;
            background: transparent;
            padding: 10px 12px;
        }
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }
        .hero-stat {
            padding: 16px 18px;
            border-radius: 20px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
        }
        .hero-stat strong {
            display: block;
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            letter-spacing: -.05em;
        }
        .home-stack-card {
            display: grid;
            gap: 18px;
            padding: 24px;
            border-radius: 32px;
            background: rgba(255,255,255,0.88);
            border: 1px solid rgba(255,255,255,0.74);
            box-shadow: var(--shadow);
        }
        .story-card {
            display: grid;
            grid-template-columns: 96px 1fr;
            gap: 14px;
            align-items: center;
            padding: 14px;
            border-radius: 22px;
            background: #fff;
            border: 1px solid var(--line);
        }
        .story-card img {
            width: 96px;
            height: 96px;
            border-radius: 22px;
            object-fit: cover;
        }
        .category-grid.figma-category-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 18px;
            align-items: start;
        }
        .figma-category-grid .category-card {
            padding: 12px;
            border-radius: var(--radius-lg, 12px);
            text-align: center;
        }
        .figma-category-grid .category-card img {
            width: 100%;
            height: 120px;
            border-radius: var(--radius-md, 10px);
            object-fit: cover;
        }
        .figma-category-grid .home-card-title {
            margin-top: 12px;
            font-size: 16px;
            font-weight: 600;
        }
        .figma-category-grid .home-card-copy {
            display: none;
        }
        @media (max-width: 1100px) {
            .category-grid.figma-category-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            .category-grid.figma-category-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        .category-card {
            display: grid;
            gap: 12px;
            padding: 14px;
            border-radius: 26px;
            background: rgba(255,255,255,0.92);
            border: 1px solid var(--line);
            box-shadow: 0 16px 30px rgba(118, 63, 30, 0.08);
        }
        .category-card img {
            width: 100%;
            height: 118px;
            object-fit: cover;
            border-radius: 20px;
        }
        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .offer-strip {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 20px;
            align-items: center;
            padding: 18px 22px;
            border-radius: var(--radius-lg, 12px);
            background: linear-gradient(90deg, #2a1810 0%, #1a0f0a 55%, rgba(255,140,0,0.35) 100%);
            color: #fff;
        }
        .offer-strip .figma-view-now {
            background: #ff8c00;
            color: #fff;
            border: 0;
            font-weight: 600;
            border-radius: var(--radius-md, 10px);
            padding: 12px 28px;
            box-shadow: 0 4px 16px rgba(255,140,0,0.4);
        }
        .promo-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }
        .promo-card {
            display: grid;
            gap: 14px;
            padding: 20px;
            border-radius: var(--radius-lg, 12px);
            background: #f5f5f5;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
        }
        .promo-card-figma-tag {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            background: rgba(255,140,0,0.2);
            color: #b45300;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
        }
        .promo-copy-link {
            background: none;
            border: 0;
            padding: 0;
            color: #ff8c00;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
        }
        .promo-apply-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 44px;
            border-radius: var(--radius-md, 10px);
            background: linear-gradient(180deg, #ff8c00, #e67e00);
            color: #fff;
            font-weight: 600;
            border: 0;
            margin-top: 4px;
        }
        .product-card--figma {
            border-radius: var(--radius-lg, 12px);
            padding: 12px;
        }
        .product-card--figma .product-media {
            border-radius: var(--radius-md, 10px);
        }
        .product-card--figma .product-media img {
            border-radius: var(--radius-md, 10px);
        }
        .product-actions--figma {
            display: block;
            margin-top: 12px;
        }
        .product-actions--figma .btn {
            width: 100%;
            justify-content: center;
        }
        .promo-card-top {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 12px;
        }
        .promo-code {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent-dark);
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .promo-value {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            font-size: 34px;
            line-height: .96;
            letter-spacing: -.05em;
        }
        .promo-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .mini-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }
        .product-card {
            display: grid;
            gap: 14px;
            padding: 14px;
            border-radius: 26px;
            background: rgba(255,255,255,0.92);
            border: 1px solid var(--line);
            box-shadow: 0 16px 30px rgba(118, 63, 30, 0.08);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
            overflow: hidden;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 40px rgba(118, 63, 30, 0.14);
            border-color: rgba(239, 90, 41, 0.22);
        }
        .product-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 20px;
            transition: transform .35s ease;
        }
        .product-card:hover img {
            transform: scale(1.05);
        }
        .product-head {
            display: flex;
            align-items: start;
            justify-content: space-between;
            gap: 12px;
        }
        .product-media {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            background: linear-gradient(135deg, #fff0e6, #ffe0cc);
        }
        .product-media::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(20, 12, 8, 0) 40%, rgba(20, 12, 8, 0.58) 100%);
            pointer-events: none;
        }
        .product-floating {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(255,255,255,0.7);
            font-size: 12px;
            font-weight: 800;
        }
        .product-bookmark {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 1;
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: rgba(255,255,255,0.94);
            border: 1px solid rgba(255,255,255,0.7);
            color: var(--accent-dark);
            box-shadow: 0 10px 24px rgba(39, 21, 15, 0.1);
            font-size: 16px;
            line-height: 1;
        }
        .product-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .product-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .subcat-grid.figma-subcat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }
        .subcat-card.figma-subcat-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: var(--radius-lg, 12px);
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
            position: relative;
        }
        .subcat-card.figma-subcat-card img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .figma-subcat-card .figma-chevron {
            margin-left: auto;
            color: rgba(26,26,26,0.35);
            flex-shrink: 0;
        }
        @media (max-width: 1240px) {
            .home-slider-stage {
                min-height: 520px;
            }
            .figma-ts-grid {
                grid-template-columns: 1fr;
            }
            .figma-ts-feature {
                min-height: 200px;
            }
            .home-hero,
            .promo-grid,
            .mini-grid,
            .subcat-grid.figma-subcat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .home-hero {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 760px) {
            .home-slider-stage {
                min-height: 460px;
            }
            .home-slide-overlay {
                align-items: end;
                padding: 22px;
                background: linear-gradient(180deg, rgba(32, 16, 10, 0.08) 0%, rgba(32, 16, 10, 0.78) 100%);
            }
            .home-slide-description {
                font-size: 14px;
            }
            .home-banner-stack {
                padding: 14px;
            }
            .home-promo-section {
                padding: 14px;
            }
            .hero-surface,
            .home-stack-card {
                padding: 20px;
                border-radius: 24px;
            }
            .hero-stats,
            .mini-grid,
            .promo-grid,
            .subcat-grid.figma-subcat-grid,
            .home-banner-grid,
            .hero-search {
                grid-template-columns: 1fr;
            }
            .story-card {
                grid-template-columns: 1fr;
            }
            .story-card img {
                width: 100%;
                height: 180px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $heroProduct = $topSellingProducts->first() ?? $featuredProducts->first();
        $heroImage = $heroProduct && is_array($heroProduct->images) && isset($heroProduct->images[0])
            ? \App\Support\ImagePath::thumbUrl($heroProduct->images[0], \App\Support\FoodImageResolver::product($heroProduct->name, 1))
            : \App\Support\FoodImageResolver::category('Pizza');
        $homeMeta = $setting?->meta ?? [];
        $sliderItems = collect($homeMeta['home_sliders'] ?? [])
            ->filter(fn ($item) => (bool) ($item['is_active'] ?? true) && ! empty($item['desktop_image']))
            ->values();
        $bannerItems = collect($homeMeta['home_banners'] ?? [])
            ->filter(fn ($item) => (bool) ($item['is_active'] ?? true) && ! empty($item['desktop_image']))
            ->values();
        if ($bannerItems->isEmpty()) {
            $bannerItems = $topSellingProducts->take(3)->map(function ($product) use ($setting) {
                $image = is_array($product->images) && isset($product->images[0]) ? $product->images[0] : null;

                return [
                    'title' => $product->name,
                    'description' => ($product->subcategory?->name ?: 'Best seller').' now available on the homepage banner section.',
                    'button_label' => 'View dish',
                    'button_url' => route('frontend.menu.show', $product),
                    'desktop_image' => $image,
                    'mobile_image' => $image,
                    'is_active' => true,
                ];
            })->values();
        }
        if ($sliderItems->isEmpty()) {
            $sliderItems = collect([[
                'title' => 'Food delivery homepage with slider managed from admin panel.',
                'description' => 'Upload slides, banners, and offers from the admin settings. Customers see this hero first—add your best food photography and CTA links.',
                'button_label' => 'View menu',
                'button_url' => route('frontend.menu.index'),
                'desktop_image' => $heroImage,
                'mobile_image' => $heroImage,
                'is_active' => true,
            ]]);
        }
        $allSubcategories = $categorySections
            ->flatMap(fn ($section) => $section['category']->subcategories ?? collect())
            ->unique('id')
            ->take(9);
    @endphp

    <section class="home-full-bleed home-band-shell">
        <div class="home-slider-layout">
            <div class="home-slider-stage js-home-slider">
                <div class="home-slider-track">
                    @foreach($sliderItems as $index => $slide)
                        @php
                            $desktopImage = \App\Support\ImagePath::url($slide['desktop_image'] ?? null, $heroImage);
                            $mobileImage = \App\Support\ImagePath::url($slide['mobile_image'] ?? $slide['desktop_image'] ?? null, $heroImage);
                            $buttonUrl = ! empty($slide['button_url']) ? $slide['button_url'] : route('frontend.menu.index');
                            $buttonLabel = ! empty($slide['button_label']) ? $slide['button_label'] : 'Browse Menu';
                        @endphp
                        <article class="home-slide {{ $index === 0 ? 'is-active' : '' }}" data-slide-index="{{ $index }}">
                            <a href="{{ $buttonUrl }}" class="home-slide-media" aria-label="{{ $slide['title'] ?? 'Homepage slider image' }}">
                                <picture>
                                    <source media="(max-width: 760px)" srcset="{{ $mobileImage }}">
                                    <img src="{{ $desktopImage }}" alt="{{ $slide['title'] ?? 'Homepage slider image' }}">
                                </picture>
                            </a>
                            <div class="home-slide-overlay">
                                <div class="home-slide-copy">
                                    <div>
                                        <h1 class="hero-heading">{{ $slide['title'] ?? 'Fresh food delivered fast.' }}</h1>
                                        @if(!empty($slide['description']))
                                            <p class="home-slide-description">{{ $slide['description'] }}</p>
                                        @endif
                                    </div>

                                    <form action="{{ route('frontend.menu.index') }}" method="GET" class="hero-locate">
                                        <input class="input" type="text" name="q" value="" placeholder="Enter delivery address or search food..." autocomplete="off" aria-label="Address or search">
                                        <button class="hero-locate-btn" type="submit">Locate</button>
                                    </form>

                                    <div class="hero-quick-pills">
                                        <a href="{{ route('frontend.menu.index') }}">All Menu</a>
                                        <a href="{{ route('frontend.offers') }}">Offer</a>
                                        <a href="{{ route('frontend.menu.index', ['sort' => 'popular']) }}">Best Rated</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($sliderItems->count() > 1)
                    <div class="home-slide-dots">
                        @foreach($sliderItems as $index => $slide)
                            <button type="button" class="home-slide-dot {{ $index === 0 ? 'is-active' : '' }}" data-slide-dot="{{ $index }}" aria-label="Show slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="home-after-hero">
        <div class="container">
            <div class="figma-ts-grid">
                <div class="figma-ts-feature">
                    <h2>Top selling right now</h2>
                </div>
                <div class="top-selling-row">
                    <div class="top-selling-list">
                        @foreach($topSellingProducts->take(4) as $product)
                            @php
                                $image = is_array($product->images) && isset($product->images[0]) ? $product->images[0] : null;
                                $thumb = \App\Support\ImagePath::thumbUrl($image, \App\Support\FoodImageResolver::product($product->name, 1));
                            @endphp
                            <div class="top-selling-item">
                                <a href="{{ route('frontend.menu.show', $product) }}"><img src="{{ $thumb }}" alt="{{ $product->name }}"></a>
                                <div>
                                    <strong style="display:block;font-weight:600;">{{ $product->name }}</strong>
                                    <div class="top-selling-price" style="margin-top:6px;">{{ $setting->currency_symbol ?? 'Rs' }} {{ number_format((float) $product->base_price, 0) }}</div>
                                </div>
                                <form method="POST" action="{{ route('frontend.cart.items.store') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="figma-ts-add" title="Add to cart" aria-label="Add {{ $product->name }}">+</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-full-bleed home-promo-section">
        <div class="section-head" style="margin-bottom:0;">
            <div>
                <div class="eyebrow">Homepage banners</div>
                <h2 class="section-title" style="font-size:42px;margin-top:12px;">Dynamic banner section from admin panel.</h2>
            </div>
        </div>

        <div class="home-banner-grid">
            @foreach($bannerItems->take(3) as $banner)
                @php
                    $desktopImage = \App\Support\ImagePath::url($banner['desktop_image'] ?? null, $heroImage);
                    $mobileImage = \App\Support\ImagePath::url($banner['mobile_image'] ?? $banner['desktop_image'] ?? null, $heroImage);
                    $buttonUrl = ! empty($banner['button_url']) ? $banner['button_url'] : route('frontend.menu.index');
                    $buttonLabel = ! empty($banner['button_label']) ? $banner['button_label'] : 'Explore';
                @endphp
                <article class="home-banner-card">
                    <a href="{{ $buttonUrl }}" class="home-banner-media">
                        <picture>
                            <source media="(max-width: 760px)" srcset="{{ $mobileImage }}">
                            <img src="{{ $desktopImage }}" alt="{{ $banner['title'] ?? 'Homepage banner' }}">
                        </picture>
                    </a>
                    <div class="home-banner-content">
                        @if(!empty($banner['title']))
                            <h3 class="feed-title">{{ $banner['title'] }}</h3>
                        @endif
                        @if(!empty($banner['description']))
                            <p>{{ $banner['description'] }}</p>
                        @endif
                        <div>
                            <a href="{{ $buttonUrl }}" class="btn-outline">{{ $buttonLabel }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="container home-app">

        <section>
            <div class="section-head">
                <div>
                    <div class="eyebrow">Categories</div>
                    <h2 class="section-title" style="font-size:42px;margin-top:12px;">Search by food category.</h2>
                </div>
                <a href="{{ route('frontend.menu.index') }}" class="btn-outline">View All</a>
            </div>

            <div class="category-grid figma-category-grid">
                @foreach($categorySections as $section)
                    <a href="{{ \App\Support\MenuUrl::category($section['category']) }}" class="category-card">
                        <img src="{{ \App\Support\ImagePath::thumbUrl($section['category']->image, \App\Support\FoodImageResolver::category($section['category']->name)) }}" alt="{{ $section['category']->name }}">
                        <div>
                            <h3 class="home-card-title">{{ $section['category']->name }}</h3>
                            <p class="home-card-copy" style="margin:8px 0 0;">{{ $section['products']->count() }} featured items, image-first browsing and quick order access.</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @php
            $howItWorksRows = collect($setting?->meta['home_how_it_works'] ?? [])
                ->filter(function ($row) {
                    if (! is_array($row) || trim((string) ($row['title'] ?? '')) === '') {
                        return false;
                    }
                    $active = $row['is_active'] ?? true;
                    if (is_bool($active)) {
                        return $active;
                    }

                    return (int) $active === 1;
                })
                ->values();
        @endphp
        @if($howItWorksRows->isNotEmpty())
            <section>
                <div class="section-head">
                    <div>
                        <div class="eyebrow" style="background:var(--brand-green-soft);color:var(--brand-green-dark);">How it works</div>
                        <h2 class="section-title" style="font-size:42px;margin-top:12px;">Order in a few taps.</h2>
                        <p class="section-copy" style="margin:10px 0 0;">Steps below are managed under <strong>Settings → Homepage blocks</strong> in the admin panel.</p>
                    </div>
                </div>
                <div class="orderplus-how-grid">
                    @foreach($howItWorksRows as $step)
                        <article class="orderplus-how-card">
                            <div class="orderplus-how-icon" aria-hidden="true">{{ $step['icon'] ?: '✓' }}</div>
                            <h3>{{ $step['title'] }}</h3>
                            @if(!empty($step['description']))
                                <p>{{ $step['description'] }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        @if($activeOffers->isNotEmpty())
            @php $offer = $activeOffers->first(); @endphp
            <section class="offer-strip">
                <div>
                    <h2 class="section-title" style="font-size:clamp(22px,3vw,32px);margin:0 0 8px;color:#fff;">Up to {{ number_format((float) $offer->value, 0) }} OFF on food and drinks.</h2>
                    <p style="margin:0;color:rgba(255,255,255,0.85);max-width:560px;font-size:15px;">Code: <strong>{{ $offer->code }}</strong> (Valid on selected items only)</p>
                </div>
                <a href="{{ route('frontend.offers') }}" class="figma-view-now">View Now</a>
            </section>

            <section>
                <div class="section-head">
                    <div>
                        <div class="eyebrow">Promo Codes</div>
                        <h2 class="section-title" style="font-size:42px;margin-top:12px;">App-style discount codes and repeat-order offers.</h2>
                    </div>
                    <a href="{{ route('frontend.offers') }}" class="btn-outline">View More</a>
                </div>

                <div class="promo-grid">
                    @foreach($activeOffers->take(4) as $promo)
                        @php
                            $promoType = $promo->type instanceof \BackedEnum ? $promo->type->value : (string) $promo->type;
                            $promoValue = $promoType === 'percent'
                                ? number_format((float) $promo->value, 0).'% OFF'
                                : ($setting->currency_symbol ?? 'Rs').' '.number_format((float) $promo->value, 0).' OFF';
                        @endphp
                        <article class="promo-card">
                            <span class="promo-card-figma-tag">COUPON</span>
                            <div>
                                <p class="promo-value" style="margin:4px 0 0;">{{ $promoValue }}</p>
                                <p class="home-card-copy" style="margin:8px 0 0;font-size:13px;">Min. order {{ $setting->currency_symbol ?? 'Rs' }} {{ number_format((float) $promo->min_order_amount, 0) }} · Valid till {{ $promo->end_date?->format('d M Y') }}</p>
                            </div>
                            <div>
                                <button type="button" class="promo-copy-link" data-copy-code="{{ $promo->code }}">Copy Code</button>
                            </div>
                            <a href="{{ route('frontend.menu.index') }}" class="promo-apply-btn">Apply</a>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section>
            <div class="section-head">
                <div>
                    <div class="eyebrow">Most Selling Food</div>
                    <h2 class="section-title" style="font-size:42px;margin-top:12px;">Popular dishes customers reorder.</h2>
                </div>
            </div>

            <div class="mini-grid">
                @foreach($topSellingProducts->take(8) as $product)
                    @php
                        $image = is_array($product->images) && isset($product->images[0]) ? $product->images[0] : null;
                        $rating = number_format(((($product->id % 8) + 42) / 10), 1);
                    @endphp
                    <article class="product-card product-card--figma">
                        <a href="{{ route('frontend.menu.show', $product) }}" class="product-media">
                            <img src="{{ \App\Support\ImagePath::thumbUrl($image, \App\Support\FoodImageResolver::product($product->name, 1)) }}" alt="{{ $product->name }}">
                            <div class="product-bookmark" aria-hidden="true">♡</div>
                            <div class="product-floating">★ {{ $rating }}</div>
                        </a>
                        <div class="product-head">
                            <div>
                                <h3 class="home-card-title">{{ $product->name }}</h3>
                                <p class="home-card-copy" style="margin:6px 0 0;">{{ $product->subcategory?->name ?: $product->category?->name }}</p>
                            </div>
                            <div class="price-tag">{{ $setting->currency_symbol ?? 'Rs' }} {{ number_format((float) $product->base_price, 0) }}</div>
                        </div>
                        <div class="product-actions product-actions--figma">
                            <form method="POST" action="{{ route('frontend.cart.items.store') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn">Add to cart</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section>
            <div class="section-head">
                <div>
                    <div class="eyebrow">Drinks & Soft Drinks</div>
                    <h2 class="section-title" style="font-size:42px;margin-top:12px;">Cold drinks, shakes, juice and coffee.</h2>
                </div>
            </div>

            <div class="mini-grid">
                @foreach($topSellingDrinks->take(4) as $product)
                    @php
                        $image = is_array($product->images) && isset($product->images[0]) ? $product->images[0] : null;
                        $rating = number_format(((($product->id % 8) + 42) / 10), 1);
                    @endphp
                    <article class="product-card product-card--figma">
                        <a href="{{ route('frontend.menu.show', $product) }}" class="product-media">
                            <img src="{{ \App\Support\ImagePath::thumbUrl($image, \App\Support\FoodImageResolver::product($product->name, 1)) }}" alt="{{ $product->name }}">
                            <div class="product-bookmark" aria-hidden="true">♡</div>
                            <div class="product-floating">★ {{ $rating }}</div>
                        </a>
                        <div class="product-head">
                            <div>
                                <h3 class="home-card-title">{{ $product->name }}</h3>
                                <p class="home-card-copy" style="margin:6px 0 0;">{{ $product->subcategory?->name ?: 'Beverage' }}</p>
                            </div>
                            <div class="price-tag">{{ $setting->currency_symbol ?? 'Rs' }} {{ number_format((float) $product->base_price, 0) }}</div>
                        </div>
                        <div class="product-actions product-actions--figma">
                            <form method="POST" action="{{ route('frontend.cart.items.store') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn">Add to cart</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section>
            <div class="section-head">
                <div>
                    <div class="eyebrow">Subcategories</div>
                    <h2 class="section-title" style="font-size:42px;margin-top:12px;">Direct entry into focused menus.</h2>
                </div>
            </div>

            <div class="subcat-grid figma-subcat-grid">
                @foreach($allSubcategories as $subcategory)
                    <a href="{{ $subcategory->category ? \App\Support\MenuUrl::subcategory($subcategory->category, $subcategory) : route('frontend.menu.index') }}" class="subcat-card figma-subcat-card">
                        <img src="{{ \App\Support\ImagePath::thumbUrl($subcategory->image, \App\Support\FoodImageResolver::subcategory($subcategory->name)) }}" alt="{{ $subcategory->name }}">
                        <div style="min-width:0;">
                            <h3 class="feed-title" style="margin:0 0 4px;font-size:16px;">{{ $subcategory->name }}</h3>
                            <p class="muted" style="margin:0;font-size:13px;line-height:1.45;">{{ $subcategory->category?->name ?: 'Menu' }} · Browse curated items</p>
                        </div>
                        <span class="figma-chevron" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"></path></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            const slider = document.querySelector('.js-home-slider');
            if (!slider) {
                return;
            }

            const slides = Array.from(slider.querySelectorAll('.home-slide'));
            const dots = Array.from(slider.querySelectorAll('[data-slide-dot]'));
            if (slides.length <= 1) {
                return;
            }

            let currentIndex = 0;
            let intervalId = null;

            const showSlide = (index) => {
                currentIndex = (index + slides.length) % slides.length;
                slides.forEach((slide, slideIndex) => {
                    slide.classList.toggle('is-active', slideIndex === currentIndex);
                });
                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('is-active', dotIndex === currentIndex);
                });
            };

            const start = () => {
                intervalId = window.setInterval(() => showSlide(currentIndex + 1), 5000);
            };

            const restart = () => {
                window.clearInterval(intervalId);
                start();
            };

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    showSlide(Number(dot.dataset.slideDot || 0));
                    restart();
                });
            });

            slider.addEventListener('mouseenter', () => window.clearInterval(intervalId));
            slider.addEventListener('mouseleave', start);
            start();
        })();

        document.querySelectorAll('[data-copy-code]').forEach((btn) => {
            btn.addEventListener('click', async () => {
                const code = btn.getAttribute('data-copy-code') || '';
                try {
                    await navigator.clipboard.writeText(code);
                    const prev = btn.textContent;
                    btn.textContent = 'Copied!';
                    window.setTimeout(() => { btn.textContent = prev; }, 1800);
                } catch (e) {
                    btn.textContent = 'Copy failed';
                    window.setTimeout(() => { btn.textContent = 'Copy Code'; }, 1800);
                }
            });
        });
    </script>
@endsection
