@extends('layouts.store')
@section('title', 'سلة التسوق')

@section('content')
<div class="container" style="padding-top:28px;padding-bottom:60px">

    <nav class="breadcrumb">
        <a href="{{ route('store.home') }}">الرئيسية</a>
        <span class="sep">/</span>
        <span>السلة</span>
    </nav>

    <h1 style="font-size:1.6rem;font-weight:900;margin-bottom:28px">
        سلة التسوق
        @if($items->count())
        <span style="font-size:1rem;font-weight:500;color:var(--text-muted)">({{ $items->count() }} منتج)</span>
        @endif
    </h1>

    @if($items->count())
    <div style="display:grid;grid-template-columns:1fr 360px;gap:28px;align-items:start" id="cartLayout">

        {{-- Items --}}
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
            @foreach($items as $item)
            <div class="cart-row" data-item="{{ $item->id }}" style="display:flex;gap:16px;padding:20px;border-bottom:1px solid var(--border);align-items:flex-start">
                <a href="{{ route('store.product', $item->product->slug) }}">
                    <img src="{{ $item->product_image ? asset('storage/'.$item->product_image) : 'https://via.placeholder.com/80x80?text=??' }}"
                         alt="{{ $item->product_name_ar }}"
                         style="width:80px;height:80px;border-radius:var(--radius-sm);object-fit:cover;flex-shrink:0;border:1px solid var(--border)">
                </a>
                <div style="flex:1">
                    <a href="{{ route('store.product', $item->product->slug) }}" style="font-weight:700;font-size:.95rem;line-height:1.4;display:block;margin-bottom:4px">
                        {{ $item->product_name_ar }}
                    </a>
                    @if($item->variant_name)
                    <div style="font-size:.8rem;color:var(--text-muted);margin-bottom:8px">{{ $item->variant_name }}</div>
                    @endif
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
                        <div style="font-size:1.1rem;font-weight:800;color:var(--primary)">
                            {{ number_format($item->unit_price, 2) }} ر.س
                        </div>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="display:flex;align-items:center;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden">
                                <button type="button" onclick="updateCartItem({{ $item->id }}, {{ $item->quantity - 1 }})"
                                        style="width:32px;height:34px;background:var(--surface);border:none;cursor:pointer;font-size:.9rem;font-weight:700">−</button>
                                <span style="width:36px;text-align:center;font-weight:700;font-size:.9rem">{{ $item->quantity }}</span>
                                <button type="button" onclick="updateCartItem({{ $item->id }}, {{ $item->quantity + 1 }})"
                                        style="width:32px;height:34px;background:var(--surface);border:none;cursor:pointer;font-size:.9rem;font-weight:700">+</button>
                            </div>
                            <div style="font-weight:700;color:var(--text);min-width:80px;text-align:end">
                                {{ number_format($item->unit_price * $item->quantity, 2) }} ر.س
                            </div>
                            <button onclick="removeCartItem({{ $item->id }})"
                                    style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:.9rem;padding:6px;transition:color .15s"
                                    onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-muted)'"
                                    title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center">
                <a href="{{ route('store.products') }}" style="font-size:.88rem;color:var(--primary);font-weight:600">
                    <i class="fas fa-arrow-right"></i> متابعة التسوق
                </a>
                <form method="POST" action="{{ route('store.cart.clear') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:.85rem;font-weight:600"
                            onclick="return confirm('هل تريد تفريغ السلة؟')">
                        <i class="fas fa-trash"></i> تفريغ السلة
                    </button>
                </form>
            </div>
        </div>

        {{-- Summary --}}
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;position:sticky;top:80px">
            <h3 style="font-weight:800;font-size:1.1rem;margin-bottom:20px">ملخص الطلب</h3>

            {{-- Coupon --}}
            <form method="POST" action="{{ route('store.cart.coupon') }}" style="margin-bottom:20px">
                @csrf
                <div style="font-size:.85rem;font-weight:600;margin-bottom:8px">كود الخصم</div>
                <div style="display:flex;gap:8px">
                    <input type="text" name="code" value="{{ session('coupon_code') }}" placeholder="أدخل الكود"
                           style="flex:1;height:40px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;font-size:.88rem;font-family:var(--font);outline:none">
                    <button type="submit" class="btn btn-outline btn-sm">تطبيق</button>
                </div>
                @if(session('coupon_error'))
                <div style="color:var(--danger);font-size:.8rem;margin-top:6px">{{ session('coupon_error') }}</div>
                @endif
                @if(session('coupon_code'))
                <div style="color:var(--success);font-size:.8rem;margin-top:6px">
                    <i class="fas fa-check-circle"></i> تم تطبيق الكوبون
                    <a href="{{ route('store.cart.coupon.remove') }}" style="color:var(--danger);margin-inline-start:8px">إزالة</a>
                </div>
                @endif
            </form>

            <div style="display:flex;flex-direction:column;gap:12px;padding-top:16px;border-top:1px solid var(--border)">
                <div style="display:flex;justify-content:space-between;font-size:.9rem">
                    <span style="color:var(--text-muted)">المجموع الفرعي</span>
                    <span id="summarySubtotal" style="font-weight:600">{{ number_format($subtotal, 2) }} ر.س</span>
                </div>
                @if($couponDiscount > 0)
                <div style="display:flex;justify-content:space-between;font-size:.9rem">
                    <span style="color:var(--success)"><i class="fas fa-tag"></i> خصم الكوبون</span>
                    <span style="color:var(--success);font-weight:600">- {{ number_format($couponDiscount, 2) }} ر.س</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:.9rem">
                    <span style="color:var(--text-muted)">الشحن</span>
                    <span style="font-weight:600">
                        @if($shippingCost == 0)
                        <span style="color:var(--success)">مجاني</span>
                        @else
                        {{ number_format($shippingCost, 2) }} ر.س
                        @endif
                    </span>
                </div>
                @if($taxAmount > 0)
                <div style="display:flex;justify-content:space-between;font-size:.9rem">
                    <span style="color:var(--text-muted)">ضريبة القيمة المضافة</span>
                    <span style="font-weight:600">{{ number_format($taxAmount, 2) }} ر.س</span>
                </div>
                @endif
            </div>

            <div style="display:flex;justify-content:space-between;font-size:1.15rem;font-weight:800;padding-top:16px;border-top:1px solid var(--border);margin-top:12px">
                <span>الإجمالي</span>
                <span style="color:var(--primary)">{{ number_format($total, 2) }} ر.س</span>
            </div>

            <a href="{{ route('store.checkout') }}" class="btn btn-primary btn-full" style="margin-top:20px;font-size:1rem;height:48px">
                <i class="fas fa-lock"></i> إتمام الشراء
            </a>
            <div style="text-align:center;margin-top:14px;font-size:.78rem;color:var(--text-muted)">
                <i class="fas fa-shield-alt"></i> دفع آمن ومشفر
            </div>
        </div>

    </div>

    @else
    <div style="text-align:center;padding:80px 20px">
        <i class="fas fa-shopping-bag" style="font-size:4rem;color:var(--border);margin-bottom:20px"></i>
        <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:10px">سلتك فارغة</h2>
        <p style="color:var(--text-muted);margin-bottom:28px">لم تضف أي منتجات بعد</p>
        <a href="{{ route('store.products') }}" class="btn btn-primary" style="font-size:1rem;padding:13px 32px">
            <i class="fas fa-shopping-bag"></i> تسوق الآن
        </a>
    </div>
    @endif

</div>

@push('scripts')
<script>
function updateCartItem(itemId, newQty) {
    if (newQty < 1) { removeCartItem(itemId); return; }
    fetch('{{ route("store.cart.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ item_id: itemId, quantity: newQty })
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.reload();
        else showToast(data.message ?? 'حدث خطأ', 'error');
    });
}

function removeCartItem(itemId) {
    fetch('{{ route("store.cart.remove") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ item_id: itemId })
    }).then(r => r.json()).then(data => {
        if (data.success) window.location.reload();
        else showToast(data.message ?? 'حدث خطأ', 'error');
    });
}
</script>
@endpush

@push('styles')
<style>
@media(max-width:768px){
    #cartLayout { grid-template-columns:1fr!important; }
}
</style>
@endpush
@endsection
