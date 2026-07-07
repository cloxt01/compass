@extends('layouts.guest')

@php
    $tanggal = '2024-06-01';
    $lastUpdated = date("F d, Y", strtotime($tanggal));;
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

            <div class="inline-flex rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-1 text-xs font-medium text-blue-400">
                Legal
            </div>

            <h1 class="mt-6 text-5xl font-bold">
                Kebijakan Privasi
            </h1>

            <p class="mt-4 text-[#a1a1aa]">
                Terakhir diperbarui: {{ $lastUpdated  }}
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
                    Informasi Anda digunakan semata-mata untuk menyediakan fitur otomatisasi, meningkatkan keandalan,
                    dan menjaga keamanan platform.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Keamanan Data</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami menerapkan perlindungan teknis dan organisasi yang wajar untuk melindungi informasi Anda.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Layanan Pihak Ketiga</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass terintegrasi dengan platform lowongan kerja eksternal. Penanganan data Anda oleh mereka diatur
                    oleh kebijakan privasi masing-masing.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Kontak</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Pertanyaan mengenai Kebijakan Privasi ini dapat diajukan melalui saluran dukungan kami.
                </p>
            </section>

        </div>

    </section>
@endsection
