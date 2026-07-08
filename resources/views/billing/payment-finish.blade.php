@extends('layouts.guest')

@section('title', 'Pembayaran Berhasil')

@section('content')

    <div class="max-w-2xl mx-auto">

        <div class="rounded-2xl border border-emerald-500/20 bg-zinc-900 p-8">

            <div class="flex items-center gap-4">

                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500/15">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-8 w-8 text-emerald-400"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-white">
                        Pembayaran Berhasil
                    </h1>

                    <p class="mt-1 text-sm text-zinc-400">
                        Terima kasih. Langganan Anda telah berhasil diproses dan akun Compass siap digunakan.
                    </p>
                </div>

            </div>

            <div class="mt-8 rounded-xl border border-zinc-800 bg-zinc-950 overflow-hidden">

                <div class="divide-y divide-zinc-800">

                    <div class="flex justify-between px-5 py-4">
                        <span class="text-zinc-400">Invoice</span>
                        <span class="font-medium text-white">{{ $invoice->invoice_number }}</span>
                    </div>

                    <div class="flex justify-between px-5 py-4">
                        <span class="text-zinc-400">Paket</span>
                        <span class="font-medium text-white">
                        {{ $invoice->subscription->package->name ?? '-' }}
                    </span>
                    </div>

                    <div class="flex justify-between px-5 py-4">
                        <span class="text-zinc-400">Total Pembayaran</span>
                        <span class="font-semibold text-emerald-400">
                        Rp {{ number_format($invoice->total,0,',','.') }}
                    </span>
                    </div>

                    <div class="flex justify-between px-5 py-4">
                        <span class="text-zinc-400">Status</span>

                        <span class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold uppercase text-emerald-400">
                        {{ $invoice->status }}
                    </span>
                    </div>

                    <div class="flex justify-between px-5 py-4">
                        <span class="text-zinc-400">Waktu Pembayaran</span>
                        <span class="text-white">
                        {{ $invoice->paid_at?->format('d M Y • H:i') ?? '-' }}
                    </span>
                    </div>

                </div>

            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">

                <a href="{{ route('dashboard') }}"
                   class="inline-flex justify-center rounded-xl bg-white px-5 py-3 font-medium text-black transition hover:bg-zinc-200">
                    Masuk ke Dashboard
                </a>

                <a href="{{ route('billing.invoices') }}"
                   class="inline-flex justify-center rounded-xl border border-zinc-700 px-5 py-3 font-medium text-white transition hover:bg-zinc-800">
                    Lihat Riwayat Tagihan
                </a>

            </div>

        </div>

    </div>

@endsection
