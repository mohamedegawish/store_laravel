<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\Manager\CompaniesManageController;
use App\Http\Controllers\API\V1\Manager\UsersManageController;
use App\Http\Controllers\API\V1\Manager\DashboardController as ManagerDashboardController;
use App\Http\Controllers\API\V1\Products\CategoryController;

use App\Http\Controllers\API\V1\Company\DashboardController as CompanyDashboardController;
use App\Http\Controllers\API\V1\Company\ProductController as CompanyProductController;
use App\Http\Controllers\API\V1\Company\ProductVariantController as CompanyProductVariantController;
use App\Http\Controllers\API\V1\Company\OrderController as CompanyOrderController;
use App\Http\Controllers\API\V1\Company\ReportController as CompanyReportController;

use App\Http\Controllers\API\V1\Store\CatalogController;
use App\Http\Controllers\API\V1\Store\CartController;
use App\Http\Controllers\API\V1\Store\CheckoutController;
use App\Http\Controllers\API\V1\Store\OrderController as StoreOrderController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'sendOtp']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::prefix('catalog')->group(function () {
        Route::get('/products', [CatalogController::class, 'index']);
        Route::get('/products/{product}', [CatalogController::class, 'show']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        
        Route::middleware('super_admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', [ManagerDashboardController::class, 'index']);
            
            Route::apiResource('users', UsersManageController::class);
            Route::apiResource('companies', CompaniesManageController::class);
            Route::apiResource('categories', CategoryController::class);
        });

        Route::middleware('company_admin')->prefix('company')->group(function () {
            Route::get('/dashboard', [CompanyDashboardController::class, 'index']);
            
            Route::apiResource('products', CompanyProductController::class);
            Route::apiResource('products.variants', CompanyProductVariantController::class);

            Route::get('/orders', [CompanyOrderController::class, 'index']);
            Route::get('/orders/{order}', [CompanyOrderController::class, 'show']);
            Route::patch('/orders/{order}/status', [CompanyOrderController::class, 'updateStatus']);

            Route::get('/reports/sales', [CompanyReportController::class, 'salesSummary']);
        });

        Route::prefix('store')->group(function () {
            Route::get('/cart', [CartController::class, 'show']);
            Route::post('/cart/sync', [CartController::class, 'syncItem']);
            Route::delete('/cart/items/{variantId}', [CartController::class, 'removeItem']);
            Route::delete('/cart', [CartController::class, 'clear']);

            Route::post('/checkout', [CheckoutController::class, 'store']);

            Route::get('/orders', [StoreOrderController::class, 'index']);
            Route::get('/orders/{order}', [StoreOrderController::class, 'show']);
            Route::post('/orders/{order}/cancel', [StoreOrderController::class, 'cancel']);
        });

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
