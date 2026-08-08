<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public $isReady = false;

    public $steps = ['resume','limit_provider', 'expired','applied', 'questionnaire', 'loading_job', 'loading_profile', 'inspecting', 'building_payload', 'applying', 'success'];

    public $statusMap = [
        'start'           => ['step' => 'loading_job',       'description' => 'Memulai proses lamaran'],
        'load_job'        => ['step' => 'loading_job',       'description' => 'Membaca detail pekerjaan'],
        'load_profile'    => ['step' => 'loading_profile',   'description' => 'Membaca profil pelamar'],
        'load_userConfig' => ['step' => 'loading_profile',   'description' => 'Membaca konfigurasi pengguna'],
        'inspect'         => ['step' => 'inspecting',        'description' => 'Memeriksa apakah dapat dilamar'],
        'build_payload'   => ['step' => 'building_payload',  'description' => 'Membangun payload lamaran'],
        'apply'           => ['step' => 'applying',          'description' => 'Mengirim lamaran'],
        'resume'         => ['step' => 'resume',           'description' => 'Dilewati, pelamar tidak memiliki resume'],
        'questionnaire'   => ['step' => 'questionnaire',     'description' => 'Dilewati, perlu menjawab pertanyaan screening'],
        'expired'         => ['step' => 'expired',           'description' => 'Dilewati, posisi ini sudah tidak tersedia'],
        'applied'         => ['step' => 'applied',           'description' => 'Dilewati, posisi ini sudah pernah dilamar sebelumnya'],
        'limit_provider'  => ['step' => 'limit_provider',    'description' => 'Dilewati, limit provider tercapai'],
        'success'         => ['step' => 'success',           'description' => 'Berhasil melamar pekerjaan baru'],
        'error'           => ['step' => 'success',           'description' => 'Terjadi kesalahan pada sistem'],
    ];

    public function init()
    {
        $this->isReady = true;
    }

    #[On('job-status-updated')]
    public function onJobStatus($payload = null)
    {
        $items = $payload['batch'] ?? [$payload['data'] ?? $payload];

        foreach ($items as $data) {
            $status    = $data['status'] ?? null;
            if (!$status) {
                continue;
            }

            $provider  = $data['provider'] ?? '-';
            $job_id    = $data['jobId'] ?? null;
            $job_title = $data['jobTitle'] ?? null;

            $mappedData = $this->statusMap[$status] ?? null;
            $displayJob = $job_title ?? ($job_id ? substr($job_id, 0, 12) . '...' : '-');

            $this->dispatch('animate-status-step', [
                'status'   => $status,
                'provider' => $provider,
                'jobId'    => $displayJob,
                'mapped'   => $mappedData
            ]);
        }
    }

    #[On('queue-status-refreshed')]
    public function onQueueRefreshed($pending = 0)
    {
        $this->dispatch('update-remaining-jobs', ['pending' => $pending]);
    }
};
?>

