@extends('layouts.app')

@section('title', 'Kurir')

@section('content')
<div class="mx-auto max-w-4xl">

    <div class="mb-8">
        <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Kurir Pengiriman</h1>
        <p class="text-xs md:text-sm text-gray-400 font-medium mt-1">
            Aktifkan/nonaktifkan kurir yang tampil di checkout. Upload logo agar muncul di resi dan pilihan pengiriman pelanggan.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($couriers as $courier)
        <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-5 transition-shadow hover:shadow-md"
             id="courier-card-{{ $courier->id }}">
            <div class="flex items-center gap-4">

                {{-- Logo --}}
                <div class="w-14 h-14 rounded-2xl border border-gray-100 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                    @if($courier->logo)
                        <img src="{{ asset('storage/' . $courier->logo) }}" alt="{{ $courier->name }}"
                             class="w-full h-full object-contain p-1.5">
                    @else
                        <i class="fa-solid fa-truck text-xl text-gray-200"></i>
                    @endif
                </div>

                {{-- Nama + kode --}}
                <div class="flex-1 min-w-0">
                    <p class="font-extrabold text-brand-dark text-sm">{{ $courier->name }}</p>
                    <code class="text-[10px] bg-gray-100 px-2 py-0.5 rounded-lg text-gray-500 font-mono">{{ $courier->code }}</code>
                </div>

                {{-- Toggle AJAX --}}
                <button type="button"
                    class="courier-toggle relative inline-flex h-7 w-12 flex-shrink-0 items-center rounded-full transition-colors focus:outline-none"
                    data-id="{{ $courier->id }}"
                    data-active="{{ $courier->is_active ? '1' : '0' }}"
                    data-url="{{ route('couriers.toggle', $courier->id) }}"
                    title="{{ $courier->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                    <span class="toggle-track absolute inset-0 rounded-full transition-colors
                        {{ $courier->is_active ? 'bg-brand-primary' : 'bg-gray-200' }}"></span>
                    <span class="toggle-thumb relative inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform z-10
                        {{ $courier->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            {{-- Status + logo actions --}}
            <div class="mt-3 flex items-center justify-between">
                <span class="courier-status text-[10px] font-bold {{ $courier->is_active ? 'text-green-600' : 'text-gray-400' }}">
                    <i class="fa-solid {{ $courier->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }} mr-1"></i>
                    {{ $courier->is_active ? 'Aktif di checkout' : 'Tidak aktif' }}
                </span>

                <div class="flex items-center gap-2">
                    <form action="{{ route('couriers.upload-logo', $courier->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="cursor-pointer text-[10px] font-bold text-brand-primary hover:underline transition-colors">
                            <i class="fa-solid fa-image mr-1"></i>{{ $courier->logo ? 'Ganti Logo' : 'Upload Logo' }}
                            <input type="file" name="logo" accept="image/*" class="sr-only" onchange="this.form.submit()">
                        </label>
                    </form>
                    @if($courier->logo)
                        <span class="text-gray-300">·</span>
                        <form action="{{ route('couriers.destroy-logo', $courier->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-[10px] font-bold text-red-400 hover:text-red-600 transition-colors">
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

@push('scripts')
<script>
$(function () {
    const csrf = $('meta[name="csrf-token"]').attr('content');

    $(document).on('click', '.courier-toggle', function () {
        const $btn    = $(this);
        const url     = $btn.data('url');
        const isActive = $btn.data('active') === '1' || $btn.data('active') === 1;
        const $card   = $btn.closest('[id^="courier-card-"]');
        const $track  = $btn.find('.toggle-track');
        const $thumb  = $btn.find('.toggle-thumb');
        const $status = $card.find('.courier-status');

        // Disable sementara agar tidak double-click
        $btn.prop('disabled', true).css('opacity', '0.6');

        $.ajax({
            url: url,
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            success: function (res) {
                const nowActive = res.is_active;

                // Update data attribute
                $btn.data('active', nowActive ? '1' : '0');

                // Animasi toggle
                if (nowActive) {
                    $track.removeClass('bg-gray-200').addClass('bg-brand-primary');
                    $thumb.removeClass('translate-x-1').addClass('translate-x-6');
                } else {
                    $track.removeClass('bg-brand-primary').addClass('bg-gray-200');
                    $thumb.removeClass('translate-x-6').addClass('translate-x-1');
                }

                // Update label status
                $status.html(
                    '<i class="fa-solid ' + (nowActive ? 'fa-circle-check' : 'fa-circle-xmark') + ' mr-1"></i>'
                    + (nowActive ? 'Aktif di checkout' : 'Tidak aktif')
                ).removeClass('text-green-600 text-gray-400')
                 .addClass(nowActive ? 'text-green-600' : 'text-gray-400');

                // Toast notifikasi
                Swal.mixin({
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 2500, timerProgressBar: true,
                }).fire({
                    icon: nowActive ? 'success' : 'info',
                    title: res.message,
                });
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Gagal mengubah status kurir', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
            },
            complete: function () {
                $btn.prop('disabled', false).css('opacity', '1');
            },
        });
    });
});
</script>
@endpush
