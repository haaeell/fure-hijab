@extends('layouts.app')

@section('title', 'Koleksi')

@section('content')
<div class="mx-auto max-w-5xl">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Koleksi Produk</h1>
            <p class="text-xs md:text-sm text-gray-400 font-medium mt-1">Kelola koleksi yang bisa dipilih saat membuat produk (Best Seller, Hijab, dll).</p>
        </div>
        <button onclick="openCreateModal()"
            class="px-5 py-3 bg-brand-primary text-white rounded-2xl font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus text-sm"></i>
            <span class="hidden sm:inline">Tambah Koleksi</span>
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden px-6 py-8">
        <table id="datatable" class="w-full text-sm">
            <thead>
                <tr class="text-gray-400 text-[11px] tracking-widest border-b border-gray-50">
                    <th class="px-4 py-4 text-left">No</th>
                    <th class="px-4 py-4 text-left">Nama Koleksi</th>
                    <th class="px-4 py-4 text-left">Slug</th>
                    <th class="px-4 py-4 text-left">Deskripsi</th>
                    <th class="px-4 py-4 text-center">Produk</th>
                    <th class="px-4 py-4 text-center">Urutan</th>
                    <th class="px-4 py-4 text-center">Status</th>
                    <th class="px-4 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($collections as $i => $col)
                    <tr class="hover:bg-soft-bg/50 transition-colors">
                        <td class="px-4 py-5 font-bold text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-4 py-5">
                            <div class="font-bold text-brand-dark">{{ $col->name }}</div>
                        </td>
                        <td class="px-4 py-5">
                            <code class="text-[11px] bg-gray-50 px-2 py-1 rounded-lg text-brand-primary font-mono">{{ $col->slug }}</code>
                        </td>
                        <td class="px-4 py-5">
                            <div class="text-xs text-gray-400 max-w-[220px] truncate">{{ $col->description ?: '-' }}</div>
                        </td>
                        <td class="px-4 py-5 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-bold bg-brand-primary/10 text-brand-primary">
                                <i class="fa-solid fa-box text-[9px]"></i> {{ $col->products_count }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-center font-semibold text-gray-500">{{ $col->sort_order }}</td>
                        <td class="px-4 py-5 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider {{ $col->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                {{ $col->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal({{ $col->id }}, {{ json_encode($col->name) }}, {{ json_encode($col->description) }}, {{ $col->sort_order }}, {{ $col->is_active ? 'true' : 'false' }})"
                                    class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm" title="Edit">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button onclick="deleteCollection({{ $col->id }}, {{ json_encode($col->name) }})"
                                    class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-600 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── Modal ─────────────────────────────────────────────────────────────── --}}
<div id="collectionModal" class="fixed inset-0 hidden bg-slate-900/50 backdrop-blur-sm items-start justify-center z-[100] p-4 overflow-y-auto">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl my-12">
        <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                    <i class="fa-solid fa-swatchbook"></i>
                </div>
                <h2 id="modalTitle" class="text-lg font-extrabold text-brand-dark">Tambah Koleksi</h2>
            </div>
            <button onclick="closeModal()" class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="collectionForm" method="POST" class="p-8 space-y-5">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">

            <div class="space-y-1.5">
                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">NAMA KOLEKSI <span class="text-red-400">*</span></label>
                <input type="text" name="name" id="inputName" required placeholder="contoh: Best Seller"
                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                <p class="ml-1 text-[10px] text-gray-400">Slug otomatis dibuat dari nama (contoh: "Best Seller" → <code>best-seller</code>).</p>
            </div>

            <div class="space-y-1.5">
                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">DESKRIPSI</label>
                <textarea name="description" id="inputDesc" rows="3" placeholder="Deskripsi singkat koleksi ini..."
                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none transition-all text-sm font-semibold resize-none"></textarea>
            </div>

            <div class="space-y-1.5">
                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">URUTAN TAMPIL</label>
                <input type="number" name="sort_order" id="inputSort" value="0" min="0"
                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none transition-all text-sm font-semibold">
            </div>

            <label class="flex items-center gap-3 p-4 rounded-2xl border border-gray-200 bg-gray-50/50 cursor-pointer hover:border-brand-primary transition-all">
                <input type="checkbox" name="is_active" id="inputActive" value="1"
                    class="h-4 w-4 rounded border-gray-300 text-brand-primary focus:ring-brand-primary" checked>
                <span>
                    <span class="block text-sm font-bold text-brand-dark">Aktif</span>
                    <span class="block text-xs text-gray-400">Koleksi aktif bisa dipilih di produk dan tampil di toko.</span>
                </span>
            </label>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()"
                    class="flex-1 py-3 rounded-2xl border border-gray-200 text-sm font-bold text-gray-500 hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-2xl bg-brand-primary text-white text-sm font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modal    = document.getElementById('collectionModal');
    const form     = document.getElementById('collectionForm');
    const title    = document.getElementById('modalTitle');
    const method   = document.getElementById('methodField');

    function openCreateModal() {
        title.textContent = 'Tambah Koleksi';
        form.action       = "{{ route('koleksi.store') }}";
        method.value      = 'POST';
        document.getElementById('inputName').value  = '';
        document.getElementById('inputDesc').value  = '';
        document.getElementById('inputSort').value  = '0';
        document.getElementById('inputActive').checked = true;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function openEditModal(id, name, desc, sort, active) {
        title.textContent = 'Edit Koleksi';
        form.action       = `/koleksi/${id}`;
        method.value      = 'PUT';
        document.getElementById('inputName').value     = name;
        document.getElementById('inputDesc').value     = desc ?? '';
        document.getElementById('inputSort').value     = sort;
        document.getElementById('inputActive').checked = active;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function deleteCollection(id, name) {
        Swal.fire({
            title: 'Hapus Koleksi?',
            html: `Koleksi <strong>${name}</strong> akan dihapus. Produk yang ada di koleksi ini tidak akan ikut terhapus.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ef4444',
        }).then(result => {
            if (!result.isConfirmed) return;
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = `/koleksi/${id}`;
            f.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(f);
            f.submit();
        });
    }

    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    $(function () {
        $('#datatable').DataTable({
            responsive: true,
            language: { search: 'Cari:', lengthMenu: 'Tampilkan _MENU_ data', info: '_START_–_END_ dari _TOTAL_', paginate: { previous: '‹', next: '›' } },
        });
    });
</script>
@endpush
