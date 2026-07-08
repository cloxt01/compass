@extends('layouts.app')

@section('title','Invoice')

@section('content')

    <div class="max-w-3xl mx-auto space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-white">
                {{ $invoice->invoice_number }}
            </h1>

            <p class="text-zinc-400">
                Detail Invoice
            </p>
        </div>

        <div class="rounded-xl border border-zinc-800 bg-zinc-900 p-6 space-y-3">

            <div class="flex justify-between">
                <span>Paket</span>
                <span> {{ $invoice->subscription->package->name ?? '-' }}</span>
            </div>

            <div class="flex justify-between">
                <span>Discount</span>
                <span> {{ ($invoice->discount ?? '-').'%' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Total</span>
                <span>Rp {{ number_format($invoice->total,0,',','.') }}</span>
            </div>

            <div class="flex justify-between">
                <span>Status</span>
                <span>{{ strtoupper($invoice->status) }}</span>
            </div>

            <div class="flex justify-between">
                <span>Due Date</span>
                <span>{{ $invoice->due_date }}</span>
            </div>


        </div>

    </div>

@endsection
