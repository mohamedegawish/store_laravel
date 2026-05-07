@extends('layouts.app')

@section('title', 'عربة التسوق - سوق')

@push('styles')
<style>
    .cart-container { margin-top: var(--spacing-xl); margin-bottom: var(--spacing-xl); }
    .cart-container h2 { text-align: center; margin-bottom: var(--spacing-xl); font-weight: var(--font-weight-bold); }
    .cart-item { display: flex; align-items: center; padding: var(--spacing-md); border-bottom: 1px solid var(--border-color); gap: var(--spacing-md); background: #fff; margin-bottom: 1rem; border-radius: 8px; }
    .cart-item-image img { width: 90px; height: 90px; object-fit: contain; }
    .cart-item-details { flex-grow: 1; }
    .cart-item-total { font-weight: bold; color: var(--primary-color); min-width: 120px; }
    .cart-summary { background-color: #fff; border-radius: 8px; padding: 1.5rem; max-width: 450px; margin: 2rem auto; border: 1px solid var(--border-color); }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 1rem; }
    .total-row { font-weight: bold; font-size: 1.25rem; border-top: 1px solid var(--border-color); padding-top: 1rem; }
    .checkout-btn { width: 100%; margin-top: 1.5rem; padding: 1rem; background: var(--secondary-color); color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
</style>
@endpush

@section('content')
<main class="container cart-container">
    <h2>عربة التسوق الخاصة بك</h2>

    <div id="cart-items-container">
        @if($cart && $cart->cartItems->isNotEmpty())
            @foreach($cart->cartItems as $item)
            @php $variant = $item->productVariant; $product = $variant->product; @endphp
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="{{ $product->base_image ? asset('storage/'.$product->base_image) : asset('front/assets/images/default-product.jpg') }}" alt="{{ $product->name }}">
                </div>
                <div class="cart-item-details">
                    <h3>{{ $product->name }} ({{ $variant->sku }})</h3>
                    <p class="price">السعر: <strong>{{ number_format($variant->price, 2) }} ج.م</strong></p>
                </div>
                <div class="cart-item-quantity" style="display:flex; align-items:center; gap: 10px;">
                    <form action="{{ route('front.cart.sync') }}" method="POST" style="display:flex; gap:10px; align-items:center;">
                        @csrf
                        <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $variant->stock_quantity }}" style="width: 60px; padding: 5px; text-align:center;">
                        <button type="submit" class="button button-outline" style="padding: 5px 10px;"><i class="fas fa-sync"></i> تحديث</button>
                    </form>
                </div>
                <div class="cart-item-total">
                    {{ number_format($variant->price * $item->quantity, 2) }} ج.م
                </div>
                <div class="cart-item-remove">
                    <form action="{{ route('front.cart.remove', $variant->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="button" style="color: red;" type="submit" onclick="return confirm('هل أنت متأكد من الحذف؟')"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        @else
            <div style="text-align:center; padding: 3rem; background: #fff; border-radius: 8px;">
                <h3>سلة التسوق فارغة</h3>
                <a href="{{ route('front.index') }}" class="button button-primary" style="margin-top: 1rem;">بدء التسوق</a>
            </div>
        @endif
    </div>

    @if($cart && $cart->cartItems->isNotEmpty())
    <div class="cart-summary">
        <h3>ملخص الطلب</h3>
        @php
            $total = $cart->cartItems->sum(function($item) {
                return $item->quantity * $item->productVariant->price;
            });
        @endphp
        <div class="summary-row">
            <span>الإجمالي الفرعي:</span>
            <span>{{ number_format($total, 2) }} ج.م</span>
        </div>
        <div class="summary-row total-row">
            <span>الإجمالي:</span>
            <strong>{{ number_format($total, 2) }} ج.م</strong>
        </div>
        <button class="checkout-btn" onclick="window.location.href='{{ route('front.checkout') }}'">
           <i class="fas fa-shield-alt"></i> المتابعة للدفع
        </button>
    </div>
    @endif
</main>
@endsection


