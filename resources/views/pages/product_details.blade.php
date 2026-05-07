@extends('layouts.app')

@section('title', 'تفاصيل المنتج - سوق')

@push('styles')
<style>
    .product-details-container { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin: 3rem 0; background: #fff; padding: 2rem; border-radius: 8px; border: 1px solid var(--border-color); }
    .product-gallery img { width: 100%; border-radius: 8px; border: 1px solid var(--border-color); height: auto; object-fit: cover; }
    .product-info h1 { font-size: 2rem; color: var(--primary-color); margin-bottom: 0.5rem; }
    .product-info .company-name { font-size: 1.1rem; color: var(--text-light); margin-bottom: 1.5rem; }
    .product-info .price { font-size: 2rem; font-weight: bold; color: var(--text-color); margin-bottom: 1.5rem; }
    .product-info .description { color: var(--text-color); line-height: 1.8; margin-bottom: 2rem; }
    .add-to-cart-box { display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-color); padding-top: 2rem; }
    .quantity-selector { display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 4px; overflow: hidden; }
    .quantity-selector button { padding: 0.75rem 1rem; background: var(--bg-color); border: none; cursor: pointer; font-size: 1.2rem; }
    .quantity-selector input { width: 50px; text-align: center; border: none; font-size: 1.1rem; font-weight: bold; border-left: 1px solid var(--border-color); border-right: 1px solid var(--border-color); }
    .btn-add { flex: 1; padding: 1rem; background: var(--primary-color); color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; }
    .btn-add:hover { opacity: 0.9; }
    @media (max-width: 768px) { .product-details-container { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<main class="container">
    <div class="product-details-container">
        <div class="product-gallery">
            <img src="{{ asset('front/assets/images/default-product.jpg') }}" alt="طقم أنتريه">
        </div>
        
        <div class="product-info">
            <h1>{{ $product->name }}</h1>
            <div class="company-name"><i class="fas fa-building"></i> {{ $product->company->company_name ?? 'شركة دمياط' }}</div>
            
            <div class="price">
                @if($product->variants->isNotEmpty())
                    {{ number_format($product->variants->first()->price, 2) }} ج.م
                @else
                    غير مسعر
                @endif
            </div>
            
            <div class="description">
                <p>{{ $product->description ?? 'لا يوجد وصف متاح.' }}</p>
            </div>
            
            <form action="{{ route('front.cart.sync') }}" method="POST">
                @csrf
                <div style="margin-bottom: 2rem;">
                    <label for="product_variant_id" style="display:block; margin-bottom:0.5rem; font-weight:bold;">اختر الموديل / القياس</label>
                    <select name="product_variant_id" id="product_variant_id" style="width:100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px;" required>
                        @foreach($product->variants as $variant)
                            <option value="{{ $variant->id }}">السعر: {{ number_format($variant->price, 2) }} ج.م - ({{ $variant->sku }}) - متوفر: {{ $variant->stock_quantity }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="add-to-cart-box">
                    <div class="quantity-selector" style="flex: 0 0 auto;">
                        <input type="number" name="quantity" value="1" min="1" max="99" style="width: 80px; text-align: center; border: 1px solid var(--border-color); font-size: 1.1rem; font-weight: bold; padding: 0.5rem; border-radius:4px;" required>
                    </div>
                    <button type="submit" class="btn-add"><i class="fas fa-cart-plus"></i> إضافة إلى السلة</button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection


