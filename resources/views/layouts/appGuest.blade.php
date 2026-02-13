<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Compass')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Font --}}
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-all.min.css') }}">

    {{-- VITE --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gray-100">

<div class="flex min-h-screen">

    {{-- MAIN CONTENT --}}
    <main class="flex-1 p-6 transition-all duration-300">
        @yield('content')
    </main>

</div>

<footer class="bg-white border-t">
    <div class="text-center py-4 text-sm text-gray-500">
        © Compass 2026
    </div>
</footer>

</body>
</html>
