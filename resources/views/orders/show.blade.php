@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
    @php
        $shippingAddr = $order->address;
        $statusLabels = [
            'pending' => 'Menunggu Pembayaran',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Refund',
        ];
        $paymentLabels = [
            'success' => 'Lunas',
            'pending' => 'Menunggu Pembayaran',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        ];
    @endphp

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <style>
            .order-address-map {
                height: 240px;
            }

            @media (max-width: 640px) {
                .order-address-map {
                    height: 210px;
                }
            }
        </style>
    @endpush

    <style>
        .print-order-document {
            display: none;
        }

        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            html,
            body {
                background: #ffffff !important;
                color: #111827 !important;
                font-family: Arial, Helvetica, sans-serif !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            #sidebar,
            #sidebarOverlay,
            #mainContent > .flex.justify-between,
            .screen-order-detail,
            .swal2-container {
                display: none !important;
            }

            #mainContent {
                margin: 0 !important;
                padding: 0 !important;
            }

            .print-order-document {
                display: block !important;
                width: 100%;
                font-size: 11px;
                line-height: 1.45;
            }

            .print-card {
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                overflow: hidden;
                break-inside: avoid;
            }

            .print-muted {
                color: #6b7280;
            }

            .print-table {
                width: 100%;
                border-collapse: collapse;
            }

            .print-table th {
                background: #f3f4f6;
                color: #374151;
                font-size: 9px;
                letter-spacing: .08em;
                text-transform: uppercase;
                text-align: left;
                padding: 9px 10px;
                border-bottom: 1px solid #e5e7eb;
            }

            .print-table td {
                padding: 10px;
                border-bottom: 1px solid #eef2f7;
                vertical-align: top;
            }

            .print-table tr:last-child td {
                border-bottom: 0;
            }

            .print-badge {
                display: inline-block;
                padding: 5px 9px;
                border-radius: 999px;
                background: #F1F8E9;
                color: #2D5A27;
                font-weight: 700;
                font-size: 9px;
                letter-spacing: .06em;
                text-transform: uppercase;
            }
        }
    </style>

    <section class="print-order-document">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #2D5A27; padding-bottom:16px; margin-bottom:18px;">
            <div>
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <div style="width:38px; height:38px; border-radius:10px; background:#81C784; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800;">
                        AH
                    </div>
                    <div>
                        <h1 style="font-size:20px; margin:0; color:#2D5A27; letter-spacing:.02em;">{{ $adminStoreName }}</h1>
                        <p class="print-muted" style="margin:2px 0 0;">Invoice / Detail Pesanan</p>
                    </div>
                </div>
                <p class="print-muted" style="margin:0; max-width:360px;">
                    Dokumen ini dicetak otomatis dari panel admin sebagai bukti transaksi dan panduan pemrosesan pesanan.
                </p>
            </div>
            <div style="text-align:right;">
                <p style="margin:0 0 6px; font-size:10px; text-transform:uppercase; letter-spacing:.12em;" class="print-muted">Nomor Pesanan</p>
                <h2 style="font-size:18px; margin:0 0 8px; color:#2D5A27; font-family:monospace;">{{ $order->order_number }}</h2>
                <span class="print-badge">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span>
                <p class="print-muted" style="margin:10px 0 0;">Dicetak: {{ now()->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:14px;">
            <div class="print-card" style="padding:12px;">
                <p class="print-muted" style="margin:0 0 6px; font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;">Pelanggan</p>
                <p style="margin:0; font-weight:800; color:#111827;">{{ $order->user->name ?? '-' }}</p>
                <p class="print-muted" style="margin:3px 0 0;">{{ $order->user->email ?? '-' }}</p>
                <p class="print-muted" style="margin:3px 0 0;">{{ $order->user->phone ?? '-' }}</p>
            </div>
            <div class="print-card" style="padding:12px;">
                <p class="print-muted" style="margin:0 0 6px; font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;">Pesanan</p>
                <p style="margin:0;"><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
                <p style="margin:3px 0 0;"><strong>Item:</strong> {{ $order->items->sum('qty') }} pcs</p>
                <p style="margin:3px 0 0;"><strong>Kupon:</strong> {{ $order->coupon?->code ?? '-' }}</p>
            </div>
            <div class="print-card" style="padding:12px;">
                <p class="print-muted" style="margin:0 0 6px; font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;">Pembayaran</p>
                <p style="margin:0;"><strong>Status:</strong> {{ $paymentLabels[$order->payment?->status] ?? ucfirst($order->payment?->status ?? 'Belum Bayar') }}</p>
                <p style="margin:3px 0 0;"><strong>Metode:</strong> {{ strtoupper($order->payment?->payment_method ?? '-') }}</p>
                <p style="margin:3px 0 0;"><strong>Dibayar:</strong> {{ $order->payment?->paid_at ? $order->payment->paid_at->format('d M Y, H:i') : '-' }}</p>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1.35fr .9fr; gap:10px; margin-bottom:14px;">
            <div class="print-card" style="padding:12px;">
                <p class="print-muted" style="margin:0 0 6px; font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;">Alamat Pengiriman</p>
                @if($shippingAddr)
                    <p style="margin:0; font-weight:800;">{{ $shippingAddr->receiver_name }}</p>
                    <p style="margin:3px 0 0;">{{ $shippingAddr->phone }}</p>
                    <p style="margin:6px 0 0;">{{ $shippingAddr->address }}</p>
                    <p class="print-muted" style="margin:3px 0 0;">
                        {{ $shippingAddr->subdistrict ? $shippingAddr->subdistrict . ', ' : '' }}
                        {{ $shippingAddr->district ? $shippingAddr->district . ', ' : '' }}
                        {{ $shippingAddr->city }}, {{ $shippingAddr->province }} {{ $shippingAddr->postal_code }}
                    </p>
                @else
                    <p class="print-muted" style="margin:0;">Alamat pengiriman belum tersedia.</p>
                @endif
            </div>
            <div class="print-card" style="padding:12px;">
                <p class="print-muted" style="margin:0 0 6px; font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;">Pengiriman</p>
                <p style="margin:0;"><strong>Kurir:</strong> {{ strtoupper($order->shipment?->courier ?? '-') }}</p>
                <p style="margin:3px 0 0;"><strong>Layanan:</strong> {{ $order->shipment?->service ?? '-' }}</p>
                <p style="margin:3px 0 0;"><strong>Estimasi:</strong> {{ $order->shipment?->estimated_days ? $order->shipment->estimated_days . ' hari' : '-' }}</p>
                <p style="margin:3px 0 0;"><strong>Berat:</strong> {{ number_format($order->shipment?->total_weight ?? 10, 0, ',', '.') }} gram</p>
                <p style="margin:3px 0 0;"><strong>Resi:</strong> {{ $order->shipment?->resi ?? '-' }}</p>
            </div>
        </div>

        <div class="print-card" style="margin-bottom:14px;">
            <table class="print-table">
                <thead>
                    <tr>
                        <th style="width:38px;">No</th>
                        <th>Produk</th>
                        <th style="width:70px; text-align:center;">Qty</th>
                        <th style="width:120px; text-align:right;">Harga</th>
                        <th style="width:130px; text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->variant_name)
                                    <div class="print-muted" style="font-size:10px; margin-top:2px;">Varian: {{ $item->variant_name }}</div>
                                @endif
                            </td>
                            <td style="text-align:center;">{{ $item->qty }}</td>
                            <td style="text-align:right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td style="text-align:right; font-weight:700;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display:grid; grid-template-columns:1fr 300px; gap:14px; align-items:start;">
            <div class="print-card" style="padding:12px; min-height:98px;">
                <p class="print-muted" style="margin:0 0 6px; font-size:9px; font-weight:700; letter-spacing:.1em; text-transform:uppercase;">Catatan Pesanan</p>
                <p style="margin:0;">{{ $order->notes ?: '-' }}</p>
                @if($order->status === 'cancelled' && $order->cancellation_reason)
                    <p style="margin:10px 0 0;"><strong>Alasan batal:</strong> {{ $order->cancellation_reason }}</p>
                @endif
            </div>

            <div class="print-card" style="padding:12px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:7px;">
                    <span class="print-muted">Subtotal</span>
                    <strong>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:7px;">
                    <span class="print-muted">Ongkos Kirim</span>
                    <strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                </div>
                @if($order->discount > 0)
                    <div style="display:flex; justify-content:space-between; margin-bottom:7px;">
                        <span class="print-muted">Diskon</span>
                        <strong>- Rp {{ number_format($order->discount, 0, ',', '.') }}</strong>
                    </div>
                @endif
                <div style="display:flex; justify-content:space-between; border-top:1px solid #e5e7eb; padding-top:10px; margin-top:10px;">
                    <span style="font-size:13px; font-weight:800; color:#2D5A27;">TOTAL</span>
                    <strong style="font-size:15px; color:#2D5A27;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-top:28px; padding-top:12px; border-top:1px solid #e5e7eb;">
            <p class="print-muted" style="margin:0; max-width:430px;">
                Periksa kesesuaian produk, alamat, kurir, dan nomor resi sebelum paket diserahkan ke ekspedisi.
            </p>
            <div style="text-align:center; width:190px;">
                <p class="print-muted" style="margin:0 0 46px;">Admin</p>
                <div style="border-top:1px solid #111827; padding-top:6px;">{{ $adminStoreName }}</div>
            </div>
        </div>
    </section>

    <div class="mx-auto screen-order-detail">

        {{-- ── Page Header ── --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">
                    Detail Pesanan
                    <span class="text-brand-primary font-mono ml-2">{{ $order->order_number }}</span>
                </h1>
                <nav class="text-xs md:text-sm text-gray-400 font-medium mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-brand-primary transition-colors">Dashboard</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                        <li><a href="{{ route('orders.index') }}"
                                class="hover:text-brand-primary transition-colors">Pesanan</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                        <li class="text-brand-dark">{{ $order->order_number }}</li>
                    </ol>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" title="Print halaman pesanan"
                    class="px-5 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-print text-brand-primary"></i>
                    <span class="hidden sm:inline">Print</span>
                </button>
            </div>
        </div>

        {{-- ── Status Timeline ── --}}
        @php
            $timeline = ['pending', 'processing', 'shipped', 'delivered'];
            $statusIndex = array_search($order->status, $timeline);
            if ($statusIndex === false && $order->status === 'confirmed') $statusIndex = 1;
            $isCancelled = in_array($order->status, ['cancelled', 'refunded']);
        @endphp
        <div class="bg-white rounded-3xl border border-gray-50 shadow-sm px-6 py-5 mb-6">
            @if($isCancelled)
                <div class="flex items-center gap-3 text-sm font-semibold text-red-500">
                    <div class="w-10 h-10 rounded-2xl bg-red-50 flex items-center justify-center">
                        <i class="fa-solid fa-circle-xmark text-xl"></i>
                    </div>
                    <div>
                        <p class="font-black text-base">Pesanan {{ ucfirst($order->status) }}</p>
                        <p class="text-[11px] text-gray-400 font-normal">Pesanan ini tidak dapat diproses lebih lanjut.</p>
                    </div>
                </div>
            @else
                <p class="text-[10px] font-black text-gray-400 tracking-widest mb-4">PROGRESS PESANAN</p>
                <div class="relative flex items-center justify-between">
                    <div class="absolute left-0 right-0 top-5 h-1 bg-gray-100 rounded-full -z-0">
                        <div class="h-full bg-brand-primary rounded-full transition-all duration-700"
                            style="width: {{ $statusIndex !== false ? ($statusIndex / (count($timeline) - 1)) * 100 : 0 }}%">
                        </div>
                    </div>
                    @foreach($timeline as $idx => $step)
                        @php
                            $stepCfg = [
                                'pending'    => ['icon' => 'fa-hourglass-half',     'label' => 'Pending'],
                                'processing' => ['icon' => 'fa-gear',               'label' => 'Diproses'],
                                'shipped'    => ['icon' => 'fa-truck',              'label' => 'Dikirim'],
                                'delivered'  => ['icon' => 'fa-house-circle-check', 'label' => 'Terkirim'],
                            ];
                            $done = $statusIndex !== false && $idx <= $statusIndex;
                        @endphp
                        <div class="flex flex-col items-center gap-2 z-10">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center text-sm transition-all
                                                                                        {{ $done ? 'bg-brand-primary text-white shadow-md shadow-brand-primary/30' : 'bg-white border-2 border-gray-200 text-gray-300' }}">
                                <i class="fa-solid {{ $stepCfg[$step]['icon'] }} text-[13px]"></i>
                            </div>
                            <span
                                class="text-[10px] font-black tracking-widest text-center {{ $done ? 'text-brand-primary' : 'text-gray-300' }}">
                                {{ strtoupper($stepCfg[$step]['label']) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Main Grid ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: 2/3 --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- ── Order Items ── --}}
                <div class="bg-white rounded-3xl border border-gray-50 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary text-sm">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <h2 class="font-extrabold text-brand-dark">Item Pesanan</h2>
                        <span
                            class="ml-auto text-[10px] font-black text-gray-400 tracking-widest">{{ $order->items->count() }}
                            PRODUK</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($order->items as $item)
                            @php $primaryImg = $item->product?->images->firstWhere('is_primary', true) ?? $item->product?->images->first(); @endphp
                            <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors">
                                {{-- Image --}}
                                @if($primaryImg)
                                    <img src="{{ asset('storage/' . $primaryImg->image_url) }}"
                                        class="w-14 h-14 rounded-2xl object-cover border border-gray-100 shadow-sm flex-shrink-0">
                                @else
                                    <div
                                        class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 border border-dashed border-gray-200 flex-shrink-0">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-brand-dark text-sm truncate">{{ $item->product_name }}</p>
                                    @if($item->variant_name)
                                        <p class="text-[10px] text-blue-500 font-semibold mt-0.5 flex items-center gap-1">
                                            <i class="fa-solid fa-layer-group text-[8px]"></i> {{ $item->variant_name }}
                                        </p>
                                    @endif
                                    <p class="text-[11px] text-gray-400 mt-0.5">Rp
                                        {{ number_format($item->price, 0, ',', '.') }} × {{ $item->qty }}
                                    </p>
                                    @if($item->note)
                                        <p class="mt-1.5 flex items-start gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1.5 text-[11px] text-amber-700">
                                            <i class="fa-regular fa-note-sticky mt-px flex-shrink-0"></i>
                                            {{ $item->note }}
                                        </p>
                                    @endif
                                </div>
                                {{-- Subtotal --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="font-extrabold text-brand-dark">Rp
                                        {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                    @if($item->product)
                                        <a href="{{ route('collections.show', $item->product->slug) }}" target="_blank" class="text-[10px] text-brand-primary font-bold hover:underline">
                                            Lihat Produk →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Price Summary --}}
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50 space-y-2">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span class="font-semibold">Subtotal</span>
                            <span class="font-bold text-brand-dark">Rp
                                {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span class="font-semibold">Ongkos Kirim</span>
                            <span class="font-bold text-brand-dark">Rp
                                {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="font-semibold text-green-600">Diskon
                                    @if($order->coupon)
                                        <span
                                            class="text-[10px] font-mono bg-green-50 px-1.5 py-0.5 rounded-md">{{ $order->coupon->code }}</span>
                                    @endif
                                </span>
                                <span class="font-bold text-green-600">- Rp
                                    {{ number_format($order->discount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-gray-200">
                            <span class="font-black text-brand-dark">TOTAL</span>
                            <span class="font-extrabold text-brand-primary text-lg">Rp
                                {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- ── Payment Info ── --}}
                @if($order->payment)
                        <div class="bg-white rounded-3xl border border-gray-50 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center text-green-500 text-sm">
                                    <i class="fa-solid fa-credit-card"></i>
                                </div>
                                <h2 class="font-extrabold text-brand-dark">Informasi Pembayaran</h2>
                                @php $p = $order->payment; @endphp
                                <span
                                    class="ml-auto px-3 py-1 rounded-full text-[10px] font-black tracking-wider
                                                                                    {{ $p->status === 'success' ? 'bg-green-50 text-green-600' :
                    ($p->status === 'pending' ? 'bg-amber-50 text-amber-600' :
                        ($p->status === 'under_review' ? 'bg-sky-50 text-sky-600' :
                            ($p->status === 'failed' ? 'bg-red-50 text-red-600' :
                                ($p->status === 'expired' ? 'bg-gray-100 text-gray-500' : 'bg-purple-50 text-purple-600')))) }}">
                                    {{ $p->status === 'under_review' ? 'Menunggu Review' : ucfirst($p->status) }}
                                </span>
                            </div>
                            <div class="px-6 py-5 grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">METODE</p>
                                    <p class="font-bold text-brand-dark mt-1">{{ strtoupper($p->payment_method ?? '-') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">JUMLAH</p>
                                    <p class="font-bold text-brand-dark mt-1">Rp {{ number_format($p->amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">DIBAYAR</p>
                                    <p class="font-bold text-brand-dark mt-1">
                                        {{ $p->paid_at ? $p->paid_at->format('d M Y H:i') : '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">MIDTRANS ORDER ID</p>
                                    <p class="font-mono text-xs text-gray-600 mt-1 break-all">{{ $p->midtrans_order_id }}</p>
                                </div>
                                @if($p->midtrans_transaction_id)
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 tracking-widest">TRANSACTION ID</p>
                                        <p class="font-mono text-xs text-gray-600 mt-1 break-all">{{ $p->midtrans_transaction_id }}</p>
                                    </div>
                                @endif
                                @if($p->expired_at)
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 tracking-widest">EXPIRED</p>
                                        <p class="font-bold text-{{ now()->gt($p->expired_at) ? 'red' : 'gray' }}-500 mt-1">
                                            {{ $p->expired_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">CHANNEL</p>
                                    <p class="font-bold text-brand-dark mt-1">{{ strtoupper($p->payment_channel ?? '-') }}</p>
                                </div>
                            </div>

                            @if($p->payment_channel === 'manual')
                                <div class="px-6 pb-6">
                                    <div class="rounded-3xl border border-gray-100 bg-gray-50/70 p-5">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="space-y-3">
                                                <div>
                                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">BUKTI TRANSFER</p>
                                                    <p class="text-sm text-brand-dark font-semibold mt-1">Pembayaran manual menunggu verifikasi admin.</p>
                                                </div>
                                                @if($p->proof_image)
                                                    <div class="space-y-3">
                                                        <img src="{{ asset('storage/' . $p->proof_image) }}" alt="Bukti transfer"
                                                            class="h-44 w-full max-w-sm rounded-2xl object-cover border border-gray-100">
                                                        <a href="{{ asset('storage/' . $p->proof_image) }}" target="_blank" rel="noopener noreferrer"
                                                            class="inline-flex items-center gap-2 rounded-2xl bg-sky-50 px-4 py-2 text-xs font-black text-sky-700 transition hover:bg-sky-100">
                                                            <i class="fa-regular fa-eye"></i>
                                                            Lihat Bukti
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-4 py-8 text-center text-sm text-gray-400">
                                                        Bukti transfer belum diupload customer.
                                                    </div>
                                                @endif
                                            </div>

                                            @if($p->status === 'under_review')
                                                <form action="{{ route('orders.payment-review', $order->id) }}" method="POST" class="w-full max-w-md space-y-3 bg-white rounded-3xl border border-gray-100 p-5 shadow-sm">
                                                    @csrf
                                                    <div>
                                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Catatan Admin</label>
                                                        <textarea name="note" rows="3" placeholder="Opsional, misalnya nominal kurang atau bukti tidak jelas"
                                                            class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50/60 px-4 py-3 text-sm outline-none focus:border-brand-primary resize-none"></textarea>
                                                    </div>
                                                    <div class="flex flex-col gap-3 sm:flex-row">
                                                        <button type="submit" name="action" value="reject"
                                                            class="flex-1 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-black text-red-600 hover:bg-red-100 transition-all">
                                                            Tolak
                                                        </button>
                                                        <button type="submit" name="action" value="approve"
                                                            class="flex-1 rounded-2xl bg-brand-primary px-4 py-3 text-sm font-black text-white hover:bg-brand-dark transition-all">
                                                            Approve
                                                        </button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                @endif

                {{-- ── Shipment Info ── --}}
                @if($order->shipment)
                    <div class="bg-white rounded-3xl border border-gray-50 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-500 text-sm">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                            <h2 class="font-extrabold text-brand-dark">Informasi Pengiriman</h2>
                            @php $ship = $order->shipment; @endphp
                            <span id="shipmentStatusBadge" class="ml-auto px-3 py-1 rounded-full text-[10px] font-black tracking-wider {{ $ship->status_badge_class }}">
                                {{ $ship->status_label }}
                            </span>
                        </div>
                        <div class="px-6 py-5">
                            @php
                                $biteshipPayload = is_string($ship->biteship_payload) ? json_decode($ship->biteship_payload, true) : ($ship->biteship_payload ?? []);
                                $biteshipCourier = $biteshipPayload['courier'] ?? [];
                                $biteshipWaybill = $biteshipPayload['courier_waybill_id']
                                    ?? $biteshipPayload['waybill_id']
                                    ?? $biteshipPayload['waybill_number']
                                    ?? ($biteshipCourier['waybill_id'] ?? null)
                                    ?? ($biteshipCourier['waybill_number'] ?? null)
                                    ?? $ship->resi;
                                $biteshipTrackingId = $biteshipPayload['courier_tracking_id']
                                    ?? $biteshipPayload['tracking_id']
                                    ?? ($biteshipCourier['tracking_id'] ?? null)
                                    ?? null;
                                $biteshipTrackingId = $biteshipTrackingId
                                    ?? (($biteshipPayload['object'] ?? null) === 'tracking' ? ($biteshipPayload['id'] ?? null) : null)
                                    ?? (($biteshipPayload['data']['object'] ?? null) === 'tracking' ? ($biteshipPayload['data']['id'] ?? null) : null);
                                $biteshipTrackingLink = $biteshipPayload['courier_link']
                                    ?? $biteshipPayload['link']
                                    ?? ($biteshipTrackingId ? 'https://track.biteship.com/' . $biteshipTrackingId : null);
                                $driverName = $biteshipPayload['courier_driver_name']
                                    ?? ($biteshipCourier['driver_name'] ?? null);
                                $driverPhone = $biteshipPayload['courier_driver_phone']
                                    ?? ($biteshipCourier['driver_phone'] ?? null);
                                $driverPlate = $biteshipPayload['courier_driver_plate_number']
                                    ?? ($biteshipCourier['driver_plate_number'] ?? null);
                                $driverPhoto = $biteshipPayload['courier_driver_photo_url']
                                    ?? ($biteshipCourier['driver_photo_url'] ?? null);
                                $biteshipPrice = $biteshipPayload['order_price']
                                    ?? $biteshipPayload['price']
                                    ?? $biteshipPayload['shipping_price']
                                    ?? null;
                                $biteshipService = $biteshipPayload['courier_type']
                                    ?? ($biteshipCourier['type'] ?? null)
                                    ?? $ship->service;
                                $biteshipWeight = $biteshipPayload['weight']
                                    ?? $biteshipPayload['total_weight']
                                    ?? $ship->total_weight;
                                $originAddress = $biteshipPayload['origin']['address'] ?? null;
                                $destinationAddress = $biteshipPayload['destination']['address'] ?? null;
                            @endphp
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-5">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">KURIR</p>
                                    <p class="font-bold text-brand-dark mt-1">{{ strtoupper($ship->courier) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">LAYANAN</p>
                                    <p class="font-bold text-brand-dark mt-1">{{ $ship->service ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">ESTIMASI</p>
                                    <p class="font-bold text-brand-dark mt-1">{{ $ship->estimated_days ?? '-' }} hari</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">BIAYA</p>
                                    <p class="font-bold text-brand-dark mt-1">Rp {{ number_format($ship->cost, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">BERAT</p>
                                    <p class="font-bold text-brand-dark mt-1">{{ number_format($ship->total_weight ?? 10, 0, ',', '.') }} gram</p>
                                </div>
                            </div>

                            @if($ship->resi)
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3 bg-cyan-50 rounded-2xl px-4 py-3 mb-5">
                                    <i class="fa-solid fa-barcode text-cyan-500 text-lg"></i>
                                    <div class="flex-1">
                                        <p class="text-[9px] font-black text-cyan-400 tracking-widest">NOMOR RESI</p>
                                        <p class="font-extrabold text-cyan-700 font-mono text-sm">{{ $ship->resi }}</p>
                                        <p id="trackedAt" class="text-[10px] text-cyan-500 mt-1">
                                            @if($ship->tracked_at) Update {{ $ship->tracked_at->format('d M Y H:i') }} @endif
                                        </p>
                                    </div>
                                    <div class="flex flex-wrap gap-2 sm:ml-auto">
                                        <button type="button" onclick="openLabelModal()" title="Download label resi PDF"
                                            class="px-3 py-1.5 bg-brand-primary text-white text-[10px] font-black rounded-xl hover:bg-brand-dark transition-all flex items-center gap-1">
                                            <i class="fa-solid fa-download mr-1"></i>Download Resi
                                        </button>
                                        @if($ship->label_url)
                                            <a href="{{ route('orders.biteship-label.download', $order->id) }}" title="Download label resmi dari Biteship"
                                                class="px-3 py-1.5 bg-white border border-cyan-200 text-cyan-600 text-[10px] font-black rounded-xl hover:bg-cyan-500 hover:text-white transition-all flex items-center gap-1">
                                                <i class="fa-solid fa-download mr-1"></i>Label Biteship
                                            </a>
                                        @endif
                                        <button onclick="copyResi('{{ $ship->resi }}')" title="Salin nomor resi ke clipboard"
                                            class="px-3 py-1.5 bg-white border border-cyan-200 text-cyan-600 text-[10px] font-black rounded-xl hover:bg-cyan-500 hover:text-white transition-all">
                                            <i class="fa-solid fa-copy mr-1"></i>Salin
                                        </button>
                                        <button id="btnLacak" type="button" onclick="lacakResi({{ $order->id }})" title="Lacak status pengiriman via Biteship"
                                            class="px-3 py-1.5 bg-cyan-500 text-white text-[10px] font-black rounded-xl hover:bg-brand-dark transition-all">
                                            <i class="fa-solid fa-location-crosshairs mr-1"></i>Lacak
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if($ship->biteship_order_id || !empty($biteshipPayload))
                                <div class="mb-5 grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-4">
                                    <div class="rounded-3xl border border-gray-100 bg-gray-50/70 p-5">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-9 h-9 rounded-2xl bg-white text-cyan-500 flex items-center justify-center">
                                                <i class="fa-solid fa-clipboard-list"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-extrabold text-brand-dark">Detail Biteship</p>
                                                <p class="text-[10px] text-gray-400 font-bold">Data dari order dan webhook Biteship</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3">
                                                <p class="text-[9px] font-black text-gray-400 tracking-widest">ORDER ID Biteship</p>
                                                <p class="mt-1 font-mono text-xs font-bold text-brand-dark break-all">{{ $ship->biteship_order_id ?? '-' }}</p>
                                            </div>
                                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3">
                                                <p class="text-[9px] font-black text-gray-400 tracking-widest">TRACKING ID</p>
                                                <p class="mt-1 font-mono text-xs font-bold text-brand-dark break-all">{{ $biteshipTrackingId ?? '-' }}</p>
                                            </div>
                                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3">
                                                <p class="text-[9px] font-black text-gray-400 tracking-widest">WAYBILL</p>
                                                <p class="mt-1 font-mono text-xs font-bold text-brand-dark break-all">{{ $biteshipWaybill ?? '-' }}</p>
                                            </div>
                                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3">
                                                <p class="text-[9px] font-black text-gray-400 tracking-widest">LAYANAN Biteship</p>
                                                <p class="mt-1 text-xs font-bold text-brand-dark">{{ strtoupper($ship->courier) }} {{ strtoupper($biteshipService ?? '-') }}</p>
                                            </div>
                                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3">
                                                <p class="text-[9px] font-black text-gray-400 tracking-widest">BOBOT</p>
                                                <p class="mt-1 text-xs font-bold text-brand-dark">{{ number_format($biteshipWeight ?? 0, 0, ',', '.') }} gram</p>
                                            </div>
                                            <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3">
                                                <p class="text-[9px] font-black text-gray-400 tracking-widest">BIAYA Biteship</p>
                                                <p class="mt-1 text-xs font-bold text-brand-dark">{{ $biteshipPrice ? 'Rp ' . number_format($biteshipPrice, 0, ',', '.') : '-' }}</p>
                                            </div>
                                        </div>
                                        @if($originAddress || $destinationAddress)
                                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3">
                                                    <p class="text-[9px] font-black text-gray-400 tracking-widest">ORIGIN</p>
                                                    <p class="mt-1 text-xs font-semibold text-gray-600 leading-relaxed">{{ $originAddress ?? '-' }}</p>
                                                </div>
                                                <div class="rounded-2xl bg-white border border-gray-100 px-4 py-3">
                                                    <p class="text-[9px] font-black text-gray-400 tracking-widest">DESTINATION</p>
                                                    <p class="mt-1 text-xs font-semibold text-gray-600 leading-relaxed">{{ $destinationAddress ?? '-' }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="rounded-3xl border border-cyan-100 bg-cyan-50/60 p-5">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-12 h-12 rounded-2xl bg-white overflow-hidden flex items-center justify-center text-cyan-500">
                                                @if($driverPhoto)
                                                    <img src="{{ $driverPhoto }}" alt="{{ $driverName ?? 'Kurir' }}" class="w-full h-full object-cover">
                                                @else
                                                    <i class="fa-solid fa-user-helmet-safety text-lg"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-extrabold text-brand-dark">Identitas Kurir</p>
                                                <p class="text-[10px] text-cyan-600 font-bold">{{ $driverName ? 'Kurir sudah ditugaskan' : 'Belum ada data driver' }}</p>
                                            </div>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="rounded-2xl bg-white border border-cyan-100 px-4 py-3">
                                                <p class="text-[9px] font-black text-cyan-400 tracking-widest">NAMA DRIVER</p>
                                                <p class="mt-1 text-sm font-bold text-brand-dark">{{ $driverName ?? '-' }}</p>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div class="rounded-2xl bg-white border border-cyan-100 px-4 py-3">
                                                    <p class="text-[9px] font-black text-cyan-400 tracking-widest">TELEPON</p>
                                                    @if($driverPhone ?? null)
                                                        @php $waPhone = 'https://wa.me/' . preg_replace('/^0/', '62', preg_replace('/\D/', '', $driverPhone)); @endphp
                                                        <a href="{{ $waPhone }}" target="_blank"
                                                            class="mt-1 flex items-center gap-1.5 text-xs font-bold text-green-600 hover:text-green-700">
                                                            <i class="fa-brands fa-whatsapp"></i> {{ $driverPhone }}
                                                        </a>
                                                    @else
                                                        <p class="mt-1 text-xs font-bold text-brand-dark">-</p>
                                                    @endif
                                                </div>
                                                <div class="rounded-2xl bg-white border border-cyan-100 px-4 py-3">
                                                    <p class="text-[9px] font-black text-cyan-400 tracking-widest">PLAT NOMOR</p>
                                                    <p class="mt-1 text-xs font-bold text-brand-dark">{{ $driverPlate ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                                @if($ship->label_url)
                                                    <a href="{{ route('orders.biteship-label.download', $order->id) }}"
                                                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white border border-cyan-200 px-4 py-3 text-xs font-black text-cyan-600 hover:bg-cyan-500 hover:text-white transition-all">
                                                        <i class="fa-solid fa-download"></i>
                                                        Download Label
                                                    </a>
                                                    <a href="{{ $ship->label_url }}" target="_blank"
                                                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white border border-cyan-200 px-4 py-3 text-xs font-black text-cyan-600 hover:bg-cyan-500 hover:text-white transition-all">
                                                        <i class="fa-solid fa-up-right-from-square"></i>
                                                        Label Biteship
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Generate resi via Biteship --}}
                            @if(!$ship->resi && $order->status === 'processing')
                                <form action="{{ route('orders.biteship-waybill', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" title="Generate nomor resi otomatis via Biteship"
                                        class="px-5 py-3 bg-cyan-500 text-white rounded-2xl text-xs font-black tracking-widest hover:bg-brand-dark transition-all">
                                        <i class="fa-solid fa-wand-magic-sparkles mr-1"></i>Generate Resi Biteship
                                    </button>
                                </form>
                            @endif

                            {{-- Tracking History --}}
                            <div id="trackingSection">
                            @if($ship->tracking_history)
                                @php
                                    $trackingPayload = is_string($ship->tracking_history) ? json_decode($ship->tracking_history, true) : $ship->tracking_history;
                                    $history = array_reverse($trackingPayload['history'] ?? $trackingPayload['manifest'] ?? $trackingPayload ?? []);
                                @endphp
                                @if(count($history) > 0)
                                    <div class="mt-4">
                                        <p class="text-[10px] font-black text-gray-400 tracking-widest mb-3">RIWAYAT TRACKING</p>
                                        <div class="relative space-y-3 before:absolute before:left-4 before:top-0 before:bottom-0 before:w-px before:bg-gray-100">
                                            @foreach($history as $i => $track)
                                                @php
                                                    $description = $track['note'] ?? $track['description'] ?? $track['manifest_description'] ?? '-';
                                                    $timestamp = $track['updated_at'] ?? trim(($track['manifest_date'] ?? $track['date'] ?? '') . ' ' . ($track['manifest_time'] ?? $track['time'] ?? ''));
                                                    $displayDateTime = filled($timestamp) ? \Carbon\Carbon::parse($timestamp)->format('d M Y H:i:s') : '';
                                                @endphp
                                                <div class="flex items-start gap-4 pl-10 relative">
                                                    <div class="absolute left-2.5 top-1 w-3 h-3 rounded-full {{ $i === 0 ? 'bg-brand-primary' : 'bg-gray-300' }} border-2 border-white shadow-sm"></div>
                                                    <div class="flex-1">
                                                        <p class="font-bold text-sm {{ $i === 0 ? 'text-brand-dark' : 'text-gray-600' }}">{{ $description }}</p>
                                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                                            {{ $displayDateTime }}
                                                            @if(!empty($track['city_name'] ?? $track['location'] ?? '')) — {{ $track['city_name'] ?? $track['location'] }} @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- ── Reviews ── --}}
                @if($order->reviews && $order->reviews->count())
                    <div class="bg-white rounded-3xl border border-gray-50 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 text-sm">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <h2 class="font-extrabold text-brand-dark">Ulasan Produk</h2>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($order->reviews as $review)
                                <div class="px-6 py-5">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <p class="font-bold text-brand-dark text-sm">{{ $review->product->name ?? '-' }}</p>
                                            <div class="flex items-center gap-1 mt-1">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <i
                                                        class="fa-solid fa-star text-[11px] {{ $s <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                                @endfor
                                                <span
                                                    class="text-[10px] text-gray-400 font-bold ml-1">{{ $review->rating }}/5</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($review->is_verified)
                                                <span
                                                    class="px-2 py-1 bg-green-50 text-green-600 text-[9px] font-black rounded-lg tracking-widest">TERVERIFIKASI</span>
                                            @else
                                                <button onclick="verifyReview({{ $review->id }})" title="Tandai ulasan sebagai terverifikasi"
                                                    class="px-3 py-1 bg-gray-50 text-gray-500 text-[9px] font-black rounded-lg hover:bg-brand-primary hover:text-white transition-all">
                                                    Verifikasi
                                                </button>
                                            @endif
                                            <span
                                                class="text-[10px] text-gray-400">{{ $review->created_at->format('d M Y') }}</span>
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-sm text-gray-600 leading-relaxed mt-2">{{ $review->comment }}</p>
                                    @endif
                                    @if($review->images)
                                        @php $reviewImgs = is_string($review->images) ? json_decode($review->images, true) : $review->images; @endphp
                                        @if(is_array($reviewImgs) && count($reviewImgs))
                                            <div class="flex gap-2 mt-3 flex-wrap">
                                                @foreach($reviewImgs as $img)
                                                    <img src="{{ asset('storage/' . $img) }}"
                                                        class="w-16 h-16 rounded-xl object-cover border border-gray-100">
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- RIGHT: 1/3 --}}
            <div class="space-y-6">

                {{-- ── Customer Info ── --}}
                <div class="bg-white rounded-3xl border border-gray-50 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary text-sm">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h3 class="font-extrabold text-brand-dark">Pelanggan</h3>
                    </div>
                    <div class="px-5 py-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-brand-primary/10 flex items-center justify-center text-brand-primary font-extrabold text-lg">
                                {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-extrabold text-brand-dark">{{ $order->user->name ?? '-' }}</p>
                                <p class="text-[11px] text-gray-400">{{ $order->user->email ?? '' }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            @if($order->user?->phone ?? null)
                                <div class="flex items-center gap-2 text-gray-500">
                                    <i class="fa-solid fa-phone text-[11px] text-brand-primary w-4"></i>
                                    {{ $order->user->phone }}
                                </div>
                            @endif
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fa-solid fa-calendar text-[11px] text-brand-primary w-4"></i>
                                Bergabung {{ $order->user?->created_at?->format('M Y') ?? '-' }}
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="/customers?search={{ urlencode($order->user?->email ?? '') }}"
                                class="block text-center py-2.5 bg-brand-primary/5 text-brand-primary text-xs font-black rounded-2xl hover:bg-brand-primary hover:text-white transition-all tracking-widest">
                                Lihat Profil
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ── Shipping Address ── --}}
                @if($shippingAddr)
                <div class="bg-white rounded-3xl border border-gray-50 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 text-sm">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                            <h3 class="font-extrabold text-brand-dark">Alamat Pengiriman</h3>
                        </div>
                        <div class="px-5 py-4 space-y-1.5 text-sm">
                            <p class="font-extrabold text-brand-dark">{{ $shippingAddr->receiver_name }}</p>
                            <p class="text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-phone text-[10px] text-blue-400 w-3"></i>
                                {{ $shippingAddr->phone }}
                            </p>
                            <p class="text-gray-600 leading-relaxed mt-2">{{ $shippingAddr->address }}</p>
                            <p class="text-gray-500">
                                {{ $shippingAddr->subdistrict ? $shippingAddr->subdistrict . ', ' : '' }}
                                {{ $shippingAddr->district ? $shippingAddr->district . ', ' : '' }}
                                {{ $shippingAddr->city }}
                            </p>
                            <p class="text-gray-500">{{ $shippingAddr->province }} {{ $shippingAddr->postal_code }}</p>
                        </div>
                        @php
                            $customerLat = $shippingAddr->latitude ?? null;
                            $customerLng = $shippingAddr->longitude ?? null;
                        @endphp
                        <div class="border-t border-gray-50 px-5 py-4">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 tracking-widest">MAP LOKASI</p>
                                    <p class="text-xs text-gray-500 mt-1">Visualisasi alamat customer bila koordinat tersedia.</p>
                                </div>
                                @if($customerLat && $customerLng)
                                    <a href="https://www.google.com/maps?q={{ $customerLat }},{{ $customerLng }}" target="_blank" rel="noopener noreferrer"
                                        class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:underline">
                                        Buka Maps
                                    </a>
                                @endif
                            </div>
                            @if($customerLat && $customerLng)
                                <div id="customer-address-map-admin"
                                    class="order-address-map overflow-hidden rounded-2xl border border-gray-100 bg-gray-50"
                                    data-lat="{{ $customerLat }}"
                                    data-lng="{{ $customerLng }}"
                                    data-label="{{ $shippingAddr->receiver_name }}"
                                    data-address="{{ $shippingAddr->address }}">
                                </div>
                            @else
                                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-5 text-xs text-gray-400">
                                    Koordinat alamat belum tersedia, jadi map tidak bisa ditampilkan.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── Order Meta ── --}}
                <div class="bg-white rounded-3xl border border-gray-50 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 text-sm">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <h3 class="font-extrabold text-brand-dark">Info Pesanan</h3>
                    </div>
                    <div class="px-5 py-4 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-semibold">No. Pesanan</span>
                            <span class="font-black text-brand-primary font-mono">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-semibold">Tanggal</span>
                            <span class="font-semibold text-gray-700">{{ $order->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-semibold">Diperbarui</span>
                            <span class="font-semibold text-gray-700">{{ $order->updated_at->diffForHumans() }}</span>
                        </div>
                        @if($order->coupon)
                            <div class="flex justify-between">
                                <span class="text-gray-400 font-semibold">Kupon</span>
                                <span class="font-black text-green-600 font-mono">{{ $order->coupon->code }}</span>
                            </div>
                        @endif
                        @if($order->notes)
                            <div class="pt-2 border-t border-gray-50">
                                <p class="text-[10px] font-black text-gray-400 tracking-widest mb-1">CATATAN</p>
                                <p class="text-gray-600 text-xs leading-relaxed">{{ $order->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── Quick Actions ── --}}
                <div class="bg-white rounded-3xl border border-gray-50 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50">
                        <h3 class="font-extrabold text-brand-dark">Aksi Cepat</h3>
                    </div>
                    <div class="px-5 py-4 space-y-2">
                        @if(in_array($order->status, ['pending', 'confirmed']))
                            <button onclick="quickStatus({{ $order->id }}, 'processing')" title="Ubah status ke Diproses"
                                class="w-full py-3 bg-indigo-50 text-indigo-600 rounded-2xl text-xs font-black tracking-widest hover:bg-indigo-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-gear"></i> Proses Pesanan
                            </button>
                        @endif
                        @if($order->status === 'shipped')
                            <button onclick="quickStatus({{ $order->id }}, 'delivered')" title="Tandai pesanan sudah diterima customer"
                                class="w-full py-3 bg-green-50 text-green-600 rounded-2xl text-xs font-black tracking-widest hover:bg-green-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-house-circle-check"></i> Tandai Terkirim
                            </button>
                        @endif
                        @if(in_array($order->status, ['pending', 'confirmed', 'processing']))
                            <button onclick="cancelOrder({{ $order->id }})" title="Batalkan pesanan dan kembalikan stok"
                                class="w-full py-3 bg-red-50 text-red-500 rounded-2xl text-xs font-black tracking-widest hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-ban"></i> Batalkan Pesanan
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
    DOWNLOAD LABEL MODAL
    ══════════════════════════════════════ --}}
    <div id="labelModal"
        class="fixed inset-0 hidden bg-black/40 backdrop-blur-sm items-center justify-center z-[110] p-4">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-brand-dark">Download Label Resi</h3>
                    <p class="text-[11px] text-gray-400 font-bold mt-0.5 tracking-widest">{{ $order->order_number }}</p>
                </div>
                <button onclick="closeLabelModal()"
                    class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="labelForm" class="p-7 space-y-6">

                {{-- Isi Detail --}}
                <div>
                    <p class="text-sm font-black text-gray-700 mb-4">Isi Detail Resi</p>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                        @foreach([
                            'shipping_cost'      => 'Ongkos Kirim',
                            'item_description'   => 'Deskripsi Barang',
                            'sender_phone'       => 'No. Telp Pengirim',
                            'sender_address'     => 'Alamat Pengirim',
                            'receiver_phone'     => 'No. Telp Penerima',
                            'mask_receiver_name' => 'Sensor Nama Penerima',
                        ] as $key => $lbl)
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" name="{{ $key }}" value="1"
                                    {{ in_array($key, ['mask_receiver_name']) ? '' : 'checked' }}
                                    class="w-5 h-5 rounded-md border-gray-300 text-brand-primary focus:ring-brand-primary/30 cursor-pointer">
                                <span class="text-sm font-bold text-gray-500 group-hover:text-brand-dark transition-colors">{{ $lbl }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Ukuran --}}
                <div>
                    <p class="text-sm font-black text-gray-700 mb-3">Tipe Label</p>
                    <select name="paper_size"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-200 text-sm font-bold text-gray-600 bg-white focus:outline-none focus:border-brand-primary appearance-none cursor-pointer">
                        <option value="a4">Default (A4 — 21 × 29.7 cm)</option>
                        <option value="thermal1">Thermal 1 (8 × 10 cm)</option>
                        <option value="thermal2">Thermal 2 (10 × 15 cm)</option>
                    </select>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-4 rounded-2xl bg-brand-primary text-white font-black text-base hover:bg-brand-dark transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-download"></i>
                    Download
                </button>

            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
    STATUS MODAL
    ══════════════════════════════════════ --}}
    <div id="statusModal"
        class="fixed inset-0 hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center z-[100] p-4">
        <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden">

            <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-brand-dark">Ubah Status Pesanan</h3>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest">{{ $order->order_number }}</p>
                    </div>
                </div>
                <button onclick="closeStatusModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="statusForm" method="POST" action="{{ route('orders.status', $order->id) }}" class="p-7 space-y-4">
                @csrf @method('PATCH')
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 tracking-widest">STATUS PESANAN</label>
                    <select name="status" id="statusSelect"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none text-sm font-semibold appearance-none">
                        @foreach(['pending' => 'Pending', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Terkirim', 'cancelled' => 'Dibatalkan', 'refunded' => 'Refund'] as $val => $label)
                            <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 tracking-widest">CATATAN <span
                            class="text-gray-300">(Opsional)</span></label>
                    <textarea name="note" rows="2" placeholder="Catatan perubahan status..."
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none text-sm font-semibold resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeStatusModal()"
                        class="flex-1 py-3 rounded-2xl text-xs font-black tracking-widest text-gray-400 hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-2xl bg-brand-primary text-white text-xs font-black tracking-widest shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all">
                        <i class="fa-solid fa-check mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
        <script>
        // ── Lacak Resi (AJAX + shimmer) ───────────────────────────────────────
        function escHtml(str) {
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function formatTrackingTimestamp(value, fallbackDate = '', fallbackTime = '') {
            if (!value) {
                return `${fallbackDate} ${fallbackTime}`.trim();
            }

            const date = new Date(value);

            if (Number.isNaN(date.getTime())) {
                return String(value);
            }

            return `${date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })} ${date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })}`;
        }

        function shimmerTracking() {
            const rows = [1,2,3].map((_, i) => `
                <div class="flex items-start gap-4 pl-10 relative">
                    <div class="absolute left-2.5 top-1 w-3 h-3 rounded-full bg-gray-200 border-2 border-white"></div>
                    <div class="flex-1 space-y-1.5 py-0.5">
                        <div class="h-3 bg-gray-200 rounded-lg animate-pulse" style="width:${70 - i*10}%"></div>
                        <div class="h-2.5 bg-gray-100 rounded-lg animate-pulse" style="width:${45 - i*5}%"></div>
                    </div>
                </div>`).join('');
            return `<div class="mt-4">
                <div class="h-2.5 bg-gray-200 rounded animate-pulse w-32 mb-4"></div>
                <div class="relative space-y-4 before:absolute before:left-4 before:top-0 before:bottom-0 before:w-px before:bg-gray-100">
                    ${rows}
                </div>
            </div>`;
        }

        function renderTrackingTimeline(manifest) {
            if (!manifest || manifest.length === 0) {
                return '';
            }
            const timeline = [...manifest].reverse();
            const items = timeline.map((track, i) => {
                const description = track.note || track.description || track.manifest_description || '-';
                const timestamp   = formatTrackingTimestamp(track.updated_at, track.manifest_date || track.date || '', track.manifest_time || track.time || '');
                const city        = track.city_name || track.location || '';
                const dotCls      = i === 0 ? 'bg-brand-primary' : 'bg-gray-300';
                const textCls     = i === 0 ? 'text-brand-dark font-bold' : 'text-gray-600 font-semibold';
                return `<div class="flex items-start gap-4 pl-10 relative">
                    <div class="absolute left-2.5 top-1 w-3 h-3 rounded-full ${dotCls} border-2 border-white shadow-sm"></div>
                    <div class="flex-1">
                        <p class="text-sm ${textCls}">${escHtml(description)}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">${escHtml(timestamp)}${city ? ' &mdash; ' + escHtml(city) : ''}</p>
                    </div>
                </div>`;
            }).join('');
            return `<div class="mt-4">
                <p class="text-[10px] font-black text-gray-400 tracking-widest mb-3">RIWAYAT TRACKING</p>
                <div class="relative space-y-3 before:absolute before:left-4 before:top-0 before:bottom-0 before:w-px before:bg-gray-100">
                    ${items}
                </div>
            </div>`;
        }

        window.lacakResi = function (orderId) {
            const $btn = $('#btnLacak');
            $btn.prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin mr-1"></i>Melacak...');
            $('#trackingSection').html(shimmerTracking());

            $.ajax({
                url: `/orders/${orderId}/track`,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                success: function (data) {
                    // Update status badge
                    $('#shipmentStatusBadge')
                        .attr('class', 'ml-auto px-3 py-1 rounded-full text-[10px] font-black tracking-wider ' + data.status_badge_class)
                        .text(data.status_label);

                    // Update tracked_at
                    $('#trackedAt').text('Update ' + data.tracked_at);

                    // Render timeline
                    $('#trackingSection').html(renderTrackingTimeline(data.manifest));

                    Swal.fire({ icon: 'success', title: data.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Gagal memperbarui tracking.';
                    $('#trackingSection').html(`<div class="mt-4 p-4 bg-red-50 rounded-2xl text-sm text-red-600 font-semibold"><i class="fa-solid fa-triangle-exclamation mr-2"></i>${escHtml(msg)}</div>`);
                    Swal.fire({ icon: 'error', title: msg, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                },
                complete: function () {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-location-crosshairs mr-1"></i>Lacak');
                },
            });
        };
        // ─────────────────────────────────────────────────────────────────────

            // ── Label Download Modal ──────────────────────────────────────────
            window.openLabelModal = function () {
                $('#labelModal').removeClass('hidden').addClass('flex');
            }
            window.closeLabelModal = function () {
                $('#labelModal').addClass('hidden').removeClass('flex');
            }
            $('#labelModal').on('click', function (e) {
                if (e.target === this) closeLabelModal();
            });
            $('#labelForm').on('submit', function (e) {
                e.preventDefault();
                const params = new URLSearchParams(new FormData(this));
                window.location.href = "{{ route('orders.label.pdf', $order->id) }}?" + params.toString();
                closeLabelModal();
            });
            // ─────────────────────────────────────────────────────────────────

            window.openStatusModal = function () {
                $('#statusModal').removeClass('hidden').addClass('flex');
            }
            window.closeStatusModal = function () {
                $('#statusModal').addClass('hidden').removeClass('flex');
            }
            $('#statusModal').on('click', function (e) {
                if (e.target === this) $(this).addClass('hidden').removeClass('flex');
            });

            window.quickStatus = function (id, status) {
                Swal.fire({
                    title: 'Ubah Status?',
                    text: `Pesanan akan diubah ke status "${status}".`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2D5A27',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal',
                }).then(r => {
                    if (r.isConfirmed) {
                        $('<form>', { method: 'POST', action: `/orders/${id}/status` })
                            .append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }))
                            .append($('<input>', { type: 'hidden', name: '_method', value: 'PATCH' }))
                            .append($('<input>', { type: 'hidden', name: 'status', value: status }))
                            .appendTo('body').submit();
                    }
                });
            }

            window.cancelOrder = function (id) {
                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    text: 'Pesanan akan dibatalkan dan tidak bisa dikembalikan ke status sebelumnya.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Kembali',
                }).then(r => {
                    if (r.isConfirmed) {
                        $('<form>', { method: 'POST', action: `/orders/${id}/status` })
                            .append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }))
                            .append($('<input>', { type: 'hidden', name: '_method', value: 'PATCH' }))
                            .append($('<input>', { type: 'hidden', name: 'status', value: 'cancelled' }))
                            .appendTo('body').submit();
                    }
                });
            }

            window.copyResi = function (resi) {
                navigator.clipboard.writeText(resi).then(() => {
                    Swal.fire({ icon: 'success', title: 'Tersalin!', text: resi, timer: 1500, showConfirmButton: false });
                });
            }

            window.verifyReview = function (reviewId) {
                $.post(`/reviews/${reviewId}/verify`, { _token: '{{ csrf_token() }}' }, function () {
                    location.reload();
                });
            }

            @if($shippingAddr && $shippingAddr->latitude && $shippingAddr->longitude)
            (function () {
                const mapEl = document.getElementById('customer-address-map-admin');
                if (!mapEl || typeof L === 'undefined') return;

                const lat = parseFloat(mapEl.dataset.lat);
                const lng = parseFloat(mapEl.dataset.lng);
                if (Number.isNaN(lat) || Number.isNaN(lng)) return;

                const label = mapEl.dataset.label || 'Alamat Customer';
                const address = mapEl.dataset.address || '';

                const map = L.map('customer-address-map-admin', {
                    zoomControl: true,
                    scrollWheelZoom: false,
                }).setView([lat, lng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);

                const popupHtml = `
                    <div style="min-width:180px">
                        <div style="font-weight:700;margin-bottom:4px;">${escHtml(label)}</div>
                        <div style="font-size:12px;line-height:1.4;color:#475569;">${escHtml(address)}</div>
                    </div>
                `;

                L.marker([lat, lng]).addTo(map).bindPopup(popupHtml).openPopup();

                setTimeout(() => {
                    map.invalidateSize();
                }, 250);
            })();
            @endif
        </script>
    @endpush
@endsection
