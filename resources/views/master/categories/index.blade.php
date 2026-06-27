@extends('layouts.app')

@section('title', 'Kategori Produk')

@section('content')
    <div class="mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Kategori Produk</h1>
                <nav class="text-xs md:text-sm text-gray-400 font-medium mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-brand-primary transition-colors">Dashboard</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                        <li class="text-brand-dark">Kategori Produk</li>
                    </ol>
                </nav>
            </div>

            <button onclick="openCreateModal()"
                class="px-5 py-3 bg-brand-primary text-white rounded-2xl font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                <span class="hidden sm:inline">Tambah Kategori</span>
            </button>
        </div>

        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden px-6 py-8">
            <table id="datatable" class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 text-[11px] uppercase tracking-widest border-b border-gray-50">
                        <th class="px-4 py-4 text-left">No</th>
                        <th class="px-4 py-4 text-left">Gambar</th>
                        <th class="px-4 py-4 text-left">Kategori</th>
                        <th class="px-4 py-4 text-left">Status</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $no = 1; @endphp
                    @foreach($categories as $cat)
                        <tr class="hover:bg-soft-bg/50 transition-colors">
                            <td class="px-4 py-5 font-bold text-gray-400">{{ $no++ }}</td>
                            <td class="px-4 py-5">
                                @if($cat->image)
                                    <img src="{{ asset('storage/' . $cat->image) }}"
                                        class="w-12 h-12 rounded-2xl object-cover border border-gray-100 shadow-sm">
                                @else
                                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 border border-dashed border-gray-200">
                                        <i class="fa-solid fa-layer-group text-xs"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-5">
                                <div class="font-bold text-brand-dark">{{ $cat->name }}</div>
                                @if($cat->children->count())
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach($cat->children as $child)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-brand-primary/8 text-[10px] font-semibold text-brand-primary">
                                                <i class="fa-solid fa-turn-down-right text-[8px]"></i>{{ $child->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-5">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $cat->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='openEditModal(@json($cat))'
                                        class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <button onclick="deleteCategory({{ $cat->id }})"
                                        class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-600 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @foreach($cat->children as $child)
                        <tr class="bg-gray-50/60 hover:bg-soft-bg/40 transition-colors">
                            <td class="px-4 py-3 text-gray-300 font-bold">{{ $no++ }}</td>
                            <td class="px-4 py-3">
                                @if($child->image)
                                    <img src="{{ asset('storage/' . $child->image) }}"
                                        class="w-9 h-9 rounded-xl object-cover border border-gray-100">
                                @else
                                    <div class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center text-gray-300 border border-dashed border-gray-200">
                                        <i class="fa-solid fa-layer-group text-[10px]"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 text-sm font-semibold text-brand-dark/70">
                                    <i class="fa-solid fa-turn-down-right text-[10px] text-brand-primary/40 ml-2"></i>
                                    {{ $child->name }}
                                    <span class="text-[10px] font-normal text-gray-400">(sub)</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $child->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                    {{ $child->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='openEditModal(@json($child))'
                                        class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <button onclick="deleteCategory({{ $child->id }})"
                                        class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-600 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="categoryModal"
        class="fixed inset-0 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center z-[100] p-4">
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all border border-white/20">
            
            <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/30 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-brand-primary shadow-lg shadow-brand-primary/20 flex items-center justify-center text-white">
                        <i class="fa-solid fa-layer-group text-lg"></i>
                    </div>
                    <div>
                        <h2 id="modalTitle" class="text-lg font-extrabold text-brand-dark leading-tight">Tambah Kategori</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.15em]">Catalog Management</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:bg-white hover:text-red-500 transition-all shadow-sm">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="categoryForm" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div class="grid grid-cols-1 gap-5">
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Kategori</label>
                        <div class="relative group">
                            <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-primary transition-colors text-xs"></i>
                            <input type="text" name="name" id="catName" required placeholder="Contoh: Pashmina"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Sub Kategori dari</label>
                        <select name="parent_id" id="catParent"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            <option value="">— Tidak ada (Kategori Utama) —</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <p class="ml-1 text-[10px] text-gray-400">Kosongkan jika ini adalah kategori utama.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Gambar Kategori</label>
                        <div class="flex items-center p-1 bg-gray-50/50 border border-gray-200 rounded-2xl">
                            <input type="file" name="image" id="catImage" accept="image/*"
                                class="block w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-brand-primary file:text-white hover:file:bg-brand-dark transition-all cursor-pointer">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Deskripsi</label>
                        <textarea name="description" id="catDescription" rows="3"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary outline-none transition-all text-sm font-semibold resize-none"></textarea>
                    </div>

                    <div class="flex items-center gap-3 px-1">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="catStatus" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-primary"></div>
                            <span class="ml-3 text-sm font-bold text-gray-600">Aktifkan Kategori</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal()"
                        class="px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest text-gray-400 hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-10 py-3 rounded-2xl bg-brand-primary text-white text-xs font-black uppercase tracking-[0.1em] shadow-xl shadow-brand-primary/20 hover:bg-brand-dark transition-all flex items-center gap-2">
                        <span id="btnText">Simpan</span>
                        <i id="loader" class="fa-solid fa-circle-notch animate-spin hidden"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#datatable').DataTable({
                    responsive: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Cari kategori...",
                    }
                });

                const modal = $('#categoryModal');
                const form = $('#categoryForm');

                window.openCreateModal = function () {
                    modal.removeClass('hidden').addClass('flex');
                    $('#modalTitle').text('Tambah Kategori');
                    form.attr('action', '/categories');
                    $('#methodField').val('POST');
                    form[0].reset();
                    $('#catParent').val('');
                }

                window.openEditModal = function (data) {
                    modal.removeClass('hidden').addClass('flex');
                    $('#modalTitle').text('Edit Kategori');
                    form.attr('action', `/categories/${data.id}`);
                    $('#methodField').val('PUT');

                    $('#catName').val(data.name);
                    $('#catParent').val(data.parent_id ?? '');
                    $('#catDescription').val(data.description);
                    $('#catStatus').prop('checked', data.is_active == 1);
                }

                window.closeModal = function () {
                    modal.addClass('hidden').removeClass('flex');
                }

                modal.on('click', function(e) { if (e.target === this) closeModal(); });

                form.on('submit', function () {
                    $('#submitBtn').prop('disabled', true).addClass('opacity-70');
                    $('#btnText').text('Memproses...');
                    $('#loader').removeClass('hidden');
                });

                window.deleteCategory = function (id) {
                    Swal.fire({
                        title: 'Hapus Kategori?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        borderRadius: '2rem'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const delForm = $('<form>', {
                                method: 'POST',
                                action: `/categories/${id}`
                            }).append($('<input>', {
                                type: 'hidden',
                                name: '_token',
                                value: '{{ csrf_token() }}'
                            })).append($('<input>', {
                                type: 'hidden',
                                name: '_method',
                                value: 'DELETE'
                            }));
                            $('body').append(delForm);
                            delForm.submit();
                        }
                    })
                }
            });
        </script>
    @endpush
@endsection
