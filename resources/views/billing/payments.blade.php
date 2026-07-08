@extends('layouts.app')

@section('title','Payments')

@section('content')

    <div class="space-y-6">

        <div>

            <h1 class="text-2xl font-bold text-white">
                Payment History
            </h1>

            <p class="mt-1 text-sm text-zinc-400">
                Seluruh transaksi pembayaran subscription.
            </p>

        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-800 bg-zinc-900">

            <table class="min-w-full">

                <thead class="border-b border-zinc-800 bg-zinc-950">

                <tr class="text-left text-xs uppercase tracking-wider text-zinc-500">

                    <th class="px-6 py-4">
                        Invoice
                    </th>



                    <th class="px-6 py-4">
                        Amount
                    </th>

                    <th class="px-6 py-4">
                        Status
                    </th>

                    <th class="px-6 py-4">
                        Paid At
                    </th>

                    <th class="px-6 py-4 text-right">
                        Action
                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($payments as $payment)

                    <tr class="border-b border-zinc-800">

                        <td class="px-6 py-4">

                            <div class="font-medium text-white">
                                {{ $payment->invoice->invoice_number }}
                            </div>

                            <div class="text-xs text-zinc-500">
                                {{ $payment->reference }}
                            </div>

                        </td>

{{--                        <td class="px-6 py-4 text-zinc-300 uppercase">--}}
{{--                            {{ $payment->gateway }}--}}
{{--                        </td>--}}

{{--                        <td class="px-6 py-4 text-zinc-300">--}}
{{--                            {{ ucfirst($payment->method) }}--}}
{{--                        </td>--}}

                        <td class="px-6 py-4 text-white">

                            Rp {{ number_format($payment->amount,0,',','.') }}

                        </td>

                        <td class="px-6 py-4">

                            @php
                                $color = match($payment->status){
                                    'paid' => 'emerald',
                                    'pending' => 'yellow',
                                    'failed' => 'red',
                                    'expired' => 'orange',
                                    'cancelled' => 'zinc',
                                    'refund' => 'sky',
                                    default => 'zinc'
                                };
                            @endphp

                            <span class="rounded-full bg-{{ $color }}-500/10 px-3 py-1 text-xs text-{{ $color }}-400">
                            {{ strtoupper($payment->status) }}
                        </span>

                        </td>

                        <td class="px-6 py-4 text-zinc-400">
                            {{ optional($payment->paid_at)?->format('d-m-Y H:i:s') ?? '-' }}

                        </td>

                        <td class="px-6 py-4 text-right">

                            <a
                                href="{{ route('billing.invoice.show',$payment->invoice) }}"
                                class="rounded-md border border-zinc-700 px-3 py-1.5 text-xs text-zinc-300 hover:bg-zinc-800">

                                Invoice

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="px-6 py-10 text-center text-zinc-500">

                            Belum ada riwayat pembayaran.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        {{ $payments->links() }}

    </div>

@endsection
