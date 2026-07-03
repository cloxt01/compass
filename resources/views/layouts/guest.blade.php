<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Compass') }}</title>

    {{-- Font & Icons dari Geist --}}
    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-all.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Geist', system-ui, sans-serif;
            background: #0A0A0A;
            color: #FAFAFA;
        }
        .font-mono {
            font-family: 'Geist Mono', monospace !important;
        }
        .saas-card {
            background: #111111;
            border: 1px solid #262626;
            border-radius: 16px;
            box-shadow: 0 2px 16px rgba(0,0,0,.28);
            transition: all .2s ease;
        }
        .saas-card:hover {
            border-color: #333333;
        }
        .saas-input {
            border: 1px solid #262626;
            background: #0A0A0A;
            outline: 0;
            transition: .2s ease;
        }
        .saas-input:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 2px rgba(59,130,246,.15);
        }
    </style>
</head>
{{-- Menggunakan min-h-screen dan flex-col agar layout bisa membagi tinggi layar dengan pas --}}
<body class="bg-[#0A0A0A] text-[#FAFAFA] antialiased min-h-screen flex flex-col">

{{-- flex-grow memastikan container ini mengambil sisa ruang kosong yang ada, mendorong footer ke bawah --}}
<div class="mx-auto w-full max-w-[1400px] px-4 pb-12 pt-4 flex-grow">
    <main class="transition-all duration-300">
        {{ $slot }}
    </main>
</div>

{{-- FOOTER GLOBAL GUEST --}}
<footer class="border-t border-[#262626] bg-[#0A0A0A] w-full">
    <div class="mx-auto max-w-[1400px] px-4 py-6 text-center text-xs text-[#a1a1aa]">
        © Compass 2026
    </div>
</footer>

</body>
</html>
