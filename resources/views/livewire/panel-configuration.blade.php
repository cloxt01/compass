<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\HasConfiguration;

new class extends Component
{
    use HasConfiguration;

    public $isReady = false;
    public $isPaused = false;

    public $jobstreetEnabled = false;
    public $glintsEnabled = false;

    public $hasJobstreet = false;
    public $hasGlints = false;

    public function mount($accounts = [], $adapters = [])
    {
        $this->hasJobstreet = isset($accounts['jobstreet']) && isset($adapters['jobstreet']);
        $this->hasGlints = isset($accounts['glints']) && isset($adapters['glints']);
    }

    public function init()
    {
        $user = Auth::user();

        $this->isPaused = $user->automation_paused ?? false;

        $config = $this->configuration();
        $this->jobstreetEnabled = $config['jobstreet']['enabled'] ?? false;
        $this->glintsEnabled = $config['glints']['enabled'] ?? false;

        $this->isReady = true;
    }

    public function toggleProvider($provider, $enabled)
    {
        $enabled = filter_var($enabled, FILTER_VALIDATE_BOOLEAN);

        $this->saveConfiguration([
            $provider => [
                'enabled' => $enabled,
            ]
        ]);

        $this->{$provider . 'Enabled'} = $enabled;

        $this->dispatch('config-saved');
        $this->dispatch('queue-status-updated', processing: 0);
    }

    public function stop()
    {
        Auth::user()->update(['automation_paused' => true]);
        $this->isPaused = true;
        $this->dispatch('queue-status-updated', processing: 0);
    }

    public function resume()
    {
        Auth::user()->update(['automation_paused' => false]);
        $this->isPaused = false;
    }
};
?>
{{--// resources/views/livewire/panel-configuration.blade.php--}}
<div class="saas-card  p-4 sm:p-6 xl:col-span-2" wire:init="init">
    @if(!$isReady)
        {{-- SKELETON --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-2">
                <div class="h-5 w-16 rounded-lg bg-[#222] animate-pulse"></div>
                <div class="h-3 w-48 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto">
                <div class="h-9 w-full rounded-md bg-[#222] animate-pulse sm:w-20"></div>
                <div class="h-9 w-full rounded-md bg-[#222] animate-pulse sm:w-16"></div>
            </div>
        </div>
        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach(range(1,3) as $i)
                <div class="space-y-2">
                    <div class="h-3 w-20 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
                    <div class="h-11 w-full rounded-xl bg-[#222] animate-pulse"></div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 flex flex-wrap gap-4 border-t border-[#262626] pt-4">
            <div class="h-5 w-24 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
            <div class="h-5 w-16 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
        </div>
    @else
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-base font-semibold tracking-tight text-[#fafafa] sm:text-lg">
                    Panel
                </h2>
                <p class="mt-1 text-xs text-[#a1a1aa] sm:text-sm">
                    Kontrol otomatisasi lamaran
                </p>
            </div>
            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:flex-wrap">
                @if ($isPaused)
                    <button
                        wire:click="resume"
                        wire:loading.attr="disabled"
                        wire:target="resume"
                        class="h-9 w-full cursor-pointer rounded-md bg-green-700 px-5 text-sm font-semibold text-[#fff] transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto">
                        Resume
                    </button>
                @else
                    <button
                        wire:click="stop"
                        wire:loading.attr="disabled"
                        wire:target="stop"
                        class="h-9 w-full cursor-pointer rounded-md bg-red-700 px-5 text-sm font-semibold text-[#fff] transition hover:bg-red-800 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto">
                        Stop
                    </button>
                @endif
            </div>
        </div>

        {{-- Provider toggle --}}
        <div class="mt-5 grid grid-cols-1 items-stretch gap-5 border-t border-[#262626] pt-5 lg:grid-cols-3">
            <div class="flex flex-col">
                <p class="mb-3 text-xs font-medium uppercase tracking-[0.14em] text-[#71717a]">
                    Provider
                </p>
                <div class="flex min-h-[170px] flex-1 flex-col rounded-xl border border-[#262626] bg-[#0a0a0a] p-3 sm:p-4">
                    <div class="flex-1 space-y-3">
                        {{-- JobStreet --}}
                        <label class="flex items-center justify-between gap-3 rounded-lg border border-[#262626] bg-[#111111] px-3 py-3 transition hover:border-[#3a3a3a] {{ !$hasJobstreet ? 'cursor-not-allowed opacity-40' : 'cursor-pointer' }}">
                            <div>
                                <p class="text-sm font-medium text-[#fafafa]">JobStreet</p>
                                <p class="mt-0.5 text-xs text-[#71717a]">Gunakan akun JobStreet</p>
                            </div>
                            <label class="relative inline-flex items-center">
                                <input
                                    type="checkbox"
                                    @checked($jobstreetEnabled)
                                    {{ !$hasJobstreet ? 'disabled' : '' }}
                                    wire:change="toggleProvider('jobstreet', $event.target.checked)"
                                    class="peer sr-only">
                                <div class="cursor-pointer h-6 w-11 rounded-full bg-[#1f1f1f] transition peer-checked:bg-blue-600 peer-disabled:bg-[#161616]"></div>
                                <div class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-all peer-checked:translate-x-5"></div>
                            </label>
                        </label>

                        {{-- Glints --}}
                        <label class="flex items-center justify-between gap-3 rounded-lg border border-[#262626] bg-[#111111] px-3 py-3 transition hover:border-[#3a3a3a] {{ !$hasGlints ? 'cursor-not-allowed opacity-40' : 'cursor-pointer' }}">
                            <div>
                                <p class="text-sm font-medium text-[#fafafa]">Glints</p>
                                <p class="mt-0.5 text-xs text-[#71717a]">Gunakan akun Glints</p>
                            </div>
                            <label class="relative inline-flex items-center">
                                <input
                                    type="checkbox"
                                    @checked($glintsEnabled)
                                    {{ !$hasGlints ? 'disabled' : '' }}
                                    wire:change="toggleProvider('glints', $event.target.checked)"
                                    class="peer sr-only">
                                <div class="cursor-pointer h-6 w-11 rounded-full bg-[#1f1f1f] transition peer-checked:bg-blue-600 peer-disabled:bg-[#161616]"></div>
                                <div class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-all peer-checked:translate-x-5"></div>
                            </label>
                        </label>
                    </div>

                    @if($isPaused)
                        <span class="mt-4 inline-flex w-fit rounded-md border border-amber-500/20 bg-amber-500/10 px-2 py-1 text-xs font-medium text-amber-400">
                            Automation Paused
                        </span>
                    @endif
                </div>
            </div>

            {{-- Auto Answer --}}
            <div class="flex flex-col lg:col-span-2">
                <p class="mb-3 text-xs font-medium uppercase tracking-[0.14em] text-[#71717a]">
                    Auto Answer
                </p>
                <div class="flex min-h-[170px] flex-1 flex-col justify-center gap-5 rounded-xl border border-[#262626] bg-[#0a0a0a] p-4 opacity-55 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                    <div>
                        <p class="text-sm font-medium text-[#fafafa]">AI Powered Auto Answer</p>
                        <p class="mt-1 max-w-full text-xs leading-5 text-[#71717a] sm:max-w-md">
                            Menjawab screening question secara otomatis menggunakan AI.
                        </p>
                    </div>
                    <div class="flex w-full items-center justify-between gap-3 sm:w-auto sm:justify-start">
                        <label class="relative inline-flex cursor-not-allowed items-center">
                            <input type="checkbox" disabled class="peer sr-only">
                            <div class="h-6 w-11 rounded-full border border-[#333] bg-[#1e1e1e]"></div>
                            <div class="absolute left-[4px] top-[4px] h-4 w-4 rounded-full bg-[#666]"></div>
                        </label>
                        <span class="inline-flex items-center rounded-full border border-[#333] bg-[#161616] px-3 py-1 text-xs font-medium text-[#9ca3af]">
                            Belum tersedia
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{--// resources/views/livewire/activity-timeline.blade.php--}}
{{--<div class="flex h-full flex-col">--}}
{{--    --}}{{-- Header --}}
{{--    <div class="flex items-center gap-2">--}}
{{--        <span class="relative flex h-1.5 w-1.5 sm:h-2 sm:w-2">--}}
{{--            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>--}}
{{--            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-500 sm:h-2 sm:w-2"></span>--}}
{{--        </span>--}}
{{--        <h2 class="text-sm font-semibold tracking-tight text-[#fafafa] sm:text-base">Timeline Activity</h2>--}}
{{--    </div>--}}

{{--    <p class="mt-0.5 text-[10px] text-[#a1a1aa] sm:mt-1 sm:text-xs">--}}
{{--        Log automasi berjalan real-time--}}
{{--    </p>--}}

{{--    --}}{{-- Container daftar aktivitas --}}
{{--    --}}{{-- Hapus max-h & overflow-y-auto, ganti dengan flex-1 min-h-0 --}}
{{--    <div class="custom-scroll mt-3 flex-1 min-h-0 space-y-2 pr-1 sm:mt-5 sm:space-y-4 sm:pr-2">--}}
{{--        @if(!$isReady)--}}
{{--            @foreach(range(1,3) as $i)--}}
{{--                <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-2.5 sm:p-4">--}}
{{--                    <div class="h-3 w-3/4 rounded bg-[#222] animate-pulse"></div>--}}
{{--                    <div class="mt-1.5 h-2.5 w-1/3 rounded bg-[#1c1c1e] animate-pulse"></div>--}}
{{--                    <div class="mt-3 ml-1 space-y-2 border-l border-[#262626] pl-2.5 sm:ml-2 sm:space-y-3 sm:pl-4">--}}
{{--                        <div class="h-2 w-1/2 rounded bg-[#1c1c1e] animate-pulse"></div>--}}
{{--                        <div class="h-2 w-1/3 rounded bg-[#1c1c1e] animate-pulse"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        @else--}}
{{--            @php--}}
{{--                $groupedActivities = collect($activities)->groupBy('job_id');--}}
{{--            @endphp--}}

{{--            @forelse($groupedActivities as $jobId => $groupEvents)--}}
{{--                @php--}}
{{--                    $latestEvent = $groupEvents->first();--}}
{{--                    $cardBorder = match($latestEvent['status']) {--}}
{{--                        'success' => 'border-emerald-500/30 bg-[#0a0a0a]/80',--}}
{{--                        'error'   => 'border-rose-500/30 bg-[#0a0a0a]/80',--}}
{{--                        default   => 'border-[#262626] bg-[#0a0a0a]',--}}
{{--                    };--}}
{{--                @endphp--}}

{{--                <div class="rounded-xl border {{ $cardBorder }} p-2.5 transition-colors sm:p-4" wire:key="job-{{ $jobId }}">--}}
{{--                    <div class="mb-2 sm:mb-3">--}}
{{--                        <p class="text-xs font-medium break-words text-[#fafafa] sm:text-sm">--}}
{{--                            {{ $latestEvent['job_title'] }}--}}
{{--                        </p>--}}
{{--                        <p class="mt-0.5 break-words text-[10px] text-[#a1a1aa] sm:mt-1 sm:text-xs">--}}
{{--                            {{ $latestEvent['job_company'] }}--}}
{{--                            ·--}}
{{--                            {{ ucfirst($latestEvent['provider']) }}--}}
{{--                        </p>--}}
{{--                    </div>--}}

{{--                    <div class="relative ml-1 space-y-2.5 border-l border-[#262626] pl-2.5 sm:ml-2 sm:space-y-4 sm:pl-4">--}}
{{--                        @foreach($groupEvents as $activity)--}}
{{--                            @php--}}
{{--                                $dotColor = match($activity['status']) {--}}
{{--                                    'success' => 'bg-emerald-400',--}}
{{--                                    'applied' => 'bg-violet-400',--}}
{{--                                    'questionnaire' => 'bg-amber-400',--}}
{{--                                    'linkout' => 'bg-blue-400',--}}
{{--                                    'error' => 'bg-rose-500',--}}
{{--                                    'start', 'load_job', 'load_profile', 'load_userConfig', 'inspect', 'build_payload', 'apply'--}}
{{--                                        => 'bg-sky-400 animate-pulse',--}}
{{--                                    default => 'bg-[#555]',--}}
{{--                                };--}}
{{--                                $statusLabel = match($activity['status']) {--}}
{{--                                    'success' => 'Sukses Melamar',--}}
{{--                                    'applied' => 'Sudah Dilamar',--}}
{{--                                    'questionnaire' => 'Butuh Screening',--}}
{{--                                    'linkout' => 'Linkout',--}}
{{--                                    'start' => 'Memulai proses...',--}}
{{--                                    'load_job' => 'Memuat detail pekerjaan...',--}}
{{--                                    'load_profile' => 'Memuat profil...',--}}
{{--                                    'load_userConfig' => 'Memuat konfigurasi...',--}}
{{--                                    'inspect' => 'Menggali informasi...',--}}
{{--                                    'build_payload' => 'Menyusun payload...',--}}
{{--                                    'apply' => 'Mengirim lamaran...',--}}
{{--                                    'error' => 'Gagal',--}}
{{--                                    default => ucfirst($activity['status']),--}}
{{--                                };--}}
{{--                            @endphp--}}

{{--                            <div wire:key="activity-{{ $activity['id'] }}" class="relative">--}}
{{--                                <span class="absolute -left-[0.82rem] top-1 h-1.5 w-1.5 rounded-full {{ $dotColor }} outline outline-4 outline-[#0a0a0a] sm:-left-[1.32rem] sm:top-1.5 sm:h-2 sm:w-2"></span>--}}
{{--                                <div class="flex flex-col line-leading-none">--}}
{{--                                    <span class="text-[10px] leading-tight sm:text-xs {{ $loop->first ? 'text-[#fafafa]' : 'text-[#a1a1aa]' }}">--}}
{{--                                        {{ $statusLabel }}--}}
{{--                                    </span>--}}
{{--                                    <span class="text-[9px] text-[#71717a] sm:text-[10px]">--}}
{{--                                        {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @empty--}}
{{--                <div class="py-6 text-center text-xs italic text-[#71717a] sm:text-sm">--}}
{{--                    Menunggu aktivitas...--}}
{{--                </div>--}}
{{--            @endforelse--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}

{{--// resources/views/apply.blade.php--}}
{{--@extends('layouts.app')--}}

{{--@section('title', 'Apply · Compass')--}}
{{--@section('titleNavbar', 'Apply')--}}

{{--@section('content')--}}
{{--    <div class="flex flex-col gap-6 max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 w-full">--}}

{{--        --}}{{-- MAIN LAYOUT GRID --}}
{{--        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3 items-start xl:items-stretch w-full">--}}

{{--            --}}{{-- COLUMN LEFT: STATS, PANEL & PROVIDER CONFIGURATION (2/3 Width) --}}
{{--            <div class="xl:col-span-2 flex flex-col gap-6 h-full w-full">--}}

{{--                --}}{{-- PERBAIKAN: STATS OVERVIEW SEKARANG SEJAJAR DENGAN PANEL --}}
{{--                <div class="w-full">--}}
{{--                    <livewire:stats-overview />--}}
{{--                </div>--}}

{{--                --}}{{-- Panel Kontrol --}}
{{--                <div class="w-full">--}}
{{--                    <livewire:panel-configuration :accounts="$accounts" :adapters="$adapters" />--}}
{{--                </div>--}}

{{--                --}}{{-- Konfigurasi Detail Provider (Mengisi sisa ruang ke bawah) --}}
{{--                <div class="flex-1 w-full flex flex-col">--}}
{{--                    <livewire:provider-configuration :accounts="$accounts" :adapters="$adapters" />--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- COLUMN RIGHT: LIVE MONITORING & ACTIVITY TIMELINE (1/3 Width) --}}
{{--            <div class="xl:col-span-1 flex flex-col gap-6 h-full w-full">--}}

{{--                --}}{{-- Live Monitoring Console --}}
{{--                <div class="w-full">--}}
{{--                    <livewire:live-monitoring />--}}
{{--                </div>--}}

{{--                --}}{{-- Activity Timeline Card --}}
{{--                <div class="saas-card p-5 flex flex-col flex-1 h-0 overflow-hidden w-full">--}}
{{--                    <div class="flex-1 overflow-y-auto pr-1 custom-scroll space-y-1">--}}
{{--                        <livewire:activity-timeline />--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--            </div>--}}
{{--        </section>--}}
{{--    </div>--}}
{{--@endsection--}}
{{--@push('styles')--}}
{{--    <link rel="preconnect" href="https://fonts.googleapis.com">--}}
{{--    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>--}}
{{--    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">--}}
{{--    <style>--}}
{{--        body {--}}
{{--            font-family: 'Geist', system-ui, sans-serif;--}}
{{--            background: #0A0A0A;--}}
{{--            color: #FAFAFA;--}}
{{--        }--}}
{{--        .font-mono {--}}
{{--            font-family: 'Geist Mono', monospace !important;--}}
{{--        }--}}

{{--        /* Glassmorphism SaaS Dashboard Card */--}}
{{--        .saas-card {--}}
{{--            background: #111111;--}}
{{--            border: 1px solid #262626;--}}
{{--            border-radius: 16px;--}}
{{--            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);--}}
{{--            transition: border-color 0.2s ease, box-shadow 0.2s ease;--}}
{{--        }--}}
{{--        .saas-card:hover {--}}
{{--            border-color: #333333;--}}
{{--        }--}}

{{--        /* Standar Form Input SaaS Custom Style */--}}
{{--        .saas-input {--}}
{{--            border: 1px solid #262626;--}}
{{--            background: #0A0A0A;--}}
{{--            outline: 0;--}}
{{--            transition: all 0.2s ease;--}}
{{--        }--}}
{{--        .saas-input:focus {--}}
{{--            border-color: #3B82F6;--}}
{{--            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15);--}}
{{--        }--}}

{{--        /* Rapi & Tipis Scrollbar Modifikasi */--}}
{{--        .custom-scroll::-webkit-scrollbar {--}}
{{--            width: 4px;--}}
{{--            height: 4px;--}}
{{--        }--}}
{{--        .custom-scroll::-webkit-scrollbar-track {--}}
{{--            background: transparent;--}}
{{--        }--}}
{{--        .custom-scroll::-webkit-scrollbar-thumb {--}}
{{--            background: #262626;--}}
{{--            border-radius: 9999px;--}}
{{--        }--}}
{{--        .custom-scroll::-webkit-scrollbar-thumb:hover {--}}
{{--            background: #3f3f46;--}}
{{--        }--}}
{{--    </style>--}}
{{--@endpush--}}

{{--<script>--}}
{{--    document.addEventListener('DOMContentLoaded', function() {--}}
{{--        if (window.Echo) {--}}
{{--            window.Echo.private(`users.{{ auth()->id() }}`)--}}
{{--                .listen('.JobStatus', (incomingEvent) => {--}}
{{--                    Livewire.dispatch('job-status-updated', {--}}
{{--                        payload: {--}}
{{--                            data: {--}}
{{--                                status: incomingEvent.status,--}}
{{--                                provider: incomingEvent.provider,--}}
{{--                                jobId: incomingEvent.data?.job?.id || null,--}}
{{--                                jobTitle: incomingEvent.data?.job?.title || null,--}}
{{--                                jobCompany: incomingEvent.data?.job?.company || null,--}}
{{--                            }--}}
{{--                        }--}}
{{--                    });--}}

{{--                    Livewire.dispatch('queue-status-refreshed', {--}}
{{--                        pending: incomingEvent.pending || 0--}}
{{--                    });--}}
{{--                });--}}
{{--        }--}}
{{--    });--}}
{{--</script>--}}
