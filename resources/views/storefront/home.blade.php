@extends('layouts.store')

@section('title', 'الرئيسية')

@section('content')

<!-- ══════════ HERO SECTION ══════════ -->
@php $banners = $company->storeSetting?->banners ?? []; @endphp
@if(count($banners))
<section class="hero-slider" id="heroSlider">
    @foreach($banners as $i => $banner)
    <div class="hero-slide {{ $i === 0 ? 'active' : '' }}" style="background-image: url('{{ asset('storage/'.$banner['image']) }}');">
        <div class="hero-overlay">
            <div class="container hero-content">
                @if(!empty($banner['title']))
                    <h1 class="hero-title animate-in">{{ $banner['title'] }}</h1>
                @endif
                @if(!empty($banner['button']))
                    <a href="{{ $banner['url'] ?? route('front.products', $company->slug) }}" class="hero-cta animate-in-delay">
                        {{ $banner['button'] }} <i class="fas fa-arrow-left"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    @if(count($banners) > 1)
    <div class="slider-dots">
        @foreach($banners as $i => $banner)
            <button class="dot {{ $i===0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></button>
        @endforeach
    </div>
    <button class="slider-arrow prev" onclick="changeSlide(-1)"><i class="fas fa-chevron-right"></i></button>
    <button class="slider-arrow next" onclick="changeSlide(1)"><i class="fas fa-chevron-left"></i></button>
    @endif
</section>
@else
{{-- Default hero when no banners --}}
<section class="hero-default">
    <div class="container" style="text-align:center; padding: 100px 20px;">
        <div class="hero-badge">🛍️ متجر {{ $company->company_name }}</div>
        <h1 class="hero-title-default">اكتشف أفضل منتجاتنا</h1>
        <p class="hero-subtitle-default">{{ $company->company_name }} — جودة تستحق الثقة</p>
        <div style="display:flex; gap:15px; justify-content:center; margin-top:35px; flex-wrap:wrap;">
            <a href="{{ route('front.products', $company->slug) }}" class="hero-cta">تسوق الآن <i class="fas fa-arrow-left"></i></a>
            @if($company->whatsapp)
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $company->whatsapp) }}" class="hero-cta-outline" target="_blank">
                    <i class="fab fa-whatsapp"></i> تواصل معنا
                </a>
            @endif
        </div>
    </div>
</section>
@endif

<!-- ══════════ STATS BAR ══════════ -->
<section class="stats-bar">
    <div class="container stats-bar-inner">
        <div class="stat-item"><i class="fas fa-truck-fast"></i><span>توصيل سريع</span></div>
        <div class="stat-item"><i class="fas fa-shield-halved"></i><span>جودة مضمونة</span></div>
        <div class="stat-item"><i class="fas fa-headset"></i><span>دعم على مدار الساعة</span></div>
        <div class="stat-item"><i class="fas fa-rotate-left"></i><span>إرجاع مجاني</span></div>
    </div>
</section>

<!-- ══════════ CATEGORIES ══════════ -->
@if($categories->count() > 0)
<section class="store-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">تسوق حسب الفئة</h2>
            <a href="{{ route('front.products', $company->slug) }}" class="section-link">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="categories-grid">
            @foreach($categories as $cat)
            <a href="{{ route('front.products', ['slug'=>$company->slug, 'category'=>$cat->id]) }}" class="category-card">
                <div class="cat-icon"><i class="fas fa-tag"></i></div>
                <span class="cat-name">{{ $cat->name }}</span>
                <span class="cat-count">{{ $cat->products_count }} منتج</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ══════════ FEATURED PRODUCTS ══════════ -->
<section class="store-section" style="background:var(--surface-2);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">أحدث المنتجات</h2>
            <a href="{{ route('front.products', $company->slug) }}" class="section-link">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>

        @if($featuredProducts->count())
        <div class="product-grid">
            @foreach($featuredProducts as $product)
                @include('storefront.partials.product-card', ['product'=>$product, 'company'=>$company])
            @endforeach
        </div>
        @else
        <div style="text-align:center; padding:60px 20px; color:var(--text-light);">
            <i class="fas fa-box-open" style="font-size:3.5rem; opacity:0.3; margin-bottom:15px;"></i>
            <h3>المنتجات قادمة قريباً</h3>
        </div>
        @endif
    </div>
