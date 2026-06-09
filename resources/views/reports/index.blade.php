@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    @php
        $statusMap = [
            '' => 'Semua Status',
            'pending' => 'Pending',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Terkirim',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Refund',
        ];

        $exportParams = array_filter($filters, fn ($value) => filled($value));
    @endphp

    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Laporan</h1>
            <p class="text-gray-400 text-sm mt-1">Ringkasan performa penjualan, produk, pelanggan, dan data operasional.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ([
                'orders' => ['label' => 'Pesanan', 'icon' => 'fa-bag-shopping'],
                'order-items' => ['label' => 'Item', 'icon' => 'fa-list-check'],
                'products' => ['label' => 'Produk', 'icon' => 'fa-boxes-stacked'],
                'customers' => ['label' => 'Pelanggan', 'icon' => 'fa-users'],
            ] as $type => $export)
                <div class="flex rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                    <a href="{{ route('reports.export', ['type' => $type, 'format' => 'excel'] + $exportParams) }}"
                        class="px-3 py-3 text-gray-600 font-bold text-xs hover:bg-soft-mint hover:text-brand-dark transition-all flex items-center gap-2">
                        <i class="fa-solid {{ $export['icon'] }} text-brand-primary"></i> {{ $export['label'] }}
                    </a>
                    <a href="{{ route('reports.export', ['type' => $type, 'format' => 'pdf'] + $exportParams) }}"
                        class="px-3 py-3 bg-red-50 text-red-500 font-black text-xs hover:bg-red-100 transition-all"
                        title="Export PDF">
                        PDF
                    </a>
                    <a href="{{ route('reports.export', ['type' => $type, 'format' => 'excel'] + $exportParams) }}"
                        class="px-3 py-3 bg-green-50 text-green-600 font-black text-xs hover:bg-green-100 transition-all"
                        title="Export Excel">
                        XLSX
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <form method="GET" action="{{ route('reports.index') }}"
        class="bg-white rounded-[28px] border border-gray-50 shadow-sm p-5 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="text-[10px] font-black text-gray-400 tracking-widest uppercase">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ $filters['start_date'] }}"
                class="mt-2 w-full rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600 outline-none focus:border-brand-primary focus:bg-white">
        </div>
        <div>
            <label class="text-[10px] font-black text-gray-400 tracking-widest uppercase">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ $filters['end_date'] }}"
                class="mt-2 w-full rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600 outline-none focus:border-brand-primary focus:bg-white">
        </div>
        <div>
            <label class="text-[10px] font-black text-gray-400 tracking-widest uppercase">Status</label>
            <select name="status"
                class="mt-2 w-full rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-600 outline-none focus:border-brand-primary focus:bg-white">
                @foreach ($statusMap as $value => $label)
                    <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit"
                class="flex-1 px-4 py-3 bg-brand-dark text-white rounded-2xl font-bold text-sm shadow-sm hover:bg-brand-primary transition-all">
                <i class="fa-solid fa-filter mr-2"></i> Terapkan
            </button>
            <a href="{{ route('reports.index') }}"
                class="px-4 py-3 bg-gray-50 text-gray-500 rounded-2xl font-bold text-sm hover:bg-gray-100 transition-all">
                Reset
            </a>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-[28px] p-6 border border-gray-50 shadow-sm">
            <div class="w-12 h-12 bg-soft-mint rounded-2xl flex items-center justify-center text-brand-primary mb-4">
                <i class="fa-solid fa-wallet text-xl"></i>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Revenue</p>
            <h3 class="text-2xl font-extrabold text-brand-dark mt-1">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white rounded-[28px] p-6 border border-gray-50 shadow-sm">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 mb-4">
                <i class="fa-solid fa-bag-shopping text-xl"></i>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Total Pesanan</p>
            <h3 class="text-2xl font-extrabold text-brand-dark mt-1">{{ number_format($summary['orders']) }}</h3>
        </div>
        <div class="bg-white rounded-[28px] p-6 border border-gray-50 shadow-sm">
            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 mb-4">
                <i class="fa-solid fa-box-open text-xl"></i>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Item Terjual</p>
            <h3 class="text-2xl font-extrabold text-brand-dark mt-1">{{ number_format($summary['items_sold']) }}</h3>
        </div>
        <div class="bg-white rounded-[28px] p-6 border border-gray-50 shadow-sm">
            <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-500 mb-4">
                <i class="fa-solid fa-receipt text-xl"></i>
            </div>
            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Rata-rata Order</p>
            <h3 class="text-2xl font-extrabold text-brand-dark mt-1">Rp {{ number_format($summary['average_order'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 bg-white rounded-[32px] p-6 border border-gray-50 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-extrabold text-brand-dark">Tren Penjualan</h2>
                    <p class="text-xs text-gray-400 font-medium">Pendapatan per bulan dalam periode filter.</p>
                </div>
            </div>
            <div id="reportSalesChart" class="min-h-[320px]"></div>
        </div>

        <div class="bg-white rounded-[32px] p-6 border border-gray-50 shadow-sm">
            <h2 class="text-lg font-extrabold text-brand-dark mb-5">Status Pesanan</h2>
            <div class="space-y-3">
                @forelse ($statusSummary as $row)
                    <div class="flex items-center justify-between gap-3 border-b border-gray-50 pb-3 last:border-0">
                        <div>
                            <p class="text-sm font-bold text-gray-700">{{ $statusMap[$row->status] ?? ucfirst($row->status) }}</p>
                            <p class="text-[10px] font-semibold text-gray-400">{{ number_format($row->total_orders) }} pesanan</p>
                        </div>
                        <p class="text-sm font-extrabold text-brand-dark">Rp {{ number_format($row->total_amount, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada data pada periode ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-[32px] border border-gray-50 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50">
                <h2 class="text-lg font-extrabold text-brand-dark">Produk Terlaris</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/60">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Produk</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Qty</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($topProducts as $product)
                            <tr>
                                <td class="px-6 py-4 text-sm font-bold text-brand-dark">{{ $product->product_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ number_format($product->total_qty) }}</td>
                                <td class="px-6 py-4 text-sm font-extrabold text-brand-primary">Rp {{ number_format($product->total_sales, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400">Belum ada produk terjual.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-[32px] border border-gray-50 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50">
                <h2 class="text-lg font-extrabold text-brand-dark">Pelanggan Terbaik</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/60">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Pelanggan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Order</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Belanja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($topCustomers as $customer)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-brand-dark">{{ $customer->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $customer->email }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ number_format($customer->total_orders) }}</td>
                                <td class="px-6 py-4 text-sm font-extrabold text-brand-primary">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400">Belum ada pelanggan pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-[32px] border border-gray-50 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50">
                <h2 class="text-lg font-extrabold text-brand-dark">Pesanan Terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/60">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Order</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Pelanggan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($latestOrders as $order)
                            <tr>
                                <td class="px-6 py-4">
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-sm font-black text-brand-primary">{{ $order->order_number }}</a>
                                    <p class="text-[10px] text-gray-400">{{ $order->created_at->format('d M Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-700">{{ $order->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-xs font-black text-gray-500 uppercase">{{ $order->status_label }}</td>
                                <td class="px-6 py-4 text-sm font-extrabold text-brand-dark">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-[32px] border border-gray-50 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50">
                <h2 class="text-lg font-extrabold text-brand-dark">Pantauan Stok</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach ($stockAlerts as $product)
                    <div class="p-4 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-brand-dark truncate">{{ $product->name }}</p>
                            <p class="text-[10px] font-semibold text-gray-400">{{ $product->category->name ?? '-' }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-xl text-xs font-black {{ $product->stock <= 5 ? 'bg-red-50 text-red-500' : 'bg-gray-50 text-gray-500' }}">
                            {{ number_format($product->stock) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const reportSalesChart = new ApexCharts(document.querySelector("#reportSalesChart"), {
            series: [{
                name: 'Revenue',
                data: @json($salesTrend->pluck('total_amount')->map(fn ($value) => (float) $value))
            }, {
                name: 'Pesanan',
                data: @json($salesTrend->pluck('total_orders')->map(fn ($value) => (int) $value))
            }],
            chart: { type: 'area', height: 330, toolbar: { show: false }, zoom: { enabled: false } },
            colors: ['#81C784', '#60A5FA'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
            xaxis: {
                categories: @json($salesTrend->pluck('period')),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return value >= 1000000 ? 'Rp ' + (value / 1000000).toFixed(1) + 'jt' : value.toFixed(0);
                    }
                }
            },
            grid: { borderColor: '#F1F5F9' },
            legend: { fontFamily: 'Poppins' }
        });

        reportSalesChart.render();
    </script>
@endpush
