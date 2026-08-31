@extends('layouts.guest')

@section('title', 'Compass — Auto Apply Kerja Buat Freshgrad')

@section('body-class', 'bg-[#0a0a0a] text-[#fafafa] antialiased')

@section('main-class', 'flex flex-1 flex-col justify-center')

@section('background') <div class="absolute inset-0 overflow-hidden"> <div class="absolute inset-0 bg-gradient-to-b from-[#111827] via-[#0a0a0a] to-[#0a0a0a]"></div>

    <div
        class="absolute left-1/2 top-0 h-[500px] w-[500px] -translate-x-1/2 -translate-y-1/4 rounded-full bg-blue-500/10 blur-[140px]"
    ></div>
</div>

@endsection

@section('content')

{{-- =========================================================
    HERO
========================================================== --}}
<section
    class="mx-auto flex max-w-2xl flex-col items-center px-6 text-center"
    data-aos="fade-up"
    data-aos-duration="900"
    data-aos-easing="ease-out-cubic"
>

    {{-- Badge --}}
    <div
        data-aos="fade-down"
        data-aos-duration="700"
        class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-1.5 text-xs font-medium text-blue-400"
    >
        <i data-lucide="graduation-cap" class="h-4 w-4"></i>

        <span>
            Untuk freshgrad yang capek apply satu-satu
        </span>
    </div>

    {{-- Heading --}}
    <h1
        data-aos="fade-up"
        data-aos-delay="100"
        data-aos-duration="800"
        class="mt-6 text-4xl font-bold leading-tight tracking-tight text-[#fafafa] sm:text-6xl"
    >
        Biar Compass yang<br>
        kirim lamarannya.
    </h1>

    {{-- Description --}}
    <p
        data-aos="fade-up"
        data-aos-delay="200"
        data-aos-duration="800"
        class="mt-4 max-w-lg text-sm leading-relaxed text-[#a1a1aa] sm:text-base"
    >
        Cari kerja itu melelahkan, apalagi kalau buka platform pencari kerja tiap hari dan isi form
        yang isinya itu-itu aja. Compass bantu kirim lamaran ke lebih banyak lowongan secara otomatis,
        biar waktu kamu bisa dipakai buat hal yang lebih penting — nyiapin CV, portofolio, atau latihan
        interview.
    </p>

    {{-- CTA --}}
    <div
        data-aos="fade-up"
        data-aos-delay="350"
        data-aos-duration="800"
        class="mt-8 flex flex-wrap items-center justify-center gap-4"
    >
        <a
            href="{{ route('register') }}"
            class="inline-flex h-11 items-center justify-center rounded-xl bg-[#fafafa] px-6 text-sm font-semibold text-black shadow-lg shadow-white/5 transition duration-300 hover:-translate-y-0.5 hover:bg-white"
        >
            Coba Sekarang
        </a>

        <a
            href="#features"
            class="inline-flex h-11 items-center justify-center rounded-xl border border-[#262626] bg-[#0a0a0a]/60 px-6 text-sm font-semibold text-[#a1a1aa] transition duration-300 hover:-translate-y-0.5 hover:bg-[#1e1e1e]/80 hover:text-[#fafafa]"
        >
            Liat Cara Kerjanya
        </a>
    </div>
</section>


