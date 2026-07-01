<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', config('ui.brand.name'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-all.min.css') }}" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="h-full bg-[#0a0a0a] text-[#fafafa]">

<div id="app" class="min-h-screen">
    @include('layouts.sidebar')

    <div class="md:pl-[240px]">
        @include('layouts.navbar')
        <main class="p-4 md:p-6">
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
