@extends('layouts.store')
@section('title', $product->name)
@section('meta_description', $product->short_description ?? $product->meta_description)

@section('content')
<div class="container" style="padding-top:24px;padding-bottom:60px">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        <a href="{{ route('store.home') }}">الرئيسية</a>
        <span class="sep">/</span>
        <a href="{{ route('store.products') }}">المنتجات</a>
        <span class="sep">/</span>
        @if($product->category)
        <a href="{{ route('store.products', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
        <span class="sep">/</span>
        @endif
        <span>{{ $product->name }}</span>
    </nav>

    {{-- ── Main Product Grid ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start;margin-bottom:60px" id="productMain">

        {{-- Gallery --}}
        <div id="gallery">
            <div style="aspect-ratio:1;border-radius:var(--radius);overflow:hidden;background:var(--surface);margin-bottom:12px;border:1px solid var(--border)">
                <img id="mainImage" src="{{ $product->main_image ? asset('storage/'.$product->main_image) : 'https://via.placeholder.com/600x600?text=No+Image' }}"
                     alt="{{ $product->name }}"
                     style="width:100%;height:100%;object-fit:cover;transition:opacity .3s;cursor:zoom-in"
                     onclick="openLightbox(this.src)">
            </div>
            @if($product->images && count($product->images) > 1)
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                @foreach($product->images as $i => $img)
                <div style="width:72px;height:72px;border-radius:var(--radius-sm);overflow:hidden;border:2px solid {{ $i===0?'var(--primary)':'var(--border)' }};cursor:pointer;flex-shrink:0"
                     onclick="switchImage('{{ asset('storage/'.$img) }}', this)">
                    <img src="{{ asset('storage/'.$img) }}" alt="" style="width:100%;height:100%;object-fit:cover">
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Info --}}
        <div>
            @if($product->brand)
            <div style="font-size:.82rem;font-weight:600;color:var(--primary);text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">
                {{ $product->brand->name }}
            </div>
            @endif

            <h1 style="font-size:1.6rem;font-weight:900;line-height:1.3;margin-bottom:12px">{{ $product->name }}</h1>

            {{-- Rating --}}
            @if($product->approvedReviews->count())
            @php $avg = $product->approvedReviews->avg('rating'); @endphp
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="color:#F59E0B">
                    @for($i=1;$i<=5;$i++)
                    <i class="{{ $i <= round($avg) ? 'fas' : 'far' }} fa-star"></i>
                    @endfor
                </div>
                <span style="font-size:.85rem;color:var(--text-muted)">({{ $product->approvedReviews->count() }} تقييم)</span>
            </div>
            @endif

            {{-- Price --}}
            <div id="priceBlock" style="margin-bottom:24px">
                @php
                    $defaultVariant = $product->defaultVariant ?? $product->activeVariants->first();
                    $currentPrice   = $defaultVariant?->effective_price ?? $product->price ?? 0;
                    $oldPrice       = ($defaultVariant?->hasActiveOffer() && $defaultVariant->price > $currentPrice) ? $defaultVariant->price : null;
                @endphp
                <span id="currentPrice" style="font-size:2rem;font-weight:900;color:var(--primary)">
                    {{ number_format($currentPrice, 2) }} ر.س
                </span>
                @if($oldPrice)
                <span id="oldPrice" style="font-size:1.1rem;text-decoration:line-through;color:var(--text-muted);margin-inline-start:10px">
                    {{ number_format($oldPrice, 2) }} ر.س
                </span>
                <span style="background:var(--danger);color:#fff;font-size:.8rem;font-weight:700;padding:3px 10px;border-radius:99px;margin-inline-start:8px">
                    وفر {{ round((($oldPrice - $currentPrice) / $oldPrice) * 100) }}%
                </span>
                @endif
            </div>

            @if($product->short_description)
            <p style="color:var(--text-muted);font-size:.92rem;line-height:1.7;margin-bottom:24px">
                {{ $product->short_description }}
            </p>
            @endif

            {{-- Variants --}}
            @if($product->activeVariants->count() > 1)
            <div style="margin-bottom:24px">
                <div style="font-weight:700;font-size:.88rem;margin-bottom:12px">الاختيار:</div>
                <div style="display:flex;gap:8px;flex-wrap:wrap" id="variantOptions">
                    @foreach($product->activeVariants as $variant)
                    <button type="button"
                            data-variant-id="{{ $variant->id }}"
                            data-price="{{ $variant->effective_price }}"
                            data-old-price="{{ $variant->hasActiveOffer() && $variant->price > $variant->effective_price ? $variant->price : '' }}"
                            data-stock="{{ $variant->stock_quantity }}"
                            onclick="selectVariant(this)"
                            style="padding:8px 18px;border-radius:var(--radius-sm);border:1.5px solid {{ ($variant->id == $defaultVariant?->id) ? 'var(--primary)' : 'var(--border)' }};background:{{ ($variant->id == $defaultVariant?->id) ? 'var(--primary-light)' : '#fff' }};font-weight:600;font-size:.85rem;cursor:pointer;font-family:var(--font);transition:all .2s;color:{{ ($variant->id == $defaultVariant?->id) ? 'var(--primary)' : 'var(--text)' }}">
                        {{ $variant->name }}
                        @if($variant->stock_quantity <= 0)
                        <span style="font-size:.72rem;color:var(--text-muted)">(نفد)</span>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quantity + Add to Cart --}}
            <div style="display:flex;gap:14px;align-items:center;margin-bottom:20px;flex-wrap:wrap">
                <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                    <button type="button" onclick="changeQty(-1)" style="width:40px;height:46px;background:var(--surface);border:none;cursor:pointer;font-size:1.1rem;font-weight:700">−</button>
                    <input type="number" id="qtyInput" value="1" min="1" max="99"
                           style="width:50px;height:46px;border:none;border-left:1px solid var(--border);border-right:1px solid var(--border);text-align:center;font-size:1rem;font-weight:700;font-family:var(--font);outline:none">
                    <button type="button" onclick="changeQty(1)" style="width:40px;height:46px;background:var(--surface);border:none;cursor:pointer;font-size:1.1rem;font-weight:700">+</button>
                </div>
                <button id="addToCartBtn" class="btn btn-primary" style="flex:1;height:46px;font-size:.95rem"
                        onclick="addToCartFromDetail()">
                    <i class="fas fa-shopping-cart"></i> أضف للسلة
                </button>
                @auth
                <button class="btn btn-ghost" style="width:46px;height:46px;padding:0" title="المفضلة"
                        id="wishlistBtn"
                        onclick="toggleWishlist({{ $product->id }}, this)">
                    <i class="{{ auth()->user()->wishlist->contains('product_id',$product->id) ? 'fas' : 'far' }} fa-heart"
                       style="{{ auth()->user()->wishlist->contains('product_id',$product->id) ? 'color:var(--danger)' : '' }}"></i>
                </button>
                @endauth
            </div>

            {{-- Stock indicator --}}
            <div id="stockIndicator" style="font-size:.85rem;margin-bottom:20px">
                @if($defaultVariant && $defaultVariant->stock_quantity > 0)
                <span style="color:var(--success)"><i class="fas fa-check-circle"></i> متوفر في المخزون ({{ $defaultVariant->stock_quantity }} قطعة)</span>
                @elseif(!$defaultVariant && $product->stock_quantity > 0)
                <span style="color:var(--success)"><i class="fas fa-check-circle"></i> متوفر في المخزون</span>
                @else
                <span style="color:var(--danger)"><i class="fas fa-times-circle"></i> نفد المخزون</span>
                @endif
            </div>

            {{-- Meta --}}
            <div style="padding-top:20px;border-top:1px solid var(--border);font-size:.85rem;color:var(--text-muted);display:flex;flex-direction:column;gap:6px">
                @if($product->sku)<div><strong>SKU:</strong> {{ $product->sku }}</div>@endif
                @if($product->category)<div><strong>التصنيف:</strong> <a href="{{ route('store.products', ['category'=>$product->category->slug]) }}" style="color:var(--primary)">{{ $product->category->name }}</a></div>@endif
                @if($product->brand)<div><strong>البراند:</strong> {{ $product->brand->name }}</div>@endif
            </div>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div style="margin-bottom:60px">
        <div style="display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:28px">
            @foreach([['desc','الوصف'],['specs','المواصفات'],['reviews','التقييمات ('.$product->approvedReviews->count().')']] as [$tab,$label])
            <button onclick="switchTab('{{ $tab }}')" id="tab-btn-{{ $tab }}"
                    style="padding:12px 28px;font-weight:700;font-size:.9rem;border:none;background:none;cursor:pointer;font-family:var(--font);border-bottom:3px solid {{ $tab==='desc'?'var(--primary)':'transparent' }};margin-bottom:-2px;color:{{ $tab==='desc'?'var(--primary)':'var(--text-muted)' }};transition:all .2s">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <div id="tab-desc" class="tab-content">
            <div style="line-height:1.9;font-size:.95rem;max-width:800px">
                {!! nl2br(e($product->description ?? 'لا يوجد وصف تفصيلي لهذا المنتج.')) !!}
            </div>
        </div>

        <div id="tab-specs" class="tab-content" style="display:none">
            @if($product->weight || $product->unit_label)
            <div style="max-width:600px">
                <table style="width:100%;border-collapse:collapse;font-size:.9rem">
                    @if($product->sku)<tr style="border-bottom:1px solid var(--border)"><td style="padding:12px 0;font-weight:600;width:200px">SKU</td><td style="padding:12px 0;color:var(--text-muted)">{{ $product->sku }}</td></tr>@endif
                    @if($product->weight)<tr style="border-bottom:1px solid var(--border)"><td style="padding:12px 0;font-weight:600">الوزن</td><td style="padding:12px 0;color:var(--text-muted)">{{ $product->weight }} كجم</td></tr>@endif
                    @if($product->unit_label)<tr style="border-bottom:1px solid var(--border)"><td style="padding:12px 0;font-weight:600">الوحدة</td><td style="padding:12px 0;color:var(--text-muted)">{{ $product->unit_label }}</td></tr>@endif
                    @if($product->brand)<tr style="border-bottom:1px solid var(--border)"><td style="padding:12px 0;font-weight:600">البراند</td><td style="padding:12px 0;color:var(--text-muted)">{{ $product->brand->name }}</td></tr>@endif
                </table>
            </div>
            @else
            <p style="color:var(--text-muted)">لا توجد مواصفات إضافية.</p>
            @endif
        </div>

        <div id="tab-reviews" class="tab-content" style="display:none">
            @if($product->approvedReviews->count())
            <div style="max-width:700px;display:flex;flex-direction:column;gap:20px;margin-bottom:40px">
                @foreach($product->approvedReviews as $review)
                <div style="background:var(--surface);border-radius:var(--radius);padding:20px">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px">
                        <div>
                            <div style="font-weight:700;font-size:.92rem">{{ $review->user?->name ?? 'عميل' }}</div>
                            <div style="color:#F59E0B;font-size:.85rem">
                                @for($i=1;$i<=5;$i++)<i class="{{ $i<=$review->rating?'fas':'far' }} fa-star"></i>@endfor
                            </div>
                        </div>
                        <div style="font-size:.78rem;color:var(--text-muted)">{{ $review->created_at->format('d/m/Y') }}</div>
                    </div>
                    @if($review->title)<div style="font-weight:600;margin-bottom:6px">{{ $review->title }}</div>@endif
                    @if($review->body)<p style="font-size:.9rem;color:var(--text-muted);line-height:1.7">{{ $review->body }}</p>@endif
                </div>
                @endforeach
            </div>
            @else
            <p style="color:var(--text-muted);margin-bottom:24px">لا توجد تقييمات بعد. كن أول من يقيّم!</p>
            @endif

            @auth
            <div style="background:var(--surface);border-radius:var(--radius);padding:24px;max-width:600px">
                <h4 style="font-weight:700;margin-bottom:16px">أضف تقييمك</h4>
                <form method="POST" action="{{ route('store.product.review', $product) }}">
                    @csrf
                    <div style="margin-bottom:16px">
                        <div style="font-size:.85rem;font-weight:600;margin-bottom:8px">التقييم</div>
                        <div style="display:flex;gap:8px">
                            @for($i=1;$i<=5;$i++)
                            <label style="cursor:pointer;font-size:1.4rem;color:#E2E8F0" class="star-label">
                                <input type="radio" name="rating" value="{{ $i }}" style="display:none">
                                <i class="fas fa-star"></i>
                            </label>
                            @endfor
                        </div>
                    </div>
                    <div style="margin-bottom:12px">
                        <input type="text" name="title" placeholder="عنوان التقييم"
                               style="width:100%;height:40px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    </div>
                    <div style="margin-bottom:16px">
                        <textarea name="body" rows="3" placeholder="اكتب تقييمك هنا..."
                                  style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;font-family:var(--font);font-size:.88rem;resize:vertical;outline:none"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">إرسال التقييم</button>
                </form>
            </div>
            @else
            <div style="padding:20px;background:var(--surface);border-radius:var(--radius);max-width:400px;text-align:center">
                <p style="margin-bottom:12px;font-size:.88rem;color:var(--text-muted)">سجّل دخولك لإضافة تقييم</p>
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">تسجيل الدخول</a>
            </div>
            @endauth
        </div>
    </div>

    {{-- ── Related Products ── --}}
    @if($relatedProducts->count())
    <div>
        <div class="section-heading"><h2>منتجات مشابهة</h2></div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:20px">
            @foreach($relatedProducts as $product)
            @include('store.partials.product-card', compact('product'))
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Lightbox --}}
<div id="lightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.9);z-index:9000;align-items:center;justify-content:center;cursor:pointer" onclick="closeLightbox()">
    <img id="lightboxImg" src="" alt="" style="max-width:90vw;max-height:90vh;border-radius:12px;object-fit:contain">
    <button onclick="closeLightbox()" style="position:absolute;top:20px;inset-inline-end:20px;background:rgba(255,255,255,.2);border:none;color:#fff;font-size:1.5rem;width:44px;height:44px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center"><i class="fas fa-times"></i></button>
