@extends('layouts.app')

@section('title', 'Profile · Compass')

@section('content')
@php
    $user = auth()->user();
    $hasJobstreet = $user->jobstreetAccount && $user->jobstreetAccount->access_token;
@endphp

<div class="max-w-6xl mx-auto px-4 py-10 text-gray-200">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: PROFILE --}}
        <div class="lg:col-span-1">
            <div class="bg-[#161b22] border border-[#30363d] rounded-md p-6 text-center">

                <img
                    src="{{ asset('assets/img/avatars/messi.png') }}"
                    alt="Avatar"
                    class="w-36 h-36 rounded-full mx-auto mb-4 object-cover
                           border border-[#30363d]"
                >

                <div class="text-sm font-semibold text-gray-100">
                    {{ $user->name }}
                </div>

                <div class="text-sm text-gray-400 mb-4">
                    {{ $user->email }}
                </div>

                <a
                    href="{{ route('auth.logout') }}"
                    class="inline-flex w-full justify-center rounded-md
                           bg-[#da3633] px-4 py-2 text-sm font-medium text-white
                           hover:bg-[#f85149] transition"
                >
                    Sign out
                </a>
            </div>
        </div>

        {{-- RIGHT: CONNECTIONS --}}
        <div class="lg:col-span-2">
            <div class="bg-[#161b22] border border-[#30363d] rounded-md">

                {{-- HEADER --}}
                <div class="px-6 py-4 border-b border-[#30363d]">
                    <h2 class="text-sm font-semibold text-gray-100">
                        Platform Connections
                    </h2>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-[#0d1117] text-gray-400">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Platform</th>
                                <th class="px-6 py-3 text-left font-medium">Status</th>
                                <th class="px-6 py-3 text-left font-medium">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#30363d]">

                            {{-- JOBSTREET --}}
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-100">
                                    JobStreet
                                </td>

                                <td class="px-6 py-4">
                                    @if($hasJobstreet)
                                        <span class="inline-flex items-center rounded-full
                                                     bg-[#238636]/20 text-[#3fb950]
                                                     text-xs px-2 py-0.5 border border-[#238636]/40">
                                            Connected
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full
                                                     bg-[#30363d] text-gray-400
                                                     text-xs px-2 py-0.5 border border-[#30363d]">
                                            Disconnected
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <a
                                        href="{{ $hasJobstreet
                                            ? route('api.platform.disconnect', ['provider' => 'jobstreet'])
                                            : route('platform.connect.jobstreet') }}"
                                        class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium
                                               {{ $hasJobstreet
                                                   ? 'bg-[#da3633] text-white hover:bg-[#f85149]'
                                                   : 'bg-[#238636] text-white hover:bg-[#2ea043]' }}
                                               transition"
                                    >
                                        {{ $hasJobstreet ? 'Disconnect' : 'Connect' }}
                                    </a>
                                </td>
                            </tr>

                            {{-- GLINTS --}}
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-100">
                                    Glints
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full
                                                 bg-[#30363d] text-gray-400
                                                 text-xs px-2 py-0.5 border border-[#30363d]">
                                        Not Available
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <button
                                        disabled
                                        class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium
                                               bg-[#21262d] text-gray-500 cursor-not-allowed border border-[#30363d]">
                                        Disabled
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
