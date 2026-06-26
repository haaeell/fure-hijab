@extends('layouts.app')

@section('title', 'Produk')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <style>
        .note-editor.note-frame {
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            overflow: hidden;
            background: #fff;
        }

        .note-editor .note-toolbar {
            background: #f9fafb;
            border-bottom: 1px solid #f3f4f6;
            padding: 0.75rem;
        }

        .note-editor .note-editing-area .note-editable {
            min-height: 420px;
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.75;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto">
        {{-- ── Page Header ── --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Produk</h1>
                <nav class="text-xs md:text-sm text-gray-400 font-medium mt-1">
                    <ol class="flex items-center gap-2">
                        <li><a href="/home" class="hover:text-brand-primary transition-colors">Dashboard</a></li>
                        <li><i class="fa-solid fa-chevron-right text-[10px]"></i></li>
                        <li class="text-brand-dark">Produk</li>
                    </ol>
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openCreateModal()"
                    class="px-5 py-3 bg-brand-primary text-white rounded-2xl font-bold shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span class="hidden sm:inline">Tambah Produk</span>
                </button>
            </div>
        </div>

        {{-- ── Summary Cards ── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @php
                $totalProduk = $products->count();
                $totalAktif = $products->where('is_active', true)->count();
                $stokHabis = $products->where('stock', 0)->count();
                $punyaVarian = $products->where('has_variant', true)->count();
            @endphp
            <div class="bg-white rounded-3xl px-5 py-4 border border-gray-50 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 tracking-widest">TOTAL PRODUK</p>
                    <p class="text-2xl font-extrabold text-brand-dark leading-tight">{{ $totalProduk }}</p>
                </div>
            </div>
            <div class="bg-white rounded-3xl px-5 py-4 border border-gray-50 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-green-50 flex items-center justify-center text-green-500">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 tracking-widest">AKTIF</p>
                    <p class="text-2xl font-extrabold text-brand-dark leading-tight">{{ $totalAktif }}</p>
                </div>
            </div>
            <div class="bg-white rounded-3xl px-5 py-4 border border-gray-50 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-red-50 flex items-center justify-center text-red-400">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 tracking-widest">STOK HABIS</p>
                    <p class="text-2xl font-extrabold text-brand-dark leading-tight">{{ $stokHabis }}</p>
                </div>
            </div>
            <div class="bg-white rounded-3xl px-5 py-4 border border-gray-50 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500">
                    <i class="fa-solid fa-layer-group text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-gray-400 tracking-widest">PUNYA VARIAN</p>
                    <p class="text-2xl font-extrabold text-brand-dark leading-tight">{{ $punyaVarian }}</p>
                </div>
            </div>
        </div>

        {{-- ── Table ── --}}
        <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 overflow-hidden px-6 py-8">
            <table id="datatable" class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 text-[11px] tracking-widest border-b border-gray-50">
                        <th class="px-4 py-4 text-left">No</th>
                        <th class="px-4 py-4 text-left">Gambar</th>
                        <th class="px-4 py-4 text-left">Nama Produk</th>
                        <th class="px-4 py-4 text-left">Kategori / Brand</th>
                        <th class="px-4 py-4 text-left">Koleksi</th>
                        <th class="px-4 py-4 text-left">Harga</th>
                        <th class="px-4 py-4 text-left">Stok</th>
                        <th class="px-4 py-4 text-left">Varian</th>
                        <th class="px-4 py-4 text-left">Status</th>
                        <th class="px-4 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($products as $i => $product)
                        @php $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                        <tr class="hover:bg-soft-bg/50 transition-colors">
                            <td class="px-4 py-5 font-bold text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-5">
                                @if($primary)
                                    <img src="{{ asset('storage/' . $primary->image_url) }}"
                                        class="w-12 h-12 rounded-2xl object-cover border border-gray-100 shadow-sm">
                                @else
                                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 border border-dashed border-gray-200">
                                        <i class="fa-solid fa-box-open"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-5">
                                <div class="font-bold text-brand-dark">{{ $product->name }}</div>
                                @if($product->sku)
                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5">Kode: {{ $product->sku }}</div>
                                @endif
                                @if($product->short_description)
                                    <div class="text-[10px] text-gray-400 mt-0.5 max-w-[200px] truncate">{{ $product->short_description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-5">
                                <div class="text-xs font-semibold text-gray-600">{{ $product->category->name ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $product->brand->name ?? 'No Brand' }}</div>
                            </td>
                            <td class="px-4 py-5">
                                @if($product->collections->isNotEmpty())
                                    <span class="px-3 py-1 rounded-full bg-brand-primary/10 text-brand-primary text-[10px] font-black tracking-wider">
                                        {{ $product->collections->first()->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs italic">Tanpa koleksi</span>
                                @endif
                            </td>
                            <td class="px-4 py-5">
                                @if($product->has_variant)
                                    @php
                                        $vMin = $product->variants_min_price;
                                        $vMax = $product->variants_max_price;
                                    @endphp
                                    <div class="font-bold text-brand-dark text-sm">
                                        @if($vMin || $vMax)
                                            Rp {{ number_format($vMin, 0, ',', '.') }}
                                            @if($vMax && $vMax != $vMin)
                                                &ndash; Rp {{ number_format($vMax, 0, ',', '.') }}
                                            @endif
                                        @else
                                            <span class="text-gray-300 font-medium text-xs">Belum diisi</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-blue-400 mt-0.5">{{ $product->variants_count }} varian</div>
                                @else
                                    <div class="font-bold text-brand-dark text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                    @if($product->compare_price)
                                        <div class="text-[10px] text-gray-400 line-through mt-0.5">Rp {{ number_format($product->compare_price, 0, ',', '.') }}</div>
                                    @endif
                                    @if($product->modal_price)
                                        <div class="text-[10px] text-amber-500 mt-0.5 font-semibold">
                                            <i class="fa-solid fa-arrow-trend-up text-[8px]"></i>
                                            Margin: Rp {{ number_format($product->price - $product->modal_price, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-5">
                                @if($product->stock <= 0)
                                    <span class="flex items-center gap-1 font-bold text-sm text-red-500">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Habis
                                    </span>
                                @elseif($product->stock <= 5)
                                    <span class="flex items-center gap-1 font-bold text-sm text-amber-500">
                                        <i class="fa-solid fa-circle text-[6px]"></i> {{ $product->stock }}
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 font-bold text-sm text-gray-700">
                                        <i class="fa-solid fa-circle text-[6px] text-green-400"></i> {{ $product->stock }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-5">
                                @if($product->has_variant)
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider bg-blue-50 text-blue-600">
                                        {{ $product->variants_count }} Varian
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider bg-gray-50 text-gray-400">
                                        Tanpa Varian
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-5">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider {{ $product->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal({{ $product->id }})"
                                        class="w-9 h-9 flex items-center justify-center bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all shadow-sm"
                                        title="Edit Produk">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <button onclick="deleteProduct({{ $product->id }})"
                                        class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-600 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-sm"
                                        title="Hapus Produk">
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

    {{-- ══════════════════════════════════════════════════════════════════════════
         MODAL
    ══════════════════════════════════════════════════════════════════════════ --}}
    <div id="productModal"
        class="fixed inset-0 hidden bg-slate-900/50 backdrop-blur-sm items-start justify-center z-[100] p-4 overflow-y-auto">
        <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20 my-6">

            {{-- Header --}}
            <div class="px-8 py-5 border-b border-gray-100 bg-white flex items-center justify-between sticky top-0 z-20 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-brand-primary shadow-lg shadow-brand-primary/20 flex items-center justify-center text-white">
                        <i class="fa-solid fa-box text-lg"></i>
                    </div>
                    <div>
                        <h2 id="modalTitle" class="text-lg font-extrabold text-brand-dark leading-tight">Tambah Produk</h2>
                        <p class="text-[10px] text-gray-400 font-bold tracking-[0.15em]">Catalog Management</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="w-9 h-9 flex items-center justify-center rounded-full text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-1 border-b border-gray-100 px-6 bg-white sticky top-[81px] z-10">
                <button type="button" onclick="switchTab('info')" id="tab-info"
                    class="tab-btn px-5 py-4 text-[11px] font-black tracking-widest border-b-2 border-brand-primary text-brand-primary -mb-px transition-all whitespace-nowrap">
                    <i class="fa-solid fa-circle-info mr-1.5"></i>Info
                </button>
                <button type="button" onclick="switchTab('harga')" id="tab-harga"
                    class="tab-btn px-5 py-4 text-[11px] font-black tracking-widest border-b-2 border-transparent text-gray-400 -mb-px transition-all hover:text-brand-dark whitespace-nowrap">
                    <i class="fa-solid fa-tags mr-1.5"></i>Harga & Stok
                </button>
                <button type="button" onclick="switchTab('images')" id="tab-images"
                    class="tab-btn px-5 py-4 text-[11px] font-black tracking-widest border-b-2 border-transparent text-gray-400 -mb-px transition-all hover:text-brand-dark whitespace-nowrap">
                    <i class="fa-solid fa-images mr-1.5"></i>Gambar
                </button>
                <button type="button" onclick="switchTab('variants')" id="tab-variants"
                    class="tab-btn px-5 py-4 text-[11px] font-black tracking-widest border-b-2 border-transparent text-gray-400 -mb-px transition-all hover:text-brand-dark whitespace-nowrap">
                    <i class="fa-solid fa-layer-group mr-1.5"></i>Varian
                </button>
            </div>

            <form id="productForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="methodField">

                <div class="p-8">

                    {{-- ═══════ TAB: INFO ═══════ --}}
                    <div id="panel-info" class="tab-panel space-y-5">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Nama Produk --}}
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">NAMA PRODUK <span class="text-red-400">*</span></label>
                                <div class="relative group">
                                    <i class="fa-solid fa-box absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-primary transition-colors text-xs"></i>
                                    <input type="text" name="name" id="productName" required placeholder="Masukkan nama produk lengkap..."
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold">
                                </div>
                            </div>

                            {{-- Kategori --}}
                            <div class="space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">KATEGORI <span class="text-red-400">*</span></label>
                                <div class="relative group">
                                    <i class="fa-solid fa-folder absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-primary transition-colors text-xs"></i>
                                    <select name="category_id" id="productCategory" required
                                        class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold appearance-none">
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- Brand --}}
                            <div class="space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">BRAND</label>
                                <div class="relative group">
                                    <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-primary transition-colors text-xs"></i>
                                    <select name="brand_id" id="productBrand"
                                        class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold appearance-none">
                                        <option value="">-- Tanpa Brand --</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- Kode --}}
                            <div class="space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">KODE <span class="text-gray-300">(Opsional)</span></label>
                                <div class="relative group">
                                    <i class="fa-solid fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-primary transition-colors text-xs"></i>
                                    <input type="text" name="sku" id="productSku" placeholder="Kode unik produk..."
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold">
                                </div>
                            </div>

                            {{-- Berat --}}
                            <div class="space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">BERAT <span class="text-gray-300">(gram)</span></label>
                                <div class="relative group">
                                    <i class="fa-solid fa-weight-hanging absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-primary transition-colors text-xs"></i>
                                    <input type="number" name="weight" id="productWeight" min="0" placeholder="0"
                                        class="w-full pl-10 pr-16 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-gray-300">gram</span>
                                </div>
                            </div>

                            {{-- Deskripsi Singkat --}}
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">DESKRIPSI SINGKAT</label>
                                <div class="relative">
                                    <textarea name="short_description" id="productShortDesc" rows="2"
                                        maxlength="500"
                                        placeholder="Ringkasan produk untuk ditampilkan di listing (maks 500 karakter)..."
                                        oninput="document.getElementById('shortDescCount').textContent = this.value.length"
                                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold resize-none"></textarea>
                                    <span class="absolute bottom-3 right-4 text-[10px] text-gray-300 font-bold">
                                        <span id="shortDescCount">0</span>/500
                                    </span>
                                </div>
                            </div>

                            {{-- Deskripsi Lengkap (Summernote) --}}
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">DESKRIPSI LENGKAP</label>
                                <p class="ml-1 text-[10px] text-gray-400">Bisa tambah gambar langsung lewat toolbar editor.</p>
                                <textarea name="description" id="productDesc" rows="12"></textarea>
                            </div>

                            {{-- Koleksi --}}
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">KOLEKSI <span class="text-gray-300">(Opsional)</span></label>
                                @if($collections->isEmpty())
                                    <p class="text-xs text-gray-400 px-1">Belum ada koleksi. <a href="{{ route('koleksi.index') }}" class="text-brand-primary font-semibold hover:underline">Buat koleksi</a> terlebih dahulu.</p>
                                @else
                                    <div class="relative group">
                                        <i class="fa-solid fa-table-cells-large absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-primary transition-colors text-xs"></i>
                                        <select name="collection_id" id="productCollection"
                                            class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold appearance-none">
                                            <option value="">Tanpa koleksi khusus</option>
                                        @foreach($collections as $col)
                                                <option value="{{ $col->id }}">{{ $col->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <p class="ml-1 text-[11px] text-gray-400">Dipakai untuk filter halaman koleksi seperti Best Seller, Hijab, Syar'i, atau New Arrived.</p>
                                @endif
                            </div>

                            {{-- Toggle Varian --}}
                            <div class="md:col-span-2 flex items-center gap-4 px-5 py-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="checkbox" name="has_variant" id="hasVariant" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-primary"></div>
                                </label>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">Produk memiliki varian</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Aktifkan untuk produk dengan pilihan warna, ukuran, dll. Harga & stok dikelola per varian di tab Varian.</p>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="md:col-span-2 flex items-center gap-3 px-1">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="productStatus" value="1" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-primary"></div>
                                    <span class="ml-3 text-sm font-bold text-gray-600">Tampilkan Produk di Publik</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- ═══════ TAB: HARGA & STOK ═══════ --}}
                    <div id="panel-harga" class="tab-panel hidden space-y-5">

                        {{-- Kalkulasi Margin --}}
                        <div id="marginCard" class="hidden bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-5 border border-emerald-100">
                            <p class="text-[10px] font-black text-emerald-700 tracking-widest mb-3"><i class="fa-solid fa-chart-line mr-2"></i>KALKULASI MARGIN OTOMATIS</p>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center bg-white rounded-2xl p-3 shadow-sm">
                                    <p class="text-[9px] font-black text-gray-400 tracking-widest">HARGA MODAL</p>
                                    <p id="calc-modal" class="text-lg font-extrabold text-gray-700 mt-1">Rp 0</p>
                                </div>
                                <div class="text-center bg-white rounded-2xl p-3 shadow-sm">
                                    <p class="text-[9px] font-black text-gray-400 tracking-widest">MARGIN</p>
                                    <p id="calc-margin" class="text-lg font-extrabold text-emerald-600 mt-1">Rp 0</p>
                                </div>
                                <div class="text-center bg-white rounded-2xl p-3 shadow-sm">
                                    <p class="text-[9px] font-black text-gray-400 tracking-widest">MARGIN %</p>
                                    <p id="calc-pct" class="text-lg font-extrabold text-emerald-600 mt-1">0%</p>
                                </div>
                            </div>
                            <div class="mt-3 h-2 bg-emerald-100 rounded-full overflow-hidden">
                                <div id="marginBar" class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width:0%"></div>
                            </div>
                            <p id="marginNote" class="text-[10px] text-gray-400 mt-2 text-center font-semibold"></p>
                        </div>

                        {{-- Info: harga & stok dikelola di varian (tampil saat varian aktif) --}}
                        <div class="hidden" id="variantPriceInfo">
                            <div class="flex items-start gap-3 p-4 bg-blue-50 rounded-2xl border border-blue-100">
                                <i class="fa-solid fa-circle-info text-blue-400 mt-0.5"></i>
                                <p class="text-xs text-blue-700 font-semibold leading-relaxed">
                                    Produk ini menggunakan varian. <strong>Harga jual, harga beli, harga coret, dan stok</strong>
                                    dikelola per varian — atur di tab
                                    <button type="button" onclick="switchTab('variants')" class="underline font-black">Varian</button>.
                                    <br><span class="text-blue-500 font-normal">Harga produk utama akan otomatis diambil dari harga varian terendah.</span>
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5" id="productPriceFields">

                            {{-- Harga Modal / HPP --}}
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">HARGA MODAL / HPP</label>
                                <p class="ml-1 text-[10px] text-gray-400">Harga Pokok Penjualan. Tidak ditampilkan ke publik, hanya untuk kalkulasi margin.</p>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-black">Rp</span>
                                    <input type="text" name="modal_price" id="productModalPrice"
                                        placeholder="0" inputmode="numeric"
                                        class="w-full pl-10 pr-4 py-3 bg-amber-50/50 border border-amber-200 rounded-2xl focus:ring-4 focus:ring-amber-200/50 focus:border-amber-400 focus:bg-white outline-none transition-all text-sm font-semibold"
                                        oninput="formatRupiah(this); recalcMargin()">
                                </div>
                            </div>

                            {{-- Harga Jual --}}
                            <div class="space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">HARGA JUAL <span class="text-red-400">*</span></label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-black">Rp</span>
                                    <input type="text" name="price" id="productPrice" required
                                        placeholder="0" inputmode="numeric"
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold"
                                        oninput="formatRupiah(this); recalcMargin()">
                                </div>
                            </div>

                            {{-- Harga Coret --}}
                            <div class="space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">HARGA CORET <span class="text-gray-300">(Opsional)</span></label>
                                <div class="relative group">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-black line-through">Rp</span>
                                    <input type="text" name="compare_price" id="productComparePrice"
                                        placeholder="0" inputmode="numeric"
                                        class="w-full pl-10 pr-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold"
                                        oninput="formatRupiah(this); validateComparePrice()">
                                </div>
                                <p id="comparePriceError" class="hidden ml-1 text-[10px] text-red-500 font-semibold">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Harga coret harus lebih besar dari harga jual.
                                </p>
                                <p class="ml-1 text-[10px] text-gray-400">Tampil sebagai harga coret untuk promo diskon.</p>
                            </div>

                            {{-- Stok --}}
                            <div class="space-y-1.5 md:col-span-2" id="stockField">
                                <label class="ml-1 text-[10px] font-black text-gray-400 tracking-widest">STOK <span class="text-red-400">*</span></label>
                                <div class="relative group">
                                    <i class="fa-solid fa-cubes absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-brand-primary transition-colors text-xs"></i>
                                    <input type="number" name="stock" id="productStock" min="0" placeholder="0"
                                        class="w-full pl-10 pr-20 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-brand-primary/10 focus:border-brand-primary focus:bg-white outline-none transition-all text-sm font-semibold">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-gray-300">unit</span>
                                </div>
                                <div id="stockWarning" class="hidden ml-1 flex items-center gap-1.5 text-[10px] text-red-500 font-semibold">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Stok menipis! Segera restok produk.
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ═══════ TAB: GAMBAR ═══════ --}}
                    <div id="panel-images" class="tab-panel hidden space-y-4">

                        {{-- Header bar --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 tracking-widest">FOTO PRODUK</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, WEBP — maks 2 MB per foto. Foto pertama otomatis jadi foto utama.</p>
                            </div>
                            <button type="button" onclick="addImageSlot()"
                                class="inline-flex items-center gap-2 px-4 py-2.5 text-[11px] font-black tracking-widest bg-brand-primary text-white rounded-xl hover:bg-brand-dark transition-all shadow-sm shadow-brand-primary/20">
                                <i class="fa-solid fa-plus text-xs"></i> Tambah Foto
                            </button>
                        </div>

                        {{-- Image list --}}
                        <div id="imageList" class="space-y-2.5"></div>

                        {{-- Empty state --}}
                        <div id="imageEmpty"
                            class="py-14 text-center border-2 border-dashed border-gray-200 rounded-3xl bg-gray-50/50">
                            <i class="fa-solid fa-images text-5xl text-gray-200 mb-4 block"></i>
                            <p class="text-sm font-semibold text-gray-400">Belum ada foto produk</p>
                            <p class="text-xs text-gray-300 mt-1">Klik "+ Tambah Foto" untuk menambahkan</p>
                        </div>
                    </div>

                    {{-- ═══════ TAB: VARIAN ═══════ --}}
                    <div id="panel-variants" class="tab-panel hidden space-y-5">

                        {{-- Warning: varian tidak aktif --}}
                        <div id="variantDisabledNote" class="flex items-center gap-3 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                            <p class="text-xs text-amber-700 font-semibold">Aktifkan toggle "Produk memiliki varian" di tab <button type="button" onclick="switchTab('info')" class="underline font-black">Info</button> terlebih dahulu.</p>
                        </div>

                        <div id="variantBuilder" class="hidden space-y-5">

                            {{-- Tipe Varian Builder --}}
                            <div class="rounded-3xl border border-blue-100 bg-blue-50/40 p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-black text-blue-700 tracking-widest"><i class="fa-solid fa-sliders mr-2"></i>TIPE VARIAN</p>
                                        <p class="text-[10px] text-blue-500 mt-0.5">Definisikan tipe dan nilainya, lalu klik Generate.</p>
                                    </div>
                                </div>

                                <div id="variantTypes" class="space-y-2"></div>

                                <button type="button" onclick="addVariantType()"
                                    class="px-4 py-2 text-[10px] font-black tracking-widest bg-white border border-blue-200 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all">
                                    <i class="fa-solid fa-plus mr-1"></i> Tambah Tipe
                                </button>

                                {{-- Default price / stok --}}
                                <div class="pt-2 border-t border-blue-100">
                                    <p class="text-[9px] font-black text-blue-600 tracking-widest mb-2">HARGA & STOK DEFAULT <span class="font-normal text-blue-400">(diisi otomatis ke semua kombinasi saat Generate)</span></p>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-gray-400">Rp</span>
                                            <input type="text" id="defaultPrice" placeholder="Harga jual" inputmode="numeric"
                                                class="w-full pl-7 pr-2 py-2 bg-white border border-blue-200 rounded-xl text-[10px] font-semibold outline-none focus:border-brand-primary"
                                                oninput="formatRupiah(this)">
                                        </div>
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[9px] font-black text-amber-400">Rp</span>
                                            <input type="text" id="defaultPurchasePrice" placeholder="Harga beli" inputmode="numeric"
                                                class="w-full pl-7 pr-2 py-2 bg-amber-50/60 border border-amber-100 rounded-xl text-[10px] font-semibold outline-none focus:border-amber-400"
                                                oninput="formatRupiah(this)">
                                        </div>
                                        <input type="number" id="defaultStock" placeholder="Stok" min="0"
                                            class="w-full px-3 py-2 bg-white border border-blue-200 rounded-xl text-[10px] font-semibold outline-none focus:border-brand-primary">
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-1">
                                    <p class="text-[10px] text-amber-600 font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Generate akan mengganti semua varian di bawah</p>
                                    <button type="button" onclick="generateVariants()"
                                        class="px-5 py-2 text-[10px] font-black tracking-widest bg-brand-primary text-white rounded-xl hover:bg-brand-dark transition-all flex items-center gap-1.5">
                                        <i class="fa-solid fa-rotate"></i> Generate Kombinasi
                                    </button>
                                </div>
                            </div>

                            {{-- Variant table --}}
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-xs font-black text-gray-400 tracking-widest">DAFTAR VARIAN <span id="variantCountBadge" class="ml-2 px-2 py-0.5 bg-blue-50 text-blue-600 rounded-lg text-[10px]">0</span></p>
                                </div>

                                {{-- Table header --}}
                                <div id="variantTableHeader" class="hidden grid gap-1.5 px-3 mb-1" style="grid-template-columns:36px 1fr 90px 90px 90px 60px 70px 60px 28px">
                                    <span class="text-[9px] font-black text-gray-400 tracking-widest">IMG</span>
                                    <span class="text-[9px] font-black text-gray-400 tracking-widest">NAMA</span>
                                    <span class="text-[9px] font-black text-gray-400 tracking-widest">HARGA JUAL</span>
                                    <span class="text-[9px] font-black text-gray-400 tracking-widest">HARGA BELI</span>
                                    <span class="text-[9px] font-black text-gray-400 tracking-widest">HARGA CORET</span>
                                    <span class="text-[9px] font-black text-gray-400 tracking-widest">STOK</span>
                                    <span class="text-[9px] font-black text-gray-400 tracking-widest">KODE</span>
                                    <span class="text-[9px] font-black text-gray-400 tracking-widest">BERAT</span>
                                    <span></span>
                                </div>

                                <div id="variantList" class="space-y-1.5"></div>

                                {{-- Summary --}}
                                <div id="variantSummary" class="hidden mt-4 grid grid-cols-4 gap-3 text-center bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                    <div><p class="text-[9px] text-gray-400 font-bold">TOTAL STOK</p><p id="sumStok" class="text-base font-extrabold text-brand-dark mt-0.5">0</p></div>
                                    <div><p class="text-[9px] text-gray-400 font-bold">HARGA MIN</p><p id="sumHargaMin" class="text-base font-extrabold text-brand-dark mt-0.5">Rp0</p></div>
                                    <div><p class="text-[9px] text-gray-400 font-bold">HARGA MAX</p><p id="sumHargaMax" class="text-base font-extrabold text-brand-dark mt-0.5">Rp0</p></div>
                                    <div><p class="text-[9px] text-gray-400 font-bold">MARGIN MIN</p><p id="sumMarginMin" class="text-base font-extrabold text-emerald-600 mt-0.5">-</p></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /p-8 --}}

                {{-- Footer --}}
                <div class="flex items-center justify-between gap-3 px-8 py-5 border-t border-gray-100 bg-gray-50/30">
                    <p id="formHint" class="text-[10px] text-gray-400 font-semibold hidden sm:block">
                        <i class="fa-solid fa-circle-info mr-1"></i> Field bertanda <span class="text-red-400">*</span> wajib diisi.
                    </p>
                    <div class="flex items-center gap-3 ml-auto">
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-3 rounded-2xl text-xs font-black tracking-widest text-gray-400 hover:bg-gray-100 transition-all">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                            class="px-10 py-3 rounded-2xl bg-brand-primary text-white text-xs font-black tracking-[0.1em] shadow-xl shadow-brand-primary/20 hover:bg-brand-dark hover:-translate-y-0.5 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span id="btnText">Simpan Produk</span>
                            <i id="loader" class="fa-solid fa-circle-notch animate-spin hidden"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        {{-- Summernote Lite CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

        <script>
        // ══════════════════════════════════════════════════════
        // UTILITY: Format Rupiah
        // ══════════════════════════════════════════════════════
        function formatRupiah(el) {
            let raw = el.value.replace(/\D/g, '');
            el.value = raw ? parseInt(raw, 10).toLocaleString('id-ID') : '';
        }

        function parseRupiah(val) {
            return parseInt((val || '0').replace(/\D/g, ''), 10) || 0;
        }

        function rupiah(num) {
            return 'Rp ' + (num || 0).toLocaleString('id-ID');
        }

        function escHtml(str) {
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }

        window.previewVariantImage = function (input, id) {
            if (!input.files?.[0]) return;
            const reader = new FileReader();
            reader.onload = e => {
                const $el = $(`#vimg-preview-${id}`);
                if ($el.is('img')) {
                    $el.attr('src', e.target.result);
                } else {
                    $el.replaceWith(`<img id="vimg-preview-${id}" src="${e.target.result}" class="w-full h-full object-cover">`);
                }
            };
            reader.readAsDataURL(input.files[0]);
        };

        // ══════════════════════════════════════════════════════
        // MARGIN CALCULATOR
        // ══════════════════════════════════════════════════════
        function recalcMargin() {
            const modal  = parseRupiah($('#productModalPrice').val());
            const jual   = parseRupiah($('#productPrice').val());

            if (!modal && !jual) { $('#marginCard').addClass('hidden'); return; }
            $('#marginCard').removeClass('hidden');

            const margin = jual - modal;
            const pct    = modal > 0 ? ((margin / modal) * 100).toFixed(1) : 0;
            const barW   = Math.min(Math.max(pct, 0), 100);

            $('#calc-modal').text(rupiah(modal));
            $('#calc-margin').text(rupiah(margin)).toggleClass('text-red-500', margin < 0).toggleClass('text-emerald-600', margin >= 0);
            $('#calc-pct').text(pct + '%').toggleClass('text-red-500', pct < 0).toggleClass('text-emerald-600', pct >= 0);
            $('#marginBar').css('width', barW + '%').toggleClass('bg-red-400', margin < 0).toggleClass('bg-emerald-500', margin >= 0);

            let note = '';
            if (modal > 0) {
                if (pct < 0)       note = '⚠️ Harga jual di bawah modal!';
                else if (pct < 10) note = 'Margin sangat tipis, pertimbangkan menaikkan harga.';
                else if (pct < 30) note = 'Margin normal.';
                else               note = '✅ Margin sehat!';
            }
            $('#marginNote').text(note);
        }

        // ══════════════════════════════════════════════════════
        // Summernote instance
        // ══════════════════════════════════════════════════════
        let summernoteReady = false;

        function initSummernote() {
            if (summernoteReady) return;

            $('#productDesc').summernote({
                placeholder: 'Tulis deskripsi lengkap produk di sini. Gunakan tombol gambar untuk menambahkan foto ke deskripsi.',
                height: 420,
                minHeight: 360,
                dialogsInBody: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'table']],
                    ['view', ['fullscreen', 'codeview', 'help']],
                ],
                callbacks: {
                    onImageUpload: function (files) {
                        Array.from(files).forEach(uploadDescriptionImage);
                    },
                },
            });

            summernoteReady = true;
        }

        function uploadDescriptionImage(file) {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: "{{ route('products.description-image') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    $('#productDesc').summernote('insertImage', res.url);
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message || 'Gagal mengupload gambar deskripsi.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Gambar Gagal',
                        text: message,
                        confirmButtonColor: '#A78B6F',
                    });
                },
            });
        }

        function destroySummernote() {
            if (!summernoteReady) return;
            $('#productDesc').summernote('destroy');
            summernoteReady = false;
        }

        function getDescriptionData() {
            return summernoteReady ? $('#productDesc').summernote('code') : $('#productDesc').val();
        }

        function setDescriptionData(val) {
            if (summernoteReady) $('#productDesc').summernote('code', val || '');
            else $('#productDesc').val(val || '');
        }

        // ══════════════════════════════════════════════════════
        // DATATABLE
        // ══════════════════════════════════════════════════════
        $(document).ready(function () {
            $('#datatable').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari produk...",
                    lengthMenu: "Show _MENU_",
                }
            });

            // Stok warning
            $('#productStock').on('input', function () {
                const val = parseInt(this.value) || 0;
                $('#stockWarning').toggleClass('hidden', val > 5 || val === 0);
            });

            // Toggle varian
            $('#hasVariant').on('change', toggleVariantMode);

            // Submit: inject raw numeric values for all price + description fields
            $('#productForm').on('submit', function (e) {
                // Summernote — remove stale hidden then inject fresh value
                $('[name="description"][type="hidden"]').remove();
                $('<input>').attr({ type: 'hidden', name: 'description', value: getDescriptionData() }).appendTo('#productForm');

                // Price fields — only inject when NOT in variant mode
                if (!$('#hasVariant').is(':checked')) {
                    const priceMap = {
                        productModalPrice:   'modal_price',
                        productPrice:        'price',
                        productComparePrice: 'compare_price',
                    };
                    Object.entries(priceMap).forEach(([elId, fieldName]) => {
                        const $el = $('#' + elId);
                        const raw = parseRupiah($el.val());
                        $el.prop('name', '');
                        $('[name="' + fieldName + '"][type="hidden"]').remove();
                        $('<input>').attr({ type: 'hidden', name: fieldName, value: raw }).appendTo('#productForm');
                    });
                }

                buildVariantsHidden();
                $('#submitBtn').prop('disabled', true).addClass('opacity-70');
                $('#btnText').text('Memproses...');
                $('#loader').removeClass('hidden');
            });

            $('#productModal').on('click', function (e) {
                if (e.target === this) closeModal();
            });

        });

        // ══════════════════════════════════════════════════════
        // TAB SWITCHING
        // ══════════════════════════════════════════════════════
        function switchTab(name) {
            $('.tab-panel').addClass('hidden');
            $('.tab-btn').removeClass('border-brand-primary text-brand-primary').addClass('border-transparent text-gray-400');
            $('#panel-' + name).removeClass('hidden');
            $('#tab-' + name).addClass('border-brand-primary text-brand-primary').removeClass('border-transparent text-gray-400');

            if (name === 'info') {
                // Lazy-init Summernote saat panel info tampil
                setTimeout(initSummernote, 100);
            }
        }

        // ══════════════════════════════════════════════════════
        // MODAL OPEN / CLOSE
        // ══════════════════════════════════════════════════════
        window.openCreateModal = function () {
            resetModal();
            $('#modalTitle').text('Tambah Produk');
            $('#productForm').attr('action', '/products');
            $('#methodField').val('POST');
            $('#btnText').text('Simpan Produk');
            switchTab('info');
            $('#productModal').removeClass('hidden').addClass('flex');
            setTimeout(initSummernote, 200);
        }

        window.openEditModal = async function (id) {
            resetModal();
            $('#modalTitle').text('Edit Produk');
            $('#productForm').attr('action', `/products/${id}`);
            $('#methodField').val('PUT');
            $('#btnText').text('Perbarui Produk');

            try {
                const res  = await fetch(`/products/${id}`);
                const data = await res.json();
                fillForm(data);
            } catch (e) {
                Swal.fire('Error', 'Gagal memuat data produk', 'error');
                return;
            }

            switchTab('info');
            $('#productModal').removeClass('hidden').addClass('flex');
            setTimeout(initSummernote, 200);
        }

        window.closeModal = function () {
            $('#productModal').addClass('hidden').removeClass('flex');
            destroySummernote();
        }

        function resetModal() {
            destroySummernote();
            $('#productForm')[0].reset();
            $('#productCollection').val('');
            $('#imageList').empty();
            $('#imageEmpty').removeClass('hidden');
            pendingCounter = 0;
            $('#variantList').empty();
            variantRows = []; variantTypes = []; varCounter = 0; typeCounter = 0;
            $('#variantTypes').empty();
            $('#variantTableHeader').addClass('hidden');
            $('#stockField').removeClass('hidden');
            $('#productStock').attr('required', 'required');
            $('#variantStockInfo').addClass('hidden');
            $('#variantDisabledNote').removeClass('hidden');
            $('#variantBuilder').addClass('hidden');
            $('#variantSummary').addClass('hidden');
            $('#marginCard').addClass('hidden');
            $('#submitBtn').prop('disabled', false).removeClass('opacity-70');
            $('#loader').addClass('hidden');
            $('#productDesc').val('');
        }

        function fillForm(data) {
            $('#productName').val(data.name);
            $('#productCategory').val(data.category_id);
            $('#productBrand').val(data.brand_id);
            $('#productShortDesc').val(data.short_description || '');
            $('#shortDescCount').text((data.short_description || '').length);
            $('#productSku').val(data.sku);
            $('#productWeight').val(data.weight);
            $('#productStatus').prop('checked', !!data.is_active);
            $('#hasVariant').prop('checked', !!data.has_variant);

            // Harga — tampil format rupiah
            if (data.modal_price) setRupiahField('#productModalPrice', data.modal_price);
            setRupiahField('#productPrice', data.price);
            if (data.compare_price) setRupiahField('#productComparePrice', data.compare_price);

            if (!data.has_variant) $('#productStock').val(data.stock);

            recalcMargin();
            toggleVariantMode(true);

            // Summernote
            setTimeout(() => setDescriptionData(data.description), 300);

            // Images
            if (data.images?.length) renderExistingImages(data.id, data.images);

            $('#productCollection').val(data.collection_id || '');

            // Variants
            if (data.has_variant && data.variants?.length) {
                // Rekonstruksi tipe builder dari atribut yang sudah ada
                const typeMap = {};
                data.variants.forEach(v => {
                    (v.attributes ?? []).forEach(a => {
                        const n = a.attribute_name ?? a.name ?? '';
                        const val = a.attribute_value ?? a.value ?? '';
                        if (!n) return;
                        if (!typeMap[n]) typeMap[n] = [];
                        if (!typeMap[n].includes(val)) typeMap[n].push(val);
                    });
                });
                Object.entries(typeMap).forEach(([name, values]) => addVariantType(name, values));

                data.variants.forEach(v => addManualVariant(v));
                updateVariantSummary();
            }
        }

        function setRupiahField(selector, val) {
            const num = Math.round(parseFloat(val) || 0);
            $(selector).val(num.toLocaleString('id-ID'));
        }

        // ══════════════════════════════════════════════════════
        // TOGGLE VARIAN MODE
        // ══════════════════════════════════════════════════════
        function toggleVariantMode(fromFillForm = false) {
            const active = $('#hasVariant').is(':checked');
            if (active) {
                $('#productPriceFields').addClass('hidden');
                $('#productPrice').removeAttr('required');
                $('#productStock').removeAttr('required');
                $('#variantPriceInfo').removeClass('hidden');
                $('#marginCard').addClass('hidden');
                $('#variantDisabledNote').addClass('hidden');
                $('#variantBuilder').removeClass('hidden');
            } else {
                $('#productPriceFields').removeClass('hidden');
                $('#productPrice').attr('required', 'required');
                $('#productStock').attr('required', 'required');
                $('#variantPriceInfo').addClass('hidden');
                $('#variantDisabledNote').removeClass('hidden');
                $('#variantBuilder').addClass('hidden');
            }
        }

        function validateComparePrice() {
            const cp = parseRupiah($('#productComparePrice').val());
            const p  = parseRupiah($('#productPrice').val());
            const invalid = cp > 0 && p > 0 && cp <= p;
            $('#comparePriceError').toggleClass('hidden', !invalid);
        }

        // ══════════════════════════════════════════════════════
        // IMAGE HANDLING
        // ══════════════════════════════════════════════════════
        let pendingCounter = 0;

        function updateImageEmpty() {
            const hasRows = $('#imageList').children().length > 0;
            $('#imageEmpty').toggleClass('hidden', hasRows);
        }

        // ── Pending slot (new image to upload) ──────────────
        window.addImageSlot = function () {
            const n = ++pendingCounter;
            $('#imageList').append(`
                <div id="pending-slot-${n}" class="img-pending-row flex items-center gap-4 p-3 bg-white border border-gray-200 rounded-2xl transition-all hover:border-brand-primary/40">
                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center cursor-pointer"
                         onclick="document.getElementById('pending-file-${n}').click()">
                        <img id="pending-preview-${n}" class="w-full h-full object-cover hidden" alt="">
                        <i class="fa-solid fa-image text-2xl text-gray-200" id="pending-icon-${n}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p id="pending-name-${n}" class="text-sm font-semibold text-gray-400 truncate">Belum ada foto dipilih</p>
                        <label for="pending-file-${n}"
                            class="mt-1 inline-flex items-center gap-1.5 text-xs font-bold text-brand-primary cursor-pointer hover:underline">
                            <i class="fa-solid fa-folder-open text-xs"></i> Pilih Foto
                        </label>
                        <input type="file" name="images[]" id="pending-file-${n}"
                            accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only"
                            onchange="previewPending(this, ${n})">
                    </div>
                    <button type="button" onclick="removePendingSlot(${n})"
                        class="w-9 h-9 flex-shrink-0 flex items-center justify-center bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all" title="Batalkan">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>`);
            updateImageEmpty();
        }

        window.previewPending = function (input, n) {
            const file = input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                $(`#pending-preview-${n}`).attr('src', e.target.result).removeClass('hidden');
                $(`#pending-icon-${n}`).addClass('hidden');
                $(`#pending-name-${n}`).text(file.name).removeClass('text-gray-400').addClass('text-gray-700');
            };
            reader.readAsDataURL(file);
        }

        window.removePendingSlot = function (n) {
            $(`#pending-slot-${n}`).fadeOut(200, function () { $(this).remove(); updateImageEmpty(); });
        }

        // ── Saved images (edit mode) ─────────────────────────
        function renderExistingImages(productId, images) {
            $('#imageList').find('.img-saved-row').remove();

            images.forEach((img, i) => {
                const isPrimary = !!img.is_primary;
                const row = `
                    <div id="img-row-${img.id}" class="img-saved-row flex items-center gap-4 p-3 rounded-2xl border transition-all
                        ${isPrimary ? 'bg-brand-primary/5 border-brand-primary/30' : 'bg-white border-gray-100 shadow-sm'}">

                        <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-50 border border-gray-100">
                            <img src="/storage/${img.image_url}" class="w-full h-full object-cover" alt="">
                        </div>

                        <div class="flex-1 min-w-0">
                            ${isPrimary
                                ? `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[9px] font-black tracking-widest rounded-full bg-brand-primary text-white">
                                       <i class="fa-solid fa-star text-[8px]"></i> FOTO UTAMA
                                   </span>`
                                : `<span class="text-xs font-semibold text-gray-400">Foto ${i + 1}</span>`
                            }
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            ${!isPrimary ? `
                            <button type="button" onclick="setPrimaryImg(${productId}, ${img.id})"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-[10px] font-black tracking-widest rounded-xl border border-amber-200 bg-amber-50 text-amber-600 hover:bg-amber-400 hover:text-white hover:border-amber-400 transition-all"
                                title="Jadikan foto utama">
                                <i class="fa-solid fa-star text-[9px]"></i>
                                <span>Jadikan Foto Utama</span>
                            </button>` : ''}

                            <label class="inline-flex items-center gap-1.5 px-3 py-2 text-[10px] font-black tracking-widest rounded-xl border border-blue-100 bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white hover:border-blue-500 transition-all cursor-pointer">
                                <i class="fa-solid fa-arrow-rotate-right text-[9px]"></i>
                                <span class="hidden sm:inline">Ganti</span>
                                <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only"
                                    onchange="replaceImg(${productId}, ${img.id}, this, 'img-row-${img.id}')">
                            </label>

                            <button type="button" onclick="deleteImg(${productId}, ${img.id}, 'img-row-${img.id}')"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-[10px] font-black tracking-widest rounded-xl border border-red-100 bg-red-50 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all">
                                <i class="fa-solid fa-trash text-[9px]"></i>
                                <span class="hidden sm:inline">Hapus</span>
                            </button>
                        </div>
                    </div>`;
                $('#imageList').prepend(row);
            });

            updateImageEmpty();
        }

        window.deleteImg = async function (productId, imgId, rowId) {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus foto ini?',
                text: 'Foto akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
            });
            if (!isConfirmed) return;

            const res = await fetch(`/products/${productId}/images/${imgId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (res.ok) {
                $(`#${rowId}`).fadeOut(250, function () { $(this).remove(); updateImageEmpty(); });
            }
        }

        window.setPrimaryImg = async function (productId, imgId) {
            await fetch(`/products/${productId}/images/primary/${imgId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            // Re-fetch and re-render images without reloading the page
            const res  = await fetch(`/products/${productId}`);
            const data = await res.json();
            renderExistingImages(productId, data.images);
        }

        window.replaceImg = async function (productId, oldImgId, fileInput, rowId) {
            const file = fileInput.files[0];
            if (!file) return;

            // Delete old image
            await fetch(`/products/${productId}/images/${oldImgId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            // Remove old row
            $(`#${rowId}`).remove();

            // Build a pending slot pre-loaded with the chosen file
            const n = ++pendingCounter;
            const dt = new DataTransfer();
            dt.items.add(file);

            $('#imageList').append(`
                <div id="pending-slot-${n}" class="img-pending-row flex items-center gap-4 p-3 bg-blue-50/40 border border-blue-200 rounded-2xl">
                    <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100 border border-gray-100">
                        <img id="pending-preview-${n}" class="w-full h-full object-cover" alt="">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[9px] font-black tracking-widest text-blue-500 mb-1">FOTO BARU (PENGGANTI)</p>
                        <p id="pending-name-${n}" class="text-sm font-semibold text-gray-700 truncate">${file.name}</p>
                        <label for="pending-file-${n}" class="mt-0.5 inline-flex items-center gap-1.5 text-xs font-bold text-brand-primary cursor-pointer hover:underline">
                            <i class="fa-solid fa-folder-open text-xs"></i> Ganti pilihan
                        </label>
                        <input type="file" name="images[]" id="pending-file-${n}"
                            accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only"
                            onchange="previewPending(this, ${n})">
                    </div>
                    <button type="button" onclick="removePendingSlot(${n})"
                        class="w-9 h-9 flex-shrink-0 flex items-center justify-center bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>`);

            // Inject the file into the new input and show preview
            document.getElementById(`pending-file-${n}`).files = dt.files;
            const reader = new FileReader();
            reader.onload = e => $(`#pending-preview-${n}`).attr('src', e.target.result);
            reader.readAsDataURL(file);

            updateImageEmpty();
        }

        // ══════════════════════════════════════════════════════
        // VARIANT BUILDER
        // ══════════════════════════════════════════════════════
        // ── Variant state ──────────────────────────────────────
        let variantRows = [], variantTypes = [], varCounter = 0, typeCounter = 0;

        // ── Tipe builder ────────────────────────────────────────
        window.addVariantType = function (name = '', values = []) {
            const tid = ++typeCounter;
            variantTypes.push({ id: tid, name, values: [...values] });

            const chipsHtml = values.map(v => chipHtml(tid, v)).join('');
            $('#variantTypes').append(`
                <div class="flex items-start gap-2" id="vtype-${tid}">
                    <input type="text" value="${escHtml(name)}" placeholder="Tipe (cth: Ukuran)"
                        class="vtype-name w-28 flex-shrink-0 px-3 py-2 bg-white border border-blue-200 rounded-xl text-xs font-semibold outline-none focus:border-brand-primary"
                        oninput="updateTypeName(${tid}, this.value)">
                    <div class="flex-1 flex flex-wrap items-center gap-1.5 px-2 py-1.5 bg-white border border-blue-200 rounded-xl min-h-[38px]"
                        id="vtype-chips-${tid}">
                        ${chipsHtml}
                        <input type="text" placeholder="Nilai, Enter untuk tambah"
                            class="vtype-value-input flex-1 min-w-[120px] text-xs font-semibold outline-none bg-transparent py-0.5"
                            onkeydown="handleTypeValueInput(event, ${tid})">
                    </div>
                    <button type="button" onclick="removeVariantType(${tid})"
                        class="w-8 h-8 flex-shrink-0 flex items-center justify-center bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all mt-0.5">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>`);
        };

        function chipHtml(tid, value) {
            return `<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-brand-primary/10 text-brand-dark rounded-lg text-[10px] font-bold">
                ${escHtml(value)}
                <button type="button" onclick="removeTypeValue(${tid}, '${escHtml(value).replace(/'/g,"\\'")}')">
                    <i class="fa-solid fa-xmark text-[8px] text-gray-400 hover:text-red-500"></i>
                </button>
            </span>`;
        }

        window.updateTypeName = function (tid, val) {
            const t = variantTypes.find(t => t.id === tid);
            if (t) t.name = val.trim();
        };

        window.handleTypeValueInput = function (e, tid) {
            if (e.key !== 'Enter' && e.key !== ',') return;
            e.preventDefault();
            const input = e.target;
            const val   = input.value.trim().replace(/,$/, '');
            if (!val) return;
            const t = variantTypes.find(t => t.id === tid);
            if (!t || t.values.includes(val)) { input.value = ''; return; }
            t.values.push(val);
            $(`#vtype-chips-${tid} .vtype-value-input`).before(chipHtml(tid, val));
            input.value = '';
        };

        window.removeTypeValue = function (tid, val) {
            const t = variantTypes.find(t => t.id === tid);
            if (t) t.values = t.values.filter(v => v !== val);
            // re-render chips
            const $input = $(`#vtype-chips-${tid} .vtype-value-input`).detach();
            $(`#vtype-chips-${tid}`).empty().append(
                t.values.map(v => chipHtml(tid, v)).join('') + $('<div>').append($input).html()
            );
            // re-attach
            $(`#vtype-chips-${tid}`).append($input);
        };

        window.removeVariantType = function (tid) {
            variantTypes = variantTypes.filter(t => t.id !== tid);
            $(`#vtype-${tid}`).remove();
        };

        // ── Generator ───────────────────────────────────────────
        window.generateVariants = function () {
            const types = variantTypes.filter(t => t.name && t.values.length);
            if (!types.length) {
                Swal.fire({ icon: 'info', title: 'Belum ada tipe', text: 'Tambah minimal 1 tipe varian dengan nilainya.', timer: 2000, showConfirmButton: false });
                return;
            }

            const defaultPrice    = parseRupiah($('#defaultPrice').val());
            const defaultPurchase = parseRupiah($('#defaultPurchasePrice').val());
            const defaultStock    = $('#defaultStock').val();

            // Buat semua kombinasi (cartesian product)
            const combos = types.reduce((acc, type) => {
                if (!acc.length) return type.values.map(v => [{ name: type.name, value: v }]);
                return acc.flatMap(combo => type.values.map(v => [...combo, { name: type.name, value: v }]));
            }, []);

            // Nama varian yang sudah ada di tabel
            const existingNames = new Set(
                variantRows.map(row => $(`#variant-row-${row.id}`).find('.variant-name').val().trim())
            );

            // Hanya tambah kombinasi yang belum ada
            let added = 0;
            combos.forEach(combo => {
                const label = combo.map(c => c.value).join(' - ');
                if (existingNames.has(label)) return;
                addManualVariant({
                    name:           label,
                    price:          defaultPrice    || '',
                    purchase_price: defaultPurchase || '',
                    compare_price:  '',
                    stock:          defaultStock    ?? '',
                    sku:            '',
                    weight:         '',
                    attributes:     combo,
                });
                added++;
            });

            if (!added) {
                Swal.fire({ icon: 'info', title: 'Semua kombinasi sudah ada', timer: 1500, showConfirmButton: false });
            }
            updateVariantSummary();
        };

        // ── Tambah / hapus variant row ──────────────────────────
        window.addManualVariant = function (data = null) {
            const id            = ++varCounter;
            const dbId          = data?.id ?? '';
            const name          = data?.name ?? '';
            const attrs         = data?.attributes ?? [];
            const price         = data?.price         ? Math.round(parseFloat(data.price)).toLocaleString('id-ID') : '';
            const purchasePrice = data?.purchase_price ? Math.round(parseFloat(data.purchase_price)).toLocaleString('id-ID') : '';
            const comparePrice  = data?.compare_price  ? Math.round(parseFloat(data.compare_price)).toLocaleString('id-ID') : '';
            const stock         = data?.stock  ?? '';
            const sku           = data?.sku    ?? '';
            const weight        = data?.weight ?? '';

            const existingImage = data?.image ? '/storage/' + data.image : '';
            variantRows.push({ id, dbId, attrs });

            $('#variantTableHeader').removeClass('hidden');
            $('#variantList').append(`
                <div class="grid items-center gap-1.5 px-3 py-2.5 bg-white border border-gray-100 rounded-xl hover:border-gray-200 transition-all"
                    id="variant-row-${id}"
                    style="grid-template-columns:36px 1fr 90px 90px 90px 60px 70px 60px 28px">

                    {{-- Gambar --}}
                    <div class="relative w-9 h-9 flex-shrink-0 cursor-pointer group" onclick="document.getElementById('vimg-${id}').click()" title="Klik untuk pilih gambar">
                        <div class="w-9 h-9 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center group-hover:border-brand-primary transition-all">
                            ${existingImage
                                ? `<img id="vimg-preview-${id}" src="${existingImage}" class="w-full h-full object-cover">`
                                : `<span id="vimg-preview-${id}"><i class="fa-solid fa-image text-gray-300 text-[10px]"></i></span>`
                            }
                        </div>
                        <input type="file" id="vimg-${id}" name="variant_image_${id}" accept="image/*" class="sr-only"
                            onchange="previewVariantImage(this, ${id})">
                    </div>

                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fa-solid fa-grip-lines text-gray-300 text-[10px] flex-shrink-0"></i>
                        <input type="text" value="${escHtml(name)}" placeholder="Nama varian"
                            class="variant-name min-w-0 w-full text-xs font-bold text-brand-dark bg-transparent outline-none border-b border-transparent focus:border-gray-300 truncate">
                        <input type="hidden" class="variant-db-id" value="${dbId}">
                    </div>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[9px] text-gray-400 font-bold">Rp</span>
                        <input type="text" value="${price}" placeholder="0" inputmode="numeric"
                            class="variant-price w-full pl-6 pr-1 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-semibold outline-none focus:border-brand-primary"
                            oninput="formatRupiah(this); updateVariantSummary()">
                    </div>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[9px] text-amber-400 font-bold">Rp</span>
                        <input type="text" value="${purchasePrice}" placeholder="0" inputmode="numeric"
                            class="variant-purchase-price w-full pl-6 pr-1 py-1.5 bg-amber-50/60 border border-amber-100 rounded-lg text-[10px] font-semibold outline-none focus:border-amber-400"
                            oninput="formatRupiah(this)">
                    </div>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-[9px] text-gray-300 font-bold line-through">Rp</span>
                        <input type="text" value="${comparePrice}" placeholder="0" inputmode="numeric"
                            class="variant-compare-price w-full pl-6 pr-1 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-semibold outline-none focus:border-brand-primary"
                            oninput="formatRupiah(this)">
                    </div>
                    <input type="number" value="${stock}" placeholder="0" min="0"
                        class="variant-stock w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-semibold text-center outline-none focus:border-brand-primary"
                        oninput="updateVariantSummary()">
                    <input type="text" value="${escHtml(sku)}" placeholder="SKU"
                        class="variant-sku w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-semibold outline-none focus:border-brand-primary">
                    <input type="number" value="${weight}" placeholder="g" min="0"
                        class="variant-weight w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-[10px] font-semibold outline-none focus:border-brand-primary">
                    <button type="button" onclick="removeVariantRow(${id})"
                        class="w-7 h-7 flex items-center justify-center bg-red-50 text-red-400 rounded-lg hover:bg-red-500 hover:text-white transition-all">
                        <i class="fa-solid fa-xmark text-[10px]"></i>
                    </button>
                </div>`);

            updateVariantSummary();
        };

        window.removeVariantRow = function (id) {
            variantRows = variantRows.filter(v => v.id !== id);
            $(`#variant-row-${id}`).remove();
            if (!variantRows.length) $('#variantTableHeader').addClass('hidden');
            updateVariantSummary();
        };

        function updateVariantSummary() {
            const count = variantRows.length;
            $('#variantCountBadge').text(count);
            if (!count) { $('#variantSummary').addClass('hidden'); return; }
            $('#variantSummary').removeClass('hidden');

            let totalStok = 0, prices = [], margins = [];
            variantRows.forEach(row => {
                const $r = $(`#variant-row-${row.id}`);
                totalStok += parseInt($r.find('.variant-stock').val()) || 0;
                const p   = parseRupiah($r.find('.variant-price').val());
                const pp  = parseRupiah($r.find('.variant-purchase-price').val());
                if (p > 0) prices.push(p);
                if (p > 0 && pp > 0) margins.push(p - pp);
            });

            $('#sumStok').text(totalStok);
            $('#sumHargaMin').text(prices.length ? rupiah(Math.min(...prices)) : 'Rp0');
            $('#sumHargaMax').text(prices.length ? rupiah(Math.max(...prices)) : 'Rp0');
            $('#sumMarginMin').text(margins.length ? rupiah(Math.min(...margins)) : '-');
        }

        function buildVariantsHidden() {
            $('[name^="variants["]').remove();
            if (!$('#hasVariant').is(':checked')) return;

            variantRows.forEach((row, idx) => {
                const $row          = $(`#variant-row-${row.id}`);
                const name          = $row.find('.variant-name').val();
                const price         = parseRupiah($row.find('.variant-price').val());
                const purchasePrice = parseRupiah($row.find('.variant-purchase-price').val());
                const comparePrice  = parseRupiah($row.find('.variant-compare-price').val());
                const stock         = $row.find('.variant-stock').val();
                const sku           = $row.find('.variant-sku').val();
                const wt            = $row.find('.variant-weight').val();
                const dbId          = $row.find('.variant-db-id').val();

                const pfx = `variants[${idx}]`;
                const add = (n, v) => $('#productForm').append(`<input type="hidden" name="${n}" value="${v}">`);

                if (dbId)          add(`${pfx}[id]`,             dbId);
                add(`${pfx}[name]`,                              name);
                add(`${pfx}[price]`,                             price);
                if (purchasePrice) add(`${pfx}[purchase_price]`, purchasePrice);
                if (comparePrice)  add(`${pfx}[compare_price]`,  comparePrice);
                add(`${pfx}[stock]`,                             stock);
                if (sku) add(`${pfx}[sku]`,    sku);
                if (wt)  add(`${pfx}[weight]`, wt);
                add(`${pfx}[front_id]`,                          row.id);

                // Simpan atribut (dari generator atau dari data edit)
                const attrs = row.attrs?.length
                    ? row.attrs
                    : [];
                attrs.forEach((a, ai) => {
                    const attrName  = a.attribute_name ?? a.name  ?? '';
                    const attrValue = a.attribute_value ?? a.value ?? '';
                    if (attrName && attrValue) {
                        add(`${pfx}[attributes][${ai}][name]`,  attrName);
                        add(`${pfx}[attributes][${ai}][value]`, attrValue);
                    }
                });
            });
        }

        // ══════════════════════════════════════════════════════
        // DELETE PRODUCT
        // ══════════════════════════════════════════════════════
        window.deleteProduct = function (id) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Data produk, gambar, dan semua varian akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2D5A27',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then(r => {
                if (r.isConfirmed) {
                    $('<form>', { method: 'POST', action: `/products/${id}` })
                        .append($('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }))
                        .append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }))
                        .appendTo('body').submit();
                }
            });
        }
        </script>
    @endpush
@endsection
