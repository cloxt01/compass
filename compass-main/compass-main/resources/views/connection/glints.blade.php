@extends('layouts.app')

@section('title', 'Connect Glints · Compass')
@section('titleNavbar', 'Platform Connection')

@section('content')
    @php
        $provider = 'glints';
        $hasLogged = auth()->user()->glintsAccount;
    @endphp

    <div class="mx-auto max-w-[600px] space-y-6 px-1 pb-6 pt-6">

        {{-- ALERT / NOTIFIKASI ERROR ATAU STATUS --}}
        <div id="errors" class="space-y-2 text-sm text-red-400 empty:hidden"></div>
        <div id="status" class="text-sm text-blue-400 empty:hidden"></div>
        <div id="response" class="break-all font-mono text-xs text-[#a1a1aa] empty:hidden"></div>

        <div class="saas-card overflow-hidden">
            <div class="border-b border-[#262626] px-6 py-5">
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">
                    Hubungkan Akun Glints
                </h2>
                <p class="mt-1 text-sm text-[#a1a1aa]">
                    Masukkan email & password untuk mengizinkan bot berjalan otomatis di akun Anda
                </p>
            </div>

            <div class="p-6">

                @if($hasLogged)

                    <div class="rounded-xl border border-green-500/20 bg-green-500/10 p-6 text-center">

                        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-500/15">
                            <i class="fas fa-check text-lg text-green-400"></i>
                        </div>

                        <h3 class="text-base font-semibold text-[#fafafa]">
                            Akun Glints Sudah Terhubung
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-[#a1a1aa]">
                            Kamu sudah login ke akun Glints.
                            <br>
                            Silakan logout terlebih dahulu jika ingin mengganti akun.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('api.connection.disconnect', ['provider' => $provider]) }}"
                            class="mt-6">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="inline-flex cursor-pointer h-11 w-full items-center justify-center rounded-xl border border-[#333333] bg-[#1a1a1a] px-4 text-sm font-semibold text-[#fafafa] transition hover:border-[#4a4a4a] hover:bg-[#222222]">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout Akun
                            </button>
                        </form>

                    </div>

                @else

                    <form method="POST"
                          action="{{ route('api.connection.login', ['provider' => $provider]) }}"
                          id="loginForm"
                          class="space-y-4">
                        @csrf

                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                required
                                placeholder="name@example.com"
                                class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]">
                        </div>

                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                required
                                placeholder="Masukkan password Glints"
                                class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]">
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white">
                                Login & Hubungkan
                            </button>
                        </div>
                    </form>

                @endif

            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('apply') }}" class="text-xs text-[#a1a1aa] hover:text-[#fafafa] transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Panel Utama
            </a>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Geist', system-ui, sans-serif; background:#0A0A0A; color:#FAFAFA; }
        .font-mono { font-family: 'Geist Mono', monospace !important; }
        .saas-card { background:#111111; border:1px solid #262626; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.28); transition:all .2s ease; }
        .saas-card:hover { border-color:#333333; }
        .saas-input { border:1px solid #262626; background:#0A0A0A; outline:0; transition:.2s ease; }
        .saas-input:focus { border-color:#3B82F6; box-shadow:0 0 0 2px rgba(59,130,246,.15); }
    </style>
@endpush
