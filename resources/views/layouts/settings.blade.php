@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-10 text-[#a1a1aa]">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">

            {{-- LEFT SIDEBAR: PROFILE & NAVIGATION TABS --}}
            <div class="lg:col-span-1 space-y-4">

                {{-- USER QUICK INFO --}}
                <div class="flex items-center gap-3 px-3 mb-2">
                    <img
                        src="{{ asset('assets/img/goat/messi.png') }}"
                        alt="Avatar"
                        class="w-9 h-9 rounded-md object-cover border border-[#333]"
                    >
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-[#fafafa] truncate">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-xs text-[#71717a] truncate">
                            Personal account
                        </div>
                    </div>
                </div>

                {{-- NAVIGATION TABS --}}
                <nav class="flex flex-col gap-0.5 text-sm">
                    <div class="px-3 pt-2 pb-1 text-[11px] font-semibold text-[#71717a] uppercase tracking-wider">
                        General Configuration
                    </div>

                    {{-- General Tab --}}
                    <a href="{{ route('settings', ['tab' => 'general']) }}"
                       class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ $currentTab === 'general' ? 'bg-[#1e1e1e] text-[#fafafa] font-medium' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                        <i data-lucide="settings" class="w-4 h-4 opacity-70 {{ $currentTab === 'general' ? 'text-[#fafafa] opacity-100' : '' }}"></i>
                        <span>General</span>
                    </a>

                    {{-- Account Tab --}}
                    <a href="{{ route('settings', ['tab' => 'account']) }}"
                       class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ $currentTab === 'account' ? 'bg-[#1e1e1e] text-[#fafafa] font-medium' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                        <i data-lucide="user-cog" class="w-4 h-4 opacity-70 {{ $currentTab === 'account' ? 'text-[#fafafa] opacity-100' : '' }}"></i>
                        <span>Account</span>
                    </a>

                    {{-- Keamanan Tab --}}
                    <a href="{{ route('settings', ['tab' => 'security']) }}"
                       class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ $currentTab === 'security' ? 'bg-[#1e1e1e] text-[#fafafa] font-medium' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                        <i data-lucide="shield-check" class="w-4 h-4 opacity-70 {{ $currentTab === 'security' ? 'text-[#fafafa] opacity-100' : '' }}"></i>
                        <span>Keamanan</span>
                    </a>

                    {{-- Apply Configuration Tab --}}
                    <a href="{{ route('settings', ['tab' => 'apply-configuration']) }}"
                       class="flex items-center gap-3 rounded-md px-3 py-2 transition-colors duration-200 {{ $currentTab === 'apply-configuration' ? 'bg-[#1e1e1e] text-[#fafafa] font-medium' : 'text-[#a1a1aa] hover:bg-[#1e1e1e] hover:text-[#fafafa]' }}">
                        <i data-lucide="monitor" class="w-4 h-4 opacity-70 {{ $currentTab === 'apply-configuration' ? 'text-[#fafafa] opacity-100' : '' }}"></i>
                        <span>Apply Configuration</span>
                    </a>
                </nav>

                <div class="pt-2 px-1">
                    <hr class="border-[#262626]">
                </div>

                {{-- SIGN OUT --}}
                <form method="POST" action="{{ route('logout') }}" class="px-3">
                    @csrf
                    <button type="submit" class="w-full text-left text-xs font-medium text-red-400 hover:text-red-300 transition flex items-center gap-2 py-1">
                        <i data-lucide="log-out" class="w-3.5 h-3.5 opacity-80"></i>
                        <span>Sign out</span>
                    </button>
                </form>
            </div>

            {{-- RIGHT CONTENT AREA --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="pb-3 border-b border-[#262626] mb-4">
                    <h1 class="text-xl font-semibold text-[#fafafa]">
                        @yield('settings-title', 'Settings')
                    </h1>
                </div>

                {{-- Slot untuk memuat kontent dari tab aktif --}}
                @yield('settings-content')
            </div>

        </div>
    </div>
@endsection
