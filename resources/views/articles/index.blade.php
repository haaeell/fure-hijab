@extends('layouts.app')
@section('title', 'Kelola Artikel')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
<style>
    /* ── Summernote ──────────────────────────────────────────── */
    .note-editor.note-frame                  { border-radius: 16px; border-color: #e5e7eb; overflow: hidden; }
    .note-editor.note-frame .note-toolbar    { background: #f9fafb; border-bottom: 1px solid #f3f4f6; padding: 6px 8px; }
    .note-editor.note-frame .note-statusbar  { background: #f9fafb; border-top: 1px solid #f3f4f6; }
    .note-editor.note-frame.focus            { border-color: #A78B6F; box-shadow: 0 0 0 3px rgba(167,139,111,.15); }
    .note-btn                                { border-radius: 8px !important; font-size: 12px; }
    .note-editable                           { font-size: 14px; line-height: 1.8; font-family: inherit; }
    .note-editable p                         { margin-bottom: 0.75rem; }
    .note-editable img                       { max-width: 100%; border-radius: 12px; }

    /* ── DataTables custom ───────────────────────────────────── */
    #articlesTable_wrapper .dataTables_length select,
    #articlesTable_wrapper .dataTables_filter input {
        border: 1px solid #e5e7eb; border-radius: 10px;
        padding: 6px 12px; font-size: 12px; outline: none;
        background: #f9fafb;
    }
    #articlesTable_wrapper .dataTables_filter input:focus { border-color: #A78B6F; }
    #articlesTable_wrapper .dataTables_info,
    #articlesTable_wrapper .dataTables_length { font-size: 11px; color: #9ca3af; }
    #articlesTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important; font-size: 12px; font-weight: 700;
        padding: 4px 10px !important; border: none !important;
    }
    #articlesTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #A78B6F !important; color: #fff !important;
    }
    #articlesTable_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
        background: #f3f4f6 !important; color: #374151 !important;
    }
    #articlesTable thead th { cursor: pointer; user-select: none; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-7xl">

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-brand-dark tracking-tight">Artikel & Journal</h1>
            <p class="mt-0.5 text-xs text-gray-400">{{ $articles->count() }} total artikel dipublikasikan / draft</p>
        </div>
        <button type="button" onclick="openArticleModal()"
            class="flex items-center gap-2 bg-brand-primary px-5 py-2.5 rounded-2xl text-xs font-bold uppercase tracking-widest text-white shadow-sm transition hover:bg-brand-dark">
            <i class="fa-solid fa-plus"></i> Artikel Baru
        </button>
    </div>

    {{-- Table card --}}
    <div class="overflow-hidden rounded-[20px] border border-gray-100 bg-white shadow-sm">
        <table id="articlesTable" class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60 text-left text-[10px] font-bold uppercase tracking-widest text-gray-400">
                    <th class="px-5 py-4">Artikel</th>
                    <th class="px-5 py-4">Kategori</th>
                    <th class="px-5 py-4 text-center">Views</th>
                    <th class="px-5 py-4">Tanggal</th>
                    <th class="px-5 py-4 text-center">Status</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($articles as $article)
                    <tr class="hover:bg-gray-50/50 transition-colors">

                        {{-- Artikel --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-16 flex-shrink-0 overflow-hidden rounded-xl bg-[#eee5dc]">
                                    @if($article->thumbnail)
                                        <img src="{{ asset('storage/' . $article->thumbnail) }}"
                                             class="h-full w-full object-cover" alt="">
                                    @else
                                        <div class="flex h-full items-center justify-center">
                                            <i class="fa-regular fa-newspaper text-brand-secondary/50 text-xs"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-brand-dark text-sm line-clamp-1">{{ $article->title }}</p>
                                    <p class="mt-0.5 text-[10px] text-gray-400 line-clamp-1">{{ $article->excerpt }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Kategori --}}
                        <td class="px-5 py-4">
                            <span class="rounded-lg bg-brand-primary/10 px-2.5 py-1 text-[10px] font-bold text-brand-primary whitespace-nowrap">
                                {{ $article->category_label }}
                            </span>
                        </td>

                        {{-- Views --}}
                        <td class="px-5 py-4 text-center text-xs text-gray-400">
                            {{ number_format($article->view_count) }}
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-5 py-4 text-xs text-gray-400 whitespace-nowrap">
                            {{ $article->published_at ? $article->published_at->format('d M Y') : '—' }}
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4 text-center">
                            <button type="button"
                                class="toggle-publish inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-bold transition-all"
                                data-id="{{ $article->id }}"
                                data-published="{{ $article->is_published ? '1' : '0' }}">
                            </button>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('articles.show', $article->slug) }}" target="_blank"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-100 text-gray-400 transition hover:bg-blue-50 hover:text-blue-500"
                                    title="Lihat artikel">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                                <button type="button" onclick="openArticleModal({{ $article->id }})"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-100 text-gray-400 transition hover:bg-brand-primary/10 hover:text-brand-primary"
                                    title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <button type="button" onclick="confirmDelete({{ $article->id }}, '{{ addslashes($article->title) }}')"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-gray-100 text-gray-400 transition hover:bg-red-50 hover:text-red-500"
                                    title="Hapus">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                                <form id="delete-form-{{ $article->id }}"
                                      action="{{ route('admin.articles.destroy', $article) }}"
                                      method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-sm text-gray-400">
                            Belum ada artikel.
                            <button type="button" onclick="openArticleModal()"
                                class="text-brand-primary font-bold hover:underline">Buat yang pertama</button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Modal Create / Edit ──────────────────────────────────────────────── --}}
