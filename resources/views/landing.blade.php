@extends('layouts.guest')

@section('title', 'Welcome to Compass')

@section('body-class', 'bg-[#0a0a0a] text-[#fafafa] overflow-hidden antialiased')

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
            Automate Your Job Application Workflow
        </h1>

        <p class="mt-4 max-w-lg text-sm leading-relaxed text-[#a1a1aa] opacity-0 animate-fade-in-bounce delay-400 sm:text-base">
            Let AI-powered pipelines seek out opportunities, inspect job requirements,
            and manage deployments smoothly across multiple providers.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-4 opacity-0 animate-fade-in-bounce delay-600">

            <a
                href="{{ route('register') }}"
                class="inline-flex h-11 items-center justify-center rounded-xl bg-[#fafafa] px-6 text-sm font-semibold text-black shadow-lg shadow-white/5 transition hover:-translate-y-0.5 hover:bg-white"
            >
                Get Started Free
            </a>

            <a
                href="#features"
                class="inline-flex h-11 items-center justify-center rounded-xl border border-[#262626] bg-[#0a0a0a]/60 px-6 text-sm font-semibold text-[#a1a1aa] transition hover:bg-[#1e1e1e]/80 hover:text-[#fafafa]"
            >
                Learn More
            </a>

        </div>

    </section>
@endsection
