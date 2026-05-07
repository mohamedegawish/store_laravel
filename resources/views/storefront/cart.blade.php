@extends('layouts.store')

@section('title', 'سلة التسوق')

@section('content')

<div class="container" style="padding-top:40px; padding-bottom:60px;">
    
    <h1 style="font-size:2rem; font-weight:800; margin-bottom:30px;">سلة التسوق 🛒</h1>

    @if($cartItems->count() > 0)
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; align-items: start;" class="cart-layout">
        
        {{-- Items --}}
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($cartItems as $item)
                @php 
                    $price = ($item->productVariant->price ?? 0) - ($item->productVariant->discount ?? 0);
                @endphp
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; display:flex; gap:20px; align-items:center;">
                    <a href="{{ route('front.product', ['slug'=>$company->slug, 'product'=>$item->product_id]) }}" style="width:100px; height:100px; border-radius:var(--radius-md); background:var(--surface-2); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0;">
                        @if($item->product && count($item->product->images ?? []))
                            <img src="{{ asset('storage/'.$item->product->images[0]) }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <i class="fas fa-image" style="color:var(--text-3); font-size:2rem;"></i>
                        @endif
                    </a>
                    
                    <div style="flex:1;">
                        <a href="{{ route('front.product', ['slug'=>$company->slug, 'product'=>$item->product_id]) }}" style="font-weight:700; font-size:1.1rem; line-height:1.4; color:var(--text); display:block; margin-bottom:5px;">{{ $item->product?->name ?? 'منتج غير متوفر' }}</a>
                        @if($item->productVariant?->sku)
                            <div style="font-size:0.8rem; color:var(--text-3); margin-bottom:12px;">SKU: {{ $item->productVariant->sku }}</div>
                        @endif
                        
                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:15px;">
                            <div style="font-weight:800; color:var(--primary); font-size:1.2rem;">{{ number_format($price, 2) }} ج.م</div>
                            
                            <div style="display:flex; align-items:center; gap:20px;">
                                <div style="display:flex; align-items:center; border:1px solid var(--border); border-radius:50px; overflow:hidden; height:36px;">
                                    <button type="button" class="qty-btn" disabled>-</button>
                                    <input type="text" value="{{ $item->quantity }}" style="width:40px; text-align:center; border:none; font-weight:700; background:transparent;" readonly>
                                    <button type="button" class="qty-btn" disabled>+</button>
                                </div>
                                <form action="#" method="POST">
                                    {{-- Real app would have a delete item route. Using placeholder here --}}
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="this.closest('.cart-layout > div').remove(); alert('سيتم الحذف فعلياً بعد ربط الراوت');" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size:1.1rem; transition:.2s;" title="حذف من السلة">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Summary --}}
        <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px; position:sticky; top:90px;">
            <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:20px; padding-bottom:15px; border-bottom:1px solid var(--border);">ملخص الطلب</h3>
            
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:1rem; color:var(--text-2);">
                <span>المجموع الفرعي:</span>
                <span style="font-weight:600; color:var(--text);">{{ number_format($total, 2) }} ج.م</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:1rem; color:var(--text-2);">
                <span>رسوم التوصيل:</span>
                <span style="font-weight:600; color:var(--success);">مجاناً لفترة محدودة</span>
            </div>
            
            <hr style="border:none; border-top:1px solid var(--border); margin:20px 0;">
            
            <div style="display:flex; justify-content:space-between; margin-bottom:24px; font-size:1.3rem; font-weight:800;">
                <span>الإجمالي:</span>
                <span style="color:var(--primary);">{{ number_format($total, 2) }} ج.م</span>
            </div>

            <a href="{{ route('front.checkout', $company->slug) }}" class="btn btn-primary" style="width:100%; display:flex; justify-content:center; padding:12px; font-size:1.1rem;">
                إتمام الطلب (الدفع) <i class="fas fa-arrow-left"></i>
            </a>

            <div style="text-align:center; margin-top:15px;">
                <a href="{{ route('front.products', $company->slug) }}" style="color:var(--text-light); font-size:0.9rem; font-weight:600; text-decoration:underline;">أو متابعة التسوق</a>
            </div>
        </div>

    </div>
    @else
    <div style="text-align:center; padding:80px 20px; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg);">
        <i class="fas fa-cart-arrow-down" style="font-size:4rem; color:var(--border); margin-bottom:20px;"></i>
        <h2 style="font-weight:800; margin-bottom:10px;">سلة التسوق فارغة</h2>
        <p style="color:var(--text-light); margin-bottom:25px;">يبدو أنك لم تقم بإضافة أي منتجات للسلة بعد.</p>
        <a href="{{ route('front.products', $company->slug) }}" class="btn btn-primary btn-lg"><i class="fas fa-store"></i> تصفح المنتجات وأضف للسلة</a>
    </div>
    @endif

</div>

@endsection

@push('styles')
<style>
.qty-btn { width:30px; background:var(--surface-2); border:none; cursor:pointer; font-size:1rem; font-weight:700; transition:.2s; }
.qty-btn:hover { background:var(--border); }
@media(max-width:800px) {
    .cart-layout { grid-template-columns: 1fr !important; }
}
</style>
@endpush