<div id="articleModal" class="fixed inset-0 z-[200] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeArticleModal()"></div>
    <div class="relative z-10 w-full max-w-3xl max-h-[92vh] overflow-y-auto rounded-[24px] bg-white shadow-2xl">

        {{-- Modal header --}}
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4 rounded-t-[24px]">
            <h3 id="modalTitle" class="font-extrabold text-brand-dark">Artikel Baru</h3>
            <button type="button" onclick="closeArticleModal()"
                class="flex h-9 w-9 items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="articleForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="grid gap-5 sm:grid-cols-2">

                {{-- Judul --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Judul Artikel *</label>
                    <input type="text" name="title" id="f_title" required
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition">
                </div>

                {{-- Excerpt --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ringkasan / Excerpt</label>
                    <textarea name="excerpt" id="f_excerpt" rows="2"
                        class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition"
                        placeholder="Deskripsi singkat artikel (maks. 500 karakter)"></textarea>
                </div>

                {{-- Kategori --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kategori *</label>
                    <select name="category" id="f_category"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Penulis --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Penulis</label>
                    <input type="text" name="author" id="f_author" value="Tim {{ $adminStoreName }}"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition">
                </div>

                {{-- Estimasi baca --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Estimasi Baca (menit)</label>
                    <input type="number" name="read_time" id="f_read_time" value="3" min="1" max="60"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition">
                </div>

                {{-- Tags --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tags <span class="normal-case font-medium">(pisah koma)</span></label>
                    <input type="text" name="tags" id="f_tags" placeholder="hijab, styling, tips"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition">
                </div>

                {{-- Thumbnail --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Thumbnail</label>
                    <div class="flex items-center gap-4">
                        <input type="file" name="thumbnail" id="f_thumbnail" accept="image/*"
                            class="block flex-1 cursor-pointer rounded-2xl border border-gray-200 bg-gray-50/50 p-1 text-xs text-gray-400
                                   file:mr-4 file:rounded-xl file:border-0 file:bg-brand-primary file:px-4 file:py-2 file:text-[10px] file:font-black file:uppercase file:text-white hover:file:bg-brand-dark transition">
                        <img id="thumbnailPreview" class="hidden h-16 w-24 rounded-xl object-cover flex-shrink-0" alt="">
                    </div>
                </div>

                {{-- Konten (Summernote) --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Konten Artikel</label>
                    <textarea name="content" id="f_content"></textarea>
                </div>

                {{-- SEO divider --}}
                <div class="sm:col-span-2 flex items-center gap-3 pt-2">
                    <div class="flex-1 h-px bg-gray-100"></div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">SEO</span>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                {{-- Meta title --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Meta Title</label>
                    <input type="text" name="meta_title" id="f_meta_title"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition"
                        placeholder="Kosong → otomatis pakai judul artikel">
                </div>

                {{-- Meta description --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Meta Description</label>
                    <textarea name="meta_description" id="f_meta_description" rows="2"
                        class="w-full resize-none rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition"
                        placeholder="Maks. 160 karakter"></textarea>
                </div>

                {{-- Meta keywords --}}
                <div class="sm:col-span-2 space-y-1.5">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Meta Keywords</label>
                    <input type="text" name="meta_keywords" id="f_meta_keywords"
                        class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm font-semibold outline-none focus:border-brand-primary transition"
                        placeholder="kata kunci, dipisah koma">
                </div>

                {{-- Publish --}}
                <div class="sm:col-span-2 flex items-center gap-3">
                    <input type="checkbox" name="is_published" id="f_is_published" value="1"
                        class="h-5 w-5 rounded border-gray-300 text-brand-primary focus:ring-brand-primary cursor-pointer">
                    <label for="f_is_published" class="text-sm font-semibold text-brand-dark cursor-pointer select-none">
                        Publish sekarang
                    </label>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" onclick="closeArticleModal()"
                    class="rounded-2xl border border-gray-200 px-6 py-2.5 text-xs font-bold text-gray-500 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit"
                    class="rounded-2xl bg-brand-primary px-8 py-2.5 text-xs font-bold uppercase tracking-widest text-white shadow hover:bg-brand-dark transition">
                    Simpan Artikel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(function () {

    // ── DataTables ────────────────────────────────────────────────────────────
    $('#articlesTable').DataTable({
        language: {
            search:           '',
            searchPlaceholder: 'Cari artikel...',
            lengthMenu:       'Tampilkan _MENU_ data',
            info:             'Menampilkan _START_–_END_ dari _TOTAL_ artikel',
            infoEmpty:        'Tidak ada data',
            infoFiltered:     '(difilter dari _MAX_ total)',
            zeroRecords:      'Artikel tidak ditemukan',
            paginate: { previous: '‹', next: '›' },
        },
        pageLength: 10,
        order:      [[3, 'desc']], // urutkan berdasarkan tanggal
        columnDefs: [
            { targets: [0], orderable: false },   // kolom Artikel tidak sortable
            { targets: [4, 5], orderable: false }, // Status & Aksi tidak sortable
        ],
        drawCallback: function () {
            // Re-render toggle setelah DataTables redraw (search/paging)
            document.querySelectorAll('.toggle-publish').forEach(renderToggle);
        },
    });

    // ── Toggle publish ────────────────────────────────────────────────────────
    const articlesData = @json($articles->keyBy('id'));

    function renderToggle(btn) {
        const pub = btn.dataset.published === '1';
        btn.innerHTML = pub
            ? '<i class="fa-solid fa-circle-check text-xs"></i> Publish'
            : '<i class="fa-solid fa-circle-pause text-xs"></i> Draft';
        btn.className = 'toggle-publish inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-bold transition-all '
            + (pub ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-gray-100 text-gray-400 hover:bg-gray-200');
    }

    // Event delegation karena DataTables re-render baris
    $(document).on('click', '.toggle-publish', function () {
        const btn = this;
        const id  = btn.dataset.id;
        fetch('/admin/articles/' + id + '/toggle-publish', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        })
        .then(r => r.json())
        .then(data => {
            btn.dataset.published = data.is_published ? '1' : '0';
            renderToggle(btn);
            // Update articlesData supaya konsisten saat modal edit dibuka
            if (articlesData[id]) articlesData[id].is_published = data.is_published;
        });
    });

    // Initial render
    document.querySelectorAll('.toggle-publish').forEach(renderToggle);

    // ── Summernote ────────────────────────────────────────────────────────────
    const uploadImageUrl = "{{ route('admin.articles.upload-image') }}";
    const csrf           = $('meta[name="csrf-token"]').attr('content');

    function initSummernote(content) {
        if ($('#f_content').next('.note-editor').length) {
            $('#f_content').summernote('destroy');
        }
        $('#f_content').summernote({
            height: 380,
            tabsize: 2,
            toolbar: [
                ['style',  ['style']],
                ['font',   ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['color',  ['color']],
                ['para',   ['ul', 'ol', 'paragraph']],
                ['table',  ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view',   ['fullscreen', 'codeview']],
            ],
            styleTags: ['p', 'h2', 'h3', 'h4', 'blockquote'],
            callbacks: {
                onImageUpload: function (files) {
                    const formData = new FormData();
                    formData.append('image', files[0]);
                    formData.append('_token', csrf);
                    $.ajax({
                        url: uploadImageUrl, method: 'POST',
                        data: formData, contentType: false, processData: false,
                        success: function (res) {
                            $('#f_content').summernote('insertImage', res.url, files[0].name);
                        },
                        error: function () {
                            Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 })
                                .fire({ icon: 'error', title: 'Gagal upload gambar' });
                        },
                    });
                },
            },
        });
        $('#f_content').summernote('code', content || '');
    }

    function destroySummernote() {
        if ($('#f_content').next('.note-editor').length) {
            $('#f_content').summernote('destroy');
        }
    }

    // ── Modal ─────────────────────────────────────────────────────────────────
    window.openArticleModal = function (id) {
        const form = document.getElementById('articleForm');
        form.reset();
        document.getElementById('thumbnailPreview').classList.add('hidden');

        let content = '';

        if (id && articlesData[id]) {
            const a = articlesData[id];
            document.getElementById('modalTitle').textContent      = 'Edit Artikel';
            form.action                                            = '/admin/articles/' + id;
            document.getElementById('formMethod').value           = 'PUT';
            document.getElementById('f_title').value              = a.title || '';
            document.getElementById('f_excerpt').value            = a.excerpt || '';
            document.getElementById('f_category').value           = a.category || 'tips';
            document.getElementById('f_author').value             = a.author || 'Tim {{ $adminStoreName }}';
            document.getElementById('f_read_time').value          = a.read_time || 3;
            document.getElementById('f_tags').value               = Array.isArray(a.tags) ? a.tags.join(', ') : '';
            document.getElementById('f_meta_title').value         = a.meta_title || '';
            document.getElementById('f_meta_description').value   = a.meta_description || '';
            document.getElementById('f_meta_keywords').value      = a.meta_keywords || '';
            document.getElementById('f_is_published').checked     = !!a.is_published;
            content = a.content || '';

            if (a.thumbnail) {
                const img = document.getElementById('thumbnailPreview');
                img.src = '/storage/' + a.thumbnail;
                img.classList.remove('hidden');
            }
        } else {
            document.getElementById('modalTitle').textContent = 'Artikel Baru';
            form.action                                       = '/admin/articles';
            document.getElementById('formMethod').value      = 'POST';
        }

        const modal = document.getElementById('articleModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        setTimeout(function () { initSummernote(content); }, 50);
    };

    window.closeArticleModal = function () {
        destroySummernote();
        const modal = document.getElementById('articleModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    window.confirmDelete = function (id, title) {
        Swal.fire({
            icon: 'warning',
            title: 'Hapus Artikel?',
            html: '<span class="text-sm text-gray-500">Artikel <b>"' + title + '"</b> akan dihapus permanen.</span>',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#e5e7eb',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: { cancelButton: 'text-gray-700' },
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    };

    // ── Thumbnail preview ─────────────────────────────────────────────────────
    document.getElementById('f_thumbnail').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const img = document.getElementById('thumbnailPreview');
        img.src = URL.createObjectURL(file);
        img.classList.remove('hidden');
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeArticleModal();
    });

});
</script>
@endpush
