@extends('layouts.app')

@section('title', 'Apply · Compass')

@section('content')

@php
    $hasJobstreet = $user->jobstreetAccount && $user->jobstreetAccount->access_token;
@endphp

<div class="max-w-3xl mx-auto py-10 px-4">

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="mb-6 rounded-md border border-red-800 bg-[#2a1215] p-4 text-sm text-red-400">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- CARD --}}
    <div class="bg-[#161b22] border border-[#30363d] rounded-md">

        {{-- HEADER --}}
        <div class="px-6 py-4 border-b border-[#30363d]">
            <h1 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                <i class="fas fa-paper-plane text-[#8b949e]"></i>
                Auto Apply Configuration
            </h1>
            <p class="text-xs text-[#8b949e] mt-1">
                Configure how Compass submits applications automatically.
            </p>
        </div>

        {{-- BODY --}}
        <div class="p-6">
            <form method="POST" action="{{ route('apply.start') }}" class="space-y-6">
                @csrf

                {{-- SEARCH --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-[#8b949e] mb-1">
                            Keyword
                        </label>
                        <input
                            type="text"
                            name="keyword"
                            required
                            placeholder="Web Developer"
                            class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                   px-3 py-2 text-sm text-[#e6edf3]
                                   placeholder-[#6e7681]
                                   focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-[#8b949e] mb-1">
                            Location
                        </label>
                        <input
                            type="text"
                            name="location"
                            placeholder="Jakarta"
                            class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                   px-3 py-2 text-sm text-[#e6edf3]
                                   placeholder-[#6e7681]
                                   focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]"
                        >
                    </div>
                </div>

                {{-- LIMITS --}}
                <div class="grid grid-cols-3 gap-4">
                    @foreach ([
                        ['Interval (sec)', 'interval', 5],
                        ['Per Batch', 'pageSize', 5],
                        ['Max Apply', 'max_applications', 10],
                    ] as [$label, $name, $value])
                        <div>
                            <label class="block text-xs font-medium text-[#8b949e] mb-1">
                                {{ $label }}
                            </label>
                            <input
                                type="number"
                                name="{{ $name }}"
                                value="{{ $value }}"
                                min="1"
                                class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                       px-3 py-2 text-sm text-[#e6edf3]
                                       focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]"
                            >
                        </div>
                    @endforeach
                </div>

                {{-- PROVIDER --}}
                <div class="border border-[#30363d] rounded-md p-4 bg-[#0d1117]">
                    <label class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                name="providers[]"
                                value="jobstreet"
                                class="h-4 w-4 rounded border-[#30363d] bg-[#0d1117] text-[#238636]
                                       focus:ring-[#238636]"
                                {{ $hasJobstreet ? '' : 'disabled' }}
                            >
                            <span class="text-sm font-medium text-[#e6edf3]">
                                JobStreet
                            </span>
                        </div>

                        @if(!$hasJobstreet)
                            <span class="text-xs px-2 py-0.5 rounded border border-yellow-700
                                         text-yellow-400 bg-[#2d1b00]">
                                Not connected
                            </span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded border border-green-700
                                         text-green-400 bg-[#0f2a1c]">
                                Ready
                            </span>
                        @endif
                    </label>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-md
                               bg-[#238636] px-5 py-2.5
                               text-sm font-semibold text-white
                               hover:bg-[#2ea043] transition">
                        Start Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <p class="mt-4 text-xs text-[#8b949e] text-center">
        Keep your connection stable while automation is running.
    </p>
</div>

@endsection
