@php
    $variant = $product->defaultVariant ?? $product->activeVariants->first();
    $price   = $variant?->effective_price ?? $product->price ?? 0;
    $oldPrice = ($variant?->hasActiveOffer() && $variant->price > $price) ? $variant->price : null;
    $discount = $oldPrice ? round((($oldPrice - $price) / $oldPrice) * 100) : 0;
    $image   = $product->main_image ? asset('storage/'.$product->main_image) : 'https://via.placeholder.com/300x300?text=No+Image';
    $isWishlisted = auth()->check() && auth()->user()->wishlist->contains('product_id', $product->id);
@endphp
<div class="product-card">
    <a href="{{ route('store.product', $product->slug) }}" class="product-card-image" style="display:block">
        <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy">
        <div class="product-card-badges">
            @if($discount > 0)<span class="badge-sale">-{{ $discount }}%</span>@endif
            @if($product->is_trending)<span class="badge-new" style="background:var(--warning)">رائج</span>@endif
        </div>
        <div class="product-card-actions">
            @auth
            <button class="product-card-action-btn {{ $isWishlisted ? 'wishlisted' : '' }}"
                    onclick="event.preventDefault();toggleWishlist({{ $product->id }},this)"
                    title="المفضلة">
                <i class="{{ $isWishlisted ? 'fas' : 'far' }} fa-heart"></i>
            </button>
            @endauth
            <a href="{{ route('store.product', $product->slug) }}" class="product-card-action-btn" title="عرض المنتج" style="display:flex">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </a>
    <div class="product-card-body">
        @if($product->brand)
        <div class="product-card-brand">{{ $product->brand->name }}</div>
        @endif
        <a href="{{ route('store.product', $product->slug) }}" class="product-card-name" style="display:-webkit-box">
            {{ $product->name }}
        </a>
        <div class="product-card-pricing">
            <span class="product-card-price">{{ number_format($price, 2) }} ر.س</span>
            @if($oldPrice)
            <span class="product-card-old-price">{{ number_format($oldPrice, 2) }} ر.س</span>
            @endif
        </div>
        @if(!$variant || $variant->stock_quantity > 0)
        <button class="product-card-add-btn"
                onclick="addToCart({{ $product->id }}, {{ $variant?->id ?? 'null' }}, 1, this)">
            <i class="fas fa-shopping-cart"></i> أضف للسلة
        </button>
        @else
        <div style="margin-top:12px;text-align:center;color:var(--text-muted);font-size:.82rem;font-weight:600">
            <i class="fas fa-times-circle"></i> نفد المخزون
        </div>
        @endif
    </div>
</div>
