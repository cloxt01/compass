<x-guest-layout>
    <div class="mx-auto max-w-[440px] space-y-6 pt-12 pb-6">

        {{-- CARD CONTAINER --}}
        <div class="saas-card overflow-hidden">

            {{-- HEADER --}}
            <div class="border-b border-[#262626] px-6 py-5">
                <h1 class="text-lg font-semibold tracking-tight text-[#fafafa]">Reset Password</h1>
                <p class="mt-1 text-sm text-[#a1a1aa]">
                    {{ __('Silakan masukkan password baru Anda di bawah ini.') }}
                </p>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">

                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div>
                        <label for="email" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Email Address') }}</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="name@example.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('New Password') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-400" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Confirm New Password') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs text-red-400" />
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="pt-2">
                        <button type="submit" class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- FOOTER / BACK TO LOGIN LINK --}}
        <p class="text-center text-xs text-[#a1a1aa]">
            <a href="{{ route('login') }}" class="font-medium text-[#fafafa] hover:underline transition">
                Batal dan kembali ke login
            </a>
        </p>
    </div>
</x-guest-layout>
