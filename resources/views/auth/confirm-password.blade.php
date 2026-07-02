<x-guest-layout>
    <div class="mx-auto max-w-[440px] space-y-6 pt-12 pb-6">

        {{-- CARD CONTAINER --}}
        <div class="saas-card overflow-hidden">

            {{-- HEADER --}}
            <div class="border-b border-[#262626] px-6 py-5">
                <h1 class="text-lg font-semibold tracking-tight text-[#fafafa]">Konfirmasi Keamanan</h1>
                <p class="mt-1 text-sm text-[#a1a1aa]">
                    {{ __('Ini adalah area aman. Harap konfirmasi password Anda sebelum melanjutkan.') }}
                </p>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">

                <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="password" class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">{{ __('Password') }}</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-400" />
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="pt-2">
                        <button type="submit" class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white">
                            {{ __('Konfirmasi') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- FOOTER / CANCEL LINK --}}
        <p class="text-center text-xs text-[#a1a1aa]">
            <a href="{{ url()->previous() }}" class="font-medium text-[#fafafa] hover:underline transition">
                Batal dan Kembali
            </a>
        </p>
    </div>
</x-guest-layout>
