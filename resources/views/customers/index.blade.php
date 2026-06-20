@extends('layouts.app')

@section('title', 'Data Pelanggan')

@section('content')
    <div class="mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Manajemen Pelanggan</h1>
                <nav class="text-xs md:text-sm text-gray-400 font-medium mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-brand-primary transition-colors">Dashboard</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                        <li class="text-brand-dark">Pelanggan</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden px-6 py-8">
            <table id="customerTable" class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 text-[11px] uppercase tracking-widest border-b border-gray-50">
                        <th class="px-4 py-4 text-left">Pelanggan</th>
                        <th class="px-4 py-4 text-left">Info Kontak</th>
                        <th class="px-4 py-4 text-center">Total Order</th>
                        <th class="px-4 py-4 text-left">Total Belanja</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($customers as $customer)
                        <tr class="hover:bg-soft-bg/50 transition-colors">
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-2xl bg-brand-primary/10 flex items-center justify-center text-brand-primary font-bold">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-brand-dark">{{ $customer->name }}</span>
                                        <span
                                            class="text-[10px] text-gray-400 uppercase font-black">{{ $customer->role }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <div class="flex flex-col text-xs">
                                    <span class="text-gray-600 font-medium"><i class="fa-regular fa-envelope mr-1"></i>
                                        {{ $customer->email }}</span>
                                    <span class="text-gray-400"><i class="fa-solid fa-phone mr-1 text-[10px]"></i>
                                        {{ $customer->phone ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-5 text-center">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg font-bold text-xs">
                                    {{ $customer->orders_count ?? 0 }}x
                                </span>
                            </td>
                            <td class="px-4 py-5 font-bold text-brand-dark">
                                Rp {{ number_format($customer->orders_sum_subtotal ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-5 text-center">
                                <button onclick='viewCustomerDetail(@json($customer->load("addresses")))'
                                    class="px-4 py-2 bg-brand-primary/10 text-brand-primary rounded-xl hover:bg-brand-primary hover:text-white transition-all text-xs font-bold">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="detailModal"
        class="fixed inset-0 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-[100] p-4">
        <div
            class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all border border-white/20">

            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div
                        class="w-11 h-11 rounded-2xl bg-brand-primary flex items-center justify-center text-white shadow-lg shadow-brand-primary/20">
                        <i class="fa-solid fa-user-tag text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-brand-dark leading-tight">Profil Pelanggan</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Data Lengkap & Alamat</p>
                    </div>
                </div>
                <button onclick="closeDetailModal()"
                    class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="p-8 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] border-b pb-2">Informasi Akun
                        </h3>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Nama Lengkap</p>
                            <p id="detName" class="font-bold text-brand-dark"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Email</p>
                            <p id="detEmail" class="font-bold text-brand-dark"></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">No. Telepon</p>
                            <p id="detPhone" class="font-bold text-brand-dark"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] border-b pb-2">Daftar Alamat
                        </h3>
                        <div id="addressList" class="space-y-3">
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50/50 flex justify-end">
                <button onclick="closeDetailModal()"
                    class="px-8 py-3 bg-white border border-gray-200 text-gray-500 text-xs font-black uppercase rounded-2xl hover:bg-gray-100 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                const table = $('#customerTable').DataTable({
                    responsive: true
                });

                const searchParam = new URLSearchParams(window.location.search).get('search');
                if (searchParam) {
                    table.search(searchParam).draw();
                }
            });

            function viewCustomerDetail(data) {
                $('#detName').text(data.name);
                $('#detEmail').text(data.email);
                $('#detPhone').text(data.phone || '-');

                let addressHtml = '';
                if (data.addresses && data.addresses.length > 0) {
                    data.addresses.forEach(addr => {
                        addressHtml += `
                                <div class="p-3 rounded-2xl bg-gray-50 border border-gray-100 relative text-xs">
                                    ${addr.is_default ? '<span class="text-[8px] bg-brand-primary text-white px-1.5 py-0.5 rounded-md absolute top-2 right-2 uppercase font-black">Utama</span>' : ''}
                                    <p class="font-black text-brand-dark uppercase text-[9px] mb-1">${addr.label || 'Alamat'}</p>
                                    <p class="font-bold text-gray-700">${addr.receiver_name}</p>
                                    <p class="text-gray-500 text-[11px] leading-relaxed mt-1">
                                        ${addr.address}, ${addr.city}, ${addr.province} ${addr.postal_code}
                                    </p>
                                </div>
                            `;
                    });
                } else {
                    addressHtml = '<p class="text-gray-400 text-xs italic">Belum ada alamat tersimpan.</p>';
                }

                $('#addressList').html(addressHtml);
                $('#detailModal').removeClass('hidden').addClass('flex');
            }

            function closeDetailModal() {
                $('#detailModal').addClass('hidden').removeClass('flex');
            }
        </script>
    @endpush
@endsection