<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Traits\UploadsImages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use UploadsImages;

    public function index()
    {
        $products = Product::query()
            ->select([
                'id', 'category_id', 'brand_id', 'name', 'slug',
                'short_description', 'price', 'compare_price', 'modal_price',
                'stock', 'sku', 'is_active', 'has_variant', 'created_at',
            ])
            ->with([
                'category:id,name',
                'brand:id,name',
                'collections:id,name',
                // Load primary first; if none, fall back to first by sort_order
                'images' => fn($q) => $q->select(['id', 'product_id', 'image_url', 'is_primary', 'sort_order'])
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->limit(1),
            ])
            ->withCount('variants')
            ->withMin('variants', 'price')
            ->withMax('variants', 'price')
            ->latest()
            ->get();

        $categories  = Category::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']);
        $brands      = Brand::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $collections = Collection::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']);

        return view('master.products.index', compact('products', 'categories', 'brands', 'collections'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'images', 'variants.attributes', 'collections'])
            ->findOrFail($id);

        $priceMin = $product->has_variant ? $product->variants->min('price') : $product->price;
        $priceMax = $product->has_variant ? $product->variants->max('price') : $product->price;

        return response()->json(array_merge($product->toArray(), [
            'collection_ids' => $product->collections->pluck('id'),
            'collection_id'  => $product->collections->pluck('id')->first(),
            'price_min'      => $priceMin,
            'price_max'      => $priceMax,
            'price_range'    => $this->buildPriceRange($priceMin, $priceMax),
        ]));
    }

    public function store(Request $request)
    {
        $this->normalizePrices($request);
        $hasVariant = $request->boolean('has_variant');

        $data = $request->validate([
            'name'              => 'required|string|max:255|unique:products,name',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => ($hasVariant ? 'nullable' : 'required') . '|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'modal_price'       => 'nullable|numeric|min:0',
            'stock'             => ($hasVariant ? 'nullable' : 'required') . '|integer|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|unique:products,sku',
            'is_active'         => 'nullable',
            'has_variant'       => 'nullable|boolean',
            'images.*'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'collection_id'     => 'nullable|exists:collections,id',
            'collection_ids'    => 'nullable|array',
            'collection_ids.*'  => 'exists:collections,id',
        ], [
            'name.required'        => 'Nama produk wajib diisi',
            'name.unique'          => 'Nama produk sudah terdaftar',
            'category_id.required' => 'Kategori wajib dipilih',
            'price.required'       => 'Harga wajib diisi untuk produk tanpa varian',
            'stock.required'       => 'Stok wajib diisi untuk produk tanpa varian',
        ]);

        // compare_price > price validation (non-variant only)
        if (!$hasVariant && !empty($data['compare_price']) && !empty($data['price'])
            && $data['compare_price'] <= $data['price']) {
            return redirect()->back()
                ->withErrors(['compare_price' => 'Harga coret harus lebih besar dari harga jual.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $product = Product::create([
                'category_id'       => $data['category_id'],
                'brand_id'          => $data['brand_id'] ?? null,
                'name'              => $data['name'],
                'slug'              => Str::slug($data['name']),
                'description'       => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'price'             => $hasVariant ? 0 : ($data['price'] ?? 0),
                'compare_price'     => $hasVariant ? null : ($data['compare_price'] ?? null),
                'modal_price'       => $hasVariant ? null : ($data['modal_price'] ?? null),
                'stock'             => $hasVariant ? 0 : ($data['stock'] ?? 0),
                'weight'            => $data['weight'] ?? null,
                'sku'               => $data['sku'] ?? null,
                'is_active'         => $request->has('is_active'),
                'has_variant'       => $hasVariant,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->uploadAsWebp($image, 'products');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url'  => $path,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            }

            if ($hasVariant && $request->has('variants')) {
                $this->syncVariants($product, $request->input('variants', []));
            }

            $this->syncCollections($product, $request);

            DB::commit();
            $this->clearProductCaches();
            return redirect()->back()->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $product    = Product::findOrFail($id);
        $hasVariant = $request->boolean('has_variant');
        $this->normalizePrices($request);

        $data = $request->validate([
            'name'              => 'required|string|max:255|unique:products,name,' . $id,
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => ($hasVariant ? 'nullable' : 'required') . '|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'modal_price'       => 'nullable|numeric|min:0',
            'stock'             => ($hasVariant ? 'nullable' : 'required') . '|integer|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|unique:products,sku,' . $id,
            'is_active'         => 'nullable',
            'has_variant'       => 'nullable|boolean',
            'images.*'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'collection_id'     => 'nullable|exists:collections,id',
            'collection_ids'    => 'nullable|array',
            'collection_ids.*'  => 'exists:collections,id',
        ], [
            'name.required'        => 'Nama produk wajib diisi',
            'name.unique'          => 'Nama produk sudah terdaftar',
            'category_id.required' => 'Kategori wajib dipilih',
            'price.required'       => 'Harga wajib diisi untuk produk tanpa varian',
            'stock.required'       => 'Stok wajib diisi untuk produk tanpa varian',
            'images.*.image'       => 'File harus berupa gambar',
            'images.*.max'         => 'Ukuran gambar maksimal 2MB',
        ]);

        if (!$hasVariant && !empty($data['compare_price']) && !empty($data['price'])
            && $data['compare_price'] <= $data['price']) {
            return redirect()->back()
                ->withErrors(['compare_price' => 'Harga coret harus lebih besar dari harga jual.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $product->update([
                'category_id'       => $data['category_id'],
                'brand_id'          => $data['brand_id'] ?? null,
                'name'              => $data['name'],
                'slug'              => Str::slug($data['name']),
                'description'       => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'price'             => $hasVariant ? $product->price : ($data['price'] ?? 0),
                'compare_price'     => $hasVariant ? null : ($data['compare_price'] ?? null),
                'modal_price'       => $hasVariant ? null : ($data['modal_price'] ?? null),
                'stock'             => $hasVariant ? $product->stock : ($data['stock'] ?? 0),
                'weight'            => $data['weight'] ?? null,
                'sku'               => $data['sku'] ?? $product->sku,
                'is_active'         => $request->has('is_active'),
                'has_variant'       => $hasVariant,
            ]);

            if ($request->hasFile('images')) {
                $lastOrder = $product->images()->max('sort_order') ?? -1;
                $hasPrimary = $product->images()->where('is_primary', true)->exists();

                foreach ($request->file('images') as $index => $image) {
                    $path = $this->uploadAsWebp($image, 'products');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url'  => $path,
                        'is_primary' => !$hasPrimary && $index === 0,
                        'sort_order' => $lastOrder + $index + 1,
                    ]);
                }
            }

            if ($hasVariant && $request->has('variants')) {
                $this->syncVariants($product, $request->input('variants', []));
            } elseif (!$hasVariant) {
                foreach ($product->variants as $variant) {
                    if ($variant->image) Storage::disk('public')->delete($variant->image);
                }
                $product->variants()->delete();
            }

            $this->syncCollections($product, $request);

            DB::commit();
            $this->clearProductCaches();
            return redirect()->back()->with('success', 'Produk berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $product = Product::with(['images', 'variants'])->findOrFail($id);

        DB::beginTransaction();
        try {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();
            }
            foreach ($product->variants as $variant) {
                if ($variant->image) Storage::disk('public')->delete($variant->image);
                $variant->delete();
            }
            $product->delete();
            DB::commit();
            $this->clearProductCaches();
            return redirect()->back()->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus produk');
        }
    }

    public function uploadDescriptionImage(Request $request)
    {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:4096']);

        $path = $request->file('image')->store('products/descriptions', 'public');

        return response()->json(['url' => asset('storage/' . $path)]);
    }

    // ─── Image helpers ────────────────────────────────────────────────────────

    private function syncCollections(Product $product, Request $request): void
    {
        if ($request->filled('collection_id')) {
            $product->collections()->sync([(int) $request->input('collection_id')]);
            return;
        }
        $product->collections()->sync($request->input('collection_ids', []));
    }

    public function destroyImage($productId, $imageId)
    {
        $image = ProductImage::where('product_id', $productId)->findOrFail($imageId);
        Storage::disk('public')->delete($image->image_url);

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            ProductImage::where('product_id', $productId)
                ->orderBy('sort_order')
                ->first()
                ?->update(['is_primary' => true]);
        }

        $this->clearProductCaches();
        return response()->json(['success' => true]);
    }

    public function setPrimaryImage($productId, $imageId)
    {
        ProductImage::where('product_id', $productId)->update(['is_primary' => false]);
        ProductImage::where('product_id', $productId)->where('id', $imageId)->update(['is_primary' => true]);

        $this->clearProductCaches();
        return response()->json(['success' => true]);
    }

    // ─── Variant CRUD (standalone endpoints) ─────────────────────────────────

    public function storeVariant(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $this->normalizeVariantPrices($request);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'compare_price'  => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'sku'            => 'nullable|string|unique:product_variants,sku',
            'weight'         => 'nullable|numeric|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'attributes'     => 'nullable|array',
            'attributes.*.name'  => 'required|string',
            'attributes.*.value' => 'required|string',
        ]);

        $variant = $product->variants()->create([
            'name'           => $data['name'],
            'price'          => $data['price'],
            'purchase_price' => $data['purchase_price'] ?? null,
            'compare_price'  => $this->validatedComparePrice($data['compare_price'] ?? null, $data['price']),
            'stock'          => $data['stock'],
            'sku'            => $data['sku'] ?? null,
            'weight'         => $data['weight'] ?? null,
            'image'          => $request->hasFile('image')
                ? $request->file('image')->store('variants', 'public') : null,
        ]);

        foreach ($data['attributes'] ?? [] as $attr) {
            $variant->attributes()->create([
                'attribute_name'  => $attr['name'],
                'attribute_value' => $attr['value'],
            ]);
        }

        $this->syncProductPriceFromVariants($product);
        $this->clearProductCaches();

        return response()->json(['success' => true, 'variant' => $variant->load('attributes')]);
    }

    public function updateVariant(Request $request, $productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = ProductVariant::where('product_id', $productId)->findOrFail($variantId);
        $this->normalizeVariantPrices($request);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'price'          => 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'compare_price'  => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'sku'            => 'nullable|string|unique:product_variants,sku,' . $variantId,
            'weight'         => 'nullable|numeric|min:0',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'attributes'     => 'nullable|array',
            'attributes.*.name'  => 'required|string',
            'attributes.*.value' => 'required|string',
        ]);

        $imagePath = $variant->image;
        if ($request->hasFile('image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('image')->store('variants', 'public');
        }

        $variant->update([
            'name'           => $data['name'],
            'price'          => $data['price'],
            'purchase_price' => $data['purchase_price'] ?? null,
            'compare_price'  => $this->validatedComparePrice($data['compare_price'] ?? null, $data['price']),
            'stock'          => $data['stock'],
            'sku'            => $data['sku'] ?? $variant->sku,
            'weight'         => $data['weight'] ?? $variant->weight,
            'image'          => $imagePath,
        ]);

        $variant->attributes()->delete();
        foreach ($data['attributes'] ?? [] as $attr) {
            $variant->attributes()->create([
                'attribute_name'  => $attr['name'],
                'attribute_value' => $attr['value'],
            ]);
        }

        $this->syncProductPriceFromVariants($product);
        $this->clearProductCaches();

        return response()->json(['success' => true, 'variant' => $variant->load('attributes')]);
    }

    public function destroyVariant($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = ProductVariant::where('product_id', $productId)->findOrFail($variantId);

        if ($variant->image) Storage::disk('public')->delete($variant->image);
        $variant->delete();

        $this->syncProductPriceFromVariants($product);
        $this->clearProductCaches();

        return response()->json(['success' => true]);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function normalizePrices(Request $request): void
    {
        foreach (['price', 'modal_price', 'compare_price'] as $field) {
            $val = $request->input($field);
            if ($val === null || $val === '') {
                $request->merge([$field => null]);
                continue;
            }
            $request->merge([$field => (int) $this->parseIndonesianNumber((string) $val)]);
        }
    }

    private function normalizeVariantPrices(Request $request): void
    {
        foreach (['price', 'purchase_price', 'compare_price'] as $field) {
            $val = $request->input($field);
            if ($val === null || $val === '') {
                $request->merge([$field => null]);
                continue;
            }
            $request->merge([$field => (int) $this->parseIndonesianNumber((string) $val)]);
        }
    }

    private function parseIndonesianNumber(string $val): int
    {
        // "155.000" (Indonesian thousands) → 155000
        // "155000"  (plain integer)         → 155000
        if (str_contains($val, '.') && strlen(explode('.', $val)[1] ?? '') === 3) {
            $val = str_replace('.', '', $val);
        }
        return (int) $val;
    }

    private function validatedComparePrice(?int $comparePrice, int $price): ?int
    {
        if (!$comparePrice || $comparePrice <= $price) return null;
        return $comparePrice;
    }

    private function syncProductPriceFromVariants(Product $product): void
    {
        $minPrice = $product->variants()->min('price') ?? 0;
        $product->update([
            'price' => $minPrice,
            'stock' => $product->variants()->sum('stock'),
        ]);
    }

    private function buildPriceRange(?int $min, ?int $max): string
    {
        if (!$min && !$max) return '-';
        if ($min === $max) return 'Rp ' . number_format($min, 0, ',', '.');
        return 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
    }

    private function clearProductCaches(): void
    {
        Cache::forget('search.popular_terms');
        Cache::forget('search.active_product_names');
    }

    private function syncVariants(Product $product, array $variants): void
    {
        $incomingIds = collect($variants)->pluck('id')->filter()->values();

        $product->variants()->whereNotIn('id', $incomingIds)->each(function ($v) {
            if ($v->image) Storage::disk('public')->delete($v->image);
            $v->delete();
        });

        foreach ($variants as $variantData) {
            $price          = (int) $this->parseIndonesianNumber((string) ($variantData['price'] ?? 0));
            $purchasePrice  = isset($variantData['purchase_price']) && $variantData['purchase_price'] !== ''
                ? (int) $this->parseIndonesianNumber((string) $variantData['purchase_price']) : null;
            $comparePrice   = $this->validatedComparePrice(
                isset($variantData['compare_price']) && $variantData['compare_price'] !== ''
                    ? (int) $this->parseIndonesianNumber((string) $variantData['compare_price']) : null,
                $price
            );

            if (!empty($variantData['id'])) {
                $variant = ProductVariant::find($variantData['id']);
                if ($variant) {
                    $variant->update([
                        'name'           => $variantData['name'],
                        'price'          => $price,
                        'purchase_price' => $purchasePrice,
                        'compare_price'  => $comparePrice,
                        'stock'          => $variantData['stock'],
                        'sku'            => $variantData['sku'] ?? $variant->sku,
                        'weight'         => $variantData['weight'] ?? $variant->weight,
                    ]);
                    $variant->attributes()->delete();
                }
            } else {
                $variant = $product->variants()->create([
                    'name'           => $variantData['name'],
                    'price'          => $price,
                    'purchase_price' => $purchasePrice,
                    'compare_price'  => $comparePrice,
                    'stock'          => $variantData['stock'],
                    'sku'            => $variantData['sku'] ?? null,
                    'weight'         => $variantData['weight'] ?? null,
                    'image'          => null,
                ]);
            }

            foreach ($variantData['attributes'] ?? [] as $attr) {
                $variant->attributes()->create([
                    'attribute_name'  => $attr['name'],
                    'attribute_value' => $attr['value'],
                ]);
            }
        }

        $this->syncProductPriceFromVariants($product);
    }
}
