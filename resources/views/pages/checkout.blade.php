@extends('layouts.app')

@section('title', 'إتمام الطلب - سوق')

@push('styles')
<style>
    .checkout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin: 3rem 0; }
    .checkout-form { background: #fff; padding: 2rem; border-radius: 8px; border: 1px solid var(--border-color); }
    .checkout-form h3 { margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
    .form-group input, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; }
    .order-summary { background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid var(--border-color); height: fit-content; position: sticky; top: 100px; }
    .btn-submit { width: 100%; padding: 1rem; background: var(--secondary-color); color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; margin-top: 1rem; }
    @media (max-width: 768px) { .checkout-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<main class="container">
    <h2 style="margin-top: 2rem; text-align: center;">إتمام الدفع</h2>

    <div class="checkout-grid">
        <div class="checkout-form">
            <h3>معلومات الشحن</h3>
            <form action="{{ route('front.checkout.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>المحافظة / المدينة</label>
                    <input type="text" name="shipping_address[city]" placeholder="أدخل اسم المدينة" required>
                </div>
                <div class="form-group">
                    <label>الشارع</label>
                    <input type="text" name="shipping_address[street]" placeholder="الشارع ورقم العمارة" required>
                </div>
                <div class="form-group">
                    <label>ملاحظات إضافية (اختياري)</label>
                    <textarea rows="3" name="notes" placeholder="معلومات للتوصيل..."></textarea>
                </div>
                
                <h3 style="margin-top: 2rem;">طريقة الدفع</h3>
                <div style="margin-bottom: 1rem;">
                    <input type="radio" id="cash" name="payment" checked>
                    <label for="cash">الدفع عند الاستلام</label>
                </div>

                @php
                    $total = $cart->cartItems->sum(function($item) { return $item->quantity * $item->productVariant->price; });
                @endphp
                <button type="submit" class="btn-submit">تأكيد الطلب {{ number_format($total, 2) }} ج.م</button>
            </form>
        </div>

        <div class="order-summary">
            <h3 style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">ملخص الطلب</h3>
            
            @foreach($cart->cartItems as $item)
            @php $variant = $item->productVariant; @endphp
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span>{{ $variant->product->name }} ({{ $item->quantity }}x)</span>
                <span>{{ number_format($variant->price * $item->quantity, 2) }}</span>
            </div>
            @endforeach

            <div style="display: flex; justify-content: space-between; margin-top: 1rem; font-weight: bold; font-size: 1.2rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
                <span>الإجمالي</span>
                <span style="color: var(--primary-color);">{{ number_format($total, 2) }} ج.م</span>
            </div>
        </div>
    </div>
</main>
@endsection


