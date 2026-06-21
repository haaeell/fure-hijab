@extends('layouts.customer')

@section('title', 'Daftar Akun')

@section('styles')
    <style>
        .auth-field:focus-within {
            border-color: #A78B6F;
            box-shadow: 0 0 0 4px rgba(167, 139, 111, .12);
        }
    </style>
@endsection

@section('content')
    <section class="bg-[#f8f3ee] text-brand-dark">
        <div class="mx-auto grid lg:min-h-[calc(100vh-7.25rem)] max-w-7xl grid-cols-1 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="relative min-h-[360px] overflow-hidden bg-brand-dark lg:min-h-[calc(100vh-7.25rem)]">
                <img src="/login-bg.png"
                    alt="Koleksi modest FURE"
                    class="absolute inset-0 h-full w-full object-cover opacity-75">
                <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/90 via-brand-dark/35 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-6 text-white sm:p-10 lg:p-12">
                    <p class="mb-4 inline-flex border border-white/40 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.28em]">
                        Create Account
                    </p>
                    <h1 class="max-w-lg text-4xl font-semibold leading-tight sm:text-5xl">
                        Mulai pengalaman belanja yang lebih personal.
                    </h1>
                    <p class="mt-5 max-w-md text-sm leading-7 text-white/75">
                        Simpan alamat, cek riwayat pesanan, dan akses promo koleksi terbaru dari satu akun FURE.
                    </p>
                </div>
            </div>

            <div class="flex items-center px-4 py-10 sm:px-6 lg:px-12">
                <div class="mx-auto w-full max-w-2xl">
                    <div class="mb-8">
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Daftar akun</p>
                        <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Buat akun baru</h2>
                        <p class="mt-3 text-sm leading-6 text-brand-dark/60">
                            Isi data berikut untuk belanja lebih cepat di web dan mobile FURE.
                        </p>
                    </div>

                    <div id="alertContainer"></div>

                    <form id="ajaxRegisterForm" class="space-y-5">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-dark/55">Nama lengkap</label>
                                <div class="auth-field mt-2 flex items-center gap-3 border border-brand-secondary/70 bg-white px-4 transition-all">
                                    <i class="fa-regular fa-user text-brand-primary/70"></i>
                                    <input type="text" name="name" required autocomplete="name"
                                        class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                        placeholder="Nama kamu">
                                </div>
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-dark/55">Nomor WhatsApp</label>
                                <div class="auth-field mt-2 flex items-center gap-3 border border-brand-secondary/70 bg-white px-4 transition-all">
                                    <i class="fa-brands fa-whatsapp text-brand-primary/70"></i>
                                    <input type="tel" name="phone" required autocomplete="tel"
                                        class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                        placeholder="0812...">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-dark/55">Email</label>
                            <div class="auth-field mt-2 flex items-center gap-3 border border-brand-secondary/70 bg-white px-4 transition-all">
                                <i class="fa-regular fa-envelope text-brand-primary/70"></i>
                                <input type="email" name="email" required autocomplete="email"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="nama@email.com">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-dark/55">Password</label>
                                <div class="auth-field mt-2 flex items-center gap-3 border border-brand-secondary/70 bg-white px-4 transition-all">
                                    <i class="fa-solid fa-lock text-brand-primary/70"></i>
                                    <input type="password" id="password" name="password" required autocomplete="new-password"
                                        class="w-full min-w-0 bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                        placeholder="Minimal 8 karakter">
                                    <button type="button" class="toggle-password shrink-0 text-brand-dark/35 transition hover:text-brand-primary"
                                        data-target="#password" aria-label="Lihat password">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <div class="mt-2 h-1.5 overflow-hidden bg-brand-secondary/35">
                                    <div id="strengthBar" class="h-full w-0 bg-red-400 transition-all"></div>
                                </div>
                                <p id="strengthText" class="mt-1 text-[10px] font-bold text-brand-dark/45">Kekuatan password</p>
                            </div>
                            <div>
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-dark/55">Konfirmasi password</label>
                                <div class="auth-field mt-2 flex items-center gap-3 border border-brand-secondary/70 bg-white px-4 transition-all">
                                    <i class="fa-solid fa-lock text-brand-primary/70"></i>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                        class="w-full min-w-0 bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                        placeholder="Ulangi password">
                                    <button type="button" class="toggle-password shrink-0 text-brand-dark/35 transition hover:text-brand-primary"
                                        data-target="#password_confirmation" aria-label="Lihat password">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                <p id="matchText" class="mt-2 text-[10px] font-bold text-brand-dark/45">Konfirmasi harus sama.</p>
                            </div>
                        </div>

                        <label class="flex items-start gap-3 text-xs leading-relaxed text-brand-dark/65">
                            <input type="checkbox" name="terms" value="1" required
                                class="mt-0.5 h-4 w-4 shrink-0 border-brand-secondary text-brand-primary focus:ring-brand-primary">
                            <span>
                                Saya menyetujui
                                <a href="{{ route('terms.index') }}" target="_blank"
                                    class="font-bold text-brand-primary transition hover:text-brand-dark">Syarat & Ketentuan</a>
                                serta penggunaan data untuk akun, transaksi, dan pengiriman pesanan.
                            </span>
                        </label>

                        <button type="submit" id="regBtn"
                            class="flex w-full items-center justify-center gap-3 bg-brand-primary px-6 py-4 text-sm font-black uppercase tracking-[0.16em] text-white shadow-lg shadow-brand-primary/20 transition hover:bg-brand-dark active:scale-[0.99]">
                            <span id="regText">Daftar</span>
                            <i id="regIcon" class="fa-solid fa-arrow-right-long"></i>
                            <i id="regLoader" class="fa-solid fa-circle-notch fa-spin hidden"></i>
                        </button>

                        <p class="text-center text-sm text-brand-dark/60">
                            Sudah punya akun?
                            <a href="{{ route('login') }}"
                                class="font-bold text-brand-primary transition hover:text-brand-dark">Masuk</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('.toggle-password').on('click', function () {
                const target = $($(this).data('target'));
                const nextType = target.attr('type') === 'password' ? 'text' : 'password';
                target.attr('type', nextType);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            $('#password').on('input', function () {
                const value = $(this).val();
                let score = 0;
                if (value.length >= 8) score++;
                if (/[A-Z]/.test(value)) score++;
                if (/[0-9]/.test(value)) score++;
                if (/[^A-Za-z0-9]/.test(value)) score++;

                const states = [
                    ['w-0', 'bg-red-400', 'Kekuatan password'],
                    ['w-1/4', 'bg-red-400', 'Lemah'],
                    ['w-2/4', 'bg-amber-400', 'Cukup'],
                    ['w-3/4', 'bg-blue-400', 'Baik'],
                    ['w-full', 'bg-brand-primary', 'Kuat'],
                ];
                $('#strengthBar').removeClass('w-0 w-1/4 w-2/4 w-3/4 w-full bg-red-400 bg-amber-400 bg-blue-400 bg-brand-primary')
                    .addClass(states[score][0] + ' ' + states[score][1]);
                $('#strengthText').text(states[score][2]);
                updateMatch();
            });

            $('#password_confirmation').on('input', updateMatch);

            function updateMatch() {
                const password = $('#password').val();
                const confirmation = $('#password_confirmation').val();
                if (!confirmation) {
                    $('#matchText').removeClass('text-green-600 text-red-500').addClass('text-brand-dark/45').text('Konfirmasi harus sama.');
                    return;
                }

                const same = password === confirmation;
                $('#matchText').toggleClass('text-green-600', same).toggleClass('text-red-500', !same)
                    .removeClass('text-brand-dark/45')
                    .text(same ? 'Password cocok.' : 'Password belum sama.');
            }

            function showErrors(errors, fallback) {
                let items = fallback ? `<li>${fallback}</li>` : '';
                if (errors) {
                    $.each(errors, function (_, value) {
                        items += `<li>${value[0]}</li>`;
                    });
                }

                $('#alertContainer').html(`
                    <div class="mb-5 border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                            <ul class="list-disc list-inside space-y-1">${items}</ul>
                        </div>
                    </div>
                `);
            }

            $('#ajaxRegisterForm').on('submit', function (e) {
                e.preventDefault();
                const btn = $('#regBtn');

                $.ajax({
                    url: "{{ route('register') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    beforeSend: function () {
                        btn.prop('disabled', true).addClass('opacity-80');
                        $('#regLoader').removeClass('hidden');
                        $('#regIcon').addClass('hidden');
                        $('#regText').text('Mendaftarkan');
                        $('#alertContainer').empty();
                    },
                    success: function (response) {
                        window.location.href = response.redirect || '/';
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).removeClass('opacity-80');
                        $('#regLoader').addClass('hidden');
                        $('#regIcon').removeClass('hidden');
                        $('#regText').text('Daftar');
                        showErrors(xhr.responseJSON?.errors, xhr.responseJSON?.message || 'Pendaftaran gagal.');
                    }
                });
            });
        });
    </script>
@endpush
