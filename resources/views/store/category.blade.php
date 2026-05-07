@extends('layouts.store')
@section('title', app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en)
@section('content')

<div style="background:var(--surface-2);padding:48px 0 32px">
    <div class="container">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px">
            @if($category->icon)
            <div style="width:48px;height:48px;background:var(--primary-soft);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:1.3rem;flex-shrink:0">
                <i class="{{ $category->icon }}"></i>
            </div>
            @endif
            <div>
                <h1 style="font-size:2rem;font-weight:800;margin-bottom:4px">
                    {{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                </h1>
                <p style="color:var(--text-muted)">{{ $products->total() }} منتج</p>
            </div>
        </div>

        @if($subCategories->isNotEmpty())
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px">
            @foreach($subCategories as $sub)
            <a href="{{ route('store.category', $sub->slug) }}"
               style="padding:6px 14px;background:var(--surface);border-radius:20px;border:1px solid var(--border);font-size:.84rem;font-weight:600;color:var(--text-2);text-decoration:none;transition:.2s"
               onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-2)'">
                {{ app()->getLocale() === 'ar' ? $sub->name_ar : $sub->name_en }}
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="container" style="padding:40px 0">
    @if($products->isEmpty())
    <div style="text-align:center;padding:80px;color:var(--text-muted)">
        <i class="fas fa-box-open" style="font-size:3rem;opacity:.3;display:block;margin-bottom:16px"></i>
        <h3 style="font-size:1.1rem;margin-bottom:8px">لا توجد منتجات في هذا التصنيف</h3>
        <a href="{{ route('store.products') }}" style="color:var(--primary)">تصفح جميع المنتجات</a>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px">
        @foreach($products as $product)
        @include('store.partials.product-card', ['product' => $product])
        @endforeach
    </div>

    <div style="margin-top:32px">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
