<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FitmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

// ── Breeze Auth ──────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ── Storefront ───────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop / Catalog
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// Fitment Finder (AJAX + full page)
Route::get('/fitment', [FitmentController::class, 'index'])->name('fitment.index');
Route::get('/api/fitment/makes', [FitmentController::class, 'makes'])->name('fitment.makes');
Route::get('/api/fitment/models', [FitmentController::class, 'models'])->name('fitment.models');
Route::get('/api/fitment/years', [FitmentController::class, 'years'])->name('fitment.years');
Route::get('/api/fitment/set', [FitmentController::class, 'setSession'])->name('fitment.set');
Route::get('/api/fitment/clear', [FitmentController::class, 'clearSession'])->name('fitment.clear');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{item}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
    Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon');
    Route::delete('/coupon', [CartController::class, 'removeCoupon'])->name('coupon.remove');
    Route::get('/count', [CartController::class, 'count'])->name('count'); // AJAX
});

// Checkout
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success/{order:order_number}', [CheckoutController::class, 'success'])->name('success');
    Route::post('/gpay-pay/{order:order_number}', [CheckoutController::class, 'processGooglePay'])->name('gpay.pay');
});

// Order tracking (public)
Route::get('/track-order', [OrderController::class, 'trackForm'])->name('order.track');
Route::post('/track-order', [OrderController::class, 'track'])->name('order.track.search');

// Social Auth (Google & Facebook)
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToProvider'])->name('auth.social.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleProviderCallback'])->name('auth.social.callback');

// Static pages
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/shipping', 'pages.shipping')->name('shipping');
Route::view('/returns', 'pages.returns')->name('returns');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

// ── Authenticated Customer ────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Breeze login redirect target
    Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');

    // Account
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order:order_number}', [AccountController::class, 'orderDetail'])->name('orders.detail');
        Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [AccountController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AccountController::class, 'deleteAddress'])->name('addresses.delete');
        Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
    });

    // Wishlist
    Route::post('/wishlist/toggle/{variant}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// ── Admin Panel ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard'); // => admin.dashboard

    // Products
    Route::resource('products', Admin\ProductController::class);
    Route::post('products/{product}/variants', [Admin\ProductController::class, 'storeVariant'])->name('products.variants.store');
    Route::put('products/{product}/variants/{variant}', [Admin\ProductController::class, 'updateVariant'])->name('products.variants.update');
    Route::delete('products/variants/{variant}', [Admin\ProductController::class, 'destroyVariant'])->name('products.variants.destroy');

    // Categories
    Route::resource('categories', Admin\CategoryController::class);

    // Brands
    Route::resource('brands', Admin\BrandController::class);
    Route::patch('brands/{brand}/toggle', [App\Http\Controllers\Admin\BrandController::class, 'toggle'])->name('brands.toggle');

    // Filter Attributes (Materials, Colors, Thread Sizes)
    Route::resource('attributes', Admin\AttributeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('attributes/{attribute}/toggle', [App\Http\Controllers\Admin\AttributeController::class, 'toggle'])->name('attributes.toggle');

    // Fitments
    Route::get('fitments', [Admin\FitmentController::class, 'index'])->name('fitments.index');
    Route::post('fitments/{product}/attach', [Admin\FitmentController::class, 'attach'])->name('fitments.attach');
    Route::delete('fitments/{product}/detach/{model}', [Admin\FitmentController::class, 'detach'])->name('fitments.detach');

    // Orders
    Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::post('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('orders/{order}/packing-slip', [Admin\OrderController::class, 'packingSlip'])->name('orders.packing-slip');

    // Customers
    Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/{user}', [Admin\CustomerController::class, 'show'])->name('customers.show');

    // Coupons
    Route::resource('coupons', Admin\CouponController::class);

    // Banners
    Route::resource('banners', Admin\BannerController::class);

    // Inventory
    Route::get('inventory', [Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/{variant}/adjust', [Admin\InventoryController::class, 'adjust'])->name('inventory.adjust');

    // Reviews moderation
    Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('reviews/{review}/hide', [Admin\ReviewController::class, 'hide'])->name('reviews.hide');
    Route::post('reviews/{review}/reply', [Admin\ReviewController::class, 'reply'])->name('reviews.reply');
});
