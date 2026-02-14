@extends('layouts.app')

@section('header')
    <title>Dashboard · Compass</title>
    @livewireStyles
@endsection

@section('content')
@livewireScripts

<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="rounded-md border border-red-800 bg-[#2a1215] p-4 text-sm text-red-400">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <h1 class="text-lg font-semibold text-[#e6edf3]">
            Good to see you,
            <span class="font-bold">{{ Auth::user()->name }}</span>
        </h1>

        <div class="flex items-center gap-3">
            <a href="{{ route('apply') }}"
               class="rounded-md bg-[#238636] px-4 py-2 text-sm font-medium text-white
                      hover:bg-[#2ea043] transition">
                Go to Apply
            </a>

            <a href="{{ route('auth.logout') }}"
               class="rounded-md border border-[#30363d] px-4 py-2 text-sm font-medium
                      text-[#e6edf3] hover:bg-[#161b22] transition">
                Logout
            </a>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="bg-[#161b22] border border-[#30363d] rounded-md p-4">
        <livewire:user-stats />
    </div>

</div>
@endsection
