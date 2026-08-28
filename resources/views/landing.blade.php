@extends('layouts.guest')

@section('title', 'Compass — Auto Apply Kerja Buat Freshgrad')

@section('body-class', 'bg-[#0a0a0a] text-[#fafafa] antialiased')

@section('main-class', 'flex flex-1 flex-col justify-center')

@section('background')
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-[#111827] via-[#0a0a0a] to-[#0a0a0a]"></div>
        <div class="absolute left-1/2 top-0 h-[500px] w-[500px] -translate-x-1/2 rounded-full bg-blue-500/10 blur-[140px]"></div>
    </div>
@endsection

@section('content')
    {{-- Hero Section --}}
    <section
        x-data="{ shown: false }"
        x-init="setTimeout(() => shown = true, 100)"
        class="mx-auto flex max-w-2xl flex-col items-center px-6 text-center"
    >
        <div
            x-show="shown"
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-1.5 text-xs font-medium text-blue-400"
        >
            <i data-lucide="graduation-cap" class="h-4 w-4"></i>
            <span>Untuk freshgrad yang capek apply satu-satu</span>
        </div>

        <h1
            x-show="shown"
            x-transition:enter="transition ease-out duration-700 delay-150"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mt-6 text-4xl font-bold leading-tight tracking-tight text-[#fafafa] sm:text-6xl"
        >
            Biar Compass yang<br>
            kirim lamarannya.
        </h1>

        <p
            x-show="shown"
            x-transition:enter="transition ease-out duration-700 delay-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mt-4 max-w-lg text-sm leading-relaxed text-[#a1a1aa] sm:text-base"
        >
            Cari kerja itu melelahkan, apalagi kalau buka platform pencari kerja tiap hari dan isi form
            yang isinya itu-itu aja. Compass bantu kirim lamaran ke lebih banyak lowongan secara otomatis,
            biar waktu kamu bisa dipakai buat hal yang lebih penting — nyiapin CV, portofolio, atau latihan
            interview.
        </p>

        <div
            x-show="shown"
            x-transition:enter="transition ease-out duration-700 delay-500"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="mt-8 flex flex-wrap items-center justify-center gap-4"
        >
            <a
                href="{{ route('register') }}"
                class="inline-flex h-11 items-center justify-center rounded-xl bg-[#fafafa] px-6 text-sm font-semibold text-black shadow-lg shadow-white/5 transition hover:-translate-y-0.5 hover:bg-white"
            >
                Coba Sekarang
            </a>

            <a
                href="#features"
                class="inline-flex h-11 items-center justify-center rounded-xl border border-[#262626] bg-[#0a0a0a]/60 px-6 text-sm font-semibold text-[#a1a1aa] transition hover:bg-[#1e1e1e]/80 hover:text-[#fafafa]"
            >
                Liat Cara Kerjanya
            </a>
        </div>
    </section>

    {{-- Impact Stats --}}
    <section class="mx-auto mt-24 max-w-4xl px-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 text-center">
            <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-6 backdrop-blur">
                <p class="text-3xl font-bold text-white sm:text-4xl">50+ Jam</p>
                <p class="mt-2 text-xs text-[#a1a1aa]">Waktu dihemat per minggu dari ngisi form manual</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-6 backdrop-blur">
                <p class="text-3xl font-bold text-blue-400 sm:text-4xl">100%</p>
                <p class="mt-2 text-xs text-[#a1a1aa]">Kriteria lowongan difilter presisi pakai kriteria kamu</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-6 backdrop-blur">
                <p class="text-3xl font-bold text-white sm:text-4xl">24/7</p>
                <p class="mt-2 text-xs text-[#a1a1aa]">Bot memantau lowongan baru yang pas tiap menit</p>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="mx-auto mt-32 max-w-5xl px-6 lg:px-8">
        <div class="mb-14 text-center">
            <span class="inline-flex rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-1 text-xs font-medium text-blue-400">
                Langkah Cepat
            </span>
            <h2 class="mx-auto mt-4 text-3xl font-bold text-white sm:text-4xl">
                Cuma 3 langkah buat jalanin otomatisasi.
            </h2>
        </div>

        <div class="grid gap-8 sm:grid-cols-3">
            <div class="relative rounded-2xl border border-white/10 bg-white/[0.02] p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-sm font-bold text-blue-400">
                    01
                </div>
                <h3 class="mt-4 text-base font-semibold text-white">Atur Profil</h3>
                <p class="mt-2 text-xs leading-relaxed text-[#a1a1aa]">
                    Atur profil kamu mulai dari range gaji, domisili, skills, sertifikat, dan pengalaman kerja. Compass bakal pakai data ini buat menjawab pertanyaan screening .
                </p>
            </div>

            <div class="relative rounded-2xl border border-white/10 bg-white/[0.02] p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-sm font-bold text-blue-400">
                    02
                </div>
                <h3 class="mt-4 text-base font-semibold text-white">Pilih Model AI</h3>
                <p class="mt-2 text-xs leading-relaxed text-[#a1a1aa]">
                    Pilih model AI favorit kamu untuk menjawab pertanyaan screening HRD sesuai pengalaman nyata di CV.
                </p>
            </div>

            <div class="relative rounded-2xl border border-white/10 bg-white/[0.02] p-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-sm font-bold text-blue-400">
                    03
                </div>
                <h3 class="mt-4 text-base font-semibold text-white">Biarkan Bot Bekerja</h3>
                <p class="mt-2 text-xs leading-relaxed text-[#a1a1aa]">
                    Compass memindai lowongan baru, melakukan apply otomatis, dan kamu tinggal pantau hasilnya via dashboard.
                </p>
            </div>
        </div>
    </section>

    {{-- Features / Notifications Grid --}}
    <section id="features" class="mx-auto mt-32 w-full max-w-5xl px-6 lg:px-8">
        <div class="mb-14 text-center">
            <span class="inline-flex rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-1 text-xs font-medium text-blue-400">
                Notifikasi dari Compass
            </span>
            <h2 class="mx-auto mt-5 max-w-2xl text-3xl font-bold leading-tight text-white sm:text-4xl">
                Ini yang Compass kerjakan di belakang layar.
            </h2>
            <p class="mx-auto mt-3 max-w-md text-sm text-[#a1a1aa]">
                Ditampilkan seperti notifikasi biar gampang dibayangin — tiap proses berjalan otomatis, tanpa
                kamu harus mantengin dashboard terus-menerus.
            </p>
        </div>

        @php
            $notifs = [
                ['icon' => 'search', 'title' => '12 lowongan baru yang cocok sama kamu', 'body' => 'Compass memantau lowongan baru terus-menerus, sesuai kriteria yang kamu atur — posisi, lokasi, sampai gaji minimal.'],
                ['icon' => 'brain', 'title' => 'Lowongan "min. 3 tahun pengalaman" dilewati', 'body' => 'Lowongan yang jelas gak cocok sama profil kamu otomatis difilter, biar gak buang waktu apply ke tempat yang jelas-jelas nolak freshgrad.'],
                ['icon' => 'send', 'title' => 'Lamaran ke PT Maju Jaya berhasil dikirim', 'body' => 'Form dan dokumen terisi otomatis pakai data kamu, gak perlu isi ulang form yang sama di tiap lowongan.'],
                ['icon' => 'message-square', 'title' => 'Pertanyaan screening sudah dijawab', 'body' => 'AI menjawab pertanyaan seperti "kenapa kamu cocok untuk posisi ini?" berdasarkan profil yang kamu atur di pengaturan — bukan mengarang pengalaman biar terlihat cocok.'],
                ['icon' => 'settings', 'title' => 'Model AI bisa kamu ganti kapan saja', 'body' => 'Mau pakai AI yang mana untuk menjawab pertanyaan screening, tinggal pilih di pengaturan.'],
                ['icon' => 'eye', 'title' => 'Jawaban AI bisa kamu tinjau ulang', 'body' => 'Semua jawaban tersimpan di riwayat, jadi kamu selalu tahu persis apa yang disampaikan atas nama kamu ke HRD.'],
                ['icon' => 'bar-chart-3', 'title' => '4 lamaran terkirim dalam 1 jam terakhir', 'body' => 'Dashboard menampilkan progres secara real-time — sedang memindai, mengisi form, atau baru saja berhasil mengirim.'],
                ['icon' => 'trending-up', 'title' => 'Minggu ini: 38 lamaran, 5 direspon', 'body' => 'Rekap performa lamaran kamu, biar tahu strategi mana yang jalan — bukan cuma asal kirim sebanyak-banyaknya.'],
                ['icon' => 'shield-check', 'title' => 'Akun kamu aman, kredensial terenkripsi', 'body' => 'Data login hanya dipakai untuk proses apply ke platform lowongan, tidak dijual atau dibagikan ke pihak lain.'],
            ];
        @endphp

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($notifs as $n)
                <div
                    x-data="{ visible: false }"
                    x-intersect.once="visible = true"
                    x-show="visible"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="rounded-2xl border border-white/10 bg-white/[0.03] p-5 backdrop-blur transition hover:border-blue-500/30 hover:bg-white/[0.05]"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-blue-500/10 text-blue-400">
                            <i data-lucide="{{ $n['icon'] }}" class="h-4 w-4"></i>
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

        {{-- Disclaimer Box --}}
    <section class="mx-auto mt-16 max-w-4xl px-6">
        <div class="relative overflow-hidden rounded-2xl border border-amber-500/20 bg-gradient-to-b from-amber-500/5 via-white/[0.02] to-transparent p-6 sm:p-8 text-left backdrop-blur">
            <!-- Top Accent Line -->
            <div class="absolute top-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-amber-500/40 to-transparent"></div>

            <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-6">
                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl border border-amber-500/20 bg-amber-500/10 text-amber-400 shadow-inner">
                    <i data-lucide="lightbulb" class="h-6 w-6"></i>
                </div>
                
                <div class="space-y-3 text-sm leading-relaxed text-[#a1a1aa] flex-1">
                    <h3 class="text-base sm:text-lg font-semibold text-white tracking-wide">
                        Realita singkat soal Compass.
                    </h3>
                    <p class="text-sm sm:text-base">
                        Ngetok pintu perusahaan satu per satu memang makan waktu. Compass hadir buat <span class="text-amber-300 font-medium">buka pintunya sebanyak mungkin</span> lewat otomatisasi lamaran, biar peluang kamu dipanggil interview makin besar.
                    </p>
                    <div class="pt-3 border-t border-white/5 text-xs text-[#88888e] flex items-center gap-2">
                        <i data-lucide="shield-alert" class="h-4 w-4 text-amber-400/80 flex-shrink-0"></i>
                        <span><strong>Catatan:</strong> Compass bantu di urusan pengiriman form. Kunci diterima kerja tetap di CV, portofolio, dan persiapan interview kamu.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="mx-auto mt-32 max-w-3xl px-6">
        <div class="mb-12 text-center">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Sering Ditanyakan</h2>
            <p class="mt-2 text-sm text-[#a1a1aa]">Pertanyaan umum sebelum mencoba Compass.</p>
        </div>

        @php
            $faqs = [
                ['q' => 'Apakah AI bakal mengarang data/pengalaman saya?', 'a' => 'Tidak. AI di Compass dikunci hanya untuk menjawab pertanyaan screening berdasarkan data riil dari CV dan profil yang kamu unggah.'],
                ['q' => 'Apakah akun platform kerja saya aman?', 'a' => 'Sangat aman. Seluruh data autentikasi disimpan menggunakan enkripsi tingkat tinggi dan hanya dipakai untuk sesi pengisian form otomatis.'],
                ['q' => 'Bisakah saya membatasi kriteria lowongan?', 'a' => 'Bisa. Kamu punya kendali penuh untuk mengatur posisi target, ekspektasi gaji minimum, hingga lokasi kerja yang kamu inginkan.'],
                ['q' => 'Apakah ada garansi pasti diterima kerja?', 'a' => 'Tidak ada. Compass fokus pada efisiensi proses pengiriman lamaran. Hasil panggilan dan interview tetap tergantung kualitas CV, portofolio, dan kecocokan kualifikasi kamu.'],
            ];
        @endphp

        <div class="space-y-4">
            @foreach ($faqs as $f)
                <div x-data="{ open: false }" class="rounded-xl border border-white/10 bg-white/[0.02]">
                    <button @click="open = !open" class="flex w-full items-center justify-between p-5 text-left text-sm font-medium text-white hover:text-blue-400 transition">
                        <span>{{ $f['q'] }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-collapse class="px-5 pb-5 text-xs leading-relaxed text-[#a1a1aa]">
                        {{ $f['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    

    {{-- Bottom CTA Banner --}}
    <section class="mx-auto mt-32 mb-16 max-w-4xl px-6">
        <div class="relative overflow-hidden rounded-3xl border border-blue-500/20 bg-gradient-to-b from-blue-500/10 to-transparent p-10 text-center">
            <h2 class="text-2xl font-bold text-white sm:text-3xl">Siap hemat waktu buat nyari kerja?</h2>
            <p class="mx-auto mt-3 max-w-md text-xs text-[#a1a1aa]">
                Biarin Compass yang ngurusin rutinitas ngisi form, kamu fokus belajar interview dan poles portofolio.
            </p>
            <div class="mt-6">
                <a
                    href="{{ route('register') }}"
                    class="inline-flex h-11 items-center justify-center rounded-xl bg-[#fafafa] px-8 text-sm font-semibold text-black shadow-lg shadow-white/5 transition hover:-translate-y-0.5 hover:bg-white"
                >
                    Mulai Sekarang — Gratis
                </a>
            </div>
        </div>
    </section>
@endsection