@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Ditolak')
@section('description', 'Kamu tidak memiliki izin untuk mengakses halaman ini. Silakan login dengan akun yang sesuai.')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z"/>
    </svg>
@endsection

@section('actions')
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="/"
            class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary text-white text-sm font-semibold rounded-xl hover:bg-brand-dark transition-colors">
            Ke Beranda
        </a>
        @guest
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-brand-secondary/60 text-brand-dark text-sm font-semibold rounded-xl hover:border-brand-primary transition-colors">
            Login
        </a>
        @endguest
    </div>
@endsection
