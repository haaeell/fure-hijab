<div class="mx-auto max-w-4xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Pusat Notifikasi</h1>
            <p class="text-xs text-gray-400 font-medium mt-1">Pantau semua aktivitas toko {{ $adminStoreName }} dalam satu tempat.
            </p>
        </div>
        <button
            class="px-5 py-2.5 bg-white border border-gray-100 text-brand-dark rounded-2xl text-xs font-bold hover:bg-brand-primary hover:text-white transition-all shadow-sm">
            Tandai Semua Dibaca
        </button>
    </div>

    <div class="space-y-4">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2">Hari Ini</p>

        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 divide-y divide-gray-50 overflow-hidden">
            <div class="p-6 flex items-start gap-5 bg-soft-mint/20 relative">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-primary"></div>
                <div
                    class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex-shrink-0 flex items-center justify-center">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <h4 class="font-bold text-brand-dark text-sm">Pesanan Baru: #INV-2024001</h4>
                        <span class="text-[10px] text-gray-400 font-medium">10:45 AM</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Pelanggan <b>Dewi Sartika</b> baru saja
                        melakukan checkout untuk 3 item. Segera cek pesanan untuk proses pengemasan.</p>
                    <div class="mt-4 flex gap-2">
                        <button
                            class="px-4 py-2 bg-brand-primary text-white text-[10px] font-black uppercase rounded-xl hover:bg-brand-dark transition-all">Proses
                            Sekarang</button>
                        <button
                            class="px-4 py-2 bg-gray-100 text-gray-500 text-[10px] font-black uppercase rounded-xl hover:bg-gray-200 transition-all">Detail</button>
                    </div>
                </div>
            </div>

            <div class="p-6 flex items-start gap-5 opacity-75">
                <div
                    class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex-shrink-0 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <h4 class="font-bold text-brand-dark text-sm">Pembayaran Diterima</h4>
                        <span class="text-[10px] text-gray-400 font-medium">08:20 AM</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Pembayaran untuk pesanan #INV-2023998 telah
                        diverifikasi otomatis oleh sistem.</p>
                </div>
            </div>
        </div>

        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] ml-2 mt-8">Kemarin</p>
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-50 divide-y divide-gray-50 overflow-hidden">
            <div class="p-6 flex items-start gap-5">
                <div
                    class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-500 flex-shrink-0 flex items-center justify-center">
                    <i class="fa-solid fa-user-plus text-lg"></i>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <h4 class="font-bold text-brand-dark text-sm">Pelanggan Baru</h4>
                        <span class="text-[10px] text-gray-400 font-medium">Kemarin, 14:00 PM</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Selamat! <b>Riana Putri</b> bergabung menjadi
                        pelanggan {{ $adminStoreName }}.</p>
                </div>
            </div>
        </div>
    </div>
</div>