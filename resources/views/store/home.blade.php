@extends('layouts.store')
@section('title', ($settings?->store_name ?? config('app.name')) . ' — ' . ($settings?->hero_title_ar ?? 'تسوق بثقة'))

@section('content')
@php $settings = app(\App\Models\StoreSetting::class)::current(); @endphp

{{-- ── Hero ── --}}
@php $heroBanner = $banners->where('position','hero')->first(); @endphp
<section style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark, #4f2d9e) 100%); color:#fff; overflow:hidden; position:relative;">
    @if($heroBanner?->image)
    <img src="{{ asset('storage/'.$heroBanner->image) }}" alt=""
         style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.25;">
    @endif
    <div class="container" style="position:relative;padding-top:80px;padding-bottom:80px">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center">
            <div>
                <div style="font-size:.85rem;font-weight:600;background:rgba(255,255,255,.15);display:inline-block;padding:4px 14px;border-radius:99px;margin-bottom:16px">
                    {{ $settings?->hero_subtitle_ar ?? 'أفضل المنتجات بأفضل الأسعار' }}
                </div>
                <h1 style="font-size:clamp(2rem,5vw,3rem);font-weight:900;line-height:1.2;margin-bottom:20px">
                    {{ $settings?->hero_title_ar ?? 'تسوق بثقة وجودة مضمونة' }}
                </h1>
                <p style="font-size:1.05rem;opacity:.85;margin-bottom:32px;line-height:1.7">
                    {{ $heroBanner?->subtitle_ar ?? 'اكتشف آلاف المنتجات المميزة بأسعار تنافسية مع ضمان الجودة والتوصيل السريع.' }}
                </p>
                <div style="display:flex;gap:14px;flex-wrap:wrap">
                    <a href="{{ route('store.products') }}" class="btn" style="background:#fff;color:var(--primary);font-size:1rem;padding:13px 30px">
                        <i class="fas fa-shopping-bag"></i> تسوق الآن
                    </a>
                    <a href="{{ route('store.products', ['sort'=>'popular']) }}" class="btn btn-outline" style="border-color:rgba(255,255,255,.5);color:#fff;font-size:1rem;padding:13px 30px">
                        الأكثر مبيعاً
                    </a>
                </div>
            </div>
            <div style="display:flex;justify-content:center;align-items:center">
                @if($heroBanner?->image)
                <img src="{{ asset('storage/'.$heroBanner->image) }}" alt="{{ $heroBanner->title_ar }}"
                     style="max-height:380px;border-radius:20px;box-shadow:0 30px 80px rgba(0,0,0,.3)">
                @else
                <div style="width:320px;height:280px;background:rgba(255,255,255,.12);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:5rem;opacity:.5">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ── Stats Bar ── --}}
<section style="background:var(--surface);border-bottom:1px solid var(--border)">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);text-align:center;padding:28px 0">
            @foreach([['fas fa-truck','شحن مجاني','للطلبات فوق '.number_format($settings?->free_shipping_threshold ?? 200,0).' ر.س'],['fas fa-shield-alt','ضمان الجودة','على جميع المنتجات'],['fas fa-headset','دعم 24/7','نحن دائماً هنا'],['fas fa-undo-alt','إرجاع مجاني','خلال 14 يوم']] as [$icon,$title,$sub])
            <div style="padding:16px;border-inline-end:1px solid var(--border)">
                <i class="{{ $icon }}" style="font-size:1.6rem;color:var(--primary);margin-bottom:8px"></i>
                <div style="font-weight:700;font-size:.92rem">{{ $title }}</div>
                <div style="font-size:.8rem;color:var(--text-muted)">{{ $sub }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Categories ── --}}
