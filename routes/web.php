<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

// ── Auth ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('store.home');
})->name('dashboard');

// ── Admin Panel ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::post('products/bulk', [\App\Http\Controllers\Web\Admin\ProductController::class, 'bulkAction'])->name('products.bulk');
    Route::post('products/{product}/toggle-status', [\App\Http\Controllers\Web\Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::post('products/{product}/variants', [\App\Http\Controllers\Web\Admin\ProductController::class, 'storeVariant'])->name('products.variants.store');
    Route::put('products/{product}/variants/{variant}', [\App\Http\Controllers\Web\Admin\ProductController::class, 'updateVariant'])->name('products.variants.update');
    Route::delete('products/{product}/variants/{variant}', [\App\Http\Controllers\Web\Admin\ProductController::class, 'destroyVariant'])->name('products.variants.destroy');
    Route::resource('products', \App\Http\Controllers\Web\Admin\ProductController::class);

    // Categories
    Route::resource('categories', \App\Http\Controllers\Web\Admin\CategoryController::class)->except('show');

    // Brands
    Route::resource('brands', \App\Http\Controllers\Web\Admin\BrandController::class)->except('show');

    // Attributes
    Route::get('attributes', [\App\Http\Controllers\Web\Admin\AttributeController::class, 'index'])->name('attributes.index');
    Route::get('attributes/{attribute}/values-list', [\App\Http\Controllers\Web\Admin\AttributeController::class, 'valuesList'])->name('attributes.values.list');
    Route::post('attributes', [\App\Http\Controllers\Web\Admin\AttributeController::class, 'store'])->name('attributes.store');
    Route::put('attributes/{attribute}', [\App\Http\Controllers\Web\Admin\AttributeController::class, 'update'])->name('attributes.update');
    Route::delete('attributes/{attribute}', [\App\Http\Controllers\Web\Admin\AttributeController::class, 'destroy'])->name('attributes.destroy');
    Route::post('attributes/{attribute}/values', [\App\Http\Controllers\Web\Admin\AttributeController::class, 'storeValue'])->name('attributes.values.store');
    Route::delete('attributes/{attribute}/values/{value}', [\App\Http\Controllers\Web\Admin\AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

    // Orders
    Route::post('orders/{order}/status', [\App\Http\Controllers\Web\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('orders/{order}/payment-status', [\App\Http\Controllers\Web\Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::post('orders/{order}/notes', [\App\Http\Controllers\Web\Admin\OrderController::class, 'updateAdminNotes'])->name('orders.update-notes');
    Route::get('orders/{order}/invoice', [\App\Http\Controllers\Web\Admin\OrderController::class, 'invoice'])->name('orders.invoice');
    Route::resource('orders', \App\Http\Controllers\Web\Admin\OrderController::class)->only(['index', 'show']);

    // Customers
    Route::post('customers/{customer}/toggle-status', [\App\Http\Controllers\Web\Admin\CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    Route::resource('customers', \App\Http\Controllers\Web\Admin\CustomerController::class)->only(['index', 'show', 'update']);

    // Coupons
    Route::post('coupons/{coupon}/toggle-status', [\App\Http\Controllers\Web\Admin\CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    Route::resource('coupons', \App\Http\Controllers\Web\Admin\CouponController::class)->except('show');

    // Banners
    Route::resource('banners', \App\Http\Controllers\Web\Admin\BannerController::class)->except('show');

    // Pages & FAQs
    Route::get('faqs', [\App\Http\Controllers\Web\Admin\PageController::class, 'faqs'])->name('faqs.index');
    Route::post('faqs', [\App\Http\Controllers\Web\Admin\PageController::class, 'storeFaq'])->name('faqs.store');
    Route::put('faqs/{faq}', [\App\Http\Controllers\Web\Admin\PageController::class, 'updateFaq'])->name('faqs.update');
    Route::delete('faqs/{faq}', [\App\Http\Controllers\Web\Admin\PageController::class, 'destroyFaq'])->name('faqs.destroy');
    Route::resource('pages', \App\Http\Controllers\Web\Admin\PageController::class)->except('show');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales',     [\App\Http\Controllers\Web\Admin\ReportController::class, 'sales'])->name('sales');
        Route::get('products',  [\App\Http\Controllers\Web\Admin\ReportController::class, 'products'])->name('products');
        Route::get('customers', [\App\Http\Controllers\Web\Admin\ReportController::class, 'customers'])->name('customers');
        Route::get('inventory', [\App\Http\Controllers\Web\Admin\ReportController::class, 'inventory'])->name('inventory');
    });

    // Settings
    Route::get('settings',          [\App\Http\Controllers\Web\Admin\StoreSettingsController::class, 'index'])->name('settings');
    Route::post('settings',         [\App\Http\Controllers\Web\Admin\StoreSettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/sections',[\App\Http\Controllers\Web\Admin\StoreSettingsController::class, 'updateSections'])->name('settings.sections');

    // Activity Logs
    Route::get('activity-logs', [\App\Http\Controllers\Web\Manager\ActivityLogsController::class, 'index'])->name('activity-logs');

    // Newsletter
    Route::get('newsletter', function () {
        $subscribers = \App\Models\NewsletterSubscriber::orderByDesc('created_at')->paginate(30);
        return view('admin.newsletter', compact('subscribers'));
    })->name('newsletter');

    // Contact Messages
    Route::get('messages', function () {
        $messages = \App\Models\ContactMessage::orderByDesc('created_at')->paginate(30);
        \App\Models\ContactMessage::unread()->update(['is_read' => true]);
        return view('admin.messages', compact('messages'));
    })->name('messages');
});

// ── Storefront ────────────────────────────────────────────────────────────
Route::name('store.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'home'])->name('home');
    Route::get('/products', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'products'])->name('products');
    Route::get('/product/{slug}', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'productDetail'])->name('product');
    Route::get('/category/{slug}', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'category'])->name('category');
    Route::get('/page/{slug}', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'page'])->name('page');
    Route::get('/faq', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'faq'])->name('faq');
    Route::get('/contact', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'contact'])->name('contact');
    Route::post('/contact', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'contactStore'])->name('contact.store');
    Route::get('/search', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'search'])->name('search');

    // Cart
    Route::get('/cart', [\App\Http\Controllers\Web\Store\CartController::class, 'index'])->name('cart');
    Route::get('/cart/mini', [\App\Http\Controllers\Web\Store\CartController::class, 'mini'])->name('cart.mini');
    Route::post('/cart/add', [\App\Http\Controllers\Web\Store\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [\App\Http\Controllers\Web\Store\CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [\App\Http\Controllers\Web\Store\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [\App\Http\Controllers\Web\Store\CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/coupon', [\App\Http\Controllers\Web\Store\CartController::class, 'applyCoupon'])->name('cart.coupon');
    Route::get('/cart/coupon/remove', [\App\Http\Controllers\Web\Store\CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // Wishlist (auth required)
    Route::middleware('auth')->post('/wishlist/toggle', [\App\Http\Controllers\Web\Store\AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');

    // Product reviews
    Route::middleware('auth')->post('/product/{product}/review', [\App\Http\Controllers\Web\Store\StorefrontController::class, 'storeReview'])->name('product.review');

    // Newsletter
    Route::post('/newsletter/subscribe', function (\Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email|max:255']);
        \App\Models\NewsletterSubscriber::firstOrCreate(
            ['email' => $request->email],
            ['subscribed_at' => now(), 'is_active' => true]
        );
        return back()->with('success', 'تم الاشتراك في النشرة البريدية');
    })->name('newsletter.subscribe');

    // Checkout & account (auth required)
    Route::middleware('auth')->group(function () {
        Route::get('/checkout', [\App\Http\Controllers\Web\Store\CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout', [\App\Http\Controllers\Web\Store\CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/order/success/{orderNumber}', [\App\Http\Controllers\Web\Store\CheckoutController::class, 'success'])->name('order.success');

        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Store\AccountController::class, 'dashboard'])->name('dashboard');
            Route::get('/orders', [\App\Http\Controllers\Web\Store\AccountController::class, 'orders'])->name('orders');
            Route::get('/orders/{order}', [\App\Http\Controllers\Web\Store\AccountController::class, 'orderShow'])->name('order');
            Route::get('/profile', [\App\Http\Controllers\Web\Store\AccountController::class, 'profile'])->name('profile');
            Route::post('/profile', [\App\Http\Controllers\Web\Store\AccountController::class, 'updateProfile'])->name('profile.update');
            Route::post('/password', [\App\Http\Controllers\Web\Store\AccountController::class, 'changePassword'])->name('password.update');
            Route::get('/addresses', [\App\Http\Controllers\Web\Store\AccountController::class, 'addresses'])->name('addresses');
            Route::post('/addresses', [\App\Http\Controllers\Web\Store\AccountController::class, 'storeAddress'])->name('addresses.store');
            Route::post('/addresses/{address}/default', [\App\Http\Controllers\Web\Store\AccountController::class, 'setDefaultAddress'])->name('addresses.default');
            Route::delete('/addresses/{address}', [\App\Http\Controllers\Web\Store\AccountController::class, 'destroyAddress'])->name('addresses.destroy');
            Route::get('/wishlist', [\App\Http\Controllers\Web\Store\AccountController::class, 'wishlist'])->name('wishlist');
        });
    });
});

// Language switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (!in_array($locale, ['ar', 'en'])) abort(404);
    session(['locale' => $locale]);
    return back();
})->name('lang.switch');

Route::get('/set-locale/{locale}', function (string $locale) {
    if (!in_array($locale, ['ar', 'en'])) abort(404);
    session(['locale' => $locale]);
    return back();
})->name('store.set-locale');
