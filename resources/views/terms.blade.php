@extends('layouts.guest')

@php
    $tanggal = '2026-09-01';
    $lastUpdated = date("F d, Y", strtotime($tanggal));
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

        {{-- HEADER --}}
        <div class="text-center">


            <h1 class="mt-6 text-5xl font-bold">
                Syarat & Ketentuan Layanan
            </h1>

            <p class="mt-4 text-[#a1a1aa]">
                Terakhir diperbarui:
                <span class="text-[#d4d4d8]">{{ $lastUpdated }}</span>
            </p>

        </div>


        {{-- CONTENT --}}
        <div class="mt-16 space-y-10 rounded-3xl border border-[#262626] bg-[#111111]/80 p-10 backdrop-blur">

            <section>
                <h2 class="text-xl font-semibold">
                    1. Persetujuan
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Dengan mengakses atau menggunakan
                    <span class="text-[#fafafa] font-medium">Compass</span>,
                    Anda menyetujui untuk terikat dengan Ketentuan ini.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    2. Akun
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Anda bertanggung jawab untuk menjaga keamanan akun dan
                    <span class="text-[#d4d4d8]">kredensial</span>
                    Anda.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Anda juga bertanggung jawab atas aktivitas yang dilakukan melalui akun Anda
                    dan wajib segera memberitahukan kepada Compass apabila mengetahui adanya
                    akses yang tidak sah.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    3. Otomatisasi
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass mengotomatiskan alur lamaran pekerjaan berdasarkan konfigurasi Anda sendiri.
                    Anda tetap bertanggung jawab untuk meninjau bagaimana otomatisasi digunakan dan mematuhi
                    kebijakan platform lowongan kerja pihak ketiga.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Penggunaan fitur otomatisasi tidak menjamin bahwa lamaran akan diterima,
                    diproses, atau menghasilkan pekerjaan.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    4. Ketersediaan Layanan
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami berupaya memberikan layanan yang andal tetapi tidak dapat menjamin
                    <span class="text-[#d4d4d8]">ketersediaan tanpa gangguan</span>.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    5. Layanan Pihak Ketiga
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass dapat terintegrasi dengan layanan atau platform pihak ketiga.
                    Penggunaan layanan tersebut tetap tunduk pada
                    <span class="text-[#d4d4d8]">syarat dan kebijakan</span>
                    masing-masing penyedia layanan.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass tidak bertanggung jawab atas perubahan, gangguan, pembatasan,
                    atau kebijakan yang diberlakukan oleh layanan pihak ketiga.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    6. Penghentian
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami dapat menangguhkan atau menghentikan akun yang menyalahgunakan platform
                    atau melanggar Ketentuan ini.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    7. Perubahan Ketentuan
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Kami dapat memperbarui Ketentuan ini dari waktu ke waktu untuk mencerminkan
                    perubahan layanan, fitur, persyaratan hukum, atau kebutuhan operasional.
                </p>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Perubahan akan ditampilkan pada halaman ini dan tanggal pembaruan akan
                    disesuaikan sebagaimana mestinya.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    8. Tanggung Jawab Pengguna
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Anda bertanggung jawab untuk menggunakan Compass secara wajar, sah,
                    dan sesuai dengan Ketentuan ini serta peraturan yang berlaku.
                </p>
            </section>


            <section>
                <h2 class="text-xl font-semibold">
                    9. Kontak
                </h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Pertanyaan mengenai Syarat & Ketentuan Layanan ini dapat diajukan
                    melalui saluran dukungan resmi Compass.
                </p>
            </section>

        </div>

    </section>
@endsection
