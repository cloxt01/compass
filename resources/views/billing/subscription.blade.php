@extends('layouts.app')

@section('title', 'Subscription')

@section('content')

    <div class="space-y-6">

        <div class="flex items-center justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Subscription
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Kelola paket langganan Compass.
                </p>
            </div>

            <a
                href="{{ route('billing.packages') }}"
                class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-500">

                Upgrade Package

            </a>

        </div>

        <div class="rounded-xl border border-zinc-800 bg-zinc-900">

            <div class="border-b border-zinc-800 px-6 py-4">
                <h2 class="font-semibold text-white">
                    Current Subscription
                </h2>
            </div>

            <div class="grid gap-6 p-6 lg:grid-cols-2">

                <div class="space-y-4">

                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500">
                            Package
                        </div>

                        <div class="mt-1 text-lg font-semibold text-white">
                            {{ $subscription?->package?->name ?? 'Belum ada paket' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500">
                            Price
                        </div>

                        <div class="mt-1 text-white">
                            Rp {{ number_format($subscription?->package_price ?? 0, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500">
                            Status
                        </div>

                        <span class="mt-2 inline-flex rounded-full bg-emerald-500/10 px-3 py-1 text-xs text-emerald-400">
                        {{ strtoupper($subscription?->status ?? 'TIDAK AKTIF') }}
                    </span>
                    </div>

                </div>

                <div class="space-y-4">

                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500">
                            Started
                        </div>

                        <div class="mt-1 text-white">
                            {{ $subscription?->started_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500">
                            Expired
                        </div>

                        <div class="mt-1 text-white">
                            {{ $subscription?->expires_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500">
                            Next Billing
                        </div>

                        <div class="mt-1 text-white">
                            {{ $subscription?->next_billing_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wider text-zinc-500">
                            Auto Renew
                        </div>

                        @if($subscription?->auto_renew)

                            <span class="mt-2 inline-flex rounded-full bg-emerald-500/10 px-3 py-1 text-xs text-emerald-400">
                            Enabled
                        </span>

                        @else

                            <span class="mt-2 inline-flex rounded-full bg-red-500/10 px-3 py-1 text-xs text-red-400">
                            Disabled
                        </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

        <div class="rounded-xl border border-zinc-800 bg-zinc-900">

            <div class="border-b border-zinc-800 px-6 py-4">
                <h2 class="font-semibold text-white">
                    Package Limits
                </h2>
            </div>

            <div class="grid gap-4 p-6 md:grid-cols-2">

                <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-5">

                    <div class="text-xs uppercase text-zinc-500">
                        Daily Apply Limit
                    </div>

                    <div class="mt-2 text-2xl font-bold text-white">
                        {{ number_format($subscription?->package?->daily_limit ?? 0) }}
                    </div>

                </div>

                <div class="rounded-lg border border-zinc-800 bg-zinc-950 p-5">

                    <div class="text-xs uppercase text-zinc-500">
                        Monthly Apply Limit
                    </div>

                    <div class="mt-2 text-2xl font-bold text-white">
                        {{ number_format($subscription?->package?->monthly_limit ?? 0) }}
                    </div>

                </div>

            </div>

        </div>

        <div class="flex flex-wrap gap-3">

            <form action="{{ route('billing.subscription.autorenew') }}" method="POST">
                @csrf

                <button
                    {{ !$subscription ? 'disabled' : '' }}
                    class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">

                    {{ $subscription?->auto_renew ? 'Disable Auto Renew' : 'Enable Auto Renew' }}

                </button>

            </form>

            <form action="{{ route('billing.subscription.cancel') }}" method="POST">
                @csrf

                <button
                    {{ !$subscription ? 'disabled' : '' }}
                    onclick="return confirm('Batalkan perpanjangan subscription?')"
                    class="rounded-lg bg-red-600 px-5 py-2 text-sm font-medium text-white hover:bg-red-500 disabled:opacity-50 disabled:cursor-not-allowed">

                    Cancel Subscription

                </button>

            </form>

        </div>

    </div>

@endsection
