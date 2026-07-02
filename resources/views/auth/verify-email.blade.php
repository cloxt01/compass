<x-guest-layout>
    <div class="mx-auto max-w-[440px] space-y-6 pt-12 pb-6">

        {{-- CARD CONTAINER --}}
        <div class="saas-card overflow-hidden">

            {{-- HEADER --}}
            <div class="border-b border-[#262626] px-6 py-5">
                <h1 class="text-lg font-semibold tracking-tight text-[#fafafa]">Verifikasi Email</h1>
                <p class="mt-1 text-sm text-[#a1a1aa]">
                    {{ __('Terima kasih telah mendaftar! Silakan verifikasi email Anda.') }}
                </p>
            </div>

            {{-- BODY --}}
            <div class="p-6 space-y-4">
                <div class="text-xs leading-relaxed text-[#a1a1aa]">
                    {{ __('Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke email Anda. Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkan yang baru.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-3 text-xs text-emerald-400">
                        {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat registrasi.') }}
                    </div>
                @endif

                {{-- ACTIONS CONTAINER --}}
                <div class="flex flex-col space-y-3 pt-2">

                    {{-- FORM RESEND EMAIL --}}
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white">
                            {{ __('Kirim Ulang Email Verifikasi') }}
                        </button>
                    </form>

                    {{-- FORM LOGOUT --}}
                    <form method="POST" action="{{ route('logout') }}" class="text-center">
                        @csrf
                        <button type="submit" class="text-xs text-[#a1a1aa] hover:text-[#fafafa] hover:underline transition">
                            {{ __('Keluar dari Akun (Log Out)') }}
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
