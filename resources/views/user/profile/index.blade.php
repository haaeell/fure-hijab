@extends('layouts.customer')

@section('title', 'Profil Saya - FURE')

@section('content')
    <section class="py-12 bg-[#FBFBFE] min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">

            <div
                class="mb-10 flex flex-col md:flex-row items-center gap-6 bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
                <div class="relative">
                    <div
                        class="w-24 h-24 bg-brand-primary/10 rounded-3xl flex items-center justify-center border-2 border-brand-primary/20">
                        <i class="fa-solid fa-user text-4xl text-brand-primary"></i>
                    </div>
                    <button
                        class="absolute -bottom-2 -right-2 bg-white shadow-md w-8 h-8 rounded-full flex items-center justify-center border border-gray-100 hover:text-brand-primary transition">
                        <i class="fa-solid fa-camera text-xs"></i>
                    </button>
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-2xl font-bold text-brand-dark">{{ $profileUser->name }}</h1>
                    <p class="text-gray-400 text-sm">{{ $profileUser->email }}</p>
                    <div
                        class="mt-2 inline-flex items-center px-3 py-1 bg-soft-mint text-brand-dark text-[10px] font-bold uppercase tracking-wider rounded-lg">
                        Member {{ $profileUser->role }}
                    </div>
                </div>
                <div class="md:ml-auto flex gap-3">
                    <a href="/order-history"
                        class="px-5 py-2.5 bg-white border border-gray-200 text-brand-dark text-xs font-bold rounded-xl hover:bg-gray-50 transition">
                        Riwayat Pesanan
                    </a>
                </div>
            </div>

            <div class="my-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-6 bg-soft-blue rounded-[24px] border border-blue-100 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-500 shadow-sm">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Total Pesanan</p>
                        <p class="text-lg font-bold text-blue-900">{{ $orderCount  }}</p>
                    </div>
                </div>
                <div class="p-6 bg-brand-primary/10 rounded-[24px] border border-brand-primary/20 flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-brand-primary shadow-sm">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-brand-primary uppercase tracking-wider">Kupon Tersedia
                        </p>
                        <p class="text-lg font-bold text-brand-dark">{{ $voucherCount }} Voucher</p>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-12 gap-8">
                <div class="md:col-span-4 space-y-3">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-4 mb-4">Menu Utama</h3>

                    <a href="#personal-info"
                        class="flex items-center gap-4 p-4 bg-brand-primary text-white rounded-2xl shadow-lg shadow-brand-primary/20 transition-all">
                        <i class="fa-solid fa-id-card w-5"></i>
                        <span class="font-semibold text-sm">Informasi Pribadi</span>
                        <i class="fa-solid fa-chevron-right ml-auto text-[10px]"></i>
                    </a>

                    <a href="/cart"
                        class="flex items-center gap-4 p-4 bg-white text-brand-dark hover:bg-soft-mint rounded-2xl border border-gray-50 transition-all group">
                        <i class="fa-solid fa-shopping-bag w-5 text-gray-400 group-hover:text-brand-primary"></i>
                        <span class="font-semibold text-sm">Keranjang Saya</span>
                        <i class="fa-solid fa-chevron-right ml-auto text-[10px] text-gray-300"></i>
                    </a>

                    <a href="/order-history"
                        class="flex items-center gap-4 p-4 bg-white text-brand-dark hover:bg-soft-mint rounded-2xl border border-gray-100 transition-all group">
                        <i class="fa-solid fa-box w-5 text-gray-400 group-hover:text-brand-primary"></i>
                        <span class="font-semibold text-sm">Riwayat Pesanan</span>
                        <i class="fa-solid fa-chevron-right ml-auto text-[10px] text-gray-300"></i>
                    </a>
                    <hr class="my-6 border-gray-100">

                    <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="w-full flex items-center gap-4 p-4 bg-red-50 text-red-600 rounded-2xl hover:bg-red-600 hover:text-white transition-all group">
                        <i class="fa-solid fa-power-off w-5"></i>
                        <span class="font-bold text-sm">Keluar Akun</span>
                    </button>
                </div>

                <div class="md:col-span-8">
                    <div class="bg-white rounded-[32px] border border-gray-100 p-8 shadow-sm">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-xl font-bold text-brand-dark">Edit Profil</h2>
                            <i class="fa-solid fa-user-pen text-brand-primary/30 text-2xl"></i>
                        </div>

                        @if(session('success'))
                            <div class="mb-6 flex items-center gap-3 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase ml-1">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ $profileUser->name }}"
                                        class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition text-sm font-medium">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase ml-1">Alamat Email</label>
                                    <input type="email" value="{{ $profileUser->email }}" disabled
                                        class="w-full px-5 py-3 bg-gray-100 border border-gray-200 rounded-2xl text-gray-400 text-sm font-medium cursor-not-allowed">
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase ml-1">Nomor WhatsApp</label>
                                    <input type="tel" name="phone" value="{{ $profileUser->phone }}" placeholder="6281234..."
                                        class="w-full px-5 py-3 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition text-sm font-medium">
                                </div>
                            </div>

                            <div class="pt-6">
                                <button type="submit"
                                    class="w-full md:w-auto px-10 py-4 bg-brand-dark text-white font-bold rounded-2xl hover:bg-brand-primary shadow-lg shadow-brand-dark/10 transition-all active:scale-95">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
