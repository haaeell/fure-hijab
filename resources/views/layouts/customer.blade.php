<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FURE - @yield('title', 'Elegansi dalam Kesantunan')</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
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
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
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

<body class="bg-white font-sans text-gray-900 antialiased overflow-x-hidden">

    <nav class="sticky top-0 z-50 border-b border-brand-secondary/40 bg-white">
        <div class="bg-brand-dark text-white">
            <div
                class="no-scrollbar mx-auto flex max-w-7xl overflow-hidden whitespace-nowrap px-4 py-2 text-[10px] font-bold uppercase tracking-[0.24em] sm:px-6 lg:px-8">
                @for ($i = 0; $i < 5; $i++)
                    <span class="mr-10">Exclusive discount 10% off</span>
                    <span class="mr-10 text-brand-secondary">Mushroom collection ready</span>
                    <span class="mr-10">Free gift selected item</span>
                @endfor
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 grid grid-cols-3 items-center lg:flex lg:justify-between">
            <div class="flex items-center gap-2 lg:hidden">
                <button type="button" aria-label="Menu" class="p-2 text-brand-dark">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <a href="{{ route('collections.index') }}" aria-label="Cari produk" class="p-2 text-brand-dark">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </a>
            </div>

            <a href="/" class="group flex items-center justify-center gap-2.5 lg:justify-start">
                <div
                    class="hidden w-9 h-9 bg-brand-primary items-center justify-center transition-colors group-hover:bg-brand-dark sm:flex">
                    <i class="fa-solid fa-wand-magic-sparkles text-white text-base"></i>
                </div>
                <span class="text-brand-dark font-black text-xl tracking-[0.22em] uppercase">FURE</span>
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
            <div class="flex items-center justify-end gap-2 lg:gap-3">
                <a href="{{ route('collections.index') }}" class="hidden p-2 text-brand-dark transition-colors hover:text-brand-primary md:block">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </a>
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
        <div class="no-scrollbar flex gap-6 overflow-x-auto border-t border-brand-secondary/30 px-4 py-3 text-[11px] font-bold uppercase tracking-[0.18em] text-brand-dark/75 lg:hidden">
            <a href="/" class="{{ request()->is('/') ? 'text-brand-primary' : '' }} whitespace-nowrap">Home</a>
            <a href="{{ route('best-seller.index') }}" class="{{ request()->routeIs('best-seller.*') ? 'text-brand-primary' : '' }} whitespace-nowrap">Best Seller</a>
            <a href="{{ route('hijab.index') }}" class="{{ request()->routeIs('hijab.*') || request()->routeIs('collections.*') ? 'text-brand-primary' : '' }} whitespace-nowrap">Hijab</a>
            <a href="{{ route('syari.index') }}" class="{{ request()->routeIs('syari.*') ? 'text-brand-primary' : '' }} whitespace-nowrap">Syar'i</a>
            <a href="{{ route('new-arrived.index') }}" class="{{ request()->routeIs('new-arrived.*') ? 'text-brand-primary' : '' }} whitespace-nowrap">New Arrived</a>
            <a href="{{ route('about.index') }}" class="{{ request()->routeIs('about.*') ? 'text-brand-primary' : '' }} whitespace-nowrap">Store Locator</a>
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
                            FURE
                        </span>
                    </div>
                    <p class="text-brand-secondary/70 text-lg leading-relaxed max-w-sm">
                        Elegansi dalam kesantunan. Mewujudkan standar baru hijab premium untuk wanita yang menghargai
                        kualitas dan estetika.
                    </p>
                    <div class="flex gap-4">
                        <a href="#"
                            class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-primary hover:scale-110 transition-all duration-300 group">
                            <i class="fa-brands fa-instagram text-xl group-hover:text-white"></i>
                        </a>
                        <a href="#"
                            class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-brand-primary hover:scale-110 transition-all duration-300 group">
                            <i class="fa-brands fa-tiktok text-xl group-hover:text-white"></i>
                        </a>
                        <a href="#"
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
                    &copy; 2026 FURE. Crafted with Grace.
                </p>
                <div class="flex gap-8 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-secondary/30">
                    <a href="#" class="hover:text-brand-secondary transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-brand-secondary transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

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

            btn.on('click', function (e) {
                e.stopPropagation();
                menu.toggleClass('hidden');
                arrow.toggleClass('rotate-180');
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#userDropdownContainer').length) {
                    menu.addClass('hidden');
                    arrow.removeClass('rotate-180');
                }
            });
        });
    </script>
</body>

</html>
