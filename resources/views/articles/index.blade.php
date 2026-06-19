@extends('layouts.app')
@section('title', 'Kelola Artikel')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-brand-dark">Artikel & Journal</h1>
            <p class="mt-0.5 text-xs text-gray-400">{{ $articles->count() }} total artikel</p>
        </div>
        <button type="button" onclick="openArticleModal()"
            class="flex items-center gap-2 bg-brand-primary px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-white shadow-sm transition hover:bg-brand-dark">
            <i class="fa-solid fa-plus"></i> Artikel Baru
        </button>
    </div>

    <div class="overflow-hidden rounded-[20px] border border-gray-100 bg-white shadow-sm">
        <table id="articlesTable" class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">
                    <th class="px-5 py-4">Artikel</th>
                    <th class="px-5 py-4 hidden md:table-cell">Kategori</th>
                    <th class="px-5 py-4 hidden lg:table-cell">Views</th>
                    <th class="px-5 py-4 hidden lg:table-cell">Tanggal</th>
                    <th class="px-5 py-4 text-center">Status</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($articles as $article)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-14 w-20 flex-shrink-0 overflow-hidden rounded-xl bg-[#eee5dc]">
                                    @if($article->thumbnail)
                                        <img src="{{ asset('storage/' . $article->thumbnail) }}" class="h-full w-full object-cover" alt="">
                                    @else
                                        <div class="flex h-full items-center justify-center">
                                            <i class="fa-regular fa-newspaper text-brand-secondary/60"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-brand-dark line-clamp-1">{{ $article->title }}</p>
                                    <p class="mt-0.5 text-[10px] text-gray-400 line-clamp-1">{{ $article->excerpt }}</p>
                                    <p class="mt-1 text-[10px] text-gray-300">/artikel/{{ $article->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="rounded-lg bg-brand-primary/10 px-2.5 py-1 text-[10px] font-bold text-brand-primary">
                                {{ $article->category_label }}
                            </span>
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell text-xs text-gray-400">
                            {{ number_format($article->view_count) }}
                        </td>
                        <td class="px-5 py-4 hidden lg:table-cell text-xs text-gray-400">
                            {{ $article->published_at ? $article->published_at->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button type="button"
                                class="toggle-publish inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-bold transition-all"
                                data-id="{{ $article->id }}"
                                data-published="{{ $article->is_published ? '1' : '0' }}">
                            </button>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-400 transition hover:bg-blue-50 hover:text-blue-500">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                                <button type="button" onclick="openArticleModal({{ $article->id }})"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-400 transition hover:bg-brand-primary/10 hover:text-brand-primary">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                                    onsubmit="return confirm('Hapus artikel ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-400 transition hover:bg-red-50 hover:text-red-500">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-sm text-gray-400">
                            Belum ada artikel. <button type="button" onclick="openArticleModal()" class="text-brand-primary font-bold hover:underline">Buat artikel pertama</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Create/Edit --}}
<div id="articleModal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeArticleModal()"></div>
    <div class="relative z-10 w-full max-w-3xl max-h-[92vh] overflow-y-auto rounded-[24px] bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h3 id="modalTitle" class="font-extrabold text-brand-dark">Artikel Baru</h3>
            <button type="button" onclick="closeArticleModal()" class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-gray-100 text-gray-400">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="articleForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Judul Artikel *</label>
                    <input type="text" name="title" id="f_title" required
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ringkasan / Excerpt</label>
                    <textarea name="excerpt" id="f_excerpt" rows="2"
                        class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary"
                        placeholder="Deskripsi singkat artikel (maks. 500 karakter)"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori *</label>
                    <select name="category" id="f_category"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penulis</label>
                    <input type="text" name="author" id="f_author" value="Tim FURE"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Estimasi Baca (menit)</label>
                    <input type="number" name="read_time" id="f_read_time" value="3" min="1" max="60"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tags (pisah koma)</label>
                    <input type="text" name="tags" id="f_tags" placeholder="hijab, styling, tips"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary">
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Thumbnail</label>
                    <input type="file" name="thumbnail" id="f_thumbnail" accept="image/*"
                        class="block w-full cursor-pointer rounded-2xl border border-gray-200 bg-gray-50/50 p-1 text-xs text-gray-400
                               file:mr-4 file:rounded-xl file:border-0 file:bg-brand-primary file:px-5 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:text-white hover:file:bg-brand-dark">
                    <img id="thumbnailPreview" class="mt-2 hidden h-24 rounded-xl object-cover" alt="">
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Konten Artikel</label>
                    <textarea name="content" id="f_content" rows="14"
                        class="w-full resize-y rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm outline-none focus:border-brand-primary font-mono"></textarea>
                </div>

                <p class="sm:col-span-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-t border-gray-100 pt-4">SEO</p>

                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Meta Title</label>
                    <input type="text" name="meta_title" id="f_meta_title"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary"
                        placeholder="Dikosongkan → pakai judul artikel">
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Meta Description</label>
                    <textarea name="meta_description" id="f_meta_description" rows="2"
                        class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary"
                        placeholder="Maks. 160 karakter"></textarea>
                </div>

                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="f_meta_keywords"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary"
                        placeholder="kata kunci, dipisah koma">
                </div>

                <div class="sm:col-span-2 flex items-center gap-3">
                    <input type="checkbox" name="is_published" id="f_is_published" value="1"
                        class="h-5 w-5 rounded border-gray-300 text-brand-primary">
                    <label for="f_is_published" class="text-sm font-semibold text-brand-dark cursor-pointer">
                        Publish sekarang
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                <button type="button" onclick="closeArticleModal()"
                    class="rounded-2xl border border-gray-200 px-6 py-2.5 text-xs font-bold text-gray-500 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="rounded-2xl bg-brand-primary px-8 py-2.5 text-xs font-bold uppercase tracking-widest text-white shadow hover:bg-brand-dark transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const articlesData = @json($articles->keyBy('id'));

function renderToggle(btn) {
    const pub = btn.dataset.published === '1';
    btn.innerHTML = pub
        ? '<i class="fa-solid fa-circle-check text-xs"></i> Publish'
        : '<i class="fa-solid fa-circle-pause text-xs"></i> Draft';
    btn.className = 'toggle-publish inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-bold transition-all '
        + (pub ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-gray-100 text-gray-400 hover:bg-gray-200');
}

document.querySelectorAll('.toggle-publish').forEach(function(btn) {
    renderToggle(btn);
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('/admin/articles/' + id + '/toggle-publish', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(data => {
            this.dataset.published = data.is_published ? '1' : '0';
            renderToggle(this);
        });
    });
});

function openArticleModal(id) {
    const modal = document.getElementById('articleModal');
    const form  = document.getElementById('articleForm');
    form.reset();
    document.getElementById('thumbnailPreview').classList.add('hidden');

    if (id && articlesData[id]) {
        const a = articlesData[id];
        document.getElementById('modalTitle').textContent = 'Edit Artikel';
        form.action = '/admin/articles/' + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('f_title').value          = a.title || '';
        document.getElementById('f_excerpt').value        = a.excerpt || '';
        document.getElementById('f_category').value       = a.category || 'tips';
        document.getElementById('f_author').value         = a.author || 'Tim FURE';
        document.getElementById('f_read_time').value      = a.read_time || 3;
        document.getElementById('f_tags').value           = Array.isArray(a.tags) ? a.tags.join(', ') : '';
        document.getElementById('f_content').value        = a.content || '';
        document.getElementById('f_meta_title').value     = a.meta_title || '';
        document.getElementById('f_meta_description').value = a.meta_description || '';
        document.getElementById('f_meta_keywords').value  = a.meta_keywords || '';
        document.getElementById('f_is_published').checked = a.is_published;

        if (a.thumbnail) {
            const img = document.getElementById('thumbnailPreview');
            img.src = '/storage/' + a.thumbnail;
            img.classList.remove('hidden');
        }
    } else {
        document.getElementById('modalTitle').textContent = 'Artikel Baru';
        form.action = '/admin/articles';
        document.getElementById('formMethod').value = 'POST';
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeArticleModal() {
    document.getElementById('articleModal').classList.add('hidden').classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

document.getElementById('f_thumbnail').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const img = document.getElementById('thumbnailPreview');
    img.src = URL.createObjectURL(file);
    img.classList.remove('hidden');
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeArticleModal();
});
</script>
@endpush
