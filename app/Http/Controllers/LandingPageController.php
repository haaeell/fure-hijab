<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\LandingBanner;
use App\Models\LandingSection;
use App\Models\Order;
use App\Models\Review;
use App\Models\UserAddress;
use App\Models\Wishlist;
use App\Services\LandingPageViewDataService;
use App\Services\StorefrontContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function index(LandingPageViewDataService $landingViewData)
    {
        $productCardRelations = $this->productCardRelations();
        $categories = Category::query()
            ->select(['id', 'name', 'slug', 'sort_order', 'is_active'])
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order', 'asc')
            ->get();

        $flashSaleProducts = Product::query()
            ->select($this->productCardColumns())
            ->with($productCardRelations)
            ->where('is_active', true)
            ->whereNotNull('compare_price')
            ->where('compare_price', '>', DB::raw('price'))
            ->where('stock', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        $latestProducts = Product::query()
            ->select($this->productCardColumns())
            ->with($productCardRelations)
            ->where('is_active', true)
            ->orderByRaw('stock > 0 DESC')
            ->latest()
            ->take(8)
            ->get();

        $bestSellerProducts = Product::query()
            ->select($this->productCardColumns())
            ->with($productCardRelations)
            ->where('is_active', true)
            ->orderByRaw('stock > 0 DESC')
            ->orderByDesc('sold_count')
            ->latest()
            ->take(4)
            ->get();

        $featuredCategorySections = $categories->take(3)->map(function ($category) use ($productCardRelations) {
            $products = Product::query()
                ->select($this->productCardColumns())
                ->with($productCardRelations)
                ->where('is_active', true)
                ->where('category_id', $category->id)
                ->latest()
                ->take(4)
                ->get();

            $category->setRelation('featuredProducts', $products);

            return $category;
        })->filter(function ($category) {
            return $category->featuredProducts->count() > 0;
        });

        $shopLookProducts = Product::query()
            ->select($this->productCardColumns())
            ->with($productCardRelations)
            ->where('is_active', true)
            ->latest()
            ->take(2)
            ->get();

        $landingBanners = LandingBanner::where('is_active', true)->orderBy('sort_order')->latest()->get();
        $landingSections = LandingSection::where('is_active', true)->orderBy('sort_order')->latest()->get();
        $viewData = $landingViewData->homeData($landingBanners, $landingSections, $shopLookProducts);

        $journalArticles = Article::published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('welcome', array_merge(compact(
            'categories',
            'flashSaleProducts',
            'latestProducts',
            'bestSellerProducts',
            'featuredCategorySections',
            'shopLookProducts',
            'landingBanners',
            'landingSections',
            'journalArticles'
        ), $viewData));
    }

    public function collections(Request $request)
    {
        return $this->catalog($request, 'all');
    }

    public function bestSeller(Request $request)
    {
        return $this->catalog($request, 'best-seller');
    }

    public function hijab(Request $request)
    {
        return $this->catalog($request, 'hijab');
    }

    public function syari(Request $request)
    {
        return $this->catalog($request, 'syari');
    }

    public function newArrived(Request $request)
    {
        return $this->catalog($request, 'new-arrived');
    }

    public function show($slug)
    {
        $product = Product::with(['variants.attributes', 'category', 'brand', 'images', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::query()
            ->select($this->productCardColumns())
            ->with($this->productCardRelations())
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $averageRating = $product->reviews->avg('rating');
        $totalReviews  = $product->reviews->count();
        $inWishlist    = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->exists()
            : false;
        $isAuthenticated = Auth::check();
        $productSeo = $this->productSeo($product, $averageRating, $totalReviews, app(StorefrontContextService::class)->store());

        return view('user.collections.show', compact('product', 'relatedProducts', 'averageRating', 'totalReviews', 'inWishlist', 'isAuthenticated', 'productSeo'));
    }

    public function about(Request $request)
    {
        return view('user.about.index');
    }
    public function profile(Request $request)
    {
        $uid         = Auth::id();
        $profileUser = Auth::user();

        $orderCount     = Order::where('user_id', $uid)->count();
        $deliveredCount = Order::where('user_id', $uid)->where('status', 'delivered')->count();
        $reviewCount    = Review::where('user_id', $uid)->count();
        $wishlistCount  = Wishlist::where('user_id', $uid)->count();
        $totalSpent     = Order::where('user_id', $uid)
            ->whereIn('status', ['delivered', 'processing', 'shipped', 'confirmed'])
            ->sum('total');

        $voucherCount = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('started_at')->orWhere('started_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>=', now());
            })
            ->count();

        $recentOrders = Order::with(['items.product.images'])
            ->where('user_id', $uid)
            ->latest()
            ->take(3)
            ->get();

        $addresses = UserAddress::where('user_id', $uid)->orderByDesc('is_default')->get();

        return view('user.profile.index', compact(
            'profileUser', 'orderCount', 'deliveredCount',
            'reviewCount', 'wishlistCount', 'totalSpent',
            'voucherCount', 'recentOrders', 'addresses'
        ));
    }

    public function promo()
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('started_at')->orWhere('started_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>=', now());
            })
            ->latest()
            ->get();

        return view('user.promo.index', compact('coupons'));
    }

    private function catalog(Request $request, string $type)
    {
        $query = Product::query()
            ->select($this->productCardColumns())
            ->with($this->productCardRelations())
            ->where('is_active', true);
        $collection = $type !== 'all' ? Collection::where('slug', $type)->where('is_active', true)->first() : null;

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if (!$collection && $request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'in_stock') {
                $query->where('stock', '>', 0);
            }
            if ($request->availability === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            }
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->max_price);
        }

        if ($collection) {
            $query->whereHas('collections', fn($q) => $q->where('collections.id', $collection->id));
        } elseif ($type === 'syari') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%syari%')
                    ->orWhere('name', 'like', "%syar'i%")
                    ->orWhereHas('category', function ($cat) {
                        $cat->where('name', 'like', '%syari%')
                            ->orWhere('name', 'like', "%syar'i%");
                    });
            });
        }

        $sort = $request->get('sort');

        if (($type === 'best-seller' && !$collection) || $sort === 'best_seller') {
            $query->orderByDesc('sold_count')->latest();
        } elseif ($sort === 'price_low') {
            $query->orderBy('price');
        } elseif ($sort === 'price_high') {
            $query->orderByDesc('price');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::query()
            ->select(['id', 'name', 'slug', 'sort_order', 'is_active'])
            ->where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order', 'asc')
            ->get();
        $stockCounts = Product::where('is_active', true)
            ->selectRaw('SUM(CASE WHEN stock > 0 THEN 1 ELSE 0 END) as in_stock_count')
            ->selectRaw('SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as out_of_stock_count')
            ->first();
        $inStockCount = (int) ($stockCounts->in_stock_count ?? 0);
        $outOfStockCount = (int) ($stockCounts->out_of_stock_count ?? 0);

        $catalogMeta = match ($type) {
            'best-seller' => [
                'title' => 'BEST SELLER FROM FURE',
                'route' => 'best-seller.index',
            ],
            'hijab' => [
                'title' => 'HIJAB COLLECTION',
                'route' => 'hijab.index',
            ],
            'syari' => [
                'title' => "SYAR'I COLLECTION",
                'route' => 'syari.index',
            ],
            'new-arrived' => [
                'title' => 'NEW ARRIVED',
                'route' => 'new-arrived.index',
            ],
            default => [
                'title' => 'ALL COLLECTIONS',
                'route' => 'collections.index',
            ],
        };

        return view('user.collections.index', compact(
            'categories', 'products', 'catalogMeta', 'collection', 'inStockCount', 'outOfStockCount'
        ));
    }

    private function productCardColumns(): array
    {
        return [
            'id',
            'category_id',
            'brand_id',
            'name',
            'slug',
            'price',
            'compare_price',
            'stock',
            'sold_count',
            'has_variant',
            'is_active',
            'created_at',
            'updated_at',
        ];
    }

    private function productCardRelations(): array
    {
        return [
            'category:id,name,slug',
            'images' => fn($q) => $q
                ->select(['id', 'product_id', 'image_url', 'is_primary', 'sort_order'])
                ->orderByDesc('is_primary')
                ->orderBy('sort_order'),
            'variants' => fn($q) => $q
                ->select(['id', 'product_id', 'name', 'price', 'compare_price', 'stock', 'image', 'weight', 'sku'])
                ->orderBy('price'),
        ];
    }

    private function productSeo(Product $product, $averageRating, int $totalReviews, array $store): array
    {
        $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        $galleryImages = $product->images->count() > 0 ? $product->images : collect([$primaryImage])->filter();
        $displayPrice = $product->has_variant && $product->variants->count() > 0
            ? $product->variants->first()->price
            : $product->price;
        $displayStock = $product->has_variant && $product->variants->count() > 0
            ? $product->variants->first()->stock
            : $product->stock;
        $description = $product->short_description
            ?: \Illuminate\Support\Str::limit(trim(strip_tags($product->description)), 155, '');
        $image = $primaryImage ? asset('storage/' . $primaryImage->image_url) : asset('favicon.ico');
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $galleryImages->map(fn($item) => asset('storage/' . $item->image_url))->values()->all() ?: [$image],
            'description' => $description ?: trim(strip_tags($product->description)),
            'sku' => $product->sku ?: strtoupper(preg_replace('/[^A-Z0-9]/i', '', $store['name'])) . '-' . $product->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand->name ?? $store['name'],
            ],
            'category' => $product->category->name ?? 'Hijab',
            'offers' => [
                '@type' => 'Offer',
                'url' => route('collections.show', $product->slug),
                'priceCurrency' => 'IDR',
                'price' => (float) $displayPrice,
                'availability' => $displayStock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
            ],
        ];

        if ($totalReviews > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round((float) $averageRating, 1),
                'reviewCount' => $totalReviews,
            ];
        }

        return [
            'description' => $description,
            'image' => $image,
            'keywords' => implode(', ', array_filter([
                $product->name,
                $store['name'] . ' ' . $product->name,
                $product->category->name ?? null,
                $store['name'] . ' ' . ($product->category->name ?? 'hijab'),
                $product->brand->name ?? null,
                'hijab premium',
                'beli ' . $product->name,
                'harga ' . $product->name,
                'modest wear',
                $store['name'],
            ])),
            'schema' => $schema,
        ];
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->name  = $request->name;
        $user->phone = $request->phone ? '62' . ltrim(preg_replace('/[\s\-]/', '', $request->phone), '0') : null;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->with('password_error', 'Password saat ini tidak sesuai.')->with('tab', 'security');
        }

        $user->password = \Hash::make($request->password);
        $user->save();

        return back()->with('password_success', 'Password berhasil diperbarui.')->with('tab', 'security');
    }
}
