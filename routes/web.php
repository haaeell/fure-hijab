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
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminArticleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;
use App\Models\Category;
use App\Models\Collection as ProductCollection;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Mime\Address;

Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml') . "\n",
        200,
        ['Content-Type' => 'text/plain']
    );
});

Route::get('/sitemap.xml', function () {
    $urls = [];
    $formatLastmod = fn($date) => $date instanceof \DateTimeInterface ? $date->format('c') : now()->format('c');
    $pushUrl = function (string $loc, $lastmod = null, string $changefreq = 'weekly', string $priority = '0.7') use (&$urls, $formatLastmod) {
        $urls[] = [
            'loc' => $loc,
            'lastmod' => $formatLastmod($lastmod),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    };

    $pushUrl(url('/'), now(), 'daily', '1.0');
    $pushUrl(route('collections.index'), now(), 'daily', '0.9');
    $pushUrl(route('best-seller.index'), now(), 'daily', '0.8');
    $pushUrl(route('hijab.index'), now(), 'weekly', '0.8');
    $pushUrl(route('syari.index'), now(), 'weekly', '0.8');
    $pushUrl(route('new-arrived.index'), now(), 'daily', '0.8');
    $pushUrl(route('about.index'), now(), 'monthly', '0.5');
    $pushUrl(route('promo.index'), now(), 'weekly', '0.6');

    Category::where('is_active', true)
        ->select(['slug', 'updated_at'])
        ->orderBy('sort_order')
        ->cursor()
        ->each(fn($category) => $pushUrl(route('collections.index', ['category' => $category->slug]), $category->updated_at, 'weekly', '0.7'));

    $collectionRoutes = [
        'best-seller' => 'best-seller.index',
        'hijab' => 'hijab.index',
        'syari' => 'syari.index',
        'new-arrived' => 'new-arrived.index',
    ];

    ProductCollection::where('is_active', true)
        ->select(['slug', 'updated_at'])
        ->whereIn('slug', array_keys($collectionRoutes))
        ->cursor()
        ->each(fn($collection) => $pushUrl(route($collectionRoutes[$collection->slug]), $collection->updated_at, 'weekly', '0.8'));

    Product::where('is_active', true)
        ->select(['slug', 'updated_at'])
        ->latest('updated_at')
        ->cursor()
        ->each(fn($product) => $pushUrl(route('collections.show', $product->slug), $product->updated_at, 'weekly', '0.9'));

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    foreach ($urls as $url) {
        $xml .= '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>' . htmlspecialchars($url['changefreq'], ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . htmlspecialchars($url['priority'], ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;
    }

    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

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

// Public artikel
Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{slug}', [ArticleController::class, 'show'])->name('articles.show');

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

        // Couriers — admin can only toggle active + manage logo
        Route::prefix('couriers')->controller(CourierController::class)->group(function () {
            Route::get('/',               'index')->name('couriers.index');
            Route::patch('/{id}/toggle',  'toggle')->name('couriers.toggle');
            Route::post('/{id}/logo',     'uploadLogo')->name('couriers.upload-logo');
            Route::delete('/{id}/logo',   'destroyLogo')->name('couriers.destroy-logo');
        });

        // Collections (product collections, not catalog)
        Route::prefix('koleksi')->controller(CollectionController::class)->group(function () {
            Route::get('/', 'index')->name('koleksi.index');
            Route::post('/', 'store')->name('koleksi.store');
            Route::put('/{id}', 'update')->name('koleksi.update');
            Route::delete('/{id}', 'destroy')->name('koleksi.destroy');
        });

        Route::prefix('products')->controller(ProductController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::post('/description-image', 'uploadDescriptionImage')->name('products.description-image');
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
            Route::get('/{id}/label/pdf', 'downloadLabelPdf')->name('orders.label.pdf');
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

        // Admin Artikel
        Route::prefix('admin/articles')->controller(AdminArticleController::class)->name('admin.articles.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{article}', 'update')->name('update');
            Route::delete('/{article}', 'destroy')->name('destroy');
            Route::patch('/{article}/toggle-publish', 'togglePublish')->name('toggle-publish');
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
            Route::get('/summary', 'summary')->name('cart.summary');
            Route::post('/add', 'store')->name('cart.store');
            Route::post('/buy-now', 'buyNow')->name('cart.buy-now');
            Route::patch('/update/{id}', 'update')->name('cart.update');
            Route::delete('/delete/{id}', 'destroy')->name('cart.destroy');
            Route::post('/checkout', 'checkout')->name('cart.checkout');
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
