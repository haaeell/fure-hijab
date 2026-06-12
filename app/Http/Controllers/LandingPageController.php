<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\LandingBanner;
use App\Models\LandingSection;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order', 'asc')->get();

        $flashSaleProducts = Product::with(['category', 'images' => function ($q) {
            $q->where('is_primary', true);
        }, 'variants'])
            ->where('is_active', true)
            ->whereNotNull('compare_price')
            ->where('compare_price', '>', DB::raw('price'))
            ->latest()
            ->take(4)
            ->get();

        $latestProducts = Product::with(['category', 'images' => function ($q) {
            $q->where('is_primary', true);
        }, 'variants'])
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $bestSellerProducts = Product::with(['category', 'images' => function ($q) {
            $q->where('is_primary', true);
        }, 'variants'])
            ->where('is_active', true)
            ->orderByDesc('sold_count')
            ->latest()
            ->take(4)
            ->get();

        $featuredCategorySections = $categories->take(3)->map(function ($category) {
            $products = Product::with(['category', 'images' => function ($q) {
                $q->where('is_primary', true);
            }, 'variants'])
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

        $shopLookProducts = Product::with(['category', 'images' => function ($q) {
            $q->where('is_primary', true);
        }, 'variants'])
            ->where('is_active', true)
            ->latest()
            ->take(2)
            ->get();

        $landingBanners = LandingBanner::where('is_active', true)->orderBy('sort_order')->latest()->get();
        $landingSections = LandingSection::where('is_active', true)->orderBy('sort_order')->latest()->get();

        return view('welcome', compact(
            'categories',
            'flashSaleProducts',
            'latestProducts',
            'bestSellerProducts',
            'featuredCategorySections',
            'shopLookProducts',
            'landingBanners',
            'landingSections'
        ));
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
        $product = Product::with(['variants.attributes', 'category', 'images', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::with(['category', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        $averageRating = $product->reviews->avg('rating');
        $totalReviews = $product->reviews->count();

        return view('user.collections.show', compact('product', 'relatedProducts', 'averageRating', 'totalReviews'));
    }

    public function about(Request $request)
    {
        return view('user.about.index');
    }
    public function profile(Request $request)
    {
        $orderCount = Order::where('user_id', Auth::id())->count();
        $voucherCount = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('started_at')->orWhere('started_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>=', now());
            })
            ->count();

        return view('user.profile.index', compact('orderCount', 'voucherCount'));
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
        $query = Product::with(['category', 'images', 'variants'])->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
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

        if ($type === 'syari') {
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

        if ($type === 'best-seller' || $sort === 'best_seller') {
            $query->orderByDesc('sold_count')->latest();
        } elseif ($sort === 'price_low') {
            $query->orderBy('price');
        } elseif ($sort === 'price_high') {
            $query->orderByDesc('price');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->withCount('products')->orderBy('sort_order', 'asc')->get();
        $inStockCount = Product::where('is_active', true)->where('stock', '>', 0)->count();
        $outOfStockCount = Product::where('is_active', true)->where('stock', '<=', 0)->count();

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
            'categories',
            'products',
            'catalogMeta',
            'inStockCount',
            'outOfStockCount'
        ));
    }
}
