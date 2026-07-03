<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') — @yield('title')</title>
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"></noscript>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <style>
        @font-face {
            font-family: 'Glamour Absolute';
            src: url('{{ asset('fonts/GlamourAbsolute_Regular.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-[#FAF8F5] flex flex-col items-center justify-center px-6 py-16">

    {{-- Brand mark --}}
    <a href="/" class="mb-10 block">
        <span style="font-family:'Glamour Absolute', serif;" class="text-2xl tracking-widest text-brand-dark">
            {{ config('app.name') }}
        </span>
    </a>

    {{-- Card --}}
    <div class="w-full max-w-md text-center">

        {{-- Big code --}}
        <p style="font-family:'Glamour Absolute', serif;"
            class="text-[100px] leading-none font-normal text-brand-primary/20 select-none mb-2">
            @yield('code')
        </p>

        {{-- Icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 rounded-2xl bg-brand-primary/10 flex items-center justify-center">
                @yield('icon')
            </div>
        </div>

        {{-- Message --}}
        <h1 class="text-xl font-bold text-brand-dark mb-3">@yield('title')</h1>
        <p class="text-sm text-brand-dark/50 leading-relaxed mb-8">@yield('description')</p>

        {{-- Actions --}}
        @yield('actions')

    </div>

    {{-- Footer --}}
    <p class="mt-16 text-[11px] text-brand-dark/30">
        &copy; {{ date('Y') }} {{ config('app.name') }}
    </p>

</body>
</html>
