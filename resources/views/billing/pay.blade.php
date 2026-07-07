@extends('layouts.app')

@section('title', 'Pay Invoice')

@section('content')

    <div class="mx-auto max-w-2xl">

        <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-8">

            <h1 class="text-2xl font-bold text-white">
                Pay Invoice
            </h1>

            <p class="mt-2 text-sm text-zinc-400">
                Selesaikan pembayaran untuk mengaktifkan subscription.
            </p>

            <div class="mt-8 space-y-4">

                <div class="flex justify-between">
                <span class="text-zinc-500">
                    Invoice
                </span>

                    <span class="text-white">
                    {{ $invoice->invoice_number }}
                </span>
                </div>

                <div class="flex justify-between">
                <span class="text-zinc-500">
                    Amount
                </span>

                    <span class="font-semibold text-white">
                    Rp {{ number_format($payment->amount,0,',','.') }}
                </span>
                </div>

                <div class="flex justify-between">
                <span class="text-zinc-500">
                    Status
                </span>

                    <span class="rounded bg-yellow-500/20 px-2 py-1 text-xs text-yellow-400">
                    {{ strtoupper($payment->status) }}
                </span>
                </div>

                <div class="flex justify-between">
                <span class="text-zinc-500">
                    Due Date
                </span>

                    <span class="text-white">
                    {{ $invoice->due_date->format('d M Y H:i') }}
                </span>
                </div>

            </div>

            <div class="mt-10 flex gap-3">

                <a
                    href="{{ $payment->redirect_url }}"
                    class="flex-1 rounded-lg bg-indigo-600 py-3 text-center font-medium text-white hover:bg-indigo-500">

                    Continue Payment

                </a>

                <a
                    href="{{ route('billing.invoices') }}"
                    class="rounded-lg border border-zinc-700 px-6 py-3 text-zinc-300 hover:bg-zinc-800">

                    Back

                </a>

            </div>

        </div>

    </div>

@endsection
