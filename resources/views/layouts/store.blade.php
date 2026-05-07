<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $settings = app(\App\Models\StoreSetting::class)::current(); @endphp
    <meta name="description" content="@yield('meta_description', $settings?->meta_description ?? '')">
    <title>@yield('title', $settings?->store_name ?? config('app.name'))</title>

    @if($settings?->favicon)
    <link rel="icon" href="{{ asset('storage/'.$settings->favicon) }}" type="image/png">
    @endif

    <!-- Fonts -->
    @php
        $primary   = $settings?->primary_color ?? '#6C3FC5';
        $secondary = $settings?->secondary_color ?? '#EFE9FA';
        $font      = $settings?->font_family ?? 'Cairo';
        $fontUrl = match($font) {
            'Tajawal'  => 'https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap',
            'Almarai'  => 'https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap',
            default    => 'https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap',
        };
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="{{ $fontUrl }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: {{ $primary }};
            --primary-dark: color-mix(in srgb, {{ $primary }} 80%, #000);
            --primary-light: color-mix(in srgb, {{ $primary }} 15%, #fff);
            --secondary: {{ $secondary }};
            --font: '{{ $font }}', sans-serif;
            --radius: 12px;
            --radius-sm: 8px;
            --shadow: 0 2px 16px rgba(0,0,0,.08);
            --shadow-lg: 0 8px 40px rgba(0,0,0,.14);
            --border: #E8ECF0;
            --surface: #F8FAFC;
            --text: #1A2332;
            --text-muted: #6B7685;
            --white: #fff;
            --danger: #EF4444;
            --success: #10B981;
            --warning: #F59E0B;
            --nav-h: 64px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: var(--font); color: var(--text); background: #fff; font-size: 15px; line-height: 1.6; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }

        /* ── Utilities ── */
        .container { max-width: 1280px; margin: 0 auto; padding: 0 20px; }
        .badge-sale { background: var(--danger); color: #fff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px; }
        .badge-new  { background: var(--success); color: #fff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 22px; border-radius: var(--radius-sm); font-weight: 600; font-size: .9rem; cursor: pointer; border: none; transition: all .2s; font-family: var(--font); }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
        .btn-outline:hover { background: var(--primary-light); }
        .btn-ghost  { background: transparent; color: var(--text-muted); border: 1.5px solid var(--border); }
        .btn-ghost:hover { border-color: var(--primary); color: var(--primary); }
        .btn-sm { padding: 7px 16px; font-size: .82rem; }
        .btn-full { width: 100%; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }

        /* ── Top Bar ── */
        .top-bar { background: var(--primary); color: rgba(255,255,255,.9); font-size: 12px; padding: 7px 0; }
        .top-bar .container { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .top-bar a { color: inherit; }
        .top-bar a:hover { color: #fff; }
        .top-bar-links { display: flex; gap: 16px; align-items: center; }

        /* ── Header ── */
        .site-header { background: #fff; box-shadow: 0 1px 0 var(--border); position: sticky; top: 0; z-index: 200; }
        .header-inner { height: var(--nav-h); display: flex; align-items: center; gap: 16px; }
        .header-logo { flex-shrink: 0; display: flex; align-items: center; gap: 10px; }
        .header-logo img { height: 42px; }
        .header-logo-text { font-size: 1.3rem; font-weight: 800; color: var(--primary); }
        .header-search { flex: 1; max-width: 520px; position: relative; }
        .header-search input { width: 100%; height: 42px; border: 1.5px solid var(--border); border-radius: 99px; padding: 0 44px 0 20px; font-size: .9rem; font-family: var(--font); outline: none; transition: border-color .2s; }
        .header-search input:focus { border-color: var(--primary); }
        .header-search .search-btn { position: absolute; inset-inline-end: 4px; top: 4px; width: 34px; height: 34px; border-radius: 99px; background: var(--primary); color: #fff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .search-results { position: absolute; top: calc(100% + 8px); inset-inline-start: 0; inset-inline-end: 0; background: #fff; border-radius: var(--radius); box-shadow: var(--shadow-lg); max-height: 380px; overflow-y: auto; display: none; z-index: 300; border: 1px solid var(--border); }
        .search-results.open { display: block; }
        .search-result-item { display: flex; align-items: center; gap: 12px; padding: 10px 16px; cursor: pointer; transition: background .15s; }
        .search-result-item:hover { background: var(--surface); }
        .search-result-item img { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; flex-shrink: 0; }
        .search-result-item .name { font-size: .88rem; font-weight: 600; }
        .search-result-item .price { font-size: .82rem; color: var(--primary); }
        .header-actions { display: flex; align-items: center; gap: 4px; margin-inline-start: auto; }
        .header-action-btn { position: relative; width: 42px; height: 42px; border-radius: 99px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); font-size: 1.1rem; transition: all .2s; cursor: pointer; background: transparent; border: none; }
        .header-action-btn:hover { background: var(--surface); color: var(--primary); }
        .header-action-btn .count { position: absolute; top: 4px; inset-inline-end: 4px; min-width: 17px; height: 17px; background: var(--primary); color: #fff; border-radius: 99px; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; padding: 0 3px; }
        .lang-switch { font-size: .8rem; font-weight: 700; color: var(--text-muted); cursor: pointer; padding: 6px 12px; border-radius: 99px; border: 1.5px solid var(--border); transition: all .2s; }
        .lang-switch:hover { border-color: var(--primary); color: var(--primary); }
        .header-mobile-toggle { display: none; width: 42px; height: 42px; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; color: var(--text); background: none; border: none; }

        /* ── Category Nav ── */
        .cat-nav { background: var(--surface); border-bottom: 1px solid var(--border); }
        .cat-nav-inner { display: flex; align-items: center; gap: 4px; overflow-x: auto; scrollbar-width: none; height: 46px; }
        .cat-nav-inner::-webkit-scrollbar { display: none; }
        .cat-nav-link { white-space: nowrap; padding: 6px 16px; border-radius: 99px; font-size: .85rem; font-weight: 500; color: var(--text-muted); transition: all .2s; flex-shrink: 0; }
        .cat-nav-link:hover, .cat-nav-link.active { background: var(--primary); color: #fff; }

        /* ── Mobile Drawer ── */
        .drawer-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 400; opacity: 0; pointer-events: none; transition: opacity .25s; }
        .drawer-overlay.open { opacity: 1; pointer-events: all; }
        .drawer { position: fixed; top: 0; inset-inline-start: -100%; width: 280px; height: 100%; background: #fff; z-index: 401; transition: inset-inline-start .3s; overflow-y: auto; box-shadow: var(--shadow-lg); }
        .drawer.open { inset-inline-start: 0; }
        .drawer-header { padding: 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .drawer-close { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: var(--text-muted); }
        .drawer-nav a { display: flex; align-items: center; gap: 12px; padding: 14px 20px; font-weight: 500; border-bottom: 1px solid var(--border); color: var(--text); }
        .drawer-nav a:hover { background: var(--surface); color: var(--primary); }
        .drawer-nav a i { width: 20px; color: var(--primary); }

        /* ── Footer ── */
        .site-footer { background: #1A2332; color: #94A3B8; margin-top: 80px; }
        .footer-main { padding: 60px 0 40px; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; }
        .footer-brand .footer-logo { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 12px; }
        .footer-brand p { font-size: .88rem; line-height: 1.8; max-width: 260px; }
        .footer-social { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
        .footer-social a { width: 36px; height: 36px; border-radius: 99px; background: rgba(255,255,255,.1); color: #fff; display: flex; align-items: center; justify-content: center; font-size: .9rem; transition: background .2s; }
        .footer-social a:hover { background: var(--primary); }
        .footer-col h5 { color: #fff; font-size: .92rem; font-weight: 700; margin-bottom: 16px; }
        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-col ul a { font-size: .85rem; transition: color .2s; }
        .footer-col ul a:hover { color: #fff; }
        .footer-newsletter input { width: 100%; height: 40px; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,.15); background: rgba(255,255,255,.07); color: #fff; padding: 0 14px; font-size: .85rem; font-family: var(--font); outline: none; margin-bottom: 10px; }
        .footer-newsletter input::placeholder { color: #94A3B8; }
        .footer-newsletter input:focus { border-color: var(--primary); }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.08); padding: 20px 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: .82rem; }
        .payment-icons { display: flex; gap: 8px; align-items: center; }
        .payment-icons i { font-size: 1.4rem; opacity: .6; }

        /* ── Product Card ── */
        .product-card { background: #fff; border-radius: var(--radius); overflow: hidden; transition: box-shadow .2s, transform .15s; border: 1px solid var(--border); }
        .product-card:hover { box-shadow: var(--shadow-lg); transform: translateY(-3px); }
        .product-card-image { position: relative; aspect-ratio: 1; overflow: hidden; background: var(--surface); }
        .product-card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
        .product-card:hover .product-card-image img { transform: scale(1.05); }
        .product-card-badges { position: absolute; top: 10px; inset-inline-start: 10px; display: flex; flex-direction: column; gap: 5px; }
        .product-card-actions { position: absolute; inset-inline-end: 10px; top: 10px; display: flex; flex-direction: column; gap: 6px; opacity: 0; transform: translateX(8px); transition: all .2s; }
        html[dir=rtl] .product-card-actions { transform: translateX(-8px); }
        .product-card:hover .product-card-actions { opacity: 1; transform: translateX(0); }
        .product-card-action-btn { width: 34px; height: 34px; background: #fff; border-radius: 99px; display: flex; align-items: center; justify-content: center; font-size: .85rem; box-shadow: 0 2px 8px rgba(0,0,0,.12); cursor: pointer; border: none; color: var(--text-muted); transition: all .15s; }
        .product-card-action-btn:hover { background: var(--primary); color: #fff; }
        .product-card-action-btn.wishlisted { color: var(--danger); }
        .product-card-body { padding: 14px; }
        .product-card-brand { font-size: .75rem; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: .03em; }
        .product-card-name { font-size: .9rem; font-weight: 600; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 10px; }
        .product-card-pricing { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .product-card-price { font-size: 1rem; font-weight: 800; color: var(--primary); }
        .product-card-old-price { font-size: .82rem; text-decoration: line-through; color: var(--text-muted); }
        .product-card-add-btn { margin-top: 12px; width: 100%; height: 38px; border-radius: var(--radius-sm); background: var(--primary-light); color: var(--primary); font-weight: 700; font-size: .85rem; border: none; cursor: pointer; font-family: var(--font); transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 7px; }
        .product-card-add-btn:hover { background: var(--primary); color: #fff; }

        /* ── Section headings ── */
        .section-heading { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .section-heading h2 { font-size: 1.5rem; font-weight: 800; position: relative; }
        .section-heading h2::after { content: ''; display: block; width: 40px; height: 3px; background: var(--primary); border-radius: 99px; margin-top: 6px; }
        .section-heading a { font-size: .88rem; color: var(--primary); font-weight: 600; }
        .section-heading a:hover { text-decoration: underline; }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; margin-top: 40px; }
        .pagination a, .pagination span { width: 36px; height: 36px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: .85rem; font-weight: 600; border: 1.5px solid var(--border); color: var(--text-muted); transition: all .2s; }
        .pagination a:hover { border-color: var(--primary); color: var(--primary); }
        .pagination .active span { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── Cart Drawer ── */
        .cart-drawer { position: fixed; top: 0; inset-inline-end: -420px; width: 420px; max-width: 100vw; height: 100%; background: #fff; z-index: 500; transition: inset-inline-end .3s; box-shadow: var(--shadow-lg); display: flex; flex-direction: column; }
        .cart-drawer.open { inset-inline-end: 0; }
        .cart-drawer-header { padding: 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
        .cart-drawer-header h3 { font-size: 1.1rem; font-weight: 800; }
        .cart-drawer-body { flex: 1; overflow-y: auto; padding: 16px; }
        .cart-drawer-footer { padding: 16px; border-top: 1px solid var(--border); }
        .cart-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .cart-item:last-child { border-bottom: none; }
        .cart-item img { width: 64px; height: 64px; border-radius: var(--radius-sm); object-fit: cover; flex-shrink: 0; }
        .cart-item-info { flex: 1; }
        .cart-item-name { font-weight: 600; font-size: .88rem; line-height: 1.4; margin-bottom: 4px; }
        .cart-item-variant { font-size: .78rem; color: var(--text-muted); }
        .cart-item-price { font-weight: 700; color: var(--primary); font-size: .92rem; margin-top: 4px; }
        .cart-item-qty { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
        .cart-qty-btn { width: 26px; height: 26px; border-radius: 99px; border: 1.5px solid var(--border); background: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: .85rem; font-weight: 700; transition: all .15s; }
        .cart-qty-btn:hover { border-color: var(--primary); color: var(--primary); }
        .cart-qty-val { font-size: .88rem; font-weight: 700; min-width: 20px; text-align: center; }
        .cart-item-remove { color: var(--text-muted); cursor: pointer; font-size: .85rem; transition: color .15s; }
        .cart-item-remove:hover { color: var(--danger); }

        /* ── Toast ── */
        .toast-container { position: fixed; bottom: 24px; inset-inline-end: 24px; display: flex; flex-direction: column; gap: 10px; z-index: 9999; }
        .toast { background: #1A2332; color: #fff; padding: 12px 20px; border-radius: var(--radius); font-size: .88rem; font-weight: 500; box-shadow: var(--shadow-lg); display: flex; align-items: center; gap: 10px; animation: slideIn .3s ease; max-width: 320px; }
        .toast.success { background: var(--success); }
        .toast.error { background: var(--danger); }
        @keyframes slideIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

        /* ── Breadcrumb ── */
        .breadcrumb { font-size: .82rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px; flex-wrap: wrap; padding: 14px 0; }
        .breadcrumb a { color: var(--text-muted); transition: color .15s; }
        .breadcrumb a:hover { color: var(--primary); }
        .breadcrumb .sep { opacity: .4; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .top-bar { display: none; }
            .header-search { display: none; }
            .header-mobile-toggle { display: flex !important; }
            .cat-nav { display: none; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-brand p { max-width: 100%; }
            .cart-drawer { width: 100vw; }
        }
        @media (max-width: 480px) {
            .container { padding: 0 14px; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Top Bar --}}
<div class="top-bar">
    <div class="container">
        <div>
            @if($settings?->phone)
            <span><i class="fas fa-phone" style="margin-inline-end:5px"></i>{{ $settings->phone }}</span>
            @endif
        </div>
        <div class="top-bar-links">
            @guest
            <a href="{{ route('login') }}">تسجيل الدخول</a>
            <a href="{{ route('register') }}">إنشاء حساب</a>
            @else
            <a href="{{ route('store.account.dashboard') }}">حسابي</a>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf <button type="submit" style="background:none;border:none;color:inherit;cursor:pointer;font-size:12px">تسجيل الخروج</button>
            </form>
            @endguest
            @if(app()->getLocale() === 'ar')
            <a href="{{ route('store.set-locale', 'en') }}">English</a>
            @else
            <a href="{{ route('store.set-locale', 'ar') }}">العربية</a>
            @endif
        </div>
    </div>
</div>

{{-- Header --}}
<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <button class="header-mobile-toggle" id="drawerToggle" aria-label="Menu">
                <i class="fas fa-bars"></i>
            </button>

            <a href="{{ route('store.home') }}" class="header-logo">
                @if($settings?->logo)
                <img src="{{ asset('storage/'.$settings->logo) }}" alt="{{ $settings->store_name }}">
                @else
                <span class="header-logo-text">{{ $settings?->store_name ?? config('app.name') }}</span>
                @endif
            </a>

            <form class="header-search" action="{{ route('store.products') }}" method="GET" role="search" autocomplete="off">
                <input type="text" name="search" id="globalSearch" value="{{ request('search') }}"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن منتج...' : 'Search products...' }}"
                    aria-label="search">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                <div class="search-results" id="searchResults"></div>
            </form>

            <div class="header-actions">
                @auth
                <a href="{{ route('store.account.wishlist') }}" class="header-action-btn" title="المفضلة">
                    <i class="far fa-heart"></i>
                    @if(auth()->user()->wishlist->count() > 0)
                    <span class="count">{{ auth()->user()->wishlist->count() }}</span>
                    @endif
                </a>
                @endauth

                <button class="header-action-btn" id="cartToggle" title="السلة">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="count" id="cartCount" style="{{ session('cart_count', 0) > 0 ? '' : 'display:none' }}">{{ session('cart_count', 0) }}</span>
                </button>

                @auth
                <a href="{{ route('store.account.dashboard') }}" class="header-action-btn" title="حسابي">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover">
                </a>
                @else
                <a href="{{ route('login') }}" class="header-action-btn" title="تسجيل الدخول">
                    <i class="far fa-user"></i>
                </a>
                @endauth
            </div>
        </div>
    </div>
</header>

{{-- Category Nav --}}
@php $navCategories = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->limit(12)->get(); @endphp
@if($navCategories->count())
<nav class="cat-nav">
    <div class="container">
        <div class="cat-nav-inner">
            <a href="{{ route('store.products') }}" class="cat-nav-link {{ request()->routeIs('store.products') && !request('category') ? 'active' : '' }}">
                {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
            </a>
            @foreach($navCategories as $cat)
            <a href="{{ route('store.products', ['category' => $cat->slug]) }}"
               class="cat-nav-link {{ request('category') == $cat->slug ? 'active' : '' }}">
                @if($cat->icon)<i class="{{ $cat->icon }}" style="margin-inline-end:5px"></i>@endif
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>
</nav>
@endif

{{-- Mobile Drawer --}}
<div class="drawer-overlay" id="drawerOverlay"></div>
<div class="drawer" id="mobileDrawer">
    <div class="drawer-header">
        <span style="font-weight:800;font-size:1.1rem;color:var(--primary)">{{ $settings?->store_name ?? config('app.name') }}</span>
        <button class="drawer-close" id="drawerClose"><i class="fas fa-times"></i></button>
    </div>
    <nav class="drawer-nav">
        <a href="{{ route('store.home') }}"><i class="fas fa-home"></i> الرئيسية</a>
        <a href="{{ route('store.products') }}"><i class="fas fa-th-large"></i> المنتجات</a>
        @foreach($navCategories->take(8) as $cat)
        <a href="{{ route('store.products', ['category' => $cat->slug]) }}">
            <i class="{{ $cat->icon ?? 'fas fa-tag' }}"></i> {{ $cat->name }}
        </a>
        @endforeach
        <a href="{{ route('store.cart') }}"><i class="fas fa-shopping-bag"></i> السلة</a>
        @auth
        <a href="{{ route('store.account.dashboard') }}"><i class="fas fa-user"></i> حسابي</a>
        <a href="{{ route('store.account.wishlist') }}"><i class="fas fa-heart"></i> المفضلة</a>
        <a href="{{ route('store.account.orders') }}"><i class="fas fa-box"></i> طلباتي</a>
        @else
        <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a>
        <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> إنشاء حساب</a>
        @endauth
    </nav>
</div>

{{-- Cart Drawer --}}
<div class="drawer-overlay" id="cartOverlay"></div>
<div class="cart-drawer" id="cartDrawer">
    <div class="cart-drawer-header">
        <h3><i class="fas fa-shopping-bag" style="color:var(--primary);margin-inline-end:8px"></i> السلة</h3>
        <button class="drawer-close" id="cartClose"><i class="fas fa-times"></i></button>
    </div>
    <div class="cart-drawer-body" id="cartDrawerBody">
        <div style="text-align:center;padding:40px;color:var(--text-muted)">
            <i class="fas fa-shopping-bag" style="font-size:2.5rem;opacity:.3;margin-bottom:12px"></i>
            <p>السلة فارغة</p>
        </div>
    </div>
    <div class="cart-drawer-footer">
        <div style="display:flex;justify-content:space-between;font-weight:700;margin-bottom:14px;font-size:1rem">
            <span>الإجمالي</span>
            <span id="cartTotal" style="color:var(--primary)">0.00 ر.س</span>
        </div>
        <a href="{{ route('store.cart') }}" class="btn btn-outline btn-full" style="margin-bottom:8px">عرض السلة</a>
        <a href="{{ route('store.checkout') }}" class="btn btn-primary btn-full">إتمام الشراء</a>
    </div>
</div>

{{-- Main Content --}}
<main>
@yield('content')
</main>

{{-- Footer --}}
<footer class="site-footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo">{{ $settings?->store_name ?? config('app.name') }}</div>
                    <p>{{ $settings?->footer_text_ar ?? 'تسوق بثقة وجودة مضمونة مع أفضل العروض والمنتجات.' }}</p>
                    @php $social = $settings?->social_links ?? []; @endphp
                    @if(count($social))
                    <div class="footer-social">
                        @foreach(['facebook'=>'fab fa-facebook-f','twitter'=>'fab fa-x-twitter','instagram'=>'fab fa-instagram','snapchat'=>'fab fa-snapchat','tiktok'=>'fab fa-tiktok','youtube'=>'fab fa-youtube','whatsapp'=>'fab fa-whatsapp'] as $key => $icon)
                        @if(!empty($social[$key]))
                        <a href="{{ $social[$key] }}" target="_blank" rel="noopener"><i class="{{ $icon }}"></i></a>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="footer-col">
                    <h5>روابط سريعة</h5>
                    <ul>
                        <li><a href="{{ route('store.home') }}">الرئيسية</a></li>
                        <li><a href="{{ route('store.products') }}">المنتجات</a></li>
                        <li><a href="{{ route('store.cart') }}">السلة</a></li>
                        @auth
                        <li><a href="{{ route('store.account.orders') }}">طلباتي</a></li>
                        @endauth
                    </ul>
                </div>
                <div class="footer-col">
                    <h5>خدمة العملاء</h5>
                    <ul>
                        @if($settings?->phone)<li><a href="tel:{{ $settings->phone }}">{{ $settings->phone }}</a></li>@endif
                        @if($settings?->email)<li><a href="mailto:{{ $settings->email }}">{{ $settings->email }}</a></li>@endif
                        <li><a href="#">سياسة الإرجاع</a></li>
                        <li><a href="#">الشحن والتوصيل</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h5>النشرة البريدية</h5>
                    <p style="font-size:.85rem;margin-bottom:14px">اشترك للحصول على أحدث العروض والمنتجات.</p>
                    <form class="footer-newsletter" action="{{ route('store.newsletter.subscribe') }}" method="POST">
                        @csrf
                        <input type="email" name="email" placeholder="بريدك الإلكتروني" required>
                        <button type="submit" class="btn btn-primary btn-sm btn-full">اشترك</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="footer-bottom">
            <span>© {{ date('Y') }} {{ $settings?->store_name ?? config('app.name') }}. جميع الحقوق محفوظة.</span>
            <div class="payment-icons">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-apple-pay"></i>
            </div>
        </div>
    </div>
</footer>

{{-- Toast Container --}}
<div class="toast-container" id="toastContainer"></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ── Toast ──
function showToast(msg, type = 'default') {
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<i class="fas fa-${type==='success'?'check-circle':type==='error'?'times-circle':'info-circle'}"></i> ${msg}`;
    document.getElementById('toastContainer').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ── Mobile Drawer ──
const overlay     = document.getElementById('drawerOverlay');
const drawer      = document.getElementById('mobileDrawer');
const drawerOpen  = document.getElementById('drawerToggle');
const drawerClose = document.getElementById('drawerClose');
drawerOpen?.addEventListener('click',  () => { drawer.classList.add('open'); overlay.classList.add('open'); });
drawerClose?.addEventListener('click', () => { drawer.classList.remove('open'); overlay.classList.remove('open'); });
overlay?.addEventListener('click',     () => { drawer.classList.remove('open'); overlay.classList.remove('open'); });

// ── Cart Drawer ──
const cartDrawer   = document.getElementById('cartDrawer');
const cartOverlay  = document.getElementById('cartOverlay');
const cartToggle   = document.getElementById('cartToggle');
const cartClose    = document.getElementById('cartClose');
cartToggle?.addEventListener('click',  () => { cartDrawer.classList.add('open'); cartOverlay.classList.add('open'); fetchCartDrawer(); });
cartClose?.addEventListener('click',   () => { cartDrawer.classList.remove('open'); cartOverlay.classList.remove('open'); });
cartOverlay?.addEventListener('click', () => { cartDrawer.classList.remove('open'); cartOverlay.classList.remove('open'); });

function fetchCartDrawer() {
    fetch('{{ route("store.cart.mini") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            document.getElementById('cartDrawerBody').innerHTML = data.html;
            document.getElementById('cartTotal').textContent   = data.total + ' ر.س';
            updateCartBadge(data.count);
        });
}

function updateCartBadge(count) {
    const badge = document.getElementById('cartCount');
    if (!badge) return;
    if (count > 0) { badge.textContent = count; badge.style.display = ''; }
    else           { badge.style.display = 'none'; }
}

// ── Add to Cart ──
function addToCart(productId, variantId = null, qty = 1, btn = null) {
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }
    fetch('{{ route("store.cart.add") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ product_id: productId, variant_id: variantId, quantity: qty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message ?? 'تمت الإضافة للسلة', 'success');
            updateCartBadge(data.cart_count);
        } else {
            showToast(data.message ?? 'حدث خطأ', 'error');
        }
    })
    .catch(() => showToast('حدث خطأ', 'error'))
    .finally(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-shopping-cart"></i> أضف للسلة'; }
    });
}

// ── Wishlist Toggle ──
function toggleWishlist(productId, btn) {
    fetch('{{ route("store.wishlist.toggle") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn?.classList.toggle('wishlisted', data.wishlisted);
            btn?.querySelector('i')?.classList.toggle('fas', data.wishlisted);
            btn?.querySelector('i')?.classList.toggle('far', !data.wishlisted);
            showToast(data.message, 'success');
        }
    });
}

// ── Live Search ──
let searchTimer;
document.getElementById('globalSearch')?.addEventListener('input', function () {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    const box = document.getElementById('searchResults');
    if (q.length < 2) { box.classList.remove('open'); return; }
    searchTimer = setTimeout(() => {
        fetch(`{{ route('store.search') }}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(items => {
                if (!items.length) { box.classList.remove('open'); return; }
                box.innerHTML = items.map(p =>
                    `<a href="${p.url}" class="search-result-item">
                        <img src="${p.image}" alt="">
                        <div><div class="name">${p.name}</div><div class="price">${p.price}</div></div>
                    </a>`
                ).join('');
                box.classList.add('open');
            });
    }, 300);
});
document.addEventListener('click', e => {
    if (!e.target.closest('.header-search')) document.getElementById('searchResults')?.classList.remove('open');
});
</script>
@stack('scripts')
</body>
</html>
