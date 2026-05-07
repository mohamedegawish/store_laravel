@extends('layouts.store')
@section('title', 'إتمام الشراء')

@section('content')
<div class="container" style="padding-top:28px;padding-bottom:60px">

    <nav class="breadcrumb">
        <a href="{{ route('store.home') }}">الرئيسية</a>
        <span class="sep">/</span>
        <a href="{{ route('store.cart') }}">السلة</a>
        <span class="sep">/</span>
        <span>الدفع</span>
    </nav>

    <h1 style="font-size:1.6rem;font-weight:900;margin-bottom:28px">إتمام الشراء</h1>

    @if($errors->any())
    <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:var(--radius);padding:16px;margin-bottom:20px;color:#991B1B">
        <ul style="margin:0;padding-right:20px">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('store.checkout.store') }}" id="checkoutForm">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 380px;gap:28px;align-items:start" id="checkoutLayout">

        {{-- ── Left: Address + Payment ── --}}
        <div style="display:flex;flex-direction:column;gap:20px">

            {{-- Saved Addresses --}}
            @auth
            @if(auth()->user()->addresses->count())
            <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px">
                <h3 style="font-weight:700;font-size:1rem;margin-bottom:16px"><i class="fas fa-map-marker-alt" style="color:var(--primary);margin-inline-end:8px"></i> عناوين محفوظة</h3>
                <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px">
                    @foreach(auth()->user()->addresses as $address)
                    <label style="display:flex;gap:12px;padding:14px;border-radius:var(--radius-sm);border:1.5px solid var(--border);cursor:pointer;transition:border-color .2s"
                           onclick="this.style.borderColor='var(--primary)'">
                        <input type="radio" name="saved_address_id" value="{{ $address->id }}"
                               {{ $address->is_default ? 'checked' : '' }}
                               onchange="fillFromSaved({{ $address->id }})"
                               style="margin-top:3px">
                        <div>
                            <div style="font-weight:700;font-size:.88rem">{{ $address->label ?? $address->name }}</div>
                            <div style="font-size:.82rem;color:var(--text-muted)">{{ $address->address_line1 }}, {{ $address->city }}</div>
                        </div>
                    </label>
                    @endforeach
                    <label style="display:flex;gap:12px;padding:14px;border-radius:var(--radius-sm);border:1.5px dashed var(--border);cursor:pointer">
                        <input type="radio" name="saved_address_id" value="" onchange="this.form.new_address.value=1">
                        <span style="font-size:.88rem;color:var(--text-muted)">+ إدخال عنوان جديد</span>
                    </label>
                </div>
            </div>
            @endif
            @endauth

            {{-- Shipping Address Form --}}
            <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px">
                <h3 style="font-weight:700;font-size:1rem;margin-bottom:20px"><i class="fas fa-truck" style="color:var(--primary);margin-inline-end:8px"></i> عنوان التوصيل</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">الاسم الكامل <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()?->name) }}" required
                               style="width:100%;height:42px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    </div>
                    <div>
                        <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">رقم الهاتف <span style="color:var(--danger)">*</span></label>
                        <input type="tel" name="shipping_phone" value="{{ old('shipping_phone', auth()->user()?->phone) }}" required
                               style="width:100%;height:42px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    </div>
                    <div style="grid-column:span 2">
                        <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">العنوان <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="shipping_address_line1" value="{{ old('shipping_address_line1') }}" required placeholder="الشارع، المبنى، الشقة"
                               style="width:100%;height:42px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    </div>
                    <div>
                        <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">المدينة <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="shipping_city" value="{{ old('shipping_city') }}" required
                               style="width:100%;height:42px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    </div>
                    <div>
                        <label style="font-size:.82rem;font-weight:600;display:block;margin-bottom:6px">المنطقة / المحافظة</label>
                        <input type="text" name="shipping_state" value="{{ old('shipping_state') }}"
                               style="width:100%;height:42px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 14px;font-family:var(--font);font-size:.88rem;outline:none">
                    </div>
                </div>
                @auth
                <label style="display:flex;align-items:center;gap:8px;margin-top:14px;cursor:pointer;font-size:.85rem">
                    <input type="checkbox" name="save_address" value="1">
                    حفظ هذا العنوان لطلبات مستقبلية
                </label>
                @endauth
            </div>

            {{-- Notes --}}
            <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px">
                <h3 style="font-weight:700;font-size:1rem;margin-bottom:14px"><i class="fas fa-sticky-note" style="color:var(--warning);margin-inline-end:8px"></i> ملاحظات إضافية</h3>
                <textarea name="notes" rows="3" placeholder="أي ملاحظات خاصة بطلبك..."
                          style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:12px 14px;font-family:var(--font);font-size:.88rem;resize:vertical;outline:none">{{ old('notes') }}</textarea>
            </div>

            {{-- Payment Method --}}
            <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px">
                <h3 style="font-weight:700;font-size:1rem;margin-bottom:16px"><i class="fas fa-credit-card" style="color:var(--info,#06B6D4);margin-inline-end:8px"></i> طريقة الدفع</h3>
                <div style="display:flex;flex-direction:column;gap:10px">
                    <label style="display:flex;align-items:center;gap:12px;padding:14px;border-radius:var(--radius-sm);border:1.5px solid var(--primary);background:var(--primary-light);cursor:pointer">
                        <input type="radio" name="payment_method" value="cod" checked>
                        <div>
                            <div style="font-weight:700;font-size:.9rem">الدفع عند الاستلام</div>
                            <div style="font-size:.78rem;color:var(--text-muted)">ادفع نقداً عند وصول الطلب</div>
                        </div>
                        <i class="fas fa-money-bill-wave" style="margin-inline-start:auto;font-size:1.3rem;color:var(--success)"></i>
                    </label>
                </div>
            </div>
        </div>

        {{-- ── Right: Order Summary ── --}}
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;position:sticky;top:80px">
            <h3 style="font-weight:800;font-size:1rem;margin-bottom:20px">ملخص الطلب</h3>

            <div style="display:flex;flex-direction:column;gap:12px;max-height:280px;overflow-y:auto;margin-bottom:16px">
                @foreach($items as $item)
                <div style="display:flex;gap:12px;align-items:center">
                    <div style="position:relative;flex-shrink:0">
                        <img src="{{ $item->product_image ? asset('storage/'.$item->product_image) : 'https://via.placeholder.com/50x50?text=?' }}"
                             style="width:50px;height:50px;border-radius:var(--radius-sm);object-fit:cover;border:1px solid var(--border)">
                        <span style="position:absolute;top:-5px;inset-inline-end:-5px;background:var(--primary);color:#fff;width:18px;height:18px;border-radius:99px;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:700">{{ $item->quantity }}</span>
                    </div>
                    <div style="flex:1">
                        <div style="font-size:.85rem;font-weight:600;line-height:1.3">{{ $item->product_name_ar }}</div>
                        @if($item->variant_name)<div style="font-size:.75rem;color:var(--text-muted)">{{ $item->variant_name }}</div>@endif
                    </div>
                    <div style="font-weight:700;font-size:.9rem;white-space:nowrap">{{ number_format($item->unit_price * $item->quantity, 2) }} ر.س</div>
                </div>
                @endforeach
            </div>

            <div style="border-top:1px solid var(--border);padding-top:16px;display:flex;flex-direction:column;gap:10px">
                <div style="display:flex;justify-content:space-between;font-size:.88rem">
                    <span style="color:var(--text-muted)">المجموع الفرعي</span>
                    <span style="font-weight:600">{{ number_format($subtotal, 2) }} ر.س</span>
                </div>
                @if($couponDiscount > 0)
                <div style="display:flex;justify-content:space-between;font-size:.88rem">
                    <span style="color:var(--success)">خصم الكوبون</span>
                    <span style="font-weight:600;color:var(--success)">- {{ number_format($couponDiscount, 2) }} ر.س</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:.88rem">
                    <span style="color:var(--text-muted)">الشحن</span>
                    <span style="font-weight:600">{{ $shippingCost > 0 ? number_format($shippingCost,2).' ر.س' : 'مجاني' }}</span>
                </div>
                @if($taxAmount > 0)
                <div style="display:flex;justify-content:space-between;font-size:.88rem">
                    <span style="color:var(--text-muted)">الضريبة</span>
                    <span style="font-weight:600">{{ number_format($taxAmount, 2) }} ر.س</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:800;padding-top:12px;border-top:1px solid var(--border)">
                    <span>الإجمالي</span>
                    <span style="color:var(--primary)">{{ number_format($total, 2) }} ر.س</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full" style="margin-top:20px;font-size:1rem;height:50px">
                <i class="fas fa-check-circle"></i> تأكيد الطلب
            </button>
            <div style="text-align:center;margin-top:12px;font-size:.78rem;color:var(--text-muted)">
                <i class="fas fa-shield-alt"></i> بيانات طلبك محمية ومشفرة
            </div>
        </div>

    </div>
    </form>
</div>

@push('styles')
<style>
@media(max-width:768px){
    #checkoutLayout { grid-template-columns:1fr!important; }
}
</style>
@endpush
@endsection
