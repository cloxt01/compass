@extends('layouts.guest')

@php
    $tanggal = '2024-06-01';
    $lastUpdated = date("F d, Y", strtotime($tanggal));;
@endphp

@section('title', 'Terms of Service')

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
                Terms of Service
            </h1>

            <p class="mt-4 text-[#a1a1aa]">
                Last updated: {{ $lastUpdated  }}

            </p>
        </div>

        <div class="mt-16 space-y-10 rounded-3xl border border-[#262626] bg-[#111111]/80 p-10 backdrop-blur">

            <section>
                <h2 class="text-xl font-semibold">1. Acceptance</h2>
                <p class="mt-3 text-[#a1a1aa] leading-8">
                    By accessing or using Compass, you agree to be bound by these Terms.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">2. Accounts</h2>
                <p class="mt-3 text-[#a1a1aa] leading-8">
                    You are responsible for maintaining the security of your account and credentials.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">3. Automation</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass automates job application workflows based on your own configuration.
                    You remain responsible for reviewing how automation is used and complying with
                    the policies of third-party job platforms.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">4. Availability</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    We strive to provide reliable service but cannot guarantee uninterrupted availability.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">5. Termination</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    We may suspend or terminate accounts that abuse the platform or violate these Terms.
                </p>
            </section>

        </div>

    </section>
@endsection
