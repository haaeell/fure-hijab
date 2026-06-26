@extends('layouts.app')

@section('title', 'Pengaturan Toko')

@section('content')
<div class="mx-auto max-w-4xl">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Pengaturan</h1>
    </div>

    {{-- Tab --}}
    <div class="flex gap-2 mb-8 border-b border-gray-100">
        <a href="{{ route('settings.store') }}"
            class="px-5 py-2.5 text-xs font-black tracking-widest rounded-t-xl transition-all border-b-2 border-brand-primary text-brand-primary bg-brand-primary/5">
            TOKO
        </a>
        <a href="{{ route('settings.index') }}"
            class="px-5 py-2.5 text-xs font-black tracking-widest rounded-t-xl transition-all border-b-2 border-transparent text-gray-400 hover:text-brand-dark">
            INTEGRASI API
        </a>
    </div>

    <form action="{{ route('settings.store.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ── Logo & Nama ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-store text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-brand-dark">Identitas Toko</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Tampil di navbar, footer, dan halaman pelanggan.</p>
                </div>
            </div>

            {{-- Logo upload with live preview --}}
            <div class="mb-8">
                <label class="block mb-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Logo Toko</label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                    <div class="relative flex-shrink-0">
                        <div id="logoPreviewWrap" class="w-24 h-24 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden transition-all hover:border-brand-primary group">
                            @if($settings['store_logo'])
                                <img id="logoPreview"
                                     src="{{ asset('storage/' . $settings['store_logo']) }}"
                                     alt="Logo toko"
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <i class="fa-solid fa-camera text-white text-xl"></i>
                                </div>
                            @else
                                <div id="logoPlaceholder" class="flex flex-col items-center gap-1 text-gray-300">
                                    <i class="fa-solid fa-image text-3xl"></i>
                                    <span class="text-[9px] font-bold uppercase tracking-widest">Logo</span>
                                </div>
                                <img id="logoPreview" src="" alt="Preview" class="hidden w-full h-full object-cover">
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <label for="logoInput" class="inline-flex cursor-pointer items-center gap-2 px-5 py-2.5 rounded-2xl border-2 border-dashed border-brand-primary/40 bg-brand-primary/5 text-brand-primary text-xs font-bold hover:bg-brand-primary hover:text-white hover:border-brand-primary transition-all">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i>
                            {{ $settings['store_logo'] ? 'Ganti Logo' : 'Upload Logo' }}
                        </label>
                        <input type="file" id="logoInput" name="store_logo" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml" class="sr-only">
                        <p class="mt-2 text-[11px] text-gray-400">PNG, JPG, SVG, WebP — maks. 2 MB. Disarankan persegi (1:1).</p>
                        @if($settings['store_logo'])
                            <p class="mt-1 text-[10px] font-semibold text-brand-primary">
                                <i class="fa-solid fa-circle-check mr-1"></i>Logo aktif terpasang
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        Nama Toko <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="store_name"
                        value="{{ old('store_name', $settings['store_name']) }}"
                        placeholder="contoh: FURE"
                        required
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                </div>

                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Email Toko</label>
                    <input type="email" name="store_email"
                        value="{{ old('store_email', $settings['store_email']) }}"
                        placeholder="hello@toko.com"
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                </div>
            </div>
        </div>

        {{-- ── Kontak ───────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-phone text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-brand-dark">Kontak</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Tampil di footer dan tombol WhatsApp pada halaman toko.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Telepon</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">
                            <i class="fa-solid fa-phone text-xs"></i>
                        </span>
                        <input type="text" name="store_phone"
                            value="{{ old('store_phone', $settings['store_phone']) }}"
                            placeholder="021-xxxx-xxxx"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor WhatsApp</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">
                            <i class="fa-brands fa-whatsapp text-sm text-green-500"></i>
                        </span>
                        <input type="text" name="store_whatsapp"
                            value="{{ old('store_whatsapp', $settings['store_whatsapp']) }}"
                            placeholder="6281234567890 (format internasional)"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <p class="ml-1 text-[10px] text-gray-400">Gunakan format internasional tanpa tanda +, contoh: <strong>62812xxx</strong></p>
                </div>
            </div>
        </div>

        {{-- ── Alamat ───────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-location-dot text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-brand-dark">Alamat Toko</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Tampil di footer dan halaman Store Locator.</p>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat Lengkap</label>
                <textarea name="store_address" rows="4"
                    placeholder="Jl. Contoh No. 1, Kelurahan, Kecamatan, Kota, Provinsi, 12345"
                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold resize-none">{{ old('store_address', $settings['store_address']) }}</textarea>
            </div>
        </div>

        {{-- ── Media Sosial ─────────────────────────────────────────────── --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center flex-shrink-0">
                    <i class="fa-brands fa-instagram text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-brand-dark">Media Sosial</h3>
                    <p class="text-xs text-gray-400 font-medium mt-0.5">Link ke akun media sosial toko, tampil di footer.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Instagram</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2">
                            <i class="fa-brands fa-instagram text-base text-pink-400"></i>
                        </span>
                        <input type="text" name="store_instagram"
                            value="{{ old('store_instagram', $settings['store_instagram']) }}"
                            placeholder="https://instagram.com/nama_toko"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">TikTok</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2">
                            <i class="fa-brands fa-tiktok text-base text-gray-700"></i>
                        </span>
                        <input type="text" name="store_tiktok"
                            value="{{ old('store_tiktok', $settings['store_tiktok']) }}"
                            placeholder="https://tiktok.com/@nama_toko"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tombol Simpan ────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between rounded-[24px] bg-white p-5 shadow-sm border border-gray-50">
            <p class="text-xs text-gray-400 font-medium">Perubahan langsung diterapkan ke seluruh halaman toko.</p>
            <button type="submit"
                class="inline-flex items-center gap-2 px-8 py-3 bg-brand-primary text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all active:scale-95">
                <i class="fa-solid fa-floppy-disk"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    // Live logo preview
    $('#logoInput').on('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#logoPlaceholder').addClass('hidden');
            $('#logoPreview').attr('src', e.target.result).removeClass('hidden');
        };
        reader.readAsDataURL(file);
    });

    // Click the preview area to open file picker
    $('#logoPreviewWrap').on('click', function () {
        $('#logoInput').trigger('click');
    });
});
</script>
@endpush
