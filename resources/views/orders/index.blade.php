@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
    @php
        $statusMap = [
            '' => ['label' => 'Semua', 'icon' => 'fa-list'],
            'pending' => ['label' => 'Pending', 'icon' => 'fa-hourglass-half'],
            'confirmed' => ['label' => 'Dikonfirmasi', 'icon' => 'fa-circle-check'],
            'processing' => ['label' => 'Diproses', 'icon' => 'fa-gear'],
            'shipped' => ['label' => 'Dikirim', 'icon' => 'fa-truck'],
            'delivered' => ['label' => 'Terkirim', 'icon' => 'fa-house-circle-check'],
            'cancelled' => ['label' => 'Dibatalkan', 'icon' => 'fa-circle-xmark'],
            'refunded' => ['label' => 'Refund', 'icon' => 'fa-rotate-left'],
        ];

        $activeFilter = $filters['status'] ?? '';
        $dateParams = array_filter([
            'start_date' => $filters['start_date'] ?? null,
            'end_date' => $filters['end_date'] ?? null,
        ]);
    @endphp

    <div class="mx-auto">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Pesanan</h1>
                <nav class="text-xs md:text-sm text-gray-400 font-medium mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-brand-primary transition-colors">Dashboard</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                        <li class="text-brand-dark">Pesanan</li>
                    </ol>
                </nav>
            </div>

            <button onclick="exportOrders()"
                class="px-5 py-3 bg-white border border-gray-200 text-gray-600 rounded-2xl font-bold shadow-sm hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-export text-sm text-brand-primary"></i>
                <span class="text-sm">Export</span>
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-3xl px-5 py-4 border border-gray-50 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 tracking-widest">TOTAL PESANAN</p>
                    <p class="text-2xl font-extrabold text-brand-dark leading-tight">{{ number_format($summary['total']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl px-5 py-4 border border-gray-50 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500">
                    <i class="fa-solid fa-clock text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 tracking-widest">MENUNGGU</p>
                    <p class="text-2xl font-extrabold text-brand-dark leading-tight">{{ number_format($summary['pending']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl px-5 py-4 border border-gray-50 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500">
                    <i class="fa-solid fa-truck text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 tracking-widest">DIPROSES</p>
                    <p class="text-2xl font-extrabold text-brand-dark leading-tight">{{ number_format($summary['processing']) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl px-5 py-4 border border-gray-50 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-green-50 flex items-center justify-center text-green-500">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 tracking-widest">SELESAI</p>
                    <p class="text-2xl font-extrabold text-brand-dark leading-tight">{{ number_format($summary['done']) }}</p>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-brand-primary to-brand-dark rounded-3xl px-6 py-5 mb-6 flex items-center justify-between shadow-lg shadow-brand-primary/20">
            <div>
                <p class="text-[10px] font-black text-white/60 tracking-widest">TOTAL PENDAPATAN (TERKIRIM)</p>
                <p class="text-3xl font-extrabold text-white mt-1">Rp {{ number_format($summary['revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-white">
                <i class="fa-solid fa-wallet text-2xl"></i>
            </div>
        </div>

        <form method="GET" action="{{ route('orders.index') }}"
            class="bg-white rounded-3xl border border-gray-50 shadow-sm mb-4 p-4 grid grid-cols-1 md:grid-cols-[1fr_1fr_auto_auto] gap-3 items-end">
            <input type="hidden" name="status" value="{{ $activeFilter }}">

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

            <button type="submit"
                class="px-5 py-3 bg-brand-dark text-white rounded-2xl font-bold text-sm shadow-sm hover:bg-brand-primary transition-all">
                <i class="fa-solid fa-calendar-days mr-2"></i> Terapkan
            </button>

            <a href="{{ route('orders.index', array_filter(['status' => $activeFilter])) }}"
                class="px-5 py-3 bg-gray-50 text-gray-500 rounded-2xl font-bold text-sm text-center hover:bg-gray-100 transition-all">
                Reset Tanggal
            </a>
        </form>

        <div class="bg-white rounded-3xl border border-gray-50 shadow-sm mb-4 px-4 py-2 flex gap-1 flex-wrap">
            @foreach ($statusMap as $key => $cfg)
                @php
                    $params = $key ? array_merge($dateParams, ['status' => $key]) : $dateParams;
                    $count = $key ? ($statusCounts[$key] ?? 0) : $summary['total'];
                @endphp
                <a href="{{ route('orders.index', $params) }}"
                    class="px-4 py-2.5 rounded-2xl text-[11px] font-black tracking-widest transition-all flex items-center gap-1.5 whitespace-nowrap
                        {{ $activeFilter === $key ? 'bg-brand-primary text-white shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-700' }}">
                    <i class="fa-solid {{ $cfg['icon'] }} text-[10px]"></i>
                    {{ $cfg['label'] }}
                    @if ($count > 0)
                        <span
                            class="px-1.5 py-0.5 rounded-md text-[9px] font-black {{ $activeFilter === $key ? 'bg-white/20' : 'bg-gray-100' }}">{{ number_format($count) }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden px-6 py-8">
            <table id="ordersTable" class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 text-[11px] tracking-widest border-b border-gray-50">
                        <th class="px-4 py-4 text-left">No. Order</th>
                        <th class="px-4 py-4 text-left">Pelanggan</th>
                        <th class="px-4 py-4 text-left">Item</th>
                        <th class="px-4 py-4 text-left">Total</th>
                        <th class="px-4 py-4 text-left">Pembayaran</th>
                        <th class="px-4 py-4 text-left">Status</th>
                        <th class="px-4 py-4 text-left">Tanggal</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50"></tbody>
            </table>
        </div>
    </div>

    <div id="statusModal"
        class="fixed inset-0 hidden bg-slate-900/50 backdrop-blur-sm items-center justify-center z-[100] p-4">
        <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden border border-white/20">
            <div class="px-7 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-brand-dark">Ubah Status Pesanan</h3>
                        <p class="text-[10px] text-gray-400 font-bold tracking-widest">ORDER MANAGEMENT</p>
                    </div>
                </div>
                <button onclick="closeStatusModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="statusForm" method="POST" class="p-7 space-y-4">
                @csrf
                @method('PATCH')
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 tracking-widest">STATUS PESANAN</label>
                    <select name="status" id="statusSelect"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none text-sm font-semibold appearance-none">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Dikonfirmasi</option>
                        <option value="processing">Diproses</option>
                        <option value="shipped">Dikirim</option>
                        <option value="delivered">Terkirim</option>
                        <option value="cancelled">Dibatalkan</option>
                        <option value="refunded">Refund</option>
                    </select>
                </div>

                <div id="resiField" class="hidden space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 tracking-widest">NOMOR RESI</label>
                    <input type="text" name="resi" placeholder="Masukkan nomor resi pengiriman..."
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

    @push('scripts')
        <script>
            const orderFilters = @json(array_filter($filters, fn ($value) => filled($value)));

            $(document).ready(function() {
                $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('orders.data') }}",
                        data: orderFilters,
                    },
                    order: [[6, 'desc']],
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                    columns: [
                        { data: 'order_identity', name: 'order_number' },
                        { data: 'customer', name: 'customer', orderable: false },
                        { data: 'items_summary', name: 'items_summary', orderable: false },
                        { data: 'total_summary', name: 'total' },
                        { data: 'payment_summary', name: 'payment_summary', orderable: false },
                        { data: 'status_badge', name: 'status' },
                        { data: 'date_summary', name: 'created_at' },
                        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Cari pesanan, pelanggan, produk...",
                        lengthMenu: "Tampilkan _MENU_ entri",
                        info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ pesanan",
                        infoEmpty: "Tidak ada data tersedia",
                        infoFiltered: "(disaring dari _MAX_ total pesanan)",
                        zeroRecords: "Pesanan tidak ditemukan",
                        paginate: {
                            first: "«",
                            last: "»",
                            next: "›",
                            previous: "‹"
                        },
                        processing: '<div class="flex items-center gap-2 text-brand-dark font-bold"><i class="fa-solid fa-spinner fa-spin"></i> Memuat pesanan...</div>'
                    },
                });
            });

            window.openStatusModal = function(orderId, currentStatus) {
                $('#statusForm').attr('action', `/orders/${orderId}/status`);
                $('#statusSelect').val(currentStatus);
                toggleResiField(currentStatus);
                $('#statusModal').removeClass('hidden').addClass('flex');
            }

            window.closeStatusModal = function() {
                $('#statusModal').addClass('hidden').removeClass('flex');
            }

            $('#statusSelect').on('change', function() {
                toggleResiField(this.value);
            });

            function toggleResiField(status) {
                if (status === 'shipped') {
                    $('#resiField').removeClass('hidden');
                } else {
                    $('#resiField').addClass('hidden');
                }
            }

            $('#statusModal').on('click', function(e) {
                if (e.target === this) closeStatusModal();
            });

            window.cancelOrder = function(id) {
                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    text: 'Pesanan akan dibatalkan dan tidak bisa dikembalikan ke status sebelumnya.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2D5A27',
                    cancelButtonColor: '#ef4444',
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

            window.exportOrders = function() {
                const params = new URLSearchParams(orderFilters);

                Swal.fire({
                    title: 'Export Pesanan',
                    text: 'Pilih format file yang ingin diunduh.',
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Excel',
                    denyButtonText: 'PDF',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#81C784',
                    denyButtonColor: '#ef4444',
                }).then((result) => {
                    if (!result.isConfirmed && !result.isDenied) {
                        return;
                    }

                    params.set('format', result.isConfirmed ? 'excel' : 'pdf');
                    window.location.href = "{{ route('reports.export', ['type' => 'orders']) }}" + '?' + params.toString();
                });
            }
        </script>
    @endpush
@endsection