</section>

<!-- ══════════ PRODUCTS WITH DISCOUNTS ══════════ -->
@if($discountedProducts->count())
<section class="store-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">🔥 عروض وخصومات</h2>
            <a href="{{ route('front.products', $company->slug) }}" class="section-link">عرض الكل <i class="fas fa-arrow-left"></i></a>
        </div>
        <div class="product-grid">
            @foreach($discountedProducts as $product)
                @include('storefront.partials.product-card', ['product'=>$product, 'company'=>$company, 'showDiscount'=>true])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ══════════ ABOUT STRIP ══════════ -->
<section class="about-strip">
    <div class="container about-strip-inner">
        <div>
            <h2 style="font-size:1.8rem; font-weight:800; margin-bottom:12px;">{{ $company->company_name }}</h2>
            <p style="color:var(--text-light); line-height:1.8; max-width:560px;">
                {{ $company->factory_address }}
            </p>
            <div style="display:flex; gap:15px; margin-top:20px; flex-wrap:wrap;">
                @if($company->hotline)
                    <a href="tel:{{ $company->hotline }}" class="contact-chip"><i class="fas fa-phone"></i> {{ $company->hotline }}</a>
                @endif
                @if($company->whatsapp)
                    <a href="https://wa.me/{{ preg_replace('/\D/','',$company->whatsapp) }}" class="contact-chip whatsapp-chip" target="_blank"><i class="fab fa-whatsapp"></i> واتساب</a>
                @endif
                @if($company->website)
                    <a href="{{ $company->website }}" class="contact-chip" target="_blank"><i class="fas fa-globe"></i> الموقع الرسمي</a>
                @endif
            </div>
        </div>
        <div style="text-align:center; flex-shrink:0;">
            @if($company->logo)
                <img src="{{ asset('storage/'.$company->logo) }}" alt="{{ $company->company_name }}" style="height:100px; object-fit:contain; filter:drop-shadow(0 4px 20px rgba(0,0,0,0.1));">
            @else
                <div style="width:100px;height:100px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:white;margin:0 auto;"> <i class="fas fa-store"></i></div>
            @endif
        </div>
    </div>
</section>

