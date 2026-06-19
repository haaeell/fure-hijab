@extends('layouts.customer')

@section('title', 'Koleksi Hijab Premium')

@php
    $fallbackHero = 'https://images.unsplash.com/photo-1585435465945-bef5a93f8849?auto=format&fit=crop&q=85&w=1800';
    $fallbackEditorial = 'https://images.unsplash.com/photo-1618232118117-98d49b20e2f5?auto=format&fit=crop&q=85&w=1400';
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
    $chatPhoneRaw = \App\Models\Setting::getValue('store_whatsapp', \App\Models\Setting::getValue('biteship_origin_contact_phone', config('services.biteship.origin_contact_phone', '081297536686')));
    $chatPhone = preg_replace('/\D+/', '', (string) $chatPhoneRaw);
    $chatPhone = str_starts_with($chatPhone, '0') ? '62' . substr($chatPhone, 1) : $chatPhone;
    $chatMessage = rawurlencode('Halo FURE, saya mau tanya koleksi dan pesanan.');
    $chatUrl = $chatPhone ? "https://api.whatsapp.com/send?phone={$chatPhone}&text={$chatMessage}" : '#';
    $featureBanners = collect([
        (object) [
            'eyebrow' => 'FURE Signature',
            'title' => 'Clean Layers for Every Day',
            'subtitle' => 'Hijab premium dengan warna lembut, mudah dipadukan, dan nyaman dipakai sepanjang hari.',
            'button_text' => 'Explore Collection',
            'button_url' => route('collections.index'),
            'image' => $fallbackEditorial,
            'align' => 'left',
        ],
        (object) [
            'eyebrow' => 'Daily Essential',
            'title' => 'Soft Colors, Effortless Fit',
            'subtitle' => 'Temukan pilihan bahan ringan untuk aktivitas harian sampai momen spesial.',
            'button_text' => 'Shop New Arrival',
            'button_url' => route('new-arrived.index'),
            'image' => $fallbackHero,
            'align' => 'right',
        ],
        (object) [
            'eyebrow' => 'Limited Deals',
            'title' => 'Special Picks This Week',
            'subtitle' => 'Koleksi favorit dengan penawaran khusus untuk tampilan modest yang rapi.',
            'button_text' => 'Claim Promo',
            'button_url' => route('promo.index'),
            'image' => 'https://images.unsplash.com/photo-1594744803329-e58b31de8bf5?auto=format&fit=crop&q=85&w=1600',
            'align' => 'left',
        ],
        (object) [
            'eyebrow' => 'Style Edit',
            'title' => 'One Look, Many Moments',
            'subtitle' => 'Padukan hijab, atasan, dan warna netral untuk tampilan santun yang tetap modern.',
            'button_text' => 'Shop The Look',
            'button_url' => route('collections.index'),
            'image' => $shopLookImage,
            'align' => 'right',
        ],
    ]);
    $homeSeoImage = $heroBanners->first()?->image
        ? asset('storage/' . $heroBanners->first()->image)
        : $fallbackHero;
@endphp

@section('seo_title', 'FURE Hijab Premium dan Modest Wear')
@section('seo_description', 'Temukan koleksi hijab premium FURE, modest wear elegan, best seller, promo, dan new arrival dengan bahan nyaman serta warna lembut untuk daily look.')
@section('seo_keywords', 'FURE, hijab premium, hijab wanita, hijab terbaru, modest wear, best seller hijab, hijab syari')
@section('seo_image', $homeSeoImage)
@section('canonical', url('/'))

