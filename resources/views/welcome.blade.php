@extends('layouts.customer')

@section('title', 'Koleksi Hijab Mushroom Premium')

@php
    $fallbackHero = 'https://images.unsplash.com/photo-1585435465945-bef5a93f8849?auto=format&fit=crop&q=85&w=1600';
    $categoryBlocks = $categories->take(6);
    $defaultBanners = collect([
        (object) [
            'eyebrow' => 'New Mushroom Collection',
            'title' => 'FURE',
            'subtitle' => 'Koleksi hijab dan modest wear bernuansa mushroom yang lembut, clean, dan siap dipakai dari hari biasa sampai momen spesial.',
            'image' => null,
            'primary_button_text' => 'Belanja Sekarang',
            'primary_button_url' => '#new-arrival',
            'secondary_button_text' => 'Semua Koleksi',
            'secondary_button_url' => route('collections.index'),
        ],
    ]);
    $heroBanners = $landingBanners->count() > 0 ? $landingBanners : $defaultBanners;
    $defaultSections = collect([
        (object) [
            'eyebrow' => 'Best Seller',
            'title' => 'Hijab Favorit Minggu Ini',
            'subtitle' => null,
            'button_text' => null,
            'button_url' => route('collections.index'),
            'image' => null,
            'icon' => 'fa-solid fa-shirt',
            'background_color' => '#d8c8b8',
            'text_color' => '#5F4A3A',
        ],
        (object) [
            'eyebrow' => 'Special Promo',
            'title' => 'Voucher Mushroom Deals',
            'subtitle' => null,
            'button_text' => null,
            'button_url' => route('promo.index'),
            'image' => null,
            'icon' => 'fa-solid fa-tags',
            'background_color' => '#5F4A3A',
            'text_color' => '#ffffff',
        ],
        (object) [
            'eyebrow' => 'New Arrival',
            'title' => 'Fresh Drop Setiap Pekan',
            'subtitle' => null,
            'button_text' => null,
            'button_url' => route('collections.index'),
            'image' => null,
            'icon' => 'fa-solid fa-bag-shopping',
            'background_color' => '#eee5dc',
            'text_color' => '#5F4A3A',
        ],
    ]);
    $promoSections = $landingSections->count() > 0 ? $landingSections : $defaultSections;
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
                    <div class="absolute inset-0 bg-gradient-to-r from-brand-dark/85 via-brand-dark/35 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-[#f8f3ee] to-transparent"></div>

                    <div class="relative mx-auto flex min-h-[calc(100vh-7.25rem)] max-w-7xl items-center px-4 py-16 sm:px-6 md:min-h-[calc(100vh-5rem)] lg:px-8">
                        <div class="max-w-xl text-white">
                            @if($banner->eyebrow)
                                <p class="mb-5 inline-flex border border-white/40 px-4 py-2 text-[11px] font-bold uppercase tracking-[0.28em]">
                                    {{ $banner->eyebrow }}
                                </p>
                            @endif
                            <h1 class="text-5xl font-semibold leading-[0.98] tracking-normal md:text-7xl">
                                {{ $banner->title }}
                            </h1>
                            @if($banner->subtitle)
                                <p class="mt-6 max-w-md text-sm leading-7 text-white/85 md:text-base">
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
                    <span class="mx-8">Mushroom Series</span>
                    <span class="mx-8">Free Gift Selected Item</span>
                    <span class="mx-8">Premium Daily Hijab</span>
                @endfor
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-3 md:grid-cols-3">
                @foreach($promoSections as $section)
                    <a href="{{ $section->button_url ?: route('collections.index') }}"
                        class="group relative min-h-48 overflow-hidden p-6"
                        style="background: {{ $section->background_color }}; color: {{ $section->text_color }};">
                        @if($section->image)
                            <img src="{{ asset('storage/' . $section->image) }}" alt="{{ $section->title }}"
                                class="absolute inset-0 h-full w-full object-cover opacity-45 transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/10"></div>
                        @endif
                        <div class="relative z-10 max-w-[14rem]">
                            @if($section->eyebrow)
                                <p class="text-[10px] font-bold uppercase tracking-[0.24em] opacity-75">{{ $section->eyebrow }}</p>
                            @endif
                            <h2 class="mt-3 text-3xl font-semibold leading-tight">{{ $section->title }}</h2>
                            @if($section->subtitle)
                                <p class="mt-3 text-sm leading-6 opacity-75">{{ $section->subtitle }}</p>
                            @endif
                            @if($section->button_text)
                                <span class="mt-5 inline-flex border border-current px-4 py-2 text-xs font-bold uppercase tracking-[0.16em]">
                                    {{ $section->button_text }}
                                </span>
                            @endif
                        </div>
                        @if(!$section->image)
                            <i class="{{ $section->icon ?: 'fa-solid fa-bag-shopping' }} absolute bottom-5 right-5 text-7xl opacity-25 transition group-hover:scale-110"></i>
                        @endif
                    </a>
                @endforeach
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 pb-14 sm:px-6 lg:px-8">
            <div class="mb-7 flex items-end justify-between gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Shop by Category</p>
                    <h2 class="mt-2 text-2xl font-semibold md:text-3xl">Pilih Kategori</h2>
                </div>
                <a href="{{ route('collections.index') }}" class="hidden text-sm font-bold text-brand-primary hover:text-brand-dark md:inline-flex">
                    Lihat Semua
                </a>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($categoryBlocks as $category)
                    <a href="{{ route('collections.index', ['category' => $category->slug]) }}"
                        class="group border border-brand-secondary/60 bg-white/70 p-4 transition hover:border-brand-primary hover:bg-white">
                        <div class="aspect-square overflow-hidden bg-[#eee5dc]">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://cdn-icons-png.flaticon.com/512/3144/3144453.png' }}"
                                class="h-full w-full object-contain p-7 transition duration-500 group-hover:scale-110"
                                alt="{{ $category->name }}">
                        </div>
                        <div class="mt-4">
                            <h3 class="text-sm font-bold leading-tight">{{ $category->name }}</h3>
                            <p class="mt-1 text-xs text-brand-dark/55">{{ $category->products_count }} produk</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @if($flashSaleProducts->count() > 0)
            <section class="bg-brand-dark px-4 py-12 text-white sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-secondary">Limited Offer</p>
                            <h2 class="mt-2 text-3xl font-semibold">Flash Sale</h2>
                        </div>
                        <a href="{{ route('promo.index') }}" class="text-sm font-bold text-brand-secondary hover:text-white">
                            Lihat Promo
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        @foreach($flashSaleProducts as $product)
                            @include('user.components.product-card', ['product' => $product, 'isFlashSale' => true])
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section id="new-arrival" class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">New Arrival</p>
                    <h2 class="mt-2 text-3xl font-semibold">Koleksi Terbaru</h2>
                </div>
                <a href="{{ route('collections.index') }}"
                    class="inline-flex w-fit items-center gap-2 border border-brand-primary px-5 py-2 text-sm font-bold text-brand-primary transition hover:bg-brand-primary hover:text-white">
                    View All
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 gap-x-3 gap-y-8 md:grid-cols-3 lg:grid-cols-4">
                @foreach($latestProducts as $product)
                    @include('user.components.product-card', ['product' => $product, 'isFlashSale' => false])
                @endforeach
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
