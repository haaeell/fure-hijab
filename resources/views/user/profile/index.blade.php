@extends('layouts.customer')

@section('title', 'Profil Saya — ' . $storeName)

@push('styles')
<style>
    .stat-card { transition: transform .15s, box-shadow .15s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px 0 rgba(0,0,0,.06); }
    .nav-item.active { background: #A78B6F; color: #fff; }
    .nav-item.active .nav-icon { color: #fff; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
</style>
@endpush

@section('content')
<section class="bg-[#f8f3ee] min-h-screen py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
<div class="max-w-6xl mx-auto space-y-6">

    <div class="relative overflow-hidden bg-brand-dark rounded-[28px] p-6 sm:p-8 lg:p-10">
        <div class="absolute inset-0 opacity-10" style="background: radial-gradient(ellipse at 70% 50%, #A78B6F 0%, transparent 70%)"></div>
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-6 text-center sm:text-left w-full md:w-auto">
                <div class="flex-shrink-0">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-brand-primary/20 flex items-center justify-center text-white border-2 border-brand-primary/40 shadow-inner">
                        <span class="text-3xl sm:text-4xl font-black">{{ strtoupper(substr($profileUser->name,0,1)) }}</span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-md px-2.5 py-1 rounded-full mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-primary animate-pulse"></span>
                        <span class="text-white text-[9px] font-bold uppercase tracking-widest">Member {{ $storeName }}</span>
                    </div>
                    <h1 class="text-white text-2xl sm:text-3xl font-extrabold leading-tight truncate">{{ $profileUser->name }}</h1>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-x-3 gap-y-0.5 mt-1 text-white/60 text-sm">
                        <span class="truncate">{{ $profileUser->email }}</span>
                        @if($profileUser->phone)
                            <span class="hidden sm:inline text-white/30">•</span>
                            <span>{{ $profileUser->phone }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bg-white/[0.06] backdrop-blur-sm border border-white/10 rounded-2xl p-4 text-center md:text-right w-full md:w-auto min-w-[200px]">
                <p class="text-white text-[10px] font-bold uppercase tracking-widest">Total Belanja</p>
                <p class="text-white text-2xl font-black mt-0.5">Rp{{ number_format($totalSpent,0,',','.') }}</p>
                <p class="text-white text-xs mt-0.5">dari {{ $orderCount }} pesanan</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card bg-white rounded-[22px] p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-primary/10 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-box-open text-brand-primary text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-brand-dark leading-tight">{{ $orderCount }}</p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Total Pesanan</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-[22px] p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-check-double text-green-500 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-brand-dark leading-tight">{{ $deliveredCount }}</p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Pesanan Selesai</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-[22px] p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-star text-amber-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-brand-dark leading-tight">{{ $reviewCount }}</p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Ulasan</p>
            </div>
        </div>
        <div class="stat-card bg-white rounded-[22px] p-5 border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-heart text-red-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-black text-brand-dark leading-tight">{{ $wishlistCount }}</p>
                <p class="text-xs text-gray-400 font-medium mt-0.5">Wishlist</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-[280px_1fr] gap-6 items-start">

        <nav class="space-y-1 bg-white p-3 rounded-[24px] border border-gray-100 shadow-sm sticky top-6">
            <button onclick="switchTab('edit-profile')" data-tab="edit-profile"
                class="nav-item active w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left">
                <i class="nav-icon fa-solid fa-user-pen w-5 text-center text-base"></i>
                <span>Edit Profil</span>
                <i class="fa-solid fa-chevron-right ml-auto text-[10px] opacity-40"></i>
            </button>
            <button onclick="switchTab('addresses')" data-tab="addresses"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left bg-white text-brand-dark hover:bg-gray-50">
                <i class="nav-icon fa-solid fa-location-dot w-5 text-center text-gray-400 text-base"></i>
                <span>Alamat Saya</span>
                <span class="ml-auto text-[10px] font-bold px-2 py-0.5 bg-gray-100 rounded-full text-gray-500">{{ $addresses->count() }}</span>
            </button>
            <button onclick="switchTab('recent-orders')" data-tab="recent-orders"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left bg-white text-brand-dark hover:bg-gray-50">
                <i class="nav-icon fa-solid fa-clock-rotate-left w-5 text-center text-gray-400 text-base"></i>
                <span>Pesanan Terbaru</span>
                <span class="ml-auto text-[10px] font-bold px-2 py-0.5 bg-gray-100 rounded-full text-gray-500">{{ $orderCount }}</span>
            </button>
            <button onclick="switchTab('security')" data-tab="security"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all text-left bg-white text-brand-dark hover:bg-gray-50">
                <i class="nav-icon fa-solid fa-shield-halved w-5 text-center text-gray-400 text-base"></i>
                <span>Keamanan</span>
            </button>
            <div class="pt-2 mt-2 border-t border-gray-100 space-y-1">
                <a href="/wishlist"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-brand-dark hover:bg-gray-50 transition-all">
                    <i class="fa-solid fa-heart w-5 text-center text-red-400 text-base"></i>
                    <span>Wishlist</span>
                    <i class="fa-solid fa-chevron-right ml-auto text-[10px] text-gray-300"></i>
                </a>
                <a href="/order-history"
                    class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-brand-dark hover:bg-gray-50 transition-all">
                    <i class="fa-solid fa-bag-shopping w-5 text-center text-brand-primary text-base"></i>
                    <span>Semua Pesanan</span>
                    <i class="fa-solid fa-chevron-right ml-auto text-[10px] text-gray-300"></i>
                </a>
                <button onclick="document.getElementById('logout-form').submit()"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50/60 transition-all text-left">
                    <i class="fa-solid fa-power-off w-5 text-center text-base"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </nav>

        <div class="space-y-6">

            <div id="tab-edit-profile" class="tab-panel active bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                        <i class="fa-solid fa-user-pen text-sm"></i>
                    </div>
                    <h2 class="font-bold text-lg text-brand-dark">Informasi Pribadi</h2>
                </div>
                <div class="p-6 sm:p-8">
                    @if(session('success'))
                        <div class="mb-6 flex items-center gap-3 rounded-xl bg-green-50 border border-green-100 px-4 py-3.5 text-sm font-semibold text-green-700">
                            <i class="fa-solid fa-circle-check text-base"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-6 flex items-center gap-3 rounded-xl bg-red-50 border border-red-100 px-4 py-3.5 text-sm font-semibold text-red-700">
                            <i class="fa-solid fa-circle-exclamation text-base"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-6">
                        @csrf @method('PUT')

                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $profileUser->name) }}" required
                                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition text-sm font-medium text-brand-dark">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email</label>
                                <input type="email" value="{{ $profileUser->email }}" disabled
                                    class="w-full px-4 py-3 bg-gray-100/80 border border-gray-200 rounded-xl text-gray-400 text-sm font-medium cursor-not-allowed">
                                <p class="mt-2 text-[11px] text-gray-400 flex items-center gap-1"><i class="fa-solid fa-lock text-[9px]"></i> Email tidak dapat diubah</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nomor WhatsApp</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400 border-r border-gray-200 pr-2">+62</span>
                                    <input type="tel" name="phone" value="{{ old('phone', ltrim($profileUser->phone ?? '', '0+62')) }}"
                                        placeholder="812-3456-7890" inputmode="tel" pattern="[0-9\-\s]*"
                                        class="w-full pl-16 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition text-sm font-medium text-brand-dark">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-100">
                            <p class="text-xs text-gray-400">Bergabung sejak {{ $profileUser->created_at->translatedFormat('d F Y') }}</p>
                            <button type="submit"
                                class="w-full sm:w-auto px-8 py-3 bg-brand-primary hover:bg-brand-dark text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-brand-primary/10 active:scale-[0.98]">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="tab-addresses" class="tab-panel bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                            <i class="fa-solid fa-location-dot text-sm"></i>
                        </div>
                        <h2 class="font-bold text-lg text-brand-dark">Alamat Pengiriman</h2>
                    </div>
                    <a href="{{ route('addresses.index') }}"
                        class="text-xs font-bold text-brand-primary hover:text-brand-dark transition-colors flex items-center gap-1.5 bg-brand-primary/5 px-3 py-1.5 rounded-lg">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Baru
                    </a>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($addresses as $addr)
                        <div class="group relative flex flex-col sm:flex-row sm:items-start gap-4 p-5 rounded-2xl border transition-all {{ $addr->is_default ? 'border-brand-primary/30 bg-brand-primary/[0.02]' : 'border-gray-100 bg-white hover:border-gray-200' }}">
                            <div class="w-10 h-10 rounded-xl {{ $addr->is_default ? 'bg-brand-primary/10 text-brand-primary' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-base"></i>
                            </div>
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-bold text-brand-dark">{{ $addr->label ?? $addr->receiver_name }}</p>
                                    @if($addr->is_default)
                                        <span class="px-2 py-0.5 bg-brand-primary text-white text-[9px] font-bold rounded-md uppercase tracking-wider">Utama</span>
                                    @endif
                                </div>
                                <p class="text-xs font-medium text-gray-500">{{ $addr->receiver_name }} <span class="text-gray-300 mx-1">|</span> {{ $addr->phone }}</p>
                                <p class="text-xs text-gray-400 leading-relaxed pt-1">
                                    {{ $addr->address }}, {{ $addr->district }}, {{ $addr->city }}, {{ $addr->province }} {{ $addr->postal_code }}
                                </p>
                            </div>
                            <div class="sm:self-center">
                                <a href="{{ route('addresses.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-50 hover:bg-gray-100 rounded-xl text-xs font-bold text-gray-600 transition-colors whitespace-nowrap w-full sm:w-auto">Kelola</a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                                <i class="fa-solid fa-location-dot text-3xl"></i>
                            </div>
                            <p class="text-sm font-bold text-brand-dark">Belum Ada Alamat Tersimpan</p>
                            <p class="text-xs text-gray-400 max-w-xs mx-auto mt-1 mb-5">Tambahkan alamat pengiriman untuk mempermudah proses checkout pesanan Anda.</p>
                            <a href="{{ route('addresses.index') }}"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-primary text-white text-xs font-bold rounded-xl hover:bg-brand-dark transition-all shadow-sm">
                                <i class="fa-solid fa-plus text-[10px]"></i> Tambah Alamat Pertama
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="tab-recent-orders" class="tab-panel bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-500">
                            <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                        </div>
                        <h2 class="font-bold text-lg text-brand-dark">Pesanan Terbaru</h2>
                    </div>
                    <a href="{{ route('order.history') }}" class="text-xs font-bold text-brand-primary hover:text-brand-dark transition-colors flex items-center gap-1">
                        Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @php
                        $statusConfig = [
                            'pending'    => ['bg'=>'bg-amber-50 text-amber-700 border-amber-100','label'=>'Belum Bayar','icon'=>'fa-clock'],
                            'confirmed'  => ['bg'=>'bg-blue-50 text-blue-700 border-blue-100','label'=>'Dikonfirmasi','icon'=>'fa-circle-check'],
                            'processing' => ['bg'=>'bg-indigo-50 text-indigo-700 border-indigo-100','label'=>'Dikemas','icon'=>'fa-box'],
                            'shipped'    => ['bg'=>'bg-cyan-50 text-cyan-700 border-cyan-100','label'=>'Dikirim','icon'=>'fa-truck'],
                            'delivered'  => ['bg'=>'bg-green-50 text-green-700 border-green-100','label'=>'Selesai','icon'=>'fa-check-double'],
                            'cancelled'  => ['bg'=>'bg-red-50 text-red-700 border-red-100','label'=>'Dibatalkan','icon'=>'fa-ban'],
                        ];
                    @endphp
                    @forelse($recentOrders as $order)
                        @php
                            $sc = $statusConfig[$order->status] ?? ['bg'=>'bg-gray-50 text-gray-600 border-gray-200','label'=>$order->status,'icon'=>'fa-circle-info'];
                            $firstItem = $order->items->first();
                            $img = $firstItem?->product?->images?->where('is_primary',true)->first() ?? $firstItem?->product?->images?->first();
                        @endphp
                        <a href="{{ route('order.history.show', $order->order_number) }}"
                            class="flex flex-col sm:flex-row sm:items-center gap-4 px-6 py-5 hover:bg-gray-50/60 transition-colors group">
                            <div class="w-14 h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                                <img src="{{ $img ? asset('storage/'.$img->image_url) : 'https://via.placeholder.com/100' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-mono font-bold text-gray-400">#{{ $order->order_number }}</span>
                                    <span class="text-gray-300 text-xs">•</span>
                                    <span class="text-xs text-gray-400">{{ $order->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <p class="text-sm font-bold text-brand-dark truncate group-hover:text-brand-primary transition-colors">
                                    {{ $firstItem?->product_name ?? 'Produk' }}
                                </p>
                                @if($order->items->count() > 1)
                                    <p class="text-xs text-gray-400 mt-0.5">dan {{ $order->items->count() - 1 }} produk lainnya</p>
                                @endif
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 pt-3 sm:pt-0 border-t sm:border-0 border-gray-50">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $sc['bg'] }}">
                                    <i class="fa-solid {{ $sc['icon'] }}"></i>
                                    {{ $sc['label'] }}
                                </span>
                                <p class="text-base font-black text-brand-dark">Rp{{ number_format($order->total,0,',','.') }}</p>
                            </div>
                            <div class="hidden sm:block pl-2">
                                <i class="fa-solid fa-chevron-right text-xs text-gray-300 group-hover:text-brand-primary group-hover:translate-x-0.5 transition-all"></i>
                            </div>
                        </a>
                    @empty
                        <div class="py-12 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                                <i class="fa-solid fa-bag-shopping text-3xl"></i>
                            </div>
                            <p class="text-sm font-bold text-brand-dark">Belum Ada Transaksi</p>
                            <p class="text-xs text-gray-400 max-w-xs mx-auto mt-1 mb-5">Anda belum melakukan pemesanan apa pun baru-baru ini.</p>
                            <a href="/collections"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-primary text-white text-xs font-bold rounded-xl hover:bg-brand-dark transition-all shadow-sm">
                                Mulai Belanja
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="tab-security" class="tab-panel bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </div>
                    <h2 class="font-bold text-lg text-brand-dark">Keamanan Akun</h2>
                </div>
                <div class="p-6 sm:p-8 space-y-8">
                    <div>
                        <div class="mb-5">
                            <h3 class="text-sm font-bold text-brand-dark">Ubah Password</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Gunakan kombinasi password yang kuat untuk menjaga keamanan akun Anda.</p>
                        </div>

                        @if(session('password_success'))
                            <div class="mb-5 flex items-center gap-3 rounded-xl bg-green-50 border border-green-100 px-4 py-3.5 text-sm font-semibold text-green-700">
                                <i class="fa-solid fa-circle-check text-base"></i>
                                {{ session('password_success') }}
                            </div>
                        @endif
                        @if(session('password_error'))
                            <div class="mb-5 flex items-center gap-3 rounded-xl bg-red-50 border border-red-100 px-4 py-3.5 text-sm font-semibold text-red-600">
                                <i class="fa-solid fa-circle-exclamation text-base"></i>
                                {{ session('password_error') }}
                            </div>
                        @endif

                        <form action="{{ route('customer.password.update') }}" method="POST" class="space-y-4">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Password Saat Ini <span class="text-red-400">*</span></label>
                                <input type="password" name="current_password" required autocomplete="current-password"
                                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition text-sm font-medium">
                            </div>
                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Password Baru <span class="text-red-400">*</span></label>
                                    <input type="password" name="password" id="new_password" required autocomplete="new-password" minlength="8"
                                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition text-sm font-medium">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Konfirmasi Password <span class="text-red-400">*</span></label>
                                    <input type="password" name="password_confirmation" id="confirm_password" required autocomplete="new-password" minlength="8"
                                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition text-sm font-medium">
                                </div>
                            </div>
                            <p id="pw-match-hint" class="text-xs font-medium text-gray-400 hidden"></p>
                            <div class="flex justify-end pt-4 border-t border-gray-100">
                                <button type="submit"
                                    class="w-full sm:w-auto px-8 py-3 bg-brand-dark hover:bg-brand-primary text-white text-sm font-bold rounded-xl transition-all shadow-sm active:scale-[0.98]">
                                    Perbarui Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-gray-50/70 border border-gray-100 rounded-2xl p-5 space-y-3.5">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Detail Sederhana Akun</h4>
                        <div class="grid gap-2.5 text-sm">
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-gray-500">Alamat Email</span>
                                <span class="font-semibold text-brand-dark">{{ $profileUser->email }}</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-gray-500">Tanggal Terdaftar</span>
                                <span class="font-semibold text-brand-dark">{{ $profileUser->created_at->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5">
                                <span class="text-gray-500">Tipe Akun</span>
                                <span class="px-2 py-0.5 bg-gray-200/60 text-gray-700 text-xs font-bold rounded-md capitalize">{{ $profileUser->role }}</span>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>

  @if($voucherCount > 0)
<a href="{{ route('promo.index') }}"
    class="relative flex items-center gap-4 p-5 overflow-hidden transition-all duration-300 rounded-2xl bg-gradient-to-r from-brand-primary to-brand-dark hover:shadow-xl hover:shadow-brand-primary/10 group">

    {{-- Efek Kilau Halus di Background --}}
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_80%_50%,#fff,transparent_60%)]"></div>


    {{-- Ikon Tiket --}}
    <div class="relative flex items-center justify-center w-12 h-12 border rounded-xl bg-white/10 backdrop-blur-md border-white/20 shadow-inner flex-shrink-0 transition-transform duration-300 group-hover:scale-105">
        <i class="fa-solid fa-ticket text-white text-lg"></i>
    </div>

    {{-- Konten Teks --}}
    <div class="relative flex-1 min-w-0 space-y-0.5">
        <div class="flex items-center gap-2">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
            </span>
            <p class="text-sm font-extrabold text-white sm:text-base tracking-wide">
                {{ $voucherCount }} Voucher Belanja Tersedia!
            </p>
        </div>
        <p class="text-xs text-white/70 truncate font-medium">
            Makin hemat dengan klaim potongan kupon eksklusif {{ $storeName }} sekarang juga.
        </p>
    </div>

    {{-- Tombol Panah Kanan --}}
    <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-white/10 text-white text-xs transition-all duration-300 ml-2 flex-shrink-0 group-hover:bg-white group-hover:text-brand-dark group-hover:translate-x-1">
        <i class="fa-solid fa-arrow-right"></i>
    </div>
</a>
@endif

</div>
</section>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
@endsection

@push('scripts')
<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.nav-item').forEach(b => {
            b.classList.remove('active');
            b.classList.add('bg-white', 'text-brand-dark', 'hover:bg-gray-50');
            var icon = b.querySelector('.nav-icon');
            if (icon) icon.classList.add('text-gray-400');
        });

        var panel = document.getElementById('tab-' + tab);
        if (panel) panel.classList.add('active');

        var btn = document.querySelector('[data-tab="' + tab + '"]');
        if (btn) {
            btn.classList.add('active');
            btn.classList.remove('bg-white', 'text-brand-dark', 'hover:bg-gray-50');
            var icon = btn.querySelector('.nav-icon');
            if (icon) icon.classList.remove('text-gray-400');
        }

        history.replaceState(null, '', '#' + tab);
    }

    (function () {
        var hash = location.hash.replace('#', '');
        var valid = ['edit-profile', 'addresses', 'recent-orders', 'security'];
        if (valid.includes(hash)) switchTab(hash);

        @if(session('password_success') || session('password_error'))
            switchTab('security');
        @endif
        @if(session('success') || $errors->any())
            switchTab('edit-profile');
        @endif
    })();
</script>
@endpush
