@extends('layouts.customer')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
    <section class="mobile-action-safe-space px-4 py-6 bg-[#f8f3ee] min-h-screen sm:px-6 sm:py-12 lg:px-8">
        <div class="max-w-6xl mx-auto">
            @php
                $statusMap = [
                    'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Menunggu Pembayaran', 'short' => 'Bayar', 'icon' => 'fa-clock'],
                    'confirmed' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Dikonfirmasi', 'short' => 'Konfirmasi', 'icon' => 'fa-check-circle'],
                    'processing' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'label' => 'Diproses', 'short' => 'Diproses', 'icon' => 'fa-box'],
                    'shipped' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'label' => 'Dalam Pengiriman', 'short' => 'Dikirim', 'icon' => 'fa-truck'],
                    'delivered' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'label' => 'Selesai', 'short' => 'Selesai', 'icon' => 'fa-check-double'],
                    'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => 'Dibatalkan', 'short' => 'Batal', 'icon' => 'fa-xmark'],
                ];
                $currentStatus = $statusMap[$order->status] ?? $statusMap['pending'];

                $payment = $order->payment;
                $paymentExpiresAt = $payment?->expired_at ?: $order->created_at->copy()->addDay();
                $remainingPaymentSeconds = $order->status === 'pending' ? max(0, now()->diffInSeconds($paymentExpiresAt, false)) : 0;
            @endphp

            <div class="mb-6 flex flex-col gap-4 md:mb-10 md:flex-row md:items-center md:justify-between">
                <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                    <a href="{{ route('order.history') }}"
                        class="flex h-11 w-11 flex-shrink-0 items-center justify-center bg-white text-brand-dark shadow-sm border border-brand-secondary/50 transition-all hover:bg-brand-primary hover:text-white sm:h-12 sm:w-12">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-xl font-black text-brand-dark tracking-tight sm:text-2xl">Detail Pesanan</h1>
                        <p class="truncate text-xs text-gray-500 sm:text-sm">ID Transaksi: <span
                                class="font-bold text-brand-dark">#{{ $order->order_number }}</span></p>
                    </div>
                </div>

                <div
                    class="flex w-full items-center gap-3 {{ $currentStatus['bg'] }} border border-current/10 bg-white p-2 pr-4 shadow-sm sm:w-fit sm:pr-6">
                    <div
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl {{ str_replace('text-', 'bg-', $currentStatus['text']) }} text-white shadow-lg">
                        <i class="fa-solid {{ $currentStatus['icon'] }}"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold opacity-60 uppercase tracking-widest leading-none">Status</p>
                        <p class="truncate text-sm font-black {{ $currentStatus['text'] }} uppercase">{{ $currentStatus['label'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">

                    {{-- STEPPER --}}
                    <div class="bg-white p-4 shadow-sm border border-brand-secondary/30 sm:p-8">
                        @if (in_array($order->status, ['cancelled', 'refunded']))
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-red-50 border border-dashed border-red-200">
                                <i class="fa-solid fa-circle-xmark text-red-500 text-2xl"></i>
                                <div>
                                    <h4 class="font-bold text-red-700">Pesanan Dibatalkan</h4>
                                    <p class="text-xs text-red-600 opacity-80">
                                        {{ $order->cancellation_reason ?: 'Mohon hubungi admin jika ada kendala terkait pengembalian dana.' }}
                                    </p>
                                    @if($order->cancelled_at)
                                        <p class="text-[10px] text-red-400 mt-1">
                                            Dibatalkan {{ $order->cancelled_at->format('d M Y, H:i') }}
                                            @if($order->cancelled_by) oleh {{ $order->cancelled_by === 'customer' ? 'customer' : 'sistem' }} @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            @php
                                $steps = ['pending', 'processing', 'shipped', 'delivered'];
                                $currentIndex = array_search($order->status, $steps);
                                $progressIndex = $currentIndex !== false ? $currentIndex : 0;
                            @endphp

                            <div class="relative">
                                <div class="absolute left-[12.5%] right-[12.5%] top-5 h-1 bg-gray-100">
                                    <div class="h-full bg-brand-primary transition-all duration-700"
                                        style="width: {{ ($progressIndex / (count($steps) - 1)) * 100 }}%">
                                    </div>
                                </div>

                                <div class="relative z-10 grid grid-cols-4 gap-1">
                                    @foreach ($steps as $index => $step)
                                        <div class="flex min-w-0 flex-col items-center text-center">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center rounded-full border-4 border-white shadow-sm transition-all sm:h-11 sm:w-11
                                                {{ $progressIndex >= $index ? 'bg-brand-primary text-white' : 'bg-gray-200 text-gray-400' }}">
                                                <i class="fa-solid {{ $statusMap[$step]['icon'] }} text-[10px]"></i>
                                            </div>
                                            <span
                                                class="mt-2 block w-full px-0.5 text-[9px] font-black uppercase leading-tight tracking-normal sm:text-[10px] {{ $progressIndex >= $index ? 'text-brand-dark' : 'text-gray-300' }}">
                                                <span class="sm:hidden">{{ $statusMap[$step]['short'] }}</span>
                                                <span class="hidden sm:inline">{{ $statusMap[$step]['label'] }}</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- PRODUK --}}
                    <div class="bg-white overflow-hidden shadow-sm border border-brand-secondary/30">
                        <div class="p-6 border-b border-brand-secondary/20 bg-[#f8f3ee]/60">
                            <h3 class="font-bold text-brand-dark flex items-center gap-2">
                                <i class="fa-solid fa-bag-shopping text-brand-primary"></i> Daftar Belanja
                            </h3>
                        </div>
                            <div class="divide-y divide-gray-100 p-4 sm:p-6">
                            @foreach ($order->items as $item)
                                <div class="flex gap-4 py-5 first:pt-0 last:pb-0 sm:gap-6 sm:py-6">
                                    <div
                                        class="h-24 w-20 flex-shrink-0 overflow-hidden rounded-2xl bg-gray-100 border border-gray-100 sm:h-24 sm:w-20">
                                        @php $primaryImage = $item->product->images->where('is_primary', true)->first(); @endphp
                                        <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0 flex-grow">
                                        <h4 class="mb-1 text-sm font-bold leading-snug text-brand-dark sm:text-base">{{ $item->product->name }}</h4>
                                        @if ($item->variant)
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">
                                                VARIAN: {{ $item->variant->name ?? 'Default' }}
                                            </p>
                                        @endif
                                        <div class="mt-3 flex flex-col gap-1 sm:mt-4 sm:flex-row sm:items-center sm:justify-between">
                                            <p class="text-sm text-gray-500">{{ $item->qty }} x
                                                Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                            <p class="font-black text-brand-dark">
                                                Rp{{ number_format($item->qty * $item->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- REVIEW SECTION --}}
                    @if($order->status == 'delivered')
                    <div class="bg-white overflow-hidden shadow-sm border border-brand-secondary/30">
                        <div class="p-6 border-b border-brand-secondary/20 bg-[#f8f3ee]/60">
                                <h3 class="font-bold text-brand-dark flex items-center gap-2">
                                    <i class="fa-solid fa-star text-brand-primary"></i> Ulasan Produk
                                </h3>
                            </div>
                            <div class="p-6 divide-y divide-gray-100">
                                @foreach($order->items as $item)
                                    <div class="py-6 first:pt-0 last:pb-0" id="review-item-{{ $item->id }}">
                                        <div class="flex gap-4 items-start">
                                            <div class="w-14 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                                @php $img = $item->product->images->where('is_primary', true)->first(); @endphp
                                                <img src="{{ $img ? asset('storage/' . $img->image_url) : 'https://via.placeholder.com/100' }}"
                                                    class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-bold text-brand-dark text-sm">{{ $item->product->name }}</p>
                                                @if($item->variant)
                                                    <p class="text-[10px] text-gray-400 font-bold uppercase mb-2">
                                                        {{ $item->variant->name }}</p>
                                                @endif

                                                @if($item->review)
                                                    <div class="mt-2 bg-soft-mint/30 rounded-xl p-3 border border-brand-primary/10">
                                                        <div class="flex gap-1 mb-1">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i
                                                                    class="fa-star text-xs {{ $i <= $item->review->rating ? 'fa-solid text-yellow-400' : 'fa-regular text-gray-300' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <p class="text-xs text-gray-600">{{ $item->review->comment ?? '-' }}</p>
                                                    </div>
                                                @else
                                                    {{-- form review --}}
                                                    <div class="mt-2">
                                                        <div class="flex gap-1 mb-2 star-rating" data-item="{{ $item->id }}">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <i class="fa-regular fa-star text-xl text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors star-btn"
                                                                    data-value="{{ $i }}"></i>
                                                            @endfor
                                                        </div>
                                                        <input type="hidden" class="rating-value" id="rating-{{ $item->id }}" value="0">
                                                        <textarea
                                                            class="w-full text-sm border border-gray-100 rounded-xl p-3 bg-gray-50 resize-none focus:outline-none focus:border-brand-primary/50 transition-colors"
                                                            rows="2" placeholder="Tulis ulasanmu..."
                                                            id="comment-{{ $item->id }}"></textarea>

                                                        {{-- foto ulasan --}}
                                                        <div class="mt-2">
                                                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">
                                                                Foto (opsional, maks. 5)
                                                            </label>
                                                            <div class="flex flex-wrap gap-2 mb-2" id="preview-{{ $item->id }}"></div>
                                                            <label class="inline-flex items-center gap-2 cursor-pointer px-3 py-2 rounded-xl border border-dashed border-brand-secondary/60 bg-gray-50 text-xs font-bold text-brand-dark/50 hover:border-brand-primary hover:text-brand-primary transition-colors">
                                                                <i class="fa-solid fa-camera text-sm"></i>
                                                                Tambah Foto
                                                                <input type="file" class="hidden review-images" id="images-{{ $item->id }}" data-item="{{ $item->id }}" multiple accept="image/jpeg,image/png,image/jpg,image/webp">
                                                            </label>
                                                        </div>

                                                        <button onclick="submitReview('{{ $order->order_number }}', {{ $item->id }})"
                                                            class="mt-3 px-4 py-2 bg-brand-primary text-white text-xs font-bold rounded-xl hover:opacity-90 transition-all">
                                                            Kirim Ulasan
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- TRACKING --}}
                    @if ($order->shipment && $order->shipment->resi)
                        <div class="bg-white p-8 shadow-sm border border-brand-secondary/30">
                            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
                                <h3 class="font-bold text-brand-dark flex items-center gap-3 text-lg">
                                    <div class="w-10 h-10 bg-brand-primary/10 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-truck-fast text-brand-primary"></i>
                                    </div>
                                    Lacak Pengiriman
                                </h3>
                                <div class="text-left md:text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">No. Resi</p>
                                    <span
                                        class="font-mono font-bold text-brand-dark bg-gray-100 px-3 py-1 rounded-lg text-sm italic">
                                        {{ $order->shipment->resi }}
                                    </span>
                                    @if($order->shipment->tracked_at)
                                        <p class="text-[10px] text-gray-400 mt-2">Update {{ $order->shipment->tracked_at->format('d M Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                                <div class="flex-1 rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Berat Paket</p>
                                    <p class="font-black text-brand-dark mt-1">{{ number_format($order->shipment->total_weight ?? 10, 0, ',', '.') }} gram</p>
                                </div>
                                <form action="{{ route('order.history.track', $order->order_number) }}" method="POST" class="sm:w-auto">
                                    @csrf
                                    <button type="submit"
                                        class="w-full sm:w-auto h-full min-h-[58px] px-5 bg-brand-primary text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-brand-dark transition-all">
                                        <i class="fa-solid fa-location-crosshairs mr-2"></i>Lacak Resi
                                    </button>
                                </form>
                            </div>

                            @if ($tracking && isset($tracking['manifest']))
                                <div
                                    class="relative pl-8 space-y-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                                    @foreach (array_reverse($tracking['manifest']) as $log)
                                        @php
                                            $statusLabel = match(strtolower($log['status'] ?? '')) {
                                                'confirmed'                                       => 'Pesanan dikonfirmasi, kurir akan dijadwalkan.',
                                                'allocated'                                       => 'Kurir sudah dialokasikan dan siap menjemput.',
                                                'picking_up'                                      => 'Kurir sedang menuju lokasi pengambilan.',
                                                'picked', 'picked_up', 'pickup'                   => 'Barang sudah diambil oleh kurir.',
                                                'dropping_off', 'in_transit', 'on_delivery', 'on_the_way' => 'Kurir sedang menuju lokasi tujuan.',
                                                'delivered'                                       => 'Pesanan berhasil diterima oleh penerima.',
                                                'cancelled', 'canceled'                           => 'Pengiriman dibatalkan.',
                                                'on_hold'                                         => 'Pengiriman ditahan sementara karena kendala.',
                                                'return_in_transit'                               => 'Paket sedang dalam proses retur ke pengirim.',
                                                'returned'                                        => 'Paket berhasil diretur ke pengirim.',
                                                'disposed'                                        => 'Paket dimusnahkan.',
                                                'failed'                                          => 'Pengiriman gagal.',
                                                default                                           => null,
                                            };
                                            $description = $log['manifest_description'] ?? $log['description'] ?? $log['note'] ?? $statusLabel ?? '-';
                                        @endphp
                                        <div class="relative">
                                            <div
                                                class="absolute -left-[27px] top-1 w-4 h-4 rounded-full border-4 border-white {{ $loop->first ? 'bg-brand-primary ring-4 ring-brand-primary/20' : 'bg-gray-300' }}">
                                            </div>
                                            <div class="flex flex-col md:flex-row md:justify-between gap-2">
                                                <div>
                                                    <p
                                                        class="text-sm font-bold {{ $loop->first ? 'text-brand-dark' : 'text-gray-500' }}">
                                                        {{ $description }}
                                                    </p>
                                                    <p class="text-xs text-gray-400 font-medium">{{ $log['city_name'] }}
                                                    </p>
                                                </div>
                                                <div class="flex flex-row md:flex-col items-center md:items-end gap-2 md:gap-0">
                                                    <p class="text-[10px] font-bold text-brand-primary uppercase">
                                                        {{ \Carbon\Carbon::parse($log['manifest_date'])->format('d M Y') }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-medium">
                                                        {{ $log['manifest_time'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bg-amber-50 border border-amber-100 p-5 rounded-2xl flex items-start gap-4">
                                    <i class="fa-solid fa-circle-info text-amber-500 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-amber-900 font-bold mb-1">Informasi Pelacakan Segera Hadir
                                        </p>
                                        <p class="text-xs text-amber-700/80 leading-relaxed">Kurir telah menerima permintaan
                                            pengiriman. Riwayat perjalanan paket Anda akan muncul secara otomatis di sini
                                            dalam
                                            beberapa jam.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- SIDEBAR --}}
                <div class="space-y-6">
                    {{-- TOTAL CARD --}}
                    <div class="bg-brand-dark text-white rounded-[40px] p-8 shadow-2xl relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-brand-primary/20 rounded-full blur-3xl"></div>
                        <h3 class="font-bold text-lg mb-6 relative z-10">Ringkasan Pembayaran</h3>

                        <div class="space-y-3 mb-6 relative z-10 border-b border-white/10 pb-6 text-white/70 text-sm">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span
                                    class="font-bold text-white">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Ongkos Kirim</span>
                                <span
                                    class="font-bold text-white">Rp{{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                            @if ($order->discount > 0)
                                <div class="flex justify-between text-brand-primary">
                                    <span>Diskon</span>
                                    <span class="font-bold">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-between items-end mb-8 relative z-10">
                            <span class="text-xs font-bold uppercase tracking-widest text-brand-primary">Total Akhir</span>
                            <span class="text-3xl font-black">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>

                        {{-- STATUS PEMBAYARAN & TOMBOL BAYAR --}}
                        <div class="relative z-10">
                            @if($order->status == 'pending')
                                <div class="bg-amber-500/10 rounded-2xl p-4 border border-amber-500/20 mb-4">
                                    <p class="text-[10px] text-amber-200 uppercase tracking-widest font-bold mb-1">Batas Pembayaran</p>
                                    <p class="text-sm text-white font-bold">{{ $paymentExpiresAt->format('d M Y, H:i') }}</p>
                                    <p class="text-xs text-white/60 mt-2">
                                        Sisa waktu:
                                        <span id="payment-countdown" class="font-black text-amber-300" data-seconds="{{ $remainingPaymentSeconds }}">
                                            Menghitung...
                                        </span>
                                    </p>
                                </div>

                                @if($remainingPaymentSeconds > 0)
                                    <button id="pay-button"
                                        class="desktop-only-action w-full py-4 bg-brand-primary text-white font-bold rounded-2xl shadow-lg hover:shadow-brand-primary/30 transition-all mt-4 flex items-center justify-center gap-2">
                                        BAYAR SEKARANG
                                    </button>
                                @else
                                    <div class="w-full py-4 bg-gray-600 text-white/70 font-bold rounded-2xl text-center mt-4">
                                        Waktu pembayaran habis
                                    </div>
                                @endif

                                <button type="button" onclick="openCancelOrderModal()"
                                    class="w-full py-3 mt-3 bg-white/10 text-white font-bold rounded-2xl border border-white/10 hover:bg-white/15 transition-all">
                                    Batalkan Pesanan
                                </button>

                            @elseif($order->status == 'processing')
                                <div class="bg-indigo-500/10 rounded-2xl p-6 border border-indigo-500/20 text-center">
                                    <div
                                        class="w-16 h-16 bg-indigo-500/20 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                                        <i class="fa-solid fa-box-open text-indigo-400 text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-white text-sm mb-1">Pesanan Sedang Diproses</h4>
                                    <p class="text-[10px] text-white/60 leading-relaxed">
                                        Pembayaran berhasil diverifikasi. Penjual sedang menyiapkan hijab cantik Anda untuk
                                        segera dikirim.
                                    </p>

                                    <div
                                        class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center text-[10px]">
                                        <span class="text-white/40 uppercase tracking-widest font-bold">Estimasi
                                            Pengemasan </span>
                                        <span class="text-indigo-300 font-bold">1-2 Hari Kerja</span>
                                    </div>
                                </div>

                            @elseif($order->status == 'shipped')
                                <div class="bg-cyan-500/10 rounded-2xl p-6 border border-cyan-500/20 text-center">
                                    <i class="fa-solid fa-truck-fast text-cyan-400 text-3xl mb-3"></i>
                                    <h4 class="font-bold text-white text-sm mb-1">Pesanan Dalam Perjalanan</h4>
                                    <p class="text-[10px] text-white/60">Cek nomor resi di bagian informasi pengiriman.</p>
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="bg-white rounded-[32px] p-6 shadow-sm border border-gray-100">
                        <h4
                            class="font-bold text-brand-dark mb-4 text-xs uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-brand-primary"></i> Alamat Pengiriman
                        </h4>
                        <div class="text-sm">
                            <p class="font-black text-brand-dark">{{ $order->address->receiver_name }}</p>
                            <p class="text-gray-500 mb-3">{{ $order->address->phone }}</p>
                            <p
                                class="text-xs text-gray-600 leading-relaxed italic bg-gray-50 p-3 rounded-xl border border-gray-100">
                                {{ $order->address->address }}, {{ $order->address->subdistrict }},
                                {{ $order->address->city }}, {{ $order->address->province }}
                            </p>
                        </div>
                    </div>

                    {{-- KONFIRMASI SELESAI --}}
                    @if ($order->status == 'shipped')
                        <form action="{{ route('order.history.complete', $order->order_number) }}" method="POST" class="desktop-only-action">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="w-full py-5 bg-green-500 text-white font-black rounded-2xl shadow-lg hover:bg-green-600 transition-all flex items-center justify-center gap-3">
                                <i class="fa-solid fa-box-open"></i> Pesanan Diterima
                            </button>
                        </form>
                    @endif

                    @if (in_array($order->status, ['pending', 'confirmed']))
                        <button type="button" onclick="openCancelOrderModal()"
                            class="w-full py-4 bg-red-50 text-red-600 font-black rounded-2xl border border-red-100 hover:bg-red-100 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-ban"></i> Batalkan Pesanan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($order->status == 'pending' && $remainingPaymentSeconds > 0)
        <x-user.components.mobile-bottom-action-bar>
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-[0.18em] text-gray-400">Total Pembayaran</p>
                    <p class="mt-0.5 truncate text-xl font-black text-brand-dark">
                        Rp{{ number_format($order->total, 0, ',', '.') }}
                    </p>
                </div>
                @if($payment && $payment->snap_token)
                    <button type="button" id="pay-button-mobile"
                        class="flex min-h-[52px] min-w-[150px] items-center justify-center rounded-2xl bg-brand-primary px-5 text-sm font-black uppercase text-white shadow-lg shadow-brand-primary/25 transition active:scale-95">
                        Bayar Sekarang
                    </button>
                @else
                    <a href="{{ route('order.history.show', $order->order_number) }}"
                        class="flex min-h-[52px] min-w-[170px] items-center justify-center rounded-2xl bg-brand-primary px-5 text-xs font-black uppercase text-white shadow-lg shadow-brand-primary/25">
                        Lanjutkan Pembayaran
                    </a>
                @endif
            </div>
        </x-user.components.mobile-bottom-action-bar>
    @elseif($order->status == 'shipped')
        <x-user.components.mobile-bottom-action-bar>
            <form action="{{ route('order.history.complete', $order->order_number) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="flex min-h-[54px] w-full items-center justify-center gap-3 rounded-2xl bg-green-500 px-5 text-sm font-black uppercase text-white shadow-lg shadow-green-500/20 transition active:scale-95">
                    <i class="fa-solid fa-box-open"></i>
                    Konfirmasi Pembayaran
                </button>
            </form>
        </x-user.components.mobile-bottom-action-bar>
    @endif

    @if (in_array($order->status, ['pending', 'confirmed']))
        <div id="cancelOrderModal" class="fixed inset-0 z-[120] hidden items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeCancelOrderModal()"></div>
            <div class="relative w-full max-w-md bg-white rounded-[28px] shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-brand-dark">Batalkan Pesanan</h3>
                        <p class="text-xs text-gray-400 mt-1">Alasan pembatalan wajib diisi.</p>
                    </div>
                    <button type="button" onclick="closeCancelOrderModal()" class="w-9 h-9 rounded-full hover:bg-gray-100 text-gray-400">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form action="{{ route('order.history.cancel', $order->order_number) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')
                    <textarea name="cancellation_reason" rows="4" required minlength="5" maxlength="500"
                        placeholder="Contoh: ingin mengubah alamat / salah pilih produk / belum jadi checkout."
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:border-brand-primary transition-colors resize-none"></textarea>
                    <button type="submit"
                        class="w-full py-3.5 bg-red-500 text-white font-black rounded-2xl hover:bg-red-600 transition-all">
                        Ya, Batalkan Pesanan
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    @if($order->status == 'pending')
        <script>
            const countdownEl = document.getElementById('payment-countdown');
            if (countdownEl) {
                let seconds = parseInt(countdownEl.dataset.seconds || '0', 10);
                const renderCountdown = () => {
                    if (seconds <= 0) {
                        countdownEl.textContent = '00:00:00';
                        return;
                    }

                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    const secs = seconds % 60;
                    countdownEl.textContent = [hours, minutes, secs].map(v => String(v).padStart(2, '0')).join(':');
                    seconds -= 1;
                };

                renderCountdown();
                setInterval(renderCountdown, 1000);
            }
        </script>
    @endif

    @if(in_array($order->status, ['pending', 'confirmed']))
        <script>
            window.openCancelOrderModal = function () {
                $('#cancelOrderModal').removeClass('hidden').addClass('flex');
                $('body').addClass('overflow-hidden');
            }

            window.closeCancelOrderModal = function () {
                $('#cancelOrderModal').addClass('hidden').removeClass('flex');
                $('body').removeClass('overflow-hidden');
            }
        </script>
    @endif

    @if($order->status == 'pending' && $payment && $payment->snap_token && $remainingPaymentSeconds > 0)
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script type="text/javascript">
            let pollingInterval = null;

            function startPolling() {
                if (pollingInterval) return;
                pollingInterval = setInterval(async () => {
                    try {
                        const res = await fetch('/order/{{ $order->order_number }}/payment-status');
                        const data = await res.json();

                        if (data.status === 'success') {
                            stopPolling();
                            window.snap.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: 'Pesanan kamu sedang diproses.',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false,
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    } catch (e) {
                        console.error('Polling error:', e);
                    }
                }, 3000);
            }

            function stopPolling() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            }

            function openSnapPayment() {
                window.snap.pay('{{ $payment->snap_token }}', {
                    onSuccess: function (result) {
                        stopPolling();
                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil!',
                            text: 'Pesanan kamu sedang diproses.',
                            confirmButtonText: 'Lihat Pesanan'
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    onPending: function (result) {
                        startPolling();
                    },
                    onError: function (result) {
                        stopPolling();
                        Swal.fire({
                            icon: 'error',
                            title: 'Pembayaran Gagal',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                            confirmButtonText: 'Coba Lagi'
                        });
                    },
                    onClose: function () {
                        startPolling();
                    }
                });
                setTimeout(() => startPolling(), 2000);
            }

            document.querySelectorAll('#pay-button, #pay-button-mobile').forEach(function (button) {
                if (button) {
                    button.addEventListener('click', openSnapPayment);
                }
            });
        </script>
    @endif

    @if ($order->status == 'delivered')
        <script>
            document.querySelectorAll('.star-rating').forEach(group => {
                const stars = group.querySelectorAll('.star-btn');
                const itemId = group.dataset.item;

                stars.forEach(star => {
                    star.addEventListener('mouseover', function() {
                        const val = this.dataset.value;
                        stars.forEach(s => {
                            s.classList.toggle('fa-solid', s.dataset.value <= val);
                            s.classList.toggle('fa-regular', s.dataset.value > val);
                            s.classList.toggle('text-yellow-400', s.dataset.value <= val);
                            s.classList.toggle('text-gray-300', s.dataset.value > val);
                        });
                    });

                    star.addEventListener('click', function() {
                        document.getElementById('rating-' + itemId).value = this.dataset.value;
                    });

                    star.addEventListener('mouseleave', function() {
                        const selected = document.getElementById('rating-' + itemId).value;
                        stars.forEach(s => {
                            s.classList.toggle('fa-solid', s.dataset.value <= selected);
                            s.classList.toggle('fa-regular', s.dataset.value > selected);
                            s.classList.toggle('text-yellow-400', s.dataset.value <= selected);
                            s.classList.toggle('text-gray-300', s.dataset.value > selected);
                        });
                    });
                });
            });

            // Preview foto ulasan
            document.querySelectorAll('.review-images').forEach(function (input) {
                input.addEventListener('change', function () {
                    const itemId = this.dataset.item;
                    const preview = document.getElementById('preview-' + itemId);
                    preview.innerHTML = '';

                    const files = Array.from(this.files).slice(0, 5);
                    files.forEach(function (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'h-16 w-16 rounded-xl object-cover border border-gray-100';
                            preview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                });
            });

            async function submitReview(orderNumber, itemId) {
                const rating = document.getElementById('rating-' + itemId).value;
                const comment = document.getElementById('comment-' + itemId).value;
                const imageInput = document.getElementById('images-' + itemId);

                if (rating == 0) {
                    Swal.fire({ icon: 'warning', title: 'Pilih Rating', text: 'Berikan bintang terlebih dahulu.', confirmButtonText: 'OK' });
                    return;
                }

                const formData = new FormData();
                formData.append('order_item_id', itemId);
                formData.append('rating', rating);
                formData.append('comment', comment);
                if (imageInput && imageInput.files.length > 0) {
                    Array.from(imageInput.files).slice(0, 5).forEach(function (file) {
                        formData.append('images[]', file);
                    });
                }

                try {
                    const res = await fetch(`/order/${orderNumber}/review`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (res.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terima Kasih!',
                            text: 'Ulasan kamu berhasil dikirim.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                    }
                } catch (e) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.' });
                }
            }
        </script>
    @endif
@endpush
