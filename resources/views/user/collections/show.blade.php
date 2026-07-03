@extends('layouts.customer')

@section('title', $product->name . ' - ' . $globalStoreName)

@section('content')
    @php
        if ($product->has_variant && $product->variants->count() > 0) {
            $priceMin         = $product->variants->min('price');
            $priceMax         = $product->variants->max('price');
            $isPriceRange     = $priceMin !== $priceMax;
            $displayPrice     = $priceMin;
            $displayComparePrice = null;
            $displayStock     = $product->stock;
        } else {
            $priceMin         = $product->price;
            $priceMax         = null;
            $isPriceRange     = false;
            $displayPrice     = $product->price;
            $displayComparePrice = $product->compare_price ?? null;
            $displayStock     = $product->stock;
        }

        $primaryImage  = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        $galleryImages = $product->images->count() > 0 ? $product->images : collect([$primaryImage])->filter();
        $isOutOfStock  = $product->stock <= 0;
    @endphp

    @section('seo_title', $product->name . ' — ' . $globalStoreName)
    @section('seo_description', $productSeo['description'] ?: 'Beli ' . $product->name . ' di ' . $globalStoreName . '. Hijab premium dengan bahan nyaman, warna lembut, dan tampilan modest untuk aktivitas harian hingga momen spesial. Pengiriman ke seluruh Indonesia.')
    @section('seo_keywords', $productSeo['keywords'] . ', beli ' . $product->name . ', harga ' . $product->name . ', ' . $globalStoreName . ' ' . ($product->category->name ?? 'hijab'))
    @section('seo_image', $productSeo['image'])
    @section('canonical', route('collections.show', $product->slug))
    @section('og_type', 'product')

    @push('seo')
        <script type="application/ld+json">{!! json_encode($productSeo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Koleksi', 'item' => route('collections.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $product->category->name ?? 'Hijab', 'item' => route('collections.index', ['category' => $product->category->slug ?? ''])],
                ['@type' => 'ListItem', 'position' => 4, 'name' => $product->name, 'item' => route('collections.show', $product->slug)],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="mobile-action-safe-space bg-[#f8f3ee]">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <nav class="mb-8 flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.24em] text-brand-dark/45">
                <a href="/" class="transition hover:text-brand-primary">Home</a>
                <span class="text-brand-secondary">/</span>
                <a href="{{ route('collections.index') }}" class="transition hover:text-brand-primary">Collections</a>
                <span class="text-brand-secondary">/</span>
                <span class="text-brand-dark">{{ $product->name }}</span>
            </nav>

            <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-start">
                <div class="space-y-4">
                    <div class="overflow-hidden bg-white">
                        <div class="relative aspect-[4/5] bg-[#eee5dc] {{ $isOutOfStock ? 'opacity-60' : '' }}">
                            <img id="mainImage"
                                src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/900x1125?text=' . urlencode($globalStoreName) }}"
                                class="h-full w-full object-cover transition-opacity duration-300" alt="{{ $product->name }}"
                                fetchpriority="high">

                            @if($isOutOfStock)
                                <div class="absolute left-4 top-4 bg-gray-500 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white">
                                    Stok Habis
                                </div>
                            @elseif($displayComparePrice && $displayComparePrice > $displayPrice)
                                <div class="absolute left-4 top-4 bg-brand-dark px-3 py-1 text-[10px] font-bold uppercase tracking-[0.18em] text-white">
                                    Sale
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($galleryImages->count() > 1)
                        <div class="no-scrollbar flex gap-3 overflow-x-auto pb-1" id="galleryThumbs">
                            @foreach($galleryImages as $index => $img)
                                <button type="button"
                                    data-src="{{ asset('storage/' . $img->image_url) }}"
                                    data-index="{{ $index }}"
                                    onclick="changeImage('{{ asset('storage/' . $img->image_url) }}', this)"
                                    class="gallery-thumb h-20 w-16 flex-none overflow-hidden border {{ $index === 0 ? 'border-brand-primary' : 'border-brand-secondary/60' }} bg-white transition-all duration-300 hover:border-brand-primary">
                                    <img src="{{ asset('storage/' . $img->image_url) }}" loading="lazy" class="h-full w-full object-cover" alt="{{ $product->name }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lg:sticky lg:top-36">
                    <div class="bg-white p-6 sm:p-8">
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-brand-primary">
                            {{ $product->category->full_name }}
                        </p>
                        <h1 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">
                            {{ $product->name }}
                        </h1>

                        <div class="mt-5 flex items-end gap-4 flex-wrap">
                            <div id="displayPrice" class="text-3xl font-semibold text-brand-dark">
                                Rp{{ number_format($priceMin, 0, ',', '.') }}@if($isPriceRange)<span id="displayPriceMax" class="text-2xl font-medium text-brand-dark/60"> &ndash; Rp{{ number_format($priceMax, 0, ',', '.') }}</span>@endif
                            </div>
                            @if($displayComparePrice && $displayComparePrice > $displayPrice)
                                <div id="displayComparePrice" class="pb-1 text-sm font-semibold text-brand-dark/35 line-through">
                                    Rp{{ number_format($displayComparePrice, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-dark/45">Stok</p>
                                <p class="mt-2 text-lg font-semibold text-brand-dark" id="productStock">{{ $displayStock }}</p>
                            </div>
                            <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-dark/45">Rating</p>
                                <p class="mt-2 text-lg font-semibold text-brand-dark">
                                    {{ number_format($product->reviews->avg('rating') ?: 0, 1) }}/5
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-brand-secondary/40 pt-6">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/45">Deskripsi</p>
                            <div class="prose prose-sm mt-4 max-w-none text-brand-dark/65">
                                {!! $product->description !!}
                            </div>
                        </div>

                        <form id="addToCartForm" class="mt-7 space-y-6">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="product_variant_id" id="selectedVariantId">

                            @if($product->has_variant && $product->variants->count() > 0)
                                @php
                                    // Kelompokkan nilai unik per tipe atribut
                                    $groupedAttrs = [];
                                    $outOfStockVariants = $product->variants->where('stock', '<=', 0)->pluck('id')->toArray();
                                    foreach ($product->variants as $variant) {
                                        foreach ($variant->attributes as $attr) {
                                            $groupedAttrs[$attr->attribute_name][] = [
                                                'value' => $attr->attribute_value,
                                                'out_of_stock' => in_array($variant->id, $outOfStockVariants),
                                            ];
                                        }
                                    }
                                    // Unikkan per tipe — jika ada nilai yg punya stok, tandai in_stock
                                    $groupedAttrsUnique = [];
                                    foreach ($groupedAttrs as $typeName => $entries) {
                                        $seen = [];
                                        foreach ($entries as $entry) {
                                            $v = $entry['value'];
                                            if (!isset($seen[$v])) {
                                                $seen[$v] = $entry['out_of_stock'];
                                            } else {
                                                // kalau ada 1 varian yang stoknya ada, tandai tersedia
                                                if (!$entry['out_of_stock']) $seen[$v] = false;
                                            }
                                        }
                                        $groupedAttrsUnique[$typeName] = $seen;
                                    }
                                    $hasAttributes = !empty($groupedAttrsUnique);
                                @endphp

                                @if($hasAttributes)
                                    <div class="space-y-5" id="variantSelection">
                                        @foreach($groupedAttrsUnique as $typeName => $valueMap)
                                            @php
                                                $isColorGroup = preg_match('/warna|colou?r/i', $typeName);
                                            @endphp
                                            <div class="variant-group" data-type="{{ $typeName }}" data-is-color="{{ $isColorGroup ? '1' : '0' }}">
                                                <p class="mb-3 flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/45">
                                                    {{ $typeName }}
                                                    <span class="variant-selected-label hidden items-center gap-1.5 normal-case tracking-normal font-semibold text-brand-dark/70 before:content-[':']"></span>
                                                </p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($valueMap as $value => $isOos)
                                                        @php
                                                            $hexMatch = [];
                                                            $hasColorHex = preg_match('/#(?:[0-9a-fA-F]{3}){1,2}/', $value, $hexMatch);
                                                            $colorHex = $hasColorHex ? strtoupper($hexMatch[0]) : null;
                                                            $colorLabel = $colorHex
                                                                ? trim(preg_replace('~\s*[-–—|:/()]?\s*#(?:[0-9a-fA-F]{3}){1,2}\s*~', ' ', $value))
                                                                : $value;
                                                            $colorLabel = $colorLabel !== '' ? $colorLabel : $colorHex;
                                                            $isColorOption = $isColorGroup && $colorHex;
                                                        @endphp
                                                        <button type="button"
                                                            data-type="{{ $typeName }}" data-value="{{ $value }}"
                                                            @if($isOos) disabled @endif
                                                            title="{{ $colorLabel }}"
                                                            aria-label="{{ $typeName }} {{ $colorLabel }}"
                                                            class="variant-btn relative border text-sm font-semibold transition
                                                                {{ $isColorOption ? 'inline-flex items-center gap-2 rounded-full px-3 py-2' : 'px-4 py-2' }}
                                                                {{ $isOos
                                                                    ? 'border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed opacity-60'
                                                                    : 'border-brand-secondary/60 bg-white text-brand-dark/65 hover:border-brand-primary hover:text-brand-primary' }}">
                                                            @if($isColorOption)
                                                                <span class="block h-6 w-6 flex-shrink-0 rounded-full border border-black/10 shadow-inner" style="background-color: {{ $colorHex }}"></span>
                                                                <span>{{ $colorLabel }}</span>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                            @if($isOos)
                                                                <span class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                                    <span class="w-full h-px bg-gray-300 absolute" style="transform:rotate(-15deg)"></span>
                                                                </span>
                                                            @endif
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Fallback: tidak ada atribut → tampilkan nama varian langsung --}}
                                    <div id="variantSelection">
                                        <p class="mb-3 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/45">Pilih Varian</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($product->variants->sortBy('price') as $variant)
                                                <button type="button" data-variant-id="{{ $variant->id }}"
                                                    @if($variant->stock <= 0) disabled @endif
                                                    class="variant-btn relative border px-4 py-2 text-sm font-semibold transition
                                                        {{ $variant->stock <= 0
                                                            ? 'border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed opacity-60'
                                                            : 'border-brand-secondary/60 bg-white text-brand-dark/65 hover:border-brand-primary hover:text-brand-primary' }}">
                                                    {{ $variant->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <div class="flex items-center gap-4">
                                <div class="flex items-center border border-brand-secondary/60 bg-white">
                                    <button type="button" onclick="adjustQty(-1)"
                                        class="h-12 w-12 text-brand-dark transition hover:bg-[#f8f3ee]">-</button>
                                    <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="{{ $displayStock }}"
                                        class="h-12 w-14 border-x border-brand-secondary/60 bg-transparent text-center text-sm font-semibold outline-none">
                                    <button type="button" onclick="adjustQty(1)"
                                        class="h-12 w-12 text-brand-dark transition hover:bg-[#f8f3ee]">+</button>
                                </div>
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-brand-dark/45">
                                    Sisa <span class="text-brand-dark" id="productStockLabel">{{ $displayStock }}</span>
                                </p>
                            </div>

                            <div class="desktop-only-action grid gap-3 sm:grid-cols-2">
                                <button type="submit" id="btnAddToCart"
                                    {{ $isOutOfStock ? 'disabled' : '' }}
                                    class="js-add-to-cart inline-flex items-center justify-center gap-3 border border-brand-primary bg-white px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] text-brand-dark transition hover:bg-[#eee5dc] {{ $isOutOfStock ? 'opacity-50 pointer-events-none' : '' }}">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <span class="js-add-text">{{ $isOutOfStock ? 'Stok Habis' : 'Tambah ke Keranjang' }}</span>
                                </button>
                                <button type="button"
                                    {{ $isOutOfStock ? 'disabled' : '' }}
                                    class="js-buy-now inline-flex items-center justify-center gap-3 bg-brand-primary px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] text-white transition hover:bg-brand-dark {{ $isOutOfStock ? 'opacity-50 pointer-events-none' : '' }}">
                                    <i class="fa-solid fa-bolt"></i>
                                    <span>{{ $isOutOfStock ? 'Stok Habis' : 'Beli Sekarang' }}</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" id="wishlistBtn"
                                    data-product-id="{{ $product->id }}"
                                    data-in-wishlist="{{ $inWishlist ? '1' : '0' }}"
                                    data-auth="{{ $isAuthenticated ? '1' : '0' }}"
                                    class="inline-flex w-full items-center justify-center gap-3 border px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] transition
                                        {{ $inWishlist
                                            ? 'border-brand-primary bg-[#f8f3ee] text-brand-primary'
                                            : 'border-brand-secondary/60 bg-white text-brand-dark hover:border-brand-primary' }}">
                                    <i id="wishlistIcon" class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                    <span id="wishlistLabel">{{ $inWishlist ? 'Tersimpan' : 'Wishlist' }}</span>
                                </button>
                                <button type="button" id="shareBtn"
                                    class="inline-flex w-full items-center justify-center gap-3 border border-brand-secondary/60 bg-white px-5 py-4 text-xs font-bold uppercase tracking-[0.18em] text-brand-dark transition hover:border-brand-primary hover:text-brand-primary">
                                    <i class="fa-solid fa-share-nodes"></i>
                                    <span>Bagikan</span>
                                </button>
                            </div>
                        </form>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-dark/45">Pengiriman</p>
                                <p class="mt-2 text-sm font-semibold text-brand-dark">Cepat & rapi</p>
                            </div>
                            <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-4">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-dark/45">Kualitas</p>
                                <p class="mt-2 text-sm font-semibold text-brand-dark">Material terpilih</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-16">
                <div class="space-y-8">
                    <section class="bg-white p-6 sm:p-8">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">Reviews</p>
                                <h2 class="mt-2 text-2xl font-semibold">Ulasan Pembeli</h2>
                            </div>
                            <span class="text-sm font-semibold text-brand-dark/45">({{ $totalReviews }} ulasan)</span>
                        </div>

                        @if($totalReviews > 0)
                            <div class="mt-6 grid gap-4 sm:grid-cols-[180px_1fr]">
                                <div class="border border-brand-secondary/40 bg-[#f8f3ee] p-5 text-center">
                                    <div class="text-5xl font-semibold text-brand-dark">{{ number_format($averageRating, 1) }}</div>
                                    <div class="mt-2 flex justify-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-star text-xs {{ $i <= round($averageRating) ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    @forelse($product->reviews->take(3) as $review)
                                        <div class="border border-brand-secondary/40 p-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="text-sm font-semibold text-brand-dark">{{ $review->user->name }}</p>
                                                <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-brand-dark/35">
                                                    {{ $review->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="mt-2 flex gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa-star text-xs {{ $i <= $review->rating ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="mt-3 text-sm leading-6 text-brand-dark/60">{{ $review->comment }}</p>
                                            @if(!empty($review->images))
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach($review->images as $img)
                                                        <a href="{{ asset('storage/' . $img) }}" target="_blank" rel="noopener">
                                                            <img src="{{ asset('storage/' . $img) }}" loading="lazy" class="h-16 w-16 object-cover border border-brand-secondary/30 hover:opacity-80 transition" alt="Foto ulasan">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                    @endforelse
                                </div>
                            </div>
                        @endif

                        @forelse($product->reviews->skip(3) as $review)
                            <div class="border-t border-brand-secondary/30 pt-5 mt-5">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center bg-brand-primary/10 text-brand-primary">
                                        <span class="text-sm font-bold">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-brand-dark">{{ $review->user->name }}</p>
                                            <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-brand-dark/35">
                                                {{ $review->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="mt-2 flex gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-star text-xs {{ $i <= $review->rating ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                        <p class="mt-3 text-sm leading-6 text-brand-dark/60">{{ $review->comment }}</p>
                                        @if(!empty($review->images))
                                            <div class="mt-3 flex flex-wrap gap-2">
                                                @foreach($review->images as $img)
                                                    <a href="{{ asset('storage/' . $img) }}" target="_blank" rel="noopener">
                                                        <img src="{{ asset('storage/' . $img) }}" loading="lazy" class="h-16 w-16 object-cover border border-brand-secondary/30 hover:opacity-80 transition" alt="Foto ulasan">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            @if($totalReviews === 0)
                                <div class="mt-8 border border-dashed border-brand-secondary/50 p-8 text-center text-brand-dark/45">
                                    Belum ada ulasan untuk produk ini.
                                </div>
                            @endif
                        @endforelse
                    </section>
                </div>
            </div>

            @if($relatedProducts->count() > 0)
                <div class="mt-16">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">Related</p>
                    <h2 class="mt-2 text-2xl font-semibold text-brand-dark">Produk Terkait</h2>
                    <div class="mt-6 grid grid-cols-2 gap-px bg-[#e8e0d8] sm:grid-cols-3 lg:grid-cols-4">
                        @foreach($relatedProducts as $rel)
                            <div class="bg-white p-4 sm:p-5">
                                @include('user.components.product-card', ['product' => $rel, 'isFlashSale' => $rel->compare_price > $rel->price])
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Share Modal --}}
    <div id="shareModal" class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" id="shareModalBackdrop"></div>
        <div class="relative z-10 w-full max-w-sm bg-white p-6 sm:rounded-none shadow-2xl mx-0 sm:mx-4">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-bold uppercase tracking-[0.2em] text-brand-dark">Bagikan Produk</h3>
                <button type="button" id="closeShareModal" class="text-brand-dark/40 hover:text-brand-dark transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="mb-5 flex items-center gap-3 border border-brand-secondary/40 bg-[#f8f3ee] p-3">
                @if($primaryImage)
                    <img src="{{ asset('storage/' . $primaryImage->image_url) }}" class="h-12 w-10 object-cover flex-shrink-0" alt="{{ $product->name }}">
                @endif
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-brand-dark truncate">{{ $product->name }}</p>
                    <p class="text-xs text-brand-primary font-bold mt-0.5">Rp{{ number_format($displayPrice, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-3 mb-5">
                <a id="shareWhatsapp" href="#" target="_blank" rel="noopener"
                    class="flex flex-col items-center gap-2 p-3 hover:bg-[#f8f3ee] transition rounded-sm">
                    <div class="flex h-11 w-11 items-center justify-center bg-[#25D366] text-white">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-brand-dark/60">WhatsApp</span>
                </a>
                <a id="shareTelegram" href="#" target="_blank" rel="noopener"
                    class="flex flex-col items-center gap-2 p-3 hover:bg-[#f8f3ee] transition rounded-sm">
                    <div class="flex h-11 w-11 items-center justify-center bg-[#229ED9] text-white">
                        <i class="fa-brands fa-telegram text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-brand-dark/60">Telegram</span>
                </a>
                <a id="shareFacebook" href="#" target="_blank" rel="noopener"
                    class="flex flex-col items-center gap-2 p-3 hover:bg-[#f8f3ee] transition rounded-sm">
                    <div class="flex h-11 w-11 items-center justify-center bg-[#1877F2] text-white">
                        <i class="fa-brands fa-facebook-f text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-brand-dark/60">Facebook</span>
                </a>
                <a id="shareTwitter" href="#" target="_blank" rel="noopener"
                    class="flex flex-col items-center gap-2 p-3 hover:bg-[#f8f3ee] transition rounded-sm">
                    <div class="flex h-11 w-11 items-center justify-center bg-black text-white">
                        <i class="fa-brands fa-x-twitter text-xl"></i>
                    </div>
                    <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-brand-dark/60">X (Twitter)</span>
                </a>
            </div>

            <button type="button" id="copyLinkBtn"
                class="flex w-full items-center justify-between gap-3 border border-brand-secondary/40 bg-[#f8f3ee] px-4 py-3 text-left transition hover:border-brand-primary group">
                <span id="copyLinkText" class="text-xs font-semibold text-brand-dark/60 truncate flex-1">{{ route('collections.show', $product->slug) }}</span>
                <span class="flex-shrink-0 text-[10px] font-bold uppercase tracking-[0.16em] text-brand-primary group-hover:text-brand-dark transition">
                    <i class="fa-solid fa-copy mr-1"></i>Salin
                </span>
            </button>
        </div>
    </div>

    <x-user.components.mobile-bottom-action-bar>
        <div class="grid grid-cols-2 gap-3">
            <button type="submit" form="addToCartForm"
                {{ $isOutOfStock ? 'disabled' : '' }}
                class="js-add-to-cart flex min-h-[52px] items-center justify-center gap-2 rounded-2xl border border-brand-primary bg-white px-3 text-[11px] font-black uppercase tracking-tight text-brand-dark {{ $isOutOfStock ? 'opacity-50 pointer-events-none' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="js-add-text">{{ $isOutOfStock ? 'Stok Habis' : 'Masukkan Keranjang' }}</span>
            </button>
            <button type="button"
                {{ $isOutOfStock ? 'disabled' : '' }}
                class="js-buy-now flex min-h-[52px] items-center justify-center gap-2 rounded-2xl bg-brand-primary px-3 text-[11px] font-black uppercase tracking-tight text-white shadow-lg shadow-brand-primary/25 {{ $isOutOfStock ? 'opacity-50 pointer-events-none' : '' }}">
                <i class="fa-solid fa-bolt"></i>
                <span>{{ $isOutOfStock ? 'Stok Habis' : 'Pesan Sekarang' }}</span>
            </button>
        </div>
    </x-user.components.mobile-bottom-action-bar>
@endsection

@push('scripts')
    <script>
        const variants = @json($product->variants->load('attributes'));


        const selectedChoices = {};

        function setActiveThumbnail(btn) {
            $('.gallery-thumb')
                .removeClass('border-brand-primary')
                .addClass('border-brand-secondary/60');
            if (btn) {
                $(btn).addClass('border-brand-primary').removeClass('border-brand-secondary/60');
            }
        }

        function switchMainImage(src) {
            const $img = $('#mainImage');
            $img.css('opacity', 0);
            setTimeout(function () {
                $img.attr('src', src);
                $img.css('opacity', 1);
            }, 200);
        }

        function changeImage(src, btn) {
            switchMainImage(src);
            setActiveThumbnail(btn || null);
        }

        function adjustQty(val) {
            const input = $('#qtyInput');
            const stock = parseInt($('#productStock').text(), 10) || 1;
            const current = parseInt(input.val(), 10) || 1;
            const next = current + val;
            if (next >= 1 && next <= stock) input.val(next);
        }

        const defaultProductImage = "{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : '' }}";

        function findMatchingVariant() {
            const chosen = Object.entries(selectedChoices);
            if (!chosen.length) return;

            const match = variants.find(v => {
                if (!v.attributes?.length) return false;
                return chosen.every(([type, value]) =>
                    v.attributes.some(a =>
                        a.attribute_name.trim() === type.trim() &&
                        a.attribute_value.trim() === value.trim()
                    )
                );
            });

            if (match) {
                selectVariant(match);
            } else {
                $('#selectedVariantId').val('');
            }
        }

        function selectVariant(match) {
            if (!match) return;

            $('#selectedVariantId').val(match.id);
            $('#displayPriceMax').remove();
            $('#displayPrice').text('Rp' + new Intl.NumberFormat('id-ID').format(match.price));

            if (match.compare_price && match.compare_price > match.price) {
                if (!$('#displayComparePrice').length) {
                    $('#displayPrice').after('<div id="displayComparePrice" class="pb-1 text-sm font-semibold text-brand-dark/35 line-through"></div>');
                }
                $('#displayComparePrice').text('Rp' + new Intl.NumberFormat('id-ID').format(match.compare_price)).show();
            } else {
                $('#displayComparePrice').hide();
            }

            $('#productStock').text(match.stock);
            $('#productStockLabel').text(match.stock);
            $('#qtyInput').attr('max', match.stock);

            if (parseInt($('#qtyInput').val(), 10) > match.stock) {
                $('#qtyInput').val(match.stock);
            }

            if (match.stock <= 0) {
                $('.js-add-to-cart').prop('disabled', true).addClass('opacity-50 pointer-events-none');
                $('.js-add-text').text('Stok Habis');
            } else {
                $('.js-add-to-cart').prop('disabled', false).removeClass('opacity-50 pointer-events-none');
                $('.js-add-text').text(function () {
                    return $(this).closest('.desktop-only-action').length ? 'Tambah ke Keranjang' : 'Masukkan Keranjang';
                });
            }

            // Ganti foto utama ke foto variant jika ada
            const $gallery = $('#galleryThumbs');
            $('#variantThumb').remove(); // hapus thumb variant sebelumnya

            if (match.image) {
                const variantImageUrl = '/storage/' + match.image;
                switchMainImage(variantImageUrl);

                // Tambahkan thumbnail sementara untuk foto variant di galeri
                if ($gallery.length) {
                    setActiveThumbnail(null);
                    const $thumb = $('<button>', {
                        id: 'variantThumb',
                        type: 'button',
                        class: 'gallery-thumb h-20 w-16 flex-none overflow-hidden border border-brand-primary bg-white transition-all duration-300'
                    }).on('click', function () {
                        switchMainImage(variantImageUrl);
                        setActiveThumbnail(this);
                    }).append(
                        $('<img>', { src: variantImageUrl, class: 'h-full w-full object-cover', alt: 'Foto varian' })
                    );
                    $gallery.prepend($thumb);
                }
            } else if (defaultProductImage) {
                switchMainImage(defaultProductImage);
                // Kembalikan highlight ke thumbnail pertama
                setActiveThumbnail($('.gallery-thumb[data-index="0"]')[0]);
            }
        }

        $(document).ready(function () {
            // Set thumbnail pertama aktif saat load
            setActiveThumbnail($('.gallery-thumb[data-index="0"]')[0] || null);

            // Wishlist toggle
            $('#wishlistBtn').on('click', function () {
                if ($(this).data('auth') == 0) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }
                const $btn = $(this);
                $btn.prop('disabled', true);
                $.post("{{ route('wishlist.toggle') }}", {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    product_id: $btn.data('product-id'),
                })
                .done(function (res) {
                    if (res.in_wishlist) {
                        $btn.removeClass('border-brand-secondary/60 bg-white text-brand-dark hover:border-brand-primary')
                            .addClass('border-brand-primary bg-[#f8f3ee] text-brand-primary');
                        $('#wishlistIcon').removeClass('fa-regular').addClass('fa-solid');
                        $('#wishlistLabel').text('Tersimpan');
                    } else {
                        $btn.removeClass('border-brand-primary bg-[#f8f3ee] text-brand-primary')
                            .addClass('border-brand-secondary/60 bg-white text-brand-dark hover:border-brand-primary');
                        $('#wishlistIcon').removeClass('fa-solid').addClass('fa-regular');
                        $('#wishlistLabel').text('Wishlist');
                    }
                    Swal.fire({
                        icon: res.in_wishlist ? 'success' : 'info',
                        title: res.message,
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 2000, timerProgressBar: true,
                    });
                })
                .fail(function () {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.', confirmButtonColor: '#A78B6F' });
                })
                .always(function () { $btn.prop('disabled', false); });
            });

            function variantHex(value) {
                const m = String(value || '').match(/#(?:[0-9a-fA-F]{3}){1,2}/);
                return m ? m[0].toUpperCase() : null;
            }
            function variantLabel(value) {
                return String(value || '').replace(/\s*[-–—|:/()]?\s*#(?:[0-9a-fA-F]{3}){1,2}\s*/g, ' ').trim() || value;
            }

            function updateSelectedLabel($group, value) {
                const $label = $group.find('.variant-selected-label');
                const isColor = $group.data('is-color') === '1' || $group.data('is-color') === 1;
                const hex = isColor ? variantHex(value) : null;
                const text = hex ? variantLabel(value) : value;

                $label.empty();
                if (hex) {
                    $label.append($('<span>').css({ display:'inline-block', width:'10px', height:'10px', borderRadius:'50%', background: hex, border:'1px solid rgba(0,0,0,0.12)', flexShrink: 0 }));
                }
                $label.append(document.createTextNode(text));
                $label.addClass('inline-flex').removeClass('hidden');
            }

            $(document).on('click', '.variant-btn:not([disabled])', function () {
                const $btn = $(this);

                if ($btn.data('variant-id')) {
                    // Mode fallback — pilih langsung by ID
                    $('.variant-btn').removeClass('border-brand-primary bg-[#eee5dc] text-brand-primary')
                        .addClass('border-brand-secondary/60 bg-white text-brand-dark/65');
                    $btn.addClass('border-brand-primary bg-[#eee5dc] text-brand-primary')
                        .removeClass('border-brand-secondary/60 bg-white text-brand-dark/65');
                    const match = variants.find(v => v.id == $btn.data('variant-id'));
                    if (match) selectVariant(match);
                    updateSelectedLabel($btn.closest('.variant-group'), $btn.text().trim());
                } else {
                    // Mode atribut — pilih per tipe, cari kombinasi
                    const type  = $btn.data('type');
                    const value = $btn.data('value');

                    const $group = $btn.closest('.variant-group');
                    $group.find('.variant-btn:not([disabled])')
                        .removeClass('border-brand-primary bg-[#eee5dc] text-brand-primary')
                        .addClass('border-brand-secondary/60 bg-white text-brand-dark/65');
                    $btn.addClass('border-brand-primary bg-[#eee5dc] text-brand-primary')
                        .removeClass('border-brand-secondary/60 bg-white text-brand-dark/65');

                    selectedChoices[type] = value;
                    findMatchingVariant();
                    updateSelectedLabel($group, value);
                }
            });

            $('#addToCartForm').on('submit', function (e) {
                e.preventDefault();
                submitCartAction('cart');
            });

            $('.js-buy-now').on('click', function () {
                submitCartAction('buy-now');
            });

            function submitCartAction(action) {
                @if($product->has_variant && $product->variants->count() > 0)
                    if (!$('#selectedVariantId').val()) {
                        const totalGroups = document.querySelectorAll('.variant-group').length;
                        const totalChosen = Object.keys(selectedChoices).length;
                        const msg = totalChosen < totalGroups
                            ? 'Silakan pilih semua pilihan varian terlebih dahulu.'
                            : 'Kombinasi varian yang dipilih tidak tersedia.';
                        Swal.fire({ icon: 'warning', title: 'Pilih Varian', text: msg });
                        return;
                    }
                @endif

                @if(!$isAuthenticated)
                    window.location.href = "{{ route('login') }}";
                    return;
                @endif

                const btn = action === 'buy-now' ? $('.js-buy-now') : $('.js-add-to-cart');
                btn.prop('disabled', true).addClass('opacity-70');

                $.ajax({
                    url: action === 'buy-now' ? "{{ route('cart.buy-now') }}" : "{{ route('cart.store') }}",
                    method: "POST",
                    data: $('#addToCartForm').serialize(),
                    success: function (response) {
                        if (response.status === 'success') {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                                return;
                            }

                            window.FureCartDrawer?.reloadAndOpen();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1400,
                                timerProgressBar: true,
                            });
                        }
                    },
                    error: function (xhr) {
                        const response = xhr.responseJSON;

                        if (response?.status === 'blocked' && response.redirect) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pesanan Belum Dibayar',
                                text: response.message,
                                confirmButtonText: 'Lihat Pesanan',
                            }).then(() => window.location.href = response.redirect);
                            return;
                        }

                        Swal.fire({ icon: 'error', title: 'Oops...', text: response?.message || 'Gagal menambah ke keranjang.' });
                    },
                    complete: function () {
                        btn.prop('disabled', false).removeClass('opacity-70');
                    }
                });
            }
        });

        // Share functionality
        const shareUrl = "{{ route('collections.show', $product->slug) }}";
        const shareTitle = "{{ addslashes($product->name) }}";
        const shareText = shareTitle + ' - Rp' + new Intl.NumberFormat('id-ID').format({{ $displayPrice }});

        function openShareModal() {
            const encodedUrl = encodeURIComponent(shareUrl);
            const encodedText = encodeURIComponent(shareText + '\n' + shareUrl);

            $('#shareWhatsapp').attr('href', 'https://wa.me/?text=' + encodedText);
            $('#shareTelegram').attr('href', 'https://t.me/share/url?url=' + encodedUrl + '&text=' + encodeURIComponent(shareText));
            $('#shareFacebook').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl);
            $('#shareTwitter').attr('href', 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareText) + '&url=' + encodedUrl);

            $('#shareModal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeShareModal() {
            $('#shareModal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#shareBtn').on('click', function () {
            if (navigator.share) {
                navigator.share({ title: shareTitle, text: shareText, url: shareUrl }).catch(() => {});
            } else {
                openShareModal();
            }
        });

        $('#closeShareModal, #shareModalBackdrop').on('click', closeShareModal);

        $('#copyLinkBtn').on('click', function () {
            navigator.clipboard.writeText(shareUrl).then(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Link disalin!',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 2000, timerProgressBar: true,
                });
                closeShareModal();
            }).catch(function () {
                const el = document.createElement('textarea');
                el.value = shareUrl;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                Swal.fire({
                    icon: 'success',
                    title: 'Link disalin!',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 2000, timerProgressBar: true,
                });
                closeShareModal();
            });
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') closeShareModal();
        });
    </script>
@endpush
