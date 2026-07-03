@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Habis')
@section('description', 'Sesi kamu sudah kedaluwarsa karena terlalu lama tidak aktif. Silakan muat ulang halaman dan coba lagi.')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 1 1-20 0 10 10 0 0 1 20 0z"/>
    </svg>
@endsection

@section('actions')
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <button onclick="window.location.reload()"
            class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary text-white text-sm font-semibold rounded-xl hover:bg-brand-dark transition-colors cursor-pointer">
            Muat Ulang Halaman
        </button>
        <a href="/"
            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-brand-secondary/60 text-brand-dark text-sm font-semibold rounded-xl hover:border-brand-primary transition-colors">
            Ke Beranda
        </a>
    </div>
@endsection
