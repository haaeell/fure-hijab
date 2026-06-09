<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Daftar Akun - AL-HAYYA HIJAB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

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
                linear-gradient(135deg, rgba(248, 251, 248, .96), rgba(255, 255, 255, .9)),
                radial-gradient(circle at 16% 14%, rgba(129, 199, 132, .24), transparent 28%),
                radial-gradient(circle at 92% 78%, rgba(45, 90, 39, .12), transparent 24%);
        }

        .field:focus-within {
            border-color: #81C784;
            box-shadow: 0 0 0 4px rgba(129, 199, 132, .12);
        }
    </style>
</head>

<body class="min-h-screen auth-bg text-gray-900">
    <main class="min-h-screen flex items-center justify-center px-4 py-8 sm:px-6">
        <div class="w-full max-w-6xl grid lg:grid-cols-[430px_1fr] bg-white/90 border border-white rounded-[32px] shadow-2xl shadow-brand-dark/10 overflow-hidden">
            <section class="bg-brand-dark text-white p-7 sm:p-10 flex flex-col justify-between gap-10">
                <a href="/" class="inline-flex items-center gap-3 w-fit">
                    <div class="w-12 h-12 rounded-2xl bg-brand-primary flex items-center justify-center text-white shadow-lg shadow-brand-primary/30">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight uppercase">Al-Hayya</span>
                </a>

                <div>
                    <p class="text-[10px] font-black text-brand-primary uppercase tracking-[0.32em] mb-4">Create account</p>
                    <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight">Mulai pengalaman belanja yang lebih personal.</h1>
                    <p class="text-sm text-white/60 mt-5 leading-relaxed">
                        Daftar untuk menyimpan alamat, memantau pesanan, dan mendapatkan akses voucher koleksi terbaru.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3">
                    <div class="rounded-2xl bg-white/10 border border-white/10 p-4 flex items-center gap-3">
                        <i class="fa-solid fa-location-dot text-brand-primary"></i>
                        <p class="text-xs font-bold">Alamat tersimpan otomatis</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 border border-white/10 p-4 flex items-center gap-3">
                        <i class="fa-solid fa-bag-shopping text-brand-primary"></i>
                        <p class="text-xs font-bold">Riwayat pesanan lengkap</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 border border-white/10 p-4 flex items-center gap-3">
                        <i class="fa-solid fa-shield-heart text-brand-primary"></i>
                        <p class="text-xs font-bold">Akun aman dan privat</p>
                    </div>
                </div>
            </section>

            <section class="p-6 sm:p-8 lg:p-12">
                <div class="mb-7">
                    <p class="text-[10px] font-black text-brand-primary uppercase tracking-[0.3em]">Daftar akun</p>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-dark mt-2">Buat akun baru</h2>
                    <p class="text-sm text-gray-400 mt-2">Isi data berikut untuk mulai belanja di Al-Hayya.</p>
                </div>

                <div id="alertContainer"></div>

                <form id="ajaxRegisterForm" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</label>
                            <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                                <i class="fa-regular fa-user text-gray-300"></i>
                                <input type="text" name="name" required autocomplete="name"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="Nama kamu">
                            </div>
                        </div>
                        <div>
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Nomor WhatsApp</label>
                            <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                                <i class="fa-brands fa-whatsapp text-gray-300"></i>
                                <input type="tel" name="phone" required autocomplete="tel"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="0812...">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Email</label>
                        <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                            <i class="fa-regular fa-envelope text-gray-300"></i>
                            <input type="email" name="email" required autocomplete="email"
                                class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Password</label>
                            <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                                <i class="fa-solid fa-lock text-gray-300"></i>
                                <input type="password" id="password" name="password" required autocomplete="new-password"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" class="toggle-password text-gray-300 hover:text-brand-primary transition-colors" data-target="#password" aria-label="Lihat password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div id="strengthBar" class="h-full w-0 bg-red-400 transition-all"></div>
                            </div>
                            <p id="strengthText" class="mt-1 text-[10px] font-bold text-gray-400">Kekuatan password</p>
                        </div>
                        <div>
                            <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Konfirmasi Password</label>
                            <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                                <i class="fa-solid fa-lock text-gray-300"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                    class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                    placeholder="Ulangi password">
                                <button type="button" class="toggle-password text-gray-300 hover:text-brand-primary transition-colors" data-target="#password_confirmation" aria-label="Lihat password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <p id="matchText" class="mt-2 text-[10px] font-bold text-gray-400">Konfirmasi harus sama.</p>
                        </div>
                    </div>

                    <label class="flex items-start gap-3 text-xs leading-relaxed text-gray-500">
                        <input type="checkbox" name="terms" value="1" required class="mt-0.5 w-4 h-4 rounded border-gray-200 text-brand-primary focus:ring-brand-primary">
                        <span>
                            Saya menyetujui
                            <a href="{{ route('terms.index') }}" target="_blank" class="font-bold text-brand-primary hover:text-brand-dark">Syarat & Ketentuan</a>
                            serta penggunaan data untuk kebutuhan akun, transaksi, dan pengiriman pesanan.
                        </span>
                    </label>

                    <button type="submit" id="regBtn"
                        class="w-full py-4 rounded-2xl bg-brand-primary text-white font-black shadow-lg shadow-brand-primary/30 hover:bg-brand-dark hover:-translate-y-0.5 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                        <span id="regText">Daftar Sekarang</span>
                        <i id="regIcon" class="fa-solid fa-arrow-right-long"></i>
                        <i id="regLoader" class="fa-solid fa-circle-notch fa-spin hidden"></i>
                    </button>

                    <p class="text-center text-sm text-gray-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-bold text-brand-primary hover:text-brand-dark transition-colors">Masuk</a>
                    </p>
                </form>
            </section>
        </div>
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
                    $('#matchText').removeClass('text-green-600 text-red-500').addClass('text-gray-400').text('Konfirmasi harus sama.');
                    return;
                }

                const same = password === confirmation;
                $('#matchText').toggleClass('text-green-600', same).toggleClass('text-red-500', !same)
                    .removeClass('text-gray-400')
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
                    <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        <div class="flex gap-3">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <ul class="space-y-1">${items}</ul>
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
                        $('#regText').text('Mendaftarkan...');
                        $('#alertContainer').empty();
                    },
                    success: function (response) {
                        window.location.href = response.redirect || '/';
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).removeClass('opacity-80');
                        $('#regLoader').addClass('hidden');
                        $('#regIcon').removeClass('hidden');
                        $('#regText').text('Daftar Sekarang');
                        showErrors(xhr.responseJSON?.errors, xhr.responseJSON?.message || 'Pendaftaran gagal.');
                    }
                });
            });
        });
    </script>
</body>

</html>