{{-- =========================================================
    IMPACT STATS
========================================================== --}}
<section class="mx-auto mt-24 max-w-4xl px-6">

    <div class="grid grid-cols-1 gap-6 text-center sm:grid-cols-3">

        {{-- Stat 1 --}}
        <div
            data-aos="fade-up"
            data-aos-delay="0"
            data-aos-duration="700"
            class="group rounded-2xl border border-white/10 bg-white/[0.02] p-6 backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-white/20 hover:bg-white/[0.04]"
        >
            <p class="text-3xl font-bold text-white sm:text-4xl">
                <span
                    class="countup"
                    data-countup="50"
                    data-suffix="+ Jam"
                >
                    0
                </span>
            </p>

            <p class="mt-2 text-xs text-[#a1a1aa]">
                Waktu dihemat per minggu dari ngisi form manual
            </p>
        </div>


        {{-- Stat 2 --}}
        <div
            data-aos="fade-up"
            data-aos-delay="100"
            data-aos-duration="700"
            class="group rounded-2xl border border-white/10 bg-white/[0.02] p-6 backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-blue-500/30 hover:bg-white/[0.04]"
        >
            <p class="text-3xl font-bold text-blue-400 sm:text-4xl">
                <span
                    class="countup"
                    data-countup="100"
                    data-suffix="%"
                >
                    0
                </span>
            </p>

            <p class="mt-2 text-xs text-[#a1a1aa]">
                Kriteria lowongan difilter presisi pakai kriteria kamu
            </p>
        </div>


        {{-- Stat 3 --}}
        <div
            data-aos="fade-up"
            data-aos-delay="200"
            data-aos-duration="700"
            class="group rounded-2xl border border-white/10 bg-white/[0.02] p-6 backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-white/20 hover:bg-white/[0.04]"
        >
            <p class="text-3xl font-bold text-white sm:text-4xl">
                <span
                    class="countup"
                    data-countup="24"
                    data-suffix="/7"
                >
                    0
                </span>
            </p>

            <p class="mt-2 text-xs text-[#a1a1aa]">
                Bot memantau lowongan baru yang pas tiap menit
            </p>
        </div>

    </div>
</section>


{{-- =========================================================
    HOW IT WORKS
========================================================== --}}
<section class="mx-auto mt-32 max-w-5xl px-6 lg:px-8">

    {{-- Heading --}}
    <div
        class="mb-14 text-center"
        data-aos="fade-up"
        data-aos-duration="800"
    >
        <span
            class="inline-flex rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-1 text-xs font-medium text-blue-400"
        >
            Langkah Cepat
        </span>

        <h2 class="mx-auto mt-4 text-3xl font-bold text-white sm:text-4xl">
            Cuma 3 langkah buat jalanin otomatisasi.
        </h2>
    </div>


    <div class="grid gap-8 sm:grid-cols-3">

        {{-- Step 01 --}}
        <div
            data-aos="fade-up"
            data-aos-delay="0"
            data-aos-duration="700"
            class="group relative rounded-2xl border border-white/10 bg-white/[0.02] p-6 transition duration-300 hover:-translate-y-1 hover:border-blue-500/20 hover:bg-white/[0.04]"
        >
            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-sm font-bold text-blue-400 transition duration-300 group-hover:bg-blue-500/20"
            >
                01
            </div>

            <h3 class="mt-4 text-base font-semibold text-white">
                Atur Profil
            </h3>

            <p class="mt-2 text-xs leading-relaxed text-[#a1a1aa]">
                Atur profil kamu mulai dari range gaji, domisili, skills,
                sertifikat, dan pengalaman kerja. Compass bakal pakai data
                ini buat menjawab pertanyaan screening.
            </p>
        </div>


        {{-- Step 02 --}}
        <div
            data-aos="fade-up"
            data-aos-delay="100"
            data-aos-duration="700"
            class="group relative rounded-2xl border border-white/10 bg-white/[0.02] p-6 transition duration-300 hover:-translate-y-1 hover:border-blue-500/20 hover:bg-white/[0.04]"
        >
            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-sm font-bold text-blue-400 transition duration-300 group-hover:bg-blue-500/20"
            >
                02
            </div>

            <h3 class="mt-4 text-base font-semibold text-white">
                Pilih Model AI
            </h3>

            <p class="mt-2 text-xs leading-relaxed text-[#a1a1aa]">
                Pilih model AI favorit kamu untuk menjawab pertanyaan
                screening HRD sesuai pengalaman nyata di CV.
            </p>
        </div>


        {{-- Step 03 --}}
        <div
            data-aos="fade-up"
            data-aos-delay="200"
            data-aos-duration="700"
            class="group relative rounded-2xl border border-white/10 bg-white/[0.02] p-6 transition duration-300 hover:-translate-y-1 hover:border-blue-500/20 hover:bg-white/[0.04]"
        >
            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-sm font-bold text-blue-400 transition duration-300 group-hover:bg-blue-500/20"
            >
                03
            </div>

            <h3 class="mt-4 text-base font-semibold text-white">
                Biarkan Bot Bekerja
            </h3>

            <p class="mt-2 text-xs leading-relaxed text-[#a1a1aa]">
                Compass memindai lowongan baru, melakukan apply otomatis,
                dan kamu tinggal pantau hasilnya via dashboard.
            </p>
        </div>

    </div>
