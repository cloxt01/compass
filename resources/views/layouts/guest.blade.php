<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('ui.brand.name') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ui-body font-sans antialiased">
        <div class="flex min-h-screen items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">
                <a href="/" class="mb-6 flex items-center justify-center gap-3">
                    <img src="{{ asset(config('ui.brand.logo')) }}" alt="{{ config('ui.brand.name') }}" class="h-10 w-10 rounded-lg" />
                    <div class="text-left">
                        <p class="text-sm font-semibold text-slate-100">{{ config('ui.brand.name') }}</p>
                        <p class="text-xs text-slate-400">{{ config('ui.brand.tagline') }}</p>
                    </div>
                </a>

                <div class="ui-card overflow-hidden p-6 shadow-xl shadow-black/20 sm:p-7">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
