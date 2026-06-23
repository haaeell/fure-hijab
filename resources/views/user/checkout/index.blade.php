@extends('layouts.customer')

@section('title', 'Checkout - FURE')

@section('content')
    <section class="mobile-action-safe-space bg-[#F8FBF8] min-h-screen px-4 sm:px-6 lg:px-8 pt-28 pb-12">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('cart.index') }}"
                        class="w-11 h-11 bg-white border border-gray-100 rounded-2xl flex items-center justify-center text-brand-dark shadow-sm hover:bg-brand-primary hover:text-white transition-all">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <p class="text-[10px] font-black text-brand-primary uppercase tracking-[0.25em]">Secure checkout</p>
                        <h1 class="text-2xl md:text-3xl font-extrabold text-brand-dark tracking-tight">Checkout</h1>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm grid grid-cols-3 gap-3 text-center min-w-full sm:min-w-[420px] lg:min-w-[480px]">
                    <div class="flex flex-col items-center gap-1">
                        <span class="w-8 h-8 rounded-full bg-brand-primary text-white flex items-center justify-center text-xs font-black">1</span>
                        <span class="text-[10px] font-black text-brand-dark uppercase tracking-wider">Alamat</span>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <span class="w-8 h-8 rounded-full bg-soft-mint text-brand-primary flex items-center justify-center text-xs font-black">2</span>
                        <span class="text-[10px] font-black text-brand-dark uppercase tracking-wider">Pengiriman</span>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <span class="w-8 h-8 rounded-full bg-soft-mint text-brand-primary flex items-center justify-center text-xs font-black">3</span>
                        <span class="text-[10px] font-black text-brand-dark uppercase tracking-wider">Bayar</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_390px] gap-6 items-start">
                    <div class="space-y-5">
                        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="h-1.5 bg-gradient-to-r from-brand-primary via-brand-secondary to-brand-dark"></div>
                            <div class="p-5 md:p-6">
                                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="w-11 h-11 rounded-2xl bg-soft-mint flex items-center justify-center text-brand-primary">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-extrabold text-brand-dark">Alamat Pengiriman</h2>
                                            <p class="text-xs text-gray-400 mt-1">Pastikan alamat dan nomor penerima sudah benar.</p>
                                        </div>
                                    </div>

                                    <button type="button" onclick="toggleAddressModal()"
                                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-brand-primary/20 text-brand-primary text-xs font-black hover:bg-soft-mint transition-all">
                                        <i class="fa-solid {{ $address ? 'fa-pen' : 'fa-plus' }}"></i>
                                        {{ $address ? 'Ganti Alamat' : 'Tambah Alamat' }}
                                    </button>
                                </div>

                                @if($address)
                                    <div class="mt-5 rounded-2xl border border-brand-primary/20 bg-soft-mint/20 p-5">
                                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                                    <span class="px-2.5 py-1 rounded-lg bg-brand-primary text-white text-[10px] font-black uppercase">{{ $address->label }}</span>
                                                    @if($address->biteship_area_id)
                                                        <span class="px-2.5 py-1 rounded-lg bg-white text-brand-primary text-[10px] font-black uppercase">Terverifikasi</span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-600 text-[10px] font-black uppercase">Manual</span>
                                                    @endif
                                                </div>
                                                <p class="font-extrabold text-brand-dark">{{ $address->receiver_name }} <span class="font-semibold text-gray-400">| {{ $address->phone }}</span></p>
                                                <p class="text-sm text-gray-600 leading-relaxed mt-2">
                                                    {{ collect([$address->address, $address->subdistrict, $address->district, $address->city, $address->province, $address->postal_code])->filter()->implode(', ') }}
                                                </p>
                                            </div>
                                        </div>
                                        <input type="hidden" name="address_id" value="{{ $address->id }}">
                                    </div>
                                @else
                                    <div class="mt-5 p-8 border-2 border-dashed border-gray-200 rounded-2xl text-center bg-gray-50/50">
                                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                                            <i class="fa-solid fa-location-dot text-xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-500 mb-4">Anda belum memiliki alamat pengiriman.</p>
                                        <button type="button" onclick="toggleAddressModal()"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary text-brand-dark font-black rounded-2xl text-sm transition-all active:scale-95">
                                            <i class="fa-solid fa-plus"></i> Tambah Alamat Baru
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 md:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-soft-mint flex items-center justify-center text-brand-primary">
                                        <i class="fa-solid fa-bag-shopping"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-extrabold text-brand-dark">Produk Dipesan</h2>
                                        <p class="text-xs text-gray-400">{{ $carts->count() }} item dalam pesanan</p>
                                    </div>
                                </div>
                            </div>

                            <div class="divide-y divide-gray-50">
                                @foreach($carts as $item)
                                    @php
                                        $primaryImage = $item->product->images->where('is_primary', true)->first();
                                        $itemSubtotal = $item->price * $item->qty;
                                    @endphp
                                    <div class="p-5 md:p-6 flex gap-4">
                                        <div class="w-16 h-20 md:w-20 md:h-24 rounded-2xl overflow-hidden flex-shrink-0 bg-gray-100">
                                            <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                                class="w-full h-full object-cover" alt="{{ $item->product->name }}">
                                        </div>
                                        <div class="flex-1 min-w-0 space-y-3">
                                            <div class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3">
                                                <div class="min-w-0">
                                                    <h3 class="font-extrabold text-brand-dark text-sm md:text-base truncate">{{ $item->product->name }}</h3>
                                                    @if($item->variant)
                                                        <p class="text-xs text-gray-400 mt-1">
                                                            @foreach($item->variant->attributes as $attr)
                                                                {{ $attr->attribute_value }}{{ !$loop->last ? ' | ' : '' }}
                                                            @endforeach
                                                        </p>
                                                    @endif
                                                    <p class="text-xs text-gray-500 mt-2">{{ $item->qty }} x Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="md:text-right">
                                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Subtotal</p>
                                                    <p class="font-extrabold text-brand-primary">Rp{{ number_format($itemSubtotal, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                            {{-- Catatan per produk --}}
                                            <div class="relative">
                                                <textarea
                                                    name="item_notes[{{ $item->id }}]"
                                                    rows="2"
                                                    maxlength="500"
                                                    placeholder="Catatan untuk produk ini (opsional)…"
                                                    class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50/70 px-4 py-3 text-xs text-brand-dark placeholder-gray-400 outline-none transition focus:border-brand-primary/50 focus:bg-white focus:ring-2 focus:ring-brand-primary/10"
                                                ></textarea>
                                                <i class="fa-regular fa-note-sticky absolute right-3 top-3 text-gray-300 text-xs pointer-events-none"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-5 md:p-6">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-soft-mint flex items-center justify-center text-brand-primary">
                                        <i class="fa-solid fa-truck-fast"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-extrabold text-brand-dark">Opsi Pengiriman</h2>
                                        <p class="text-xs text-gray-400">Pilih kurir dan layanan pengiriman di modal.</p>
                                    </div>
                                </div>

                                <button type="button" onclick="toggleShippingModal(true)"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-brand-primary text-white text-xs font-black uppercase tracking-wider transition-all active:scale-95">
                                    <i class="fa-solid fa-truck-fast"></i>
                                    Pilih Pengiriman
                                </button>
                            </div>

                            <div id="shipping-selected-card" class="mt-5 rounded-2xl border border-dashed border-gray-200 bg-gray-50/70 p-5">
                                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-[0.22em] text-gray-400">Belum dipilih</p>
                                        <p id="shipping-selected-title" class="mt-1 text-sm font-extrabold text-brand-dark">
                                            Pilih kurir untuk melihat ongkir otomatis.
                                        </p>
                                        <p id="shipping-selected-meta" class="mt-1 text-xs text-gray-400">
                                            Biaya pengiriman akan masuk ke ringkasan pembayaran.
                                        </p>
                                    </div>
                                    <button type="button" onclick="toggleShippingModal(true)"
                                        class="text-xs font-black text-brand-primary hover:text-brand-dark transition-colors">
                                        Ubah
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="courier_code" id="selected_courier_code">
                            <input type="hidden" name="courier_service" id="selected_courier_service">
                            <input type="hidden" name="shipping_cost" id="selected_shipping_cost">
                            <input type="hidden" name="shipping_etd" id="selected_shipping_etd">
                        </div>

                        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-5 md:p-6">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-500">
                                    <i class="fa-solid fa-ticket"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-extrabold text-brand-dark">Voucher / Kupon</h2>
                                    <p class="text-xs text-gray-400">Gunakan kode promo yang masih aktif.</p>
                                </div>
                            </div>

                            @if($appliedCoupon)
                                <div id="coupon-applied-box"
                                    class="flex items-center justify-between p-4 bg-purple-50 border border-purple-200 rounded-2xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-purple-600">
                                            <i class="fa-solid fa-tag"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-purple-700">{{ $appliedCoupon->code }}</p>
                                            <p class="text-xs text-purple-500">{{ $appliedCoupon->name }}</p>
                                            <p class="text-xs font-bold text-green-600 mt-0.5">Hemat Rp{{ number_format($discountAmount, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <button type="button" id="btn-remove-coupon"
                                        class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors px-3 py-1 rounded-lg hover:bg-red-50">
                                        <i class="fa-solid fa-xmark mr-1"></i> Hapus
                                    </button>
                                </div>
                                <input type="hidden" name="coupon_code" id="coupon_code_input" value="{{ $appliedCoupon->code }}">
                            @else
                                <div id="coupon-input-box" class="flex flex-col sm:flex-row gap-3">
                                    <div class="relative flex-1">
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                                            <i class="fa-solid fa-ticket text-sm"></i>
                                        </div>
                                        <input type="text" id="coupon_input" placeholder="Masukkan kode voucher"
                                            autocomplete="off" autocapitalize="characters"
                                            class="w-full pl-10 pr-4 py-3.5 rounded-2xl border border-gray-200 text-sm font-bold uppercase tracking-widest focus:outline-none focus:border-purple-400 transition-colors">
                                    </div>
                                    <button type="button" id="btn-apply-coupon"
                                        class="px-6 py-3.5 bg-purple-500 text-white font-black rounded-2xl text-sm hover:bg-purple-600 transition-all active:scale-95 whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed">
                                        Pakai
                                    </button>
                                </div>
                                <div id="coupon-message" class="mt-3 hidden"></div>
                                <input type="hidden" name="coupon_code" id="coupon_code_input" value="">
                            @endif
                        </div>

                        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-5 md:p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500">
                                    <i class="fa-solid fa-note-sticky"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-extrabold text-brand-dark">Catatan Pesanan</h2>
                                    <p class="text-xs text-gray-400">Opsional, untuk detail tambahan ke admin.</p>
                                </div>
                            </div>
                            <textarea name="notes" rows="3" placeholder="Contoh: mohon dikemas rapi untuk hadiah."
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors resize-none"></textarea>
                        </div>
                    </div>

                    <aside class="w-full lg:sticky lg:top-28">
                        <div class="bg-white rounded-[32px] shadow-xl shadow-brand-dark/5 border border-gray-100 overflow-hidden">
                            <div class="bg-brand-dark text-white p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] uppercase tracking-[0.25em] text-brand-primary font-black">Ringkasan</p>
                                        <h2 class="text-xl font-extrabold mt-1">Pembayaran</h2>
                                    </div>
                                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="max-h-56 overflow-y-auto no-scrollbar space-y-4 mb-6 pr-1">
                                    @foreach($carts as $item)
                                        @php $primaryImage = $item->product->images->where('is_primary', true)->first(); @endphp
                                        <div class="flex gap-3">
                                            <div class="w-12 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                                <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                                    class="w-full h-full object-cover" alt="{{ $item->product->name }}">
                                            </div>
                                            <div class="flex-grow min-w-0">
                                                <h4 class="text-xs font-extrabold text-brand-dark truncate">{{ $item->product->name }}</h4>
                                                @if($item->variant)
                                                    <p class="text-[10px] text-gray-400 mt-0.5 truncate">
                                                        @foreach($item->variant->attributes as $attr)
                                                            {{ $attr->attribute_value }}{{ !$loop->last ? ' | ' : '' }}
                                                        @endforeach
                                                    </p>
                                                @endif
                                                <p class="text-[10px] text-gray-500 mt-1">{{ $item->qty }} x Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="space-y-3 pt-5 border-t border-gray-100">
                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>Subtotal Produk</span>
                                        <span class="font-extrabold text-brand-dark">Rp{{ number_format($total_price, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="flex justify-between text-sm text-gray-500" id="discount-row"
                                        style="{{ $discountAmount > 0 ? '' : 'display:none' }}">
                                        <span class="text-purple-500 flex items-center gap-1">
                                            <i class="fa-solid fa-tag text-[10px]"></i> Diskon Voucher
                                        </span>
                                        <span class="font-extrabold text-purple-500" id="discount_display">-Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>Total Ongkos Kirim</span>
                                        <span class="font-extrabold text-brand-dark" id="shipping_cost_display">Rp0</span>
                                    </div>

                                    <div class="flex justify-between text-sm text-gray-500">
                                        <span>Berat Paket</span>
                                        <span class="font-extrabold text-brand-dark">{{ number_format($total_weight, 0, ',', '.') }} gram</span>
                                    </div>

                                    <div id="selected_service_info" class="hidden rounded-2xl bg-soft-mint/60 border border-brand-primary/10 p-3">
                                        <div class="flex justify-between gap-3 text-xs text-brand-dark">
                                            <span id="selected_service_label" class="font-bold">-</span>
                                            <span id="selected_service_etd" class="text-gray-500">-</span>
                                        </div>
                                    </div>

                                    <div id="cheapest-shipping-highlight" class="hidden rounded-2xl bg-green-50 border border-green-200 p-3">
                                        <p class="text-[10px] font-bold text-green-700 flex items-center gap-1">
                                            <i class="fa-solid fa-award text-green-500"></i>
                                            Ongkir termurah untuk rute ini.
                                        </p>
                                    </div>

                                    <div class="pt-5 mt-2 border-t border-gray-100">
                                        <div class="flex items-end justify-between gap-3">
                                            <div>
                                                <span class="text-[10px] uppercase tracking-widest text-gray-400 font-black">Total Pembayaran</span>
                                                <p class="text-2xl md:text-3xl font-extrabold text-brand-dark mt-1" id="grand_total_display">Rp{{ number_format($total_price, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" id="btn-submit" @if(!$address) disabled @endif
                                    class="desktop-only-action group mt-6 relative flex w-full py-4 bg-brand-primary text-brand-dark font-black rounded-2xl items-center justify-center gap-3 overflow-hidden transition-all active:scale-95 shadow-lg hover:shadow-brand-primary/30 hover:-translate-y-0.5 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:hover:shadow-none">
                                    <span class="relative z-10 uppercase tracking-tight">Buat Pesanan</span>
                                    <i class="fa-solid fa-arrow-right text-xs relative z-10 group-hover:translate-x-1 transition-transform"></i>
                                </button>

                                @if(!$address)
                                    <p class="text-center text-gray-400 text-[10px] mt-3">Tambahkan alamat pengiriman terlebih dahulu.</p>
                                @endif
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        </div>
    </section>

    <x-user.components.mobile-bottom-action-bar>
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400">Total</p>
                <p class="mt-0.5 truncate text-xl font-black text-brand-dark" id="mobile_grand_total_display">
                    Rp{{ number_format($total_price, 0, ',', '.') }}
                </p>
            </div>
            <button type="submit" form="checkoutForm" id="mobile-btn-submit" @if(!$address) disabled @endif
                class="flex min-h-[52px] min-w-[148px] items-center justify-center rounded-2xl bg-brand-primary px-5 text-sm font-black text-white shadow-lg shadow-brand-primary/25 transition active:scale-95 disabled:bg-gray-200 disabled:text-gray-400 disabled:shadow-none">
                Buat Pesanan
            </button>
        </div>
        @if(!$address)
            <p class="mt-2 text-center text-[10px] font-bold text-gray-400">Tambahkan alamat pengiriman terlebih dahulu.</p>
        @endif
    </x-user.components.mobile-bottom-action-bar>

    {{-- MODAL PENGIRIMAN --}}
    <div id="shippingModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="toggleShippingModal(false)"></div>
        <div class="absolute inset-x-0 bottom-0 mx-auto w-full max-w-3xl md:inset-x-auto md:bottom-auto md:left-1/2 md:top-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:p-4">
            <div class="flex max-h-[88vh] flex-col overflow-hidden rounded-t-[28px] bg-white shadow-2xl md:rounded-[28px]">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5 md:p-6">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.25em] text-brand-primary">Shipping Method</p>
                        <h3 class="mt-1 text-xl font-extrabold text-brand-dark">Pilih Pengiriman</h3>
                        <p class="mt-1 text-xs text-gray-400">Pilih satu atau beberapa kurir, lalu pilih layanan yang tersedia.</p>
                    </div>
                    <button type="button" onclick="toggleShippingModal(false)"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-gray-50 text-gray-400 transition hover:bg-brand-dark hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="overflow-y-auto p-5 md:p-6">
                    @php
                        $courierBadge = [
                            'jne'       => ['icon' => 'fa-box',        'color' => 'text-red-600',    'bg' => 'bg-red-50'],
                            'jnt'       => ['icon' => 'fa-truck-fast', 'color' => 'text-red-700',    'bg' => 'bg-red-50'],
                            'sicepat'   => ['icon' => 'fa-bolt',       'color' => 'text-orange-500', 'bg' => 'bg-orange-50'],
                            'anteraja'  => ['icon' => 'fa-paper-plane','color' => 'text-blue-600',   'bg' => 'bg-blue-50'],
                            'tiki'      => ['icon' => 'fa-cube',       'color' => 'text-purple-600', 'bg' => 'bg-purple-50'],
                            'pos'       => ['icon' => 'fa-envelope',   'color' => 'text-green-600',  'bg' => 'bg-green-50'],
                            'lion'      => ['icon' => 'fa-paw',        'color' => 'text-yellow-700', 'bg' => 'bg-yellow-50'],
                            'ninja'     => ['icon' => 'fa-star',       'color' => 'text-gray-700',   'bg' => 'bg-gray-100'],
                            'gosend'    => ['icon' => 'fa-motorcycle', 'color' => 'text-green-500',  'bg' => 'bg-green-50'],
                            'grab'      => ['icon' => 'fa-motorcycle', 'color' => 'text-green-600',  'bg' => 'bg-green-50'],
                            'wahana'    => ['icon' => 'fa-ship',       'color' => 'text-cyan-600',   'bg' => 'bg-cyan-50'],
                            'rpx'       => ['icon' => 'fa-globe',      'color' => 'text-indigo-600', 'bg' => 'bg-indigo-50'],
                            'idexpress' => ['icon' => 'fa-id-card',    'color' => 'text-red-600',    'bg' => 'bg-red-50'],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach($couriers as $code => $name)
                            @php $badge = $courierBadge[$code] ?? ['icon' => 'fa-truck', 'color' => 'text-brand-primary', 'bg' => 'bg-soft-mint']; @endphp
                            <label class="relative cursor-pointer">
                                <input type="checkbox" name="courier_check[]" value="{{ $code }}"
                                    class="courier-checkbox peer sr-only">
                                <div class="flex h-full min-h-[64px] flex-col items-center justify-center gap-1.5 rounded-2xl border border-gray-100 bg-gray-50/70 px-3 py-3 text-center transition-all peer-checked:border-brand-primary peer-checked:bg-soft-mint peer-checked:shadow-sm hover:border-brand-primary/40">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl {{ $badge['bg'] }} {{ $badge['color'] }}">
                                        <i class="fa-solid {{ $badge['icon'] }} text-sm"></i>
                                    </span>
                                    <span class="text-[10px] font-black uppercase tracking-wider text-gray-500 peer-checked:text-brand-dark">{{ $name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div id="shipping-services" class="mt-5 space-y-3">
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 py-8 text-center">
                            <i class="fa-solid fa-truck text-3xl text-gray-300 mb-3"></i>
                            <p class="text-sm font-semibold text-gray-500">Pilih kurir untuk melihat layanan pengiriman.</p>
                            <p class="mt-1 text-xs text-gray-400">Biaya pengiriman akan masuk ke ringkasan pembayaran.</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 bg-white p-4 md:p-5">
                    <button type="button" onclick="toggleShippingModal(false)"
                        class="w-full rounded-2xl bg-brand-dark py-3.5 text-sm font-black uppercase tracking-wider text-white transition hover:bg-brand-primary">
                        Simpan Pilihan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ALAMAT --}}
    <div id="addressModal" class="fixed inset-0 z-[99] hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="toggleAddressModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl p-4">
            <div class="bg-white rounded-[32px] shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <div class="p-8 overflow-y-auto no-scrollbar">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-brand-dark">Alamat Pengiriman</h3>
                        <button type="button" onclick="toggleAddressModal()" class="text-gray-400 hover:text-brand-dark">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <div class="flex gap-4 mb-6 border-b border-gray-100">
                        <button type="button" onclick="switchAddressTab('list')" id="tab-list"
                            class="pb-3 border-b-2 border-brand-primary text-brand-primary font-bold text-sm -mb-px">
                            Alamat Saya
                        </button>
                        <button type="button" onclick="switchAddressTab('new')" id="tab-new"
                            class="pb-3 border-b-2 border-transparent text-gray-400 font-bold text-sm -mb-px">
                            Tambah Baru
                        </button>
                    </div>

                    {{-- LIST ALAMAT --}}
                    <div id="address-list-section" class="space-y-3">
                        @forelse($addresses as $item)
                            <form action="{{ route('checkout.set-address') }}" method="POST">
                                @csrf
                                <input type="hidden" name="address_id" value="{{ $item->id }}">
                                <button type="submit"
                                    class="w-full text-left p-4 border-2 {{ $address && $address->id == $item->id ? 'border-brand-primary bg-soft-mint/10' : 'border-gray-100' }} rounded-2xl hover:border-brand-primary transition-all">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1 min-w-0">
                                            <span
                                                class="text-[10px] font-bold uppercase px-2 py-0.5 bg-gray-100 rounded text-gray-500 mb-2 inline-block">
                                                {{ $item->label }}
                                            </span>
                                            <p class="font-bold text-brand-dark text-sm">
                                                {{ $item->receiver_name }}
                                                <span class="font-normal text-gray-400">| {{ $item->phone }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $item->address }}, {{ $item->city }}</p>
                                        </div>
                                        @if($address && $address->id == $item->id)
                                            <i class="fa-solid fa-circle-check text-brand-primary ml-3 flex-shrink-0"></i>
                                        @endif
                                    </div>
                                </button>
                            </form>
                        @empty
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-location-dot text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-400 text-sm">Belum ada alamat tersimpan.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- FORM TAMBAH ALAMAT BARU --}}
                    <div id="address-new-section" class="hidden">
                        <form action="{{ route('addresses.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="is_default" value="1">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-1 block">Label Alamat</label>
                                    <select name="label"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors bg-white"
                                        required>
                                        <option value="" disabled selected>Pilih label...</option>
                                        <option value="Rumah"><i class="fa-solid fa-home"></i> Rumah</option>
                                        <option value="Kantor"><i class="fa-solid fa-building"></i> Kantor</option>
                                        <option value="Kost"><i class="fa-solid fa-hotel"></i> Kost</option>
                                        <option value="Lainnya"><i class="fa-solid fa-location-dot"></i> Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-1 block">Nama Penerima</label>
                                    <input type="text" name="receiver_name" placeholder="Nama lengkap penerima"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors"
                                        required>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-1 block">Nomor WhatsApp</label>
                                    <input type="tel" name="phone" placeholder="08xxxxxxxxxx"
                                        inputmode="tel" pattern="[0-9+\-\s]*"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors"
                                        required>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-1 block">Kode Pos</label>
                                    <input type="text" name="postal_code" id="new_postal_code" placeholder="Terisi otomatis"
                                        readonly
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-500 cursor-not-allowed focus:outline-none transition-colors">
                                </div>
                            </div>

                            <div class="relative">
                                <label class="text-xs font-bold text-gray-500 mb-1 block">
                                    Cari Area Pengiriman
                                    <span class="ml-1 font-normal text-gray-400">— ketik nama kecamatan</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                                    </div>
                                    <input type="text" id="destination_search" placeholder="Contoh: Coblong, Buah Batu, Cilandak..."
                                        autocomplete="off"
                                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors">
                                </div>
                                <div id="destination_results"
                                    class="hidden absolute z-30 mt-2 w-full bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden"></div>
                                <div id="dest_preview" class="hidden mt-2 rounded-xl bg-soft-mint/40 border border-brand-primary/10 px-4 py-3">
                                    <p id="dest_preview_label" class="text-xs font-black text-brand-dark">Lokasi valid</p>
                                    <p id="dest_preview_detail" class="text-[11px] text-gray-500 mt-0.5"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-1 block">Provinsi</label>
                                    <input type="text" name="province" id="dest_province"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors"
                                        required>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-1 block">Kota/Kabupaten</label>
                                    <input type="text" name="city" id="dest_city"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors"
                                        required>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-1 block">Kecamatan</label>
                                    <input type="text" name="district" id="dest_district"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors"
                                        required>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 mb-1 block">Kelurahan/Desa</label>
                                    <input type="text" name="subdistrict" id="dest_subdistrict"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors"
                                        required>
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-bold text-gray-500 mb-1 block">
                                    Titik Lokasi di Peta
                                    <span class="text-[10px] font-normal text-gray-400 ml-1">(opsional, geser pin untuk
                                        akurasi)</span>
                                </label>
                                <div class="rounded-2xl overflow-hidden border border-gray-200 shadow-sm">
                                    <div id="map" class="w-full h-56"></div>
                                </div>
                                <div class="flex gap-3 mt-2">
                                    <div class="flex-1">
                                        <input type="text" id="lat_display" placeholder="Latitude"
                                            class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-500 focus:outline-none focus:border-brand-primary bg-gray-50"
                                            readonly>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" id="lng_display" placeholder="Longitude"
                                            class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-500 focus:outline-none focus:border-brand-primary bg-gray-50"
                                            readonly>
                                    </div>
                                    <button type="button" id="btn-my-location"
                                        class="px-3 py-2 bg-soft-mint text-brand-primary rounded-xl text-xs font-bold hover:bg-brand-primary hover:text-white transition-all whitespace-nowrap">
                                        <i class="fa-solid fa-location-crosshairs mr-1"></i> Lokasiku
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="latitude" id="dest_latitude">
                            <input type="hidden" name="longitude" id="dest_longitude">
                            <input type="hidden" name="biteship_area_id" id="biteship_area_id">

                            <div>
                                <label class="text-xs font-bold text-gray-500 mb-1 block">Detail Alamat Lengkap</label>
                                <textarea name="address" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, patokan..."
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors resize-none"
                                    required></textarea>
                            </div>

                            <button type="submit" id="btn-save-address"
                                class="w-full py-4 bg-brand-primary text-brand-dark font-black rounded-xl shadow-lg
                                                                                                                                                                   transition-all active:scale-95
                                                                                                                                                                   disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100">
                                <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan & Gunakan Alamat
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            // ─── STATE ──────────────────────────────────────────────────────────
            const subtotal = {{ $total_price }};
            const totalWeight = {{ $total_weight }};
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            let isCheckingOngkir = false;
            let currentServices = [];
            let currentDiscount = {{ $discountAmount ?? 0 }};
            let currentShipping = 0;

            // ─── HELPERS ────────────────────────────────────────────────────────

            function formatRupiah(amount) {
                return 'Rp' + new Intl.NumberFormat('id-ID').format(amount);
            }

            function updateTotals() {
                const grandTotal = subtotal - currentDiscount + currentShipping;
                $('#shipping_cost_display').text(formatRupiah(currentShipping));
                $('#grand_total_display').text(formatRupiah(grandTotal));
                $('#mobile_grand_total_display').text(formatRupiah(grandTotal));
            }

            function showShippingMessage(type, message) {
                const colors = {
                    error: 'bg-red-50 border-red-200 text-red-700',
                    warning: 'bg-amber-50 border-amber-200 text-amber-700',
                };
                const icon = type === 'error' ? 'xmark-circle' : 'triangle-exclamation';
                $('#shipping-services').html(`
                                                <div class="p-6 border-2 rounded-2xl ${colors[type]} flex items-center gap-3">
                                                    <i class="fa-solid fa-${icon}"></i>
                                                    <p class="text-sm">${message}</p>
                                                </div>
                                            `);
            }

            function showCouponMessage(type, text) {
                const styles = {
                    success: 'bg-green-50 border-green-200 text-green-700',
                    error: 'bg-red-50 border-red-200 text-red-700',
                };
                const icon = type === 'success' ? 'circle-check' : 'circle-xmark';
                $('#coupon-message').removeClass('hidden').html(`
                                                <div class="flex items-center gap-2 p-3 border-2 rounded-xl text-xs font-bold ${styles[type]}">
                                                    <i class="fa-solid fa-${icon}"></i>
                                                    ${text}
                                                </div>
                                            `);
            }

            function showLoading(message) {
                $('#shipping-services').html(`
                                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                                    <div class="w-12 h-12 border-4 border-brand-primary/20 border-t-brand-primary rounded-full animate-spin mb-4"></div>
                                                    <p class="text-sm text-gray-600">${message}</p>
                                                </div>
                                            `);
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

            // ─── ADDRESS MODAL ───────────────────────────────────────────────────

            function syncBottomBar() {
                const anyModalOpen = !$('#addressModal').hasClass('hidden') || !$('#shippingModal').hasClass('hidden');
                $('.mobile-bottom-action-bar').toggleClass('hidden', anyModalOpen);
            }

            window.toggleAddressModal = function () {
                $('#addressModal').toggleClass('hidden');
                $('body').toggleClass('overflow-hidden');
                syncBottomBar();
            };

            window.toggleShippingModal = function (forceOpen) {
                const shouldOpen = typeof forceOpen === 'boolean'
                    ? forceOpen
                    : $('#shippingModal').hasClass('hidden');

                $('#shippingModal').toggleClass('hidden', !shouldOpen);
                $('body').toggleClass('overflow-hidden', shouldOpen || !$('#addressModal').hasClass('hidden'));
                syncBottomBar();
            };

            window.switchAddressTab = function (tab) {
                const isNew = tab === 'new';
                $('#address-list-section').toggleClass('hidden', isNew);
                $('#address-new-section').toggleClass('hidden', !isNew);
                $('#tab-new').toggleClass('border-brand-primary text-brand-primary', isNew)
                    .toggleClass('border-transparent text-gray-400', !isNew);
                $('#tab-list').toggleClass('border-brand-primary text-brand-primary', !isNew)
                    .toggleClass('border-transparent text-gray-400', isNew);
            };

            // ─── DESTINATION SEARCH ──────────────────────────────────────────────

            let searchTimer;
            let searchXhr    = null; // referensi XHR in-flight
            let activeQuery  = '';   // query yang sedang ditampilkan hasilnya

            $('#destination_search').on('input', function () {
                const query = $(this).val().trim();
                clearTimeout(searchTimer);

                // Batalkan request lama SEKETIKA agar tidak overwrite hasil baru
                if (searchXhr) {
                    searchXhr.abort();
                    searchXhr = null;
                }

                if (query.length < 3) {
                    $('#destination_results').addClass('hidden').empty();
                    return;
                }

                searchTimer = setTimeout(function () {
                    activeQuery = query; // catat query yang sedang diproses

                    $('#destination_results').removeClass('hidden').html(`
                        <div class="px-4 py-3 text-xs text-gray-400 flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Mencari lokasi...
                        </div>
                    `);

                    searchXhr = $.ajax({
                        url: "{{ route('checkout.search-destination') }}",
                        method: 'GET',
                        data: { search: query },
                        success: function (results) {
                            // Abaikan respons jika user sudah mengetik query berbeda
                            if (query !== activeQuery) return;

                            const $list = $('#destination_results').empty();

                            if (!results?.length) {
                                $list.html('<div class="px-4 py-3 text-xs text-gray-400">Lokasi tidak ditemukan.</div>');
                                return;
                            }

                            results.forEach(function (item) {
                                // Simpan data asli langsung di elemen — tidak di-escape sebagai HTML entity
                                // agar jQuery .data() membaca nilai yang benar
                                const $row = $('<div>', {
                                    class: 'px-4 py-3 hover:bg-soft-mint/20 cursor-pointer border-b border-gray-50 last:border-0 transition-colors',
                                }).html(
                                    '<p class="font-bold text-brand-dark text-xs">' + escapeHtml(item.label || '-') + '</p>'
                                    + '<p class="text-[10px] text-gray-400 mt-0.5">'
                                    + (item.postal_code ? 'Kode Pos: ' + escapeHtml(item.postal_code) : 'Kode pos tidak tersedia')
                                    + '</p>'
                                );

                                // Set data via .data() langsung — tidak lewat atribut HTML
                                // Ini mencegah HTML-entity decoding yang bisa mengubah nilai
                                $row.data('id',          item.id          ?? '');
                                $row.data('label',       item.label       ?? '');
                                $row.data('province',    item.province    ?? '');
                                $row.data('city',        item.city        ?? '');
                                $row.data('district',    item.district    ?? '');
                                $row.data('subdistrict', item.subdistrict ?? '');
                                $row.data('postal-code', item.postal_code ?? '');
                                $row.data('latitude',    item.latitude    ?? '');
                                $row.data('longitude',   item.longitude   ?? '');

                                $list.append($row);
                            });
                        },
                        error: function (xhr) {
                            if (xhr.statusText === 'abort') return; // diabaikan jika sengaja di-abort
                            $('#destination_results').html(`<div class="px-4 py-3 text-xs text-red-500">${xhr.responseJSON?.message || 'Gagal mencari lokasi.'}</div>`);
                        },
                        complete: function () {
                            searchXhr = null;
                        },
                    });
                }, 400);
            });

            $(document).on('click', '#destination_results > div', function () {
                const $el = $(this);

                // Baca dari jQuery .data() — nilainya di-set langsung via .data(), bukan dari atribut HTML
                const province    = $el.data('province')    || '';
                const city        = $el.data('city')        || '';
                const district    = $el.data('district')    || '';
                const subdistrict = $el.data('subdistrict') || '';
                const postalCode  = $el.data('postal-code') || '';
                const areaId      = $el.data('id')          || '';
                const label       = $el.data('label')       || '';
                const lat         = $el.data('latitude')    || '';
                const lng         = $el.data('longitude')   || '';

                // Validasi — jangan proses jika areaId kosong (klik pada elemen loading/error)
                if (!areaId) return;

                $('#dest_province').val(province);
                $('#dest_city').val(city);
                $('#dest_district').val(district);
                $('#dest_subdistrict').val(subdistrict);
                $('#new_postal_code').val(postalCode);
                $('#biteship_area_id').val(areaId);

                $('#destination_search').val(label);
                $('#destination_results').addClass('hidden').empty();
                activeQuery = ''; // reset agar respons in-flight lama tidak overwrite

                $('#dest_preview_label').text('Lokasi ditemukan');
                $('#dest_preview_detail').text(label + (postalCode ? ' — Kode Pos: ' + postalCode : ''));
                $('#dest_preview').removeClass('hidden');

                if (lat && lng) {

                    if (typeof setCoords === 'function') {
                        setCoords(lat, lng);
                    }

                    if (typeof map !== 'undefined' && map && typeof marker !== 'undefined' && marker) {
                        map.setView([lat, lng], 15);
                        marker.setLatLng([lat, lng]);
                    }
                }

                $('#btn-save-address').prop('disabled', false);
            });

            // Tutup dropdown saat klik di luar
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#destination_search, #destination_results').length) {
                    $('#destination_results').addClass('hidden');
                }
            });

            // ─── COURIER LOGO MAPPING ────────────────────────────────────────────
            const courierMeta = {
                'jne':       { icon: 'fa-box',         color: '#DC2626', bg: '#FEE2E2' },
                'jnt':       { icon: 'fa-truck-fast',  color: '#B91C1C', bg: '#FEE2E2' },
                'sicepat':   { icon: 'fa-bolt',        color: '#D97706', bg: '#FEF3C7' },
                'anteraja':  { icon: 'fa-paper-plane', color: '#2563EB', bg: '#DBEAFE' },
                'tiki':      { icon: 'fa-cube',        color: '#7C3AED', bg: '#EDE9FE' },
                'pos':       { icon: 'fa-envelope',    color: '#059669', bg: '#D1FAE5' },
                'lion':      { icon: 'fa-paw',         color: '#B45309', bg: '#FEF3C7' },
                'ninja':     { icon: 'fa-star',        color: '#374151', bg: '#F3F4F6' },
                'gosend':    { icon: 'fa-motorcycle',  color: '#16A34A', bg: '#DCFCE7' },
                'grab':      { icon: 'fa-motorcycle',  color: '#15803D', bg: '#DCFCE7' },
                'wahana':    { icon: 'fa-ship',        color: '#0891B2', bg: '#CFFAFE' },
                'rpx':       { icon: 'fa-globe',       color: '#4F46E5', bg: '#E0E7FF' },
                'idexpress': { icon: 'fa-id-card',     color: '#DC2626', bg: '#FEE2E2' },
            };

            function getCourierBadge(code) {
                const m = courierMeta[String(code).toLowerCase()] || { icon: 'fa-truck', color: '#A78B6F', bg: '#F5EDE4' };
                return `<span class="inline-flex h-8 w-8 items-center justify-center rounded-xl shrink-0" style="background:${m.bg};color:${m.color}">
                            <i class="fa-solid ${m.icon} text-sm"></i>
                        </span>`;
            }

            // ─── SHIPPING ────────────────────────────────────────────────────────

            $('.courier-checkbox').on('change', function () {
                const selected = $('.courier-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                selected.length > 0 ? instantCheckOngkir(selected) : resetShippingDisplay();
            });

            function instantCheckOngkir(couriers) {
                if (isCheckingOngkir) return;
                isCheckingOngkir = true;
                showLoading('Mengecek ongkir...');

                $.ajax({
                    url: "{{ route('checkout.check-ongkir') }}",
                    method: 'POST',
                    data: { _token: csrfToken, couriers },
                    success: function (services) {
                        currentServices = services;
                        displayServices(services);
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.error;
                        if (xhr.status === 404) {
                            showShippingMessage('error', 'Kurir tidak ditemukan, silakan cek kurir lain.');
                        } else {
                            showShippingMessage('error', msg || 'Gagal cek ongkir. Coba lagi.');
                        }
                    },
                    complete: function () {
                        isCheckingOngkir = false;
                    },
                });
            }

            function displayServices(services) {
                if (!services?.length) {
                    showShippingMessage('error', 'Kurir tidak ditemukan, silakan cek kurir lain.');
                    return;
                }

                services.sort((a, b) => parseInt(a.cost) - parseInt(b.cost));

                let html = `
                    <div class="flex items-center gap-2 mb-3 p-3 bg-soft-mint border border-brand-primary/20 rounded-2xl">
                        <i class="fa-solid fa-list-check text-brand-primary"></i>
                        <p class="text-xs font-bold text-brand-dark">
                            ${services.length} layanan tersedia
                            <span class="text-brand-primary ml-1">— termurah otomatis dipilih</span>
                        </p>
                    </div>
                `;

                services.forEach(function (svc, index) {
                    const cost = parseInt(svc.cost);
                    const isCheapest = index === 0;
                    const badge = getCourierBadge(svc.code);

                    html += `
                        <label class="block cursor-pointer transition-all">
                            <input type="radio" name="shipping_service_radio" value="${cost}"
                                   data-code="${escapeHtml(svc.code)}" data-service="${escapeHtml(svc.service)}"
                                   data-name="${escapeHtml(svc.name)}" data-etd="${escapeHtml(svc.etd)}"
                                   class="peer sr-only shipping-option"
                                   ${isCheapest ? 'id="shipping-cheapest"' : ''}>
                            <div class="relative p-4 border rounded-2xl flex items-center gap-3
                                        ${isCheapest ? 'border-brand-primary bg-soft-mint shadow-sm' : 'border-gray-100 hover:border-brand-primary/30'}
                                        peer-checked:border-brand-primary peer-checked:bg-soft-mint peer-checked:shadow-sm">

                                ${isCheapest ? `
                                <div class="absolute -top-2.5 left-3 bg-brand-primary text-white px-2.5 py-0.5 rounded-full text-[9px] font-black shadow">
                                    TERMURAH
                                </div>` : ''}

                                ${badge}

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                                        <span class="text-sm font-bold text-brand-dark">${escapeHtml(svc.name)}</span>
                                        <span class="text-[10px] bg-white text-brand-primary border border-brand-primary/20 px-2 py-0.5 rounded-full font-bold">${escapeHtml(svc.service)}</span>
                                    </div>
                                    <p class="text-[10px] text-gray-400 truncate">${escapeHtml(svc.description || '')}</p>
                                    <p class="text-[10px] font-semibold text-brand-primary mt-0.5">Estimasi ${escapeHtml(String(svc.etd))} hari</p>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-base font-black ${isCheapest ? 'text-brand-dark' : 'text-brand-primary'}">
                                        ${formatRupiah(cost)}
                                    </p>
                                    ${isCheapest ? '<p class="text-[9px] text-brand-primary font-bold mt-0.5">Paling hemat</p>' : ''}
                                </div>
                            </div>
                        </label>
                    `;
                });

                $('#shipping-services').html(html);

                // Auto-select yang termurah
                const $cheapest = $('#shipping-cheapest');
                if ($cheapest.length) {
                    $cheapest.prop('checked', true).trigger('change');
                }
            }

            function resetShippingDisplay() {
                $('#shipping-services').html(`
                                                <div class="text-center py-8 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                                                    <i class="fa-solid fa-truck text-gray-300 text-3xl mb-3"></i>
                                                    <p class="text-sm font-semibold text-gray-500 mb-2">Pilih kurir untuk melihat ongkir otomatis.</p>
                                                    <p class="text-xs text-gray-400">Biaya pengiriman akan dihitung setelah kurir dipilih.</p>
                                                </div>
                                            `);
                currentShipping = 0;
                $('#selected_shipping_cost').val('');
                $('#selected_service_info').addClass('hidden');
                $('#shipping-selected-card')
                    .removeClass('border-brand-primary/30 bg-soft-mint/30')
                    .addClass('border-dashed border-gray-200 bg-gray-50/70');
                $('#shipping-selected-title').text('Pilih kurir untuk melihat ongkir otomatis.');
                $('#shipping-selected-meta').text('Biaya pengiriman akan dihitung setelah kurir dipilih.');
                updateTotals();
            }

            $(document).on('change', '.shipping-option', function () {
                const $el = $(this);
                currentShipping = parseInt($el.val()) || 0;

                $('#selected_courier_code').val($el.data('code'));
                $('#selected_courier_service').val($el.data('service'));
                $('#selected_shipping_cost').val(currentShipping);
                $('#selected_shipping_etd').val($el.data('etd'));
                $('#selected_service_label').text(`${$el.data('name')} ${$el.data('service')}`);
                $('#selected_service_etd').text(`Est. ${$el.data('etd')} hari`);
                $('#selected_service_info').removeClass('hidden');
                $('#btn-submit').prop('disabled', false);
                $('#mobile-btn-submit').prop('disabled', false);
                $('#shipping-selected-card')
                    .removeClass('border-dashed border-gray-200 bg-gray-50/70')
                    .addClass('border-brand-primary/30 bg-soft-mint/30');
                $('#shipping-selected-title').text(`${$el.data('name')} ${$el.data('service')} - ${formatRupiah(currentShipping)}`);
                $('#shipping-selected-meta').text(`Estimasi ${$el.data('etd')} hari`);

                updateTotals();
            });

            // ─── COUPON ──────────────────────────────────────────────────────────

            $(document).on('input', '#coupon_input', function () {
                $(this).val($(this).val().toUpperCase());
            });

            $(document).on('click', '#btn-apply-coupon', function () {
                const code = $('#coupon_input').val().trim();
                if (!code) return;

                const $btn = $(this).prop('disabled', true).text('Mengecek...');

                $.ajax({
                    url: "{{ route('checkout.apply-coupon') }}",
                    method: 'POST',
                    data: { _token: csrfToken, coupon_code: code },
                    success: function (res) {
                        if (!res.success) {
                            showCouponMessage('error', res.message);
                            $btn.prop('disabled', false).text('Pakai');
                            return;
                        }

                        currentDiscount = res.discount_amount;
                        $('#coupon_code_input').val(code.toUpperCase());
                        $('#discount_display').text('-' + formatRupiah(res.discount_amount));
                        $('#discount-row').show();
                        $('#coupon-message').addClass('hidden');
                        updateTotals();

                        $('#coupon-input-box').replaceWith(buildAppliedCouponHTML(code, res));
                    },
                    error: function () {
                        showCouponMessage('error', 'Terjadi kesalahan. Coba lagi.');
                        $btn.prop('disabled', false).text('Pakai');
                    },
                });
            });

            $(document).on('click', '#btn-remove-coupon', function () {
                $.post("{{ route('checkout.remove-coupon') }}", { _token: csrfToken }, function () {
                    currentDiscount = 0;
                    $('#coupon_code_input').val('');
                    $('#discount-row').hide();
                    updateTotals();
                    $('#coupon-applied-box').replaceWith(buildCouponInputHTML());
                });
            });

            // ─── TEMPLATE BUILDERS ───────────────────────────────────────────────

            function buildAppliedCouponHTML(code, res) {
                return `
                                                <div id="coupon-applied-box"
                                                    class="flex items-center justify-between p-4 bg-purple-50 border-2 border-purple-300 rounded-2xl">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                                                            <i class="fa-solid fa-tag"></i>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-black text-purple-700">${code.toUpperCase()}</p>
                                                            <p class="text-xs text-purple-500">${res.coupon_name}</p>
                                                            <p class="text-xs font-bold text-green-600 mt-0.5">Hemat ${formatRupiah(res.discount_amount)}</p>
                                                        </div>
                                                    </div>
                                                    <button type="button" id="btn-remove-coupon"
                                                        class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors px-3 py-1 rounded-lg hover:bg-red-50">
                                                        <i class="fa-solid fa-xmark mr-1"></i> Hapus
                                                    </button>
                                                </div>
                                            `;
            }

            function buildCouponInputHTML() {
                return `
                                                <div id="coupon-input-box" class="flex gap-3">
                                                    <div class="relative flex-1">
                                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-300">
                                                            <i class="fa-solid fa-ticket text-sm"></i>
                                                        </div>
                                                        <input type="text" id="coupon_input" placeholder="Masukkan kode voucher"
                                                            autocomplete="off" autocapitalize="characters"
                                                            class="w-full pl-10 pr-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm font-bold
                                                                   uppercase tracking-widest focus:outline-none focus:border-purple-400 transition-colors">
                                                    </div>
                                                    <button type="button" id="btn-apply-coupon"
                                                        class="px-5 py-3.5 bg-purple-500 text-white font-black rounded-xl text-sm
                                                               hover:-translate-y-0.5 transition-all active:scale-95 whitespace-nowrap">
                                                        Pakai
                                                    </button>
                                                </div>
                                                <div id="coupon-message" class="mt-3 hidden"></div>
                                            `;
            }

            // ─── FORM SUBMIT ─────────────────────────────────────────────────────

            let formSubmitting = false;

            function submitCheckout() {
                formSubmitting = true;
                $('#checkoutForm')[0].submit();
            }

            $('#checkoutForm, #mobile-btn-submit').on('click', function (e) { /* handled below */ });

            $('#checkoutForm').on('submit', function (e) {
                if (formSubmitting) return; // izinkan submit manual
                e.preventDefault();

                if (!$('#selected_shipping_cost').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Pengiriman Dulu',
                        text: 'Silakan pilih kurir dan layanan pengiriman sebelum membuat pesanan.',
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#A78B6F',
                    });
                    toggleShippingModal(true);
                    return;
                }

                const grandTotal = subtotal - currentDiscount + currentShipping;
                const courierLabel = $('#selected_service_label').text() || '-';
                const etd = $('#selected_service_etd').text() || '-';
                const itemCount = {{ $carts->count() }};

                @php
                    $confirmItems = '';
                    foreach ($carts as $item) {
                        $confirmItems .= '<div class="flex justify-between text-xs py-1 border-b border-gray-100">'
                            . '<span class="text-gray-600 truncate max-w-[60%]">' . e($item->product->name)
                            . ($item->variant ? ' <span class="text-gray-400">(' . e(collect($item->variant->attributes)->pluck('attribute_value')->implode('/')) . ')</span>' : '')
                            . ' ×' . $item->qty . '</span>'
                            . '<span class="font-bold text-gray-800">Rp' . number_format($item->price * $item->qty, 0, ',', '.') . '</span>'
                            . '</div>';
                    }
                @endphp

                const itemsHtml = `{!! addslashes($confirmItems) !!}`;

                const discountRow = currentDiscount > 0
                    ? `<div class="flex justify-between text-xs py-1.5 text-purple-600">
                            <span>Diskon Voucher</span>
                            <span class="font-bold">-${formatRupiah(currentDiscount)}</span>
                       </div>`
                    : '';

                Swal.fire({
                    title: '<span class="text-lg font-black text-brand-dark">Konfirmasi Pesanan</span>',
                    html: `
                        <div class="text-left space-y-1 max-h-48 overflow-y-auto pr-1 mb-3">
                            ${itemsHtml}
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 space-y-1.5">
                            <div class="flex justify-between text-xs py-1">
                                <span class="text-gray-500">Subtotal Produk</span>
                                <span class="font-bold text-gray-800">${formatRupiah(subtotal)}</span>
                            </div>
                            ${discountRow}
                            <div class="flex justify-between text-xs py-1">
                                <span class="text-gray-500">Ongkos Kirim</span>
                                <span class="font-bold text-gray-800">${formatRupiah(currentShipping)}</span>
                            </div>
                            <div class="text-[10px] text-gray-400 pb-1">${courierLabel} · ${etd}</div>
                            <div class="flex justify-between pt-2 border-t border-gray-200">
                                <span class="text-sm font-black text-brand-dark">Total Pembayaran</span>
                                <span class="text-sm font-black text-brand-primary">${formatRupiah(grandTotal)}</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-3">Dengan melanjutkan, kamu setuju untuk membayar pesanan ini.</p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa-solid fa-check mr-1"></i> Ya, Buat Pesanan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#A78B6F',
                    cancelButtonColor: '#9CA3AF',
                    reverseButtons: false,
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl font-bold text-sm',
                        cancelButton: 'rounded-xl font-bold text-sm',
                    },
                }).then(function (result) {
                    if (result.isConfirmed) {
                        submitCheckout();
                    }
                });
            });

        });
    </script>
@endpush
