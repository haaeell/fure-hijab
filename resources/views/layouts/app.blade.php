<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - {{ $adminStoreName }}</title>
    @if($adminStoreLogo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $adminStoreLogo) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $adminStoreLogo) }}">
    @endif

    <link href="https://fonts.bunny.net/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Plugin CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-primary': '#A78B6F',
                        'brand-secondary': '#D6C4B0',
                        'brand-dark': '#5F4A3A',
                        'soft-mint': '#F1F8E9',
                        'soft-bg': '#F8FBF8',
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #F8FBF8;
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #A78B6F;
            border-radius: 10px;
        }

        @keyframes fure-shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position:  200% 0; }
        }
        .shimmer-loading {
            background: linear-gradient(90deg, #f5f3f1 25%, #ede9e5 50%, #f5f3f1 75%);
            background-size: 200% 100%;
            animation: fure-shimmer 1.4s ease-in-out infinite;
        }

        .sidebar-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .active-menu {
            background: rgba(167, 139, 111, 0.1);
            color: #5F4A3A !important;
            border-right: 4px solid #A78B6F;
        }

        @media (max-width: 1024px) {
            .sidebar-closed {
                transform: translateX(-100%);
            }

            .main-content-expanded {
                margin-left: 0 !important;
            }
        }

        table.dataTable thead th {
            background-color: #f8fafc;
        }

        .select2-container .select2-selection--single {
            height: 42px;
            border-radius: 0.75rem;
            border: 1px solid #d1d5db;
            padding: 6px 12px;
            display: flex;
            align-items: center;
        }

        .select2-selection__rendered {
            padding-left: 0 !important;
        }

        .select2-selection__arrow {
            height: 100%;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #A78B6F;
            outline: none;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased">

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-[55] hidden lg:hidden"></div>

    <aside id="sidebar"
        class="w-64 bg-white border-r border-gray-100 flex flex-col fixed h-full z-[60] sidebar-transition sidebar-closed lg:transform-none">

        <div class="p-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($adminStoreLogo)
                    <img src="{{ asset('storage/' . $adminStoreLogo) }}" alt="{{ $adminStoreName }}" class="w-10 h-10 rounded-xl object-cover shadow-md">
                @else
                    <div class="w-10 h-10 bg-brand-primary rounded-xl flex items-center justify-center shadow-md shadow-brand-primary/20">
                        <i class="fa-solid fa-wand-magic-sparkles text-white"></i>
                    </div>
                @endif
                <span class="text-brand-dark font-extrabold text-xl tracking-tight">{{ $adminStoreName }}</span>
            </div>
            <button id="closeSidebar" class="lg:hidden text-gray-400 hover:text-red-500">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto pb-10">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] px-4 mb-3 mt-4">Utama</p>

            <a href="/home"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('home') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-gray-50' }}">
                <i class="fa-solid fa-chart-simple w-5"></i> Dashboard
            </a>

            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] px-4 mb-3 mt-6">Master</p>

            <a href="/categories"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('categories*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-layer-group w-5"></i> Kategori
            </a>

            <a href="/brands"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('brands*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-copyright w-5"></i> Brand
            </a>

            <a href="/products"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('products') || request()->is('products/*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-box w-5"></i> Produk
            </a>

            <a href="{{ route('koleksi.index') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('koleksi*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-swatchbook w-5"></i> Koleksi
            </a>

            <a href="{{ route('couriers.index') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('couriers*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-truck w-5"></i> Kurir
            </a>

            <a href="{{ route('landing-content.index') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('landing-content*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-images w-5"></i> Landing Page
            </a>

            <a href="{{ route('admin.articles.index') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('admin/articles*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-newspaper w-5"></i> Artikel
            </a>

            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] px-4 mb-3 mt-6">Penjualan</p>

            <a href="/orders"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('orders*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-cart-shopping w-5"></i> Pesanan

                @if($adminPendingOrderCount > 0)
                    <span class="ml-auto bg-orange-100 text-orange-600 text-[10px] px-2 py-0.5 rounded-lg">
                        {{ $adminPendingOrderCount }}
                    </span>
                @endif
            </a>

            <a href="/coupons"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('coupons*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-ticket w-5"></i> Kupon Promo
            </a>

            <a href="{{ route('reports.index') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('reports*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-chart-pie w-5"></i> Laporan
            </a>

            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] px-4 mb-3 mt-6">Pengguna</p>

            <a href="/customers"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('customers*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-user-group w-5"></i> Pelanggan
            </a>

            <a href="/reviews"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('reviews*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-message w-5"></i> Ulasan
            </a>

            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] px-4 mb-3 mt-6">Sistem</p>

            <a href="{{ route('settings.store') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('settings/store*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-store w-5"></i> Pengaturan Toko
            </a>

            <a href="{{ route('settings.index') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('settings') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-plug w-5"></i> Integrasi API
            </a>
        </nav>

        <div class="p-4 border-t border-gray-50 mt-auto">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>

            <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="w-full flex items-center gap-4 px-4 py-3.5 rounded-xl text-red-500 hover:bg-red-50 font-bold transition-all">
                <i class="fa-solid fa-power-off w-5"></i> Keluar
            </button>
        </div>
    </aside>

    <main id="mainContent" class="lg:ml-64 p-4 md:p-8 sidebar-transition">

        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <button id="mobileMenuBtn"
                    class="lg:hidden w-12 h-12 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-gray-500">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <div class="hidden md:block">
                    <p class="text-gray-400 text-sm font-medium">Panel Admin toko FURE.</p>
                </div>
            </div>

            <div class="flex items-center gap-2">

                {{-- ── NOTIFIKASI ── --}}
                <div class="relative">
                    <button id="notifDropdownBtn"
                        class="relative w-11 h-11 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-gray-400 hover:text-brand-primary hover:border-brand-primary/30 transition-all shadow-sm">
                        <i class="fa-regular fa-bell text-base"></i>
                        {{-- Badge server-side (muncul saat halaman dimuat) --}}
                        <span id="notifBadge"
                            class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[9px] font-black border-2 border-white leading-none transition-transform duration-300
                            {{ $adminNotifTotal > 0 ? '' : 'hidden' }}"
                            data-count="{{ $adminNotifTotal }}">
                            {{ $adminNotifTotal > 9 ? '9+' : $adminNotifTotal }}
                        </span>
                    </button>

                    {{-- Dropdown --}}
                    <div id="notifDropdown"
                        class="absolute right-0 mt-2 w-[22rem] bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden hidden z-50">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-50">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-xl bg-brand-primary/10 flex items-center justify-center">
                                    <i class="fa-regular fa-bell text-brand-primary text-xs"></i>
                                </div>
                                <span class="font-extrabold text-brand-dark text-sm">Notifikasi</span>
                                <span id="notifBadgeDropdown"
                                    class="text-[10px] font-black bg-red-500 text-white px-2 py-0.5 rounded-full {{ $adminNotifTotal > 0 ? '' : 'hidden' }}">
                                    {{ $adminNotifTotal }}
                                </span>
                            </div>
                            <a href="{{ route('orders.index') }}"
                                class="text-[10px] font-bold text-brand-primary hover:text-brand-dark transition-colors">
                                Lihat semua
                            </a>
                        </div>

                        {{-- List --}}
                        <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-50" id="notifList">

                            @forelse($adminNotifPendingOrders as $notifOrder)
                                <a href="{{ route('orders.show', $notifOrder->id) }}"
                                    class="flex items-start gap-3 px-5 py-3.5 hover:bg-amber-50/50 transition-colors group">
                                    <div class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-600 flex-shrink-0 flex items-center justify-center mt-0.5">
                                        <i class="fa-solid fa-bag-shopping text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-brand-dark leading-snug">
                                            {{ $notifOrder->status === 'pending' ? 'Menunggu Pembayaran' : 'Perlu Dikonfirmasi' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                            {{ $notifOrder->user->name }} · #{{ $notifOrder->order_number }}
                                        </p>
                                        <p class="text-[10px] font-bold text-amber-600 mt-0.5">
                                            Rp{{ number_format($notifOrder->total, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <span class="text-[9px] text-gray-400 shrink-0 mt-1">{{ $notifOrder->created_at->diffForHumans(null, true) }}</span>
                                </a>
                            @empty
                            @endforelse

                            @forelse($adminNotifLowStock as $lowProd)
                                <a href="/products"
                                    class="flex items-start gap-3 px-5 py-3.5 hover:bg-red-50/50 transition-colors group">
                                    <div class="w-9 h-9 rounded-2xl bg-red-100 text-red-500 flex-shrink-0 flex items-center justify-center mt-0.5">
                                        <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-brand-dark leading-snug">Stok Menipis</p>
                                        <p class="text-[11px] text-gray-500 mt-0.5 truncate">{{ $lowProd->name }}</p>
                                        <p class="text-[10px] font-bold text-red-500 mt-0.5">Sisa {{ $lowProd->stock }} pcs</p>
                                    </div>
                                </a>
                            @empty
                            @endforelse

                            @forelse($adminNotifNewReviews as $rev)
                                <a href="/reviews"
                                    class="flex items-start gap-3 px-5 py-3.5 hover:bg-blue-50/50 transition-colors group">
                                    <div class="w-9 h-9 rounded-2xl bg-blue-100 text-blue-500 flex-shrink-0 flex items-center justify-center mt-0.5">
                                        <i class="fa-solid fa-star text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-brand-dark leading-snug">Ulasan Baru
                                            <span class="text-amber-500">{{ str_repeat('★', (int)$rev->rating) }}</span>
                                        </p>
                                        <p class="text-[11px] text-gray-500 mt-0.5 truncate">
                                            {{ $rev->user->name }} · {{ $rev->product->name }}
                                        </p>
                                    </div>
                                    <span class="text-[9px] text-gray-400 shrink-0 mt-1">{{ $rev->created_at->diffForHumans(null, true) }}</span>
                                </a>
                            @empty
                            @endforelse

                            @if($adminNotifTotal === 0)
                                <div id="notifEmpty" class="py-12 text-center">
                                    <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-solid fa-check text-green-400 text-lg"></i>
                                    </div>
                                    <p class="text-xs font-bold text-gray-500">Semua sudah ditangani!</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Tidak ada notifikasi baru.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Footer --}}
                        <div class="px-5 py-3 border-t border-gray-50 bg-gray-50/40">
                            <a href="{{ route('orders.index', ['status' => 'pending']) }}"
                                class="flex items-center justify-center gap-2 text-[11px] font-bold text-brand-primary hover:text-brand-dark transition-colors">
                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                Buka halaman pesanan pending
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ── USER DROPDOWN ── --}}
                <div class="relative">
                    <button id="userDropdownBtn"
                        class="flex items-center gap-2.5 bg-white pl-1.5 pr-3 py-1.5 border border-gray-100 rounded-2xl shadow-sm hover:border-brand-primary/40 transition-all">
                        @if($adminUser->avatar)
                            <img src="{{ asset('storage/' . $adminUser->avatar) }}"
                                class="w-8 h-8 rounded-xl object-cover shadow-sm">
                        @else
                            <div class="w-8 h-8 rounded-xl bg-brand-primary/10 flex items-center justify-center">
                                <i class="fa-solid fa-user-tie text-brand-primary text-sm"></i>
                            </div>
                        @endif
                        <div class="text-left hidden sm:block">
                            <p class="text-[11px] font-extrabold text-brand-dark leading-none">{{ $adminUser->name }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">{{ $adminUser->role }}</p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[9px] text-gray-300"></i>
                    </button>

                    <div id="userDropdown"
                        class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 p-1.5 hidden z-50">
                        <div class="px-4 py-3 border-b border-gray-50 mb-1">
                            <p class="text-xs font-bold text-brand-dark truncate">{{ $adminUser->name }}</p>
                            <p class="text-[10px] text-gray-400 truncate">{{ $adminUser->email }}</p>
                        </div>
                        <a href="/profile"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-soft-mint transition-all">
                            <i class="fa-regular fa-user text-gray-400 w-4 text-center"></i>
                            <span class="text-xs">Profil Saya</span>
                        </a>
                        <a href="{{ route('settings.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-soft-mint transition-all">
                            <i class="fa-solid fa-gear text-gray-400 w-4 text-center"></i>
                            <span class="text-xs">Pengaturan</span>
                        </a>
                        <div class="border-t border-gray-50 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 transition-all">
                                    <i class="fa-solid fa-right-from-bracket w-4 text-center text-sm"></i>
                                    <span class="text-xs font-bold">Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            const sidebar = $('#sidebar');
            const overlay = $('#sidebarOverlay');
            const mainContent = $('#mainContent');

            $('#mobileMenuBtn').click(function () {
                sidebar.removeClass('sidebar-closed').addClass('translate-x-0');
                overlay.fadeIn(300).removeClass('hidden');
            });

            $('#closeSidebar, #sidebarOverlay').click(function () {
                sidebar.addClass('sidebar-closed').removeClass('translate-x-0');
                overlay.fadeOut(300);
            });

            $('#userDropdownBtn').click(function (e) {
                e.stopPropagation();
                $('#userDropdown').toggleClass('hidden animate-fade-in');
            });

            $(document).click(function () {
                $('#userDropdown').addClass('hidden');
            });

            $('.sidebar-item').click(function () {
                $('.sidebar-item').removeClass('active-menu');
                $(this).addClass('active-menu');
            });

            $(window).resize(function () {
                if ($(window).width() >= 1024) {
                    sidebar.removeClass('sidebar-closed translate-x-0');
                    overlay.hide();
                } else {
                    sidebar.addClass('sidebar-closed');
                }
            });

            $('#notifDropdownBtn').click(function (e) {
                e.stopPropagation();
                $('#userDropdown').addClass('hidden');
                $('#notifDropdown').toggleClass('hidden animate-fade-in');
            });

            $(document).click(function () {
                $('#notifDropdown').addClass('hidden');
            });

            $('#notifDropdown').click(function (e) {
                e.stopPropagation();
            });
        });
    </script>

    @stack('scripts')

    @if ($errors->any())
        <script>
            let errorMessages = '';
            @foreach ($errors->all() as $error)
                errorMessages += "{{ $error }}\n";
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: errorMessages,
            });
        </script>
    @endif

    @if (session('success') || session('error'))
        <script>
            $(document).ready(function () {
                var successMessage = "{{ session('success') }}";
                var errorMessage = "{{ session('error') }}";

                if (successMessage) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: successMessage,
                    });
                }

                if (errorMessage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                }
            });
        </script>
    @endif
    {{-- ─── ADMIN ORDER NOTIFICATION POLLING ─────────────────────────────── --}}
    <script>
    (function () {
        var POLL_INTERVAL = 30000;
        var STORAGE_KEY   = 'fure_admin_notif_ts';
        var seenKey       = 'fure_admin_notif_seen';

        function formatRp(amount) {
            return 'Rp' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function loadSeen() {
            try { return JSON.parse(localStorage.getItem(seenKey) || '[]'); } catch (e) { return []; }
        }

        function markSeen(ids) {
            var seen = loadSeen().concat(ids);
            if (seen.length > 200) seen = seen.slice(-200);
            localStorage.setItem(seenKey, JSON.stringify(seen));
        }

        // Update badge angka di tombol bell
        function bumpBadge(delta) {
            var $badge = $('#notifBadge');
            var $badgeInner = $('#notifBadgeDropdown');
            var cur  = parseInt($badge.data('count') || $badge.text()) || 0;
            var next = Math.max(0, cur + delta);

            if (next > 0) {
                var label = next > 9 ? '9+' : String(next);
                $badge.text(label).data('count', next).removeClass('hidden');
                $badgeInner.text(next).removeClass('hidden');
                // Animasi pulse singkat
                $badge.addClass('scale-125');
                setTimeout(function () { $badge.removeClass('scale-125'); }, 400);
            } else {
                $badge.addClass('hidden').data('count', 0);
                $badgeInner.addClass('hidden');
            }
        }

        function showToast(icon, title, html, onClick) {
            Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: true,
                confirmButtonText: 'Lihat →',
                showCloseButton: true,
                timer: 15000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl text-sm',
                    confirmButton: 'rounded-xl text-xs font-bold px-3 py-1.5 bg-brand-primary text-white',
                },
            }).fire({
                icon: icon,
                title: title,
                html: html,
            }).then(function (result) {
                if (result.isConfirmed && typeof onClick === 'function') onClick();
            });
        }

        function poll() {
            var since = localStorage.getItem(STORAGE_KEY) || (Date.now() - 35000);
            var seen  = loadSeen();

            $.getJSON("{{ route('orders.notifications.poll') }}", { since: since }, function (res) {
                if (res.server_ts) localStorage.setItem(STORAGE_KEY, res.server_ts);

                var newIds = [];
                var newOrderCount = 0;

                (res.events || []).forEach(function (ev) {
                    var uid = ev.type + '_' + ev.order_id;
                    if (seen.indexOf(uid) !== -1) return;
                    newIds.push(uid);

                    if (ev.type === 'new_order') {
                        newOrderCount++;
                        showToast(
                            'info',
                            '🛍️ Pesanan Baru Masuk!',
                            '<b>' + ev.order_number + '</b><br>'
                            + '<span style="font-size:12px;color:#555">' + ev.customer + ' — ' + formatRp(ev.total) + '</span>',
                            function () { window.location.href = '/orders/' + ev.order_id; }
                        );
                    } else if (ev.type === 'paid') {
                        showToast(
                            'success',
                            '💳 Pembayaran Diterima!',
                            '<b>' + ev.order_number + '</b><br>'
                            + '<span style="font-size:12px;color:#555">' + ev.customer + ' — ' + formatRp(ev.total) + '</span>'
                            + (ev.method ? '<br><span style="font-size:11px;color:#999">' + ev.method + '</span>' : ''),
                            function () { window.location.href = '/orders/' + ev.order_id; }
                        );
                    } else if (ev.type === 'cancelled') {
                        showToast(
                            'warning',
                            '❌ Pesanan Dibatalkan',
                            '<b>' + ev.order_number + '</b><br>'
                            + '<span style="font-size:12px;color:#555">' + ev.customer + ' — ' + formatRp(ev.total) + '</span>',
                            function () { window.location.href = '/orders/' + ev.order_id; }
                        );
                    }
                });

                if (newIds.length) {
                    markSeen(newIds);
                    if (newOrderCount > 0) bumpBadge(newOrderCount);
                }
            });
        }

        setTimeout(function () {
            poll();
            setInterval(poll, POLL_INTERVAL);
        }, 3000);
    })();
    </script>

    <script>
    (function () {
        function initShimmer() {
            document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
                if (img.complete && img.naturalHeight !== 0) return;
                var parent = img.parentElement;
                parent.classList.add('shimmer-loading');
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.25s ease';
                function done() {
                    parent.classList.remove('shimmer-loading');
                    img.style.opacity = '1';
                }
                img.addEventListener('load',  done, { once: true });
                img.addEventListener('error', done, { once: true });
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
