<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Masuk - AL-HAYYA HIJAB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'soft-mint': '#F1F8E9',
                        'soft-blue': '#E3F2FD',
                        'brand-primary': '#81C784',
                        'brand-secondary': '#A5D6A7',
                        'brand-dark': '#2D5A27',
                    },
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .auth-bg {
            background:
                linear-gradient(135deg, rgba(241, 248, 233, .9), rgba(255, 255, 255, .92)),
                radial-gradient(circle at 12% 20%, rgba(129, 199, 132, .22), transparent 28%),
                radial-gradient(circle at 88% 76%, rgba(45, 90, 39, .14), transparent 26%);
        }

        .field:focus-within {
            border-color: #81C784;
            box-shadow: 0 0 0 4px rgba(129, 199, 132, .12);
        }
    </style>
</head>

<body class="min-h-screen auth-bg text-gray-900">
    <main class="min-h-screen grid lg:grid-cols-[1fr_520px]">
        <section class="hidden lg:flex relative overflow-hidden px-12 py-10 flex-col justify-between">
            <a href="/" class="inline-flex items-center gap-3 w-fit">
                <div class="w-12 h-12 rounded-2xl bg-brand-primary flex items-center justify-center text-white shadow-lg shadow-brand-primary/30">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <span class="text-brand-dark font-extrabold text-2xl tracking-tight uppercase">Al-Hayya</span>
            </a>

            <div class="max-w-xl">
                <p class="text-[11px] font-black text-brand-primary uppercase tracking-[0.35em] mb-4">Member access</p>
                <h1 class="text-5xl font-extrabold text-brand-dark leading-tight">
                    Masuk untuk belanja hijab favorit dengan lebih cepat.
                </h1>
                <p class="mt-5 text-gray-500 leading-relaxed">
                    Simpan alamat, pantau pesanan, gunakan voucher, dan lanjutkan pembayaran dari satu akun yang aman.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3 max-w-xl">
                <div class="bg-white/80 border border-white rounded-2xl p-4 shadow-sm">
                    <i class="fa-solid fa-shield-heart text-brand-primary mb-3"></i>
                    <p class="text-xs font-bold text-brand-dark">Checkout aman</p>
                </div>
                <div class="bg-white/80 border border-white rounded-2xl p-4 shadow-sm">
                    <i class="fa-solid fa-truck-fast text-brand-primary mb-3"></i>
                    <p class="text-xs font-bold text-brand-dark">Lacak pesanan</p>
                </div>
                <div class="bg-white/80 border border-white rounded-2xl p-4 shadow-sm">
                    <i class="fa-solid fa-ticket text-brand-primary mb-3"></i>
                    <p class="text-xs font-bold text-brand-dark">Voucher aktif</p>
                </div>
            </div>
        </section>

        <section class="min-h-screen flex items-center justify-center px-4 py-8 sm:px-6">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-8 flex items-center justify-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-brand-primary flex items-center justify-center text-white shadow-lg shadow-brand-primary/30">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <span class="text-brand-dark font-extrabold text-xl tracking-tight uppercase">Al-Hayya</span>
                </div>

                <div class="bg-white/95 border border-white rounded-[32px] shadow-2xl shadow-brand-dark/10 p-6 sm:p-8">
                    <div class="mb-7">
                        <p class="text-[10px] font-black text-brand-primary uppercase tracking-[0.3em]">Masuk akun</p>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-dark mt-2">Selamat datang kembali</h2>
                        <p class="text-sm text-gray-400 mt-2">Gunakan email dan password yang terdaftar.</p>
                    </div>

                    <div id="alertContainer"></div>

                    <form id="ajaxLoginForm" class="space-y-5">
                        <div>
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Email</label>
                            <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                                <i class="fa-regular fa-envelope text-gray-300"></i>
                                <input type="email" name="email" required autocomplete="email"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="nama@email.com">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-brand-primary hover:text-brand-dark transition-colors">
                                        Lupa password?
                                    </a>
                                @endif
                            </div>
                            <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                                <i class="fa-solid fa-lock text-gray-300"></i>
                                <input type="password" id="password" name="password" required autocomplete="current-password"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="Masukkan password">
                                <button type="button" class="toggle-password text-gray-300 hover:text-brand-primary transition-colors" data-target="#password" aria-label="Lihat password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <label class="flex items-center gap-3 text-xs font-semibold text-gray-500">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-200 text-brand-primary focus:ring-brand-primary">
                            Ingat saya di perangkat ini
                        </label>

                        <button type="submit" id="loginBtn"
                            class="w-full py-4 rounded-2xl bg-brand-primary text-white font-black tracking-wide shadow-lg shadow-brand-primary/30 hover:bg-brand-dark hover:-translate-y-0.5 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                            <span id="btnText">Masuk Sekarang</span>
                            <i id="btnIcon" class="fa-solid fa-arrow-right-long"></i>
                            <i id="btnLoader" class="fa-solid fa-circle-notch fa-spin hidden"></i>
                        </button>

                        <p class="text-center text-sm text-gray-500">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="font-bold text-brand-primary hover:text-brand-dark transition-colors">Daftar sekarang</a>
                        </p>
                    </form>
                </div>
            </div>
        </section>
    </main>

    @include('partials.customer-bottom-navigation')

    <script>
        $(function () {
            $('.toggle-password').on('click', function () {
                const target = $($(this).data('target'));
                const nextType = target.attr('type') === 'password' ? 'text' : 'password';
                target.attr('type', nextType);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            function showAlert(message) {
                $('#alertContainer').html(`
                    <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 flex gap-3">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <span>${message}</span>
                    </div>
                `);
            }

            $('#ajaxLoginForm').on('submit', function (e) {
                e.preventDefault();
                const btn = $('#loginBtn');

                $.ajax({
                    url: "{{ route('login') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    beforeSend: function () {
                        btn.prop('disabled', true).addClass('opacity-80');
                        $('#btnLoader').removeClass('hidden');
                        $('#btnIcon').addClass('hidden');
                        $('#btnText').text('Memverifikasi...');
                        $('#alertContainer').empty();
                    },
                    success: function (response) {
                        window.location.href = response.redirect || '/';
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).removeClass('opacity-80');
                        $('#btnLoader').addClass('hidden');
                        $('#btnIcon').removeClass('hidden');
                        $('#btnText').text('Masuk Sekarang');
                        showAlert(xhr.responseJSON?.message || 'Email atau password salah.');
                    }
                });
            });
        });
    </script>
</body>

</html>
