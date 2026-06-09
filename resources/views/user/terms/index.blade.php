@extends('layouts.customer')

@section('title', 'Syarat & Ketentuan - Al-Hayya Hijab')

@section('content')
    <section class="bg-[#F8FBF8] min-h-screen px-4 sm:px-6 lg:px-8 pt-28 pb-14">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-brand-dark text-white p-8">
                    <p class="text-[10px] font-black text-brand-primary uppercase tracking-[0.3em]">Al-Hayya Hijab</p>
                    <h1 class="text-3xl font-extrabold mt-2">Syarat & Ketentuan</h1>
                    <p class="text-white/60 text-sm mt-3">Terakhir diperbarui: 9 Juni 2026</p>
                </div>

                <div class="p-6 sm:p-8 space-y-7 text-sm text-gray-600 leading-relaxed">
                    <div>
                        <h2 class="text-lg font-extrabold text-brand-dark mb-2">1. Akun Pengguna</h2>
                        <p>Pengguna wajib memberikan data yang benar saat membuat akun, termasuk nama, email, dan nomor WhatsApp untuk kebutuhan transaksi dan pengiriman.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-extrabold text-brand-dark mb-2">2. Pemesanan dan Pembayaran</h2>
                        <p>Pesanan dibuat setelah customer menyelesaikan checkout. Pesanan yang belum dibayar harus diselesaikan dalam batas waktu pembayaran yang ditampilkan pada detail pesanan.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-extrabold text-brand-dark mb-2">3. Pengiriman</h2>
                        <p>Biaya dan estimasi pengiriman mengikuti layanan kurir yang dipilih customer. Pastikan alamat sudah benar sebelum membuat pesanan.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-extrabold text-brand-dark mb-2">4. Pembatalan Pesanan</h2>
                        <p>Customer dapat membatalkan pesanan selama pesanan belum diproses. Alasan pembatalan wajib diisi untuk membantu proses administrasi.</p>
                    </div>

                    <div>
                        <h2 class="text-lg font-extrabold text-brand-dark mb-2">5. Privasi Data</h2>
                        <p>Data customer digunakan untuk kebutuhan akun, transaksi, layanan pelanggan, dan pengiriman. Al-Hayya tidak menjual data pribadi customer kepada pihak lain.</p>
                    </div>

                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-brand-primary text-white font-bold hover:bg-brand-dark transition-all">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Registrasi
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
