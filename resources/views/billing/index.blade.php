@extends('layouts.app')

@section('title', 'Billing')

@section('content')

<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-white">
            Billing
        </h1>

        <p class="mt-1 text-sm text-zinc-400">
            Kelola subscription, invoice, pembayaran, dan penggunaan paket.
        </p>
    </div>

    {{-- Overview --}}
    <div class="grid gap-4 lg:grid-cols-4">

        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5">

            <p class="text-xs uppercase tracking-wider text-zinc-500">
                Paket
            </p>

            <h2 class="mt-2 text-xl font-semibold text-white">
                {{ optional($subscription?->package)->name ?? '-' }}
            </h2>

            <p class="mt-2 text-sm text-zinc-400">
                Rp {{ number_format($subscription?->package_price ?? 0,0,',','.') }}
            </p>

        </div>

        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5">

            <p class="text-xs uppercase tracking-wider text-zinc-500">
                Status
            </p>

            <span class="mt-2 inline-flex rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-400">
                {{ strtoupper($subscription?->status ?? '-') }}
            </span>

        </div>

        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5">

            <p class="text-xs uppercase tracking-wider text-zinc-500">
                Berakhir
            </p>

            <h2 class="mt-2 text-xl font-semibold text-white">
                {{ optional($subscription?->expires_at)->format('d M Y') }}
            </h2>

        </div>

        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-5">

            <p class="text-xs uppercase tracking-wider text-zinc-500">
                Auto Renew
            </p>

            <span class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-medium
                {{ $subscription?->auto_renew
                    ? 'bg-emerald-500/10 text-emerald-400'
                    : 'bg-red-500/10 text-red-400' }}">

                {{ $subscription?->auto_renew ? 'ON' : 'OFF' }}

            </span>

        </div>

    </div>

    {{-- Usage --}}
    <div class="rounded-xl border border-zinc-800 bg-zinc-900">

        <div class="border-b border-zinc-800 px-6 py-4">

            <h2 class="font-semibold text-white">
                Usage
            </h2>

        </div>

        <div class="grid gap-6 p-6 md:grid-cols-2">

            <div>

                <div class="flex justify-between text-sm">

                    <span class="text-zinc-400">
                        Daily Usage
                    </span>

                    <span class="text-white">
                        {{ $dailyUsage }}
                        /
                        {{ $subscription?->package?->daily_limit }}
                    </span>

                </div>

                <div class="mt-2 h-2 overflow-hidden rounded bg-zinc-800">

                    <div
                        class="h-full rounded bg-indigo-500"
                        style="width: {{ $dailyPercent }}%">
                    </div>

                </div>

            </div>

            <div>

                <div class="flex justify-between text-sm">

                    <span class="text-zinc-400">
                        Monthly Usage
                    </span>

                    <span class="text-white">
                        {{ $monthlyUsage }}
                        /
                        {{ $subscription?->package?->monthly_limit }}
                    </span>

                </div>

                <div class="mt-2 h-2 overflow-hidden rounded bg-zinc-800">

                    <div
                        class="h-full rounded bg-emerald-500"
                        style="width: {{ $monthlyPercent }}%">
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Latest Invoice --}}
    <div class="rounded-xl border border-zinc-800 bg-zinc-900">

        <div class="flex items-center justify-between border-b border-zinc-800 px-6 py-4">

            <h2 class="font-semibold text-white">
                Latest Invoice
            </h2>

            <a
                href="{{ route('billing.invoices') }}"
                class="text-sm text-indigo-400 hover:text-indigo-300">

                View All

            </a>

        </div>

        @if($invoice)

        <div class="p-6">

            <div class="flex items-center justify-between">

                <div>

                    <h3 class="font-medium text-white">
                        {{ $invoice->invoice_number }}
                    </h3>

                    <p class="mt-1 text-sm text-zinc-500">

                        Due

                        {{ $invoice->due_date->format('d M Y') }}

                    </p>

                </div>

                <div class="text-right">

                    <h3 class="text-lg font-semibold text-white">

                        Rp {{ number_format($invoice->total,0,',','.') }}

                    </h3>

                    <span class="mt-2 inline-flex rounded-full bg-yellow-500/10 px-3 py-1 text-xs text-yellow-400">

                            {{ strtoupper($invoice->status) }}

                        </span>

                </div>

            </div>

        </div>

        @else

        <div class="p-6 text-sm text-zinc-500">

            Belum ada invoice.

        </div>

        @endif

    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-3">

        <a
            href="{{ route('billing.packages') }}"
            class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-500">

            Upgrade Package

        </a>

        <a
            href="{{ route('billing.invoices') }}"
            class="rounded-lg border border-zinc-700 px-5 py-2 text-sm text-zinc-300 hover:bg-zinc-800">

            Invoice

        </a>

        <a
            href="{{ route('billing.payments') }}"
            class="rounded-lg border border-zinc-700 px-5 py-2 text-sm text-zinc-300 hover:bg-zinc-800">

            Payment History

        </a>

    </div>

</div>

@endsection
