<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">



    <x-og-meta />



    <title>

        @hasSection('title')

            @yield('title')

        @else

            {{ config('app.name', 'Compass') }}

        @endif

    </title>



    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">

    <link rel="apple-touch-icon" href="{{ asset('assets/logo.svg') }}">



    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>



    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet">



    @vite(['resources/css/app.css', 'resources/js/app.js'])



    <style>

        body {

            font-family: 'Geist', sans-serif;

        }



        @keyframes elegant-bounce {

            0%,100% {

                transform: translateY(0);

                animation-timing-function: cubic-bezier(.8,0,1,1);

            }



            50% {

                transform: translateY(-10px);

                animation-timing-function: cubic-bezier(0,0,.2,1);

            }

        }



        .saas-card {

            background: #111111;

            border: 1px solid #262626;

            border-radius: 16px;

            box-shadow: 0 2px 16px rgba(0,0,0,.28);

        }



        .saas-input {

            border: 1px solid #262626;

            background: #0A0A0A;

            transition: .2s;

        }



        .saas-input:focus {

            border-color: #3B82F6;

            box-shadow: 0 0 0 2px rgba(59,130,246,.15);

        }

    </style>



    @stack('styles')

</head>



<body class="@hasSection('body-class')@yield('body-class')@else bg-[#0a0a0a] text-[#fafafa] antialiased min-h-screen flex flex-col relative overflow-hidden @endif">



{{-- Background --}}

@yield('background')



<div class="relative z-10 flex min-h-screen flex-col">



    {{-- Header --}}

    @unless(View::hasSection('hide-header'))

        <header class="px-6 py-8 md:px-12">

            <div class="mx-auto flex max-w-7xl items-center justify-between">



                <a href="{{ url('/') }}" class="flex items-center gap-2.5">

                    @include('partials.logo', ['class' => 'h-8 w-8'])

                    <span class="text-sm font-semibold tracking-wide">

                            {{ config('ui.brand.name', 'Compass') }}

                        </span>

                </a>



                @guest

                    <a

                        href="{{ route('login') }}"

                        class="inline-flex h-9 items-center justify-center rounded-md border border-[#333] bg-[#1e1e1e] px-4 text-xs font-semibold hover:bg-[#262626] transition"

                    >

                        Masuk

                    </a>

                @endguest



            </div>

        </header>

    @endunless



    {{-- Content --}}

    <main class="@hasSection('main-class')@yield('main-class')@else flex-1 @endif">

        @yield('content')

    </main>



    {{-- Footer --}}

    @unless(View::hasSection('hide-footer'))

        <footer class="px-6 py-6 md:px-12">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 text-xs text-[#71717a] sm:flex-row sm:items-center sm:justify-between">

                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <div>
                        © {{ date('Y') }} {{ config('ui.brand.name', 'Compass') }}. All rights reserved.
                    </div>
                    <a href="https://wa.me/6283172935110" class="hover:text-[#a1a1aa] transition">
                        • Telp: 0831-7293-5110
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    <a class="transition hover:text-[#a1a1aa] cursor-pointer {{ request()->routeIs('terms') ? 'text-[#a1a1aa]' : '' }}" href="{{ route('terms') }}">
                        Syarat & Ketentuan
                    </a>
                    <a class="transition hover:text-[#a1a1aa] cursor-pointer {{ request()->routeIs('privacy') ? 'text-[#a1a1aa]' : '' }}" href="{{ route('privacy') }}">
                        Privasi
                    </a>
                    <span class="h-1 w-1 rounded-full bg-[#333]"></span>
                    <span class="flex items-center gap-1.5">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                All systems operational
            </span>
                </div>

            </div>
        </footer>

    @endunless



</div>



<script>

    document.addEventListener('DOMContentLoaded', () => {

        if (window.lucide) {

            lucide.createIcons();

        }

    });

</script>



@stack('scripts')



</body>

</html>
