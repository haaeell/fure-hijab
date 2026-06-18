@extends('layouts.app')

@section('title', 'Kurir')

@section('content')
<div class="mx-auto max-w-4xl">

    <div class="mb-8">
        <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Kurir Pengiriman</h1>
        <p class="text-xs md:text-sm text-gray-400 font-medium mt-1">
            Aktifkan/nonaktifkan kurir yang tampil di halaman checkout. Upload logo agar tampil di resi dan halaman pelanggan.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($couriers as $courier)
        <div class="bg-white rounded-[28px] border border-gray-50 shadow-sm p-5">
            <div class="flex items-center gap-4">

                {{-- Logo / placeholder --}}
                <div class="w-14 h-14 rounded-2xl border border-gray-100 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($courier->logo)
                        <img src="{{ asset('storage/' . $courier->logo) }}" alt="{{ $courier->name }}" class="w-full h-full object-contain p-1.5">
                    @else
                        <i class="fa-solid fa-truck text-xl text-gray-200"></i>
                    @endif
                </div>

                {{-- Name + code --}}
                <div class="flex-1 min-w-0">
                    <p class="font-extrabold text-brand-dark text-sm">{{ $courier->name }}</p>
                    <code class="text-[10px] bg-gray-100 px-2 py-0.5 rounded-lg text-gray-500 font-mono">{{ $courier->code }}</code>
                </div>

                {{-- Toggle active --}}
                <form action="{{ route('couriers.toggle', $courier->id) }}" method="POST" class="flex-shrink-0">
                    @csrf @method('PATCH')
                    <button type="submit"
                        class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors focus:outline-none
                            {{ $courier->is_active ? 'bg-brand-primary' : 'bg-gray-200' }}"
                        title="{{ $courier->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform
                            {{ $courier->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </form>
            </div>

            {{-- Status label --}}
            <div class="mt-3 flex items-center justify-between">
                <span class="text-[10px] font-bold {{ $courier->is_active ? 'text-green-600' : 'text-gray-400' }}">
                    <i class="fa-solid {{ $courier->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }} mr-1"></i>
                    {{ $courier->is_active ? 'Aktif di checkout' : 'Tidak aktif' }}
                </span>

                {{-- Logo actions --}}
                <div class="flex items-center gap-2">
                    <form action="{{ route('couriers.upload-logo', $courier->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="cursor-pointer text-[10px] font-bold text-brand-primary hover:underline">
                            <i class="fa-solid fa-image mr-1"></i>{{ $courier->logo ? 'Ganti Logo' : 'Upload Logo' }}
                            <input type="file" name="logo" accept="image/*" class="sr-only" onchange="this.form.submit()">
                        </label>
                    </form>
                    @if($courier->logo)
                        <span class="text-gray-300">·</span>
                        <form action="{{ route('couriers.destroy-logo', $courier->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[10px] font-bold text-red-400 hover:text-red-600 transition-colors">
                                Hapus Logo
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <p class="mt-6 text-[11px] text-gray-400 text-center">
        <i class="fa-solid fa-circle-info mr-1"></i>
        Daftar kurir sesuai yang tersedia di Biteship API. Hubungi developer untuk menambah atau menghapus kurir.
    </p>
</div>
@endsection
