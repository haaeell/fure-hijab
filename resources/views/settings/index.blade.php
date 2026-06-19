@extends('layouts.app')

@section('title', 'Pengaturan Integrasi')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-8">
            <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Pengaturan Integrasi</h1>
            <p class="text-xs md:text-sm text-gray-400 font-medium mt-1">Atur kredensial Biteship, Midtrans, dan email SMTP langsung dari panel admin.</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary/10 text-brand-primary flex items-center justify-center">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-brand-dark">Profil Toko</h3>
                        <p class="text-xs text-gray-400 font-medium">Dipakai untuk logo, nama brand, footer, kontak, dan tombol WhatsApp.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Toko</label>
                        <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Logo Toko</label>
                        <input type="file" name="store_logo" accept="image/*"
                            class="block w-full cursor-pointer rounded-2xl border border-gray-200 bg-gray-50/50 p-1 text-xs text-gray-400 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-primary file:px-5 file:py-2.5 file:text-[10px] file:font-black file:uppercase file:text-white hover:file:bg-brand-dark">
                        @if($settings['store_logo'])
                            <div class="mt-2 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $settings['store_logo']) }}" class="h-10 w-10 rounded-xl object-cover border border-gray-100" alt="Logo toko">
                                <span class="text-xs font-semibold text-gray-400">Logo aktif</span>
                            </div>
                        @endif
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Email Toko</label>
                        <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Telepon</label>
                        <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor WhatsApp</label>
                        <input type="text" name="store_whatsapp" value="{{ old('store_whatsapp', $settings['store_whatsapp']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Instagram</label>
                        <input type="text" name="store_instagram" value="{{ old('store_instagram', $settings['store_instagram']) }}" placeholder="https://instagram.com/fure"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">TikTok</label>
                        <input type="text" name="store_tiktok" value="{{ old('store_tiktok', $settings['store_tiktok']) }}" placeholder="https://tiktok.com/@fure"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat Toko</label>
                        <textarea name="store_address" rows="3"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold resize-none">{{ old('store_address', $settings['store_address']) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-brand-dark">Biteship</h3>
                        <p class="text-xs text-gray-400 font-medium">Dipakai untuk cek ongkir, generate resi otomatis, label, dan tracking.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">API Key</label>
                        <div class="relative">
                            <input type="password" name="biteship_api_key" id="biteship_api_key" value="{{ old('biteship_api_key', $settings['biteship_api_key']) }}"
                                class="w-full px-4 py-3 pr-12 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            <button type="button" class="toggle-secret absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand-primary transition-colors" data-target="biteship_api_key">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Webhook Secret</label>
                        <div class="relative">
                            <input type="password" name="biteship_webhook_secret" id="biteship_webhook_secret" value="{{ old('biteship_webhook_secret', $settings['biteship_webhook_secret']) }}"
                                placeholder="Opsional, isi token rahasia yang sama di dashboard pengiriman"
                                class="w-full px-4 py-3 pr-12 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            <button type="button" class="toggle-secret absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand-primary transition-colors" data-target="biteship_webhook_secret">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 ml-1">Jika diisi, webhook hanya diterima ketika request membawa Bearer token, header secret, atau query token yang sama.</p>
                    </div>
                    <div class="md:col-span-2 space-y-1.5 relative">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Area Origin Biteship</label>
                        @php
                            $originAreaId = old('biteship_origin_area_id', $settings['biteship_origin_area_id']);
                            $originAreaLabel = old('biteship_origin_area_label', $settings['biteship_origin_area_label']);
                            $originAreaDisplay = $originAreaLabel ?: ($originAreaId ? 'Area origin sudah dipilih' : '');
                        @endphp
                        <input type="hidden" name="biteship_origin_area_id" id="biteship_origin_area_id"
                            value="{{ $originAreaId }}">
                        <input type="hidden" name="biteship_origin_area_label" id="biteship_origin_area_label"
                            value="{{ $originAreaLabel }}">
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-primary">
                                <i class="fa-solid fa-magnifying-glass text-sm"></i>
                            </div>
                            <input type="text" id="biteship_origin_area_search"
                                value="{{ $originAreaDisplay }}"
                                placeholder="Cari dan pilih area gudang/toko..."
                                autocomplete="off"
                                class="w-full pl-11 pr-12 py-3 bg-white border-2 border-dashed border-brand-primary/30 rounded-2xl focus:border-brand-primary focus:border-solid outline-none transition-all text-sm font-semibold cursor-pointer">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        <div id="biteship_origin_area_results"
                            class="hidden absolute z-30 mt-2 w-full bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden"></div>
                        <div id="biteship_origin_area_status"
                            class="{{ $originAreaId ? '' : 'hidden' }} inline-flex items-center gap-2 ml-1 mt-1 px-3 py-1.5 rounded-xl bg-soft-mint/50 text-brand-primary text-[11px] font-bold">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            <span>{{ $originAreaLabel ? 'Area origin sudah dipilih' : 'Area origin tersimpan. Pilih ulang jika ingin memperbarui nama area.' }}</span>
                        </div>
                        <p class="text-[11px] text-gray-400 ml-1">
                            Klik field ini, ketik minimal 3 huruf, lalu pilih salah satu hasil Biteship. Field ini bukan isian bebas; pilihan yang dipilih akan dipakai untuk cek ongkir dan cetak resi.
                        </p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Origin / Gudang</label>
                        <input type="text" name="biteship_origin_contact_name" value="{{ old('biteship_origin_contact_name', $settings['biteship_origin_contact_name']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Telepon Origin</label>
                        <input type="text" name="biteship_origin_contact_phone" value="{{ old('biteship_origin_contact_phone', $settings['biteship_origin_contact_phone']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat Origin / Gudang</label>
                        <textarea name="biteship_origin_address" rows="3"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold resize-none">{{ old('biteship_origin_address', $settings['biteship_origin_address']) }}</textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kode Pos Origin</label>
                        <input type="text" name="biteship_origin_postal_code" value="{{ old('biteship_origin_postal_code', $settings['biteship_origin_postal_code']) }}"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Latitude</label>
                            <input type="text" name="biteship_origin_latitude" id="biteship_origin_latitude" value="{{ old('biteship_origin_latitude', $settings['biteship_origin_latitude']) }}"
                                class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                        </div>
                        <div class="space-y-1.5">
                            <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Longitude</label>
                            <input type="text" name="biteship_origin_longitude" id="biteship_origin_longitude" value="{{ old('biteship_origin_longitude', $settings['biteship_origin_longitude']) }}"
                                class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pin Lokasi Origin</label>
                                <p class="text-[11px] text-gray-400 ml-1 mt-1">Klik map atau geser pin untuk set latitude dan longitude gudang/toko.</p>
                            </div>
                            <button type="button" id="btn-origin-my-location"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-soft-mint text-brand-primary text-xs font-black hover:bg-brand-primary hover:text-white transition-all">
                                <i class="fa-solid fa-location-crosshairs"></i>
                                Gunakan Lokasi Saya
                            </button>
                        </div>
                        <div class="rounded-[24px] overflow-hidden border border-gray-200 shadow-sm bg-gray-50">
                            <div id="origin_map" class="w-full h-72"></div>
                        </div>
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
                        <div class="relative">
                            <input type="password" name="midtrans_server_key" id="midtrans_server_key" value="{{ old('midtrans_server_key', $settings['midtrans_server_key']) }}"
                                class="w-full px-4 py-3 pr-12 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            <button type="button" class="toggle-secret absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand-primary transition-colors" data-target="midtrans_server_key">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Client Key</label>
                        <div class="relative">
                            <input type="password" name="midtrans_client_key" id="midtrans_client_key" value="{{ old('midtrans_client_key', $settings['midtrans_client_key']) }}"
                                class="w-full px-4 py-3 pr-12 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            <button type="button" class="toggle-secret absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand-primary transition-colors" data-target="midtrans_client_key">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
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

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-brand-dark">Email SMTP</h3>
                        <p class="text-xs text-gray-400 font-medium">Dipakai untuk lupa password dan email otomatis toko.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Mailer</label>
                        <select name="mail_mailer"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            @foreach(['smtp' => 'SMTP', 'log' => 'Log saja', 'array' => 'Array / testing'] as $value => $label)
                                <option value="{{ $value }}" {{ old('mail_mailer', $settings['mail_mailer']) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Scheme</label>
                        <select name="mail_scheme"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            @foreach(['smtp' => 'smtp - Gmail 587', 'smtps' => 'smtps - SSL 465'] as $value => $label)
                                <option value="{{ $value }}" {{ old('mail_scheme', $settings['mail_scheme']) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-400 ml-1">Untuk Gmail port 587 gunakan scheme smtp.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">SMTP Host</label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}"
                            placeholder="smtp.gmail.com"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Port</label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}"
                            placeholder="587"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Username Email</label>
                        <input type="email" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}"
                            placeholder="nama@gmail.com"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Password / App Password</label>
                        <div class="relative">
                            <input type="password" name="mail_password" id="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}"
                                placeholder="Google app password"
                                class="w-full px-4 py-3 pr-12 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            <button type="button" id="toggleMailPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-brand-primary transition-colors">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400 ml-1">Gmail membutuhkan App Password, bukan password login biasa.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">From Address</label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}"
                            placeholder="noreply@domain.com"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>

                    <div class="space-y-1.5">
                        <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">From Name</label>
                        <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}"
                            placeholder="FURE"
                            class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    </div>
                </div>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="px-8 py-3 bg-brand-primary text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all">
                    Simpan Pengaturan
                </button>
            </div>
        </form>

        <form action="{{ route('settings.test-email') }}" method="POST" class="mt-8 bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
            @csrf
            <div class="flex flex-col md:flex-row md:items-end gap-5">
                <div class="flex-1 space-y-1.5">
                    <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Kirim Test Email</label>
                    <input type="email" name="test_email" value="{{ old('test_email', $testEmailDefault) }}"
                        class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                    <p class="text-[11px] text-gray-400 ml-1">Simpan pengaturan terlebih dahulu, lalu kirim test email ke alamat tujuan.</p>
                </div>
                <button type="submit"
                    class="px-6 py-3 bg-brand-dark text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-brand-dark/10 hover:bg-brand-primary transition-all">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Test Kirim
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(function () {
            // Toggle visibility untuk semua secret/key field
            $('.toggle-secret').on('click', function () {
                const $input = $('#' + $(this).data('target'));
                $input.attr('type', $input.attr('type') === 'password' ? 'text' : 'password');
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            $('#toggleMailPassword').on('click', function () {
                const $input = $('#mail_password');
                $input.attr('type', $input.attr('type') === 'password' ? 'text' : 'password');
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            let originAreaTimer;
            let originMap;
            let originMarker;
            const defaultOriginLat = -6.2088;
            const defaultOriginLng = 106.8456;

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function (char) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;',
                    }[char];
                });
            }

            function currentOriginCoords() {
                const lat = parseFloat($('#biteship_origin_latitude').val());
                const lng = parseFloat($('#biteship_origin_longitude').val());

                return {
                    lat: Number.isFinite(lat) ? lat : defaultOriginLat,
                    lng: Number.isFinite(lng) ? lng : defaultOriginLng,
                };
            }

            function setOriginCoords(lat, lng, focusMap = true) {
                const latValue = parseFloat(lat);
                const lngValue = parseFloat(lng);

                if (!Number.isFinite(latValue) || !Number.isFinite(lngValue)) {
                    return;
                }

                const latFixed = latValue.toFixed(7);
                const lngFixed = lngValue.toFixed(7);

                $('#biteship_origin_latitude').val(latFixed);
                $('#biteship_origin_longitude').val(lngFixed);

                if (originMap && originMarker) {
                    originMarker.setLatLng([latValue, lngValue]);

                    if (focusMap) {
                        originMap.setView([latValue, lngValue], 16);
                    }
                }
            }

            function initOriginMap() {
                if (originMap || !document.getElementById('origin_map')) {
                    return;
                }

                const coords = currentOriginCoords();

                originMap = L.map('origin_map').setView([coords.lat, coords.lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                }).addTo(originMap);

                originMarker = L.marker([coords.lat, coords.lng], { draggable: true }).addTo(originMap);

                originMarker.on('dragend', function () {
                    const position = originMarker.getLatLng();
                    setOriginCoords(position.lat, position.lng, false);
                });

                originMap.on('click', function (event) {
                    setOriginCoords(event.latlng.lat, event.latlng.lng, false);
                });

                setTimeout(function () {
                    originMap.invalidateSize();
                }, 250);
            }

            initOriginMap();

            $('#biteship_origin_latitude, #biteship_origin_longitude').on('change', function () {
                const coords = currentOriginCoords();
                setOriginCoords(coords.lat, coords.lng);
            });

            $('#btn-origin-my-location').on('click', function () {
                if (!navigator.geolocation) {
                    return;
                }

                const $btn = $(this).prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin"></i> Mencari...');

                navigator.geolocation.getCurrentPosition(function (position) {
                    setOriginCoords(position.coords.latitude, position.coords.longitude);
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-location-crosshairs"></i> Gunakan Lokasi Saya');
                }, function () {
                    $btn.prop('disabled', false).html('<i class="fa-solid fa-location-crosshairs"></i> Gunakan Lokasi Saya');
                    alert('Tidak bisa mendapatkan lokasi perangkat.');
                });
            });

            $('#biteship_origin_area_search').on('input', function () {
                const query = $(this).val().trim();
                clearTimeout(originAreaTimer);
                $('#biteship_origin_area_id').val('');
                $('#biteship_origin_area_label').val('');
                $('#biteship_origin_area_status').addClass('hidden');

                if (query.length < 3) {
                    $('#biteship_origin_area_results').addClass('hidden').empty();
                    return;
                }

                originAreaTimer = setTimeout(function () {
                    $('#biteship_origin_area_results').removeClass('hidden').html(`
                        <div class="px-4 py-3 text-xs text-gray-400 flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i> Mencari area...
                        </div>
                    `);

                    $.ajax({
                        url: "{{ route('settings.biteship.areas') }}",
                        method: 'GET',
                        data: {
                            search: query,
                            api_key: $('input[name="biteship_api_key"]').val(),
                        },
                        success: function (areas) {
                            const $list = $('#biteship_origin_area_results').empty();

                            if (!areas?.length) {
                                $list.html('<div class="px-4 py-3 text-xs text-gray-400">Area tidak ditemukan.</div>');
                                return;
                            }

                            areas.forEach(function (area) {
                                $list.append(`
                                    <button type="button"
                                        class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors"
                                        data-id="${escapeHtml(area.id)}"
                                        data-label="${escapeHtml(area.label)}"
                                        data-postal-code="${escapeHtml(area.postal_code)}"
                                        data-latitude="${escapeHtml(area.latitude)}"
                                        data-longitude="${escapeHtml(area.longitude)}">
                                        <span class="block text-sm font-bold text-brand-dark">${escapeHtml(area.label || '-')}</span>
                                        <span class="block text-[10px] text-gray-400 mt-0.5">${area.postal_code ? 'Kode pos ' + escapeHtml(area.postal_code) : 'Pilih area ini sebagai origin'}</span>
                                    </button>
                                `);
                            });
                        },
                        error: function (xhr) {
                            $('#biteship_origin_area_results').html(`<div class="px-4 py-3 text-xs text-red-500">${xhr.responseJSON?.message || 'Gagal mencari area.'}</div>`);
                        },
                    });
                }, 400);
            });

            $(document).on('click', '#biteship_origin_area_results button[data-id]', function () {
                const $el = $(this);

                $('#biteship_origin_area_id').val($el.data('id'));
                $('#biteship_origin_area_label').val($el.data('label'));
                $('#biteship_origin_area_search').val($el.data('label'));
                $('#biteship_origin_area_status')
                    .removeClass('hidden')
                    .find('span')
                    .text('Area origin sudah dipilih');

                if ($el.data('postal-code')) {
                    $('input[name="biteship_origin_postal_code"]').val($el.data('postal-code'));
                }

                if ($el.data('latitude')) {
                    $('input[name="biteship_origin_latitude"]').val($el.data('latitude'));
                }

                if ($el.data('longitude')) {
                    $('input[name="biteship_origin_longitude"]').val($el.data('longitude'));
                }

                if ($el.data('latitude') && $el.data('longitude')) {
                    setOriginCoords($el.data('latitude'), $el.data('longitude'));
                }

                $('#biteship_origin_area_results').addClass('hidden').empty();
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('#biteship_origin_area_search, #biteship_origin_area_results').length) {
                    $('#biteship_origin_area_results').addClass('hidden');
                }
            });
        });
    </script>
@endpush
