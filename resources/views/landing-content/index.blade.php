@extends('layouts.app')

@section('title', 'Konten Landing Page')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Konten Landing Page</h1>
                <p class="text-xs md:text-sm text-gray-400 font-medium mt-1">Atur banner slider yang tampil di halaman utama FURE.</p>
            </div>
            <a href="/" target="_blank"
                class="inline-flex w-fit items-center gap-2 rounded-2xl bg-brand-dark px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-brand-primary">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                Preview
            </a>
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
            {{-- Form Tambah Banner --}}
            <form action="{{ route('landing-content.banners.store') }}" method="POST" enctype="multipart/form-data"
                class="rounded-[32px] border border-gray-50 bg-white p-6 shadow-sm md:p-8">
                @csrf
                <div class="mb-6 flex items-center gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-primary text-white">
                        <i class="fa-solid fa-panorama"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold text-brand-dark">Tambah Banner Slider</h2>
                        <p class="text-xs font-medium text-gray-400">Bisa dibuat lebih dari satu dan akan otomatis menjadi slider.</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Label kecil</label>
                        <input name="eyebrow" placeholder="New Hijab Collection"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-brand-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Urutan</label>
                        <input type="number" name="sort_order" value="0" min="0"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-brand-primary">
                    </div>
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Judul <span class="font-bold normal-case tracking-normal text-gray-300">(opsional untuk banner foto saja)</span></label>
                        <input name="title" placeholder="FURE"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-brand-primary">
                    </div>
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Deskripsi</label>
                        <textarea name="subtitle" rows="3"
                            class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-brand-primary"></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Foto Banner Desktop</label>
                        <input type="file" name="image" accept="image/*"
                            class="block w-full cursor-pointer rounded-2xl border border-gray-200 bg-gray-50/50 p-1 text-xs text-gray-400 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-primary file:px-5 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:text-white hover:file:bg-brand-dark">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Foto Banner Mobile</label>
                        <input type="file" name="mobile_image" accept="image/*"
                            class="block w-full cursor-pointer rounded-2xl border border-gray-200 bg-gray-50/50 p-1 text-xs text-gray-400 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-primary file:px-5 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:text-white hover:file:bg-brand-dark">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Tombol utama</label>
                        <input name="primary_button_text" placeholder="Belanja Sekarang"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-brand-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Link utama</label>
                        <input name="primary_button_url" placeholder="/collections"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-brand-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Tombol kedua</label>
                        <input name="secondary_button_text" placeholder="Semua Koleksi"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-brand-primary">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Link kedua</label>
                        <input name="secondary_button_url" placeholder="/collections"
                            class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none transition focus:border-brand-primary">
                    </div>
                    <label class="flex items-center gap-3 px-1">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-brand-primary">
                        <span class="text-sm font-bold text-gray-600">Aktif</span>
                    </label>
                </div>

                <button class="mt-6 rounded-2xl bg-brand-primary px-6 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-brand-dark">
                    Simpan Banner
                </button>
            </form>

            {{-- List Banner --}}
            <div class="rounded-[32px] border border-gray-50 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-extrabold text-brand-dark">Banner Slider</h2>
                <div class="space-y-4">
                    @forelse($banners as $banner)
                        <div class="flex gap-4 rounded-3xl border border-gray-100 p-4">
                            <div class="h-20 w-28 flex-shrink-0 overflow-hidden rounded-2xl bg-gray-100">
                                @if($banner->mobile_image || $banner->image)
                                    <img src="{{ asset('storage/' . ($banner->image ?: $banner->mobile_image)) }}" class="h-full w-full object-cover" alt="{{ $banner->title }}">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-black uppercase tracking-widest text-brand-primary">{{ $banner->eyebrow ?: 'Banner' }}</p>
                                        <h3 class="truncate text-sm font-extrabold text-brand-dark">{{ $banner->title ?: 'Banner foto saja' }}</h3>
                                        @if($banner->mobile_image)
                                            <p class="mt-0.5 text-[10px] font-bold uppercase tracking-widest text-green-600">Mobile image aktif</p>
                                        @endif
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase {{ $banner->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                        {{ $banner->is_active ? 'Aktif' : 'Off' }}
                                    </span>
                                </div>
                                <p class="mt-1 line-clamp-2 text-xs text-gray-400">{{ $banner->subtitle }}</p>
                                <div class="mt-3 flex gap-2">
                                    <button type="button"
                                        data-banner="{{ htmlspecialchars(json_encode($banner), ENT_QUOTES, 'UTF-8') }}"
                                        onclick="editBanner(this)"
                                        class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-600">Edit</button>
                                    <form action="{{ route('landing-content.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-600">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-3xl border border-dashed border-gray-200 p-8 text-center text-sm text-gray-400">Belum ada banner. Landing akan memakai banner default.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Banner Modal --}}
    <div id="editModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
        <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[32px] bg-white p-6 shadow-2xl md:p-8">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-extrabold text-brand-dark">Edit Banner Slider</h2>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-red-500">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Label kecil</label>
                    <input name="eyebrow" id="editEyebrow" class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>
                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Urutan</label>
                    <input type="number" min="0" name="sort_order" id="editSort" class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>
                <div class="space-y-1.5 md:col-span-2">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Judul</label>
                    <input name="title" id="editMainTitle" class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>
                <div class="space-y-1.5 md:col-span-2">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Deskripsi</label>
                    <textarea name="subtitle" id="editSubtitle" rows="3" class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Tombol utama</label>
                    <input name="primary_button_text" id="editPrimaryText" class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>
                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Link utama</label>
                    <input name="primary_button_url" id="editPrimaryUrl" class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>
                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Tombol kedua</label>
                    <input name="secondary_button_text" id="editSecondaryText" class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>
                <div class="space-y-1.5">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Link kedua</label>
                    <input name="secondary_button_url" id="editSecondaryUrl" class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>
                <div class="space-y-1.5 md:col-span-2">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Ganti Foto Desktop</label>
                    <input type="file" name="image" accept="image/*" class="block w-full cursor-pointer rounded-2xl border border-gray-200 bg-gray-50/50 p-1 text-xs text-gray-400 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-primary file:px-5 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:text-white hover:file:bg-brand-dark">
                </div>
                <div class="space-y-1.5 md:col-span-2">
                    <label class="ml-1 text-[10px] font-black uppercase tracking-widest text-gray-400">Ganti Foto Mobile</label>
                    <input type="file" name="mobile_image" accept="image/*" class="block w-full cursor-pointer rounded-2xl border border-gray-200 bg-gray-50/50 p-1 text-xs text-gray-400 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-primary file:px-5 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:text-white hover:file:bg-brand-dark">
                </div>
                <label class="flex items-center gap-3 px-1">
                    <input type="checkbox" name="is_active" id="editActive" value="1" class="h-4 w-4 rounded border-gray-300 text-brand-primary">
                    <span class="text-sm font-bold text-gray-600">Aktif</span>
                </label>
                <div class="md:col-span-2 flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeEditModal()" class="rounded-2xl px-6 py-3 text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-gray-100">Batal</button>
                    <button class="rounded-2xl bg-brand-primary px-8 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-brand-dark">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const editModal = document.getElementById('editModal');
        const editForm  = document.getElementById('editForm');

        function closeEditModal() {
            editModal.classList.add('hidden');
            editModal.classList.remove('flex');
        }

        function editBanner(btn) {
            const data = JSON.parse(btn.getAttribute('data-banner'));

            editForm.action = '/landing-content/banners/' + data.id;

            document.getElementById('editEyebrow').value      = data.eyebrow || '';
            document.getElementById('editSort').value         = data.sort_order || 0;
            document.getElementById('editMainTitle').value    = data.title || '';
            document.getElementById('editSubtitle').value     = data.subtitle || '';
            document.getElementById('editPrimaryText').value  = data.primary_button_text || '';
            document.getElementById('editPrimaryUrl').value   = data.primary_button_url || '';
            document.getElementById('editSecondaryText').value = data.secondary_button_text || '';
            document.getElementById('editSecondaryUrl').value = data.secondary_button_url || '';
            document.getElementById('editActive').checked     = Boolean(data.is_active);

            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
        }

        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) closeEditModal();
        });
    </script>
@endpush
