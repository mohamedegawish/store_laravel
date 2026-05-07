@extends('layouts.store')

@section('title', $product->name)

@section('content')

@php 
    $variant = $product->variants->first(); 
    $price = $variant ? ($variant->price - ($variant->discount ?? 0)) : null;
@endphp

<div class="container" style="padding-top:40px; padding-bottom:60px;">
    
    {{-- Breadcrumb --}}
    <div style="margin-bottom:24px; font-size:0.9rem; color:var(--text-light); display:flex; gap:8px;">
        <a href="{{ route('front.home', $company->slug) }}" style="hover:color:var(--primary);">الرئيسية</a>
        <span><i class="fas fa-chevron-left" style="font-size:0.7rem;"></i></span>
        <a href="{{ route('front.products', ['slug'=>$company->slug, 'category'=>$product->category_id]) }}" style="hover:color:var(--primary);">{{ $product->category?->name ?? 'منتجات' }}</a>
        <span><i class="fas fa-chevron-left" style="font-size:0.7rem;"></i></span>
        <span style="color:var(--text); font-weight:600;">{{ $product->name }}</span>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:50px; align-items:start;" class="product-details-grid">
        
        {{-- ── IMAGES GALLERY ── --}}
        <div class="product-gallery">
            <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; position:relative; aspect-ratio:1;">
                @if($variant && $variant->discount > 0)
                    <span class="discount-badge" style="top:20px; left:20px; font-size:1rem; padding:6px 14px;">خصم {{ round(($variant->discount / $variant->price) * 100) }}%</span>
                @endif
                
                @if($product->images && count($product->images))
                    <img id="mainImage" src="{{ asset('storage/'.$product->images[0]) }}" style="width:100%;height:100%;object-fit:cover;transition:.3s;">
                @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text-3);background:var(--surface-2);">
                        <i class="fas fa-image" style="font-size:5rem;"></i>
                    </div>
                @endif
            </div>
            
            @if($product->images && count($product->images) > 1)
            <div style="display:flex; gap:12px; margin-top:16px; overflow-x:auto; padding-bottom:8px;" class="thumbs-scroll">
                @foreach($product->images as $i => $img)
                    <div class="thumb-box {{ $i===0?'active':'' }}" onclick="changeImage('{{ asset('storage/'.$img) }}', this)" 
                         style="width:90px;height:90px;border-radius:var(--radius-md);border:2px solid {{ $i===0?'var(--primary)':'transparent' }};overflow:hidden;cursor:pointer;flex-shrink:0;transition:.2s;opacity:{{ $i===0?'1':'.6' }};">
                        <img src="{{ asset('storage/'.$img) }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── PRODUCT INFO & ADD TO CART ── --}}
        <div class="product-info-panel">
            <h1 style="font-size:2.2rem; font-weight:800; line-height:1.3; margin-bottom:12px;">{{ $product->name }}</h1>
            
            <div style="display:flex; align-items:center; gap:20px; margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid var(--border);">
                @if($price !== null)
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span style="font-size:2rem; font-weight:800; color:var(--primary);">{{ number_format($price, 2) }} ج.م</span>
                        @if($variant->discount > 0)
                            <span style="font-size:1.1rem; color:var(--text-light); text-decoration:line-through;">{{ number_format($variant->price, 2) }}</span>
                        @endif
                    </div>
                @else
                    <span style="font-size:1.5rem; color:var(--text-light);">غير متوفر حالياً</span>
                @endif

                @if($variant)
                    @if($variant->stock_quantity == 0)
                        <span class="badge badge-danger">نفذت الكمية</span>
                    @elseif($variant->stock_quantity <= 5)
                        <span class="badge badge-warning" style="background:#F59E0B; color:#fff;">تبقى {{ $variant->stock_quantity }} فقط! اطلب الآن</span>
                    @endif
                @endif
            </div>

            <div style="font-size:1.05rem; line-height:1.8; color:var(--text-2); margin-bottom:30px; white-space:pre-wrap;">{{ $product->description }}</div>

            @if($variant && $variant->stock_quantity > 0)
                @auth
                <div style="background:var(--surface); border:1px solid var(--border); padding:24px; border-radius:var(--radius-lg); box-shadow:var(--shadow-sm);">
                    <div style="margin-bottom:20px;">
                        <label style="font-weight:700; margin-bottom:8px; display:block;">الكمية</label>
                        <div style="display:flex; align-items:center; width:140px; border:1px solid var(--border); border-radius:50px; overflow:hidden;">
                            <button type="button" onclick="updateQty(-1)" style="flex:1; background:var(--surface-2); border:none; height:44px; cursor:pointer; font-size:1.2rem; transition:.2s;">-</button>
                            <input type="number" id="qtyInput" value="1" min="1" max="{{ $variant->stock_quantity }}" style="width:50px; text-align:center; border:none; background:transparent; font-weight:700; font-size:1.1rem; outline:none;" readonly>
                            <button type="button" onclick="updateQty(1)" style="flex:1; background:var(--surface-2); border:none; height:44px; cursor:pointer; font-size:1.2rem; transition:.2s;">+</button>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary btn-lg" style="width:100%; display:flex; justify-content:center; gap:12px; font-size:1.15rem;" onclick="addSingleProductToCart({{ $variant->id }})">
                        <i class="fas fa-cart-shopping"></i> إضافة إلى السلة
                    </button>
                    
                    <div id="cartMessage" style="display:none; margin-top:15px; padding:12px; border-radius:var(--radius-md); background:#D1FAE5; color:#065F46; text-align:center; font-weight:600; font-size:0.95rem;">
                        <i class="fas fa-check-circle"></i> تمت إضافة المنتج بنجاح! <a href="{{ route('front.cart', $company->slug) }}" style="text-decoration:underline; font-weight:700;">عرض السلة</a>
                    </div>
                </div>
                @else
                <div style="background:var(--surface-2); padding:24px; border-radius:var(--radius-lg); text-align:center; border:1px dashed var(--border);">
                    <i class="fas fa-user-lock" style="font-size:2.5rem; color:var(--text-3); margin-bottom:15px;"></i>
                    <h3 style="font-weight:700; margin-bottom:8px;">يرجى تسجيل الدخول للشراء</h3>
                    <p style="color:var(--text-light); font-size:0.9rem; margin-bottom:20px;">يجب عليك تسجيل الدخول أو إنشاء حساب جديد لتتمكن من إضافة المنتجات للسلة وإتمام الطلب.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary"><i class="fas fa-user"></i> تسجيل الدخول الآن</a>
                </div>
                @endauth
            @endif

            <div style="margin-top:30px; display:flex; flex-direction:column; gap:15px; border-top:1px solid var(--border); padding-top:30px;">
                <div style="display:flex; align-items:center; gap:12px; color:var(--text-2);">
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--surface-2);display:flex;align-items:center;justify-content:center;color:var(--primary);"><i class="fas fa-truck-fast"></i></div>
                    <div>
                        <div style="font-weight:700; color:var(--text);">توصيل سريع وموثوق</div>
                        <div style="font-size:0.85rem;">خيارات شحن متعددة للوصول إليك أينما كنت.</div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:12px; color:var(--text-2);">
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--surface-2);display:flex;align-items:center;justify-content:center;color:var(--primary);"><i class="fas fa-shield-halved"></i></div>
                    <div>
                        <div style="font-weight:700; color:var(--text);">جودة المتجر مضمونة</div>
                        <div style="font-size:0.85rem;">منتجات أصلية وبأفضل المعايير.</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- ── RELATED PRODUCTS ── --}}
    @if($relatedProducts->count())
    <div style="margin-top:80px;">
        <h3 style="font-size:1.6rem; font-weight:800; margin-bottom:30px; display:flex; align-items:center; gap:15px;">
            منتجات مشابهة 
            <div style="flex:1; height:1px; background:var(--border);"></div>
        </h3>
        
        <div class="product-grid">
            @foreach($relatedProducts as $relProduct)
                @include('storefront.partials.product-card', ['product'=>$relProduct, 'company'=>$company, 'showDiscount'=>true])
            @endforeach
        </div>
    </div>
    @endif

