@extends('layouts.store')

@section('title', 'جميع المنتجات')

@section('content')

<div class="store-section" style="padding-top:40px;">
    <div class="container">
        
        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:30px; flex-wrap:wrap; gap:20px;">
            <div>
                <h1 style="font-size:2rem; font-weight:800; margin-bottom:8px;">منتجاتنا</h1>
                <p style="color:var(--text-light);">تصفح جميع المنتجات المتوفرة واكتشف أحدث العروض.</p>
            </div>
            
            <form action="{{ route('front.products', $company->slug) }}" method="GET" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <select name="category" class="form-control" style="width:160px; height:44px; border-radius:50px; padding:0 20px;" onchange="this.form.submit()">
                    <option value="">كل الأقسام</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="sort" class="form-control" style="width:160px; height:44px; border-radius:50px; padding:0 20px;" onchange="this.form.submit()">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث إضافتة</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>السعر: الأقل للأعلى</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>السعر: الأعلى للأقل</option>
                </select>

                <button type="submit" style="display:none;"></button>
            </form>
        </div>

        @if(request('search'))
            <div style="margin-bottom:20px; font-weight:600;">
                نتائج البحث عن: <span style="color:var(--primary);">"{{ request('search') }}"</span>
                <a href="{{ route('front.products', $company->slug) }}" style="color:var(--danger); font-size:0.85rem; margin-right:10px;"><i class="fas fa-times"></i> مسح</a>
            </div>
        @endif

        @if($products->count())
            <div class="product-grid">
                @foreach($products as $product)
                    @include('storefront.partials.product-card', ['product'=>$product, 'company'=>$company, 'showDiscount'=>true])
                @endforeach
            </div>

            @if($products->hasPages())
                <div style="margin-top:40px; display:flex; justify-content:center;">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div style="text-align:center; padding:80px 20px; color:var(--text-light); background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg);">
                <i class="fas fa-box-open" style="font-size:3.5rem; opacity:0.3; margin-bottom:15px;"></i>
                <h3>لا توجد منتجات تطابق بحثك أو في هذا القسم.</h3>
                <a href="{{ route('front.products', $company->slug) }}" class="btn btn-primary" style="margin-top:20px;">عرض كل المنتجات</a>
            </div>
        @endif

    </div>
</div>

@endsection
