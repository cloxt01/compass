@extends('layouts.app')

@section('title','Packages')

@section('content')

    <div class="space-y-8">

        <div>

            <h1 class="text-2xl font-bold text-white">
                Subscription Packages
            </h1>

            <p class="mt-2 text-sm text-zinc-400">
                Pilih paket yang paling sesuai dengan kebutuhanmu.
            </p>

        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            @foreach($packages as $package)

                @php

                    $current = $subscription
                        && $subscription->package_id == $package->id;

                @endphp

                <div class="relative rounded-2xl border
                {{ $current ? 'border-indigo-500 bg-indigo-500/5' : 'border-zinc-800 bg-zinc-900' }}
                p-7">

                    @if($current)

                        <div class="absolute right-4 top-4 rounded-full bg-indigo-600 px-3 py-1 text-xs font-medium text-white">
                            Current Plan
                        </div>

                    @endif

                    <h2 class="text-xl font-semibold text-white">

                        {{ $package->name }}

                    </h2>

                    <div class="mt-6">

                    <span class="text-4xl font-bold text-white">

                        Rp {{ number_format($package->price,0,',','.') }}

                    </span>

                        <span class="text-zinc-500">

                        /{{ $package->duration_days }} hari

                    </span>

                    </div>

                    <div class="mt-8 space-y-3">

                        <div class="flex justify-between">

                        <span class="text-zinc-400">
                            Daily Apply
                        </span>

                            <span class="text-white">

                            {{ number_format($package->daily_limit) }}

                        </span>

                        </div>

                        <div class="flex justify-between">

                        <span class="text-zinc-400">
                            Monthly Apply
                        </span>

                            <span class="text-white">

                            {{ number_format($package->monthly_limit) }}

                        </span>

                        </div>

                    </div>

                    @if($package->features)

                        <div class="mt-8">

                            <p class="mb-3 text-xs uppercase tracking-widest text-zinc-500">
                                Features
                            </p>

                            <ul class="space-y-2">

                                @foreach(json_decode($package->features,true) as $feature)

                                    <li class="flex gap-2 text-sm text-zinc-300">

                                        <svg class="mt-1 h-4 w-4 text-emerald-500"
                                             fill="currentColor"
                                             viewBox="0 0 20 20">

                                            <path fill-rule="evenodd"
                                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                  clip-rule="evenodd"/>

                                        </svg>

                                        {{ $feature }}

                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    @endif

                        <div class="mt-8">

                            @if($current)

                                <button
                                    disabled
                                    class="w-full rounded-lg bg-zinc-700 py-3 text-sm font-medium text-zinc-400">

                                    Current Package

                                </button>

                            @else

                                <button
                                    type="button"
                                    class="subscribe-btn w-full rounded-lg bg-indigo-600 py-3 text-sm font-medium text-white transition hover:bg-indigo-500"
                                    data-package="{{ $package->id }}">

                                    Choose Package

                                </button>

                            @endif

                        </div>
                </div>

            @endforeach

        </div>

    </div>

@endsection
@push('scripts')
    <script>

        document.querySelectorAll('.subscribe-btn').forEach(button => {

            button.addEventListener('click', async function () {

                button.disabled = true;
                button.innerText = 'Loading...';

                try {

                    const response = await fetch(
                        "{{ route('payment.subscribe') }}",
                        {
                            method: "POST",

                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": document
                                    .querySelector('meta[name="csrf-token"]')
                                    .content
                            },

                            body: JSON.stringify({
                                package_id: this.dataset.package
                            })
                        }
                    );

                    const data = await response.json();


                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }

                    alert(data.message ?? "Gagal membuat transaksi.");

                } catch (e) {

                    console.error(e);

                    alert("Terjadi kesalahan.");

                }

                button.disabled = false;
                button.innerText = "Choose Package";

            });

        });

    </script>
@endpush
