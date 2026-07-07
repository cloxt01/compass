@extends('layouts.guest')

@php
    $tanggal = '2024-06-01';
    $lastUpdated = date("F d, Y", strtotime($tanggal));;
@endphp

@section('title', 'Syarat & Ketentuan Layanan')

@section('body-class', 'bg-[#0a0a0a] text-[#fafafa] antialiased')

@section('background')
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-[#111827] via-[#0a0a0a] to-[#0a0a0a]"></div>

        <div class="absolute left-1/2 top-0 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-blue-500/10 blur-[140px]"></div>
    </div>
@endsection

@section('content')
    <section class="mx-auto max-w-4xl px-6 py-20">

        <div class="text-center">
            <div class="inline-flex rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-1 text-xs font-medium text-blue-400">
                Legal
            </div>

            <h1 class="mt-6 text-5xl font-bold">
                Syarat & Ketentuan Layanan
            </h1>

            <p class="mt-4 text-[#a1a1aa]">
                Terakhir diperbarui: {{ $lastUpdated  }}

            </p>
        </div>

        <div class="mt-16 space-y-10 rounded-3xl border border-[#262626] bg-[#111111]/80 p-10 backdrop-blur">

            <section>
                <h2 class="text-xl font-semibold">1. Persetujuan</h2>
                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Dengan mengakses atau menggunakan Compass, Anda menyetujui untuk terikat dengan Ketentuan ini.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">2. Akun</h2>
                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Anda bertanggung jawab untuk menjaga keamanan akun dan kredensial Anda.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">3. Otomatisasi</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass mengotomatiskan alur lamaran pekerjaan berdasarkan konfigurasi Anda sendiri.
                    Anda tetap bertanggung jawab untuk meninjau bagaimana otomatisasi digunakan dan mematuhi
                    kebijakan platform lowongan kerja pihak ketiga.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">4. Ketersediaan Layanan</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami berupaya memberikan layanan yang andal tetapi tidak dapat menjamin ketersediaan tanpa gangguan.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">5. Penghentian</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami dapat menangguhkan atau menghentikan akun yang menyalahgunakan platform atau melanggar Ketentuan ini.
                </p>
            </section>

        </div>

    </section>
@endsection
