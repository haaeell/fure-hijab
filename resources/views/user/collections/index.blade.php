@extends('layouts.customer')

@section('title', $catalogMeta['title'] . ' - FURE')

@php
    $activeRoute = $catalogMeta['route'];
    $activeCategory = isset($collectionCategory) && $collectionCategory ? null : request('category');
    $activeAvailability = request('availability');
    $activeSort = request('sort');
    $collectionTabs = [
        ['label' => 'Best Seller', 'route' => 'best-seller.index'],
        ['label' => 'Hijab', 'route' => 'hijab.index'],
        ['label' => "Syar'i", 'route' => 'syari.index'],
        ['label' => 'New Arrived', 'route' => 'new-arrived.index'],
        ['label' => 'All Collections', 'route' => 'collections.index'],
    ];
    $catalogDescriptions = [
        'best-seller.index' => 'Koleksi favorit pelanggan FURE, dipilih dari produk yang paling sering dibeli dan mudah dipadukan.',
        'hijab.index' => 'Pilihan hijab premium dengan warna lembut, bahan nyaman, dan tampilan rapi untuk aktivitas harian.',
        'syari.index' => 'Siluet santun dan clean untuk tampilan modest yang tetap ringan, modern, dan elegan.',
        'new-arrived.index' => 'Drop terbaru FURE untuk melengkapi wardrobe modest kamu dengan warna dan bahan pilihan.',
        'collections.index' => 'Semua koleksi FURE dalam satu katalog, dari hijab harian sampai pilihan spesial.',
    ];
@endphp

@section('content')
    <section class="bg-[#f8f3ee] text-brand-dark">
        <div class="bg-white">
            <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">
                <nav class="mb-5 flex text-[10px] font-bold uppercase tracking-[0.22em] text-brand-dark/45">
                    <a href="/" class="transition hover:text-brand-primary">Home</a>
                    <span class="mx-2 text-brand-secondary">/</span>
                    <span class="text-brand-dark">{{ $catalogMeta['title'] }}</span>
                </nav>

                <div class="grid gap-7 lg:grid-cols-[1fr_380px] lg:items-end">
                    <div>
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-[0.26em] text-brand-primary">
                            FURE Collection
                        </p>
                        <h1 class="max-w-3xl text-3xl font-semibold leading-tight tracking-normal sm:text-4xl lg:text-5xl">
                            {{ $catalogMeta['title'] }}
                        </h1>
                        <p class="mt-4 max-w-xl text-sm leading-7 text-brand-dark/60">
                            {{ $catalogDescriptions[$activeRoute] ?? $catalogDescriptions['collections.index'] }}
                        </p>
                    </div>

                    <form action="{{ route($activeRoute) }}" method="GET" class="relative">
                        @foreach(request()->except('search', 'page') as $key => $value)
                            @if(is_scalar($value))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk"
                            class="h-[52px] w-full border border-brand-secondary/70 bg-[#f8f3ee] px-12 text-sm font-semibold text-brand-dark outline-none transition focus:border-brand-primary focus:bg-white focus:ring-4 focus:ring-brand-primary/10">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-brand-primary/70"></i>
                        @if(request('search'))
                            <a href="{{ route($activeRoute, request()->except('search', 'page')) }}"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-brand-dark/45 transition hover:text-brand-primary">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="mx-auto max-w-7xl px-4 pb-6 sm:px-6 lg:px-8">
                <div class="no-scrollbar flex gap-2 overflow-x-auto border-t border-brand-secondary/40 pt-5 text-[11px] font-bold uppercase tracking-[0.16em]">
                @foreach($collectionTabs as $tab)
                    <a href="{{ route($tab['route']) }}"
                        class="flex-none border px-4 py-2.5 transition {{ $activeRoute === $tab['route'] ? 'border-brand-primary bg-brand-primary text-white' : 'border-brand-secondary/60 bg-white text-brand-dark/60 hover:border-brand-primary hover:text-brand-primary' }}">
                        {{ $tab['label'] }}
                    </a>
                @endforeach
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 grid gap-3 lg:hidden">
                @unless(isset($collectionCategory) && $collectionCategory)
                    <div class="no-scrollbar -mx-4 flex gap-2 overflow-x-auto px-4">
                        <a href="{{ route($activeRoute, request()->except('category', 'page')) }}"
                            class="flex-none border px-4 py-2 text-xs font-bold uppercase tracking-[0.12em] transition {{ !$activeCategory ? 'border-brand-primary bg-brand-primary text-white' : 'border-brand-secondary/70 bg-white text-brand-dark/65' }}">
                            Semua
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route($activeRoute, array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                class="flex-none border px-4 py-2 text-xs font-bold uppercase tracking-[0.12em] transition {{ $activeCategory === $cat->slug ? 'border-brand-primary bg-brand-primary text-white' : 'border-brand-secondary/70 bg-white text-brand-dark/65' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                @endunless

                <form action="{{ route($activeRoute) }}" method="GET" class="grid grid-cols-2 gap-2">
                    @foreach(request()->except('sort', 'page') as $key => $value)
                        @if(is_scalar($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="sort" onchange="this.form.submit()"
                        class="border border-brand-secondary/70 bg-white px-3 py-3 text-xs font-bold uppercase tracking-[0.12em] text-brand-dark outline-none focus:border-brand-primary">
                        <option value="">Terbaru</option>
                        <option value="best_seller" @selected($activeSort === 'best_seller')>Best Seller</option>
                        <option value="price_low" @selected($activeSort === 'price_low')>Harga Rendah</option>
                        <option value="price_high" @selected($activeSort === 'price_high')>Harga Tinggi</option>
                    </select>
                    <a href="{{ route($activeRoute) }}"
                        class="flex items-center justify-center border border-brand-secondary/70 bg-white px-3 py-3 text-xs font-bold uppercase tracking-[0.12em] text-brand-dark transition hover:border-brand-primary">
                        Reset Filter
                    </a>
                </form>
            </div>

            <div class="grid gap-8 lg:grid-cols-[270px_1fr]">
                <aside class="hidden lg:block">
                    <div class="sticky top-36 space-y-5">
                        <div class="bg-white p-5 shadow-sm">
                            <h2 class="mb-4 text-xs font-bold uppercase tracking-[0.22em] text-brand-dark">Collections</h2>
                            <div class="space-y-1">
                                @foreach($collectionTabs as $tab)
                                    <a href="{{ route($tab['route']) }}"
                                        class="flex items-center justify-between px-3 py-2.5 text-sm font-semibold transition {{ $activeRoute === $tab['route'] ? 'bg-[#f8f3ee] text-brand-primary' : 'text-brand-dark/65 hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                                        {{ $tab['label'] }}
                                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        @unless(isset($collectionCategory) && $collectionCategory)
                            <div class="bg-white p-5 shadow-sm">
                                <h2 class="mb-4 text-xs font-bold uppercase tracking-[0.22em] text-brand-dark">Category</h2>
                                <div class="space-y-1">
                                    <a href="{{ route($activeRoute, request()->except('category', 'page')) }}"
                                        class="flex items-center justify-between px-3 py-2.5 text-sm font-semibold transition {{ !$activeCategory ? 'bg-[#f8f3ee] text-brand-primary' : 'text-brand-dark/60 hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                                        <span>All Products</span>
                                    </a>
                                    @foreach($categories as $cat)
                                        <a href="{{ route($activeRoute, array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                            class="flex items-center justify-between px-3 py-2.5 text-sm font-semibold transition {{ $activeCategory === $cat->slug ? 'bg-[#f8f3ee] text-brand-primary' : 'text-brand-dark/60 hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                                            <span>{{ $cat->name }}</span>
                                            <span class="text-xs font-medium text-brand-dark/35">{{ $cat->products_count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endunless

                        <form action="{{ route($activeRoute) }}" method="GET" class="space-y-5 bg-white p-5 shadow-sm">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if($activeCategory)
                                <input type="hidden" name="category" value="{{ $activeCategory }}">
                            @endif

                            <div>
                                <h2 class="mb-4 text-xs font-bold uppercase tracking-[0.22em] text-brand-dark">Availability</h2>
                                <label class="mb-3 flex items-center justify-between gap-3 bg-[#f8f3ee] px-3 py-2.5 text-sm font-semibold text-brand-dark/65">
                                    <span>Ready Stock</span>
                                    <input type="radio" name="availability" value="in_stock" onchange="this.form.submit()"
                                        class="text-brand-primary focus:ring-brand-primary" @checked($activeAvailability === 'in_stock')>
                                </label>
                                <label class="flex items-center justify-between gap-3 bg-[#f8f3ee] px-3 py-2.5 text-sm font-semibold text-brand-dark/65">
                                    <span>Sold Out</span>
                                    <input type="radio" name="availability" value="out_of_stock" onchange="this.form.submit()"
                                        class="text-brand-primary focus:ring-brand-primary" @checked($activeAvailability === 'out_of_stock')>
                                </label>
                            </div>

                            <div>
                                <h2 class="mb-4 text-xs font-bold uppercase tracking-[0.22em] text-brand-dark">Price</h2>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min"
                                        class="w-full border border-brand-secondary/70 bg-[#f8f3ee] px-3 py-3 text-xs font-semibold outline-none focus:border-brand-primary focus:bg-white">
                                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max"
                                        class="w-full border border-brand-secondary/70 bg-[#f8f3ee] px-3 py-3 text-xs font-semibold outline-none focus:border-brand-primary focus:bg-white">
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full bg-brand-dark px-4 py-3 text-xs font-bold uppercase tracking-[0.18em] text-white transition hover:bg-brand-primary">
                                Terapkan
                            </button>
                            <a href="{{ route($activeRoute) }}"
                                class="block text-center text-xs font-bold uppercase tracking-[0.16em] text-brand-dark/45 transition hover:text-brand-primary">
                                Clear Filter
                            </a>
                        </form>
                    </div>
                </aside>

                <div>
                    <div class="mb-5 flex flex-col gap-4 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-brand-dark/50">
                                Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                            </p>
                            @if(request()->hasAny(['search', 'category', 'availability', 'min_price', 'max_price']))
                                <a href="{{ route($activeRoute) }}" class="mt-2 inline-flex text-xs font-bold uppercase tracking-[0.16em] text-brand-primary hover:text-brand-dark">
                                    Clear active filters
                                </a>
                            @endif
                        </div>
                        <form action="{{ route($activeRoute) }}" method="GET" class="hidden items-center gap-3 lg:flex">
                            @foreach(request()->except('sort', 'page') as $key => $value)
                                @if(is_scalar($value))
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <label class="text-xs font-bold uppercase tracking-[0.18em] text-brand-dark/45">Sort</label>
                            <select name="sort" onchange="this.form.submit()"
                                class="border border-brand-secondary/70 bg-[#f8f3ee] px-4 py-3 text-xs font-bold uppercase tracking-[0.12em] text-brand-dark outline-none focus:border-brand-primary">
                                <option value="">Terbaru</option>
                                <option value="best_seller" @selected($activeSort === 'best_seller')>Best Seller</option>
                                <option value="price_low" @selected($activeSort === 'price_low')>Harga Rendah</option>
                                <option value="price_high" @selected($activeSort === 'price_high')>Harga Tinggi</option>
                            </select>
                        </form>
                    </div>

                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 lg:gap-4">
                            @foreach($products as $product)
                                <div class="bg-white p-2 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-3">
                                    @include('user.components.product-card', ['product' => $product, 'isFlashSale' => $product->compare_price > $product->price])
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-14 flex justify-center">
                            <div class="bg-white p-2 shadow-sm">
                                {{ $products->links('vendor.pagination.custom-tailwind') }}
                            </div>
                        </div>
                    @else
                        <div class="border border-dashed border-brand-secondary/70 bg-white px-6 py-20 text-center">
                            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center bg-[#eee5dc] text-brand-primary">
                                <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                            </div>
                            <h3 class="mb-2 text-xl font-semibold text-brand-dark">Produk Tidak Ditemukan</h3>
                            <p class="mx-auto mb-8 max-w-sm text-sm leading-6 text-brand-dark/55">
                                Coba ubah kata kunci, kategori, atau filter harga untuk menemukan koleksi yang sesuai.
                            </p>
                            <a href="{{ route($activeRoute) }}"
                                class="inline-flex bg-brand-primary px-6 py-3 text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-brand-dark">
                                Lihat Semua Produk
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
