<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Compass</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Menggunakan font Geist biar senada dengan dashboard */
        @import url('https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap');
        body { font-family: 'Geist', sans-serif; }
    </style>
</head>
<body class="bg-[#0a0a0a] text-[#fafafa] overflow-hidden antialiased">

<div class="absolute inset-0 z-0 h-full w-full overflow-hidden">
    <video
        autoplay
        loop
        muted
        playsinline
        class="h-full w-full object-cover opacity-50"
    >
        <source src="{{ asset('assets/video/background.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    {{-- Menggunakan backdrop-blur untuk memberikan kesan kaca modern (glassmorphism) --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-[#0a0a0a]/40 to-[#0a0a0a] backdrop-blur-[2px]"></div>
</div>

<main class="relative z-10 flex h-screen flex-col justify-between px-6 py-8 md:px-12">

    <header class="flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            {{-- Gunakan logo dari config ui kamu --}}
            <img src="{{ asset(config('ui.brand.logo', 'assets/img/logo.png')) }}" alt="Logo" class="h-7 w-7 rounded-md border border-[#333]" />
            <span class="text-sm font-semibold tracking-wide text-[#fafafa]">{{ config('ui.brand.name', 'Compass') }}</span>
        </div>

        <a href="{{ route('login') }}" class="inline-flex h-9 items-center justify-center rounded-md bg-[#1e1e1e] border border-[#333] px-4 text-xs font-semibold text-[#fafafa] hover:bg-[#262626] transition">
            Sign In
        </a>
    </header>

    <section class="mx-auto flex max-w-2xl flex-col items-center text-center">
        <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/10 px-3 py-1 text-xs font-medium text-blue-400">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
            <span>Next-Gen Enterprise Automation</span>
        </div>

        <h1 class="mt-6 text-4xl font-bold tracking-tight text-[#fafafa] sm:text-6xl bg-gradient-to-b from-[#fafafa] to-[#a1a1aa] bg-clip-text text-transparent leading-tight">
            Automate Your Job Application Workflow
        </h1>

        <p class="mt-4 text-sm text-[#a1a1aa] sm:text-base max-w-lg leading-relaxed">
            Let AI-powered pipelines seek out opportunities, inspect job requirements, and manage deployments smoothly across multiple providers.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('register') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#fafafa] px-6 text-sm font-semibold text-black hover:bg-white transition shadow-lg shadow-white/5">
                Get Started Free
            </a>
            <a href="#features" class="inline-flex h-11 items-center justify-center rounded-xl border border-[#262626] bg-[#0a0a0a]/60 px-6 text-sm font-semibold text-[#a1a1aa] hover:text-[#fafafa] hover:bg-[#1e1e1e]/80 transition">
                Learn More
            </a>
        </div>
    </section>

    <footer class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between text-xs text-[#71717a]">
        <div>
            &copy; {{ date('Y') }} {{ config('ui.brand.name', 'Compass') }}. All rights reserved.
        </div>
        <div class="flex items-center gap-4">
            <a href="#" class="hover:text-[#a1a1aa] transition">Terms</a>
            <a href="#" class="hover:text-[#a1a1aa] transition">Privacy</a>
            <span class="h-1 w-1 rounded-full bg-[#333]"></span>
            <span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span> All systems operational</span>
        </div>
    </footer>

</main>

<script>
    // Jalankan Lucide Icons
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
</body>
</html>
