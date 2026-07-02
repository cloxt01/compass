<x-guest-layout>
    <div class="mx-auto max-w-[440px] space-y-6 pt-12 pb-6">

        {{-- CARD CONTAINER --}}
        <div class="saas-card overflow-hidden">

            {{-- HEADER --}}
            <div class="border-b border-[#262626] px-6 py-5">
                <h1 class="text-lg font-semibold tracking-tight text-[#fafafa]">Masuk ke akun</h1>
                <p class="mt-1 text-sm text-[#a1a1aa]">Kelola automasi job application kamu dari satu dashboard.</p>
            </div>

            {{-- BODY --}}
            <div class="p-6">
                <x-auth-session-status class="mb-4 text-xs text-emerald-400" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Email Address') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="name@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Password') }}</label>
                            @if (Route::has('password.request'))
                                <a class="text-xs text-[#a1a1aa] hover:text-[#fafafa] transition" href="{{ route('password.request') }}">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="Masukkan password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <div class="block pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-[#a1a1aa] cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="h-4 w-4 rounded border-[#3f3f3f] bg-[#0a0a0a] text-blue-600 focus:ring-0 cursor-pointer">
                            <span class="text-xs">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="pt-2">
                        <button type="submit" class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white">
                            {{ __('Log in') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- FOOTER / REGISTER LINK --}}
        @if (Route::has('register'))
            <p class="text-center text-xs text-[#a1a1aa]">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-medium text-[#fafafa] hover:underline transition">
                    Daftar sekarang
                </a>
            </p>
        @endif
    </div>
</x-guest-layout>
