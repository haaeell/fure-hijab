<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Lupa Password - {{ $globalStoreName }}</title>
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
                @if($globalStoreLogo)
                    <img src="{{ asset('storage/' . $globalStoreLogo) }}" alt="{{ $globalStoreName }}" class="h-10 w-auto max-w-[140px] object-contain">
                @else
                    <span class="text-brand-dark font-extrabold text-xl tracking-tight uppercase">{{ $globalStoreName }}</span>
                @endif
            </a>

            <div class="bg-white rounded-[32px] border border-white shadow-2xl shadow-brand-dark/10 p-6 sm:p-8">
                <div class="text-center mb-7">
                    <div class="w-14 h-14 bg-soft-mint rounded-2xl flex items-center justify-center text-brand-primary mx-auto mb-4">
                        <i class="fa-solid fa-key text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-extrabold text-brand-dark">Lupa Password?</h1>
                    <p class="text-sm text-gray-400 mt-2">Masukkan email akun Anda. Kami akan mengirim link reset password.</p>
                </div>

                <div id="alertContainer">
                    @if (session('status'))
                        <div class="mb-5 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif
                    @error('email')
                        <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Email</label>
                        <div class="field mt-2 flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 transition-all">
                            <i class="fa-regular fa-envelope text-gray-300"></i>
                            <input type="email" name="email" required autocomplete="email" autofocus value="{{ old('email') }}"
                                class="w-full bg-transparent py-4 text-sm font-semibold text-brand-dark outline-none"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <button type="submit" id="forgotBtn"
                        class="w-full py-4 rounded-2xl bg-brand-primary text-white font-black shadow-lg shadow-brand-primary/30 hover:bg-brand-dark hover:-translate-y-0.5 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                        <span id="forgotText">Kirim Link Reset</span>
                        <i id="forgotIcon" class="fa-solid fa-paper-plane"></i>
                        <i id="forgotLoader" class="fa-solid fa-circle-notch fa-spin hidden"></i>
                    </button>

                    <p class="text-center text-sm text-gray-500">
                        Ingat password?
                        <a href="{{ route('login') }}" class="font-bold text-brand-primary hover:text-brand-dark transition-colors">Masuk</a>
                    </p>
                </form>
            </div>
        </div>
    </main>

    <script>
        $(function () {
            $('#forgotPasswordForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('password.email') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    beforeSend: function () {
                        $('#forgotBtn').prop('disabled', true).addClass('opacity-80');
                        $('#forgotLoader').removeClass('hidden');
                        $('#forgotIcon').addClass('hidden');
                        $('#forgotText').text('Mengirim...');
                        $('#alertContainer').empty();
                    },
                    success: function (response) {
                        $('#alertContainer').html(`
                            <div class="mb-5 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                                ${response.message || 'Link reset password sudah dikirim. Silakan cek email Anda.'}
                            </div>
                        `);
                    },
                    error: function (xhr) {
                        const message = xhr.responseJSON?.errors?.email?.[0] || xhr.responseJSON?.message || 'Gagal mengirim link reset password.';
                        $('#alertContainer').html(`
                            <div class="mb-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                                ${message}
                            </div>
                        `);
                    },
                    complete: function () {
                        $('#forgotBtn').prop('disabled', false).removeClass('opacity-80');
                        $('#forgotLoader').addClass('hidden');
                        $('#forgotIcon').removeClass('hidden');
                        $('#forgotText').text('Kirim Link Reset');
                    }
                });
            });
        });
    </script>
</body>

</html>
