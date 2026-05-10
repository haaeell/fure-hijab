@extends('layouts.customer')

@section('title', 'Koleksi Hijab Premium & Elegan')

@section('content')

    <section class="relative pt-5 pb-5 lg:pt-5 lg:pb-5 overflow-hidden px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
            <div
                class="absolute top-[-10%] right-[-5%] w-[400px] h-[400px] bg-soft-mint rounded-full blur-[120px] opacity-70">
            </div>
            <div
                class="absolute bottom-[-10%] left-[-5%] w-[300px] h-[300px] bg-soft-blue rounded-full blur-[100px] opacity-70">
            </div>
        </div>

        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
            <div class="w-full lg:w-1/2 text-center lg:text-left space-y-6 order-2 lg:order-1">
                <span
                    class="inline-block px-4 py-1.5 bg-brand-primary/10 text-brand-primary text-xs font-bold tracking-widest uppercase rounded-full shadow-inner">Koleksi
                    Terbaru 2026</span>
                <h1 class="text-4xl md:text-5xl lg:text-7xl font-extrabold text-brand-dark leading-[1.1] tracking-tight">
                    Pancarkan Pesonamu dalam <span class="text-brand-primary italic font-medium relative">Kesantunan.<div
                            class="absolute bottom-1 left-0 w-full h-1 bg-brand-secondary rounded-full opacity-50"></div>
                    </span>
                </h1>
                <p class="text-gray-500 text-base md:text-lg mb-10 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                    Temukan koleksi hijab premium dengan material terbaik yang dirancang khusus untuk kenyamanan dan
                    kepercayaan diri Anda setiap hari.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                    <a href="#koleksi"
                        class="group relative px-10 py-4 bg-brand-dark text-white font-bold rounded-2xl flex items-center justify-center gap-3 overflow-hidden shadow-xl shadow-brand-dark/10 hover:shadow-brand-dark/20 transition-all hover:-translate-y-0.5 active:scale-95">
                        <div
                            class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-shimmer">
                        </div>
                        Belanja Sekarang
                        <i
                            class="fa-solid fa-arrow-right-long text-sm group-hover:translate-x-1 transition-transform relative z-10"></i>
                    </a>
                </div>
            </div>

            <div class="w-full lg:w-1/2 order-1 lg:order-2">
                <div
                    class="relative z-10 w-full aspect-[5/4] sm:aspect-square bg-gradient-to-br from-brand-secondary to-brand-primary rounded-[50px] overflow-hidden shadow-2xl shadow-brand-primary/20 border-4 border-white">
                    <img src="https://images.unsplash.com/photo-1585435465945-bef5a93f8849?auto=format&fit=crop&q=80&w=800"
                        alt="Model Hijab Al-Hayya"
                        class="w-full h-full object-cover mix-blend-overlay opacity-90 transition-transform duration-1000 hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/50 to-transparent"></div>

                    <div
                        class="absolute bottom-6 left-6 right-6 bg-white/20 backdrop-blur-lg p-4 rounded-2xl border border-white/20 flex items-center gap-4 shadow-inner">
                        <div
                            class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-brand-primary text-xl shadow">
                            <i class="fa-solid fa-crown"></i>
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">Premium Quality Material</p>
                            <p class="text-white/80 text-xs">Dijamin nyaman & adem seharian</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <p class="text-xs font-bold text-brand-primary uppercase tracking-widest mb-2">Jelajahi Pilihan</p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-brand-dark">Pilih Kategori</h2>
                </div>
                <a href="{{ route('collections.index') }}"
                    class="hidden md:flex items-center gap-2 text-sm font-bold text-brand-primary hover:underline">
                    Lihat Semua
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3 md:gap-5">
                @foreach($categories as $category)
                    <a href="{{ route('collections.index', ['category' => $category->slug]) }}"
                        class="group flex flex-col items-center gap-3 p-4 md:p-5 bg-gray-50 rounded-3xl border border-gray-100 hover:border-brand-primary/30 hover:bg-brand-primary/5 hover:shadow-lg hover:shadow-brand-primary/10 transition-all duration-300 hover:-translate-y-1">
                        <div
                            class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-white flex items-center justify-center shadow-sm border border-gray-100 group-hover:border-brand-primary/20 group-hover:shadow-brand-primary/10 transition-all duration-300">
                            <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://cdn-icons-png.flaticon.com/512/3144/3144453.png' }}"
                                class="w-8 h-8 md:w-9 md:h-9 object-contain transition-transform duration-300 group-hover:scale-110"
                                alt="{{ $category->name }}">
                        </div>
                        <div class="text-center">
                            <span
                                class="block text-xs md:text-sm font-bold text-brand-dark group-hover:text-brand-primary transition-colors leading-tight">{{ $category->name }}</span>
                            @if(isset($category->products_count))
                                <span class="block text-[10px] text-gray-400 mt-0.5">{{ $category->products_count }} produk</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('collections.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-bold text-brand-primary hover:underline">
                    Lihat Semua Kategori
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    @if($flashSaleProducts->count() > 0)
        <section
            class="py-16 bg-gray-50 px-4 sm:px-6 lg:px-8 rounded-[40px] md:rounded-[60px] my-10 border border-gray-100 shadow-inner">
            <div class="max-w-7xl mx-auto relative">

                <div class="flex items-center gap-4 mb-12">
                    <div
                        class="px-4 py-2 bg-red-500 text-white font-bold text-sm rounded-xl flex items-center gap-2 shadow-lg shadow-red-500/20">
                        <i class="fa-solid fa-bolt-lightning animate-pulse"></i>
                        FLASH SALE
                    </div>
                    <p class="text-gray-500 text-sm">Penawaran terbatas, jangan sampai kehabisan!</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    @foreach($flashSaleProducts as $product)
                        @include('user.components.product-card', ['product' => $product, 'isFlashSale' => true])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="koleksi" class="py-16 bg-white px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-brand-dark mb-4">Koleksi Terbaru</h2>
                <p class="text-gray-500 max-w-xl mx-auto text-sm md:text-base leading-relaxed">Kualitas premium dengan
                    desain elegan, dirancang khusus untuk kenyamanan Anda.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
                @foreach($latestProducts as $product)
                    @include('user.components.product-card', ['product' => $product, 'isFlashSale' => false])
                @endforeach
            </div>

            <div class="mt-16 text-center">
                <a href="/collections"
                    class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-brand-dark font-bold rounded-xl border-2 border-brand-dark hover:bg-brand-dark hover:text-white transition-all active:scale-95 group">
                    Lihat Koleksi Lengkap
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </section>

@endsection