@push('seo')
    @php
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => \App\Models\Setting::getValue('store_name', 'FURE'),
            'url' => url('/'),
            'logo' => \App\Models\Setting::getValue('store_logo') ? asset('storage/' . \App\Models\Setting::getValue('store_logo')) : asset('favicon.ico'),
            'sameAs' => array_values(array_filter([
                \App\Models\Setting::getValue('store_instagram'),
                \App\Models\Setting::getValue('store_tiktok'),
            ])),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => \App\Models\Setting::getValue('store_phone') ?: \App\Models\Setting::getValue('store_whatsapp'),
                'contactType' => 'customer service',
                'areaServed' => 'ID',
                'availableLanguage' => ['Indonesian'],
            ],
        ];
        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => \App\Models\Setting::getValue('store_name', 'FURE'),
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('collections.index') . '?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <div class="bg-[#f8f3ee] text-brand-dark">
        <div class="fixed bottom-5 right-5 z-[70] flex flex-col items-end gap-3 md:bottom-7 md:right-7">
            <div id="landingChatPopup"
                class="hidden w-[min(calc(100vw-2.5rem),21rem)] overflow-hidden border border-brand-secondary/60 bg-white shadow-[0_22px_60px_rgba(95,74,58,0.22)]">
                <div class="flex items-center justify-between bg-[#8A7664] px-4 py-3 text-white">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15">
                            <i class="fa-solid fa-headset text-sm"></i>
                        </span>
                        <div>
                            <p class="text-sm font-bold leading-tight">FURE Assistant</p>
                            <p class="text-[11px] text-white/75">Online via WhatsApp</p>
                        </div>
                    </div>
                    <button type="button" id="landingChatClose"
                        class="inline-flex h-8 w-8 items-center justify-center text-white/80 transition hover:bg-white/10 hover:text-white"
                        aria-label="Tutup chat">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="space-y-4 bg-[#f8f3ee] p-4">
                    <div class="max-w-[15rem] bg-white px-4 py-3 text-sm leading-6 text-brand-dark shadow-sm">
                        Halo, ada yang bisa FURE bantu? Klik tombol di bawah untuk lanjut chat lewat WhatsApp.
                    </div>
                    <a href="{{ $chatUrl }}" target="_blank" rel="noopener"
                        class="flex w-full items-center justify-center gap-2 bg-[#8A7664] px-5 py-3 text-sm font-bold text-white transition hover:bg-brand-dark">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                        Lanjut ke WhatsApp
                    </a>
                </div>
            </div>

            <button type="button" id="landingChatButton"
                class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#8A7664] text-white shadow-[0_18px_40px_rgba(95,74,58,0.30)] transition hover:-translate-y-1 hover:bg-brand-dark md:h-16 md:w-16"
                aria-label="Buka chat FURE" aria-expanded="false" aria-controls="landingChatPopup">
                <i class="fa-brands fa-whatsapp text-3xl md:text-4xl"></i>
            </button>
        </div>

        <section id="landingHero" class="relative h-[58vh] min-h-[360px] max-h-[640px] overflow-hidden bg-brand-dark sm:h-[64vh] md:h-auto md:min-h-[calc(100vh-5rem)] md:max-h-none">
            @foreach($heroBanners as $index => $banner)
                @php
                    $bannerImage = $banner->image ? asset('storage/' . $banner->image) : $fallbackHero;
                    $bannerMobileImage = $banner->mobile_image ? asset('storage/' . $banner->mobile_image) : $bannerImage;
                    $hasHeroText = filled($banner->eyebrow)
                        || filled($banner->title)
                        || filled($banner->subtitle)
                        || (filled($banner->primary_button_text) && filled($banner->primary_button_url))
                        || (filled($banner->secondary_button_text) && filled($banner->secondary_button_url));
                @endphp
                <div class="landing-slide absolute inset-0 transition-opacity duration-700 {{ $index === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}"
                    data-slide="{{ $index }}">
                    <picture>
                        <source media="(max-width: 767px)" srcset="{{ $bannerMobileImage }}">
                        <img src="{{ $bannerImage }}" alt="{{ $banner->title ?: 'Banner FURE' }}"
                            class="absolute inset-0 h-full w-full object-cover object-center {{ $hasHeroText ? 'opacity-85 md:opacity-80' : 'opacity-100' }}">
                    </picture>
                    @if($hasHeroText)
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/88 via-brand-dark/25 to-transparent md:bg-gradient-to-r md:from-brand-dark/90 md:via-brand-dark/38 md:to-transparent"></div>
                    @endif
                    @if($hasHeroText)
                        <div class="relative mx-auto flex h-full max-w-7xl items-end px-4 pb-10 pt-16 sm:px-6 md:min-h-[calc(100vh-5rem)] md:items-center md:py-16 lg:px-8">
                            <div class="max-w-xl text-white md:max-w-2xl">
                                @if($banner->eyebrow)
                                    <p class="mb-4 inline-flex border border-white/45 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.22em] md:mb-5 md:px-4 md:py-2 md:text-[11px] md:tracking-[0.28em]">
                                        {{ $banner->eyebrow }}
                                    </p>
                                @endif
                                @if($banner->title)
                                    <h1 class="text-3xl font-semibold leading-tight tracking-normal sm:text-4xl md:text-6xl md:leading-[0.96] lg:text-7xl">
                                        {{ $banner->title }}
                                    </h1>
                                @endif
                                @if($banner->subtitle)
                                    <p class="mt-4 max-w-md text-xs leading-6 text-white/85 sm:text-sm md:mt-6 md:max-w-lg md:text-base md:leading-7">
                                        {{ $banner->subtitle }}
                                    </p>
                                @endif
                                <div class="mt-6 flex flex-wrap gap-2 md:mt-8 md:gap-3">
                                    @if($banner->primary_button_text && $banner->primary_button_url)
                                        <a href="{{ $banner->primary_button_url }}"
                                            class="inline-flex items-center gap-2 bg-white px-4 py-2.5 text-xs font-bold text-brand-dark transition hover:bg-brand-secondary md:gap-3 md:px-7 md:py-3 md:text-sm">
                                            {{ $banner->primary_button_text }}
                                            <i class="fa-solid fa-arrow-right text-xs"></i>
                                        </a>
                                    @endif
                                    @if($banner->secondary_button_text && $banner->secondary_button_url)
                                        <a href="{{ $banner->secondary_button_url }}"
                                            class="inline-flex items-center gap-2 border border-white/60 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-white hover:text-brand-dark md:gap-3 md:px-7 md:py-3 md:text-sm">
                                            {{ $banner->secondary_button_text }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            @if($heroBanners->count() > 1)
                <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2 md:bottom-8">
                    @foreach($heroBanners as $index => $banner)
                        <button type="button" aria-label="Banner {{ $index + 1 }}"
                            class="landing-dot h-2.5 w-2.5 border border-white transition {{ $index === 0 ? 'bg-white' : 'bg-white/20' }}"
                            data-target="{{ $index }}"></button>
                    @endforeach
                </div>
            @endif
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

        @include('user.components.landing-feature-banner', ['banner' => $featureBanners->get(0)])

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

            @include('user.components.landing-feature-banner', ['banner' => $featureBanners->get(2)])
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

            @include('user.components.landing-feature-banner', ['banner' => $featureBanners->get(($loop->index + 3) % $featureBanners->count())])
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

        @include('user.components.landing-feature-banner', ['banner' => $featureBanners->get(3)])

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
            const chatButton = document.getElementById('landingChatButton');
            const chatPopup = document.getElementById('landingChatPopup');
            const chatClose = document.getElementById('landingChatClose');

            if (chatButton && chatPopup) {
                const setChatOpen = function (isOpen) {
                    chatPopup.classList.toggle('hidden', !isOpen);
                    chatButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                };

                chatButton.addEventListener('click', function () {
                    setChatOpen(chatPopup.classList.contains('hidden'));
                });

                chatClose?.addEventListener('click', function () {
                    setChatOpen(false);
                });
            }

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
