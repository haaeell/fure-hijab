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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'images' => fn($q) => $q->where('is_primary', true)])
            ->latest()
            ->get();
        $categories  = Category::where('is_active', true)->get();
        $brands      = Brand::where('is_active', true)->get();
        $collections = Collection::where('is_active', true)->orderBy('sort_order')->get();

        return view('master.products.index', compact('products', 'categories', 'brands', 'collections'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'brand', 'images', 'variants.attributes', 'collections'])
            ->findOrFail($id);

        return response()->json(array_merge($product->toArray(), [
            'collection_ids' => $product->collections->pluck('id'),
        ]));
    }

    public function store(Request $request)
    {
        $this->normalizePrices($request);

        $data = $request->validate([
            'name'              => 'required|string|max:255|unique:products,name',
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'modal_price'       => 'nullable|numeric|min:0',
            'stock'             => 'required_if:has_variant,0|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|unique:products,sku',
            'is_active'         => 'nullable',
            'has_variant'       => 'nullable|boolean',
            'images.*'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'collection_ids'    => 'nullable|array',
            'collection_ids.*'  => 'exists:collections,id',
        ], [
            'name.required'     => 'Nama produk wajib diisi',
            'name.unique'       => 'Nama produk sudah terdaftar',
            'category_id.required' => 'Kategori wajib dipilih',
            'price.required'    => 'Harga wajib diisi',
            'stock.required_if' => 'Stok wajib diisi untuk produk tanpa varian',
        ]);

        DB::beginTransaction();
        try {
            $hasVariant = $request->boolean('has_variant');

            $product = Product::create([
                'category_id'       => $data['category_id'],
                'brand_id'          => $data['brand_id'] ?? null,
                'name'              => $data['name'],
                'slug'              => Str::slug($data['name']),
                'description'       => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'price'             => $data['price'],
                'compare_price'     => $data['compare_price'] ?? null,
                'modal_price'       => $data['modal_price'] ?? null,
                'stock'             => $hasVariant ? 0 : ($data['stock'] ?? 0),
                'weight'            => $data['weight'] ?? null,
                'sku'               => $data['sku'] ?? null,
                'is_active'         => $request->has('is_active'),
                'has_variant'       => $hasVariant,
            ]);

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url'  => $path,
                        'is_primary'  => $index === 0,
                        'sort_order'  => $index,
                    ]);
                }
            }

            // Handle variants
            if ($hasVariant && $request->has('variants')) {
                $this->syncVariants($product, $request->input('variants', []));
            }

            // Sync collections
            $product->collections()->sync($request->input('collection_ids', []));

            DB::commit();
            return redirect()->back()->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->normalizePrices($request);

        $data = $request->validate([
            'name'              => 'required|string|max:255|unique:products,name,' . $id,
            'category_id'       => 'required|exists:categories,id',
            'brand_id'          => 'nullable|exists:brands,id',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price'             => 'required|numeric|min:0',
            'compare_price'     => 'nullable|numeric|min:0',
            'modal_price'       => 'nullable|numeric|min:0',
            'stock'             => 'required_if:has_variant,0|min:0',
            'weight'            => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|unique:products,sku,' . $id,
            'is_active'         => 'nullable',
            'has_variant'       => 'nullable|boolean',
            'images.*'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'collection_ids'    => 'nullable|array',
            'collection_ids.*'  => 'exists:collections,id',
        ], [
            'name.required'     => 'Nama produk wajib diisi',
            'name.unique'       => 'Nama produk sudah terdaftar',
            'category_id.required' => 'Kategori wajib dipilih',
            'price.required'    => 'Harga wajib diisi',
            'stock.required_if' => 'Stok wajib diisi untuk produk tanpa varian',
            'images.*.image'    => 'File harus berupa gambar',
            'images.*.max'      => 'Ukuran gambar maksimal 2MB',
        ]);

        DB::beginTransaction();
        try {
            $hasVariant = $request->boolean('has_variant');

            $product->update([
                'category_id'       => $data['category_id'],
                'brand_id'          => $data['brand_id'] ?? null,
                'name'              => $data['name'],
                'slug'              => Str::slug($data['name']),
                'description'       => $data['description'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'price'             => $data['price'],
                'compare_price'     => $data['compare_price'] ?? null,
                'modal_price'       => $data['modal_price'] ?? null,
                'stock'             => $hasVariant ? $product->stock : ($data['stock'] ?? 0),
                'weight'            => $data['weight'] ?? null,
                'sku'               => $data['sku'] ?? $product->sku,
                'is_active'         => $request->has('is_active'),
                'has_variant'       => $hasVariant,
            ]);

            // Handle new images
            if ($request->hasFile('images')) {
                $lastOrder = $product->images()->max('sort_order') ?? -1;
                $hasPrimary = $product->images()->where('is_primary', true)->exists();

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url'  => $path,
                        'is_primary'  => !$hasPrimary && $index === 0,
                        'sort_order'  => $lastOrder + $index + 1,
                    ]);
                }
            }

            // Handle variants
            if ($hasVariant && $request->has('variants')) {
                $this->syncVariants($product, $request->input('variants', []));
            } elseif (!$hasVariant) {
                // Remove all variants if switching to no-variant mode
                foreach ($product->variants as $variant) {
                    if ($variant->image) {
                        Storage::disk('public')->delete($variant->image);
                    }
                }
                $product->variants()->delete();
            }

            // Sync collections
            $product->collections()->sync($request->input('collection_ids', []));

            DB::commit();
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
            }
            foreach ($product->variants as $variant) {
                if ($variant->image) {
                    Storage::disk('public')->delete($variant->image);
                }
            }
            $product->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus produk');
        }
    }

    // ─── Image helpers ────────────────────────────────────────────────────────

    public function destroyImage($productId, $imageId)
    {
        $image = ProductImage::where('product_id', $productId)->findOrFail($imageId);
        Storage::disk('public')->delete($image->image_url);

        $wasPrimary = $image->is_primary;
        $image->delete();

        // Promote next image as primary
        if ($wasPrimary) {
            ProductImage::where('product_id', $productId)
                ->orderBy('sort_order')
                ->first()
                ?->update(['is_primary' => true]);
        }

        return response()->json(['success' => true]);
    }

    public function setPrimaryImage($productId, $imageId)
    {
        ProductImage::where('product_id', $productId)->update(['is_primary' => false]);
        ProductImage::where('product_id', $productId)->where('id', $imageId)->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }

    // ─── Variant CRUD (standalone endpoints) ─────────────────────────────────

    public function storeVariant(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'price'      => 'required|numeric|min:0',
            'stock'      => 'required|integer|min:0',
            'sku'        => 'nullable|string|unique:product_variants,sku',
            'weight'     => 'nullable|numeric|min:0',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'attributes' => 'nullable|array',
            'attributes.*.name'  => 'required|string',
            'attributes.*.value' => 'required|string',
        ]);

        $variant = $product->variants()->create([
            'name'   => $data['name'],
            'price'  => $data['price'],
            'stock'  => $data['stock'],
            'sku'    => $data['sku'] ?? null,
            'weight' => $data['weight'] ?? null,
            'image'  => $request->hasFile('image')
                ? $request->file('image')->store('variants', 'public')
                : null,
        ]);

        foreach ($data['attributes'] ?? [] as $attr) {
            $variant->attributes()->create([
                'attribute_name'  => $attr['name'],
                'attribute_value' => $attr['value'],
            ]);
        }

        // Sync product stock
        $product->update(['stock' => $product->variants()->sum('stock')]);

        return response()->json(['success' => true, 'variant' => $variant->load('attributes')]);
    }

    public function updateVariant(Request $request, $productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = ProductVariant::where('product_id', $productId)->findOrFail($variantId);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'price'      => 'required|numeric|min:0',
            'stock'      => 'required|integer|min:0',
            'sku'        => 'nullable|string|unique:product_variants,sku,' . $variantId,
            'weight'     => 'nullable|numeric|min:0',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'attributes' => 'nullable|array',
            'attributes.*.name'  => 'required|string',
            'attributes.*.value' => 'required|string',
        ]);

        $imagePath = $variant->image;
        if ($request->hasFile('image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('image')->store('variants', 'public');
        }

        $variant->update([
            'name'   => $data['name'],
            'price'  => $data['price'],
            'stock'  => $data['stock'],
            'sku'    => $data['sku'] ?? $variant->sku,
            'weight' => $data['weight'] ?? $variant->weight,
            'image'  => $imagePath,
        ]);

        // Sync attributes
        $variant->attributes()->delete();
        foreach ($data['attributes'] ?? [] as $attr) {
            $variant->attributes()->create([
                'attribute_name'  => $attr['name'],
                'attribute_value' => $attr['value'],
            ]);
        }

        $product->update(['stock' => $product->variants()->sum('stock')]);

        return response()->json(['success' => true, 'variant' => $variant->load('attributes')]);
    }

    public function destroyVariant($productId, $variantId)
    {
        $product = Product::findOrFail($productId);
        $variant = ProductVariant::where('product_id', $productId)->findOrFail($variantId);

        if ($variant->image) Storage::disk('public')->delete($variant->image);
        $variant->delete();

        $product->update(['stock' => $product->variants()->sum('stock')]);

        return response()->json(['success' => true]);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Strip Indonesian thousands separators (dots) from price fields so
     * "155.000" is treated as 155000, not 155. Runs before validation.
     */
    private function normalizePrices(Request $request): void
    {
        foreach (['price', 'modal_price', 'compare_price'] as $field) {
            $val = $request->input($field);
            if ($val === null || $val === '') {
                $request->merge([$field => null]);
                continue;
            }
            $val = (string) $val;
            // "155.000" (Indonesian thousands) → strip dots → 155000
            // "155000"  (plain integer)         → unchanged → 155000
            // "100000.00" (DB decimal)          → use intval first → 100000
            if (str_contains($val, '.') && !str_ends_with($val, '.00') && strlen(explode('.', $val)[1] ?? '') === 3) {
                // Indonesian formatted: last segment after dot is 3 digits (thousands separator)
                $val = str_replace('.', '', $val);
            }
            $request->merge([$field => (int) $val]);
        }
    }

    private function syncVariants(Product $product, array $variants): void
    {
        $incomingIds = collect($variants)->pluck('id')->filter()->values();

        // Delete removed variants
        $product->variants()->whereNotIn('id', $incomingIds)->each(function ($v) {
            if ($v->image) Storage::disk('public')->delete($v->image);
            $v->delete();
        });

        foreach ($variants as $variantData) {
            $imagePath = null;

            if (!empty($variantData['id'])) {
                $variant = ProductVariant::find($variantData['id']);
                if ($variant) {
                    $imagePath = $variant->image;
                    $variant->update([
                        'name'   => $variantData['name'],
                        'price'  => $variantData['price'],
                        'stock'  => $variantData['stock'],
                        'sku'    => $variantData['sku'] ?? $variant->sku,
                        'weight' => $variantData['weight'] ?? $variant->weight,
                        'image'  => $imagePath,
                    ]);
                    $variant->attributes()->delete();
                }
            } else {
                $variant = $product->variants()->create([
                    'name'   => $variantData['name'],
                    'price'  => $variantData['price'],
                    'stock'  => $variantData['stock'],
                    'sku'    => $variantData['sku'] ?? null,
                    'weight' => $variantData['weight'] ?? null,
                    'image'  => null,
                ]);
            }

            foreach ($variantData['attributes'] ?? [] as $attr) {
                $variant->attributes()->create([
                    'attribute_name'  => $attr['name'],
                    'attribute_value' => $attr['value'],
                ]);
            }
        }

        // Sync total stock
        $product->update(['stock' => $product->variants()->sum('stock')]);
    }
}
