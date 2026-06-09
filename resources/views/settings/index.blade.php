@extends('layouts.app')

@section('title', 'Pengaturan Integrasi')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-8">
            <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Pengaturan Integrasi</h1>
            <p class="text-xs md:text-sm text-gray-400 font-medium mt-1">Atur kredensial RajaOngkir dan Midtrans langsung dari panel admin.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-brand-dark">RajaOngkir</h3>
                        <p class="text-xs text-gray-400 font-medium">Digunakan untuk cek ongkir dan pelacakan resi.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">API Key</label>
                        <input type="text" name="rajaongkir_api_key" value="{{ old('rajaongkir_api_key', $settings['rajaongkir_api_key']) }}"
                            id="rajaongkir_api_key"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Origin Pengiriman</label>
                        <select id="rajaongkir_origin_select"
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl text-sm font-semibold">
                            @if(old('rajaongkir_origin', $settings['rajaongkir_origin']))
                                <option value="{{ old('rajaongkir_origin', $settings['rajaongkir_origin']) }}" selected>
                                    Origin ID: {{ old('rajaongkir_origin', $settings['rajaongkir_origin']) }}
                                </option>
                            @endif
                        </select>
                        <input type="hidden" name="rajaongkir_origin" id="rajaongkir_origin"
                            value="{{ old('rajaongkir_origin', $settings['rajaongkir_origin']) }}">
                        <p class="text-[11px] text-gray-400 ml-1">Ketik minimal 3 huruf nama kecamatan, kota, atau provinsi lalu pilih origin dari RajaOngkir.</p>
                        <p id="origin-help" class="text-[11px] text-gray-400 ml-1"></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-brand-dark">Midtrans</h3>
                        <p class="text-xs text-gray-400 font-medium">Dipakai untuk Snap checkout dan callback pembayaran.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Server Key</label>
                        <input type="text" name="midtrans_server_key" value="{{ old('midtrans_server_key', $settings['midtrans_server_key']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Client Key</label>
                        <input type="text" name="midtrans_client_key" value="{{ old('midtrans_client_key', $settings['midtrans_client_key']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>

                    <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-200 bg-gray-50/50 cursor-pointer">
                        <input type="checkbox" name="midtrans_is_production" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-primary"
                            {{ old('midtrans_is_production', $settings['midtrans_is_production']) ? 'checked' : '' }}>
                        <span>
                            <span class="block text-sm font-bold text-brand-dark">Mode Production</span>
                            <span class="block text-xs text-gray-400">Aktifkan saat memakai kredensial Midtrans production.</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 p-4 rounded-2xl border border-gray-200 bg-gray-50/50 cursor-pointer">
                        <input type="checkbox" name="midtrans_is_sanitized" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-primary"
                            {{ old('midtrans_is_sanitized', $settings['midtrans_is_sanitized']) ? 'checked' : '' }}>
                        <span>
                            <span class="block text-sm font-bold text-brand-dark">Sanitized Request</span>
                            <span class="block text-xs text-gray-400">Biarkan aktif untuk request payload yang lebih aman.</span>
                        </span>
                    </label>

                    <label class="md:col-span-2 flex items-start gap-3 p-4 rounded-2xl border border-gray-200 bg-gray-50/50 cursor-pointer">
                        <input type="checkbox" name="midtrans_is_3ds" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-primary"
                            {{ old('midtrans_is_3ds', $settings['midtrans_is_3ds']) ? 'checked' : '' }}>
                        <span>
                            <span class="block text-sm font-bold text-brand-dark">Aktifkan 3DS</span>
                            <span class="block text-xs text-gray-400">Disarankan tetap aktif untuk transaksi kartu kredit.</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="px-8 py-3 bg-brand-primary text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            const $originSelect = $('#rajaongkir_origin_select');
            const $originInput = $('#rajaongkir_origin');
            const $originHelp = $('#origin-help');
            const $apiKeyInput = $('#rajaongkir_api_key');

            $originSelect.select2({
                placeholder: 'Cari origin RajaOngkir',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('settings.rajaongkir.origins') }}",
                    dataType: 'json',
                    delay: 350,
                    data: function (params) {
                        return {
                            search: params.term || '',
                            api_key: $apiKeyInput.val()
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.data || []
                        };
                    },
                    transport: function (params, success, failure) {
                        const term = (params.data && params.data.search ? params.data.search : '').trim();

                        if (term.length < 3) {
                            success({ data: [] });
                            return null;
                        }

                        return $.ajax(params).done(success).fail(function (xhr) {
                            const message = xhr.responseJSON?.message || 'Gagal mencari origin RajaOngkir.';
                            $originHelp.text(message).removeClass('text-gray-400').addClass('text-red-500');
                            failure(xhr);
                        });
                    }
                }
            });

            $originSelect.on('select2:select', function (e) {
                const selected = e.params.data;
                $originInput.val(selected.id);
                $originHelp.text('Origin terpilih: ' + selected.text + ' (ID: ' + selected.id + ')')
                    .removeClass('text-red-500')
                    .addClass('text-gray-400');
            });

            $originSelect.on('select2:clear', function () {
                $originInput.val('');
                $originHelp.text('Origin dibersihkan. Silakan cari dan pilih ulang lokasi origin.')
                    .removeClass('text-red-500')
                    .addClass('text-gray-400');
            });

            if ($originInput.val()) {
                $originHelp.text('Origin tersimpan saat ini memakai ID: ' + $originInput.val())
                    .removeClass('text-red-500')
                    .addClass('text-gray-400');
            }
        });
    </script>
@endpush
