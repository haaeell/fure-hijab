<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Reset Password - FURE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
                        'brand-primary': '#A78B6F',
                        'brand-secondary': '#D6C4B0',
                        'brand-dark': '#5F4A3A',
                    },
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .auth-bg {
            background:
                linear-gradient(135deg, rgba(248,251,248,.96), rgba(255,255,255,.92)),
                radial-gradient(circle at 20% 18%, rgba(167,139,111,.24), transparent 28%),
                radial-gradient(circle at 86% 78%, rgba(95,74,58,.13), transparent 24%);
        }
        .field:focus-within {
            border-color: #A78B6F;
            box-shadow: 0 0 0 4px rgba(167,139,111,.12);
        }
    </style>
</head>

<body class="min-h-screen auth-bg text-gray-900">
    <main class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <a href="/" class="mb-8 flex items-center justify-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-brand-primary flex items-center justify-center text-white shadow-lg shadow-brand-primary/30">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <span class="text-brand-dark font-extrabold text-xl tracking-tight uppercase">FURE</span>
            </a>

            <div class="bg-white rounded-[32px] border border-white shadow-2xl shadow-brand-dark/10 p-6 sm:p-8">
                <div class="text-center mb-7">
                    <div class="w-14 h-14 bg-soft-mint rounded-2xl flex items-center justify-center text-brand-primary mx-auto mb-4">
                        <i class="fa-solid fa-lock-open text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-brand-dark">Buat Password Baru</h1>
                    <p class="text-sm text-gray-400 mt-2">Gunakan password yang kuat dan mudah Anda ingat.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Email</label>
                        <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                            <i class="fa-regular fa-envelope text-gray-300"></i>
                            <input type="email" name="email" required autocomplete="email" value="{{ $email ?? old('email') }}"
                                class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Password Baru</label>
                        <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                            <i class="fa-solid fa-lock text-gray-300"></i>
                            <input type="password" id="password" name="password" required autocomplete="new-password"
                                class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                placeholder="Minimal 8 karakter">
                            <button type="button" class="toggle-password text-gray-300 hover:text-brand-primary transition-colors" data-target="#password" aria-label="Lihat password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Konfirmasi Password</label>
                        <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                            <i class="fa-solid fa-lock text-gray-300"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                placeholder="Ulangi password baru">
                            <button type="button" class="toggle-password text-gray-300 hover:text-brand-primary transition-colors" data-target="#password_confirmation" aria-label="Lihat password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 rounded-2xl bg-brand-primary text-white font-black shadow-lg shadow-brand-primary/30 hover:bg-brand-dark hover:-translate-y-0.5 transition-all active:scale-[0.98]">
                        Simpan Password Baru
                    </button>

                    <p class="text-center text-sm text-gray-500">
                        Sudah ingat?
                        <a href="{{ route('login') }}" class="font-bold text-brand-primary hover:text-brand-dark transition-colors">Masuk</a>
                    </p>
                </form>
            </div>
        </div>
    </main>

    <script>
        $(function () {
            $('.toggle-password').on('click', function () {
                const target = $($(this).data('target'));
                const nextType = target.attr('type') === 'password' ? 'text' : 'password';
                target.attr('type', nextType);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });
        });
    </script>
</body>

</html>
