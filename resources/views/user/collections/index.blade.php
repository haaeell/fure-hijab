@extends('layouts.customer')

@section('title', $catalogMeta['title'] . ' - ' . $globalStoreName)

@php
    $activeRoute      = $catalogMeta['route'];
    $activeCategory   = $collection ?? false ? null : request('category');
    $activeAvailability = request('availability');
    $activeSort       = request('sort');
    $collectionTabs   = [
        ['label' => 'Best Seller',     'route' => 'best-seller.index'],
        ['label' => 'Hijab',           'route' => 'hijab.index'],
        ['label' => "Syar'i",          'route' => 'syari.index'],
        ['label' => 'New Arrived',     'route' => 'new-arrived.index'],
        ['label' => 'All Collections', 'route' => 'collections.index'],
    ];
    $catalogDescriptions = [
        'best-seller.index' => 'Koleksi favorit pelanggan ' . $globalStoreName . ', dipilih dari produk yang paling sering dibeli dan mudah dipadukan.',
        'hijab.index'       => 'Pilihan hijab premium dengan warna lembut, bahan nyaman, dan tampilan rapi untuk aktivitas harian.',
        'syari.index'       => 'Siluet santun dan clean untuk tampilan modest yang tetap ringan, modern, dan elegan.',
        'new-arrived.index' => 'Drop terbaru ' . $globalStoreName . ' untuk melengkapi wardrobe modest kamu dengan warna dan bahan pilihan.',
        'collections.index' => 'Semua koleksi ' . $globalStoreName . ' dalam satu katalog, dari hijab harian sampai pilihan spesial.',
    ];
    $catalogSeoDescription  = $catalogDescriptions[$activeRoute] ?? $catalogDescriptions['collections.index'];
    $catalogSeoImageProduct = $products->first(fn($item) => $item->images->first());
    $catalogSeoImage        = $catalogSeoImageProduct
        ? asset('storage/' . $catalogSeoImageProduct->images->first()->image_url)
        : asset('favicon.ico');
@endphp

@section('seo_title', $catalogMeta['title'])
@section('seo_description', $catalogSeoDescription)
@section('seo_keywords', $catalogMeta['title'] . ', ' . $globalStoreName . ', hijab premium, hijab wanita, modest wear, koleksi hijab')
@section('seo_image', $catalogSeoImage)
@section('canonical', route($activeRoute))