@push('styles')
<style>
/* ── Hero Slider ─────────────────────────────── */
.hero-slider { position:relative; height:520px; overflow:hidden; }
.hero-slide { position:absolute; inset:0; background-size:cover; background-position:center; opacity:0; transition:opacity .7s ease; }
.hero-slide.active { opacity:1; z-index:1; }
.hero-overlay { position:absolute; inset:0; background:linear-gradient(to left, rgba(0,0,0,.5), rgba(0,0,0,.15)); display:flex; align-items:center; }
.hero-content { text-align:right; }
.hero-title { font-size:3rem; font-weight:800; color:#fff; text-shadow:0 2px 20px rgba(0,0,0,.3); margin-bottom:20px; }
.hero-cta { display:inline-flex; align-items:center; gap:10px; background:var(--primary); color:#fff; padding:14px 32px; border-radius:50px; font-weight:700; font-size:1.05rem; transition:.3s; }
.hero-cta:hover { transform:translateY(-2px); opacity:.9; }
.hero-cta-outline { display:inline-flex; align-items:center; gap:10px; background:rgba(255,255,255,.15); backdrop-filter:blur(10px); color:#fff; border:1px solid rgba(255,255,255,.4); padding:14px 28px; border-radius:50px; font-weight:700; transition:.3s; }
.hero-cta-outline:hover { background:rgba(255,255,255,.25); }
.slider-dots { position:absolute; bottom:20px; left:50%; transform:translateX(-50%); display:flex; gap:8px; z-index:10; }
.dot { width:10px; height:10px; border-radius:50%; background:rgba(255,255,255,.5); border:none; cursor:pointer; transition:.3s; }
.dot.active, .dot:hover { background:#fff; transform:scale(1.2); }
.slider-arrow { position:absolute; top:50%; transform:translateY(-50%); z-index:10; background:rgba(255,255,255,.2); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.3); color:#fff; width:44px; height:44px; border-radius:50%; cursor:pointer; font-size:1rem; display:flex; align-items:center; justify-content:center; transition:.3s; }
.slider-arrow:hover { background:rgba(255,255,255,.35); }
.slider-arrow.prev { right:20px; }
.slider-arrow.next { left:20px; }

/* ── Default Hero (no banners) ────────────────── */
.hero-default { background:linear-gradient(135deg, var(--secondary) 0%, var(--bg) 100%); border-bottom:1px solid var(--border); }
.hero-badge { display:inline-flex; align-items:center; gap:8px; background:var(--primary); color:#fff; padding:6px 18px; border-radius:50px; font-size:.875rem; font-weight:700; margin-bottom:20px; }
.hero-title-default { font-size:3rem; font-weight:800; color:var(--text); margin-bottom:15px; }
.hero-subtitle-default { font-size:1.2rem; color:var(--text-light); }

/* ── Stats Bar ────────────────────────────────── */
.stats-bar { background:var(--primary); color:#fff; padding:16px 0; }
.stats-bar-inner { display:flex; gap:30px; justify-content:center; flex-wrap:wrap; }
.stat-item { display:flex; align-items:center; gap:8px; font-size:.9rem; font-weight:600; }
.stat-item i { font-size:1.1rem; }

/* ── Sections ─────────────────────────────────── */
.store-section { padding:70px 0; }
.section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:35px; }
.section-title { font-size:1.9rem; font-weight:800; color:var(--text); position:relative; }
.section-title::after { content:''; position:absolute; bottom:-8px; right:0; width:50px; height:4px; background:var(--primary); border-radius:2px; }
.section-link { color:var(--primary); font-weight:700; font-size:.95rem; }
.section-link:hover { text-decoration:underline; }

/* ── Categories ───────────────────────────────── */
.categories-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:16px; }
.category-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px 15px; text-align:center; text-decoration:none; transition:.3s; display:flex; flex-direction:column; align-items:center; gap:8px; }
.category-card:hover { border-color:var(--primary); transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.07); }
.cat-icon { width:48px;height:48px;border-radius:50%;background:var(--secondary);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.2rem; }
.cat-name { font-weight:700; font-size:.9rem; color:var(--text); }
.cat-count { font-size:.75rem; color:var(--text-light); }

/* ── About Strip ──────────────────────────────── */
.about-strip { background:var(--surface); border-top:1px solid var(--border); padding:60px 0; }
.about-strip-inner { display:flex; align-items:center; justify-content:space-between; gap:40px; flex-wrap:wrap; }
.contact-chip { display:inline-flex; align-items:center; gap:8px; background:var(--secondary); color:var(--primary); padding:8px 18px; border-radius:50px; font-weight:600; font-size:.9rem; transition:.2s; }
.contact-chip:hover { opacity:.85; }
.whatsapp-chip { background:#D1FAE5; color:#065F46; }

@media(max-width:768px) {
    .hero-slider { height:360px; }
    .hero-title { font-size:2rem; }
    .hero-title-default { font-size:2rem; }
    .store-section { padding:50px 0; }
}
</style>
@endpush

@push('scripts')
<script>
// ── Hero Slider ──────────────────────────────────
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots   = document.querySelectorAll('.dot');

function goToSlide(n) {
    slides[currentSlide]?.classList.remove('active');
    dots[currentSlide]?.classList.remove('active');
    currentSlide = (n + slides.length) % slides.length;
    slides[currentSlide]?.classList.add('active');
    dots[currentSlide]?.classList.add('active');
}
function changeSlide(dir) { goToSlide(currentSlide + dir); }

if (slides.length > 1) {
    setInterval(() => changeSlide(1), 5000);
}
</script>
@endpush

@endsection
