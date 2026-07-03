@extends('errors.layout')

@section('code', '500')
@section('title', 'Terjadi Kesalahan')
@section('description', 'Sepertinya ada masalah di server kami. Tim kami sedang menanganinya. Coba lagi dalam beberapa saat.')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
@endsection

@section('actions')
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <button onclick="window.location.reload()"
            class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary text-white text-sm font-semibold rounded-xl hover:bg-brand-dark transition-colors cursor-pointer">
            Coba Lagi
        </button>
        <a href="/"
            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-brand-secondary/60 text-brand-dark text-sm font-semibold rounded-xl hover:border-brand-primary transition-colors">
            Ke Beranda
        </a>
    </div>
@endsection
