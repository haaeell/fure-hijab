@extends('layouts.customer')

@section('title', 'Alamat Saya — FURE')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 200px; border-radius: 14px; z-index: 0; border: 1px solid #e5e7eb; }
    .leaflet-container { font-family: inherit; }
    .label-chip.active { background: #A78B6F; color: #fff; border-color: #A78B6F; }
</style>
@endpush

@section('content')
<div class="bg-[#f8f3ee] min-h-screen py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
<div class="max-w-5xl mx-auto">

    {{-- ── Page header ── --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                <a href="{{ route('profile.index') }}" class="hover:text-brand-primary font-semibold transition-colors">Profil</a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <span class="font-bold text-brand-dark">Alamat Saya</span>
            </div>
            <h1 class="text-2xl font-extrabold text-brand-dark">Alamat Pengiriman</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola alamat yang digunakan untuk pengiriman pesanan</p>
        </div>
        {{-- Mobile: scroll ke form --}}
        <a href="#form-section"
            class="lg:hidden flex-shrink-0 flex items-center gap-2 px-4 py-2.5 bg-brand-primary text-white text-xs font-bold rounded-xl hover:bg-brand-dark transition-all">
            <i class="fa-solid fa-plus"></i> Tambah
        </a>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-100 rounded-2xl px-5 py-3.5 text-sm font-semibold text-green-700">
            <i class="fa-solid fa-circle-check text-green-500 flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid lg:grid-cols-[1fr_360px] gap-6 items-start">

        {{-- ── LEFT: Daftar Alamat ── --}}
        <div class="space-y-3">
            @if($addresses->isNotEmpty())
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">
                    {{ $addresses->count() }} alamat tersimpan
                </p>
            @endif

            @forelse($addresses as $addr)
            <div class="group bg-white rounded-[20px] border-2 {{ $addr->is_default ? 'border-brand-primary/30' : 'border-transparent shadow-sm' }} p-5 transition-all hover:border-brand-primary/20">
                <div class="flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="mt-0.5 w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center
                        {{ $addr->is_default ? 'bg-brand-primary text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-brand-primary/10 group-hover:text-brand-primary' }} transition-colors">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-extrabold text-brand-dark text-sm">{{ $addr->label }}</span>
                            @if($addr->is_default)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-brand-primary text-white text-[9px] font-black rounded-full uppercase tracking-wider">
                                    <i class="fa-solid fa-star text-[7px]"></i> Utama
                                </span>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-brand-dark mt-1.5">{{ $addr->receiver_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $addr->phone }}</p>
                        <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                            {{ $addr->address }},
                            {{ $addr->district }}, {{ $addr->city }},
                            {{ $addr->province }} {{ $addr->postal_code }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1.5 mt-4 pt-3.5 border-t border-gray-100">
                    @if(!$addr->is_default)
                        <form method="POST" action="{{ route('addresses.default', $addr) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-primary hover:text-brand-dark px-3 py-1.5 rounded-lg hover:bg-brand-primary/5 transition-all">
                                <i class="fa-regular fa-star text-[10px]"></i>
                                Jadikan Utama
                            </button>
                        </form>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400 px-3 py-1.5">
                            <i class="fa-solid fa-star text-[10px] text-brand-primary"></i>
                            Alamat Utama
                        </span>
                    @endif

                    <form method="POST" action="{{ route('addresses.destroy', $addr) }}" class="ml-auto delete-form">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete(this.closest('form'), '{{ $addr->label }}')"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-red-400 hover:text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-all">
                            <i class="fa-solid fa-trash-can text-[10px]"></i>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-[20px] border border-dashed border-gray-200 p-14 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-map-pin text-3xl text-gray-200"></i>
                </div>
                <p class="font-bold text-brand-dark">Belum ada alamat</p>
                <p class="text-xs text-gray-400 mt-1.5">Tambahkan alamat pengiriman pertama kamu di form di bawah</p>
                <a href="#form-section" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-brand-primary hover:text-brand-dark transition-colors">
                    <i class="fa-solid fa-arrow-down"></i> Ke Form Tambah Alamat
                </a>
            </div>
            @endforelse
        </div>

        {{-- ── RIGHT: Form Tambah Alamat ── --}}
        <div id="form-section" class="bg-white rounded-[24px] border border-gray-100 shadow-sm overflow-hidden lg:sticky lg:top-6">
            {{-- Form header --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary flex-shrink-0">
                    <i class="fa-solid fa-location-dot text-sm"></i>
                </div>
                <div>
                    <h2 class="font-extrabold text-brand-dark text-sm">Tambah Alamat Baru</h2>
                    <p class="text-[10px] text-gray-400 mt-0.5">Isi detail alamat pengiriman</p>
                </div>
            </div>

            <div class="px-6 py-5">
                @if($errors->any())
                    <div class="mb-4 flex items-start gap-2.5 rounded-xl bg-red-50 border border-red-100 px-3.5 py-3 text-xs font-semibold text-red-600">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('addresses.store') }}" method="POST" id="addr-form" class="space-y-4">
                    @csrf

                    {{-- Label + Quick Chips --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">
                            Label Alamat <span class="text-red-400">*</span>
                        </label>
                        <div class="flex gap-2 mb-2 flex-wrap">
                            @foreach(['Rumah','Kantor','Kost','Lainnya'] as $chip)
                                <button type="button" onclick="setLabel('{{ $chip }}')"
                                    class="label-chip px-3 py-1.5 text-[11px] font-bold border border-gray-200 rounded-full text-gray-500 hover:border-brand-primary hover:text-brand-primary transition-all">
                                    {{ $chip }}
                                </button>
                            @endforeach
                        </div>
                        <input type="text" name="label" id="label-input" value="{{ old('label') }}"
                            placeholder="Atau ketik label…" required maxlength="50"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition">
                    </div>

                    {{-- Nama & Telp --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nama Penerima <span class="text-red-400">*</span></label>
                            <input type="text" name="receiver_name" value="{{ old('receiver_name') }}" placeholder="Nama lengkap" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition">
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">No. Telepon <span class="text-red-400">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="08xx-xxxx-xxxx" required inputmode="tel"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition">
                        </div>
                    </div>

                    {{-- Area Search --}}
                    <div class="relative">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">
                            Cari Kecamatan / Kota <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <input type="text" id="area-search" placeholder="Ketik nama kecamatan atau kota…" autocomplete="off"
                                class="w-full  px-4 py-2.5 pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition">
                        </div>
                        <div id="area-results"
                            class="hidden absolute z-30 left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden max-h-44 overflow-y-auto">
                        </div>
                    </div>

                    {{-- Area fields (auto-filled) --}}
                    <div id="area-selected" class="{{ old('district') ? '' : 'hidden' }} bg-gray-50 rounded-xl border border-gray-200 px-4 py-3">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Area Terpilih</p>
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                            <div><span class="text-gray-400">Kecamatan:</span> <span id="show-district" class="font-semibold text-brand-dark">{{ old('district') }}</span></div>
                            <div><span class="text-gray-400">Kota:</span> <span id="show-city" class="font-semibold text-brand-dark">{{ old('city') }}</span></div>
                            <div><span class="text-gray-400">Provinsi:</span> <span id="show-province" class="font-semibold text-brand-dark">{{ old('province') }}</span></div>
                            <div><span class="text-gray-400">Kode Pos:</span> <span id="show-postal" class="font-semibold text-brand-dark">{{ old('postal_code') }}</span></div>
                        </div>
                    </div>
                    <input type="hidden" name="district"         id="f-district"    value="{{ old('district') }}">
                    <input type="hidden" name="city"             id="f-city"        value="{{ old('city') }}">
                    <input type="hidden" name="province"         id="f-province"    value="{{ old('province') }}">
                    <input type="hidden" name="postal_code"      id="f-postal"      value="{{ old('postal_code') }}">
                    <input type="hidden" name="subdistrict"      id="f-subdistrict" value="{{ old('subdistrict') }}">
                    <input type="hidden" name="biteship_area_id" id="f-area-id"     value="{{ old('biteship_area_id') }}">
                    <input type="hidden" name="latitude"         id="f-lat"         value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude"        id="f-lng"         value="{{ old('longitude') }}">

                    {{-- Map --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">
                            Pin Lokasi
                            <span class="normal-case text-gray-400 font-semibold tracking-normal ml-1">(opsional — klik atau geser pin)</span>
                        </label>
                        <div id="map"></div>
                    </div>

                    {{-- Alamat lengkap --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Alamat Lengkap <span class="text-red-400">*</span></label>
                        <textarea name="address" rows="2" required
                            placeholder="Nama jalan, nomor rumah, RT/RW, patokan…"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary outline-none transition resize-none">{{ old('address') }}</textarea>
                    </div>

                    {{-- Default checkbox --}}
                    <label class="flex items-center gap-3 cursor-pointer select-none group">
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="is_default" id="is_default" value="1"
                                {{ old('is_default') ? 'checked' : '' }} class="peer sr-only">
                            <div class="w-5 h-5 rounded-md border-2 border-gray-300 bg-white peer-checked:bg-brand-primary peer-checked:border-brand-primary transition-all flex items-center justify-center">
                                <i class="fa-solid fa-check text-white text-[9px] hidden peer-checked:block"></i>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-brand-dark group-hover:text-brand-primary transition-colors">Jadikan sebagai alamat utama</span>
                    </label>

                    <button type="submit"
                        class="w-full py-3 bg-brand-primary text-white text-sm font-extrabold rounded-xl hover:bg-brand-dark transition-all active:scale-[.98] shadow-sm shadow-brand-primary/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Alamat
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    /* ── Label chips ── */
    window.setLabel = function (val) {
        document.getElementById('label-input').value = val;
        document.querySelectorAll('.label-chip').forEach(function (c) {
            c.classList.toggle('active', c.textContent.trim() === val);
        });
    };

    /* ── Checkbox custom render ── */
    var cb = document.getElementById('is_default');
    var checkIcon = cb.closest('label').querySelector('.fa-check');
    function syncCheck() { checkIcon.style.display = cb.checked ? 'block' : 'none'; }
    cb.addEventListener('change', syncCheck); syncCheck();

    /* ── Map ── */
    var defaultLat = -6.2, defaultLng = 106.8;
    var map    = L.map('map', { zoomControl: true }).setView([defaultLat, defaultLng], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap', maxZoom: 19
    }).addTo(map);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function setCoords(lat, lng) {
        document.getElementById('f-lat').value = lat.toFixed(7);
        document.getElementById('f-lng').value = lng.toFixed(7);
    }
    marker.on('dragend', function (e) { var p = e.target.getLatLng(); setCoords(p.lat, p.lng); });
    map.on('click', function (e) { marker.setLatLng(e.latlng); setCoords(e.latlng.lat, e.latlng.lng); });

    /* ── Area search ── */
    var timer;
    var searchEl  = document.getElementById('area-search');
    var resultsEl = document.getElementById('area-results');
    var selectedEl = document.getElementById('area-selected');

    searchEl.addEventListener('input', function () {
        clearTimeout(timer);
        var q = this.value.trim();
        if (q.length < 3) { resultsEl.classList.add('hidden'); return; }
        timer = setTimeout(function () { fetchAreas(q); }, 320);
    });

    document.addEventListener('click', function (e) {
        if (!searchEl.contains(e.target) && !resultsEl.contains(e.target)) {
            resultsEl.classList.add('hidden');
        }
    });

    function fetchAreas(q) {
        fetch('{{ route('checkout.search-destination') }}?search=' + encodeURIComponent(q))
            .then(function (r) { return r.json(); })
            .then(function (data) {
                resultsEl.innerHTML = '';
                if (!data.length) {
                    resultsEl.innerHTML =
                        '<div class="px-4 py-4 text-center">' +
                        '<i class="fa-solid fa-magnifying-glass text-gray-200 text-lg mb-1.5 block"></i>' +
                        '<p class="text-xs font-semibold text-gray-400">Area tidak ditemukan</p>' +
                        '</div>';
                    resultsEl.classList.remove('hidden');
                    return;
                }
                data.forEach(function (area) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left px-4 py-3 text-xs hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0';
                    btn.innerHTML =
                        '<span class="font-bold text-brand-dark block">' +
                            (area.district || '') + ', ' + (area.city || '') +
                        '</span>' +
                        '<span class="text-gray-400">' +
                            (area.province || '') +
                            (area.postal_code ? ' · ' + area.postal_code : '') +
                        '</span>';
                    btn.addEventListener('click', function () {
                        var d = area.district    || '';
                        var s = area.subdistrict || d;
                        var c = area.city        || '';
                        var p = area.province    || '';
                        var z = area.postal_code || '';

                        document.getElementById('f-district').value    = d;
                        document.getElementById('f-subdistrict').value  = s;
                        document.getElementById('f-city').value         = c;
                        document.getElementById('f-province').value     = p;
                        document.getElementById('f-postal').value       = z;
                        document.getElementById('f-area-id').value      = area.id;

                        document.getElementById('show-district').textContent = d;
                        document.getElementById('show-city').textContent     = c;
                        document.getElementById('show-province').textContent = p;
                        document.getElementById('show-postal').textContent   = z;
                        selectedEl.classList.remove('hidden');

                        searchEl.value = d + ', ' + c;
                        resultsEl.classList.add('hidden');

                        if (area.latitude && area.longitude) {
                            var ll = [parseFloat(area.latitude), parseFloat(area.longitude)];
                            map.setView(ll, 13);
                            marker.setLatLng(ll);
                            setCoords(ll[0], ll[1]);
                        }
                    });
                    resultsEl.appendChild(btn);
                });
                resultsEl.classList.remove('hidden');
            })
            .catch(function () {});
    }

    /* ── Delete confirmation ── */
    window.confirmDelete = function (form, label) {
        if (typeof Swal === 'undefined') { form.submit(); return; }
        Swal.fire({
            title: 'Hapus Alamat?',
            html: 'Alamat <strong>' + label + '</strong> akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#e5e7eb',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: { cancelButton: '!text-gray-600' }
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    };
})();
</script>
@endpush
