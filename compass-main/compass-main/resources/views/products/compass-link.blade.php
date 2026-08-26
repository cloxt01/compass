@extends('layouts.app')
@php
    $repo = "https://github.com/cloxt01/compass-ext";
    $downloadLink = asset('assets/extension/compass-ext.zip');
@endphp
@section('content')
    <div class="w-full bg-[#090909]">
        <div class="mx-auto w-full max-w-[1500px] space-y-8 px-6 py-8 sm:px-8 lg:px-10">

            {{-- HERO --}}
            <section class="saas-card overflow-hidden">
                <div class="grid items-center gap-16 lg:grid-cols-[1fr_480px] p-8">

                    {{-- LEFT --}}
                    <div>
                    <span class="rounded-full border border-violet-500/30 bg-violet-500/10 px-3 py-1 text-xs uppercase tracking-[0.14em] text-violet-400">
                        Browser Extension
                    </span>

                        <h1 class="mt-5 text-5xl font-bold tracking-tight text-white">
                            CompassLink
                        </h1>

                        <p class="mt-6 max-w-2xl text-base leading-8 text-[#a1a1aa]">
                            CompassLink adalah browser extension yang menghubungkan akun provider dengan Compass Automation. Extension dibutuhkan untuk menghubungkan akun (Jobstreet).
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ $downloadLink }}"
                               download
                               target="_blank"
                               class="rounded-lg bg-violet-600 px-6 py-3 text-sm font-medium text-white transition hover:bg-violet-500">
                                Download ZIP
                            </a>

                            <a href="{{ $repo }}"
                               target="_blank"
                               class="rounded-lg border border-[#262626] bg-[#111111] px-6 py-3 text-sm text-[#d4d4d8] transition hover:bg-[#181818]">
                                GitHub
                            </a>
                        </div>

                        <div class="mt-10 flex flex-wrap gap-8 border-t border-[#262626] pt-8">
                            <div>
                                <p class="text-2xl font-semibold text-white">2</p>
                                <p class="text-sm text-[#71717a]">Supported Providers</p>
                            </div>
                            <div>
                                <p class="text-2xl font-semibold text-white">v1.0.0</p>
                                <p class="text-sm text-[#71717a]">Latest Version</p>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-violet-600/10 blur-3xl"></div>
                        <div class="relative overflow-hidden rounded-2xl border border-[#262626] bg-[#111111] shadow-2xl">
                            <img
                                src="{{ asset('assets/img/products/compass-link/overview.png') }}"
                                alt="CompassLink"
                                class="w-full">
                        </div>
                    </div>

                </div>
            </section>

            {{-- INSTALLATION TIMELINE --}}
            <section class="saas-card overflow-hidden">
                <div class="p-8">

                    <div class="border-b border-[#262626] pb-6">
                        <h2 class="text-xl font-semibold tracking-tight text-white">
                            Cara Menggunakan
                        </h2>
                        <p class="mt-2 text-sm text-[#71717a]">
                            Ikuti langkah berikut untuk memasang CompassLink pada browser Chrome.
                        </p>
                    </div>

                    <div class="relative px-8 py-8">
                        @php
                            $installSteps = [
                                'Download extension melalui tombol <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">Download ZIP</span>.',
                                'Ekstrak file ZIP yang telah diunduh.',
                                'Buka Google Chrome kemudian kunjungi <code class="rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs text-[#d4d4d8]">chrome://extensions</code>.',
                                'Aktifkan <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">Developer Mode</span> pada pojok kanan atas.',
                                'Klik <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">Load Unpacked</span>, kemudian pilih folder hasil ekstraksi CompassLink.',
                                'Pastikan <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">CompassLink</span> berhasil ditambahkan ke daftar extension Chrome.',
                                'CompassLink telah siap digunakan. Selanjutnya ikuti panduan di bawah untuk menghubungkan akun provider dengan Compass Automation.',
                            ];
                        @endphp

                        @foreach ($installSteps as $index => $step)
                            <div class="relative flex gap-5 {{ !$loop->last ? 'pb-8' : '' }}">
                                {{-- Connecting line --}}
                                @if (!$loop->last)
                                    <span class="absolute left-[17px] top-9 h-full w-px bg-gradient-to-b from-violet-500/40 to-[#262626]"></span>
                                @endif

                                {{-- Step marker --}}
                                <div class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-violet-500/30 bg-[#111111] text-sm font-semibold text-violet-300 ring-4 ring-[#090909]">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Step content --}}
                                <div class="pt-1.5 text-sm leading-7 text-[#a1a1aa]">
                                    {!! $step !!}
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </section>

            {{-- CONNECT PROVIDER TIMELINE --}}
            <section class="saas-card overflow-hidden">
                <div class="p-8">

                    <div class="border-b border-[#262626] pb-6">
                        <h2 class="text-xl font-semibold tracking-tight text-white">
                            Menghubungkan Akun Provider
                        </h2>
                        <p class="mt-2 text-sm text-[#71717a]">
                            Setelah CompassLink berhasil di-install, ikuti langkah berikut untuk menghubungkan akun provider.
                        </p>
                    </div>

                    <div class="relative px-8 py-8">
                        @php
                            $connectSteps = [
                                'Klik <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">Start Capture</span> untuk memulai proses autentikasi.',
                                'Login ke akun provider menggunakan browser seperti biasa.',
                                'Setelah login berhasil, buka kembali <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">CompassLink</span>.',
                                'Apabila status berubah menjadi <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">Captured</span>, berarti token autentikasi berhasil diperoleh.',
                                'Klik <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">Hubungkan Akun</span> untuk menyelesaikan proses sinkronisasi dengan Compass Automation.',
                            ];
                        @endphp

                        @foreach ($connectSteps as $index => $step)
                            <div class="relative flex gap-5 {{ !$loop->last ? 'pb-8' : '' }}">
                                @if (!$loop->last)
                                    <span class="absolute left-[17px] top-9 h-full w-px bg-gradient-to-b from-violet-500/40 to-[#262626]"></span>
                                @endif

                                <div class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-violet-500/30 bg-[#111111] text-sm font-semibold text-violet-300 ring-4 ring-[#090909]">
                                    {{ $index + 1 }}
                                </div>

                                <div class="pt-1.5 text-sm leading-7 text-[#a1a1aa]">
                                    {!! $step !!}
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </section>

            {{-- INFORMATION --}}
            <section class="saas-card overflow-hidden">
                <div class="p-8">

                    <h2 class="text-xl font-semibold tracking-tight text-white">
                        Informasi
                    </h2>

                    <p class="mt-4 text-sm leading-7 text-[#a1a1aa]">
                        <span class="inline-flex items-center rounded-md border border-[#2b2b2b] bg-[#181818] px-2 py-0.5 text-xs font-medium text-[#d4d4d8]">CompassLink</span> hanya digunakan selama proses autentikasi akun provider. Extension tidak mengakses riwayat penelusuran, isi halaman web, maupun data pribadi di luar proses yang diperlukan untuk menghubungkan akun dengan Compass Automation.
                    </p>

                </div>
            </section>

        </div>
    </div>
@endsection
