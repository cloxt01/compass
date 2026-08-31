```blade
@extends('layouts.guest')

@php
    $tanggal = '2024-06-01';
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

        <div class="text-center">

            <h1 class="mt-6 text-5xl font-bold">
                Kebijakan Privasi
            </h1>

            <p class="mt-4 text-[#a1a1aa]">
                Terakhir diperbarui: {{ $lastUpdated }}
            </p>

        </div>

        <div class="mt-16 space-y-10 rounded-3xl border border-[#262626] bg-[#111111]/80 p-10 backdrop-blur">

            <section>
                <h2 class="text-xl font-semibold">Informasi yang Kami Kumpulkan</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami mengumpulkan informasi akun, preferensi lamaran, kredensial penyedia yang terhubung,
                    dan aktivitas yang diperlukan untuk mengoperasikan Compass.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Bagaimana Kami Menggunakan Informasi</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Informasi Anda digunakan untuk menyediakan fitur otomatisasi, meningkatkan keandalan,
                    mengelola proses lamaran, serta menjaga keamanan dan stabilitas platform.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Keamanan Data</h2>

                <p class="mt-3 text-[#a1a1a1] leading-8">
                    Kami menerapkan perlindungan teknis dan organisasi yang wajar untuk menjaga keamanan informasi Anda.
                    Data sensitif dilindungi menggunakan enkripsi AES-256-CBC dengan kunci yang dikelola secara aman,
                    serta pembatasan akses untuk membantu mencegah akses atau penggunaan yang tidak sah.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Penggunaan dan Pembagian Data</h2>

                <p class="mt-3 text-[#a1a1a1] leading-8">
                    Kami tidak menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak lain
                    untuk tujuan pemasaran atau kepentingan komersial. Informasi hanya digunakan sejauh diperlukan
                    untuk menyediakan, mengoperasikan, mengamankan, dan meningkatkan layanan Compass.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Layanan Pihak Ketiga</h2>

                <p class="mt-3 text-[#a1a1a1] leading-8">
                    Compass dapat terintegrasi dengan platform atau layanan eksternal yang Anda pilih untuk digunakan.
                    Informasi tertentu dapat diproses atau dikirimkan kepada layanan tersebut hanya sejauh diperlukan
                    untuk menjalankan fitur yang Anda gunakan. Penanganan data oleh pihak ketiga tersebut tunduk pada
                    kebijakan privasi dan ketentuan masing-masing layanan.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Penghapusan dan Pemutusan Akses</h2>

                <p class="mt-3 text-[#a1a1a1] leading-8">
                    Data Anda disimpan dalam sistem kami selama Anda menggunakan layanan Compass.
                    Jika Anda sudah tidak menggunakan layanan, Anda dapat mengajukan permintaan
                    penghapusan akun melalui saluran dukungan resmi kami.
                </p>

                <p class="mt-3 text-[#a1a1a1] leading-8">
                    Anda juga dapat memutuskan koneksi dengan akun platform atau provider yang
                    terhubung melalui sistem Compass. Pemutusan koneksi tersebut akan menghentikan
                    akses sistem kami terhadap akun atau provider yang terhubung, tanpa harus
                    menghapus akun Compass Anda.
                </p>
            </section>



            <section>
                <h2 class="text-xl font-semibold">Kontak</h2>

                <p class="mt-3 text-[#a1a1a1] leading-8">
                    Pertanyaan mengenai Kebijakan Privasi ini atau permintaan terkait data dapat diajukan
                    melalui saluran dukungan resmi Compass.
                </p>
            </section>

        </div>

    </section>
@endsection
```
