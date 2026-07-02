<x-guest-layout>
    <div class="mx-auto max-w-[440px] space-y-6 pt-12 pb-6">

        {{-- CARD CONTAINER --}}
        <div class="saas-card overflow-hidden">

            {{-- HEADER --}}
            <div class="border-b border-[#262626] px-6 py-5">
                <h1 class="text-lg font-semibold tracking-tight text-[#fafafa]">Lupa Password</h1>
                <p class="mt-1 text-sm text-[#a1a1aa]">
                    {{ __('Masukkan email Anda untuk menerima link reset password.') }}
                </p>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">
                <div class="text-xs leading-relaxed text-[#a1a1aa]">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4 text-xs text-emerald-400" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Email Address') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="name@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-400" />
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="pt-2">
                        <button type="submit" class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white">
                            {{ __('Email Password Reset Link') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- FOOTER / BACK TO LOGIN LINK --}}
        <p class="text-center text-xs text-[#a1a1aa]">
            <a href="{{ route('login') }}" class="font-medium text-[#fafafa] hover:underline transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke halaman login
            </a>
        </p>
    </div>
</x-guest-layout>
