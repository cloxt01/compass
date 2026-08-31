@extends('layouts.guest')

@php
    $tanggal = '2026-09-01';
    $lastUpdated = date("F d, Y", strtotime($tanggal));
@endphp

@section('title', 'Kebijakan Privasi')

@section('body-class', 'bg-[#0a0a0a] text-[#fafafa] antialiased')

@section('background')
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-[#111827] via-[#0a0a0a] to-[#0a0a0a]"></div>

        <div class="absolute right-0 top-20 h-[500px] w-[500px] rounded-full bg-blue-500/10 blur-[140px]"></div>
    </div>
@endsection

@section('content')
    <section class="mx-auto max-w-4xl px-6 py-20">

        {{-- HEADER --}}
        <div class="text-center">

            <span class="inline-flex rounded-full border border-[#333] bg-[#161616] px-3 py-1 text-[10px] font-medium uppercase tracking-wider text-[#a1a1aa]">
                Privacy Policy
            </span>

            <h1 class="mt-6 text-5xl font-bold">
                Kebijakan Privasi
            </h1>

            <p class="mt-4 text-[#a1a1aa]">
                Terakhir diperbarui:
                <span class="text-[#d4d4d8]">
                    {{ $lastUpdated }}
                </span>
            </p>

        </div>


        {{-- CONTENT --}}
        <div class="mt-16 space-y-10 rounded-3xl border border-[#262626] bg-[#111111]/80 p-10 backdrop-blur">

            <section>
                <h2 class="text-xl font-semibold">
                    Informasi yang Kami Kumpulkan
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami mengumpulkan informasi akun, preferensi lamaran, kredensial penyedia yang terhubung,
                    dan aktivitas yang diperlukan untuk mengoperasikan Compass.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Jika Anda mengaktifkan
                    <span class="rounded bg-[#1c1c1c] px-1.5 py-0.5 text-[#d4d4d8]">
                        autentikasi dua faktor (2FA)
                    </span>,
                    Compass juga dapat menyimpan informasi yang diperlukan untuk mengaktifkan dan memverifikasi
                    metode autentikasi tersebut, termasuk konfigurasi autentikasi dan kode pemulihan yang terkait
                    dengan akun Anda.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    Bagaimana Kami Menggunakan Informasi
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Informasi Anda digunakan untuk menyediakan fitur otomatisasi, meningkatkan keandalan,
                    mengelola proses lamaran, serta menjaga keamanan dan stabilitas platform.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Informasi autentikasi dua faktor digunakan secara khusus untuk
                    <span class="text-[#d4d4d8]">
                        memverifikasi identitas
                    </span>
                    Anda saat masuk ke akun dan membantu mencegah akses yang tidak sah.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    Keamanan Data
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami menerapkan perlindungan teknis dan organisasi yang wajar untuk menjaga keamanan informasi Anda.
                    Data sensitif dilindungi menggunakan
                    <span class="rounded bg-[#1c1c1c] px-1.5 py-0.5 font-mono text-xs text-[#d4d4d8]">
                        AES-256-CBC
                    </span>
                    dengan kunci yang dikelola secara aman, serta pembatasan akses untuk membantu mencegah akses
                    atau penggunaan yang tidak sah.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    Autentikasi Dua Faktor
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass menyediakan fitur
                    <span class="rounded bg-[#1c1c1c] px-1.5 py-0.5 text-[#d4d4d8]">
                        2FA
                    </span>
                    sebagai lapisan keamanan tambahan pada akun. Dengan mengaktifkan fitur ini, Anda akan diminta
                    memberikan kode autentikasi tambahan setelah memasukkan kredensial akun yang benar.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kode autentikasi dapat dihasilkan melalui aplikasi authenticator yang kompatibel dengan metode
                    autentikasi yang digunakan Compass. Compass tidak memerlukan akses ke isi aplikasi authenticator Anda.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Saat 2FA diaktifkan, Compass juga menyediakan
                    <span class="rounded bg-[#1c1c1c] px-1.5 py-0.5 text-[#d4d4d8]">
                        kode pemulihan
                    </span>
                    yang dapat digunakan sebagai metode alternatif apabila Anda kehilangan akses ke aplikasi
                    authenticator. Kode pemulihan harus disimpan oleh Anda di tempat yang aman dan tidak dibagikan
                    kepada pihak lain.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Setiap kode pemulihan ditujukan untuk
                    <span class="font-medium text-[#d4d4d8]">
                        penggunaan satu kali
                    </span>.
                    Setelah digunakan, kode tersebut tidak dapat digunakan kembali untuk proses autentikasi berikutnya.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    Penggunaan dan Pembagian Data
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami tidak menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak lain
                    untuk tujuan pemasaran atau kepentingan komersial. Informasi hanya digunakan sejauh diperlukan
                    untuk menyediakan, mengoperasikan, mengamankan, dan meningkatkan layanan Compass.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    Layanan Pihak Ketiga
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass dapat terintegrasi dengan platform atau layanan eksternal yang Anda pilih untuk digunakan.
                    Informasi tertentu dapat diproses atau dikirimkan kepada layanan tersebut hanya sejauh diperlukan
                    untuk menjalankan fitur yang Anda gunakan. Penanganan data oleh pihak ketiga tersebut tunduk pada
                    kebijakan privasi dan ketentuan masing-masing layanan.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    Penghapusan dan Pemutusan Akses
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Data Anda disimpan dalam sistem kami selama Anda menggunakan layanan Compass.
                    Jika Anda sudah tidak menggunakan layanan, Anda dapat mengajukan permintaan
                    penghapusan akun melalui saluran dukungan resmi kami.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Anda juga dapat memutuskan koneksi dengan akun platform atau provider yang
                    terhubung melalui sistem Compass. Pemutusan koneksi tersebut akan menghentikan
                    akses sistem kami terhadap akun atau provider yang terhubung, tanpa harus
                    menghapus akun Compass Anda.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    Tanggung Jawab Pengguna
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Anda bertanggung jawab untuk menjaga kerahasiaan kredensial akun, perangkat authenticator,
                    dan kode pemulihan. Jangan membagikan kode autentikasi atau kode pemulihan kepada pihak lain.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    Kontak
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Pertanyaan mengenai Kebijakan Privasi ini atau permintaan terkait data dapat diajukan
                    melalui saluran dukungan resmi Compass.
                </p>
            </section>

        </div>

    </section>
@endsection
