@extends('layouts.customer')

@section('title', 'Tentang Kami - FURE')

@section('content')
    <section class="py-16 bg-gray-50 min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">

            <div class="text-center max-w-2xl mx-auto mb-16">
                <nav class="flex justify-center text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                    <a href="/" class="hover:text-brand-primary">Beranda</a>
                    <span class="mx-2 text-gray-300">/</span>
                    <span class="text-brand-dark">Tentang Kami</span>
                </nav>
                <h1 class="text-3xl md:text-5xl font-extrabold text-brand-dark tracking-tight mb-4">
                    Tentang <span class="text-brand-primary">FURE</span>
                </h1>
                <p class="text-gray-500 text-sm md:text-base">
                    Elegansi dalam kesantunan. Kami menghadirkan hijab premium untuk wanita modern yang menghargai kualitas,
                    kenyamanan, dan estetika.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-brand-dark">Cerita Kami</h2>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        FURE lahir dari keinginan untuk menghadirkan produk hijab yang tidak hanya indah
                        dipandang, tetapi juga nyaman digunakan sepanjang hari. Kami percaya bahwa setiap wanita berhak
                        tampil percaya diri dengan gaya yang anggun dan berkelas.
                    </p>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Dengan material pilihan dan desain yang terus mengikuti tren, kami berkomitmen untuk memberikan
                        kualitas terbaik dalam setiap koleksi yang kami hadirkan.
                    </p>
                </div>
                <div class="bg-white rounded-[32px] p-10 shadow-sm border border-gray-100">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Visi</h3>
                            <p class="text-brand-dark font-semibold text-sm">
                                Menjadi brand hijab premium yang menginspirasi wanita untuk tampil elegan dan percaya diri.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-2">Misi</h3>
                            <ul class="text-gray-500 text-sm space-y-2">
                                <li>• Menghadirkan produk berkualitas tinggi</li>
                                <li>• Mengutamakan kenyamanan dan desain modern</li>
                                <li>• Memberikan pengalaman belanja terbaik</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mb-20">
                <div class="bg-white p-8 rounded-[28px] border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-brand-primary/10 flex items-center justify-center">
                        <i class="fa-solid fa-gem text-brand-primary text-xl"></i>
                    </div>
                    <h4 class="font-bold text-brand-dark mb-2">Premium Quality</h4>
                    <p class="text-gray-500 text-sm">Material terbaik dengan kualitas yang terjaga.</p>
                </div>

                <div class="bg-white p-8 rounded-[28px] border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-brand-primary/10 flex items-center justify-center">
                        <i class="fa-solid fa-wand-magic-sparkles text-brand-primary text-xl"></i>
                    </div>
                    <h4 class="font-bold text-brand-dark mb-2">Elegant Design</h4>
                    <p class="text-gray-500 text-sm">Desain modern yang mengikuti tren fashion.</p>
                </div>

                <div class="bg-white p-8 rounded-[28px] border border-gray-100 text-center hover:shadow-md transition">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-brand-primary/10 flex items-center justify-center">
                        <i class="fa-solid fa-heart text-brand-primary text-xl"></i>
                    </div>
                    <h4 class="font-bold text-brand-dark mb-2">Customer First</h4>
                    <p class="text-gray-500 text-sm">Kepuasan pelanggan adalah prioritas kami.</p>
                </div>
            </div>

            <div class="bg-brand-dark rounded-[40px] p-10 text-white text-center relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-primary/20 rounded-full blur-3xl"></div>
                <h2 class="text-2xl md:text-3xl font-bold mb-4">Bergabung Bersama Kami</h2>
                <p class="text-brand-secondary/70 text-sm mb-6 max-w-xl mx-auto">
                    Jadilah bagian dari perjalanan FURE dan temukan koleksi terbaik untuk gaya eleganmu.
                </p>
                <a href="{{ route('collections.index') }}"
                    class="inline-block px-8 py-3 bg-white text-brand-dark font-bold rounded-xl hover:bg-brand-secondary transition">
                    Lihat Koleksi
                </a>
            </div>

        </div>
    </section>
@endsection