@extends('layouts.app')

@section('title', 'Invoices')

@section('content')

    <div class="space-y-6">

        <div class="flex items-center justify-between">

            <div>
                <h1 class="text-2xl font-bold text-white">
                    Invoices
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    Riwayat seluruh tagihan subscription Compass.
                </p>
            </div>

        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-900">

            <table class="min-w-full">

                <thead class="border-b border-zinc-800 bg-zinc-950">

                <tr class="text-left text-xs uppercase tracking-wider text-zinc-500">

                    <th class="px-6 py-4">
                        Invoice
                    </th>

                    <th class="px-6 py-4">
                        Total
                    </th>

                    <th class="px-6 py-4">
                        Due Date
                    </th>

                    <th class="px-6 py-4">
                        Status
                    </th>

                    <th class="px-6 py-4 text-right">
                        Action
                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($invoices as $invoice)

                    <tr class="border-b border-zinc-800">

                        <td class="px-6 py-4">

                            <div class="font-medium text-white">

                                {{ $invoice->invoice_number }}

                            </div>

                            <div class="text-xs text-zinc-500">

                                {{ $invoice->created_at->format('d M Y H:i') }}

                            </div>

                        </td>

                        <td class="px-6 py-4 text-white">

                            Rp {{ number_format($invoice->total,0,',','.') }}

                        </td>

                        <td class="px-6 py-4 text-zinc-300">

                            {{ $invoice->due_date->format('d M Y') }}

                        </td>

                        <td class="px-6 py-4">

                            @switch($invoice->status)

                                @case('paid')

                                    <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs text-emerald-400">
                                    PAID
                                </span>

                                    @break

                                @case('pending')

                                    <span class="rounded-full bg-yellow-500/10 px-3 py-1 text-xs text-yellow-400">
                                    PENDING
                                </span>

                                    @break

                                @case('overdue')

                                    <span class="rounded-full bg-red-500/10 px-3 py-1 text-xs text-red-400">
                                    OVERDUE
                                </span>

                                    @break

                                @case('cancelled')

                                    <span class="rounded-full bg-zinc-700 px-3 py-1 text-xs text-zinc-300">
                                    CANCELLED
                                </span>

                                    @break

                                @default

                                    <span class="rounded-full bg-zinc-700 px-3 py-1 text-xs text-zinc-300">
                                    {{ strtoupper($invoice->status) }}
                                </span>

                            @endswitch

                        </td>

                        <td class="px-6 py-4 text-right">

                            <div class="flex justify-end gap-2">

                                @if($invoice->status === 'pending')

                                    <a
                                        href="{{ route('billing.pay', $invoice) }}"
                                        class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs text-white hover:bg-indigo-500">

                                        Pay

                                    </a>

                                @endif

                                <a
                                    href="{{ route('billing.invoice.show', $invoice) }}"
                                    class="rounded-md border border-zinc-700 px-3 py-1.5 text-xs text-zinc-300 hover:bg-zinc-800">

                                    Detail

                                </a>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="px-6 py-10 text-center text-zinc-500">

                            Belum ada invoice.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        {{ $invoices->links() }}

    </div>

@endsection
