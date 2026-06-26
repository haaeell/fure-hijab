<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, interactive-widget=overlays-content">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- LCP image preload — harus di atas CSS agar browser fetch gambar lebih awal --}}
    @stack('preload')
    <meta name="google-site-verification" content="LMJfBdLrqMUltckNxt1F3PNmrXMSd-UbjHqKq6zhOWE" />
    @php
        $defaultSeoTitle = $seoDefaults['title'];
        $defaultSeoDescription = $seoDefaults['description'];
        $defaultSeoKeywords = $seoDefaults['keywords'];
        $seoTitle = trim($__env->yieldContent('seo_title') ?: $__env->yieldContent('title', $defaultSeoTitle));
        $seoTitleFull = str_contains($seoTitle, $storeName) ? $seoTitle : $seoTitle . ' | ' . $storeName;
        $seoDescription = trim($__env->yieldContent('seo_description') ?: $defaultSeoDescription);
        $seoKeywords = trim($__env->yieldContent('seo_keywords') ?: $defaultSeoKeywords);
        $seoImage = trim($__env->yieldContent('seo_image') ?: $seoDefaults['image']);
        $canonicalUrl = trim($__env->yieldContent('canonical') ?: url()->current());
        $robotsContent = trim($__env->yieldContent('robots') ?: (request()->hasAny(['search', 'category', 'availability', 'min_price', 'max_price', 'sort']) ? 'noindex,follow' : 'index,follow'));
    @endphp
    <title>{{ $seoTitleFull }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seoDescription), 160, '') }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="robots" content="{{ $robotsContent }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if($storeLogo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $storeLogo) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $storeLogo) }}">
    @endif
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $storeName }} Hijab">
    <meta property="og:title" content="{{ $seoTitleFull }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seoDescription), 200, '') }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitleFull }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($seoDescription), 200, '') }}">
    <meta name="twitter:image" content="{{ $seoImage }}">

    {{-- Preconnect ke CDN penting --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    {{-- Tailwind CSS via Vite build (menggantikan CDN yang render-blocking) --}}
    @vite('resources/css/app.css')

    {{-- Google Fonts async – tidak memblokir render --}}
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"></noscript>

    {{-- Font Awesome: hanya load 3 famili yang dipakai (solid/regular/brands)
         all.min.css juga include Light/Thin/Duotone/Sharp yang tidak dipakai --}}
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/fontawesome.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/solid.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/regular.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/brands.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/fontawesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/solid.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/regular.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/brands.min.css">
    </noscript>
    <link rel="preload" as="font" type="font/woff2" crossorigin href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-solid-900.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-regular-400.woff2">
    <link rel="preload" as="font" type="font/woff2" crossorigin href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-brands-400.woff2">

    {{-- Select2 CSS async (dibutuhkan untuk styling address inputs) --}}
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /></noscript>
    {{-- Leaflet CSS + JS diload lazy saat address modal dibuka (via loadLeaflet()) --}}

    <style>
        /* font-display:swap override untuk Font Awesome (CDN tidak include ini) */
        @font-face { font-family:"Font Awesome 6 Free"; font-style:normal; font-weight:900; font-display:swap;
            src:url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-solid-900.woff2") format("woff2"); }
        @font-face { font-family:"Font Awesome 6 Free"; font-style:normal; font-weight:400; font-display:swap;
            src:url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-regular-400.woff2") format("woff2"); }
        @font-face { font-family:"Font Awesome 6 Brands"; font-style:normal; font-weight:400; font-display:swap;
            src:url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/webfonts/fa-brands-400.woff2") format("woff2"); }

        @keyframes fure-shimmer {
            to { transform: translateX(200%); }
        }
        .shimmer-loading {
            position: relative;
            overflow: hidden;
            background: #f0ece8;
        }
        .shimmer-loading::after {
            content: '';
            position: absolute;
            inset: 0;
            width: 50%;
            background: linear-gradient(90deg, transparent, rgba(232,227,222,0.8), transparent);
            transform: translateX(-100%);
            animation: fure-shimmer 1.4s ease-in-out infinite;
        }

        /* Cegah iOS Safari auto-zoom saat fokus ke input (font-size < 16px memicu zoom) */
        @media screen and (max-width: 1023px) {
            input:not([type="checkbox"]):not([type="radio"]):not([type="range"]),
            textarea,
            select {
                font-size: 16px;
            }
        }

        /* ─── Product card hover ────────────────────────────────────────────── */
        .product-card .product-image {
            transition: transform 0.65s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .product-card:hover .product-image {
            transform: scale(1.06);
        }
        .product-card .card-overlay {
            opacity: 0;
            transition: opacity 0.38s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .product-card:hover .card-overlay { opacity: 1; }
        .product-card .card-zoom-icon {
            transform: scale(0.78);
            transition: transform 0.38s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .product-card:hover .card-zoom-icon { transform: scale(1); }
        .product-card .card-bag-icon {
            transition: background 0.32s ease, color 0.32s ease, transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .product-card:hover .card-bag-icon {
            background: var(--color-brand-primary, #A78B6F);
            color: #fff;
            transform: scale(1.1);
        }

        /* ─── Scroll reveal ─────────────────────────────────────────────────── */
        .reveal {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.72s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.72s cubic-bezier(0.22, 1, 0.36, 1);
            will-change: opacity, transform;
        }
        .reveal.from-left  { transform: translateX(-28px); }
        .reveal.from-right { transform: translateX(28px); }
        .reveal.from-scale { transform: scale(0.93) translateY(16px); }
        .reveal.revealed {
            opacity: 1;
            transform: none;
        }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden; /* iOS Safari: body overflow-x:hidden saja tidak cukup karena html adalah scroll container asli */
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .product-card:hover .product-image {
            transform: scale(1.08);
        }

        @keyframes shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .animate-shimmer {
            animation: shimmer 2s infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            animation: marquee 34s linear infinite;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 0.75rem;
            border-color: #f3f4f6;
            height: 46px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
        }

        #datatable_wrapper {
            font-family: 'Poppins', sans-serif;
            margin-top: 1rem;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        /* Search Input - Simple */
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e5e7eb !important;
            border-radius: 1rem !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            height: 44px !important;
            background: #f9fafb !important;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #A78B6F !important;
            box-shadow: 0 0 0 3px rgba(167, 139, 111, 0.1) !important;
            outline: none !important;
        }

        /* Length Menu */
        .dataTables_wrapper .dataTables_length select {
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            height: 44px;
        }

        /* Pagination - Simple */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem !important;
            margin: 0 0.125rem !important;
            border-radius: 0.75rem !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            border: 1px solid #e5e7eb !important;
            background: white !important;
            color: #6b7280 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background: #A78B6F !important;
            border-color: #A78B6F !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #A78B6F !important;
            border-color: #A78B6F !important;
            color: white !important;
        }

        /* Table Header */
        #datatable thead th {
            background: #f8fafc !important;
            color: #6b7280 !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            padding: 1.25rem 1rem !important;
            border: none !important;
        }

        /* Sorting Icons */
        table.dataTable thead .sorting::after,
        table.dataTable thead .sorting_asc::after,
        table.dataTable thead .sorting_desc::after {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900;
            opacity: 0.4;
            margin-left: 0.5rem;
        }

        table.dataTable thead .sorting::after {
            content: "\f0dc";
        }

        table.dataTable thead .sorting_asc::after {
            content: "\f0de";
            color: #A78B6F;
        }

        table.dataTable thead .sorting_desc::after {
            content: "\f0dd";
            color: #A78B6F;
        }

        /* Rows */
        #datatable tbody tr:hover {
            background: rgba(167, 139, 111, 0.05) !important;
        }

        /* Info */
        .dataTables_wrapper .dataTables_info {
            color: #9ca3af;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Mobile */
        .customer-nav-shell {
            grid-template-columns: 5.75rem minmax(0, 1fr) 5.75rem;
        }

        @media (min-width: 769px) {
            .customer-nav-shell {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-bottom: 1rem;
            }
        }
    </style>
    @stack('seo')
    @stack('styles')
    @yield('styles')
</head>

    <body class="bg-[#f8f3ee] font-sans text-gray-900 antialiased overflow-x-hidden">

    <nav id="mainNav" class="top-0 z-50 border-b border-brand-secondary/40 bg-white" style="position:sticky">
        <div class="bg-brand-dark text-white overflow-hidden">
            <div class="flex w-max animate-marquee whitespace-nowrap px-4 py-1.5 text-[9px] font-bold uppercase tracking-[0.18em] sm:px-6 sm:py-2 sm:text-[10px] sm:tracking-[0.24em] lg:px-8">
                <div class="flex shrink-0 items-center">
                    @for ($i = 0; $i < 5; $i++)
                        <span class="mr-10">Exclusive discount 10% off</span>
                        <span class="mr-10 text-brand-secondary">New hijab collection ready</span>
                        <span class="mr-10">Free gift selected item</span>
                    @endfor
                </div>
                <div class="flex shrink-0 items-center" aria-hidden="true">
                    @for ($i = 0; $i < 5; $i++)
                        <span class="mr-10">Exclusive discount 10% off</span>
                        <span class="mr-10 text-brand-secondary">New hijab collection ready</span>
                        <span class="mr-10">Free gift selected item</span>
                    @endfor
                </div>
            </div>
        </div>
        <div class="customer-nav-shell mx-auto grid h-14 max-w-7xl items-center gap-1 px-3 sm:h-16 sm:px-6 lg:flex lg:justify-between lg:px-8">
            <div class="flex min-w-0 items-center gap-1 lg:hidden">
                <button type="button" id="mobileMenuButton" aria-label="Menu" aria-expanded="false" aria-controls="mobileMenuPanel"
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center text-brand-dark transition hover:bg-[#f8f3ee]">
                    <i id="mobileMenuIcon" class="fa-solid fa-bars text-lg"></i>
                </button>
                <button type="button" data-search-trigger aria-label="Cari produk" class="flex h-10 w-10 flex-shrink-0 items-center justify-center text-brand-dark transition hover:text-brand-primary">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </button>
            </div>

            <a href="/" class="group flex min-w-0 items-center justify-center lg:justify-start">
                @if($storeLogo)
                    <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" class="h-10 w-auto max-w-[160px] object-contain">
                @else
                    <span class="truncate text-center text-brand-dark font-black text-lg tracking-[0.18em] uppercase sm:text-xl sm:tracking-[0.22em]">{{ $storeName }}</span>
                @endif
            </a>

            <div class="hidden lg:flex items-center gap-7 text-[11px] font-bold uppercase tracking-[0.18em] text-brand-dark/80">
                <a href="/"
                    class="transition-colors relative {{ request()->is('/') ? 'text-brand-dark after:scale-x-100' : 'hover:text-brand-primary after:scale-x-0' }} after:content-[''] after:absolute after:bottom-[-6px] after:left-0 after:w-full after:h-px after:bg-brand-primary hover:after:scale-x-100 after:transition-transform">
                    Home
                </a>

                <a href="{{ route('best-seller.index') }}"
                    class="transition-colors {{ request()->routeIs('best-seller.*') ? 'text-brand-dark' : 'hover:text-brand-primary' }}">
                    Best Seller
                </a>

                <a href="{{ route('hijab.index') }}"
                    class="transition-colors {{ request()->routeIs('hijab.*') || request()->routeIs('collections.*') ? 'text-brand-dark' : 'hover:text-brand-primary' }}">
                    Hijab
                </a>

                <a href="{{ route('syari.index') }}"
                    class="transition-colors {{ request()->routeIs('syari.*') ? 'text-brand-dark' : 'hover:text-brand-primary' }}">
                    Syar'i
                </a>

                <a href="{{ route('new-arrived.index') }}"
                    class="transition-colors {{ request()->routeIs('new-arrived.*') ? 'text-brand-dark' : 'hover:text-brand-primary' }}">
                    New Arrived
                </a>

                <a href="{{ route('about.index') }}"
                    class="transition-colors {{ request()->routeIs('about.*') ? 'text-brand-dark' : 'hover:text-brand-primary' }}">
                    Store Locator
                </a>

                <a href="{{ route('articles.index') }}"
                    class="transition-colors {{ request()->routeIs('articles.*') ? 'text-brand-dark' : 'hover:text-brand-primary' }}">
                    Journal
                </a>
            </div>
            <div class="flex min-w-0 items-center justify-end gap-1 sm:gap-2 lg:gap-3">
                <button type="button" data-search-trigger aria-label="Cari produk" class="hidden p-2 text-brand-dark transition-colors hover:text-brand-primary md:block">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </button>
                @if($isCustomer)
                    <a href="{{ route('wishlist.index') }}" class="relative hidden p-2 text-brand-dark transition-colors hover:text-brand-primary sm:block">
                        <i class="fa-regular fa-heart text-lg"></i>
                        @if($wishlistCount > 0)
                            <span class="absolute top-0 right-0 bg-brand-primary text-white text-[10px] w-4 h-4 flex items-center justify-center border-2 border-white shadow">
                                {{ $wishlistCount > 9 ? '9+' : $wishlistCount }}
                            </span>
                        @endif
                    </a>
                @endif
                <a href="{{ route('cart.index') }}"
                    @if($isCustomer) data-cart-trigger @endif
                    class="relative flex h-10 w-10 flex-shrink-0 items-center justify-center text-brand-dark transition-colors hover:text-brand-primary">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    <span
                        id="navCartCount"
                        class="js-cart-count absolute top-0 right-0 bg-brand-primary text-white text-[10px] w-4 h-4 flex items-center justify-center border-2 border-white shadow">
                        {{ $cartCount > 9 ? '9+' : $cartCount }}
                    </span>
                </a>

                @if($currentUser)
                    <div class="relative ml-0 sm:ml-2" id="userDropdownContainer">
                        <button type="button" id="userDropdownBtn"
                            class="flex h-10 w-10 items-center justify-center gap-0 bg-gray-50 border border-gray-100 rounded-2xl hover:bg-white hover:shadow-md transition-all duration-300 sm:w-auto sm:gap-3 sm:pl-3 sm:pr-1 sm:py-1">
                            <span class="hidden md:block text-sm font-bold text-brand-dark">{{ $currentUser->name }}</span>
                            <div
                                class="w-9 h-9 flex-shrink-0 bg-brand-primary/10 rounded-xl flex items-center justify-center border border-brand-primary/20">
                                <i class="fa-solid fa-user text-brand-primary text-sm"></i>
                            </div>
                            <i class="hidden fa-solid fa-chevron-down text-[10px] text-gray-400 mr-2 transition-transform duration-300 sm:block"
                                id="dropdownArrow"></i>
                        </button>

                        <div id="userDropdownMenu"
                            class="hidden absolute right-0 mt-3 w-56 bg-white/90 backdrop-blur-xl rounded-[24px] shadow-[0_20px_50px_rgba(95,74,58,0.1)] border border-white p-2 z-[60] origin-top-right transition-all">

                            <div class="px-4 py-3 border-b border-gray-50 mb-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role Akun</p>
                                <p class="text-xs font-bold text-brand-primary uppercase">{{ $currentUser->role }}</p>
                            </div>

                            <a href="/user/profile"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-brand-dark hover:bg-soft-mint rounded-2xl transition-colors group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center group-hover:bg-white transition-colors">
                                    <i class="fa-solid fa-gear text-xs text-gray-400 group-hover:text-brand-primary"></i>
                                </div>
                                Profile
                            </a>

                            <a href="/order-history"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-brand-dark hover:bg-soft-mint rounded-2xl transition-colors group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center group-hover:bg-white transition-colors">
                                    <i
                                        class="fa-solid fa-clock-rotate-left text-xs text-gray-400 group-hover:text-brand-primary"></i>
                                </div>
                                Riwayat Pesanan
                            </a>

                            <hr class="my-2 border-gray-50">

                            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-2xl transition-colors group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-red-50/50 flex items-center justify-center group-hover:bg-white transition-colors">
                                    <i class="fa-solid fa-power-off text-xs group-hover:scale-110 transition-transform"></i>
                                </div>
                                Keluar
                            </button>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                @else
                    <a href="/login"
                        class="hidden md:block text-sm font-semibold text-brand-dark hover:text-brand-primary transition-colors">Masuk</a>
                    <a href="/register"
                        class="hidden px-6 py-3 bg-brand-dark text-white text-sm font-bold shadow-lg shadow-brand-dark/20 hover:bg-brand-primary hover:shadow-brand-primary/40 hover:-translate-y-0.5 transition-all active:scale-95 md:inline-flex">Daftar</a>
                    <a href="/login" aria-label="Masuk" class="p-2 text-brand-dark md:hidden">
                        <i class="fa-regular fa-user text-lg"></i>
                    </a>
                @endif
            </div>
        </div>
        <div id="mobileMenuPanel" class="hidden border-t border-brand-secondary/30 bg-white px-4 py-4 shadow-[0_18px_40px_rgba(95,74,58,0.08)] lg:hidden">
            <div class="grid gap-2 text-[12px] font-bold uppercase tracking-[0.16em] text-brand-dark/75">
                <a href="/"
                    class="flex items-center px-3 py-3 transition {{ request()->is('/') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Home
                </a>
                <a href="{{ route('best-seller.index') }}"
                    class="flex items-center px-3 py-3 transition {{ request()->routeIs('best-seller.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Best Seller
                </a>
                <a href="{{ route('hijab.index') }}"
                    class="flex items-center px-3 py-3 transition {{ request()->routeIs('hijab.*') || request()->routeIs('collections.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Hijab
                </a>
                <a href="{{ route('syari.index') }}"
                    class="flex items-center px-3 py-3 transition {{ request()->routeIs('syari.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Syar'i
                </a>
                <a href="{{ route('new-arrived.index') }}"
                    class="flex items-center px-3 py-3 transition {{ request()->routeIs('new-arrived.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    New Arrived
                </a>
                <a href="{{ route('about.index') }}"
                    class="flex items-center px-3 py-3 transition {{ request()->routeIs('about.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Store Locator
                </a>
                <a href="{{ route('articles.index') }}"
                    class="flex items-center px-3 py-3 transition {{ request()->routeIs('articles.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Journal
                </a>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-brand-dark text-white pt-24 pb-12 mt-20 rounded-t-[60px] relative overflow-hidden">
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-px bg-gradient-to-r from-transparent via-brand-secondary/30 to-transparent">
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-20">
                <div class="md:col-span-5 space-y-8">
                    <div class="flex items-center gap-3 group">
                        <div
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-secondary flex items-center justify-center shadow-lg shadow-brand-primary/20">
                            <i class="fa-solid fa-wand-magic-sparkles text-white text-xl"></i>
                        </div>
                        <span
                            class="text-2xl font-black tracking-widest uppercase bg-clip-text text-transparent bg-gradient-to-r from-white to-brand-secondary/80">
                            {{ $storeName }}
                        </span>
                    </div>
                    <p class="text-brand-secondary text-lg leading-relaxed max-w-sm">
                        Elegansi dalam kesantunan. Mewujudkan standar baru hijab premium untuk wanita yang menghargai
                        kualitas dan estetika.
                    </p>
                    @if($storeAddress || $storeEmail || $storePhone)
                        <div class="space-y-2 text-sm text-brand-secondary">
                            @if($storeAddress)<p>{{ $storeAddress }}</p>@endif
                            @if($storeEmail)<p>{{ $storeEmail }}</p>@endif
                            @if($storePhone)<p>{{ $storePhone }}</p>@endif
                        </div>
                    @endif
                    <div class="flex gap-4">
                        <a href="{{ $storeInstagram ?: '#' }}" aria-label="Instagram {{ $storeName }}"
                            class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-primary hover:scale-110 transition-all duration-300 group">
                            <i class="fa-brands fa-instagram text-xl group-hover:text-white" aria-hidden="true"></i>
                        </a>
                        <a href="{{ $storeTiktok ?: '#' }}" aria-label="TikTok {{ $storeName }}"
                            class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-primary hover:scale-110 transition-all duration-300 group">
                            <i class="fa-brands fa-tiktok text-xl group-hover:text-white" aria-hidden="true"></i>
                        </a>
                        <a href="{{ $storeWhatsapp ? 'https://api.whatsapp.com/send?phone=' . preg_replace('/\D+/', '', str_starts_with($storeWhatsapp, '0') ? '62' . substr($storeWhatsapp, 1) : $storeWhatsapp) : '#' }}" aria-label="WhatsApp {{ $storeName }}"
                            class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-primary hover:scale-110 transition-all duration-300 group">
                            <i class="fa-brands fa-whatsapp text-xl group-hover:text-white" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">
                    <h4 class="text-sm font-bold uppercase tracking-widest text-brand-secondary">Koleksi</h4>
                    <ul class="space-y-4 text-brand-secondary">
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Best Seller</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Hijab Instan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Pashmina</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Premium Silk</a></li>
                    </ul>
                </div>

                <div class="md:col-span-5 space-y-6">
                    <h4 class="text-sm font-bold uppercase tracking-widest text-brand-secondary">Dapatkan Update Terbaru
                    </h4>
                    <p class="text-brand-secondary text-sm">Berlangganan newsletter untuk info promo eksklusif.</p>
                    <form
                        class="flex gap-2 p-1.5 bg-white/5 border border-white/10 rounded-2xl focus-within:border-brand-primary/50 transition-all">
                        <input type="email" placeholder="Email Anda"
                            class="bg-transparent border-none focus:ring-0 px-4 py-2 w-full text-sm">
                        <button
                            class="bg-white text-brand-dark px-6 py-2 rounded-xl font-bold text-sm hover:bg-brand-secondary transition-colors">
                            Join
                        </button>
                    </form>
                </div>
            </div>

            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-brand-secondary text-xs tracking-widest uppercase">
                    &copy; 2026 {{ $storeName }}. Crafted with Grace.
                </p>
                <div class="flex gap-8 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-secondary">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- ─── Search Overlay ──────────────────────────────────────────────── --}}
    <div id="searchOverlay" class="fixed inset-0 z-[300] flex flex-col" style="display:none;">
        <div id="searchPanel" class="bg-white shadow-2xl border-b border-brand-secondary/20"
             style="transform:translateY(-100%); transition:transform 0.35s cubic-bezier(0.16,1,0.3,1);">
            <div class="mx-auto max-w-3xl px-4 py-5 sm:px-6">
                <div class="flex items-center gap-4 border-b border-brand-secondary/20 pb-4">
                    <i class="fa-solid fa-magnifying-glass flex-shrink-0 text-lg text-brand-primary"></i>
                    <input type="text" id="searchInput" placeholder="Cari produk hijab…"
                        autocomplete="off"
                        class="flex-1 bg-transparent text-lg font-medium text-brand-dark outline-none placeholder-brand-dark/30">
                    <button id="searchClose" class="flex-shrink-0 p-1 text-xl text-brand-dark/40 transition hover:text-brand-dark">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="py-5" id="searchContent">
                    {{-- Recent --}}
                    <div id="recentSection" class="mb-5 hidden">
                        <div class="mb-3 flex items-center justify-between">
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/40">Pencarian Terakhir</p>
                            <button id="clearHistory" class="text-[10px] font-bold uppercase tracking-[0.16em] text-brand-primary transition hover:text-brand-dark">Hapus Semua</button>
                        </div>
                        <div id="recentList" class="flex flex-wrap gap-2"></div>
                    </div>

                    {{-- Popular --}}
                    <div id="popularSection">
                        <p class="mb-3 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/40">Produk Populer</p>
                        <div id="popularList" class="flex flex-wrap gap-2">
                            <span class="animate-pulse h-7 w-24 bg-brand-secondary/20 rounded"></span>
                            <span class="animate-pulse h-7 w-32 bg-brand-secondary/20 rounded"></span>
                            <span class="animate-pulse h-7 w-20 bg-brand-secondary/20 rounded"></span>
                        </div>
                    </div>

                    {{-- Loading --}}
                    <div id="searchLoading" class="hidden py-6 text-center">
                        <i class="fa-solid fa-circle-notch fa-spin text-2xl text-brand-primary"></i>
                    </div>

                    {{-- Results --}}
                    <div id="searchResults" class="hidden"></div>

                    {{-- No results --}}
                    <div id="searchNoResults" class="hidden py-6 text-center">
                        <i class="fa-regular fa-face-sad-tear mb-3 text-3xl text-brand-secondary"></i>
                        <p class="text-sm font-semibold text-brand-dark/60">Tidak ada produk untuk "<span id="noResultQuery" class="text-brand-dark"></span>"</p>
                        <p id="didYouMeanWrap" class="mt-2 hidden text-sm text-brand-dark/45">
                            Maksud Anda:
                            <button id="didYouMean" class="font-bold text-brand-primary transition hover:underline"></button>?
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div id="searchBackdrop" class="flex-1 cursor-pointer bg-black/50"
             style="opacity:0; transition:opacity 0.3s ease; backdrop-filter:blur(2px);"></div>
    </div>
    {{-- ─────────────────────────────────────────────────────────────────── --}}

    @if($isCustomer)
        <div id="cartDrawer" class="fixed inset-0 z-[240] hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-black/45 backdrop-blur-[2px]" data-cart-drawer-close></div>
            <div id="cartDrawerPanel"
                class="absolute right-0 top-0 flex h-full w-full max-w-md translate-x-full flex-col overflow-hidden bg-white shadow-2xl transition-transform duration-300">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-brand-primary">Keranjang</p>
                        <h3 class="mt-1 text-xl font-black text-brand-dark">Tas Belanja</h3>
                    </div>
                    <button type="button" data-cart-drawer-close
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gray-50 text-gray-400 transition hover:bg-brand-dark hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Pilih Semua bar --}}
                <div id="cartDrawerSelectBar" class="hidden items-center gap-3 border-b border-gray-100 px-5 py-3">
                    <label class="flex cursor-pointer items-center gap-2.5 select-none">
                        <input type="checkbox" id="drawerSelectAll"
                            class="h-4 w-4 rounded border-gray-300 accent-brand-primary cursor-pointer">
                        <span class="text-xs font-bold text-brand-dark">Pilih Semua</span>
                    </label>
                    <span class="ml-auto text-[10px] font-semibold text-gray-400" id="drawerSelectedCount">0 dipilih</span>
                </div>

                <div id="cartDrawerItems" class="min-h-[220px] flex-1 overflow-y-auto p-5">
                    <div class="py-12 text-center text-sm font-semibold text-gray-400">
                        <i class="fa-solid fa-circle-notch fa-spin mb-3 text-2xl text-brand-primary"></i>
                        <p>Memuat keranjang...</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 bg-white p-5" style="padding-bottom: calc(1.25rem + env(safe-area-inset-bottom));">
                    <div class="mb-4 flex items-end justify-between gap-4">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Subtotal Terpilih</p>
                            <p class="mt-1 text-2xl font-black text-brand-dark" id="cartDrawerSubtotal">Rp0</p>
                        </div>
                        <p class="text-xs font-bold text-gray-400" id="cartDrawerCount">0 item</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" data-cart-drawer-close
                            class="flex items-center justify-center rounded-2xl border border-brand-primary/30 bg-white px-4 py-3 text-xs font-black uppercase tracking-wide text-brand-dark">
                            Lanjut Belanja
                        </button>
                        <button type="button" id="drawerCheckoutBtn"
                            class="flex items-center justify-center rounded-2xl bg-brand-primary px-4 py-3 text-xs font-black uppercase tracking-wide text-white shadow-lg shadow-brand-primary/20 disabled:opacity-50 disabled:pointer-events-none">
                            Checkout
                        </button>
                    </div>
                </div>

                {{-- Hidden form untuk submit selected items ke checkout --}}
                <form id="drawerCheckoutForm" action="{{ route('cart.checkout') }}" method="POST" class="hidden">
                    @csrf
                    {{-- selected_items[] diisi via JS --}}
                </form>
            </div>
        </div>
    @endif

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script>
        function loadSwal() {
            if (typeof Swal !== 'undefined') return Promise.resolve();
            return new Promise(function(resolve) {
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                s.onload = resolve;
                document.head.appendChild(s);
            });
        }
        function loadLeaflet() {
            if (typeof L !== 'undefined') return Promise.resolve();
            return new Promise(function(resolve) {
                if (!document.querySelector('link[href*="leaflet"]')) {
                    var css = document.createElement('link');
                    css.rel = 'stylesheet';
                    css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    document.head.appendChild(css);
                }
                var s = document.createElement('script');
                s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                s.onload = resolve;
                document.head.appendChild(s);
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')

    @if (session('register_success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Akun Berhasil Dibuat!',
                html: 'Selamat datang di {{ $storeName }} 🛍️<br><span style="font-size:0.9em;color:#888">Selamat berbelanja koleksi hijab premium kami.</span>',
                confirmButtonColor: '#A78B6F',
                confirmButtonText: 'Mulai Belanja',
            });
        </script>
    @elseif (session('success') || session('error'))
        <script>
            Swal.fire({
                icon: @json(session('success') ? 'success' : 'error'),
                title: @json(session('success') ? 'Berhasil' : 'Perhatian'),
                text: @json(session('success') ?: session('error')),
                confirmButtonColor: '#A78B6F',
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Lengkapi Data',
                html: @json(implode('<br>', $errors->all())),
                confirmButtonColor: '#A78B6F',
            });
        </script>
    @endif

    <script>
        @if($isCustomer)
            window.FureCartDrawer = (function () {
                const cartSummaryUrl = "{{ route('cart.summary') }}";
                const cartUpdateUrl = "{{ url('/cart/update') }}";

                function formatRupiah(amount) {
                    return 'Rp' + new Intl.NumberFormat('id-ID').format(amount || 0);
                }

                function escapeHtml(value) {
                    return String(value ?? '').replace(/[&<>"']/g, function (char) {
                        return {
                            '&': '&amp;',
                            '<': '&lt;',
                            '>': '&gt;',
                            '"': '&quot;',
                            "'": '&#039;',
                        }[char];
                    });
                }

                function open() {
                    $('#cartDrawer').removeClass('hidden').attr('aria-hidden', 'false');
                    requestAnimationFrame(function () {
                        $('#cartDrawerPanel').removeClass('translate-x-full');
                    });
                    $('body').addClass('overflow-hidden');
                }

                function close() {
                    $('#cartDrawerPanel').addClass('translate-x-full');
                    setTimeout(function () {
                        $('#cartDrawer').addClass('hidden').attr('aria-hidden', 'true');
                    }, 300);
                    $('body').removeClass('overflow-hidden');
                }

                function recalcDrawer() {
                    let total = 0, selected = 0, total_items = 0;
                    $('#cartDrawerItems .drawer-item').each(function () {
                        total_items++;
                        if ($(this).find('.drawer-item-check').is(':checked')) {
                            total += parseInt($(this).data('subtotal')) || 0;
                            selected++;
                        }
                    });
                    $('#cartDrawerSubtotal').text(formatRupiah(total));
                    $('#drawerSelectedCount').text(selected + ' dipilih');
                    $('#drawerCheckoutBtn').prop('disabled', selected === 0);

                    // Sinkron "Pilih Semua"
                    const $all = $('#drawerSelectAll');
                    $all.prop('indeterminate', selected > 0 && selected < total_items);
                    $all.prop('checked', total_items > 0 && selected === total_items);
                }

                function render(data) {
                    const count = data.count || 0;
                    $('#cartDrawerCount').text(`${count} item`);
                    $('.js-cart-count').text(count > 9 ? '9+' : count);

                    if (!data.items?.length) {
                        $('#cartDrawerSelectBar').addClass('hidden').removeClass('flex');
                        $('#drawerCheckoutBtn').prop('disabled', true);
                        $('#cartDrawerItems').html(`
                            <div class="py-12 text-center">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-soft-mint text-brand-primary">
                                    <i class="fa-solid fa-bag-shopping text-2xl"></i>
                                </div>
                                <p class="font-black text-brand-dark">Keranjang masih kosong</p>
                                <p class="mt-1 text-xs text-gray-400">Produk yang ditambahkan akan muncul di sini.</p>
                            </div>
                        `);
                        $('#cartDrawerSubtotal').text(formatRupiah(0));
                        return;
                    }

                    $('#cartDrawerSelectBar').removeClass('hidden').addClass('flex');

                    $('#cartDrawerItems').html(data.items.map(function (item) {
                        return `
                            <div class="drawer-item flex gap-3 border-b border-gray-100 py-4 first:pt-0 last:border-b-0 items-start"
                                 data-id="${item.id}" data-price="${item.price}" data-subtotal="${item.subtotal}">
                                <label class="flex-shrink-0 pt-1 cursor-pointer">
                                    <input type="checkbox" class="drawer-item-check h-4 w-4 rounded border-gray-300 accent-brand-primary cursor-pointer" checked>
                                </label>
                                <div class="h-20 w-14 flex-shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                    <img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}" class="h-full w-full object-cover" loading="lazy">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-brand-primary">${escapeHtml(item.category || 'Produk')}</p>
                                            <h4 class="mt-0.5 line-clamp-2 text-xs font-black text-brand-dark leading-snug">${escapeHtml(item.name)}</h4>
                                            ${item.variant ? `<p class="mt-0.5 text-[10px] text-gray-400">${escapeHtml(item.variant)}</p>` : ''}
                                        </div>
                                        <button type="button" class="drawer-delete flex-shrink-0 p-1 text-gray-300 hover:text-red-500 transition-colors" data-id="${item.id}">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2.5 flex items-center justify-between gap-2">
                                        <div class="flex items-center rounded-xl border border-brand-secondary/50 bg-white">
                                            <button type="button" class="drawer-qty h-7 w-7 text-brand-dark hover:text-brand-primary transition-colors" data-id="${item.id}" data-qty="${item.qty - 1}">
                                                <i class="fa-solid fa-minus text-[9px]"></i>
                                            </button>
                                            <span class="w-8 text-center text-xs font-black text-brand-dark">${item.qty}</span>
                                            <button type="button" class="drawer-qty h-7 w-7 text-brand-dark hover:text-brand-primary transition-colors" data-id="${item.id}" data-qty="${item.qty + 1}">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                            </button>
                                        </div>
                                        <p class="text-xs font-black text-brand-dark">${formatRupiah(item.subtotal)}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join(''));

                    recalcDrawer();
                }

                function load() {
                    $('#cartDrawerItems').html(`
                        <div class="py-12 text-center text-sm font-semibold text-gray-400">
                            <i class="fa-solid fa-circle-notch fa-spin mb-3 text-2xl text-brand-primary"></i>
                            <p>Memuat keranjang...</p>
                        </div>
                    `);

                    return $.get(cartSummaryUrl)
                        .done(render)
                        .fail(function () {
                            $('#cartDrawerItems').html('<div class="rounded-2xl bg-red-50 p-5 text-sm font-bold text-red-600">Gagal memuat keranjang.</div>');
                        });
                }

                function reloadAndOpen() {
                    open();
                    return load();
                }

                $(document).on('click', '[data-cart-trigger]', function (e) {
                    e.preventDefault();
                    reloadAndOpen();
                });

                $(document).on('click', '[data-cart-drawer-close]', close);

                // Update qty di drawer
                $(document).on('click', '.drawer-qty', function () {
                    const btn     = $(this);
                    const id      = btn.data('id');
                    const nextQty = parseInt(btn.data('qty'), 10);

                    btn.prop('disabled', true);
                    $.ajax({
                        url: `${cartUpdateUrl}/${id}`,
                        method: 'PATCH',
                        data: { _token: $('meta[name="csrf-token"]').attr('content'), quantity: nextQty },
                        success: load,
                        error: function (xhr) {
                            btn.prop('disabled', false);
                            loadSwal().then(function() { Swal.fire({ icon: 'warning', title: xhr.responseJSON?.message || 'Stok tidak mencukupi.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 }); });
                        },
                    });
                });

                // Hapus item dari drawer
                $(document).on('click', '.drawer-delete', function () {
                    const id   = $(this).data('id');
                    const $row = $(this).closest('.drawer-item');
                    $row.css({ opacity: 0.4, pointerEvents: 'none', transition: 'opacity 0.15s' });
                    $.ajax({
                        url: `{{ url('/cart/delete') }}/${id}`,
                        method: 'DELETE',
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: load,
                        error: function () {
                            $row.css({ opacity: 1, pointerEvents: '' });
                            loadSwal().then(function() { Swal.fire({ icon: 'error', title: 'Gagal menghapus.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 }); });
                        },
                    });
                });

                // Checkbox per item
                $(document).on('change', '.drawer-item-check', function () {
                    recalcDrawer();
                });

                // Pilih Semua drawer
                $(document).on('change', '#drawerSelectAll', function () {
                    $('#cartDrawerItems .drawer-item-check').prop('checked', $(this).is(':checked'));
                    recalcDrawer();
                });

                // Checkout dengan item terpilih
                $('#drawerCheckoutBtn').on('click', function () {
                    const $form = $('#drawerCheckoutForm');
                    $form.find('input[name="selected_items[]"]').remove();
                    $('#cartDrawerItems .drawer-item').each(function () {
                        if ($(this).find('.drawer-item-check').is(':checked')) {
                            $form.append(`<input type="hidden" name="selected_items[]" value="${$(this).data('id')}">`);
                        }
                    });
                    $form.submit();
                });

                return { open, close, load, reloadAndOpen };
            })();
        @endif

        let map, marker;
        const defaultLat = -7.7956;
        const defaultLng = 110.3695;

        function initMap() {
            if (map) return Promise.resolve();
            return loadLeaflet().then(function() {
                if (map) return;
                map = L.map('map').setView([defaultLat, defaultLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);
                marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    setCoords(pos.lat, pos.lng);
                });
                map.on('click', function (e) {
                    marker.setLatLng(e.latlng);
                    setCoords(e.latlng.lat, e.latlng.lng);
                });
            });
        }

        function setCoords(lat, lng) {
            const latR = parseFloat(lat).toFixed(7);
            const lngR = parseFloat(lng).toFixed(7);
            $('#dest_latitude').val(latR);
            $('#dest_longitude').val(lngR);
            $('#lat_display').val(latR);
            $('#lng_display').val(lngR);
        }

        const _origSwitchTab = window.switchAddressTab;
        window.switchAddressTab = function (tab) {
            _origSwitchTab(tab);
            if (tab === 'new') {
                setTimeout(function () {
                    initMap().then(function() {
                        if (map) map.invalidateSize();
                    });
                }, 100);
            }
        };

        $(document).on('click', '#btn-my-location', function () {
            if (!navigator.geolocation) return;
            const $btn = $(this).prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Mencari...');
            initMap().then(function() {
                navigator.geolocation.getCurrentPosition(function (pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    setCoords(lat, lng);
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-location-crosshairs mr-1"></i> Lokasiku');
                }, function () {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-location-crosshairs mr-1"></i> Lokasiku');
                    alert('Tidak bisa mendapatkan lokasi.');
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            const btn = $('#userDropdownBtn');
            const menu = $('#userDropdownMenu');
            const arrow = $('#dropdownArrow');
            const mobileMenuButton = $('#mobileMenuButton');
            const mobileMenuPanel = $('#mobileMenuPanel');
            const mobileMenuIcon = $('#mobileMenuIcon');

            mobileMenuButton.on('click', function (e) {
                e.stopPropagation();
                const isOpen = mobileMenuPanel.toggleClass('hidden').is(':visible');
                mobileMenuButton.attr('aria-expanded', isOpen ? 'true' : 'false');
                mobileMenuIcon.toggleClass('fa-bars', !isOpen).toggleClass('fa-xmark', isOpen);
            });

            btn.on('click', function (e) {
                e.stopPropagation();
                menu.toggleClass('hidden');
                arrow.toggleClass('rotate-180');
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#mobileMenuPanel, #mobileMenuButton').length) {
                    mobileMenuPanel.addClass('hidden');
                    mobileMenuButton.attr('aria-expanded', 'false');
                    mobileMenuIcon.addClass('fa-bars').removeClass('fa-xmark');
                }

                if (!$(e.target).closest('#userDropdownContainer').length) {
                    menu.addClass('hidden');
                    arrow.removeClass('rotate-180');
                }
            });
        });
    </script>

    <script>
    // ─── Search Overlay ───────────────────────────────────────────────────
    (function () {
        const overlay  = document.getElementById('searchOverlay');
        const panel    = document.getElementById('searchPanel');
        const backdrop = document.getElementById('searchBackdrop');
        const input    = document.getElementById('searchInput');
        const closeBtn = document.getElementById('searchClose');

        function esc(str) {
            return String(str ?? '')
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
        function fmtPrice(n) {
            return new Intl.NumberFormat('id-ID').format(n);
        }

        // ── Open / Close ─────────────────────────────────────────────────
        function openSearch() {
            overlay.style.display = 'flex';
            requestAnimationFrame(() => {
                panel.style.transform = 'translateY(0)';
                backdrop.style.opacity = '1';
            });
            document.body.style.overflow = 'hidden';
            setTimeout(() => input.focus(), 320);
            renderRecentSearches();
            loadPopular();
        }

        function closeSearch() {
            panel.style.transform = 'translateY(-100%)';
            backdrop.style.opacity = '0';
            setTimeout(() => {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }, 350);
        }

        document.querySelectorAll('[data-search-trigger]').forEach(el =>
            el.addEventListener('click', openSearch)
        );
        backdrop?.addEventListener('click', closeSearch);
        closeBtn?.addEventListener('click', closeSearch);
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSearch(); });

        // ── Sections ────────────────────────────────────────────────────
        function showIdle() {
            document.getElementById('searchLoading').classList.add('hidden');
            document.getElementById('searchResults').classList.add('hidden');
            document.getElementById('searchNoResults').classList.add('hidden');
            document.getElementById('popularSection').classList.remove('hidden');
            renderRecentSearches();
        }
        function showLoading() {
            document.getElementById('recentSection').classList.add('hidden');
            document.getElementById('popularSection').classList.add('hidden');
            document.getElementById('searchResults').classList.add('hidden');
            document.getElementById('searchNoResults').classList.add('hidden');
            document.getElementById('searchLoading').classList.remove('hidden');
        }

        // ── Popular ──────────────────────────────────────────────────────
        async function loadPopular() {
            try {
                const res  = await fetch('/search/popular');
                const data = await res.json();
                const list = document.getElementById('popularList');
                const items = data.products || [];
                list.innerHTML = items.map(p => `
                    <a href="/collections/${encodeURIComponent(p.slug)}"
                        class="flex items-center gap-2 border border-brand-secondary/60 bg-white px-3 py-1.5 text-xs font-semibold text-brand-dark transition hover:border-brand-primary hover:text-brand-primary">
                        <i class="fa-solid fa-fire text-[10px] text-brand-primary/60"></i>${esc(p.name)}
                    </a>`).join('');
            } catch (_) {
                document.getElementById('popularSection').classList.add('hidden');
            }
        }

        // ── Recent Searches (localStorage) ──────────────────────────────
        function getHistory() {
            try { return JSON.parse(localStorage.getItem('fure_search') || '[]'); }
            catch (_) { return []; }
        }
        function saveSearch(q) {
            if (!q.trim()) return;
            let h = getHistory();
            h = [q, ...h.filter(x => x !== q)].slice(0, 5);
            localStorage.setItem('fure_search', JSON.stringify(h));
        }
        function renderRecentSearches() {
            const h       = getHistory();
            const section = document.getElementById('recentSection');
            const list    = document.getElementById('recentList');
            if (h.length === 0) { section.classList.add('hidden'); return; }
            section.classList.remove('hidden');
            list.innerHTML = h.map(t => `
                <button onclick="document.getElementById('searchInput').value=${JSON.stringify(t)};document.getElementById('searchInput').dispatchEvent(new Event('input'))"
                    class="flex items-center gap-2 border border-brand-secondary/50 bg-[#f8f3ee] px-3 py-1.5 text-xs font-semibold text-brand-dark transition hover:border-brand-primary hover:text-brand-primary">
                    <i class="fa-solid fa-clock-rotate-left text-[10px] text-brand-dark/30"></i>${esc(t)}
                </button>`).join('');
        }
        document.getElementById('clearHistory')?.addEventListener('click', () => {
            localStorage.removeItem('fure_search');
            document.getElementById('recentSection').classList.add('hidden');
        });

        // ── Live Search ──────────────────────────────────────────────────
        let debounceTimer;
        input?.addEventListener('input', function () {
            const q = this.value.trim();
            clearTimeout(debounceTimer);
            if (q.length < 2) { showIdle(); return; }
            showLoading();
            debounceTimer = setTimeout(() => fetchSuggestions(q), 350);
        });

        async function fetchSuggestions(q) {
            try {
                const res  = await fetch('/search/suggestions?q=' + encodeURIComponent(q));
                const data = await res.json();
                document.getElementById('searchLoading').classList.add('hidden');
                if (data.products.length > 0) {
                    renderResults(data.products, q);
                } else {
                    renderNoResults(q, data.did_you_mean);
                }
            } catch (_) {
                document.getElementById('searchLoading').classList.add('hidden');
            }
        }

        function renderResults(products, q) {
            document.getElementById('searchNoResults').classList.add('hidden');
            const el = document.getElementById('searchResults');
            el.classList.remove('hidden');
            el.innerHTML = `
                <p class="mb-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-dark/40">Hasil Pencarian</p>
                <div class="grid gap-1 sm:grid-cols-2">
                    ${products.map(p => `
                        <a href="/collections/${esc(p.slug)}" onclick="saveSearchAndGo(${JSON.stringify(q)})"
                            class="flex items-center gap-4 p-3 transition hover:bg-[#f8f3ee] group">
                            <div class="h-14 w-10 flex-shrink-0 overflow-hidden bg-[#eee5dc]">
                                ${p.image
                                    ? `<img src="/storage/${esc(p.image)}" class="h-full w-full object-cover" alt="${esc(p.name)}">`
                                    : `<div class="flex h-full w-full items-center justify-center"><i class="fa-solid fa-image text-brand-secondary"></i></div>`}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[9px] font-bold uppercase tracking-[0.14em] text-brand-primary">${esc(p.category ?? '')}</p>
                                <p class="truncate text-sm font-semibold text-brand-dark transition group-hover:text-brand-primary">${esc(p.name)}</p>
                                <p class="text-sm font-bold text-brand-dark">Rp${fmtPrice(p.price)}</p>
                            </div>
                        </a>`).join('')}
                </div>
                <a href="/collections?search=${encodeURIComponent(q)}" onclick="saveSearchAndGo(${JSON.stringify(q)})"
                    class="mt-4 flex w-full items-center justify-center gap-2 border border-brand-secondary/60 py-3 text-xs font-bold uppercase tracking-[0.16em] text-brand-dark transition hover:border-brand-primary hover:bg-[#f8f3ee]">
                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    Lihat semua hasil untuk "${esc(q)}"
                </a>`;
        }

        function renderNoResults(q, didYouMean) {
            document.getElementById('searchResults').classList.add('hidden');
            const el = document.getElementById('searchNoResults');
            el.classList.remove('hidden');
            document.getElementById('noResultQuery').textContent = q;
            const dym = document.getElementById('didYouMeanWrap');
            if (didYouMean) {
                dym.classList.remove('hidden');
                const btn = document.getElementById('didYouMean');
                btn.textContent = didYouMean;
                btn.onclick = () => {
                    input.value = didYouMean;
                    input.dispatchEvent(new Event('input'));
                };
            } else {
                dym.classList.add('hidden');
            }
        }

        // ── Submit on Enter ──────────────────────────────────────────────
        input?.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                const q = input.value.trim();
                if (!q) return;
                saveSearch(q);
                closeSearch();
                window.location.href = '/collections?search=' + encodeURIComponent(q);
            }
        });

        // Exposed so inline onclick handlers can call it
        window.saveSearchAndGo = function (q) { saveSearch(q); closeSearch(); };
    })();
    </script>
    <script>
    (function () {
        var nav  = document.getElementById('mainNav');
        var main = document.querySelector('main');
        var LG   = 1024;

        function applyNav() {
            if (window.innerWidth >= LG) {
                nav.style.position = 'fixed';
                nav.style.top      = '0';
                nav.style.left     = '0';
                nav.style.right    = '0';
                var h = nav.offsetHeight;
                main.style.paddingTop = h + 'px';
                document.documentElement.style.setProperty('--nav-h', h + 'px');
            } else {
                nav.style.position = 'sticky';
                nav.style.top      = '0';
                nav.style.left     = '';
                nav.style.right    = '';
                main.style.paddingTop = '';
                document.documentElement.style.setProperty('--nav-h', '0px');
            }
        }

        applyNav();
        window.addEventListener('resize', applyNav);
    })();
    </script>
    <script>
    (function () {
        function initShimmer() {
            document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
                if (img.complete && img.naturalHeight !== 0) return;
                var parent = img.parentElement;
                parent.classList.add('shimmer-loading');
                function done() {
                    parent.classList.remove('shimmer-loading');
                }
                img.addEventListener('load',  done, { once: true });
                img.addEventListener('error', done, { once: true });
                // Guard race condition: image selesai load sebelum listener dipasang
                if (img.complete) done();
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initShimmer);
        } else {
            initShimmer();
        }
    })();
    </script>
</body>

</html>
