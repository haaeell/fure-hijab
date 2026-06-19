<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $adminStoreName = \App\Models\Setting::getValue('store_name', 'FURE');
        $adminStoreLogo = \App\Models\Setting::getValue('store_logo');
    @endphp
    <title>Admin Dashboard - {{ $adminStoreName }}</title>

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
                {{ request()->is('products*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
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

            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] px-4 mb-3 mt-6">Penjualan</p>

            <a href="/orders"
                class="flex items-center gap-4 px-4 py-3.5 rounded-xl transition-all
                {{ request()->is('orders*') ? 'active-menu font-bold text-gray-500' : 'font-semibold text-gray-500 hover:bg-soft-mint/50 hover:text-brand-dark' }}">
                <i class="fa-solid fa-cart-shopping w-5"></i> Pesanan

                @php
                    $count = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])->count();
                @endphp

                @if($count > 0)
                    <span class="ml-auto bg-orange-100 text-orange-600 text-[10px] px-2 py-0.5 rounded-lg">
                        {{ $count }}
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

            <div class="flex items-center gap-3">
                <div class="relative">
                    <button id="notifDropdownBtn"
                        class="w-12 h-12 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-gray-400 hover:text-brand-primary transition-all relative">
                        <i class="fa-regular fa-bell"></i>
                        @php
                            $hasNotif = \App\Models\Order::whereIn('status', ['pending','confirmed'])->exists()
                                     || \App\Models\Product::where('is_active',true)->where('stock','>'  ,0)->where('stock','<=',5)->exists()
                                     || \App\Models\Review::where('is_verified',false)->exists();
                        @endphp
                        @if($hasNotif)
                            <span class="absolute top-3 right-3 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                        @endif
                    </button>

                    @php
                        $notifPendingOrders = \App\Models\Order::whereIn('status', ['pending', 'confirmed'])
                            ->with('user')->latest()->take(3)->get();
                        $notifLowStock = \App\Models\Product::where('is_active', true)
                            ->where('stock', '>', 0)->where('stock', '<=', 5)
                            ->orderBy('stock')->take(3)->get();
                        $notifNewReviews = \App\Models\Review::where('is_verified', false)
                            ->with(['product', 'user'])->latest()->take(3)->get();
                        $notifTotal = $notifPendingOrders->count() + $notifLowStock->count() + $notifNewReviews->count();
                    @endphp
                    <div id="notifDropdown"
                        class="absolute right-0 mt-3 w-80 md:w-96 bg-white rounded-[2rem] shadow-2xl border border-gray-50 overflow-hidden hidden z-50">
                        <div class="p-5 border-b border-gray-50 flex justify-between items-center bg-soft-bg/50">
                            <h3 class="font-extrabold text-brand-dark text-sm">Perlu Perhatian</h3>
                            @if($notifTotal > 0)
                                <span class="text-[10px] font-bold text-brand-primary bg-brand-primary/10 px-2 py-1 rounded-lg">
                                    {{ $notifTotal }} item
                                </span>
                            @endif
                        </div>

                        <div class="max-h-[400px] overflow-y-auto">
                            {{-- Pending / confirmed orders --}}
                            @forelse($notifPendingOrders as $notifOrder)
                                <a href="{{ route('orders.show', $notifOrder->id) }}"
                                    class="flex gap-4 p-4 hover:bg-soft-mint/30 transition-all border-b border-gray-50">
                                    <div class="w-11 h-11 rounded-xl bg-orange-100 text-orange-600 flex-shrink-0 flex items-center justify-center">
                                        <i class="fa-solid fa-cart-shopping text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-brand-dark leading-tight mb-1">
                                            Order {{ $notifOrder->status === 'pending' ? 'Menunggu Pembayaran' : 'Perlu Dikonfirmasi' }}
                                        </p>
                                        <p class="text-[10px] text-gray-500 line-clamp-2">
                                            {{ $notifOrder->user->name }} — #{{ $notifOrder->order_number }}
                                            · Rp{{ number_format($notifOrder->total, 0, ',', '.') }}
                                        </p>
                                        <p class="text-[9px] text-gray-400 mt-1 font-medium">{{ $notifOrder->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @empty
                            @endforelse

                            {{-- Low stock --}}
                            @forelse($notifLowStock as $lowProd)
                                <a href="/products"
                                    class="flex gap-4 p-4 hover:bg-soft-mint/30 transition-all border-b border-gray-50">
                                    <div class="w-11 h-11 rounded-xl bg-red-100 text-red-600 flex-shrink-0 flex items-center justify-center">
                                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-brand-dark leading-tight mb-1">Stok Menipis</p>
                                        <p class="text-[10px] text-gray-500 line-clamp-2">
                                            {{ $lowProd->name }} — sisa {{ $lowProd->stock }} pcs
                                        </p>
                                    </div>
                                </a>
                            @empty
                            @endforelse

                            {{-- Unverified reviews --}}
                            @forelse($notifNewReviews as $rev)
                                <a href="/reviews"
                                    class="flex gap-4 p-4 hover:bg-soft-mint/30 transition-all border-b border-gray-50">
                                    <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex-shrink-0 flex items-center justify-center">
                                        <i class="fa-solid fa-star text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-brand-dark leading-tight mb-1">
                                            Ulasan Baru ({{ $rev->rating }}★)
                                        </p>
                                        <p class="text-[10px] text-gray-500 line-clamp-2">
                                            {{ $rev->user->name }} — {{ $rev->product->name }}
                                        </p>
                                        <p class="text-[9px] text-gray-400 mt-1 font-medium">{{ $rev->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @empty
                            @endforelse

                            @if($notifTotal === 0)
                                <div class="py-10 text-center text-gray-400">
                                    <i class="fa-solid fa-check-circle text-2xl text-green-300 mb-2 block"></i>
                                    <p class="text-xs font-semibold">Semua beres!</p>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('orders.index') }}"
                            class="block py-4 text-center text-[11px] font-bold text-brand-primary border-t border-gray-50 hover:bg-gray-50 tracking-wider uppercase">
                            Lihat Semua Pesanan
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <button id="userDropdownBtn"
                        class="flex items-center gap-3 bg-white p-1.5 pr-4 border border-gray-100 rounded-2xl shadow-sm hover:border-brand-primary transition-all">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                class="w-9 h-9 rounded-xl shadow-sm">
                        @else
                            <i class="fa-solid fa-user-tie text-5xl text-brand-primary"></i>
                        @endif
                        <div class="text-left hidden xs:block">
                            <p class="text-[12px] font-extrabold text-brand-dark leading-none mb-1">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                {{ Auth::user()->role }}
                            </p>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-300 ml-1"></i>
                    </button>

                    <div id="userDropdown"
                        class="absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-xl border border-gray-50 p-2 hidden z-50">
                        <a href="/profile"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:bg-soft-mint transition-all">
                            <i class="fa-regular fa-user"></i> Profil
                        </a>
                        <a href="{{ route('settings.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:bg-soft-mint transition-all">
                            <i class="fa-solid fa-gear"></i> Pengaturan
                        </a>
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
</body>

</html>
