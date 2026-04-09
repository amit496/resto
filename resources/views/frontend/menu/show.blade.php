@extends('frontend.layouts.app')

@section('title', $product->name.' | '.($setting->app_name ?? config('app.name', 'FoodiHub')))

@section('content')
    <section class="page-hero">
        <div class="container commerce-shell">
            <div class="commerce-card" style="padding:14px;">
                @php $heroImage = is_array($product->images) && isset($product->images[0]) ? $product->images[0] : null; @endphp
                <img src="{{ \App\Support\ImagePath::url($heroImage, \App\Support\FoodImageResolver::product($product->name, 1)) }}" alt="{{ $product->name }}" class="media-thumb" style="height: 480px;margin-bottom:0;border-radius:26px;">
            </div>
            <div class="commerce-card commerce-sticky" style="background:linear-gradient(145deg,#fff 0%,#fff4ef 100%);padding:16px;">
                <div class="detail-stack">
                    <div class="eyebrow">{{ $product->category?->name ?: 'Menu Item' }}</div>
                    <h1 class="section-title">{{ $product->name }}</h1>
                    <p class="detail-microcopy">{{ $product->description ?: 'Detailed menu presentation for your restaurant frontend.' }}</p>
                    <div class="detail-meta-row">
                        <span class="pill">{{ $product->type->value }}</span>
                        <span class="pill">{{ number_format((float) $product->base_price, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</span>
                        @if($product->subcategory?->name)
                            <span class="pill">{{ $product->subcategory->name }}</span>
                        @endif
                    </div>
                    <div class="detail-note-row">
                        <span class="trust-chip">Freshly prepared</span>
                        <span class="trust-chip">Variants available</span>
                        <span class="trust-chip">Branch-ready order</span>
                    </div>
                    <form method="POST" action="{{ route('frontend.cart.items.store') }}" class="detail-actions">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @if($product->variants->isNotEmpty())
                            <select class="select" name="product_variant_id">
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}">{{ $variant->name }} {{ $variant->value ? '- '.$variant->value : '' }} ({{ number_format((float) $variant->price, 2) }})</option>
                                @endforeach
                            </select>
                        @endif
                        <input class="input qty-field" type="number" min="1" max="20" name="quantity" value="1">
                        <button type="submit" class="btn">Add To Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container grid-2">
            <div class="commerce-card">
                <div class="section-title">Available Variants</div>
                <div class="grid-2">
                    @forelse($product->variants as $variant)
                        <div class="feature-card">
                            <h3>{{ $variant->name }} {{ $variant->value ? '- '.$variant->value : '' }}</h3>
                            <p class="muted">Price: {{ number_format((float) $variant->price, 2) }} {{ $setting->currency_symbol ?? 'Rs' }}</p>
                            <div class="pill">{{ $variant->status->value }}</div>
                        </div>
                    @empty
                        <div class="feature-card">No variants configured.</div>
                    @endforelse
                </div>
            </div>
            <div class="commerce-card">
                <div class="section-title">Customer Reviews</div>
                <div class="grid-2">
                    @forelse($product->reviews->where('status', 'approved')->where('is_published', true) as $review)
                        <div class="review-card">
                            <div class="pill">{{ $review->rating }}/5 Rating</div>
                            <h3>{{ $review->title ?: 'Food Review' }}</h3>
                            <p class="muted">{{ $review->comment ?: 'Published review from the review module.' }}</p>
                            <div class="menu-meta">
                                <span class="pill">{{ $review->customer?->name ?: 'Guest' }}</span>
                                @if($review->branch?->name)
                                    <span class="pill">{{ $review->branch->name }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="feature-card">No published reviews yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="section-sm">
        <div class="container">
            <div class="section-title">Related Items</div>
            <div class="grid-4">
                @foreach($relatedProducts as $item)
                    <a href="{{ route('frontend.menu.show', $item) }}" class="menu-card menu-card-modern menu-result-card">
                        @php $image = is_array($item->images) && isset($item->images[0]) ? $item->images[0] : null; @endphp
                        <div class="menu-image">
                            <img src="{{ \App\Support\ImagePath::thumbUrl($image, \App\Support\FoodImageResolver::product($item->name, 1)) }}" alt="{{ $item->name }}" class="media-thumb" style="margin-bottom:0;">
                            <div class="image-badge">Related</div>
                        </div>
                        <div class="menu-card-body">
                            <div class="menu-card-top">
                                <div class="menu-card-title">
                                    <span class="menu-card-kicker">{{ $item->subcategory?->name ?: 'Menu Item' }}</span>
                                    <h3 class="home-card-title">{{ $item->name }}</h3>
                                </div>
                            </div>
                            <div class="menu-badge-row">
                                <span class="menu-chip">{{ $item->category?->name ?: 'Food' }}</span>
                                <span class="menu-chip">Related</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection
