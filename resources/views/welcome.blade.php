@extends('layouts.customer')

@section('title', 'Koleksi Hijab Premium')

@php
    $fallbackHero = 'https://images.unsplash.com/photo-1585435465945-bef5a93f8849?auto=format&fit=crop&q=85&w=1800';
    $fallbackEditorial = 'https://images.unsplash.com/photo-1618232118117-98d49b20e2f5?auto=format&fit=crop&q=85&w=1400';
    $categoryBlocks = $categories->take(5);
    $defaultBanners = collect([
        (object) [
            'eyebrow' => 'New Collection',
            'title' => 'FURE',
            'subtitle' => 'Koleksi hijab dan modest wear bernuansa lembut, clean, dan siap dipakai dari hari biasa sampai momen spesial.',
            'image' => null,
            'primary_button_text' => 'Belanja Sekarang',
            'primary_button_url' => route('new-arrived.index'),
            'secondary_button_text' => 'Lihat Koleksi',
            'secondary_button_url' => route('collections.index'),
        ],
    ]);
    $heroBanners = $landingBanners->count() > 0 ? $landingBanners : $defaultBanners;
    $defaultSections = collect([
        (object) [
            'eyebrow' => 'Best Seller',
            'title' => 'Hijab Favorit Minggu Ini',
            'subtitle' => 'Pilihan yang paling sering dibeli untuk daily look.',
            'button_text' => 'Shop Now',
            'button_url' => route('best-seller.index'),
            'image' => null,
            'icon' => 'fa-solid fa-star',
            'background_color' => '#d8c8b8',
            'text_color' => '#5F4A3A',
        ],
        (object) [
            'eyebrow' => 'Special Promo',
            'title' => 'Voucher Hijab Deals',
            'subtitle' => 'Promo eksklusif untuk koleksi pilihan.',
            'button_text' => 'Claim Promo',
            'button_url' => route('promo.index'),
            'image' => null,
            'icon' => 'fa-solid fa-tags',
            'background_color' => '#5F4A3A',
            'text_color' => '#ffffff',
        ],
        (object) [
            'eyebrow' => 'New Arrival',
            'title' => 'Fresh Drop Setiap Pekan',
            'subtitle' => 'Model terbaru untuk melengkapi wardrobe modest kamu.',
            'button_text' => 'Explore',
            'button_url' => route('new-arrived.index'),
            'image' => null,
            'icon' => 'fa-solid fa-bag-shopping',
            'background_color' => '#eee5dc',
            'text_color' => '#5F4A3A',
        ],
    ]);
    $promoSections = $landingSections->count() > 0 ? $landingSections : $defaultSections;
    $shopLookHero = $shopLookProducts->first();
    $shopLookImage = $shopLookHero && $shopLookHero->images->first()
        ? asset('storage/' . $shopLookHero->images->first()->image_url)
        : $fallbackEditorial;
@endphp

@section('content')
    <div class="bg-[#f8f3ee] text-brand-dark">
        <section id="landingHero" class="relative min-h-[calc(100vh-7.25rem)] overflow-hidden bg-brand-dark md:min-h-[calc(100vh-5rem)]">
            @foreach($heroBanners as $index => $banner)
                @php
                    $bannerImage = $banner->image ? asset('storage/' . $banner->image) : $fallbackHero;
                @endphp
                <div class="landing-slide absolute inset-0 transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}"
                    data-slide="{{ $index }}">
                    <img src="{{ $bannerImage }}" alt="{{ $banner->title }}"
                        class="absolute inset-0 h-full w-full object-cover opacity-80">
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-dark/90 via-brand-dark/38 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-[#f8f3ee] to-transparent"></div>

                    <div class="relative mx-auto flex min-h-[calc(100vh-7.25rem)] max-w-7xl items-center px-4 py-16 sm:px-6 md:min-h-[calc(100vh-5rem)] lg:px-8">
                        <div class="max-w-2xl text-white">
                            @if($banner->eyebrow)
                                <p class="mb-5 inline-flex border border-white/45 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.28em]">
                                    {{ $banner->eyebrow }}
                                </p>
                            @endif
                            <h1 class="text-5xl font-semibold leading-[0.96] tracking-normal sm:text-6xl lg:text-7xl">
                                {{ $banner->title }}
                            </h1>
                            @if($banner->subtitle)
                                <p class="mt-6 max-w-lg text-sm leading-7 text-white/85 md:text-base">
                                    {{ $banner->subtitle }}
                                </p>
                            @endif
                            <div class="mt-8 flex flex-wrap gap-3">
                                @if($banner->primary_button_text && $banner->primary_button_url)
                                    <a href="{{ $banner->primary_button_url }}"
                                        class="inline-flex items-center gap-3 bg-white px-7 py-3 text-sm font-bold text-brand-dark transition hover:bg-brand-secondary">
                                        {{ $banner->primary_button_text }}
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </a>
                                @endif
                                @if($banner->secondary_button_text && $banner->secondary_button_url)
                                    <a href="{{ $banner->secondary_button_url }}"
                                        class="inline-flex items-center gap-3 border border-white/60 px-7 py-3 text-sm font-bold text-white transition hover:bg-white hover:text-brand-dark">
                                        {{ $banner->secondary_button_text }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($heroBanners->count() > 1)
                <div class="absolute bottom-8 left-1/2 z-20 flex -translate-x-1/2 gap-2">
                    @foreach($heroBanners as $index => $banner)
                        <button type="button" aria-label="Banner {{ $index + 1 }}"
                            class="landing-dot h-2.5 w-2.5 border border-white transition {{ $index === 0 ? 'bg-white' : 'bg-white/20' }}"
                            data-target="{{ $index }}"></button>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="border-y border-brand-secondary/50 bg-brand-primary text-white">
            <div class="no-scrollbar flex overflow-hidden whitespace-nowrap py-3 text-[11px] font-bold uppercase tracking-[0.22em]">
                @for ($i = 0; $i < 8; $i++)
                    <span class="mx-8">Free Shipping</span>
                    <span class="mx-8">Free Returns</span>
                    <span class="mx-8">Premium Daily Hijab</span>
                @endfor
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid gap-3 md:grid-cols-3">
                @foreach($promoSections as $section)
                    <a href="{{ $section->button_url ?: route('collections.index') }}"
                        class="group relative min-h-56 overflow-hidden p-6 md:min-h-64"
                        style="background: {{ $section->background_color }}; color: {{ $section->text_color }};">
                        @if($section->image)
                            <img src="{{ asset('storage/' . $section->image) }}" alt="{{ $section->title }}"
                                class="absolute inset-0 h-full w-full object-cover opacity-55 transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/10"></div>
                        @endif
                        <div class="relative z-10 flex h-full max-w-[16rem] flex-col justify-between">
                            <div>
                                @if($section->eyebrow)
                                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] opacity-75">{{ $section->eyebrow }}</p>
                                @endif
                                <h2 class="mt-3 text-3xl font-semibold leading-tight">{{ $section->title }}</h2>
                                @if($section->subtitle)
                                    <p class="mt-3 text-sm leading-6 opacity-75">{{ $section->subtitle }}</p>
                                @endif
                            </div>
                            <span class="mt-6 inline-flex w-fit border border-current px-4 py-2 text-xs font-bold uppercase tracking-[0.16em]">
                                {{ $section->button_text ?: 'Shop Now' }}
                            </span>
                        </div>
                        @if(!$section->image)
                            <i class="{{ $section->icon ?: 'fa-solid fa-bag-shopping' }} absolute bottom-5 right-5 text-7xl opacity-20 transition group-hover:scale-110"></i>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
            <div class="mb-7 flex items-end justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Shop by Category</p>
                    <h2 class="mt-2 text-3xl font-semibold">Pilih Kategori</h2>
                </div>
                <a href="{{ route('collections.index') }}" class="hidden text-sm font-bold text-brand-primary hover:text-brand-dark md:inline-flex">
                    View all
                </a>
            </div>

            <div class="no-scrollbar -mx-4 flex gap-3 overflow-x-auto px-4 md:mx-0 md:grid md:grid-cols-5 md:px-0">
                @foreach($categoryBlocks as $category)
                    <a href="{{ route('collections.index', ['category' => $category->slug]) }}"
                        class="group min-w-[180px] border border-brand-secondary/60 bg-white/70 p-3 transition hover:border-brand-primary hover:bg-white md:min-w-0">
                        <div class="aspect-[4/5] overflow-hidden bg-[#eee5dc]">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://images.unsplash.com/photo-1590159983013-d4ff5fc71c55?auto=format&fit=crop&q=80&w=600' }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                alt="{{ $category->name }}">
                        </div>
                        <div class="mt-3">
                            <h3 class="text-sm font-bold leading-tight">{{ $category->name }}</h3>
                            <p class="mt-1 text-xs text-brand-dark/55">{{ $category->products_count }} produk</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @if($bestSellerProducts->count() > 0)
            <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Most Loved</p>
                        <h2 class="mt-2 text-3xl font-semibold">Best Seller From FURE</h2>
                    </div>
                    <a href="{{ route('best-seller.index') }}" class="text-sm font-bold text-brand-primary hover:text-brand-dark">
                        View all products
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-4">
                    @foreach($bestSellerProducts as $product)
                        @include('user.components.product-card', ['product' => $product, 'isFlashSale' => $product->compare_price > $product->price])
                    @endforeach
                </div>
            </section>
        @endif

        @foreach($featuredCategorySections as $category)
            <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Featured Collection</p>
                        <h2 class="mt-2 text-3xl font-semibold">{{ strtoupper($category->name) }}</h2>
                    </div>
                    <a href="{{ route('collections.index', ['category' => $category->slug]) }}"
                        class="text-sm font-bold text-brand-primary hover:text-brand-dark">
                        View all products
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-4">
                    @foreach($category->featuredProducts as $product)
                        @include('user.components.product-card', ['product' => $product, 'isFlashSale' => $product->compare_price > $product->price])
                    @endforeach
                </div>
            </section>
        @endforeach

        <section id="new-arrival" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Fresh Drop</p>
                    <h2 class="mt-2 text-3xl font-semibold">New Arrival</h2>
                </div>
                <a href="{{ route('new-arrived.index') }}"
                    class="text-sm font-bold text-brand-primary hover:text-brand-dark">
                    View all products
                </a>
            </div>

            <div class="grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 lg:grid-cols-4">
                @foreach($latestProducts as $product)
                    @include('user.components.product-card', ['product' => $product, 'isFlashSale' => $product->compare_price > $product->price])
                @endforeach
            </div>
        </section>

        @if($flashSaleProducts->count() > 0)
            <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="bg-brand-dark px-5 py-8 text-white sm:px-8 lg:px-10">
                    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-secondary">Limited Offer</p>
                            <h2 class="mt-2 text-3xl font-semibold">Special Price</h2>
                        </div>
                        <a href="{{ route('promo.index') }}" class="text-sm font-bold text-brand-secondary hover:text-white">
                            View all promos
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-4">
                        @foreach($flashSaleProducts as $product)
                            <div class="bg-[#f8f3ee] p-3 text-brand-dark">
                                @include('user.components.product-card', ['product' => $product, 'isFlashSale' => true])
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-4 lg:grid-cols-[1.05fr_0.95fr] lg:items-stretch">
                <div class="relative min-h-[520px] overflow-hidden bg-brand-dark">
                    <img src="{{ $shopLookImage }}" alt="Shop the look FURE"
                        class="absolute inset-0 h-full w-full object-cover opacity-80">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/85 via-brand-dark/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 max-w-xl p-6 text-white sm:p-10">
                        <p class="mb-4 text-[11px] font-bold uppercase tracking-[0.28em] text-brand-secondary">Style Edit</p>
                        <h2 class="text-4xl font-semibold leading-tight">Shop The Look</h2>
                        <p class="mt-4 text-sm leading-7 text-white/75">
                            Padukan hijab favorit dengan warna lembut dan potongan modest yang mudah dipakai sepanjang hari.
                        </p>
                        <a href="{{ route('collections.index') }}"
                            class="mt-7 inline-flex bg-white px-6 py-3 text-xs font-bold uppercase tracking-[0.16em] text-brand-dark transition hover:bg-brand-secondary">
                            View products
                        </a>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                    @forelse($shopLookProducts as $product)
                        <a href="{{ route('collections.show', $product->slug) }}"
                            class="group grid grid-cols-[130px_1fr] gap-4 bg-white p-3 transition hover:bg-[#eee5dc] sm:grid-cols-1 lg:grid-cols-[150px_1fr]">
                            <div class="aspect-[3/4] overflow-hidden bg-[#eee5dc]">
                                <img src="{{ optional($product->images->first())->image_url ? asset('storage/' . $product->images->first()->image_url) : 'https://via.placeholder.com/400x533?text=FURE' }}"
                                    alt="{{ $product->name }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                            <div class="flex flex-col justify-center">
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">{{ $product->category->name }}</p>
                                <h3 class="mt-2 line-clamp-2 text-base font-semibold leading-snug">{{ $product->name }}</h3>
                                <p class="mt-3 text-sm font-bold">
                                    Rp{{ number_format($product->has_variant && $product->variants->count() > 0 ? $product->variants->first()->price : $product->price, 0, ',', '.') }}
                                </p>
                                <span class="mt-5 text-xs font-bold uppercase tracking-[0.16em] text-brand-primary">View this product</span>
                            </div>
                        </a>
                    @empty
                        @foreach($promoSections->take(2) as $section)
                            <a href="{{ $section->button_url ?: route('collections.index') }}"
                                class="group bg-white p-6 transition hover:bg-[#eee5dc]">
                                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">{{ $section->eyebrow }}</p>
                                <h3 class="mt-3 text-2xl font-semibold leading-tight">{{ $section->title }}</h3>
                                <span class="mt-6 inline-flex text-xs font-bold uppercase tracking-[0.16em] text-brand-primary">Shop the look</span>
                            </a>
                        @endforeach
                    @endforelse
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Stories</p>
                    <h2 class="mt-2 text-3xl font-semibold">FURE Journal</h2>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <article class="bg-white p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">Styling Guide</p>
                    <h3 class="mt-3 text-xl font-semibold leading-tight">Cara memilih hijab harian yang nyaman dan tetap rapi.</h3>
                    <p class="mt-4 text-sm leading-6 text-brand-dark/60">Mulai dari bahan, ukuran, hingga warna netral yang mudah dipadukan.</p>
                    <a href="{{ route('collections.index') }}" class="mt-6 inline-flex text-xs font-bold uppercase tracking-[0.16em] text-brand-primary">Read more</a>
                </article>
                <article class="bg-white p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">Fabric Notes</p>
                    <h3 class="mt-3 text-xl font-semibold leading-tight">Kenali bahan hijab untuk aktivitas dari pagi sampai malam.</h3>
                    <p class="mt-4 text-sm leading-6 text-brand-dark/60">Pilih tekstur yang ringan, mudah dibentuk, dan tidak cepat kusut.</p>
                    <a href="{{ route('hijab.index') }}" class="mt-6 inline-flex text-xs font-bold uppercase tracking-[0.16em] text-brand-primary">Read more</a>
                </article>
                <article class="bg-white p-5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-primary">Occasion</p>
                    <h3 class="mt-3 text-xl font-semibold leading-tight">Inspirasi modest look untuk acara spesial dan keluarga.</h3>
                    <p class="mt-4 text-sm leading-6 text-brand-dark/60">Tampil santun dengan palet lembut dan siluet yang elegan.</p>
                    <a href="{{ route('syari.index') }}" class="mt-6 inline-flex text-xs font-bold uppercase tracking-[0.16em] text-brand-primary">Read more</a>
                </article>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slides = Array.from(document.querySelectorAll('.landing-slide'));
            const dots = Array.from(document.querySelectorAll('.landing-dot'));
            if (slides.length <= 1) return;

            let active = 0;
            const showSlide = function (index) {
                active = index;
                slides.forEach(function (slide, slideIndex) {
                    slide.classList.toggle('opacity-100', slideIndex === active);
                    slide.classList.toggle('opacity-0', slideIndex !== active);
                    slide.classList.toggle('pointer-events-none', slideIndex !== active);
                });
                dots.forEach(function (dot, dotIndex) {
                    dot.classList.toggle('bg-white', dotIndex === active);
                    dot.classList.toggle('bg-white/20', dotIndex !== active);
                });
            };

            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    showSlide(Number(dot.dataset.target));
                });
            });

            setInterval(function () {
                showSlide((active + 1) % slides.length);
            }, 5500);
        });
    </script>
@endpush
