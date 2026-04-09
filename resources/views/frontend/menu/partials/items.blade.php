@forelse($products as $product)
    @php
        $image = is_array($product->images) && isset($product->images[0]) ? $product->images[0] : null;
        $productType = $product->type instanceof \BackedEnum ? $product->type->value : $product->type;
        $productImage = \App\Support\ImagePath::thumbUrl($image, \App\Support\FoodImageResolver::product($product->name, 1));
        $rating = number_format(((($product->id % 8) + 42) / 10), 1);
        $eta = 15 + ($product->id % 5) * 5;
        $isVeg = $productType === 'veg';
        $isTrending = ($product->id % 6) === 0;
        $hasOffer = ($product->id % 5) === 0;
    @endphp
    <article class="menu-card menu-card-modern product-card">
        <a class="product-card-media" href="{{ route('frontend.menu.show', $product) }}">
            <div class="menu-image" style="margin-bottom:0;">
                <img src="{{ $productImage }}" alt="{{ $product->name }}">
                <div class="floating-chip">
                    <span style="display:inline-flex;align-items:center;gap:6px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:{{ $isVeg ? '#16a34a' : '#dc2626' }};"></span>
                        {{ $isVeg ? 'Veg' : 'Non Veg' }}
                    </span>
                </div>
                <div class="image-badge pill" style="display:inline-flex;gap:10px;align-items:center;">
                    <span>★ {{ $rating }}</span>
                    <span class="muted">{{ $eta }}-{{ $eta + 5 }} min</span>
                </div>
                @if($hasOffer)
                    <div class="product-badge product-badge-offer">Best Offer</div>
                @elseif($isTrending)
                    <div class="product-badge product-badge-hot">Trending</div>
                @endif
            </div>
        </a>

        <div class="menu-card-body" style="padding:0 4px;">
            <div class="product-card-head">
                <h3 class="dish-title" style="font-size:17px;">
                    <a href="{{ route('frontend.menu.show', $product) }}">{{ $product->name }}</a>
                </h3>
                <div class="dish-price">{{ $setting->currency_symbol ?? 'Rs' }} {{ number_format((float) $product->base_price, 0) }}</div>
            </div>

            <div class="menu-meta product-meta">
                @if($product->category?->name)
                    <span class="pill">{{ $product->category->name }}</span>
                @endif
                @if($product->subcategory?->name)
                    <span class="pill">{{ $product->subcategory->name }}</span>
                @endif
                <span class="pill">{{ $productType ? ucfirst(str_replace('_', ' ', (string) $productType)) : 'Dish' }}</span>
            </div>

            <p class="dish-desc product-desc">{{ $product->description ?: 'Freshly prepared, quick to add, delivered fast.' }}</p>

            <div class="product-card-actions">
                <a class="btn-outline product-view" href="{{ route('frontend.menu.show', $product) }}">View</a>
                <form method="POST" action="{{ route('frontend.cart.items.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="btn product-add">Add to cart</button>
                </form>
            </div>
        </div>
    </article>
@empty
    <div class="contact-card">No active menu items found for the selected filter.</div>
@endforelse
