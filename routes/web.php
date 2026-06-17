<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LandingContentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Mime\Address;

Route::get('/', [LandingPageController::class, 'index']);

Auth::routes();

Route::get('/collections', [LandingPageController::class, 'collections'])->name('collections.index');
Route::get('/best-seller', [LandingPageController::class, 'bestSeller'])->name('best-seller.index');
Route::get('/hijab', [LandingPageController::class, 'hijab'])->name('hijab.index');
Route::get('/syari', [LandingPageController::class, 'syari'])->name('syari.index');
Route::get('/new-arrived', [LandingPageController::class, 'newArrived'])->name('new-arrived.index');
Route::get('/collections/{slug}', [LandingPageController::class, 'show'])->name('collections.show');
Route::get('/about-us', [LandingPageController::class, 'about'])->name('about.index');
Route::get('/promo', [LandingPageController::class, 'promo'])->name('promo.index');
Route::get('/user/profile', [LandingPageController::class, 'profile'])->name('profile.index');
Route::view('/terms-and-conditions', 'user.terms.index')->name('terms.index');

// Public search API
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/search/popular', [SearchController::class, 'popular'])->name('search.popular');

Route::post('/midtrans/callback', [App\Http\Controllers\MidtransController::class, 'callback']);
Route::get('/order/{order:order_number}/payment-status', [CheckoutController::class, 'checkPaymentStatus'])->middleware('auth');
Route::post('/order/{order:order_number}/review', [OrderHistoryController::class, 'submitReview'])->middleware('auth')->name('order.review.store');

Route::middleware(['auth'])->group(function () {

    // ADMIN ONLY ROUTES
    Route::middleware(['admin'])->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        // Categories
        Route::prefix('categories')->controller(CategoryController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // Brands
        Route::prefix('brands')->controller(BrandController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // Coupons
        Route::prefix('coupons')->controller(CouponController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // Customers
        Route::prefix('customers')->controller(CustomerController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // Reviews Management
        Route::prefix('reviews')->controller(ReviewController::class)->group(function () {
            Route::get('/', 'index');
            Route::patch('/{id}/toggle-verify', 'toggleVerify');
            Route::delete('/{id}', 'destroy');
        });
        Route::post('/reviews/{id}/verify', [OrderController::class, 'verify'])->name('reviews.verify');

        // Products
        Route::prefix('products')->controller(ProductController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::delete('/{id}/images/{imageId}', 'destroyImage');
            Route::post('/{id}/images/primary/{imageId}', 'setPrimaryImage');
            Route::post('/{id}/variants', 'storeVariant');
            Route::put('/{id}/variants/{variantId}', 'updateVariant');
            Route::delete('/{id}/variants/{variantId}', 'destroyVariant');
        });

        // Orders Management
        Route::prefix('orders')->controller(OrderController::class)->group(function () {
            Route::get('/', 'index')->name('orders.index');
            Route::get('/data', 'data')->name('orders.data');
            Route::get('/{id}', 'show')->name('orders.show');
            Route::get('/{id}/api', 'showApi')->name('orders.api');
            Route::post('/{id}/track', 'trackShipment')->name('orders.track');
            Route::get('/{id}/biteship-label', 'printBiteshipLabel')->name('orders.biteship-label');
            Route::get('/{id}/biteship-label/download', 'downloadBiteshipLabel')->name('orders.biteship-label.download');
            Route::patch('/{id}/status', 'updateStatus')->name('orders.status');
            Route::patch('/{id}/resi', 'updateResi')->name('orders.resi');
            Route::post('/{id}/biteship-waybill', 'generateBiteshipWaybill')->name('orders.biteship-waybill');
        });

        Route::prefix('reports')->controller(ReportController::class)->group(function () {
            Route::get('/', 'index')->name('reports.index');
            Route::get('/export/{type}', 'export')->name('reports.export');
        });

        Route::prefix('profile')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'index');
            Route::put('/update', 'updateProfile')->name('profile.update');
            Route::put('/password', 'updatePassword')->name('profile.password');
        });

        Route::prefix('settings')->controller(AdminSettingController::class)->group(function () {
            Route::get('/', 'index')->name('settings.index');
            Route::get('/store', 'storeIndex')->name('settings.store');
            Route::put('/store', 'updateStore')->name('settings.store.update');
            Route::get('/biteship/areas', 'searchBiteshipAreas')->name('settings.biteship.areas');
            Route::put('/', 'update')->name('settings.update');
            Route::post('/test-email', 'testEmail')->name('settings.test-email');
        });

        Route::prefix('landing-content')->controller(LandingContentController::class)->name('landing-content.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/banners', 'storeBanner')->name('banners.store');
            Route::put('/banners/{banner}', 'updateBanner')->name('banners.update');
            Route::delete('/banners/{banner}', 'destroyBanner')->name('banners.destroy');
            Route::post('/sections', 'storeSection')->name('sections.store');
            Route::put('/sections/{section}', 'updateSection')->name('sections.update');
            Route::delete('/sections/{section}', 'destroySection')->name('sections.destroy');
        });
    });

    // Wishlist (auth required, any role)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // CUSTOMER ONLY ROUTES
    Route::middleware(['customer'])->group(function () {

        // Cart
        Route::prefix('cart')->controller(CartController::class)->group(function () {
            Route::get('/', 'index')->name('cart.index');
            Route::post('/add', 'store')->name('cart.store');
            Route::post('/buy-now', 'buyNow')->name('cart.buy-now');
            Route::patch('/update/{id}', 'update')->name('cart.update');
            Route::delete('/delete/{id}', 'destroy')->name('cart.destroy');
        });

        // Checkout
        Route::prefix('checkout')->controller(CheckoutController::class)->group(function () {
            Route::get('/', 'index')->name('checkout.index');
            Route::post('/set-address', 'setAddress')->name('checkout.set-address');
            Route::post('/check-ongkir', 'checkOngkir')->name('checkout.check-ongkir');
            Route::get('/search-destination', 'searchDestination')->name('checkout.search-destination');
            Route::post('/apply-coupon', 'applyCoupon')->name('checkout.apply-coupon');
            Route::post('/remove-coupon', 'removeCoupon')->name('checkout.remove-coupon');
            Route::post('/', 'store')->name('checkout.store');
        });

        // Addresses
        Route::prefix('addresses')->controller(AddressController::class)->group(function () {
            Route::post('/', 'store')->name('addresses.store');
        });

        // Order History
        Route::prefix('order-history')->controller(OrderHistoryController::class)->group(function () {
            Route::get('/', 'index')->name('order.history');
            Route::get('/{orderNumber}', 'show')->name('order.history.show');
            Route::post('/{orderNumber}/track', 'trackShipment')->name('order.history.track');
            Route::patch('/{orderNumber}/complete', 'markAsCompleted')->name('order.history.complete');
            Route::patch('/{orderNumber}/cancel', 'cancel')->name('order.history.cancel');
        });
    });
});
