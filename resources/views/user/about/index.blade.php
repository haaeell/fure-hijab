@extends('layouts.customer')

@section('title', 'Tentang Kami - ' . $storeName)
@section('seo_title', 'Tentang Kami — ' . $storeName)
@section('seo_description', 'Kenali ' . $storeName . ', toko hijab premium online yang menghadirkan hijab dan modest wear berkualitas dengan desain elegan, nyaman, dan mudah dipakai.')
@section('seo_keywords', 'tentang ' . $storeName . ', ' . strtolower($storeName) . ' hijab, toko hijab premium, modest wear Indonesia')
@section('canonical', route('about.index'))

@section('content')
    <section class="bg-[#f8f3ee]">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="mb-8 flex text-[10px] font-bold uppercase tracking-[0.24em] text-brand-dark/45">
                <a href="/" class="transition hover:text-brand-primary">Home</a>
                <span class="mx-2 text-brand-secondary">/</span>
                <span class="text-brand-dark">Tentang Kami</span>
            </nav>

            <div class="grid gap-4 lg:grid-cols-[1.05fr_0.95fr] lg:items-stretch">
                <div class="relative min-h-[520px] overflow-hidden bg-brand-dark">
                    <img src="/banner2.webp"
                        alt="Tentang {{ $storeName }}"
                        class="absolute inset-0 h-full w-full object-cover opacity-80">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/85 via-brand-dark/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 max-w-xl p-6 text-white sm:p-10">
                        <p class="mb-4 text-[11px] font-bold uppercase tracking-[0.28em] text-brand-secondary">About {{ $storeName }}</p>
                        <h1 class="text-4xl font-semibold leading-tight sm:text-5xl">Elegansi dalam kesantunan.</h1>
                        <p class="mt-4 text-sm leading-7 text-white/75">
                            {{ $storeName }} menghadirkan hijab premium untuk wanita modern yang menghargai kualitas, kenyamanan, dan estetika.
                        </p>
                    </div>
                </div>

                <div class="grid gap-4">
                    <div class="bg-white p-6 sm:p-8">
                        <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-brand-primary">Our Story</p>
                        <h2 class="mt-3 text-3xl font-semibold">Cerita Kami</h2>
                        <p class="mt-4 text-sm leading-7 text-brand-dark/60">
                            {{ $storeName }} lahir dari keinginan untuk menghadirkan produk hijab yang tidak hanya indah dipandang,
                            tetapi juga nyaman digunakan sepanjang hari.
                        </p>
                        <p class="mt-4 text-sm leading-7 text-brand-dark/60">
                            Dengan material pilihan dan desain yang mengikuti kebutuhan gaya hidup modern, kami berkomitmen
                            untuk memberikan pengalaman berbelanja yang rapi, mudah, dan menyenangkan.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="bg-white p-5">
                            <i class="fa-solid fa-gem text-brand-primary"></i>
                            <h3 class="mt-4 text-sm font-semibold text-brand-dark">Premium Quality</h3>
                            <p class="mt-2 text-sm leading-6 text-brand-dark/55">Material yang nyaman dan terjaga kualitasnya.</p>
                        </div>
                        <div class="bg-white p-5">
                            <i class="fa-solid fa-wand-magic-sparkles text-brand-primary"></i>
                            <h3 class="mt-4 text-sm font-semibold text-brand-dark">Elegant Design</h3>
                            <p class="mt-2 text-sm leading-6 text-brand-dark/55">Desain bersih, modern, dan mudah dipadukan.</p>
                        </div>
                        <div class="bg-white p-5">
                            <i class="fa-solid fa-heart text-brand-primary"></i>
                            <h3 class="mt-4 text-sm font-semibold text-brand-dark">Customer First</h3>
                            <p class="mt-2 text-sm leading-6 text-brand-dark/55">Pengalaman belanja yang jelas dan nyaman.</p>
                        </div>
                    </div>

                    <div class="bg-brand-dark p-6 text-white sm:p-8">
                        <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-brand-secondary">Mission</p>
                        <h2 class="mt-3 text-2xl font-semibold">Misi kami sederhana.</h2>
                        <ul class="mt-4 space-y-3 text-sm leading-6 text-white/75">
                            <li>• Menghadirkan produk berkualitas tinggi.</li>
                            <li>• Mengutamakan kenyamanan dan desain modern.</li>
                            <li>• Memberikan pengalaman belanja terbaik.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-12 bg-white p-6 sm:p-8">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-brand-primary">Join us</p>
                        <h2 class="mt-3 text-3xl font-semibold">Bergabung bersama kami</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-brand-dark/60">
                            Jadilah bagian dari perjalanan {{ $storeName }} dan temukan koleksi terbaik untuk gaya eleganmu.
                        </p>
                    </div>
                    <a href="{{ route('collections.index') }}"
                        class="inline-flex bg-brand-primary px-6 py-3 text-xs font-bold uppercase tracking-[0.16em] text-white transition hover:bg-brand-dark">
                        Lihat Koleksi
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
