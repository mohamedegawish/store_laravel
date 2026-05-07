<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>

<!-- Mobile Sidebar -->
<aside class="sidebar-mobile" id="mobile-sidebar" aria-hidden="true">
     <div class="sidebar-mobile-header">
         <a href="{{ route('front.index') }}" class="logo">سوق</a>
         <button class="close-sidebar-btn" id="close-sidebar-btn" aria-label="إغلاق القائمة"><i class="fas fa-times"></i></button>
     </div>
     <nav class="sidebar-mobile-nav" aria-label="Mobile Navigation">
          <ul>
              <li><a href="{{ route('front.index') }}" class="{{ request()->routeIs('front.home') ? 'active' : '' }}"><i class="fas fa-home"></i> الرئيسية</a></li>
              <li><a href="{{ route('front.index') }}#products-section"><i class="fas fa-box-open"></i> كل المنتجات</a></li>
              <li><a href="#" class="{{ request()->routeIs('front.cart') ? 'active' : '' }}"><i class="fas fa-shopping-cart"></i> السلة</a></li> 
              <li><a href="#"><i class="fas fa-receipt"></i> طلباتي</a></li>
              <li><a href="{{ route('front.index') }}#contact"><i class="fas fa-envelope"></i> اتصل بنا</a></li>
         </ul>
     </nav>
      <div class="mobile-sidebar-actions">
           @guest
           <a href="{{ route('login') }}" class="button button-primary">تسجيل الدخول</a>
           <a href="{{ route('register') }}" class="button button-outline">إنشاء حساب</a>
           @endguest
           @auth
           <form action="{{ route('logout') }}" method="POST" style="display:inline;">@csrf<button type="submit" class="button button-outline">خروج</button></form>
           @endauth
     </div>
</aside>

<!-- Header -->
<header class="top-bar" role="banner">
    <div class="container">
         <div class="header-left">
             <button class="hamburger" id="hamburger-btn" aria-label="فتح القائمة" aria-expanded="false" aria-controls="mobile-sidebar">
                 <i class="fas fa-bars"></i>
             </button>
             <a href="{{ route('front.index') }}" class="logo" aria-label="الصفحة الرئيسية للمتجر">
                 <!-- Replace with assets/images if available -->
                 سـوق
             </a>
         </div>

         <div class="header-center">
            <form class="search-bar" method="GET" action="{{ route('front.index') }}#products-section" role="search" aria-label="البحث في المتجر">
                <input type="search" name="search" id="search-input"
                       placeholder="ابحث عن المنتجات، الماركات..."
                       value="{{ request('search') }}"
                       aria-label="ابحث في المتجر"
                       autocomplete="off">
                <button class="search-button" type="submit" aria-label="إرسال البحث"><i class="fas fa-search" aria-hidden="true"></i></button>
            </form>
         </div>

        <div class="header-right">
             <nav class="nav-menu" aria-label="القائمة الرئيسية">
                 <ul>
                     <li><a href="{{ route('front.index') }}" class="{{ request()->routeIs('front.home') ? 'active' : '' }}">الرئيسية</a></li>
                     <li><a href="{{ route('front.index') }}#products-section">المنتجات</a></li>
                     <li><a href="#">طلباتي</a></li>
                     <li><a href="{{ route('front.index') }}#contact">اتصل بنا</a></li>
                 </ul>
             </nav>
             <div class="header-actions">
                    @guest
                    <a href="{{ route('login') }}" class="button button-primary">دخول</a>
                    <a href="{{ route('register') }}" class="button button-outline">حساب جديد</a>
                    @endguest
                    @auth
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">@csrf<button type="submit" class="button button-outline">خروج</button></form>
                    @endauth
                 <div class="cart-icon-wrapper">
                     <a href="#" class="action-link cart-icon" id="cart-icon-button"
                             aria-label="سلة التسوق" aria-haspopup="dialog" aria-expanded="false" aria-controls="miniCartPopup">
                         <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                         <span class="cart-count" id="cart-item-count" aria-label="عدد المنتجات في السلة">0</span>
                     </a>
                </div>
             </div>
         </div>
    </div>
</header>


