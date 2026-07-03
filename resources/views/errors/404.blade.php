@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('description', 'Halaman yang kamu cari tidak ada atau sudah dipindahkan. Yuk balik ke beranda dan jelajahi koleksi kami.')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
    </svg>
@endsection

@section('actions')
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="/"
            class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary text-white text-sm font-semibold rounded-xl hover:bg-brand-dark transition-colors">
            Ke Beranda
        </a>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-brand-secondary/60 text-brand-dark text-sm font-semibold rounded-xl hover:border-brand-primary transition-colors">
            Kembali
        </a>
    </div>
@endsection