</section>


{{-- =========================================================
    FEATURES / NOTIFICATIONS
========================================================== --}}
<section
    id="features"
    class="mx-auto mt-32 w-full max-w-5xl px-6 lg:px-8"
>

    <div
        class="mb-14 text-center"
        data-aos="fade-up"
        data-aos-duration="800"
    >
        <span
            class="inline-flex rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-1 text-xs font-medium text-blue-400"
        >
            Notifikasi dari Compass
        </span>

        <h2
            class="mx-auto mt-5 max-w-2xl text-3xl font-bold leading-tight text-white sm:text-4xl"
        >
            Ini yang Compass kerjakan di belakang layar.
        </h2>

        <p class="mx-auto mt-3 max-w-md text-sm text-[#a1a1aa]">
            Ditampilkan seperti notifikasi biar gampang dibayangin — tiap
            proses berjalan otomatis, tanpa kamu harus mantengin dashboard
            terus-menerus.
        </p>
    </div>


    @php
        $notifs = [
            [
                'icon' => 'search',
                'title' => '12 lowongan baru yang cocok sama kamu',
                'body' => 'Compass memantau lowongan baru terus-menerus, sesuai kriteria yang kamu atur — posisi, lokasi, sampai gaji minimal.'
            ],
            [
                'icon' => 'brain',
                'title' => 'Lowongan "min. 3 tahun pengalaman" dilewati',
                'body' => 'Lowongan yang jelas gak cocok sama profil kamu otomatis difilter, biar gak buang waktu apply ke tempat yang jelas-jelas nolak freshgrad.'
            ],
            [
                'icon' => 'send',
                'title' => 'Lamaran ke PT Maju Jaya berhasil dikirim',
                'body' => 'Form dan dokumen terisi otomatis pakai data kamu, gak perlu isi ulang form yang sama di tiap lowongan.'
            ],
            [
                'icon' => 'message-square',
                'title' => 'Pertanyaan screening sudah dijawab',
                'body' => 'AI menjawab pertanyaan seperti "kenapa kamu cocok untuk posisi ini?" berdasarkan profil yang kamu atur di pengaturan — bukan mengarang pengalaman biar terlihat cocok.'
            ],
            [
                'icon' => 'settings',
                'title' => 'Model AI bisa kamu ganti kapan saja',
                'body' => 'Mau pakai AI yang mana untuk menjawab pertanyaan screening, tinggal pilih di pengaturan.'
            ],
            [
                'icon' => 'eye',
                'title' => 'Jawaban AI bisa kamu tinjau ulang',
                'body' => 'Semua jawaban tersimpan di riwayat, jadi kamu selalu tahu persis apa yang disampaikan atas nama kamu ke HRD.'
            ],
            [
                'icon' => 'bar-chart-3',
                'title' => '4 lamaran terkirim dalam 1 jam terakhir',
                'body' => 'Dashboard menampilkan progres secara real-time — sedang memindai, mengisi form, atau baru saja berhasil mengirim.'
            ],
            [
                'icon' => 'trending-up',
                'title' => 'Minggu ini: 38 lamaran, 5 direspon',
                'body' => 'Rekap performa lamaran kamu, biar tahu strategi mana yang jalan — bukan cuma asal kirim sebanyak-banyaknya.'
            ],
            [
                'icon' => 'shield-check',
                'title' => 'Akun kamu aman, kredensial terenkripsi',
                'body' => 'Data login hanya dipakai untuk proses apply ke platform lowongan, tidak dijual atau dibagikan ke pihak lain.'
            ],
        ];
    @endphp


    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

        @foreach ($notifs as $index => $n)

            <div
                data-aos="fade-up"
                data-aos-delay="{{ ($index % 3) * 100 }}"
                data-aos-duration="700"
                class="group rounded-2xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-blue-500/30 hover:bg-white/[0.05]"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-blue-500/10 text-blue-400 transition duration-300 group-hover:bg-blue-500/20"
                    >
                        <i
                            data-lucide="{{ $n['icon'] }}"
                            class="h-4 w-4 transition duration-300 group-hover:scale-110"
                        ></i>
                    </div>

                    <div class="min-w-0">

                        <p class="text-sm font-semibold leading-snug text-white">
                            {{ $n['title'] }}
                        </p>

                        <p class="mt-1.5 text-xs leading-relaxed text-[#a1a1aa]">
                            {{ $n['body'] }}
                        </p>

                    </div>

                </div>

            </div>

        @endforeach

    </div>
