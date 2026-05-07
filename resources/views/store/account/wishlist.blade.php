@extends('store.account.layout')
@section('title', 'المفضلة')

@section('account_content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <h2 style="font-size:1.2rem;font-weight:800">المفضلة</h2>
    <span style="font-size:.85rem;color:var(--text-muted)">{{ $wishlistItems->count() }} منتج</span>
</div>

@if($wishlistItems->count())
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px">
    @foreach($wishlistItems as $item)
    @php $product = $item->product; @endphp
    @if($product)
    <div class="product-card" style="position:relative">
        <button onclick="toggleWishlist({{ $product->id }}, this);this.closest('.product-card').remove()"
                style="position:absolute;top:10px;inset-inline-end:10px;z-index:10;width:32px;height:32px;border-radius:99px;background:#fff;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.12);color:var(--danger);font-size:.9rem">
            <i class="fas fa-heart"></i>
        </button>
        @include('store.partials.product-card', compact('product'))
    </div>
    @endif
    @endforeach
</div>
@else
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:60px;text-align:center;color:var(--text-muted)">
    <i class="fas fa-heart" style="font-size:3rem;opacity:.2;margin-bottom:16px"></i>
    <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:8px">لا توجد منتجات في المفضلة</h3>
    <p style="margin-bottom:16px">أضف المنتجات التي تعجبك للمفضلة</p>
    <a href="{{ route('store.products') }}" class="btn btn-primary btn-sm">تصفح المنتجات</a>
</div>
@endif
@endsection