</div>

@push('scripts')
<script>
let selectedVariantId = {{ $defaultVariant?->id ?? 'null' }};

function switchImage(src, thumb) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('#gallery .thumbnail-wrap').forEach(t => t.style.borderColor = 'var(--border)');
    if (thumb) thumb.style.borderColor = 'var(--primary)';
}

function openLightbox(src) {
    const lb = document.getElementById('lightbox');
    document.getElementById('lightboxImg').src = src;
    lb.style.display = 'flex';
}
function closeLightbox() { document.getElementById('lightbox').style.display = 'none'; }

function selectVariant(btn) {
    document.querySelectorAll('#variantOptions button').forEach(b => {
        b.style.borderColor = 'var(--border)';
        b.style.background  = '#fff';
        b.style.color       = 'var(--text)';
    });
    btn.style.borderColor = 'var(--primary)';
    btn.style.background  = 'var(--primary-light)';
    btn.style.color       = 'var(--primary)';

    selectedVariantId = parseInt(btn.dataset.variantId);
    const price    = parseFloat(btn.dataset.price);
    const oldPrice = parseFloat(btn.dataset.oldPrice);
    const stock    = parseInt(btn.dataset.stock);

    document.getElementById('currentPrice').textContent = price.toFixed(2) + ' ر.س';
    const oldPriceEl = document.getElementById('oldPrice');
    if (oldPriceEl) oldPriceEl.textContent = oldPrice ? oldPrice.toFixed(2) + ' ر.س' : '';

    const si = document.getElementById('stockIndicator');
    if (stock > 0) {
        si.innerHTML = `<span style="color:var(--success)"><i class="fas fa-check-circle"></i> متوفر (${stock} قطعة)</span>`;
        document.getElementById('addToCartBtn').disabled = false;
    } else {
        si.innerHTML = `<span style="color:var(--danger)"><i class="fas fa-times-circle"></i> نفد المخزون</span>`;
        document.getElementById('addToCartBtn').disabled = true;
    }
}

