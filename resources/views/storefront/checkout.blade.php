@extends('layouts.store')

@section('title', 'إتمام الطلب (الدفع)')

@section('content')

<div class="container" style="padding-top:40px; padding-bottom:60px;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:20px; margin-bottom:30px;">
        <h1 style="font-size:2rem; font-weight:800; margin:0;">إتمام الطلب 🚚</h1>
        <a href="{{ route('front.cart', $company->slug) }}" style="color:var(--text-light); font-weight:600;"><i class="fas fa-arrow-right"></i> الرجوع للسلة</a>
    </div>

    <form action="{{ route('front.processCheckout', $company->slug) }}" method="POST" id="checkoutForm">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; align-items: start;" class="checkout-layout">
            
            {{-- ── FORM ── --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                {{-- Account Info --}}
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px;">
                    <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-user-circle" style="color:var(--primary);"></i> معلومات الحساب
                    </h3>
                    <div style="display:flex; gap:15px; align-items:center;">
                        <div style="width:48px;height:48px;background:var(--surface-2);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.2rem;font-weight:700;color:var(--text-3);">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight:700; font-size:1rem;">{{ auth()->user()->name }}</div>
                            <div style="font-size:0.85rem; color:var(--text-light);">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>

                {{-- Shipping Details --}}
                <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px;">
                    <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-truck-fast" style="color:var(--primary);"></i> تفاصيل الشحن والتوصيل
                    </h3>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                        <div class="form-group">
                            <label style="display:block; font-weight:600; font-size:0.9rem; margin-bottom:8px; color:var(--text-2);">رقم الجوال للتواصل <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                                   style="width:100%; border:1px solid var(--border); border-radius:var(--radius-md); padding:10px 14px; font-size:1rem; outline:none; transition:.2s;" dir="ltr" placeholder="05xxxxxxxx">
                            @error('phone') <div style="color:var(--danger); font-size:0.8rem; margin-top:5px;">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label style="display:block; font-weight:600; font-size:0.9rem; margin-bottom:8px; color:var(--text-2);">المدينة <span style="color:var(--danger);">*</span></label>
                            <input type="text" name="city" value="{{ old('city') }}" required placeholder="مثال: الرياض، جدة، الدمام..."
                                   style="width:100%; border:1px solid var(--border); border-radius:var(--radius-md); padding:10px 14px; font-size:1rem; outline:none; transition:.2s;">
                            @error('city') <div style="color:var(--danger); font-size:0.8rem; margin-top:5px;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label style="display:block; font-weight:600; font-size:0.9rem; margin-bottom:8px; color:var(--text-2);">اسم الحي وعنوان الشارع بدقة <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="address" value="{{ old('address') }}" required placeholder="مثال: حي العليا، الشارع الرئيسي، جوار كذا..."
                               style="width:100%; border:1px solid var(--border); border-radius:var(--radius-md); padding:10px 14px; font-size:1rem; outline:none; transition:.2s;">
                        @error('address') <div style="color:var(--danger); font-size:0.8rem; margin-top:5px;">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label style="display:block; font-weight:600; font-size:0.9rem; margin-bottom:8px; color:var(--text-2);">ملاحظات إضافية (اختياري)</label>
                        <textarea name="notes" rows="3" placeholder="أي ملاحظات للمندوب أو عن وقت الاستلام يرجى إضافتها هنا..."
                                  style="width:100%; border:1px solid var(--border); border-radius:var(--radius-md); padding:10px 14px; font-size:1rem; outline:none; transition:.2s; resize:vertical; font-family:inherit;">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Payment Method (Static for now) --}}
                <div style="background:var(--surface); border:1px solid var(--primary); border-radius:var(--radius-lg); padding:24px; box-shadow: 0 0 0 1px rgba(var(--primary-rgb),0.2);">
                    <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-wallet" style="color:var(--primary);"></i> طريقة الدفع
                    </h3>
                    <div style="display:flex; align-items:center; justify-content:space-between; border:2px solid var(--primary); padding:14px 20px; border-radius:var(--radius-md); background:var(--secondary); cursor:pointer;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <input type="radio" checked style="width:18px;height:18px;accent-color:var(--primary);">
                            <span style="font-weight:700; font-size:1.05rem; color:var(--primary);">الدفع عند الاستلام</span>
                        </div>
                        <i class="fas fa-hand-holding-dollar" style="font-size:1.5rem; color:var(--primary);"></i>
                    </div>
                    <p style="font-size:0.85rem; color:var(--text-3); margin-top:10px;">
                        <i class="fas fa-info-circle"></i> يمكنك الدفع نقداً أوبالبطاقة عبر جهاز نقاط البيع عند وصول المندوب إليك.
                    </p>
                </div>

            </div>

            {{-- ── SUMMARY COL ── --}}
            <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px; position:sticky; top:90px;">
                <h3 style="font-size:1.2rem; font-weight:800; margin-bottom:20px; padding-bottom:15px; border-bottom:1px solid var(--border);">تفاصيل الطلب</h3>
                
                <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px; max-height:200px; overflow-y:auto; padding-right:5px;" class="checkout-items-scroll">
                    @foreach($cartItems as $item)
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                            <div style="display:flex; align-items:center; gap:10px; flex:1; overflow:hidden;">
                                <div style="position:relative;">
                                    <div style="width:40px; height:40px; border-radius:6px; background:var(--surface-2); display:flex; align-items:center; justify-content:center; overflow:hidden; flex-shrink:0; border:1px solid var(--border);">
                                        @if($item->product && count($item->product->images ?? []))
                                            <img src="{{ asset('storage/'.$item->product->images[0]) }}" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <i class="fas fa-image" style="color:var(--text-3); font-size:1rem;"></i>
                                        @endif
                                    </div>
                                    <span style="position:absolute; top:-6px; right:-6px; width:18px; height:18px; background:var(--text-light); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.65rem; font-weight:700;">{{ $item->quantity }}</span>
                                </div>
                                <div style="font-size:0.85rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $item->product?->name }}</div>
                            </div>
                            <div style="font-weight:700; font-size:0.9rem;">
                                @php $p = ($item->productVariant->price ?? 0) - ($item->productVariant->discount ?? 0); @endphp
                                {{ number_format($p * $item->quantity, 2) }} ج.م
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <hr style="border:none; border-top:1px dashed var(--border); margin:0 0 15px 0;">

                <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.95rem; color:var(--text-2);">
                    <span>المجموع الفرعي:</span>
                    <span style="font-weight:600; color:var(--text);">{{ number_format($total, 2) }} ج.م</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:0.95rem; color:var(--text-2);">
                    <span>رسوم التوصيل:</span>
                    <span style="font-weight:600; color:var(--success);">مجاناً</span>
                </div>
                
                <hr style="border:none; border-top:1px solid var(--border); margin:0 0 20px 0;">
                
                <div style="display:flex; justify-content:space-between; margin-bottom:24px; font-size:1.3rem; font-weight:800;">
                    <span>الإجمالي المستحق:</span>
                    <span style="color:var(--primary);">{{ number_format($total, 2) }} ج.م</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; display:flex; justify-content:center; padding:14px; font-size:1.15rem; border-radius:50px;">
                    <i class="fas fa-check-circle"></i> تأكيد الطلب
                </button>
                <div style="text-align:center; margin-top:10px; font-size:0.8rem; color:var(--text-3);">بالنقر على تأكيد الطلب، أنت توافق على شروط وأحكام المتجر.</div>
            </div>

        </div>
    </form>
</div>

@endsection

@push('styles')
<style>
@media(max-width:900px) {
    .checkout-layout { grid-template-columns: 1fr !important; }
}
.checkout-items-scroll::-webkit-scrollbar { width:4px; }
.checkout-items-scroll::-webkit-scrollbar-thumb { background:var(--border); border-radius:2px; }
input:focus, textarea:focus { border-color:var(--primary) !important; box-shadow: 0 0 0 3px rgba(var(--primary-rgb),0.1); }
</style>
@endpush