@push('seo')
    @php
        $itemListSchema = [
            '@context'       => 'https://schema.org',
            '@type'          => 'ItemList',
            'name'           => $catalogMeta['title'],
            'description'    => $catalogSeoDescription,
            'url'            => route($activeRoute),
            'numberOfItems'  => $products->count(),
            'itemListElement'=> $products->values()->map(function ($item, $index) {
                $image = $item->images->first();
                $product = [
                    '@type'  => 'Product',
                    'name'   => $item->name,
                    'image'  => $image ? asset('storage/' . $image->image_url) : asset('favicon.ico'),
                    'offers' => [
                        '@type'          => 'Offer',
                        'priceCurrency'  => 'IDR',
                        'price'          => (float) $item->price,
                        'availability'   => $item->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    ],
                ];
                if ($item->reviews_count > 0) {
                    $product['aggregateRating'] = [
                        '@type'       => 'AggregateRating',
                        'ratingValue' => round((float) $item->reviews_avg_rating, 1),
                        'reviewCount' => (int) $item->reviews_count,
                        'bestRating'  => 5,
                        'worstRating' => 1,
                    ];
                }
                return [
                    '@type'    => 'ListItem',
                    'position' => $index + 1,
                    'url'      => route('collections.show', $item->slug),
                    'item'     => $product,
                ];
            })->all(),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($itemListSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@push('styles')
<style>
    /* Range slider mushroom accent */
    input[type="range"].price-slider {
        -webkit-appearance: none;
        appearance: none;
        height: 2px;
        background: #e8e0d8;
        outline: none;
        border-radius: 0;
    }
    input[type="range"].price-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 14px;
        height: 14px;
        background: #A78B6F;
        border-radius: 50%;
        cursor: pointer;
    }
    input[type="range"].price-slider::-moz-range-thumb {
        width: 14px;
        height: 14px;
        background: #A78B6F;
        border-radius: 50%;
        cursor: pointer;
        border: none;
    }
    /* Toolbar & sidebar follow fixed nav height via CSS variable set by layout JS */
    @media (min-width: 1024px) {
        #catalogToolbar { top: var(--nav-h, 93px); }
        #catalogSidebar { top: calc(var(--nav-h, 93px) + 48px); }
    }
    /* Sort select clean look */
    .sort-select {
        -webkit-appearance: none;
        appearance: none;
        padding-right: 1.5rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23A78B6F'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0 center;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white">

    {{-- ── Collection Header ─────────────────────────────────────────── --}}
    <div class="border-b border-gray-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 py-3 text-[10px] font-bold uppercase tracking-[0.22em] text-brand-dark/40">
                <a href="/" class="transition hover:text-brand-primary">Home</a>
                <span>/</span>
                <span class="text-brand-dark">{{ $catalogMeta['title'] }}</span>
            </nav>
        </div>
        <div class="pb-8 pt-4 text-center">
            <h1 class="text-xl font-bold uppercase tracking-[0.32em] text-brand-dark sm:text-2xl lg:text-3xl">
                {{ $catalogMeta['title'] }}
            </h1>
            <p class="mt-2 text-[11px] tracking-[0.12em] text-brand-dark/35">
                {{ $products->total() }} {{ $products->total() === 1 ? 'Product' : 'Products' }}
            </p>
        </div>
    </div>

    {{-- ── Toolbar (sticky below fixed nav on desktop) ───────────────── --}}
    <div id="catalogToolbar" class="sticky top-0 z-40 border-b border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-12 items-center gap-4">

                {{-- Grid toggles --}}
                <div class="flex items-center gap-0.5 border-r border-gray-100 pr-4">
                    <button type="button" id="grid3Btn" onclick="setGrid(3)" title="3 kolom"
                        class="flex h-8 w-8 items-center justify-center text-brand-dark transition hover:text-brand-primary">
                        <svg width="14" height="14" viewBox="0 0 12 12" fill="currentColor">
                            <rect x="0"   y="0"    width="3.3" height="3.3"/>
                            <rect x="4.3" y="0"    width="3.3" height="3.3"/>
                            <rect x="8.6" y="0"    width="3.3" height="3.3"/>
                            <rect x="0"   y="4.3"  width="3.3" height="3.3"/>
                            <rect x="4.3" y="4.3"  width="3.3" height="3.3"/>
                            <rect x="8.6" y="4.3"  width="3.3" height="3.3"/>
                            <rect x="0"   y="8.6"  width="3.3" height="3.3"/>
                            <rect x="4.3" y="8.6"  width="3.3" height="3.3"/>
                            <rect x="8.6" y="8.6"  width="3.3" height="3.3"/>
                        </svg>
                    </button>
                    <button type="button" id="grid4Btn" onclick="setGrid(4)" title="4 kolom"
                        class="flex h-8 w-8 items-center justify-center text-brand-dark/30 transition hover:text-brand-primary">
                        <svg width="14" height="14" viewBox="0 0 12 12" fill="currentColor">
                            <rect x="0"    y="0"    width="2.4" height="2.4"/>
                            <rect x="3.2"  y="0"    width="2.4" height="2.4"/>
                            <rect x="6.4"  y="0"    width="2.4" height="2.4"/>
                            <rect x="9.6"  y="0"    width="2.4" height="2.4"/>
                            <rect x="0"    y="3.2"  width="2.4" height="2.4"/>
                            <rect x="3.2"  y="3.2"  width="2.4" height="2.4"/>
                            <rect x="6.4"  y="3.2"  width="2.4" height="2.4"/>
                            <rect x="9.6"  y="3.2"  width="2.4" height="2.4"/>
                            <rect x="0"    y="6.4"  width="2.4" height="2.4"/>
                            <rect x="3.2"  y="6.4"  width="2.4" height="2.4"/>
                            <rect x="6.4"  y="6.4"  width="2.4" height="2.4"/>
                            <rect x="9.6"  y="6.4"  width="2.4" height="2.4"/>
                            <rect x="0"    y="9.6"  width="2.4" height="2.4"/>
                            <rect x="3.2"  y="9.6"  width="2.4" height="2.4"/>
                            <rect x="6.4"  y="9.6"  width="2.4" height="2.4"/>
                            <rect x="9.6"  y="9.6"  width="2.4" height="2.4"/>
                        </svg>
                    </button>
                </div>

                {{-- Collection tabs — desktop only --}}
                <div class="no-scrollbar hidden flex-1 items-center gap-7 overflow-x-auto lg:flex">
                    @foreach($collectionTabs as $tab)
                        <a href="{{ route($tab['route']) }}"
                            class="flex-none whitespace-nowrap text-[11px] font-bold uppercase tracking-[0.18em] transition
                                {{ $activeRoute === $tab['route']
                                    ? 'border-b-2 border-brand-primary pb-px text-brand-dark'
                                    : 'text-brand-dark/40 hover:text-brand-dark' }}">
                            {{ $tab['label'] }}
                        </a>
                    @endforeach
                </div>

                {{-- Right: mobile filter btn + sort --}}
                <div class="ml-auto flex items-center gap-4">
                    <button type="button" id="mobileFilterBtn"
                        class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-brand-dark lg:hidden">
                        <i class="fa-solid fa-sliders text-[10px] text-brand-primary"></i>
                        Filter
                    </button>
                    <form action="{{ route($activeRoute) }}" method="GET" class="flex items-center">
                        @foreach(request()->except('sort', 'page') as $key => $value)
                            @if(is_scalar($value))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <label class="mr-2 hidden text-[11px] font-bold uppercase tracking-[0.18em] text-brand-dark/40 lg:block">Sort</label>
                        <select name="sort" onchange="this.form.submit()"
                            class="sort-select border-0 bg-transparent text-[11px] font-bold uppercase tracking-[0.18em] text-brand-dark outline-none cursor-pointer">
                            <option value="">Featured</option>
                            <option value="best_seller" @selected($activeSort === 'best_seller')>Best Selling</option>
                            <option value="price_low"   @selected($activeSort === 'price_low')>Price, Low to High</option>
                            <option value="price_high"  @selected($activeSort === 'price_high')>Price, High to Low</option>
                        </select>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Main Layout ────────────────────────────────────────────────── --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex gap-0 lg:gap-10 py-8">

            {{-- ── Sidebar ─────────────────────────────────────────────── --}}
            <aside class="hidden w-52 flex-shrink-0 lg:block">
                <div id="catalogSidebar" class="sticky top-0 space-y-0">

                    <form action="{{ route($activeRoute) }}" method="GET" id="filterForm">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        {{-- Availability --}}
                        <div class="border-b border-gray-100 pb-6 mb-6">
                            <h2 class="mb-4 text-[11px] font-bold uppercase tracking-[0.24em] text-brand-dark">Availability</h2>
                            <div class="space-y-3">
                                <label class="flex cursor-pointer items-center justify-between group">
                                    <span class="text-[13px] text-brand-dark/70 group-hover:text-brand-dark transition">
                                        In stock
                                        <span class="text-brand-dark/35">({{ $inStockCount }})</span>
                                    </span>
                                    <input type="radio" name="availability" value="in_stock" onchange="this.form.submit()"
                                        class="h-4 w-4 border-gray-300 accent-brand-primary cursor-pointer"
                                        @checked($activeAvailability === 'in_stock')>
                                </label>
                                <label class="flex cursor-pointer items-center justify-between group">
                                    <span class="text-[13px] text-brand-dark/70 group-hover:text-brand-dark transition">
                                        Out of stock
                                        <span class="text-brand-dark/35">({{ $outOfStockCount }})</span>
                                    </span>
                                    <input type="radio" name="availability" value="out_of_stock" onchange="this.form.submit()"
                                        class="h-4 w-4 border-gray-300 accent-brand-primary cursor-pointer"
                                        @checked($activeAvailability === 'out_of_stock')>
                                </label>
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="border-b border-gray-100 pb-6 mb-6">
                            <h2 class="mb-4 text-[11px] font-bold uppercase tracking-[0.24em] text-brand-dark">Price</h2>
                            <div class="space-y-4">
                                <input type="range" class="price-slider w-full" id="priceSlider"
                                    min="0" max="5000000" step="10000"
                                    value="{{ request('max_price', 5000000) }}"
                                    oninput="document.getElementById('maxPriceInput').value = this.value">
                                <div class="flex items-stretch gap-2">
                                    <div class="flex-1 border border-gray-200 px-3 py-2">
                                        <p class="text-[9px] font-bold uppercase tracking-widest text-brand-dark/35">Min</p>
                                        <div class="flex items-center gap-0.5">
                                            <span class="text-[11px] font-semibold text-brand-dark/40">Rp</span>
                                            <input type="number" name="min_price" id="minPriceInput"
                                                value="{{ request('min_price', 0) }}"
                                                placeholder="0"
                                                class="w-full bg-transparent text-xs font-bold text-brand-dark outline-none">
                                        </div>
                                    </div>
                                    <div class="flex items-center text-brand-dark/20">–</div>
                                    <div class="flex-1 border border-gray-200 px-3 py-2">
                                        <p class="text-[9px] font-bold uppercase tracking-widest text-brand-dark/35">Max</p>
                                        <div class="flex items-center gap-0.5">
                                            <span class="text-[11px] font-semibold text-brand-dark/40">Rp</span>
                                            <input type="number" name="max_price" id="maxPriceInput"
                                                value="{{ request('max_price') }}"
                                                placeholder="–"
                                                class="w-full bg-transparent text-xs font-bold text-brand-dark outline-none">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit"
                                    class="w-full border border-brand-dark/20 py-2.5 text-[11px] font-bold uppercase tracking-[0.18em] text-brand-dark transition hover:border-brand-primary hover:bg-brand-primary hover:text-white">
                                    Apply
                                </button>
                            </div>
                        </div>

                        {{-- Category --}}
                        @unless($collection ?? false)
                        <div class="pb-6">
                            <h2 class="mb-4 text-[11px] font-bold uppercase tracking-[0.24em] text-brand-dark">Category</h2>
                            <div class="space-y-0.5">
                                <a href="{{ route($activeRoute, request()->except('category', 'page')) }}"
                                    class="flex items-center justify-between py-2 text-[13px] transition
                                        {{ !$activeCategory ? 'font-bold text-brand-dark' : 'font-medium text-brand-dark/50 hover:text-brand-dark' }}">
                                    <span>All Products</span>
                                    @if(!$activeCategory)
                                        <span class="h-1.5 w-1.5 rounded-full bg-brand-primary"></span>
                                    @endif
                                </a>
                                @foreach($categories as $cat)
                                    <a href="{{ route($activeRoute, array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                        class="flex items-center justify-between py-2 text-[13px] transition
                                            {{ $activeCategory === $cat->slug ? 'font-bold text-brand-dark' : 'font-medium text-brand-dark/50 hover:text-brand-dark' }}">
                                        <span>{{ $cat->name }}</span>
                                        <span class="text-[11px] text-brand-dark/25">{{ $cat->products_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endunless

                    </form>

                    @if(request()->hasAny(['search', 'category', 'availability', 'min_price', 'max_price']))
                        <a href="{{ route($activeRoute) }}"
                            class="block text-[11px] font-bold uppercase tracking-[0.16em] text-brand-dark/35 transition hover:text-brand-primary">
                            <i class="fa-solid fa-rotate-left mr-1 text-[10px]"></i> Clear All Filters
                        </a>
                    @endif
                </div>
            </aside>

            {{-- ── Product Grid ─────────────────────────────────────────── --}}
            <div class="flex-1 min-w-0">

                {{-- Mobile collection tabs --}}
                <div class="no-scrollbar mb-5 -mx-4 flex gap-2 overflow-x-auto px-4 lg:hidden">
                    @foreach($collectionTabs as $tab)
                        <a href="{{ route($tab['route']) }}"
                            class="flex-none border px-4 py-2 text-[11px] font-bold uppercase tracking-[0.14em] transition
                                {{ $activeRoute === $tab['route']
                                    ? 'border-brand-primary bg-brand-primary text-white'
                                    : 'border-brand-secondary/60 text-brand-dark/55 hover:border-brand-primary hover:text-brand-primary' }}">
                            {{ $tab['label'] }}
                        </a>
                    @endforeach
                </div>

                @if($products->count() > 0)
                    {{-- Hairline grid: gap-px with bg as divider color --}}
                    <div id="productGrid" class="grid grid-cols-2 gap-px sm:grid-cols-3 bg-[#e8e0d8]">
                        @foreach($products as $product)
                            <div class="bg-white p-4 sm:p-5">
                                @include('user.components.product-card', [
                                    'product'      => $product,
                                    'isFlashSale'  => $product->compare_price > $product->price
                                ])
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-14 flex justify-center">
                        {{ $products->links('vendor.pagination.custom-tailwind') }}
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <div class="mb-6 flex h-16 w-16 items-center justify-center bg-[#eee5dc] text-brand-primary">
                            <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                        </div>
                        <h3 class="mb-2 text-base font-bold uppercase tracking-[0.18em] text-brand-dark">
                            No Products Found
                        </h3>
                        <p class="mb-8 max-w-sm text-sm text-brand-dark/45">
                            Coba ubah filter untuk menemukan koleksi yang sesuai.
                        </p>
                        <a href="{{ route($activeRoute) }}"
                            class="border border-brand-dark px-8 py-3 text-[11px] font-bold uppercase tracking-[0.2em] text-brand-dark transition hover:bg-brand-dark hover:text-white">
                            Lihat Semua
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- ── Mobile Filter Drawer ─────────────────────────────────────────── --}}
<div id="mobileFilterDrawer" class="fixed inset-0 z-[250] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" id="mobileFilterBackdrop"></div>
    <div class="absolute left-0 top-0 h-full w-72 max-w-[85vw] overflow-y-auto bg-white shadow-2xl">
        {{-- Drawer header --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="text-[11px] font-bold uppercase tracking-[0.24em] text-brand-dark">Filter</h2>
            <button type="button" id="mobileFilterClose"
                class="flex h-8 w-8 items-center justify-center text-brand-dark/40 hover:text-brand-dark transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route($activeRoute) }}" method="GET" class="px-5 py-5 space-y-6">
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            {{-- Collection --}}
            <div class="border-b border-gray-100 pb-5">
                <h3 class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-brand-dark">Collection</h3>
                <div class="space-y-1">
                    @foreach($collectionTabs as $tab)
                        <a href="{{ route($tab['route']) }}"
                            class="flex items-center justify-between py-2.5 text-sm transition
                                {{ $activeRoute === $tab['route'] ? 'font-bold text-brand-primary' : 'font-medium text-brand-dark/60 hover:text-brand-dark' }}">
                            {{ $tab['label'] }}
                            @if($activeRoute === $tab['route'])
                                <i class="fa-solid fa-check text-[10px] text-brand-primary"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Availability --}}
            <div class="border-b border-gray-100 pb-5">
                <h3 class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-brand-dark">Availability</h3>
                <div class="space-y-3">
                    <label class="flex cursor-pointer items-center justify-between">
                        <span class="text-sm font-medium text-brand-dark/70">
                            In stock <span class="text-brand-dark/35">({{ $inStockCount }})</span>
                        </span>
                        <input type="radio" name="availability" value="in_stock"
                            class="h-4 w-4 accent-brand-primary" @checked($activeAvailability === 'in_stock')>
                    </label>
                    <label class="flex cursor-pointer items-center justify-between">
                        <span class="text-sm font-medium text-brand-dark/70">
                            Out of stock <span class="text-brand-dark/35">({{ $outOfStockCount }})</span>
                        </span>
                        <input type="radio" name="availability" value="out_of_stock"
                            class="h-4 w-4 accent-brand-primary" @checked($activeAvailability === 'out_of_stock')>
                    </label>
                </div>
            </div>

            {{-- Category --}}
            @unless($collection ?? false)
            <div class="border-b border-gray-100 pb-5">
                <h3 class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-brand-dark">Category</h3>
                <div class="space-y-1">
                    <a href="{{ route($activeRoute, request()->except('category', 'page')) }}"
                        class="block py-2 text-sm transition {{ !$activeCategory ? 'font-bold text-brand-dark' : 'font-medium text-brand-dark/55 hover:text-brand-dark' }}">
                        All Products
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route($activeRoute, array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                            class="flex items-center justify-between py-2 text-sm transition {{ $activeCategory === $cat->slug ? 'font-bold text-brand-dark' : 'font-medium text-brand-dark/55 hover:text-brand-dark' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-xs text-brand-dark/30">{{ $cat->products_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endunless

            {{-- Price --}}
            <div>
                <h3 class="mb-3 text-[11px] font-bold uppercase tracking-[0.22em] text-brand-dark">Price</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="border border-gray-200 px-3 py-2">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-brand-dark/35">Min</p>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Rp"
                            class="w-full bg-transparent text-sm font-bold text-brand-dark outline-none">
                    </div>
                    <div class="border border-gray-200 px-3 py-2">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-brand-dark/35">Max</p>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Rp"
                            class="w-full bg-transparent text-sm font-bold text-brand-dark outline-none">
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-brand-dark py-3 text-[11px] font-bold uppercase tracking-[0.2em] text-white transition hover:bg-brand-primary">
                Apply Filters
            </button>

            @if(request()->hasAny(['search', 'category', 'availability', 'min_price', 'max_price']))
                <a href="{{ route($activeRoute) }}"
                    class="block text-center text-[11px] font-bold uppercase tracking-[0.16em] text-brand-dark/35 transition hover:text-brand-primary">
                    Clear All
                </a>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ── Grid toggle ──────────────────────────────────────────────────────
    window.setGrid = function (cols) {
        var grid = document.getElementById('productGrid');
        if (!grid) return;

        grid.className = 'grid gap-px bg-[#e8e0d8]';
        if (cols === 3) {
            grid.classList.add('grid-cols-2', 'sm:grid-cols-3');
        } else {
            grid.classList.add('grid-cols-2', 'sm:grid-cols-4');
        }

        var btn3 = document.getElementById('grid3Btn');
        var btn4 = document.getElementById('grid4Btn');
        btn3.classList.toggle('text-brand-dark',    cols === 3);
        btn3.classList.toggle('text-brand-dark/30', cols !== 3);
        btn4.classList.toggle('text-brand-dark',    cols === 4);
        btn4.classList.toggle('text-brand-dark/30', cols !== 4);
    };

    // ── Mobile filter drawer ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        var drawer   = document.getElementById('mobileFilterDrawer');
        var backdrop = document.getElementById('mobileFilterBackdrop');
        var closeBtn = document.getElementById('mobileFilterClose');
        var openBtn  = document.getElementById('mobileFilterBtn');

        function openDrawer() {
            drawer.classList.remove('hidden');
            drawer.removeAttribute('aria-hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeDrawer() {
            drawer.classList.add('hidden');
            drawer.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        openBtn  && openBtn.addEventListener('click',  openDrawer);
        backdrop && backdrop.addEventListener('click', closeDrawer);
        closeBtn && closeBtn.addEventListener('click', closeDrawer);
    });
</script>
@endpush