<div class="saas-card p-4 sm:p-5 xl:col-span-2 relative overflow-hidden" wire:init="init"
     x-data="{
        isReady: @entangle('isReady'),
        currentProvider: '-',
        currentJob: '-',
        currentStage: 'waiting',
        lastRawStatus: null,
        remainingJobs: 0,
        logLine: 'No active process',
        steps: @js($steps),
        eventQueue: [],
        isProcessingQueue: false,

        get progressPercent() {
            let idx = this.steps.indexOf(this.currentStage);
            return idx !== -1 ? Math.round(((idx + 1) / this.steps.length) * 100) : 0;
        },

        queueEvent(e) {
            let data = e.detail[0] || e.detail;

            let lastInQueue = this.eventQueue.length > 0 ? this.eventQueue[this.eventQueue.length - 1] : null;
            if (lastInQueue && lastInQueue.jobId === data.jobId && lastInQueue.status === data.status) {
                return;
            }

            if (this.currentJob === data.jobId && this.lastRawStatus === data.status) {
                return;
            }

            this.eventQueue.push(data);

            if (!this.isProcessingQueue) {
                this.processNextEvent();
            }
        },

        async processNextEvent() {
            if (this.eventQueue.length === 0) {
                this.isProcessingQueue = false;
                return;
            }

            this.isProcessingQueue = true;
            let data = this.eventQueue.shift();

            if (data.provider) this.currentProvider = data.provider;
            if (data.jobId) this.currentJob = data.jobId;
            if (data.status) this.lastRawStatus = data.status;

            if (data.mapped) {
                if (data.mapped.step) this.currentStage = data.mapped.step;
                this.logLine = data.mapped.description;
            }

            this.processNextEvent();
        }
     }"
     @animate-status-step.window="queueEvent($event)"
     @update-remaining-jobs.window="remainingJobs = $event.detail[0].pending">

    {{-- SKELETON LOADING VIEW --}}
    <div x-show="!isReady" class="space-y-4">
        <div class="flex flex-col gap-1 border-b border-[#262626] pb-4">
            <div class="h-4 w-32 rounded bg-[#222] animate-pulse"></div>
            <div class="mt-1.5 h-3 w-52 rounded bg-[#1c1c1e] animate-pulse"></div>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            @foreach(range(1, 3) as $i)
                <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-3.5">
                    <div class="h-2.5 w-16 rounded bg-[#222] animate-pulse"></div>
                    <div class="mt-2 h-4 w-24 rounded bg-[#1c1c1e] animate-pulse"></div>
                </div>
            @endforeach
        </div>
        <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4 space-y-3">
            <div class="flex justify-between">
                <div class="h-2.5 w-12 rounded bg-[#222] animate-pulse"></div>
                <div class="h-2.5 w-24 rounded bg-[#222] animate-pulse"></div>
            </div>
            <div class="h-1.5 w-full rounded-full bg-[#222] animate-pulse"></div>
        </div>
    </div>

    {{-- REAL DATA AUTOMATION VIEW --}}
    <div x-show="isReady" x-cloak class="space-y-4 sm:space-y-5">

        {{-- CARD COMPONENT HEADER --}}
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#262626] pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-1.5 w-1.5 sm:h-2 sm:w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-500 sm:h-2 sm:w-2"></span>
                    </span>
                    <h2 class="text-sm font-semibold tracking-tight text-[#fafafa] sm:text-base">Live Monitoring</h2>
                </div>
                <p class="mt-0.5 text-[10px] text-[#a1a1aa] sm:text-xs">Pantau proses pengiriman lamaran otomatis secara real-time</p>
            </div>
        </div>

        {{-- GRID METRICS STATS (Dioptimalkan agar tidak pecah/meluap) --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            {{-- PROVIDER --}}
            <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-3.5 transition-colors duration-200 hover:border-zinc-800 flex flex-col justify-between min-w-0">
                <p class="text-[10px] font-medium uppercase tracking-wider text-zinc-500">Active Provider</p>
                <p class="mt-1 text-xs font-semibold tracking-tight text-[#fafafa] font-mono capitalize truncate" x-text="currentProvider"></p>
            </div>
            {{-- JOB ID --}}
            <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-3.5 transition-colors duration-200 hover:border-zinc-800 flex flex-col justify-between min-w-0">
                <p class="text-[10px] font-medium uppercase tracking-wider text-zinc-500">Current Job Title</p>
                <p class="mt-1 text-xs font-semibold tracking-tight text-[#fafafa] font-mono line-clamp-2 min-h-[2rem]" x-text="currentJob"></p>
            </div>
            {{-- STAGE --}}
            <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-3.5 transition-colors duration-200 hover:border-blue-900/30 flex flex-col justify-between min-w-0">
                <p class="text-[10px] font-medium uppercase tracking-wider text-zinc-500">Current Stage</p>
                <p class="mt-1 text-xs font-semibold tracking-tight text-blue-400 font-mono truncate" x-text="currentStage.replace('_', ' ').toUpperCase()"></p>
            </div>
        </div>

        {{-- LOG & PROGRESS BOX --}}
        <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4 space-y-3.5">

            {{-- PROGRESS TRACK --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-[11px] font-mono">
                    <span class="text-zinc-400">Task Completion Rate</span>
                    <span class="text-blue-400 font-bold" x-text="progressPercent + '%'"></span>
                </div>
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-[#141414] border border-zinc-900">
                    <div class="h-full rounded-full bg-blue-500 shadow-[0_0_12px_rgba(59,130,246,0.5)] transition-all duration-500 ease-out"
                         :style="'width: ' + progressPercent + '%'"></div>
                </div>
            </div>

            {{-- WORKFLOW PROCESS BADGES --}}
            <div class="pt-2 border-t border-zinc-900/60">
                <div class="flex flex-wrap gap-1">
                    <template x-for="step in steps" :key="step">
                        <span
                            class="rounded-md border px-2 py-0.5 text-[10px] font-mono tracking-wide transition-all duration-200"
                            :class="step === currentStage
                                ? 'border-blue-500/30 bg-blue-500/10 text-blue-400 font-medium'
                                : 'border-[#262626]/60 bg-[#070708] text-zinc-600'"
                            x-text="step.replace('_', ' ').toLowerCase()">
                        </span>
                    </template>
                </div>
            </div>

            {{-- LOG LINE CONSOLE --}}
            <div class="mt-1 rounded-lg bg-[#050505] border border-zinc-900 p-2.5 flex items-start gap-2.5">
                <span class="text-[11px] font-mono text-zinc-600 select-none">&gt;_</span>
                <p class="text-[11px] font-mono text-zinc-300 leading-normal tracking-wide flex-1" x-text="logLine"></p>
            </div>

        </div>
    </div>
</div>
