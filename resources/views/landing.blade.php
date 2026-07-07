
@extends('layouts.guest')

@section('title', 'Selamat Datang di Compass')

@section('body-class', 'bg-[#0a0a0a] text-[#fafafa] antialiased')


@section('main-class', 'flex flex-1 flex-col justify-center')

@section('background')
    <div class="absolute inset-0 z-0 overflow-hidden">
        <video
            autoplay
            muted
            loop
            playsinline
            class="h-full w-full object-cover opacity-50"
        >
            <source src="{{ asset('assets/video/background.mp4') }}" type="video/mp4">
        </video>

        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-[#0a0a0a]/40 to-[#0a0a0a] backdrop-blur-[2px]"></div>
    </div>
@endsection

@section('content')
    <section class="mx-auto flex max-w-2xl flex-col items-center px-6 text-center">

        <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-400 opacity-0 animate-fade-in-bounce animate-elegant-bounce">
            <i data-lucide="sparkles" class="h-3.5 w-3.5"></i>
            <span>Next-Gen Enterprise Automation</span>
        </div>

        <h1 class="mt-6 bg-gradient-to-b from-[#fafafa] to-[#a1a1aa] bg-clip-text text-4xl font-bold leading-tight tracking-tight text-transparent opacity-0 animate-fade-in-bounce delay-200 sm:text-6xl">
            Otomatiskan Alur Lamaran Pekerjaan Anda
        </h1>

        <p class="mt-4 max-w-lg text-sm leading-relaxed text-[#a1a1aa] opacity-0 animate-fade-in-bounce delay-400 sm:text-base">
            Biarkan AI mencari peluang, memeriksa persyaratan pekerjaan,
            dan mengelola penerapan dengan lancar di berbagai penyedia.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-4 opacity-0 animate-fade-in-bounce delay-600">

            <a
                href="{{ route('register') }}"
                class="inline-flex h-11 items-center justify-center rounded-xl bg-[#fafafa] px-6 text-sm font-semibold text-black shadow-lg shadow-white/5 transition hover:-translate-y-0.5 hover:bg-white"
            >
                Dapatkan Akses Awal
            </a>

            <a
                href="#features"
                class="inline-flex h-11 items-center justify-center rounded-xl border border-[#262626] bg-[#0a0a0a]/60 px-6 text-sm font-semibold text-[#a1a1aa] transition hover:bg-[#1e1e1e]/80 hover:text-[#fafafa]"
            >
                Pelajari Lebih Lanjut
            </a>


        </div>


    </section>
    <section id="features" class="mx-auto mt-32 w-full max-w-7xl px-6 lg:px-8">

        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white/[0.03] backdrop-blur-xl">

            <div class="grid lg:grid-cols-2">

                <div class="p-8 sm:p-10 lg:p-14">

                <span class="text-sm font-semibold tracking-widest uppercase text-blue-400">
                    Automation
                </span>

                    <h2 class="mt-4 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                        Compass Bekerja Bahkan Saat Anda Tidak Sedang Online
                    </h2>

                    <p class="mt-6 max-w-xl text-base leading-8 text-zinc-400 sm:text-lg">
                        Setelah akun dan preferensi pekerjaan dikonfigurasi, Compass
                        secara otomatis memantau lowongan baru, memeriksa kecocokan,
                        dan mengirim lamaran tanpa perlu Anda membuka platform setiap
                        saat. Proses berjalan selama 24 jam sehingga peluang tidak
                        terlewat ketika lowongan baru dipublikasikan.
                    </p>

                </div>

                <div class="border-t border-white/10 lg:border-l lg:border-t-0">

                    <div class="grid divide-y divide-white/10">

                        <div class="p-8">
                            <h3 class="text-lg font-semibold text-white">
                                Monitoring Berkelanjutan
                            </h3>

                            <p class="mt-3 text-sm leading-7 text-zinc-400">
                                Compass terus memantau lowongan baru sesuai filter
                                yang telah Anda tentukan.
                            </p>
                        </div>

                        <div class="p-8">
                            <h3 class="text-lg font-semibold text-white">
                                Seleksi Otomatis
                            </h3>

                            <p class="mt-3 text-sm leading-7 text-zinc-400">
                                Persyaratan pekerjaan diperiksa terlebih dahulu agar
                                hanya lowongan yang relevan yang diproses.
                            </p>
                        </div>

                        <div class="p-8">
                            <h3 class="text-lg font-semibold text-white">
                                Pengiriman Lamaran
                            </h3>

                            <p class="mt-3 text-sm leading-7 text-zinc-400">
                                Data pelamar digunakan untuk mengisi formulir dan
                                mengirim lamaran secara otomatis tanpa proses yang
                                berulang.
                            </p>
                        </div>

                        <div class="p-8">
                            <h3 class="text-lg font-semibold text-white">
                                Berjalan 24 Jam
                            </h3>

                            <p class="mt-3 text-sm leading-7 text-zinc-400">
                                Selama layanan aktif, Compass tetap bekerja di
                                belakang layar sehingga Anda tidak perlu terus
                                memantau platform pencarian kerja.
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
@endsection
