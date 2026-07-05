@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-[1400px] space-y-6 px-4 pb-6 pt-2">

        {{-- HERO --}}
        <section class="saas-card overflow-hidden">
            <div class="grid lg:grid-cols-[1fr_460px] gap-10 items-center p-8">

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
                        <a href="{{ asset('assets/extension/compass-ext.zip') }}"
                           download
                           target="_blank"
                           class="rounded-lg bg-violet-600 px-6 py-3 text-sm font-medium text-white transition hover:bg-violet-500">
                            Download ZIP
                        </a>

                        <a href="https://github.com/your-repo"
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
                            <p class="text-2xl font-semibold text-white">MV3</p>
                            <p class="text-sm text-[#71717a]">Chrome Extension</p>
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
        {{-- INSTALLATION --}}
        <section class="saas-card">
            <div class="border-b border-[#262626] px-6 py-4">
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">
                    Cara Menggunakan
                </h2>
            </div>

            <div class="divide-y divide-[#262626]">
                <div class="flex gap-4 px-6 py-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-sm font-semibold text-violet-400">
                        1
                    </div>

                    <div>

                        <p class="mt-1 text-sm text-[#a1a1aa]">
                            Tambahkan extension CompassLink ke browser Google Chrome.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 px-6 py-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-sm font-semibold text-violet-400">
                        2
                    </div>

                    <div>
                        <p class="font-medium text-[#fafafa]">
                            Start Listening
                        </p>
                        <p class="mt-1 text-sm text-[#a1a1aa]">
                            Buka extension kemudian aktifkan proses token capture.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 px-6 py-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-sm font-semibold text-violet-400">
                        3
                    </div>

                    <div>
                        <p class="font-medium text-[#fafafa]">
                            Login Provider
                        </p>
                        <p class="mt-1 text-sm text-[#a1a1aa]">
                            Login ke JobStreet atau Glints seperti biasa.
                        </p>
                    </div>
                </div>

                <div class="flex gap-4 px-6 py-5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-500/10 text-sm font-semibold text-violet-400">
                        4
                    </div>

                    <div>
                        <p class="font-medium text-[#fafafa]">
                            Token Terhubung
                        </p>
                        <p class="mt-1 text-sm text-[#a1a1aa]">
                            Setelah token berhasil ditangkap, Compass akan dapat menggunakan akun
                            tersebut untuk automation.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- NOTE --}}
        <section class="rounded-xl border border-blue-500/20 bg-blue-500/5 p-6">
            <h2 class="text-sm font-semibold uppercase tracking-[0.14em] text-blue-400">
                Informasi
            </h2>

            <p class="mt-3 text-sm leading-7 text-[#a1a1aa]">
                CompassLink tidak mengambil riwayat browsing atau data pribadi di luar proses
                autentikasi. Extension hanya membaca endpoint login yang dibutuhkan untuk
                menghubungkan akun dengan Compass Automation.
            </p>


        </section>


    </div>
@endsection