</section>


{{-- =========================================================
    DISCLAIMER
========================================================== --}}
<section class="mx-auto mt-16 max-w-4xl px-6">

    <div
        data-aos="fade-up"
        data-aos-duration="800"
        class="group relative overflow-hidden rounded-2xl border border-amber-500/20 bg-gradient-to-b from-amber-500/5 via-white/[0.02] to-transparent p-6 text-left backdrop-blur transition duration-300 hover:border-amber-500/30 sm:p-8"
    >

        <div
            class="absolute left-0 right-0 top-0 h-[1px] bg-gradient-to-r from-transparent via-amber-500/40 to-transparent"
        ></div>

        <div class="flex flex-col items-start gap-4 sm:flex-row sm:gap-6">

            <div
                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl border border-amber-500/20 bg-amber-500/10 text-amber-400 shadow-inner transition duration-300 group-hover:scale-105"
            >
                <i data-lucide="lightbulb" class="h-6 w-6"></i>
            </div>

            <div class="flex-1 space-y-3 text-sm leading-relaxed text-[#a1a1aa]">

                <h3 class="text-base font-semibold tracking-wide text-white sm:text-lg">
                    Penting
                </h3>

                <p class="text-sm sm:text-base">
                    Cari kerja sebagai fresh-grad itu tidaklah mudah, apalagi
                    hidup di Indonesia yang tiap lowongan bisa dilamar oleh
                    ratusan atau bahkan ribuan pelamar. Salah satu hal yang
                    paling realistis ialah memperbanyak jumlah lamaran guna
                    memperbesar peluang. Oleh karena itu Compass hadir untuk
                    membantu kamu dalam mengirim lamaran kerja secara otomatis.
                </p>

                <div class="flex items-center gap-2 border-t border-white/5 pt-3 text-xs text-[#88888e]">

                    <i
                        data-lucide="shield-alert"
                        class="h-4 w-4 flex-shrink-0 text-amber-400/80"
                    ></i>

                    <span>
                        <strong>Catatan:</strong>
                        Compass tidak menjamin kamu diterima kerja,
                        karena kunci diterima kerja tetap di CV,
                        portofolio, dan persiapan interview kamu.
                    </span>

                </div>

            </div>

        </div>

    </div>
</section>