</div>

@endsection

@push('styles')
<style>
@media(max-width:900px) {
    .product-details-grid { grid-template-columns: 1fr !important; gap:30px !important; }
}
.thumbs-scroll::-webkit-scrollbar { height:6px; }
.thumbs-scroll::-webkit-scrollbar-thumb { background:var(--border); border-radius:3px; }
.thumb-box:hover { opacity:1 !important; border-color:var(--border) !important; }
.thumb-box.active { opacity:1 !important; border-color:var(--primary) !important; }
</style>
@endpush

@push('scripts')
<script>
function changeImage(src, el) {
    document.getElementById('mainImage').style.opacity = 0;
    setTimeout(() => {
        document.getElementById('mainImage').src = src;
        document.getElementById('mainImage').style.opacity = 1;
    }, 200);
    
    document.querySelectorAll('.thumb-box').forEach(t => {
        t.classList.remove('active');
        t.style.borderColor = 'transparent';
        t.style.opacity = '.6';
    });
    el.classList.add('active');
    el.style.borderColor = 'var(--primary)';
    el.style.opacity = '1';
}

function updateQty(change) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value) + change;
    const max = parseInt(input.max);
    if(val < 1) val = 1;
    if(val > max) val = max;
    input.value = val;
}

// Placeholder for Add to Cart
function addSingleProductToCart(variantId) {
    const qty = document.getElementById('qtyInput').value;
    const msg = document.getElementById('cartMessage');
    
    // In real scenario, make AJAX call to add to cart route with qty.
    // Here we simulate success.
    msg.style.display = 'block';
    msg.style.opacity = '0';
    setTimeout(()=> msg.style.opacity = '1', 10);
    
    // Increment cart badge
    const badge = document.querySelector('.cart-badge');
    if(badge) {
        badge.innerText = parseInt(badge.innerText) + parseInt(qty);
        badge.style.transform = 'scale(1.3)';
        setTimeout(()=> badge.style.transform = 'scale(1)', 300);
    }
}
</script>
@endpush
