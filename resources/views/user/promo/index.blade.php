@extends('layouts.customer')

@section('title', 'Promo Spesial - ' . $storeName)

@section('content')
    <section class="py-16 bg-[#FBFBFE] min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">

            <div class="text-center mb-16">
                <nav class="flex justify-center items-center space-x-2 text-xs font-semibold uppercase tracking-[0.2em] text-gray-400 mb-6">
                    <a href="/" class="hover:text-brand-primary transition">Beranda</a>
                    <span class="text-gray-300">/</span>
                    <span class="text-brand-dark">Promo</span>
                </nav>
                <h1 class="text-4xl md:text-6xl font-black text-brand-dark tracking-tight mb-4">
                    Penawaran <span class="text-brand-primary relative">Terbatas<span class="absolute bottom-1 left-0 w-full h-2 bg-brand-primary/10 -z-10"></span></span>
                </h1>
                <p class="text-gray-500 max-w-md mx-auto leading-relaxed">
                    Koleksi hijab terbaik kini lebih hemat. Salin kode promo dan nikmati keistimewaannya.
                </p>
            </div>

            @if($coupons->count())
                <div class="grid md:grid-cols-2 gap-8 mb-20">
                    @foreach($coupons as $coupon)
                        <div class="group relative bg-white border border-gray-100 rounded-3xl p-6 transition-all duration-300 hover:shadow-[0_20px_50px_rgba(0,0,0,0.05)] hover:-translate-y-1 overflow-hidden">
                            
                            <div class="absolute top-1/2 -left-3 w-6 h-6 bg-[#FBFBFE] border-r border-gray-100 rounded-full -translate-y-1/2"></div>
                            <div class="absolute top-1/2 -right-3 w-6 h-6 bg-[#FBFBFE] border-l border-gray-100 rounded-full -translate-y-1/2"></div>

                            <div class="flex flex-col h-full">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="p-3 bg-brand-primary/5 rounded-2xl">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                        </svg>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-3xl font-black text-brand-dark">
                                            @if($coupon->type === 'percent')
                                                {{ $coupon->value }}% <span class="text-sm font-medium text-gray-400">OFF</span>
                                            @else
                                                <span class="text-sm font-medium text-gray-400">POTONGAN</span> <br>
                                                Rp{{ number_format($coupon->value/1000, 0) }}k
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <h3 class="font-bold text-gray-800 text-lg mb-1 leading-tight">{{ $coupon->name }}</h3>
                                    <div class="flex items-center text-[11px] text-gray-400 gap-3">
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            Min. Rp{{ number_format($coupon->min_purchase, 0, ',', '.') }}
                                        </span>
                                        @if($coupon->expired_at)
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($coupon->expired_at)->format('d M Y') }}
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-auto relative group/code">
                                    <div class="flex items-center justify-between bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-1.5 pl-4 transition-colors group-hover/code:bg-brand-primary/5">
                                        <span class="font-mono font-bold tracking-wider text-brand-dark text-sm">{{ $coupon->code }}</span>
                                        <button 
                                            onclick="copyCoupon(this, '{{ $coupon->code }}')"
                                            class="bg-white text-brand-dark text-[11px] font-bold px-4 py-2 rounded-xl shadow-sm border border-gray-100 active:scale-95 transition-all hover:bg-brand-dark hover:text-white"
                                        >
                                            SALIN
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-[40px] py-24 text-center border-2 border-dashed border-gray-100 mb-20">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-brand-dark mb-2">Belum Ada Promo Aktif</h3>
                    <p class="text-gray-400 text-sm max-w-xs mx-auto">Kami sedang menyiapkan kejutan spesial untukmu. Cek kembali nanti ya!</p>
                </div>
            @endif

            <div class="relative bg-brand-dark rounded-[2.5rem] p-8 md:p-14 text-center overflow-hidden">
                <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-80 h-80 bg-brand-primary/30 rounded-full blur-[100px]"></div>
                <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-80 h-80 bg-brand-primary/10 rounded-full blur-[80px]"></div>
                
                <div class="relative z-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Siap Tampil Menawan?</h2>
                    <p class="text-gray-400 mb-8 max-w-md mx-auto">Gunakan kodenya sekarang sebelum masa berlaku habis dan stok koleksi menipis.</p>
                    <a href="{{ route('collections.index') }}" 
                       class="inline-flex items-center gap-2 px-10 py-4 bg-brand-primary text-white font-bold rounded-2xl hover:bg-opacity-90 transition-all shadow-lg shadow-brand-primary/20">
                        Mulai Belanja
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script>
        function copyCoupon(btn, code) {
            navigator.clipboard.writeText(code);
            const originalText = btn.innerText;
            btn.innerText = 'TERSALIN!';
            btn.classList.add('bg-green-500', 'text-white', 'border-green-500');
            
            setTimeout(() => {
                btn.innerText = originalText;
                btn.classList.remove('bg-green-500', 'text-white', 'border-green-500');
            }, 2000);
        }
    </script>
@endsection