@extends('layouts.guest-auth')

@section('content')
    <div
        class="mx-auto max-w-[440px] space-y-6 pt-12 pb-6"
        x-data="{ recovery: false }"
    >

        {{-- CARD CONTAINER --}}
        <div class="saas-card overflow-hidden">

            {{-- HEADER --}}
            <div class="border-b border-[#262626] px-6 py-5">
                <h1 class="text-lg font-semibold tracking-tight text-[#fafafa]">
                    Verifikasi Dua Faktor
                </h1>

                <p class="mt-1 text-sm text-[#a1a1aa]">
                    Masukkan kode dari aplikasi authenticator kamu.
                </p>
            </div>


            {{-- BODY --}}
            <div class="p-6">

                @if ($errors->any())
                    <div class="mb-5 rounded-md border border-red-500/20 bg-red-500/10 p-3 text-xs text-red-400">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form
                    method="POST"
                    action="{{ route('two-factor.login') }}"
                    class="space-y-4"
                >
                    @csrf

                    {{-- KODE AUTHENTICATOR --}}
                    <div>
                        <label
                            for="code"
                            class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]"
                        >
                            Kode Autentikasi
                        </label>

                        <input
                            id="code"
                            type="text"
                            name="code"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]"
                            placeholder="123456"
                        >
                    </div>


                    {{-- FORM TAMBAHAN RECOVERY --}}
                    <div
                        x-show="recovery"
                        x-transition
                    >
                        <label
                            for="recovery_code"
                            class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]"
                        >
                            Kode Pemulihan
                        </label>

                        <input
                            id="recovery_code"
                            type="text"
                            name="recovery_code"
                            autocomplete="off"
                            class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]"
                            placeholder="Masukkan kode pemulihan"
                        >

                        <p class="mt-2 text-xs leading-relaxed text-[#71717a]">
                            Gunakan salah satu kode pemulihan yang telah kamu simpan.
                        </p>
                    </div>


                    {{-- TOGGLE --}}
                    <div class="pt-1">

                        <button
                            type="button"
                            x-show="!recovery"
                            @click="recovery = true"
                            class="text-xs text-[#a1a1aa] underline transition hover:text-[#fafafa]"
                        >
                            Gunakan kode pemulihan
                        </button>

                        <button
                            type="button"
                            x-show="recovery"
                            @click="recovery = false"
                            class="text-xs text-[#a1a1aa] underline transition hover:text-[#fafafa]"
                        >
                            Gunakan kode autentikasi
                        </button>

                    </div>


                    {{-- SUBMIT --}}
                    <div class="pt-2">
                        <button
                            type="submit"
                            class="h-11 w-full cursor-pointer rounded-xl bg-white/85 text-sm font-semibold text-black transition hover:bg-white"
                        >
                            Verifikasi
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
