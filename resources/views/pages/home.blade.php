@extends('layouts.app')

@section('content')
<div class="page-wrapper container">
    @include('partials.sidebar')

    <main class="main-content" id="main-content">
        <section class="hero-section" id="home" aria-labelledby="hero-title">
            <h1 id="hero-title">اكتشف منتجات دمياط الجديدة</h1>
            <h2>تحت رعاية جمعية مستثمرى دمياط الجديدة - جودة وتميز</h2>
            <a href="#products-section" class="button button-secondary">تسوق الآن <i class="fas fa-arrow-left"></i></a>
        </section>

        <section class="section" id="products-section" aria-labelledby="products-title">
            <h2 class="section-title" id="products-title">المنتجات المميزة</h2>

            <div class="product-grid" id="product-grid">
                @forelse($products as $product)
                <div class="product-card">
                    <a href="{{ route('front.product', $product) }}" class="product-image-link">
                        <img src="{{ $product->base_image ? asset('storage/'.$product->base_image) : asset('front/assets/images/default-product.jpg') }}" alt="{{ $product->name }}" class="product-image" loading="lazy" width="230" height="220" style="object-fit: cover; background: #eee;">
                    </a>
                    <div class="product-info">
                        <h3><a href="{{ route('front.product', $product) }}">{{ $product->name }}</a></h3>
                        <div class="product-price">
                            @if($product->variants->isNotEmpty())
                                {{ number_format($product->variants->first()->price, 2) }} ج.م
                            @else
                                غير متوفر
                            @endif
                        </div>
                        <div class="product-meta">
                            <p><i class="fas fa-building fa-fw"></i> {{ $product->company->company_name ?? 'شركة دمياط' }}</p>
                        </div>
                        <a href="{{ route('front.product', $product) }}" class="button button-primary add-to-cart-button" style="text-align:center; display:block;">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                    </div>
                </div>
                @empty
                    <p style="text-align: center; grid-column: 1 / -1; margin-top: 2rem;">لا توجد منتجات متاحة حالياً.</p>
                @endforelse
            </div>
            
            <div style="margin-top: 2rem; display: flex; justify-content: center;">
                {{ $products->links() }}
            </div>
        </section>

        <section id="contact" class="section">
            <h2 class="section-title">تواصل معنا</h2>
            <p style="color: var(--text-light); margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto; text-align: center;">هل لديك أي استفسارات أو اقتراحات؟ فريق خدمة العملاء لدينا جاهز للمساعدة!</p>
            <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
                 <p><i class="fas fa-envelope" style="color: var(--primary-color); margin-left: 8px;"></i> <a href="mailto:info@souq.test">info@souq.test</a></p>
                 <p><i class="fas fa-phone" style="color: var(--primary-color); margin-left: 8px;"></i> <a href="tel:9200XXXXX">9200XXXXX</a></p>
                 <p><i class="fab fa-whatsapp" style="color: var(--secondary-color2); margin-left: 8px;"></i> <a href="#" target="_blank" rel="noopener noreferrer">تواصل عبر واتساب</a></p>
            </div>
        </section>
    </main>
</div>
@endsection