{{-- =========================================================
    SOCIAL PROOF
========================================================== --}}
<section class="mx-auto mt-32 max-w-4xl px-6">

    <div
        class="mb-10 text-center"
        data-aos="fade-up"
        data-aos-duration="800"
    >

        <span
            class="inline-flex rounded-full border border-green-500/20 bg-green-500/10 px-4 py-1 text-xs font-medium text-green-400"
        >
            Bukti Nyata
        </span>

        <h2
            class="mx-auto mt-4 max-w-xl text-3xl font-bold leading-tight text-white sm:text-4xl"
        >
            Ini balasan asli HRD yang masuk ke user Compass.
        </h2>

        <p class="mx-auto mt-3 max-w-md text-sm text-[#a1a1aa]">
            Bukan mockup — ini rangkaian chat WhatsApp dan email
            konfirmasi interview yang datang sendiri.
        </p>

    </div>


    <div class="grid gap-6 sm:grid-cols-2">

        {{-- WhatsApp --}}
        <div
            data-aos="fade-right"
            data-aos-duration="800"
            class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] p-2 shadow-2xl shadow-black/40 transition duration-300 hover:-translate-y-1 hover:border-white/20"
        >

            <img
                src="{{ asset('images/proof-whatsapp.png') }}"
                alt="Bukti balasan HRD di WhatsApp setelah Compass auto-apply"
                class="w-full rounded-xl transition duration-500 group-hover:scale-[1.01]"
                loading="lazy"
            >

            <p class="mt-2 pb-1 text-center text-xs text-[#a1a1aa]">
                Balasan HRD via WhatsApp
            </p>

        </div>


        {{-- Email --}}
        <div
            data-aos="fade-left"
            data-aos-duration="800"
            data-aos-delay="100"
            class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] p-2 shadow-2xl shadow-black/40 transition duration-300 hover:-translate-y-1 hover:border-white/20"
        >

            <img
                src="{{ asset('images/proof-email.png') }}"
                alt="Bukti undangan interview via email setelah Compass auto-apply"
                class="w-full rounded-xl transition duration-500 group-hover:scale-[1.01]"
                loading="lazy"
            >

            <p class="mt-2 pb-1 text-center text-xs text-[#a1a1aa]">
                Undangan interview via Email
            </p>

        </div>

    </div>
</section>


{{-- =========================================================
    FAQ
========================================================== --}}
<section class="mx-auto mt-32 max-w-3xl px-6">

    <div
        class="mb-12 text-center"
        data-aos="fade-up"
        data-aos-duration="800"
    >

        <h2 class="text-3xl font-bold text-white sm:text-4xl">
            Sering Ditanyakan
        </h2>

        <p class="mt-2 text-sm text-[#a1a1aa]">
            Pertanyaan umum sebelum mencoba Compass.
        </p>

    </div>


    @php
        $faqs = [
            [
                'q' => 'Apakah AI bakal mengarang data/pengalaman saya?',
                'a' => 'Tidak. AI di Compass dikunci hanya untuk menjawab pertanyaan screening berdasarkan data riil dari CV dan profil yang kamu unggah.'
            ],
            [
                'q' => 'Apakah akun platform kerja saya aman?',
                'a' => 'Sangat aman. Seluruh data autentikasi disimpan menggunakan enkripsi tingkat tinggi dan hanya dipakai untuk sesi pengisian form otomatis.'
            ],
            [
                'q' => 'Bisakah saya membatasi kriteria lowongan?',
                'a' => 'Bisa. Kamu punya kendali penuh untuk mengatur posisi target, ekspektasi gaji minimum, hingga lokasi kerja yang kamu inginkan.'
            ],
            [
                'q' => 'Apakah ada garansi pasti diterima kerja?',
                'a' => 'Tidak ada. Compass fokus pada efisiensi proses pengiriman lamaran. Hasil panggilan dan interview tetap tergantung kualitas CV, portofolio, dan kecocokan kualifikasi kamu.'
            ],
        ];
    @endphp


    <div class="space-y-4">

        @foreach ($faqs as $index => $f)

            <div
                x-data="{ open: false }"
                data-aos="fade-up"
                data-aos-delay="{{ $index * 80 }}"
                data-aos-duration="600"
                class="overflow-hidden rounded-xl border border-white/10 bg-white/[0.02] transition duration-300 hover:border-white/20 hover:bg-white/[0.03]"
            >

                <button
                    type="button"
                    @click="open = !open"
                    class="flex w-full items-center justify-between gap-4 p-5 text-left text-sm font-medium text-white transition-colors duration-200 hover:text-blue-400"
                    :aria-expanded="open"
                >

                    <span>
                        {{ $f['q'] }}
                    </span>

                    <i
                        data-lucide="chevron-down"
                        class="h-4 w-4 flex-shrink-0 transition-transform duration-300 ease-out"
                        :class="{ 'rotate-180': open }"
                    ></i>

                </button>


                <div
                    x-show="open"
                    x-collapse.duration.400ms
                    class="px-5 pb-5"
                >

                    <div class="text-xs leading-relaxed text-[#a1a1aa]">
                        {{ $f['a'] }}
                    </div>

                </div>

            </div>

        @endforeach

    </div>

