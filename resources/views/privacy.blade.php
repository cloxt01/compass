@extends('layouts.guest')

@section('title', 'Privacy Policy')

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
                Privacy Policy
            </h1>

            <p class="mt-4 text-[#a1a1aa]">
                Last updated: {{ now()->format('F d, Y') }}
            </p>

        </div>

        <div class="mt-16 space-y-10 rounded-3xl border border-[#262626] bg-[#111111]/80 p-10 backdrop-blur">

            <section>
                <h2 class="text-xl font-semibold">Information We Collect</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    We collect account information, application preferences, connected provider credentials,
                    and activity required to operate Compass.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">How We Use Information</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Your information is used solely to provide automation features, improve reliability,
                    and maintain platform security.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Data Security</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    We implement reasonable technical and organizational safeguards to protect your information.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Third-Party Services</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Compass integrates with external job platforms. Their handling of your data is governed
                    by their own privacy policies.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold">Contact</h2>

                <p class="mt-3 text-[#a1a1aa] leading-8">
                    Questions regarding this Privacy Policy may be submitted through our support channels.
                </p>
            </section>

        </div>

    </section>
@endsection
