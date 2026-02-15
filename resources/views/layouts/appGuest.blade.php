<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Compass')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Font --}}
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome-all.min.css') }}">

    {{-- VITE --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#0d1117]">

<div class="flex flex-col min-h-screen">
    {{-- NAVBAR --}}
    @include('layouts.navbar-guest')
    
    {{-- MAIN CONTENT --}}
    <main class="flex-1 w-full">
        @yield('content')
    </main>
</div>

</body>
</html>