</section>


{{-- =========================================================
    BOTTOM CTA
========================================================== --}}
<section class="mx-auto mt-32 mb-16 max-w-4xl px-6">

    <div
        data-aos="zoom-in"
        data-aos-duration="800"
        class="group relative overflow-hidden rounded-3xl border border-blue-500/20 bg-gradient-to-b from-blue-500/10 to-transparent p-10 text-center transition duration-300 hover:border-blue-500/30"
    >

        <h2 class="text-2xl font-bold text-white sm:text-3xl">
            Siap hemat waktu buat nyari kerja?
        </h2>

        <p class="mx-auto mt-3 max-w-md text-xs text-[#a1a1aa]">
            Biarin Compass yang ngurusin rutinitas ngisi form,
            kamu fokus belajar interview dan poles portofolio.
        </p>

        <div class="mt-6">

            <a
                href="{{ route('register') }}"
                class="inline-flex h-11 items-center justify-center rounded-xl bg-[#fafafa] px-8 text-sm font-semibold text-black shadow-lg shadow-white/5 transition duration-300 hover:-translate-y-0.5 hover:bg-white"
            >
                Mulai Sekarang — Gratis
            </a>

        </div>

    </div>

</section>


{{-- =========================================================
    AOS + COUNTUP
========================================================== --}}
<link
    rel="stylesheet"
    href="https://unpkg.com/aos@2.3.4/dist/aos.css"
>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script src="https://cdn.jsdelivr.net/npm/countup.js@2.9.0/dist/countUp.umd.js"></script>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | AOS
        |--------------------------------------------------------------------------
        */

        AOS.init({
            once: true,
            duration: 700,
            easing: 'ease-out-cubic',
            offset: 80,
            disable: function () {
                return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            }
        });


        /*
        |--------------------------------------------------------------------------
        | CountUp
        |--------------------------------------------------------------------------
        |
        | Angka akan mulai dari 0 kemudian naik ke angka target.
        | Digabung dengan easing CountUp sehingga terasa seperti angka
        | sedang "diproses", bukan sekadar berubah instan.
        |
        */

        const counters = document.querySelectorAll('.countup');

        if (counters.length) {

            const observer = new IntersectionObserver(
                function (entries, observer) {

                    entries.forEach(function (entry) {

                        if (!entry.isIntersecting) {
                            return;
                        }

                        const element = entry.target;

                        if (element.dataset.counted === 'true') {
                            return;
                        }

                        element.dataset.counted = 'true';

                        const target = Number(element.dataset.countup);
                        const suffix = element.dataset.suffix || '';

                        const counter = new countUp.CountUp(
                            element,
                            target,
                            {
                                startVal: 0,
                                duration: 2.2,
                                useEasing: true,
                                useGrouping: false,
                                suffix: suffix
                            }
                        );

                        if (!counter.error) {
                            counter.start();
                        }

                        observer.unobserve(element);
                    });

                },
                {
                    threshold: 0.5
                }
            );


            counters.forEach(function (counter) {
                observer.observe(counter);
            });
        }

    });
</script>

@endsection
