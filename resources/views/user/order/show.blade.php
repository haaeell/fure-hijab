@extends('layouts.customer')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
    <section class="py-12 bg-[#F8F9FA] min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            @php
                $statusMap = [
                    'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Menunggu Pembayaran', 'icon' => 'fa-clock'],
                    'confirmed' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Dikonfirmasi', 'icon' => 'fa-check-circle'],
                    'processing' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'label' => 'Diproses', 'icon' => 'fa-box'],
                    'shipped' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'label' => 'Dalam Pengiriman', 'icon' => 'fa-truck'],
                    'delivered' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'label' => 'Selesai', 'icon' => 'fa-check-double'],
                    'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => 'Dibatalkan', 'icon' => 'fa-xmark'],
                ];
                $currentStatus = $statusMap[$order->status] ?? $statusMap['pending'];

                $payment = $order->payment;
                $paymentExpiresAt = $payment?->expired_at ?: $order->created_at->copy()->addDay();
                $remainingPaymentSeconds = $order->status === 'pending' ? max(0, now()->diffInSeconds($paymentExpiresAt, false)) : 0;
            @endphp

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                <div class="flex items-center gap-4">
                    <a href="{{ route('order.history') }}"
                        class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-brand-dark shadow-sm border border-gray-100 hover:bg-brand-primary hover:text-white transition-all">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black text-brand-dark tracking-tight">Detail Pesanan</h1>
                        <p class="text-sm text-gray-500">ID Transaksi: <span
                                class="font-bold text-brand-dark">#{{ $order->order_number }}</span></p>
                    </div>
                </div>

                <div
                    class="flex items-center gap-3 {{ $currentStatus['bg'] }} p-2 pr-6 rounded-2xl border border-current/10 w-fit">
                    <div
                        class="w-10 h-10 {{ str_replace('text-', 'bg-', $currentStatus['text']) }} rounded-xl flex items-center justify-center text-white shadow-lg">
                        <i class="fa-solid {{ $currentStatus['icon'] }}"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold opacity-60 uppercase tracking-widest leading-none">Status</p>
                        <p class="text-sm font-black {{ $currentStatus['text'] }} uppercase">{{ $currentStatus['label'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">

                    {{-- STEPPER --}}
                    <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100">
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
                            <div class="relative flex justify-between px-4">
                                @php
                                    $steps = ['pending', 'processing', 'shipped', 'delivered'];
                                    $currentIndex = array_search($order->status, $steps);
                                @endphp
                                <div class="absolute top-5 left-10 right-10 h-1 bg-gray-100 -z-0">
                                    <div class="h-full bg-brand-primary transition-all duration-700"
                                        style="width: {{ $currentIndex !== false ? ($currentIndex / (count($steps) - 1)) * 100 : 0 }}%">
                                    </div>
                                </div>
                                @foreach ($steps as $index => $step)
                                    <div class="relative z-10 flex flex-col items-center">
                                        <div
                                            class="w-10 h-10 rounded-full flex items-center justify-center border-4 border-white shadow-sm transition-all 
                                                                                                                                                                                                                                            {{ $currentIndex >= $index ? 'bg-brand-primary text-white' : 'bg-gray-200 text-gray-400' }}">
                                            <i class="fa-solid {{ $statusMap[$step]['icon'] }} text-[10px]"></i>
                                        </div>
                                        <span
                                            class="mt-2 text-[9px] font-bold uppercase tracking-tighter {{ $currentIndex >= $index ? 'text-brand-dark' : 'text-gray-300' }}">
                                            {{ $statusMap[$step]['label'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- PRODUK --}}
                    <div class="bg-white rounded-[32px] overflow-hidden shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-bold text-brand-dark flex items-center gap-2">
                                <i class="fa-solid fa-bag-shopping text-brand-primary"></i> Daftar Belanja
                            </h3>
                        </div>
                        <div class="p-6 divide-y divide-gray-100">
                            @foreach ($order->items as $item)
                                <div class="py-6 first:pt-0 last:pb-0 flex gap-6">
                                    <div
                                        class="w-20 h-24 rounded-2xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-100">
                                        @php $primaryImage = $item->product->images->where('is_primary', true)->first(); @endphp
                                        <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-grow">
                                        <h4 class="font-bold text-brand-dark text-base mb-1">{{ $item->product->name }}</h4>
                                        @if ($item->variant)
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-2">
                                                VARIAN: {{ $item->variant->name ?? 'Default' }}
                                            </p>
                                        @endif
                                        <div class="flex justify-between items-center mt-4">
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
                        <div class="bg-white rounded-[32px] overflow-hidden shadow-sm border border-gray-100">
                            <div class="p-6 border-b border-gray-50 bg-gray-50/50">
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
                                                        <button onclick="submitReview({{ $order->id }}, {{ $item->id }})"
                                                            class="mt-2 px-4 py-2 bg-brand-primary text-white text-xs font-bold rounded-xl hover:opacity-90 transition-all">
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
                        <div class="bg-white rounded-[32px] p-8 shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="font-bold text-brand-dark flex items-center gap-3 text-lg">
                                    <div class="w-10 h-10 bg-brand-primary/10 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-truck-fast text-brand-primary"></i>
                                    </div>
                                    Lacak Pengiriman
                                </h3>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">No. Resi</p>
                                    <span
                                        class="font-mono font-bold text-brand-dark bg-gray-100 px-3 py-1 rounded-lg text-sm italic">
                                        {{ $order->shipment->resi }}
                                    </span>
                                </div>
                            </div>
                            {{-- (Log pelacakan Anda di sini...) --}}

                            @if ($tracking && isset($tracking['manifest']))
                                <div
                                    class="relative pl-8 space-y-8 before:content-[''] before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                                    @foreach (array_reverse($tracking['manifest']) as $log)
                                        <div class="relative">
                                            <div
                                                class="absolute -left-[27px] top-1 w-4 h-4 rounded-full border-4 border-white {{ $loop->first ? 'bg-brand-primary ring-4 ring-brand-primary/20' : 'bg-gray-300' }}">
                                            </div>
                                            <div class="flex flex-col md:flex-row md:justify-between gap-2">
                                                <div>
                                                    <p
                                                        class="text-sm font-bold {{ $loop->first ? 'text-brand-dark' : 'text-gray-500' }}">
                                                        {{ $log['manifest_description'] }}
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
                                        class="w-full py-4 bg-brand-primary text-white font-bold rounded-2xl shadow-lg hover:shadow-brand-primary/30 transition-all mt-4 flex items-center justify-center gap-2">
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
                        <form action="{{ route('order.history.complete', $order->id) }}" method="POST">
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
                <form action="{{ route('order.history.cancel', $order->id) }}" method="POST" class="p-6 space-y-4">
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
                        const res = await fetch('/order/{{ $order->id }}/payment-status');
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

            const payButton = document.getElementById('pay-button');
            payButton.addEventListener('click', function () {
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

            async function submitReview(orderId, itemId) {
                const rating = document.getElementById('rating-' + itemId).value;
                const comment = document.getElementById('comment-' + itemId).value;

                if (rating == 0) {
                    Swal.fire({ icon: 'warning', title: 'Pilih Rating', text: 'Berikan bintang terlebih dahulu.', confirmButtonText: 'OK' });
                    return;
                }

                try {
                    const res = await fetch(`/order/${orderId}/review`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ order_item_id: itemId, rating, comment })
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
