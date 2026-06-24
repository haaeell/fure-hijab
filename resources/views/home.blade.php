@extends('layouts.app')

@section('content')

{{-- ── Header ──────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-8">
    <div>
        <h1 class="text-2xl font-extrabold text-brand-dark">Dashboard</h1>
        <p class="text-gray-400 text-sm mt-0.5">Ringkasan performa toko &mdash; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
    <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 bg-white border border-gray-100 px-4 py-2 rounded-2xl shadow-sm">
        <i class="fa-solid fa-circle text-green-400 text-[8px] animate-pulse"></i>
        Live &bull; diperbarui otomatis
    </div>
</div>

{{-- ── Row 1: Main KPIs ──────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-5">

    {{-- Revenue Bulan Ini --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full rounded-l-2xl bg-emerald-400"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            @php $grow = $salesGrowth; @endphp
            <span class="text-[10px] font-bold px-2 py-1 rounded-lg {{ $grow >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-500' }}">
                <i class="fa-solid fa-arrow-{{ $grow >= 0 ? 'up' : 'down' }} text-[8px]"></i>
                {{ number_format(abs($grow), 1) }}%
            </span>
        </div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Revenue Bulan Ini</p>
        <h3 class="text-xl font-extrabold text-brand-dark leading-tight">Rp {{ number_format($thisMonthSales, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400 mt-1">Total: <span class="font-bold text-gray-500">Rp {{ number_format($totalSales, 0, ',', '.') }}</span></p>
    </div>

    {{-- Orders Bulan Ini --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full rounded-l-2xl bg-blue-400"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            @php $og = $ordersGrowth; @endphp
            <span class="text-[10px] font-bold px-2 py-1 rounded-lg {{ $og >= 0 ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-500' }}">
                <i class="fa-solid fa-arrow-{{ $og >= 0 ? 'up' : 'down' }} text-[8px]"></i>
                {{ number_format(abs($og), 1) }}%
            </span>
        </div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Order Bulan Ini</p>
        <h3 class="text-xl font-extrabold text-brand-dark leading-tight">{{ number_format($thisMonthOrders) }}</h3>
        <p class="text-[10px] text-gray-400 mt-1">Total: <span class="font-bold text-gray-500">{{ number_format($totalOrders) }} order</span></p>
    </div>

    {{-- Customers --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full rounded-l-2xl bg-violet-400"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-violet-500">
                <i class="fa-solid fa-users"></i>
            </div>
            @if($newCustomersThisMonth > 0)
            <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-violet-50 text-violet-600">
                +{{ $newCustomersThisMonth }} baru
            </span>
            @endif
        </div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Customer</p>
        <h3 class="text-xl font-extrabold text-brand-dark leading-tight">{{ number_format($totalCustomers) }}</h3>
        <p class="text-[10px] text-gray-400 mt-1">{{ $newCustomersThisMonth }} daftar bulan ini</p>
    </div>

    {{-- Avg Order Value --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full rounded-l-2xl bg-amber-400"></div>
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-amber-50 text-amber-600">
                <i class="fa-solid fa-star text-[8px]"></i> {{ number_format($avgRating, 1) }}
            </span>
        </div>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Avg Order Value</p>
        <h3 class="text-xl font-extrabold text-brand-dark leading-tight">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</h3>
        <p class="text-[10px] text-gray-400 mt-1">Rating rata-rata <span class="font-bold text-gray-500">{{ number_format($avgRating, 1) }} / 5.0</span></p>
    </div>
</div>

{{-- ── Row 2: Operational KPIs ────────────────────────────────── --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

    <a href="{{ route('orders.index', ['status' => 'pending']) }}"
       class="bg-white rounded-2xl shadow-sm border border-amber-100 p-5 flex items-center gap-4 hover:border-amber-300 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 group-hover:bg-amber-100 transition-all flex-shrink-0">
            <i class="fa-solid fa-clock text-xl"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Menunggu Konfirmasi</p>
            <h3 class="text-2xl font-extrabold text-amber-600">{{ $pendingOrders }}</h3>
            <p class="text-[10px] text-gray-400">order pending &amp; dikonfirmasi</p>
        </div>
        <i class="fa-solid fa-chevron-right text-gray-300 ml-auto text-xs group-hover:text-amber-400 transition-all"></i>
    </a>

    <a href="{{ route('orders.index', ['status' => 'processing']) }}"
       class="bg-white rounded-2xl shadow-sm border border-blue-100 p-5 flex items-center gap-4 hover:border-blue-300 transition-all group">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 group-hover:bg-blue-100 transition-all flex-shrink-0">
            <i class="fa-solid fa-box-open text-xl"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sedang Diproses</p>
            <h3 class="text-2xl font-extrabold text-blue-600">{{ $processingOrders }}</h3>
            <p class="text-[10px] text-gray-400">diproses &amp; dalam pengiriman</p>
        </div>
        <i class="fa-solid fa-chevron-right text-gray-300 ml-auto text-xs group-hover:text-blue-400 transition-all"></i>
    </a>

    <a href="/products"
       class="bg-white rounded-2xl shadow-sm border {{ $lowStockCount > 0 ? 'border-red-100' : 'border-emerald-100' }} p-5 flex items-center gap-4 hover:border-{{ $lowStockCount > 0 ? 'red' : 'emerald' }}-300 transition-all group">
        <div class="w-12 h-12 rounded-xl {{ $lowStockCount > 0 ? 'bg-red-50 text-red-500' : 'bg-emerald-50 text-emerald-500' }} flex items-center justify-center group-hover:opacity-80 transition-all flex-shrink-0">
            <i class="fa-solid fa-boxes-stacked text-xl"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stok Rendah</p>
            <h3 class="text-2xl font-extrabold {{ $lowStockCount > 0 ? 'text-red-500' : 'text-emerald-500' }}">{{ $lowStockCount }}</h3>
            <p class="text-[10px] text-gray-400">produk stok &le; 5 unit</p>
        </div>
        <i class="fa-solid fa-chevron-right text-gray-300 ml-auto text-xs group-hover:text-gray-400 transition-all"></i>
    </a>
</div>

{{-- ── Row 3: Charts ───────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Revenue + Orders Trend --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-base font-extrabold text-brand-dark">Tren Penjualan</h2>
            <div class="flex items-center gap-4 text-[10px] font-bold text-gray-400">
                <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-brand-primary inline-block rounded"></span> Revenue</span>
                <span class="flex items-center gap-1"><span class="w-3 h-0.5 bg-blue-400 inline-block rounded"></span> Order</span>
            </div>
        </div>
        <p class="text-[10px] text-gray-400 mb-4">6 bulan terakhir</p>
        <div id="salesChart"></div>
    </div>

    {{-- Order Status Donut --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-extrabold text-brand-dark mb-1">Status Order</h2>
        <p class="text-[10px] text-gray-400 mb-4">Distribusi semua order</p>
        <div id="statusChart"></div>
        <div class="mt-4 space-y-2">
            @php
                $statusLabels = [
                    'pending'    => ['Menunggu',   '#F59E0B'],
                    'confirmed'  => ['Dikonfirmasi','#3B82F6'],
                    'processing' => ['Diproses',   '#6366F1'],
                    'shipped'    => ['Dikirim',    '#F97316'],
                    'delivered'  => ['Terkirim',   '#10B981'],
                    'cancelled'  => ['Dibatalkan', '#EF4444'],
                    'refunded'   => ['Refund',     '#9CA3AF'],
                ];
                $totalAll = $orderStatusData->sum() ?: 1;
            @endphp
            @foreach($statusLabels as $key => [$label, $color])
                @php $cnt = $orderStatusData[$key] ?? 0; @endphp
                @if($cnt > 0)
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $color }}"></span>
                        <span class="text-gray-500 font-medium">{{ $label }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full" style="width:{{ round($cnt/$totalAll*100) }}%; background:{{ $color }}"></div>
                        </div>
                        <span class="font-bold text-gray-700 w-5 text-right">{{ $cnt }}</span>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

{{-- ── Row 4: Top Products + Recent Orders ─────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">

    {{-- Top Products --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-extrabold text-brand-dark">Produk Terlaris</h2>
            <a href="/products" class="text-[10px] font-bold text-brand-primary hover:underline">Lihat Semua</a>
        </div>
        <div class="space-y-4">
            @foreach($topProducts as $i => $top)
            @php $topImg = $top->images->first(); @endphp
            <div class="flex items-center gap-3">
                <span class="text-xs font-extrabold text-gray-300 w-4 text-center">{{ $i+1 }}</span>
                <div class="w-10 h-10 rounded-xl bg-gray-100 overflow-hidden flex-shrink-0">
                    @if($topImg)
                        <img src="{{ asset('storage/' . $topImg->image_url) }}" class="w-full h-full object-cover" alt="{{ $top->name }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-brand-dark truncate">{{ $top->name }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-brand-primary rounded-full transition-all"
                                 style="width: {{ $maxSold > 0 ? round($top->sold_count / $maxSold * 100) : 0 }}%">
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 flex-shrink-0">{{ $top->sold_count }} terjual</span>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs font-extrabold text-brand-primary">Rp {{ number_format($top->price, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
            <h2 class="text-base font-extrabold text-brand-dark">Transaksi Terbaru</h2>
            <a href="{{ route('orders.index') }}" class="text-[10px] font-bold text-brand-primary hover:underline">Lihat Semua</a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recentTransactions as $order)
            @php
                $badge = match($order->status) {
                    'pending'    => ['bg-amber-50 text-amber-600',  'Menunggu'],
                    'confirmed'  => ['bg-blue-50 text-blue-600',    'Dikonfirmasi'],
                    'processing' => ['bg-indigo-50 text-indigo-600','Diproses'],
                    'shipped'    => ['bg-orange-50 text-orange-600','Dikirim'],
                    'delivered'  => ['bg-emerald-50 text-emerald-600','Terkirim'],
                    'cancelled'  => ['bg-red-50 text-red-500',      'Dibatalkan'],
                    'refunded'   => ['bg-gray-100 text-gray-500',   'Refund'],
                    default      => ['bg-gray-100 text-gray-400',   $order->status],
                };
            @endphp
            <div class="px-6 py-4 hover:bg-gray-50/60 transition-all flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center font-bold text-xs flex-shrink-0">
                    {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-700 truncate">{{ $order->user->name ?? '-' }}</p>
                    <p class="text-[10px] text-gray-400">#{{ $order->order_number }} &bull; {{ $order->items->count() }} item</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs font-extrabold text-brand-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-lg {{ $badge[0] }}">{{ $badge[1] }}</span>
                </div>
                <a href="{{ route('orders.show', $order->id) }}"
                   class="flex-shrink-0 w-7 h-7 rounded-lg bg-gray-100 hover:bg-brand-primary hover:text-white text-gray-400 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// ── Revenue + Orders trend chart ──────────────────────────────
(function () {
    const months  = @json($salesData->pluck('month'));
    const revenue = @json($salesData->pluck('sum')->map(fn($v) => (int)$v));
    const orders  = @json($salesData->pluck('orders')->map(fn($v) => (int)$v));

    new ApexCharts(document.querySelector('#salesChart'), {
        series: [
            { name: 'Revenue (Rp)', type: 'bar', data: revenue },
            { name: 'Jumlah Order', type: 'line', data: orders }
        ],
        chart: {
            height: 260, toolbar: { show: false }, zoom: { enabled: false },
            fontFamily: 'Arial, sans-serif',
        },
        colors: ['#A78B6F', '#3B82F6'],
        stroke: { width: [0, 3], curve: 'smooth' },
        plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: months,
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { fontSize: '10px', colors: '#9CA3AF', fontWeight: 700 } }
        },
        yaxis: [
            {
                title: { text: '' }, seriesName: 'Revenue (Rp)',
                labels: {
                    formatter: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : (v/1000).toFixed(0)+'rb'),
                    style: { fontSize: '10px', colors: '#9CA3AF' }
                }
            },
            {
                opposite: true, seriesName: 'Jumlah Order',
                labels: {
                    formatter: v => v + ' order',
                    style: { fontSize: '10px', colors: '#9CA3AF' }
                }
            }
        ],
        grid: { borderColor: '#F3F4F6', strokeDashArray: 4 },
        legend: { show: false },
        tooltip: {
            y: [
                { formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) },
                { formatter: v => v + ' order' }
            ]
        }
    }).render();
})();

// ── Order status donut ────────────────────────────────────────
(function () {
    const statusData = @json($orderStatusData);
    const map = {
        pending:    { label: 'Menunggu',    color: '#F59E0B' },
        confirmed:  { label: 'Dikonfirmasi',color: '#3B82F6' },
        processing: { label: 'Diproses',    color: '#6366F1' },
        shipped:    { label: 'Dikirim',     color: '#F97316' },
        delivered:  { label: 'Terkirim',    color: '#10B981' },
        cancelled:  { label: 'Dibatalkan',  color: '#EF4444' },
        refunded:   { label: 'Refund',      color: '#9CA3AF' },
    };

    const labels = [], series = [], colors = [];
    Object.entries(map).forEach(([k, v]) => {
        const c = statusData[k] ?? 0;
        if (c > 0) { labels.push(v.label); series.push(c); colors.push(v.color); }
    });

    if (!series.length) return;

    new ApexCharts(document.querySelector('#statusChart'), {
        series, chart: { type: 'donut', height: 180, toolbar: { show: false } },
        labels, colors,
        plotOptions: { pie: { donut: { size: '65%', labels: {
            show: true,
            total: {
                show: true, label: 'Total',
                formatter: w => w.globals.seriesTotals.reduce((a,b)=>a+b,0)
            }
        }}}},
        dataLabels: { enabled: false },
        legend: { show: false },
        tooltip: { y: { formatter: v => v + ' order' } }
    }).render();
})();
</script>
@endpush
