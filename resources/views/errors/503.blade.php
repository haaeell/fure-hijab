@extends('errors.layout')

@section('code', '503')
@section('title', 'Sedang Dalam Pemeliharaan')
@section('description', 'Toko kami sedang dalam pemeliharaan sebentar. Kami akan segera kembali. Terima kasih atas kesabarannya!')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.654-4.654m5.65-5.65 1.36-1.36a3.375 3.375 0 1 1 4.773 4.773l-1.36 1.36M11.42 15.17 7 8.5"/>
    </svg>
@endsection

@section('actions')
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <button onclick="window.location.reload()"
            class="inline-flex items-center gap-2 px-6 py-3 bg-brand-primary text-white text-sm font-semibold rounded-xl hover:bg-brand-dark transition-colors cursor-pointer">
            Coba Lagi
        </button>
    </div>

    @if(isset($exception) && $exception->getMessage())
        <p class="mt-6 text-xs text-brand-dark/30 font-mono">{{ $exception->getMessage() }}</p>
    @endif
@endsection
