@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-8">
            <h1 class="text-xl md:text-2xl font-extrabold text-brand-dark tracking-tight">Pengaturan Profil</h1>
            <p class="text-xs md:text-sm text-gray-400 font-medium mt-1">Kelola informasi akun dan keamanan Anda.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8 text-center">
                    <div class="relative inline-block mb-6">
                        <div
                            class="w-32 h-32 rounded-[2.5rem] bg-brand-primary/10 flex items-center justify-center overflow-hidden border-4 border-white shadow-xl">
                            @if($profileUser->avatar)
                                <img src="{{ asset('storage/' . $profileUser->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-user-tie text-5xl text-brand-primary"></i>
                            @endif
                        </div>
                        <label for="avatar-upload"
                            class="absolute -bottom-2 -right-2 w-10 h-10 bg-brand-primary text-white rounded-2xl flex items-center justify-center shadow-lg cursor-pointer hover:bg-brand-dark transition-all">
                            <i class="fa-solid fa-camera text-sm"></i>
                        </label>
                    </div>

                    <h2 class="text-xl font-extrabold text-brand-dark">{{ $profileUser->name }}</h2>
                    <p class="text-xs font-black text-brand-primary uppercase tracking-widest mt-1">
                        {{ $profileUser->role }}
                    </p>

                    <div class="mt-8 pt-8 border-t border-gray-50 flex flex-col gap-3">
                        <div class="flex items-center gap-3 text-left p-3 rounded-2xl bg-gray-50/50">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                            <span class="text-xs font-bold text-gray-600">{{ $profileUser->email }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-left p-3 rounded-2xl bg-gray-50/50">
                            <i class="fa-solid fa-phone text-gray-400"></i>
                            <span
                                class="text-xs font-bold text-gray-600">{{ $profileUser->phone ?? 'Belum diatur' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-brand-dark">Informasi Pribadi</h3>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="file" id="avatar-upload" name="avatar" class="hidden" onchange="this.form.submit()">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama
                                    Lengkap</label>
                                <input type="text" name="name" value="{{ $profileUser->name }}"
                                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</label>
                                <input type="email" name="email" value="{{ $profileUser->email }}"
                                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            </div>
                            <div class="md:col-span-2 space-y-1.5 text-right">
                                <button type="submit"
                                    class="px-8 py-3 bg-brand-primary text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-brand-primary/20 hover:bg-brand-dark transition-all">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-[32px] shadow-sm border border-gray-50 p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-brand-dark">Keamanan Akun</h3>
                    </div>

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2 space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Password
                                    Saat Ini</label>
                                <input type="password" name="current_password"
                                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            </div>
                            <div class="space-y-1.5">
                                <label class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Password
                                    Baru</label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="ml-1 text-[10px] font-black text-gray-400 uppercase tracking-widest">Konfirmasi
                                    Password Baru</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full px-4 py-3 bg-gray-50/50 border border-gray-200 rounded-2xl focus:border-brand-primary outline-none transition-all text-sm font-semibold">
                            </div>
                            <div class="md:col-span-2 space-y-1.5 text-right">
                                <button type="submit"
                                    class="px-8 py-3 bg-brand-dark text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg hover:bg-black transition-all">
                                    Perbarui Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