function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    input.value = Math.max(1, parseInt(input.value) + delta);
}

function addToCartFromDetail() {
    const qty = parseInt(document.getElementById('qtyInput').value);
    const btn = document.getElementById('addToCartBtn');
    addToCart({{ $product->id }}, selectedVariantId, qty, btn);
}

// Star rating hover
document.querySelectorAll('.star-label').forEach((label, idx) => {
    label.addEventListener('mouseenter', () => {
        document.querySelectorAll('.star-label i').forEach((s, i) => {
            s.style.color = i <= idx ? '#F59E0B' : '#E2E8F0';
        });
    });
    label.addEventListener('click', () => {
        document.querySelectorAll('.star-label i').forEach((s, i) => {
            s.style.color = i <= idx ? '#F59E0B' : '#E2E8F0';
        });
    });
});

function switchTab(name) {
    ['desc','specs','reviews'].forEach(t => {
        const content = document.getElementById('tab-' + t);
        const btn     = document.getElementById('tab-btn-' + t);
        content.style.display = t === name ? '' : 'none';
        btn.style.borderBottomColor = t === name ? 'var(--primary)' : 'transparent';
        btn.style.color = t === name ? 'var(--primary)' : 'var(--text-muted)';
    });
}
</script>
@endpush

@push('styles')
<style>
@media(max-width:768px){
    #productMain { grid-template-columns:1fr!important; }
}
</style>
@endpush
@endsection
