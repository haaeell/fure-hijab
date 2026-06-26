@extends('layouts.customer')

@section('title', 'Masuk')

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
        <div class="mx-auto grid lg:min-h-[calc(100vh-7.25rem)] max-w-7xl grid-cols-1 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="relative order-2 min-h-[420px] overflow-hidden bg-brand-dark lg:order-1 lg:min-h-[calc(100vh-7.25rem)]">
                <img src="/login-bg.png"
                    alt="Koleksi hijab {{ $globalStoreName }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-80">
                <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/90 via-brand-dark/35 to-transparent lg:bg-gradient-to-r lg:from-brand-dark/85 lg:via-brand-dark/35 lg:to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-6 text-white sm:p-10 lg:p-12">
                    <p class="mb-4 inline-flex border border-white/40 px-4 py-2 text-[10px] font-bold uppercase tracking-[0.28em]">
                        Member Access
                    </p>
                    <h1 class="max-w-xl text-4xl font-semibold leading-tight sm:text-5xl">
                        Masuk untuk lanjut belanja koleksi hijab favorit.
                    </h1>
                    <div class="mt-8 grid max-w-2xl grid-cols-3 gap-2 sm:gap-3">
                        <div class="border border-white/20 bg-white/10 p-3 backdrop-blur-sm sm:p-4">
                            <i class="fa-solid fa-shield-heart text-brand-secondary"></i>
                            <p class="mt-3 text-[11px] font-bold leading-snug sm:text-xs">Checkout aman</p>
                        </div>
                        <div class="border border-white/20 bg-white/10 p-3 backdrop-blur-sm sm:p-4">
                            <i class="fa-solid fa-truck-fast text-brand-secondary"></i>
                            <p class="mt-3 text-[11px] font-bold leading-snug sm:text-xs">Lacak pesanan</p>
                        </div>
                        <div class="border border-white/20 bg-white/10 p-3 backdrop-blur-sm sm:p-4">
                            <i class="fa-solid fa-ticket text-brand-secondary"></i>
                            <p class="mt-3 text-[11px] font-bold leading-snug sm:text-xs">Voucher aktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-1 flex items-center px-4 py-10 sm:px-6 lg:order-2 lg:px-12">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8">
                        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-brand-primary">Masuk akun</p>
                        <h2 class="mt-3 text-3xl font-semibold leading-tight sm:text-4xl">Selamat datang kembali</h2>
                        <p class="mt-3 text-sm leading-6 text-brand-dark/60">
                            Gunakan email dan password yang sudah terdaftar untuk mengakses pesanan, alamat, dan promo.
                        </p>
                    </div>

                    <div id="alertContainer"></div>

                    <form id="ajaxLoginForm" class="space-y-5">
                        <div>
                            <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-dark/55">Email</label>
                            <div class="auth-field mt-2 flex items-center gap-3 border border-brand-secondary/70 bg-white px-4 transition-all">
                                <i class="fa-regular fa-envelope text-brand-primary/70"></i>
                                <input type="email" name="email" required autocomplete="email"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="nama@email.com">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <label class="text-[11px] font-bold uppercase tracking-[0.2em] text-brand-dark/55">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                        class="text-[11px] font-bold text-brand-primary transition hover:text-brand-dark">
                                        Lupa password?
                                    </a>
                                @endif
                            </div>
                            <div class="auth-field mt-2 flex items-center gap-3 border border-brand-secondary/70 bg-white px-4 transition-all">
                                <i class="fa-solid fa-lock text-brand-primary/70"></i>
                                <input type="password" id="password" name="password" required autocomplete="current-password"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="Masukkan password">
                                <button type="button" class="toggle-password text-brand-dark/35 transition hover:text-brand-primary"
                                    data-target="#password" aria-label="Lihat password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <label class="flex items-center gap-3 text-xs font-semibold text-brand-dark/65">
                            <input type="checkbox" name="remember"
                                class="h-4 w-4 border-brand-secondary text-brand-primary focus:ring-brand-primary">
                            Ingat saya di perangkat ini
                        </label>

                        <button type="submit" id="loginBtn"
                            class="flex w-full items-center justify-center gap-3 bg-brand-primary px-6 py-4 text-sm font-black uppercase tracking-[0.16em] text-white shadow-lg shadow-brand-primary/20 transition hover:bg-brand-dark active:scale-[0.99]">
                            <span id="btnText">Masuk</span>
                            <i id="btnIcon" class="fa-solid fa-arrow-right-long"></i>
                            <i id="btnLoader" class="fa-solid fa-circle-notch fa-spin" style="display:none"></i>
                        </button>

                        <p class="text-center text-sm text-brand-dark/60">
                            Belum punya akun?
                            <a href="{{ route('register') }}"
                                class="font-bold text-brand-primary transition hover:text-brand-dark">Daftar sekarang</a>
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
            // Simpan halaman sebelumnya untuk redirect setelah login
            const referrer = document.referrer;
            const sameOrigin = referrer && referrer.startsWith(window.location.origin);
            const notAuthPage = sameOrigin && !referrer.includes('/login') && !referrer.includes('/register');

            $('.toggle-password').on('click', function () {
                const target = $($(this).data('target'));
                const nextType = target.attr('type') === 'password' ? 'text' : 'password';
                target.attr('type', nextType);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            function showAlert(message) {
                $('#alertContainer').html(`
                    <div class="mb-5 border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                            <span>${message}</span>
                        </div>
                    </div>
                `);
            }

            window.addEventListener('pageshow', function (e) {
                if (e.persisted) {
                    $('#loginBtn').prop('disabled', false).removeClass('opacity-80');
                    $('#btnLoader').hide();
                    $('#btnIcon').show();
                    $('#btnText').text('Masuk');
                }
            });

            $('#ajaxLoginForm').on('submit', function (e) {
                e.preventDefault();
                const btn = $('#loginBtn');
                const extraData = notAuthPage ? '&_referrer=' + encodeURIComponent(referrer) : '';

                $.ajax({
                    url: "{{ route('login') }}",
                    method: "POST",
                    data: $(this).serialize() + extraData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    beforeSend: function () {
                        btn.prop('disabled', true).addClass('opacity-80');
                        $('#btnLoader').show();
                        $('#btnIcon').hide();
                        $('#btnText').text('Memverifikasi...');
                        $('#alertContainer').empty();
                    },
                    success: function (response) {
                        window.location.href = response.redirect || '/';
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).removeClass('opacity-80');
                        $('#btnLoader').hide();
                        $('#btnIcon').show();
                        $('#btnText').text('Masuk');
                        const status = xhr.status;
                        let msg = xhr.responseJSON?.message || 'Terjadi kesalahan. Coba lagi.';
                        if (status === 422 && xhr.responseJSON?.errors) {
                            const errs = xhr.responseJSON.errors;
                            msg = Object.values(errs).map(e => e[0]).join('<br>');
                        }
                        showAlert(msg);
                    }
                });
            });
        });
    </script>
@endpush
