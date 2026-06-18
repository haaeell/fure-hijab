@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-brand-dark">Dashboard Analitik</h1>
        <p class="text-gray-400 text-sm">Pantau performa penjualan FURE secara real-time.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-soft-mint rounded-2xl flex items-center justify-center text-brand-primary">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <span
                    class="text-[10px] font-bold px-2 py-1 {{ $salesGrowth >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-lg">
                    {{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 1) }}%
                </span>
            </div>
            <p class="text-gray-400 text-[11px] font-bold uppercase tracking-widest">Revenue</p>
            <h3 class="text-2xl font-extrabold text-brand-dark">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 mb-4">
                <i class="fa-solid fa-bag-shopping text-xl"></i>
            </div>
            <p class="text-gray-400 text-[11px] font-bold uppercase tracking-widest">Orders</p>
            <h3 class="text-2xl font-extrabold text-brand-dark">{{ number_format($totalOrders) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50">
            <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-500 mb-4">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <p class="text-gray-400 text-[11px] font-bold uppercase tracking-widest">Customers</p>
            <h3 class="text-2xl font-extrabold text-brand-dark">{{ number_format($totalCustomers) }}</h3>
        </div>

        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-50">
            <div class="w-12 h-12 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-500 mb-4">
                <i class="fa-solid fa-star text-xl"></i>
            </div>
            <p class="text-gray-400 text-[11px] font-bold uppercase tracking-widest">Avg Rating</p>
            <h3 class="text-2xl font-extrabold text-brand-dark">{{ number_format($avgRating, 1) }} <span
                    class="text-sm font-medium text-gray-300">/ 5.0</span></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white p-8 rounded-[40px] shadow-sm border border-gray-50">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-extrabold text-brand-dark">Statistik Penjualan</h2>
                <select class="text-xs bg-gray-50 border-none rounded-lg px-3 py-2 outline-none font-bold text-gray-500">
                    <option>6 Bulan Terakhir</option>
                </select>
            </div>
            <div id="salesChart" class="min-h-[300px]"></div>
        </div>

        <div class="bg-white p-8 rounded-[40px] shadow-sm border border-gray-50">
            <h2 class="text-lg font-extrabold text-brand-dark mb-6">Produk Terlaris</h2>
            <div class="space-y-6">
                @foreach($topProducts as $top)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 overflow-hidden flex-shrink-0">
                            @php $topImg = $top->images->first(); @endphp
                            @if($topImg)
                                <img src="{{ asset('storage/' . $topImg->image_url) }}" class="w-full h-full object-cover" alt="{{ $top->name }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fa-solid fa-box text-lg"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-brand-dark truncate w-32">{{ $top->name }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $top->sold_count }} Terjual</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-extrabold text-brand-primary">Rp
                                {{ number_format($top->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="/products"
                class="block w-full mt-8 py-3 bg-gray-50 text-gray-500 text-xs font-bold rounded-2xl hover:bg-soft-mint hover:text-brand-primary transition-all text-center">Lihat
                Semua Produk</a>
        </div>
    </div>

    <div class="bg-white rounded-[40px] shadow-sm border border-gray-50 overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-extrabold text-brand-dark">Transaksi Terbaru</h2>
            <a href="{{ route('orders.index') }}" class="text-sm font-bold text-brand-primary hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase">Customer</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase">Order ID</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase">Amount</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase">Status</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentTransactions as $order)
                        <tr class="hover:bg-soft-mint/20 transition-all">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-brand-primary/10 text-brand-primary rounded-full flex items-center justify-center font-bold text-xs">
                                        {{ substr($order->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">{{ $order->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm font-medium text-gray-500">#{{ $order->order_number }}</td>
                            <td class="px-8 py-5 text-sm font-extrabold text-brand-dark">Rp
                                {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="px-8 py-5">
                                @php
                                    $badge = match($order->status) {
                                        'pending'    => 'bg-yellow-100 text-yellow-700',
                                        'confirmed'  => 'bg-blue-100 text-blue-700',
                                        'processing' => 'bg-indigo-100 text-indigo-700',
                                        'shipped'    => 'bg-orange-100 text-orange-700',
                                        'delivered'  => 'bg-green-100 text-green-700',
                                        'cancelled'  => 'bg-red-100 text-red-700',
                                        'refunded'   => 'bg-gray-100 text-gray-600',
                                        default      => 'bg-gray-100 text-gray-500',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase {{ $badge }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const options = {
        series: [{
            name: 'Penjualan',
            data: @json($salesData->pluck('sum'))
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: ['#81C784'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.0,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: @json($salesData->pluck('month')),
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { show: false },
        grid: { borderColor: '#F1F1F1' }
    };

    const chart = new ApexCharts(document.querySelector("#salesChart"), options);
    chart.render();
</script>
@endpush