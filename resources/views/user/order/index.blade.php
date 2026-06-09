@extends('layouts.customer')

@section('title', 'Riwayat Pesanan - Al-Hayya Hijab')

@section('content')
    <section class="py-8 md:py-12 bg-gray-50 min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex items-center gap-3 md:gap-4 mb-6 md:mb-8">
                <a href="/home"
                    class="w-10 h-10 md:w-11 md:h-11 bg-white rounded-2xl flex items-center justify-center text-brand-dark shadow-sm border border-gray-100 hover:bg-brand-primary hover:text-white transition-all flex-shrink-0">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="min-w-0">
                    <h1 class="text-xl md:text-3xl font-extrabold text-brand-dark leading-tight">Riwayat Pesanan</h1>
                    <p class="text-xs text-gray-400 font-medium mt-1">Pantau pembayaran, pengiriman, dan status pesanan Anda.</p>
                </div>
            </div>

            <!-- Tab Status -->
            <div class="mb-5 md:mb-8 -mx-4 px-4 overflow-x-auto no-scrollbar">
                <div class="flex gap-2 p-1 bg-gray-200/50 rounded-2xl w-max min-w-max">
                    @php
                        $tabs = [
                            'all' => 'Semua',
                            'pending' => 'Pending',
                            'processing' => 'Diproses',
                            'shipped' => 'Dikirim',
                            'delivered' => 'Selesai',
                            'cancelled' => 'Batal'
                        ];
                    @endphp
                    @foreach($tabs as $key => $label)
                        <button type="button" class="tab-btn px-4 md:px-6 py-2.5 rounded-xl text-xs md:text-sm font-bold transition-all duration-300 whitespace-nowrap"
                            data-status="{{ $key }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            @php
                $statusMap = [
                    'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Belum Dibayar', 'icon' => 'fa-clock'],
                    'confirmed' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Dikonfirmasi', 'icon' => 'fa-circle-check'],
                    'processing' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'label' => 'Diproses', 'icon' => 'fa-box'],
                    'shipped' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'label' => 'Dikirim', 'icon' => 'fa-truck'],
                    'delivered' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'label' => 'Selesai', 'icon' => 'fa-check-double'],
                    'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => 'Batal', 'icon' => 'fa-ban'],
                ];
            @endphp

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4" id="mobileOrderList">
                @forelse($orders as $order)
                    @php
                        $currentStatus = $statusMap[$order->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'label' => $order->status, 'icon' => 'fa-circle-info'];
                        $paymentExpiresAt = $order->payment?->expired_at ?: $order->created_at->copy()->addDay();
                        $firstItem = $order->items->first();
                        $primaryImage = $firstItem?->product?->images?->where('is_primary', true)->first()
                            ?? $firstItem?->product?->images?->first();
                    @endphp
                    <article class="mobile-order-card bg-white rounded-[28px] border border-gray-100 shadow-sm overflow-hidden"
                        data-status="{{ $order->status }}">
                        <div class="p-4 border-b border-gray-50 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">No. Pesanan</p>
                                <p class="text-sm font-black text-brand-dark font-mono truncate">#{{ $order->order_number }}</p>
                                <p class="text-[11px] text-gray-400 font-semibold mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-wider {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} flex-shrink-0">
                                <i class="fa-solid {{ $currentStatus['icon'] }}"></i>
                                {{ $currentStatus['label'] }}
                            </span>
                        </div>

                        <div class="p-4">
                            <div class="flex gap-3">
                                <div class="w-16 h-20 rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                                    <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                        class="w-full h-full object-cover" alt="{{ $firstItem?->product?->name ?? 'Produk' }}">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-brand-dark leading-snug line-clamp-2">
                                        {{ $firstItem?->product?->name ?? $firstItem?->product_name ?? 'Produk tidak tersedia' }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 font-semibold mt-1">
                                        {{ $order->items->count() }} produk · {{ $order->items->sum('qty') }} pcs
                                    </p>
                                    @if($order->status === 'pending')
                                        <p class="text-[11px] text-amber-600 font-bold mt-2 leading-snug">
                                            Bayar sebelum {{ $paymentExpiresAt->format('d M Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 rounded-2xl bg-gray-50 px-4 py-3 flex items-center justify-between gap-3">
                                <span class="text-xs font-bold text-gray-400">Total Pembayaran</span>
                                <span class="text-base font-black text-brand-primary whitespace-nowrap">
                                    Rp{{ number_format($order->total, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <a href="{{ route('order.history.show', $order->order_number) }}"
                                    class="h-11 flex items-center justify-center gap-2 text-xs font-black bg-brand-dark text-white rounded-2xl hover:bg-brand-primary transition-all">
                                    <i class="fa-solid fa-eye"></i> Detail
                                </a>

                                @if($order->status == 'shipped')
                                    <form action="{{ route('order.history.complete', $order->order_number) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="w-full h-11 flex items-center justify-center gap-2 text-xs font-black bg-brand-primary text-white rounded-2xl hover:bg-brand-dark transition-all">
                                            <i class="fa-solid fa-check"></i> Selesai
                                        </button>
                                    </form>
                                @elseif(in_array($order->status, ['pending', 'confirmed']))
                                    <button type="button"
                                        onclick="cancelCustomerOrder({{ $order->id }})"
                                        class="h-11 flex items-center justify-center gap-2 text-xs font-black bg-red-50 text-red-600 rounded-2xl hover:bg-red-500 hover:text-white transition-all">
                                        <i class="fa-solid fa-ban"></i> Batal
                                    </button>
                                    <form id="cancel-form-mobile-{{ $order->id }}" action="{{ route('order.history.cancel', $order->order_number) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="cancellation_reason" id="cancel-reason-mobile-{{ $order->id }}">
                                    </form>
                                @else
                                    <div class="h-11 flex items-center justify-center text-[11px] font-bold text-gray-400 bg-gray-50 rounded-2xl">
                                        Tidak ada aksi
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="bg-white rounded-[28px] border border-gray-100 p-8 text-center">
                        <div class="w-14 h-14 bg-soft-mint rounded-2xl flex items-center justify-center mx-auto text-brand-primary mb-4">
                            <i class="fa-solid fa-bag-shopping"></i>
                        </div>
                        <p class="font-black text-brand-dark">Belum ada pesanan</p>
                        <p class="text-xs text-gray-400 mt-1">Pesanan Anda akan tampil di sini setelah checkout.</p>
                    </div>
                @endforelse

                <div id="mobileEmptyState" class="hidden bg-white rounded-[28px] border border-gray-100 p-8 text-center">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto text-gray-300 mb-4">
                        <i class="fa-solid fa-filter"></i>
                    </div>
                    <p class="font-black text-brand-dark">Tidak ada pesanan</p>
                    <p class="text-xs text-gray-400 mt-1">Belum ada pesanan pada status ini.</p>
                </div>
            </div>

            <!-- Tabel Container -->
            <div class="hidden md:block bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden p-6">
                <table id="orderTable" class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                            <th class="pb-4 px-2">Info Pesanan</th>
                            <th class="pb-4">Produk</th>
                            <th class="pb-4">Total</th>
                            <th class="pb-4">Status</th>
                            <th class="pb-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($orders as $order)
                            @php
                                $currentStatus = $statusMap[$order->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'label' => $order->status];
                                $paymentExpiresAt = $order->payment?->expired_at ?: $order->created_at->copy()->addDay();
                                $item = $order->items->first();
                                $primaryImage = $item?->product?->images?->where('is_primary', true)->first()
                                    ?? $item?->product?->images?->first();
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-6 px-2">
                                    <p class="text-sm font-black text-brand-dark">#{{ $order->order_number }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                </td>
                                <td class="py-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-12 h-14 rounded-lg overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0">
                                            <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="max-w-[150px]">
                                            <p class="text-xs font-bold text-brand-dark truncate">{{ $item?->product?->name ?? $item?->product_name ?? 'Produk tidak tersedia' }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $order->items->count() }} Produk</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6">
                                    <p class="text-sm font-black text-brand-primary">
                                        Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                                </td>
                                <td class="py-6">
                                    <span class="sr-only">{{ $order->status }}</span>
                                    <span
                                        class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }}">
                                        {{ $currentStatus['label'] }}
                                    </span>
                                    @if($order->status === 'pending')
                                        <p class="text-[10px] text-amber-600 font-semibold mt-2">
                                            Bayar sebelum {{ $paymentExpiresAt->format('d M Y H:i') }}
                                        </p>
                                    @endif
                                </td>
                                <td class="py-6">
                                    <div class="flex gap-2">
                                        <a href="{{ route('order.history.show', $order->order_number) }}"
                                            class="p-2 text-xs font-bold bg-gray-100 text-gray-600 rounded-lg hover:bg-brand-dark hover:text-white transition-all shadow-sm">
                                            <i class="fa-solid fa-eye text-xs"></i> Detail
                                        </a>
                                        @if($order->status == 'shipped')
                                            <form action="{{ route('order.history.complete', $order->order_number) }}" method="POST"
                                                class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="p-2 bg-brand-primary text-white rounded-lg hover:shadow-lg hover:shadow-brand-primary/30 transition-all">
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if(in_array($order->status, ['pending', 'confirmed']))
                                            <button type="button"
                                                onclick="cancelCustomerOrder({{ $order->id }})"
                                                class="p-2 text-xs font-bold bg-red-50 text-red-600 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                <i class="fa-solid fa-ban text-xs"></i> Batal
                                            </button>
                                            <form id="cancel-form-{{ $order->id }}" action="{{ route('order.history.cancel', $order->order_number) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cancellation_reason" id="cancel-reason-{{ $order->id }}">
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <p class="font-black text-brand-dark">Belum ada pesanan</p>
                                    <p class="text-xs text-gray-400 mt-1">Pesanan Anda akan tampil di sini setelah checkout.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable
            const table = $('#orderTable').DataTable({
                dom: '<"flex flex-col md:flex-row justify-between gap-4 mb-6"f>rtip',
                pageLength: 10,
                autoWidth: false,
                language: {
                    search: "",
                    searchPlaceholder: "Cari nomor pesanan...",
                    emptyTable: "Belum ada pesanan dalam kategori ini",
                    paginate: {
                        previous: "<i class='fa-solid fa-chevron-left'></i>",
                        next: "<i class='fa-solid fa-chevron-right'></i>"
                    }
                }
            });

            function filterMobileOrders(status) {
                let visibleCount = 0;

                $('.mobile-order-card').each(function () {
                    const matched = status === 'all' || $(this).data('status') === status;
                    $(this).toggleClass('hidden', !matched);

                    if (matched) {
                        visibleCount++;
                    }
                });

                $('#mobileEmptyState').toggleClass('hidden', visibleCount > 0);
            }

            function filterDesktopOrders(status) {
                if (status === 'all') {
                    table.column(3).search('').draw();
                    return;
                }

                table.column(3).search(status).draw();
            }

            $('.tab-btn').on('click', function () {
                const status = $(this).data('status');

                filterDesktopOrders(status);
                filterMobileOrders(status);

                // Update UI Button
                $('.tab-btn').removeClass('bg-brand-primary text-white shadow-lg shadow-brand-primary/20').addClass('text-gray-500 hover:text-brand-dark');
                $(this)
                    .addClass('bg-brand-primary text-white shadow-lg shadow-brand-primary/20')
                    .removeClass('text-gray-500 hover:text-brand-dark');
            });

            // Set default tab "All" aktif
            $('.tab-btn[data-status="all"]').trigger('click');
        });

        window.cancelCustomerOrder = function (orderId) {
            Swal.fire({
                title: 'Batalkan Pesanan?',
                input: 'textarea',
                inputLabel: 'Alasan pembatalan',
                inputPlaceholder: 'Tulis alasan pembatalan...',
                inputAttributes: { maxlength: 500 },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali',
                confirmButtonColor: '#ef4444',
                inputValidator: (value) => {
                    if (!value || value.trim().length < 5) {
                        return 'Alasan minimal 5 karakter.';
                    }
                }
            }).then((result) => {
                if (!result.isConfirmed) return;

                $(`#cancel-reason-${orderId}, #cancel-reason-mobile-${orderId}`).val(result.value.trim());
                const form = $(`#cancel-form-${orderId}`).length ? $(`#cancel-form-${orderId}`) : $(`#cancel-form-mobile-${orderId}`);
                form.submit();
            });
        }
    </script>
@endpush
