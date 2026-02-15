@extends('layouts.appGuest')

@section('title', 'Home - Compass')

@section('content')
<!-- Hero Section -->
<section class="w-full bg-gradient-to-br from-[#161b22] to-[#0d1117] py-24 md:py-32 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-radial from-[#58a6ff]/20 to-transparent rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-radial from-[#58a6ff]/15 to-transparent rounded-full blur-3xl -z-10"></div>
    
    <div class="max-w-6xl mx-auto px-6 text-center relative z-10">
        <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-[#58a6ff] to-[#79c0ff] bg-clip-text text-transparent">
            Job Application Intelligence
        </h1>
        <p class="text-2xl md:text-3xl text-[#8b949e] mb-6 font-light">
            Tingkatkan kesempatan karir Anda dengan smart automation
        </p>
        <p class="text-lg text-[#8b949e] mb-10 max-w-2xl mx-auto leading-relaxed">
            Compass mengotomatisasi proses aplikasi pekerjaan Anda dengan AI yang cerdas. 
            Terapkan dengan presisi ke ratusan posisi dalam hitungan menit.
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
            <a href="{{ route('register') ?? '#' }}" class="px-8 py-3 bg-[#238636] text-white font-semibold rounded-lg hover:bg-[#2ea043] transition duration-200 shadow-lg">
                Mulai Sekarang Gratis
            </a>
            <a href="#" class="px-8 py-3 border border-[#30363d] text-[#58a6ff] font-semibold rounded-lg hover:border-[#58a6ff] hover:bg-[#0d1117] transition duration-200">
                ◆ Lihat Demo
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 border-t border-[#30363d] pt-12">
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-bold text-[#58a6ff] mb-2">10K+</div>
                <div class="text-sm text-[#8b949e]">Aplikasi Terproses</div>
            </div>
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-bold text-[#58a6ff] mb-2">95%</div>
                <div class="text-sm text-[#8b949e]">Tingkat Keberhasilan</div>
            </div>
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-bold text-[#58a6ff] mb-2">24/7</div>
                <div class="text-sm text-[#8b949e]">Dukungan Otomatis</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="w-full py-24 md:py-32 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16 md:mb-20">
            <h2 class="text-4xl md:text-5xl font-bold text-[#c9d1d9] mb-4">Fitur Unggulan</h2>
            <p class="text-lg text-[#8b949e] max-w-2xl mx-auto">Semua yang Anda butuhkan untuk mendominasi pasar kerja</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Card 1 -->
            <div class="group p-8 border border-[#30363d] rounded-xl bg-[#0d1117] hover:border-[#58a6ff] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">⚡</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">Smart Application</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">AI menganalisis lowongan kerja dan secara otomatis mengajukan aplikasi yang sesuai dengan profil Anda dengan akurasi tinggi.</p>
            </div>

            <!-- Card 2 -->
            <div class="group p-8 border border-[#30363d] rounded-xl bg-[#0d1117] hover:border-[#58a6ff] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">🔐</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">Secure Integration</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">Terintegrasi aman dengan platform rekrutmen major. Data Anda dienkripsi end-to-end dan tidak pernah dibagikan.</p>
            </div>

            <!-- Card 3 -->
            <div class="group p-8 border border-[#30363d] rounded-xl bg-[#0d1117] hover:border-[#58a6ff] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">Real-time Analytics</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">Dashboard komprehensif menampilkan status aplikasi, interview invites, dan insights mendalam tentang prospek karir Anda.</p>
            </div>

            <!-- Card 4 -->
            <div class="group p-8 border border-[#30363d] rounded-xl bg-[#0d1117] hover:border-[#58a6ff] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">🎯</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">Precision Matching</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">Engine matching khusus hanya menargetkan posisi yang selaras dengan keinginan, industri, dan range gaji Anda.</p>
            </div>

            <!-- Card 5 -->
            <div class="group p-8 border border-[#30363d] rounded-xl bg-[#0d1117] hover:border-[#58a6ff] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">🤖</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">AI-Powered Resume</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">Otomatis optimize resume Anda untuk setiap aplikasi tanpa mengubah konten asli. Tayang lebih baik di ATS system.</p>
            </div>

            <!-- Card 6 -->
            <div class="group p-8 border border-[#30363d] rounded-xl bg-[#0d1117] hover:border-[#58a6ff] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">⏰</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">24/7 Automation</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">Sistem berjalan 24/7 bahkan saat Anda tidur. Tidak perlu intervensi manual—semua berjalan secara otomatis dan cerdas.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section id="howitworks" class="w-full bg-[#161b22] py-24 md:py-32 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16 md:mb-20">
            <h2 class="text-4xl md:text-5xl font-bold text-[#c9d1d9] mb-4">Cara Kerja</h2>
            <p class="text-lg text-[#8b949e] max-w-2xl mx-auto">Tiga langkah sederhana untuk memulai karir impian Anda</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            <!-- Step 1 -->
            <div>
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-[#58a6ff] text-white font-bold text-lg mb-4">1</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">Setup Profil</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">Buat akun, upload resume, dan atur preferensi pekerjaan Anda. Prosesnya cepat dan mudah—hanya membutuhkan 5 menit.</p>
            </div>

            <!-- Step 2 -->
            <div>
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-[#58a6ff] text-white font-bold text-lg mb-4">2</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">Aktifkan Automation</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">Pilih kriteria pencarian dan biarkan AI Compass menganalisis pasar. Sistem otomatis menemukan dan mengajukan aplikasi untuk Anda.</p>
            </div>

            <!-- Step 3 -->
            <div>
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-[#58a6ff] text-white font-bold text-lg mb-4">3</div>
                <h3 class="text-lg font-semibold text-[#c9d1d9] mb-3">Terima Offers</h3>
                <p class="text-[#8b949e] text-sm leading-relaxed">Monitor dashboard real-time untuk interview invites dan offers. Kami memberitahu Anda instans saat ada peluang menarik.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="w-full border-y border-[#30363d] py-16 md:py-20 px-6">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-[#c9d1d9] mb-4">Siap Mengubah Karir Anda?</h2>
        <p class="text-lg text-[#8b949e] mb-8">Bergabunglah dengan ribuan profesional yang telah menemukan pekerjaan impian mereka melalui Compass</p>
        <a href="{{ route('register') ?? '#' }}" class="inline-block px-8 py-3 bg-[#238636] text-white font-semibold rounded-lg hover:bg-[#2ea043] transition duration-200 shadow-lg">
            Mulai Sekarang—Gratis
        </a>
    </div>
</section>

<!-- Footer -->
<footer class="w-full bg-[#161b22] border-t border-[#30363d] py-12 px-6 mt-20">
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="text-sm font-semibold text-[#c9d1d9] mb-4">Produk</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Fitur</a></li>
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Pricing</a></li>
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Security</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-[#c9d1d9] mb-4">Perusahaan</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Tentang</a></li>
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Blog</a></li>
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Karir</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-[#c9d1d9] mb-4">Legal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Privacy</a></li>
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Terms</a></li>
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">License</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-[#c9d1d9] mb-4">Kontak</h4>
                <ul class="space-y-2">
                    <li><a href="mailto:support@compass.app" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">support@compass.app</a></li>
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">Twitter</a></li>
                    <li><a href="#" class="text-sm text-[#8b949e] hover:text-[#58a6ff] transition">LinkedIn</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-[#30363d] pt-6 text-center">
            <p class="text-xs text-[#8b949e]">&copy; 2026 Compass. Built with ❤️ for your career.</p>
        </div>
    </div>
</footer>
@endsection