@if($categories->count())
<section style="padding:60px 0 0">
    <div class="container">
        <div class="section-heading">
            <h2>تسوق حسب التصنيف</h2>
            <a href="{{ route('store.products') }}">عرض الكل <i class="fas fa-arrow-left" style="font-size:.8rem"></i></a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:16px">
            @foreach($categories as $cat)
            <a href="{{ route('store.products', ['category' => $cat->slug]) }}"
               style="display:flex;flex-direction:column;align-items:center;gap:10px;padding:20px 10px;border-radius:var(--radius);border:1.5px solid var(--border);transition:all .2s;text-align:center"
               onmouseover="this.style.borderColor='var(--primary)';this.style.background='var(--primary-light)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.background='#fff'">
                @if($cat->image)
                <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}"
                     style="width:54px;height:54px;border-radius:50%;object-fit:cover">
                @else
                <div style="width:54px;height:54px;border-radius:50%;background:var(--primary-light);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.3rem">
                    <i class="{{ $cat->icon ?? 'fas fa-tag' }}"></i>
                </div>
                @endif
                <span style="font-size:.82rem;font-weight:600">{{ $cat->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Featured Products ── --}}
@if($featuredProducts->count())
<section style="padding:60px 0 0">
    <div class="container">
        <div class="section-heading">
            <h2>منتجات مميزة</h2>
            <a href="{{ route('store.products', ['featured' => 1]) }}">عرض الكل <i class="fas fa-arrow-left" style="font-size:.8rem"></i></a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px">
            @foreach($featuredProducts as $product)
            @include('store.partials.product-card', compact('product'))
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Mid Banner ── --}}
@php $midBanner = $banners->where('position','mid')->first(); @endphp
@if($midBanner)
<section style="padding:60px 0 0">
    <div class="container">
        <a href="{{ $midBanner->link ?? '#' }}"
           style="display:block;border-radius:var(--radius);overflow:hidden;position:relative;min-height:180px;background:var(--primary);padding:40px">
            @if($midBanner->image)
            <img src="{{ asset('storage/'.$midBanner->image) }}" alt=""
                 style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.3">
            @endif
            <div style="position:relative;color:#fff">
                <div style="font-size:1.6rem;font-weight:900;margin-bottom:8px">{{ $midBanner->title_ar }}</div>
                @if($midBanner->subtitle_ar)<p style="opacity:.85">{{ $midBanner->subtitle_ar }}</p>@endif
            </div>
        </a>
    </div>
</section>
@endif

{{-- ── Trending Products ── --}}
@if($trendingProducts->count())
<section style="padding:60px 0 0">
    <div class="container">
        <div class="section-heading">
            <h2>رائج الآن 🔥</h2>
            <a href="{{ route('store.products', ['trending' => 1]) }}">عرض الكل <i class="fas fa-arrow-left" style="font-size:.8rem"></i></a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px">
            @foreach($trendingProducts as $product)
            @include('store.partials.product-card', compact('product'))
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Best Sellers ── --}}
@if($bestSellers->count())
<section style="padding:60px 0 0">
    <div class="container">
        <div class="section-heading">
            <h2>الأكثر مبيعاً ⭐</h2>
            <a href="{{ route('store.products', ['best_seller' => 1]) }}">عرض الكل <i class="fas fa-arrow-left" style="font-size:.8rem"></i></a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px">
            @foreach($bestSellers as $product)
            @include('store.partials.product-card', compact('product'))
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── New Arrivals ── --}}
@if($newArrivals->count())
<section style="padding:60px 0 40px">
    <div class="container">
        <div class="section-heading">
            <h2>وصل حديثاً ✨</h2>
            <a href="{{ route('store.products', ['sort'=>'newest']) }}">عرض الكل <i class="fas fa-arrow-left" style="font-size:.8rem"></i></a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px">
            @foreach($newArrivals as $product)
            @include('store.partials.product-card', compact('product'))
            @endforeach
        </div>
    </div>
</section>
@endif

@push('styles')
<style>
@media(max-width:768px){
    section > .container > div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr!important; }
    section > .container > div[style*="grid-template-columns:repeat(4"] { grid-template-columns:1fr 1fr!important; }
    section > .container > div[style*="grid-template-columns:repeat(4"] > div:nth-child(2n) { border-inline-end:none!important; }
    section > .container > div[style*="grid-template-columns:repeat(4"] > div:nth-child(n+3) { border-top:1px solid var(--border); }
}
</style>
@endpush
@endsection
