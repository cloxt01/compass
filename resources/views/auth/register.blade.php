@extends('layouts.guest-auth')

@section('content')
    <div class="mx-auto max-w-[440px] space-y-6 pt-12 pb-6">

        {{-- CARD CONTAINER --}}
        <div class="saas-card overflow-hidden">

            {{-- HEADER --}}
            <div class="border-b border-[#262626] px-6 py-5">
                <h1 class="text-lg font-semibold tracking-tight text-[#fafafa]">Buat akun baru</h1>
                <p class="mt-1 text-sm text-[#a1a1aa]">Mulai setup automasi lamaran kerja kamu sekarang.</p>
            </div>

            {{-- BODY --}}
            <div class="p-6">
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Nama') }}</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="Nama Lengkap" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Alamat Email') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="name@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="Minimal 8 karakter" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Konfirmasi Password') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="Ulangi password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-400" />
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="pt-2">
                        <button type="submit" class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white">
                            {{ __('Daftar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- FOOTER / LOGIN LINK --}}
        <p class="text-center text-xs text-[#a1a1aa]">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-medium text-[#fafafa] hover:underline transition">
                {{ __('Login disini') }}
            </a>
        </p>
    </div>
@endsection
