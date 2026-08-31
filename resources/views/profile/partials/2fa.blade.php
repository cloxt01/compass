

<div class="px-5 py-4">
    <p class="text-sm text-[#a1a1aa] leading-relaxed">
        Tambahkan lapisan keamanan tambahan pada akun Anda menggunakan aplikasi authenticator.
    </p>
</div>

<div class="px-5 pb-5">

    @if (! auth()->user()->two_factor_secret)

        <form method="POST" action="{{ route('two-factor.enable') }}">
            @csrf

            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-[#1e1e1e] border border-[#333] px-4 py-2 text-xs font-semibold text-[#fafafa] hover:bg-[#262626] transition"
            >
                Aktifkan 2FA
            </button>
        </form>

    @else

        <div class="space-y-5">

            @if (! auth()->user()->two_factor_confirmed_at)

                <div>
                    <p class="text-sm text-[#a1a1aa] leading-relaxed">
                        Scan QR code berikut menggunakan aplikasi authenticator
                        seperti Google Authenticator atau Authy, lalu masukkan kode
                        yang dihasilkan untuk mengaktifkan 2FA.
                    </p>
                </div>

                <div class="inline-flex rounded-md border border-[#262626] bg-white p-3">
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                </div>

                <form
                    method="POST"
                    action="{{ route('two-factor.confirm') }}"
                    class="flex flex-col sm:flex-row items-start sm:items-center gap-3"
                >
                    @csrf

                    <input
                        type="text"
                        name="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="Masukkan 6 digit kode"
                        maxlength="6"
                        required
                        class="w-full sm:w-52 bg-[#141414] border border-[#262626] rounded-md px-3 py-2 text-sm text-[#fafafa] placeholder-[#52525b] focus:outline-none focus:border-[#333] focus:ring-1 focus:ring-[#333] transition"
                    >

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-[#1e1e1e] border border-[#333] px-4 py-2 text-xs font-semibold text-[#fafafa] hover:bg-[#262626] transition"
                    >
                        Konfirmasi
                    </button>
                </form>

            @else

                <div class="rounded-md border border-emerald-500/20 bg-emerald-500/5 px-4 py-3">
                    <p class="text-sm font-medium text-emerald-300">
                        2FA Aktif
                    </p>

                    <p class="mt-1 text-xs text-emerald-200/60">
                        Autentikasi dua faktor sudah aktif pada akun Anda.
                    </p>
                </div>

                <div>
                    <button
                        type="button"
                        x-data=""
                        x-on:click="$dispatch('open-recovery-codes')"
                        class="inline-flex items-center rounded-md bg-[#1e1e1e] border border-[#333] px-4 py-2 text-xs font-semibold text-[#fafafa] hover:bg-[#262626] transition"
                    >
                        Lihat Kode Pemulihan
                    </button>
                </div>

            <div
                x-data="{ open: false }"
                x-show="open"
                x-cloak
                x-on:open-recovery-codes.window="open = true"
                x-on:keydown.escape.window="open = false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                {{-- Overlay --}}
                <div
                    x-show="open"
                    x-transition.opacity
                    x-on:click="open = false"
                    class="absolute inset-0 bg-black/70"
                ></div>

                {{-- Modal --}}
                <div
                    x-show="open"
                    x-transition
                    class="relative w-full max-w-lg bg-[#0a0a0a] border border-[#262626] rounded-md p-6 shadow-2xl"
                    x-on:click.stop
                >
                    <h2 class="text-lg font-semibold text-[#fafafa]">
                        Kode Pemulihan
                    </h2>

                    <p class="mt-2 text-sm text-[#a1a1aa] leading-relaxed">
                        Simpan kode pemulihan ini di tempat yang aman. Kode ini dapat digunakan
                        untuk masuk jika Anda tidak dapat mengakses aplikasi authenticator.
                    </p>

                    <div class="mt-5 grid grid-cols-2 gap-2">
                        @foreach (auth()->user()->recoveryCodes() as $code)
                            <div class="rounded-md border border-[#262626] bg-[#141414] px-3 py-2 text-center font-mono text-sm text-[#fafafa]">
                                {{ $code }}
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button
                            type="button"
                            x-on:click="open = false"
                            class="inline-flex items-center rounded-md bg-[#1e1e1e] border border-[#333] px-4 py-2 text-xs font-semibold text-[#fafafa] hover:bg-[#262626] transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>


            @endif

            <div class="pt-2 border-t border-[#262626]">
                <form method="POST" action="{{ route('two-factor.disable') }}">
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md border border-red-500/30 bg-red-500/5 px-4 py-2 text-xs font-semibold text-red-400 hover:bg-red-500/10 transition"
                    >
                        Nonaktifkan 2FA
                    </button>
                </form>
            </div>

        </div>

    @endif

</div>

