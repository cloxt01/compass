@extends('layouts.app')

@php
    $provider = 'jobstreet';
    $isConnected = auth()->user()->jobstreetAccount;
@endphp

@section('title', 'Connect JobStreet · Compass')
@section('titleNavbar', 'Platform Connection')

@section('content')
    <div class="mx-auto max-w-[700px] space-y-6 px-1 pb-6 pt-6">

        <div id="status" class="text-sm text-blue-400 empty:hidden"></div>
        <div id="errors" class="space-y-2 text-sm text-red-400 empty:hidden"></div>

        <div class="saas-card overflow-hidden">

            <div class="border-b border-[#262626] px-6 py-5">
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">
                    JobStreet Connection
                </h2>

                <p class="mt-1 text-sm text-[#a1a1aa]">
                    @if($isConnected)
                        Akun JobStreet kamu sudah berhasil terhubung dengan Compass.
                    @else
                        Hubungkan akun JobStreet untuk mulai menggunakan fitur automation.
                    @endif
                </p>
            </div>

            <div class="space-y-6 p-6">

                @if($isConnected)

                    <div class="flex items-center gap-3 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                        <span>
                            Terhubung sebagai
                            <strong>{{ auth()->user()->email }}</strong>
                        </span>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('api.connection.disconnect', ['provider' => $provider]) }}"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            class="h-11 w-full rounded-xl border border-red-500/40 bg-transparent text-sm font-medium text-red-400 transition hover:bg-red-500/10"
                        >
                            Putuskan Koneksi
                        </button>
                    </form>

                @else

                    <div class="rounded-xl border border-violet-500/20 bg-violet-500/5 p-5">

                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-violet-400">
                                    Hubungkan dengan CompassLink
                                </h3>

                                <p class="mt-2 text-sm leading-6 text-[#a1a1aa]">
                                    Untuk menghubungkan akun <span class="text-[#fafafa] font-medium">JobStreet</span>,
                                    diperlukan browser extension <span class="text-[#fafafa] font-medium">CompassLink</span>.
                                    Silakan install extension terlebih dahulu, kemudian ikuti proses koneksi melalui halaman CompassLink.
                                </p>
                            </div>

                            <span class="shrink-0 rounded-full border border-violet-500/30 bg-violet-500/10 px-3 py-1 text-xs font-medium text-violet-400">
            Required
        </span>
                        </div>

                        <div class="mt-5 flex gap-3">
                            <a
                                href="{{ route('products.compass-link') }}"
                                class="flex-1 rounded-xl bg-white py-3 text-center text-sm font-semibold text-black transition hover:bg-neutral-200"
                            >
                                Buka CompassLink
                            </a>
                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>
@endsection
