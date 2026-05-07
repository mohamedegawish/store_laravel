@php $variant = $product->variants->first(); @endphp
@php $price = $variant ? ($variant->price - ($variant->discount ?? 0)) : null; @endphp
@php $showDiscount = $showDiscount ?? false; @endphp

<a href="{{ route('front.product', ['slug' => $company->slug, 'product' => $product->id]) }}" class="product-card">

    {{-- Smart Badges --}}
    @if($company->storeSetting?->show_trending_badges && isset($product->total_sold) && $product->total_sold > 0)
        <span class="badge-tag" style="background:#EF4444;"><i class="fas fa-fire"></i> الأكثر مبيعاً</span>
    @elseif($company->storeSetting?->show_low_stock_badges && $variant && $variant->stock_quantity > 0 && $variant->stock_quantity <= 5)
        <span class="badge-tag badge-tag-warning"><i class="fas fa-hourglass-half"></i> تبقى {{ $variant->stock_quantity }}</span>
    @endif

    @if($variant && $variant->discount > 0)
        <span class="discount-badge">-{{ round(($variant->discount / $variant->price) * 100) }}%</span>
    @endif

    {{-- Image --}}
    <div class="product-img-wrap">
        @if($product->images && count($product->images))
            <img src="{{ asset('storage/'.$product->images[0]) }}" alt="{{ $product->name }}" class="product-img" loading="lazy">
        @else
            <div class="product-img" style="display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:var(--primary);opacity:.35;">
                <i class="fas fa-image"></i>
            </div>
        @endif

        {{-- Quick add overlay --}}
        @auth
        <div class="product-overlay">
            <button class="quick-add-btn" onclick="event.preventDefault(); addToCart({{ $variant?->id }}, this)">
                <i class="fas fa-cart-plus"></i> أضف للسلة
            </button>
        </div>
        @endauth
    </div>

    {{-- Info --}}
    <div class="product-info">
        <div class="product-category">{{ $product->category?->name ?? 'عام' }}</div>
        <h3 class="product-title">{{ $product->name }}</h3>
        <div class="product-price-row">
            @if($price !== null)
                <span class="product-price">{{ number_format($price, 2) }} ج.م</span>
                @if($variant->discount > 0)
                    <span class="product-price-old">{{ number_format($variant->price, 2) }}</span>
                @endif
            @else
                <span style="color:var(--text-light); font-size:.9rem;">غير متوفر</span>
            @endif

            @if($variant && $variant->stock_quantity == 0)
                <span style="margin-right:auto; font-size:.75rem; color:var(--text-light); background:var(--surface-2); padding:2px 8px; border-radius:4px;">نفذت الكمية</span>
            @endif
        </div>
    </div>
</a>
