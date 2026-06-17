<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $storeName = \App\Models\Setting::getValue('store_name', 'FURE');
        $storeLogo = \App\Models\Setting::getValue('store_logo');
        $storeEmail = \App\Models\Setting::getValue('store_email');
        $storePhone = \App\Models\Setting::getValue('store_phone');
        $storeAddress = \App\Models\Setting::getValue('store_address');
        $storeInstagram = \App\Models\Setting::getValue('store_instagram');
        $storeTiktok = \App\Models\Setting::getValue('store_tiktok');
        $storeWhatsapp = \App\Models\Setting::getValue('store_whatsapp');
    @endphp
    <title>{{ $storeName }} - @yield('title', 'Elegansi dalam Kesantunan')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'soft-mint': '#F1F8E9',
                        'soft-blue': '#E3F2FD',
                        'brand-primary': '#A78B6F',
                        'brand-secondary': '#D6C4B0',
                        'brand-dark': '#5F4A3A',
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        html {
            scroll-behavior: smooth;
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
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-bottom: 1rem;
            }
        }
    </style>
    @yield('styles')
</head>

    <body class="bg-[#f8f3ee] font-sans text-gray-900 antialiased overflow-x-hidden">

    <nav class="sticky top-0 z-50 border-b border-brand-secondary/40 bg-white">
        <div class="bg-brand-dark text-white overflow-hidden">
            <div class="flex w-max animate-marquee whitespace-nowrap px-4 py-2 text-[10px] font-bold uppercase tracking-[0.24em] sm:px-6 lg:px-8">
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 grid grid-cols-3 items-center lg:flex lg:justify-between">
            <div class="flex items-center gap-2 lg:hidden">
                <button type="button" id="mobileMenuButton" aria-label="Menu" aria-expanded="false" aria-controls="mobileMenuPanel"
                    class="flex h-10 w-10 items-center justify-center text-brand-dark transition hover:bg-[#f8f3ee]">
                    <i id="mobileMenuIcon" class="fa-solid fa-bars text-lg"></i>
                </button>
                <button type="button" data-search-trigger aria-label="Cari produk" class="p-2 text-brand-dark transition hover:text-brand-primary">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </button>
            </div>

            <a href="/" class="group flex items-center justify-center gap-2.5 lg:justify-start">
                @if($storeLogo)
                    <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" class="hidden h-9 w-9 object-cover sm:block">
                @else
                    <div class="hidden w-9 h-9 bg-brand-primary items-center justify-center transition-colors group-hover:bg-brand-dark sm:flex">
                        <i class="fa-solid fa-wand-magic-sparkles text-white text-base"></i>
                    </div>
                @endif
                <span class="text-brand-dark font-black text-xl tracking-[0.22em] uppercase">{{ $storeName }}</span>
            </a>

            <div class="hidden lg:flex items-center gap-7 text-[11px] font-bold uppercase tracking-[0.18em] text-brand-dark/80">
                <a href="/"
                    class="transition-colors relative {{ request()->is('/') ? 'text-brand-primary after:scale-x-100' : 'hover:text-brand-primary after:scale-x-0' }} after:content-[''] after:absolute after:bottom-[-6px] after:left-0 after:w-full after:h-px after:bg-brand-primary hover:after:scale-x-100 after:transition-transform">
                    Home
                </a>

                <a href="{{ route('best-seller.index') }}"
                    class="transition-colors {{ request()->routeIs('best-seller.*') ? 'text-brand-primary' : 'hover:text-brand-primary' }}">
                    Best Seller
                </a>

                <a href="{{ route('hijab.index') }}"
                    class="transition-colors {{ request()->routeIs('hijab.*') || request()->routeIs('collections.*') ? 'text-brand-primary' : 'hover:text-brand-primary' }}">
                    Hijab
                </a>

                <a href="{{ route('syari.index') }}"
                    class="transition-colors {{ request()->routeIs('syari.*') ? 'text-brand-primary' : 'hover:text-brand-primary' }}">
                    Syar'i
                </a>

                <a href="{{ route('new-arrived.index') }}"
                    class="transition-colors {{ request()->routeIs('new-arrived.*') ? 'text-brand-primary' : 'hover:text-brand-primary' }}">
                    New Arrived
                </a>

                <a href="{{ route('about.index') }}"
                    class="transition-colors {{ request()->routeIs('about.*') ? 'text-brand-primary' : 'hover:text-brand-primary' }}">
                    Store Locator
                </a>
            </div>
            @php
                $wishlistCount = (Auth::check() && Auth::user()->role === 'customer')
                    ? \App\Models\Wishlist::where('user_id', Auth::id())->count()
                    : 0;
            @endphp
            <div class="flex items-center justify-end gap-2 lg:gap-3">
                <button type="button" data-search-trigger class="hidden p-2 text-brand-dark transition-colors hover:text-brand-primary md:block">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </button>
                @if(Auth::check() && Auth::user()->role === 'customer')
                    <a href="{{ route('wishlist.index') }}" class="relative p-2 text-brand-dark transition-colors hover:text-brand-primary">
                        <i class="fa-regular fa-heart text-lg"></i>
                        @if($wishlistCount > 0)
                            <span class="absolute top-0 right-0 bg-brand-primary text-white text-[10px] w-4 h-4 flex items-center justify-center border-2 border-white shadow">
                                {{ $wishlistCount > 9 ? '9+' : $wishlistCount }}
                            </span>
                        @endif
                    </a>
                @endif
                <a href="/cart" class="relative p-2 text-brand-dark hover:text-brand-primary transition-colors">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    <span
                        class="absolute top-0 right-0 bg-brand-primary text-white text-[10px] w-4 h-4 flex items-center justify-center border-2 border-white shadow">
                        @auth
                                            {{ \App\Models\CartItem::whereHas('cart', function ($q) {
                            $q->where('user_id', auth()->id()); })->count() }}
                        @else
                            0
                        @endauth
                    </span>
                </a>

                @auth
                    <div class="relative ml-2" id="userDropdownContainer">
                        <button type="button" id="userDropdownBtn"
                            class="flex items-center gap-3 pl-3 pr-1 py-1 bg-gray-50 border border-gray-100 rounded-2xl hover:bg-white hover:shadow-md transition-all duration-300">
                            <span class="hidden md:block text-sm font-bold text-brand-dark">{{ Auth::user()->name }}</span>
                            <div
                                class="w-9 h-9 bg-brand-primary/10 rounded-xl flex items-center justify-center border border-brand-primary/20">
                                <i class="fa-solid fa-user text-brand-primary text-sm"></i>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 mr-2 transition-transform duration-300"
                                id="dropdownArrow"></i>
                        </button>

                        <div id="userDropdownMenu"
                            class="hidden absolute right-0 mt-3 w-56 bg-white/90 backdrop-blur-xl rounded-[24px] shadow-[0_20px_50px_rgba(95,74,58,0.1)] border border-white p-2 z-[60] origin-top-right transition-all">

                            <div class="px-4 py-3 border-b border-gray-50 mb-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role Akun</p>
                                <p class="text-xs font-bold text-brand-primary uppercase">{{ Auth::user()->role }}</p>
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
                        class="hidden px-6 py-3 bg-brand-primary text-white text-sm font-bold shadow-lg shadow-brand-primary/20 hover:shadow-brand-primary/40 hover:-translate-y-0.5 transition-all active:scale-95 md:inline-flex">Daftar</a>
                    <a href="/login" aria-label="Masuk" class="p-2 text-brand-dark md:hidden">
                        <i class="fa-regular fa-user text-lg"></i>
                    </a>
                @endauth
            </div>
        </div>
        <div id="mobileMenuPanel" class="hidden border-t border-brand-secondary/30 bg-white px-4 py-4 shadow-[0_18px_40px_rgba(95,74,58,0.08)] lg:hidden">
            <div class="grid gap-2 text-[12px] font-bold uppercase tracking-[0.16em] text-brand-dark/75">
                <a href="/"
                    class="flex items-center justify-between px-3 py-3 transition {{ request()->is('/') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Home
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-40"></i>
                </a>
                <a href="{{ route('best-seller.index') }}"
                    class="flex items-center justify-between px-3 py-3 transition {{ request()->routeIs('best-seller.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Best Seller
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-40"></i>
                </a>
                <a href="{{ route('hijab.index') }}"
                    class="flex items-center justify-between px-3 py-3 transition {{ request()->routeIs('hijab.*') || request()->routeIs('collections.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Hijab
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-40"></i>
                </a>
                <a href="{{ route('syari.index') }}"
                    class="flex items-center justify-between px-3 py-3 transition {{ request()->routeIs('syari.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Syar'i
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-40"></i>
                </a>
                <a href="{{ route('new-arrived.index') }}"
                    class="flex items-center justify-between px-3 py-3 transition {{ request()->routeIs('new-arrived.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    New Arrived
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-40"></i>
                </a>
                <a href="{{ route('about.index') }}"
                    class="flex items-center justify-between px-3 py-3 transition {{ request()->routeIs('about.*') ? 'bg-[#f8f3ee] text-brand-primary' : 'hover:bg-[#f8f3ee] hover:text-brand-primary' }}">
                    Store Locator
                    <i class="fa-solid fa-chevron-right text-[10px] opacity-40"></i>
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
                    <p class="text-brand-secondary/70 text-lg leading-relaxed max-w-sm">
                        Elegansi dalam kesantunan. Mewujudkan standar baru hijab premium untuk wanita yang menghargai
                        kualitas dan estetika.
                    </p>
                    @if($storeAddress || $storeEmail || $storePhone)
                        <div class="space-y-2 text-sm text-brand-secondary/60">
                            @if($storeAddress)<p>{{ $storeAddress }}</p>@endif
                            @if($storeEmail)<p>{{ $storeEmail }}</p>@endif
                            @if($storePhone)<p>{{ $storePhone }}</p>@endif
                        </div>
                    @endif
                    <div class="flex gap-4">
                        <a href="{{ $storeInstagram ?: '#' }}"
                            class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-primary hover:scale-110 transition-all duration-300 group">
                            <i class="fa-brands fa-instagram text-xl group-hover:text-white"></i>
                        </a>
                        <a href="{{ $storeTiktok ?: '#' }}"
                            class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-primary hover:scale-110 transition-all duration-300 group">
                            <i class="fa-brands fa-tiktok text-xl group-hover:text-white"></i>
                        </a>
                        <a href="{{ $storeWhatsapp ? 'https://api.whatsapp.com/send?phone=' . preg_replace('/\D+/', '', str_starts_with($storeWhatsapp, '0') ? '62' . substr($storeWhatsapp, 1) : $storeWhatsapp) : '#' }}"
                            class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-primary hover:scale-110 transition-all duration-300 group">
                            <i class="fa-brands fa-whatsapp text-xl group-hover:text-white"></i>
                        </a>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">
                    <h4 class="text-sm font-bold uppercase tracking-widest text-brand-secondary">Koleksi</h4>
                    <ul class="space-y-4 text-brand-secondary/60">
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Best Seller</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Hijab Instan</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Pashmina</a></li>
                        <li><a href="#" class="hover:text-white transition-colors duration-200">Premium Silk</a></li>
                    </ul>
                </div>

                <div class="md:col-span-5 space-y-6">
                    <h4 class="text-sm font-bold uppercase tracking-widest text-brand-secondary">Dapatkan Update Terbaru
                    </h4>
                    <p class="text-brand-secondary/60 text-sm">Berlangganan newsletter untuk info promo eksklusif.</p>
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
                <p class="text-brand-secondary/40 text-xs tracking-widest uppercase">
                    &copy; 2026 {{ $storeName }}. Crafted with Grace.
                </p>
                <div class="flex gap-8 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-secondary/30">
                    <a href="#" class="hover:text-brand-secondary transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-brand-secondary transition-colors">Terms of Service</a>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('scripts')

    @if (session('success') || session('error'))
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
        let map, marker;
        const defaultLat = -7.7956;
        const defaultLng = 110.3695;

        function initMap() {
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
                    initMap();
                    map.invalidateSize();
                }, 100);
            }
        };

        $(document).on('click', '#btn-my-location', function () {
            if (!navigator.geolocation) return;

            if (!map) {
                initMap();
            }

            const $btn = $(this).prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Mencari...');

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
                list.innerHTML = data.terms.map(t => `
                    <button onclick="document.getElementById('searchInput').value=${JSON.stringify(t)};document.getElementById('searchInput').dispatchEvent(new Event('input'))"
                        class="flex items-center gap-2 border border-brand-secondary/60 bg-white px-3 py-1.5 text-xs font-semibold text-brand-dark transition hover:border-brand-primary hover:text-brand-primary">
                        <i class="fa-solid fa-fire text-[10px] text-brand-primary/60"></i>${esc(t)}
                    </button>`).join('');
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
</body>

</html>
