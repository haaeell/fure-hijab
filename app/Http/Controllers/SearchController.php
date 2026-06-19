<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['products' => [], 'did_you_mean' => null]);
        }

        $cacheKey = 'search.suggestions.' . md5(mb_strtolower($q));
        $products = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($q) {
            return Product::query()
                ->select(['id', 'category_id', 'name', 'slug', 'price', 'has_variant', 'sold_count', 'is_active'])
                ->with([
                    'images' => fn($query) => $query
                        ->select(['id', 'product_id', 'image_url', 'is_primary'])
                        ->where('is_primary', true)
                        ->orderBy('sort_order'),
                    'category:id,name',
                    'variants' => fn($query) => $query
                        ->select(['id', 'product_id', 'price'])
                        ->orderBy('price'),
                ])
                ->where('is_active', true)
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$q}%"));
                })
                ->orderByDesc('sold_count')
                ->limit(8)
                ->get()
                ->map(fn($p) => [
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => $p->has_variant ? ($p->variants->first()?->price ?? $p->price) : $p->price,
                    'image' => $p->images->first()?->image_url,
                    'category' => $p->category?->name,
                ]);
        });

        $didYouMean = null;
        if ($products->isEmpty()) {
            $didYouMean = $this->findClosestMatch($q);
        }

        return response()->json(['products' => $products, 'did_you_mean' => $didYouMean]);
    }

    public function popular()
    {
        $products = Cache::remember('search.popular_products', now()->addMinutes(10), function () {
            return Product::where('is_active', true)
                ->orderByDesc('sold_count')
                ->limit(6)
                ->get(['name', 'slug']);
        });

        return response()->json(['products' => $products]);
    }

    private function findClosestMatch(string $query): ?string
    {
        $names = Cache::remember('search.active_product_names', now()->addMinutes(10), function () {
            return Product::where('is_active', true)
                ->orderByDesc('sold_count')
                ->limit(300)
                ->pluck('name');
        });

        $best      = null;
        $bestScore = 0;

        // Full-string similarity
        foreach ($names as $name) {
            similar_text(mb_strtolower($query), mb_strtolower($name), $percent);
            if ($percent > $bestScore && $percent >= 35) {
                $bestScore = $percent;
                $best      = $name;
            }
        }

        if ($best) {
            return $best;
        }

        // Word-level similarity for single-word typos
        foreach ($names as $name) {
            foreach (explode(' ', mb_strtolower($query)) as $qWord) {
                if (mb_strlen($qWord) < 3) continue;
                foreach (explode(' ', mb_strtolower($name)) as $nWord) {
                    if (mb_strlen($nWord) < 3) continue;
                    similar_text($qWord, $nWord, $wordPct);
                    if ($wordPct > $bestScore && $wordPct >= 65) {
                        $bestScore = $wordPct;
                        $best      = $name;
                    }
                }
            }
        }

        return $best;
    }
}
