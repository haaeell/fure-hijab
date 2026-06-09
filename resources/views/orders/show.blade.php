@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_number)

@section('content')
    @php
        $shippingAddr = $order->address->firstWhere('type', 'shipping');
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
                        <h1 style="font-size:20px; margin:0; color:#2D5A27; letter-spacing:.02em;">AL-HAYYA HIJAB</h1>
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
                <div style="border-top:1px solid #111827; padding-top:6px;">AL-HAYYA HIJAB</div>
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
                <button onclick="window.print()"
                    class="px-5 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-print text-brand-primary"></i>
                    <span class="hidden sm:inline">Print</span>
                </button>
                <button onclick="openStatusModal()"
                    class="px-5 py-3 bg-brand-primary text-white rounded-2xl font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span class="hidden sm:inline">Ubah Status</span>
                </button>
            </div>
        </div>

        {{-- ── Status Timeline ── --}}
        @php
            $timeline = ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
            $statusIndex = array_search($order->status, $timeline);
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
                                'pending' => ['icon' => 'fa-hourglass-half', 'label' => 'Pending'],
                                'confirmed' => ['icon' => 'fa-circle-check', 'label' => 'Dikonfirmasi'],
                                'processing' => ['icon' => 'fa-gear', 'label' => 'Diproses'],
                                'shipped' => ['icon' => 'fa-truck', 'label' => 'Dikirim'],
                                'delivered' => ['icon' => 'fa-house-circle-check', 'label' => 'Terkirim'],
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
                                </div>
                                {{-- Subtotal --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="font-extrabold text-brand-dark">Rp
                                        {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                    @if($item->product)
                                        <a href="#" class="text-[10px] text-brand-primary font-bold hover:underline">
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
                        ($p->status === 'failed' ? 'bg-red-50 text-red-600' :
                            ($p->status === 'expired' ? 'bg-gray-100 text-gray-500' : 'bg-purple-50 text-purple-600'))) }}">
                                    {{ ucfirst($p->status) }}
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
                            </div>
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
                            <span
                                class="ml-auto px-3 py-1 rounded-full text-[10px] font-black tracking-wider bg-cyan-50 text-cyan-600">
                                {{ ucfirst($ship->status) }}
                            </span>
                        </div>
                        <div class="px-6 py-5">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
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
                            </div>

                            @if($ship->resi)
                                <div class="flex items-center gap-3 bg-cyan-50 rounded-2xl px-4 py-3 mb-5">
                                    <i class="fa-solid fa-barcode text-cyan-500 text-lg"></i>
                                    <div>
                                        <p class="text-[9px] font-black text-cyan-400 tracking-widest">NOMOR RESI</p>
                                        <p class="font-extrabold text-cyan-700 font-mono text-sm">{{ $ship->resi }}</p>
                                    </div>
                                    <button onclick="copyResi('{{ $ship->resi }}')"
                                        class="ml-auto px-3 py-1.5 bg-white border border-cyan-200 text-cyan-600 text-[10px] font-black rounded-xl hover:bg-cyan-500 hover:text-white transition-all">
                                        <i class="fa-solid fa-copy mr-1"></i>Salin
                                    </button>
                                </div>
                            @endif

                            {{-- Resi input for admin --}}
                            @if(!$ship->resi && $order->status === 'processing')
                                <div class="flex gap-3">
                                    <input type="text" id="resiInput" placeholder="Masukkan nomor resi..."
                                        class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none text-sm font-semibold">
                                    <button onclick="saveResi({{ $order->id }})"
                                        class="px-5 py-3 bg-brand-primary text-white rounded-2xl text-xs font-black tracking-widest hover:bg-brand-dark transition-all">
                                        <i class="fa-solid fa-save mr-1"></i>Simpan Resi
                                    </button>
                                </div>
                            @endif

                            {{-- Tracking History --}}
                            @if($ship->tracking_history)
                                @php $history = is_string($ship->tracking_history) ? json_decode($ship->tracking_history, true) : $ship->tracking_history; @endphp
                                @if(count($history) > 0)
                                    <div class="mt-4">
                                        <p class="text-[10px] font-black text-gray-400 tracking-widest mb-3">RIWAYAT TRACKING</p>
                                        <div
                                            class="relative space-y-3 before:absolute before:left-4 before:top-0 before:bottom-0 before:w-px before:bg-gray-100">
                                            @foreach($history as $track)
                                                <div class="flex items-start gap-4 pl-10 relative">
                                                    <div
                                                        class="absolute left-2.5 top-1 w-3 h-3 rounded-full bg-brand-primary border-2 border-white shadow-sm">
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-bold text-sm text-brand-dark">{{ $track['description'] ?? '-' }}</p>
                                                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $track['date'] ?? '' }}
                                                            {{ $track['time'] ?? '' }} — {{ $track['location'] ?? '' }}
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
                                                <button onclick="verifyReview({{ $review->id }})"
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
                            <a href="#"
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
                        @if($order->status === 'pending')
                            <button onclick="quickStatus({{ $order->id }}, 'confirmed')"
                                class="w-full py-3 bg-blue-50 text-blue-600 rounded-2xl text-xs font-black tracking-widest hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-check"></i> Konfirmasi Pesanan
                            </button>
                        @endif
                        @if($order->status === 'confirmed')
                            <button onclick="quickStatus({{ $order->id }}, 'processing')"
                                class="w-full py-3 bg-indigo-50 text-indigo-600 rounded-2xl text-xs font-black tracking-widest hover:bg-indigo-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-gear"></i> Proses Pesanan
                            </button>
                        @endif
                        @if($order->status === 'processing')
                            <button onclick="openShipModal()"
                                class="w-full py-3 bg-cyan-50 text-cyan-600 rounded-2xl text-xs font-black tracking-widest hover:bg-cyan-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-truck"></i> Tandai Dikirim + Input Resi
                            </button>
                        @endif
                        @if($order->status === 'shipped')
                            <button onclick="quickStatus({{ $order->id }}, 'delivered')"
                                class="w-full py-3 bg-green-50 text-green-600 rounded-2xl text-xs font-black tracking-widest hover:bg-green-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-house-circle-check"></i> Tandai Terkirim
                            </button>
                        @endif
                        @if(in_array($order->status, ['pending', 'confirmed', 'processing']))
                            <button onclick="cancelOrder({{ $order->id }})"
                                class="w-full py-3 bg-red-50 text-red-500 rounded-2xl text-xs font-black tracking-widest hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-ban"></i> Batalkan Pesanan
                            </button>
                        @endif
                        <button onclick="window.print()"
                            class="w-full py-3 bg-gray-50 text-gray-500 rounded-2xl text-xs font-black tracking-widest hover:bg-gray-100 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-print"></i> Print Invoice
                        </button>
                    </div>
                </div>

            </div>
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
                        @foreach(['pending' => 'Pending', 'confirmed' => 'Dikonfirmasi', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'delivered' => 'Terkirim', 'cancelled' => 'Dibatalkan', 'refunded' => 'Refund'] as $val => $label)
                            <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="resiGroup" class="{{ $order->status === 'shipped' ? '' : 'hidden' }} space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 tracking-widest">NOMOR RESI</label>
                    <input type="text" name="resi" value="{{ $order->shipment?->resi }}"
                        placeholder="Masukkan nomor resi..."
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none text-sm font-semibold">
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

    {{-- ══════════════════════════════════════
    SHIP MODAL (input resi + shipped)
    ══════════════════════════════════════ --}}
    <div id="shipModal"
        class="fixed inset-0 hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center z-[100] p-4">
        <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden">
            <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-500">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-brand-dark">Input Resi & Kirim</h3>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest">{{ $order->order_number }}</p>
                    </div>
                </div>
                <button onclick="closeShipModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('orders.status', $order->id) }}" class="p-7 space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="shipped">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 tracking-widest">NOMOR RESI <span
                            class="text-red-400">*</span></label>
                    <input type="text" name="resi" required placeholder="Contoh: JNE12345678"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-cyan-100 focus:border-cyan-400 outline-none text-sm font-semibold">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 tracking-widest">CATATAN <span
                            class="text-gray-300">(Opsional)</span></label>
                    <textarea name="note" rows="2" placeholder="Catatan pengiriman..."
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-cyan-100 focus:border-cyan-400 outline-none text-sm font-semibold resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeShipModal()"
                        class="flex-1 py-3 rounded-2xl text-xs font-black tracking-widest text-gray-400 hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-2xl bg-cyan-500 text-white text-xs font-black tracking-widest shadow-lg shadow-cyan-200 hover:bg-cyan-600 transition-all">
                        <i class="fa-solid fa-truck mr-2"></i>Tandai Dikirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            window.openStatusModal = function () {
                $('#statusModal').removeClass('hidden').addClass('flex');
            }
            window.closeStatusModal = function () {
                $('#statusModal').addClass('hidden').removeClass('flex');
            }
            window.openShipModal = function () {
                $('#shipModal').removeClass('hidden').addClass('flex');
            }
            window.closeShipModal = function () {
                $('#shipModal').addClass('hidden').removeClass('flex');
            }

            $('#statusSelect').on('change', function () {
                $('#resiGroup').toggleClass('hidden', this.value !== 'shipped');
            });

            $('#statusModal, #shipModal').on('click', function (e) {
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

            window.saveResi = function (orderId) {
                const resi = $('#resiInput').val().trim();
                if (!resi) { Swal.fire({ icon: 'warning', title: 'Resi kosong', timer: 1500, showConfirmButton: false }); return; }
                $('<form>', { method: 'POST', action: `/orders/${orderId}/resi` })
                    .append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }))
                    .append($('<input>', { type: 'hidden', name: '_method', value: 'PATCH' }))
                    .append($('<input>', { type: 'hidden', name: 'resi', value: resi }))
                    .appendTo('body').submit();
            }
        </script>
    @endpush
@endsection
