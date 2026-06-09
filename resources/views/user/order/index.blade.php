@extends('layouts.customer')

@section('title', 'Riwayat Pesanan - Al-Hayya Hijab')

@section('content')
    <section class="py-12 bg-gray-50 min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex items-center gap-4 mb-8">
                <a href="/home"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-brand-dark shadow-sm hover:bg-brand-primary hover:text-white transition-all">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-extrabold text-brand-dark">Riwayat Pesanan</h1>
            </div>

            <!-- Tab Status -->
            <div class="mb-8 overflow-x-auto no-scrollbar">
                <div class="flex gap-2 p-1 bg-gray-200/50 rounded-2xl w-max">
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
                        <button class="tab-btn px-6 py-2.5 rounded-xl text-sm font-bold transition-all duration-300"
                            data-status="{{ $key }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Tabel Container -->
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 overflow-hidden p-6">
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
                                $statusMap = [
                                    'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'Belum Dibayar'],
                                    'confirmed' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'Dikonfirmasi'],
                                    'processing' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'label' => 'Diproses'],
                                    'shipped' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'label' => 'Dikirim'],
                                    'delivered' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'label' => 'Selesai'],
                                    'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'label' => 'Batal'],
                                ];
                                $currentStatus = $statusMap[$order->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'label' => $order->status];
                                $paymentExpiresAt = $order->payment?->expired_at ?: $order->created_at->copy()->addDay();
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
                                            @php $item = $order->items->first(); @endphp
                                            @php $primaryImage = $item->product->images->where('is_primary', true)->first(); @endphp
                                            <img src="{{ $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x533' }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="max-w-[150px]">
                                            <p class="text-xs font-bold text-brand-dark truncate">{{ $item->product->name }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $order->items->count() }} Produk</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6">
                                    <p class="text-sm font-black text-brand-primary">
                                        Rp{{ number_format($order->total, 0, ',', '.') }}</p>
                                </td>
                                <td class="py-6">
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
                                        <a href="{{ route('order.history.show', $order->id) }}"
                                            class="p-2 text-xs font-bold bg-gray-100 text-gray-600 rounded-lg hover:bg-brand-dark hover:text-white transition-all shadow-sm">
                                            <i class="fa-solid fa-eye text-xs"></i> Detail
                                        </a>
                                        @if($order->status == 'shipped')
                                            <form action="{{ route('order.history.complete', $order->id) }}" method="POST"
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
                                            <form id="cancel-form-{{ $order->id }}" action="{{ route('order.history.cancel', $order->id) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="cancellation_reason" id="cancel-reason-{{ $order->id }}">
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
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

            // Logika Filter Tab
            window.filterStatus = function (status) {
                table.column(3).search(status).draw();

                // Update UI Button
                $('.tab-btn').removeClass('bg-brand-primary text-white shadow-lg shadow-brand-primary/20').addClass('text-gray-500 hover:text-brand-dark');
                event.currentTarget.classList.add('bg-brand-primary', 'text-white', 'shadow-lg', 'shadow-brand-primary/20');
                event.currentTarget.classList.remove('text-gray-500');
            };

            // Set default tab "All" aktif
            $('.tab-btn[data-status="all"]').click();
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

                $(`#cancel-reason-${orderId}`).val(result.value.trim());
                $(`#cancel-form-${orderId}`).submit();
            });
        }
    </script>
@endpush
