<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public $isReady = false;

    public $steps = ['applied', 'questionnaire', 'loading_job', 'loading_profile', 'inspecting', 'building_payload', 'applying', 'success'];

    public $statusMap = [
        'start'           => ['step' => 'loading_job',       'description' => 'Memulai proses lamaran'],
        'load_job'        => ['step' => 'loading_job',       'description' => 'Membaca detail pekerjaan'],
        'load_profile'    => ['step' => 'loading_profile',   'description' => 'Membaca profil pelamar'],
        'load_userConfig' => ['step' => 'loading_profile',   'description' => 'Membaca konfigurasi pengguna'],
        'inspect'         => ['step' => 'inspecting',        'description' => 'Memeriksa apakah dapat dilamar'],
        'build_payload'   => ['step' => 'building_payload',  'description' => 'Membangun payload lamaran'],
        'apply'           => ['step' => 'applying',          'description' => 'Mengirim lamaran'],
        'questionnaire'   => ['step' => 'questionnaire',     'description' => 'Dilewati, perlu menjawab pertanyaan screening'],
        'applied'         => ['step' => 'applied',           'description' => 'Dilewati, posisi ini sudah pernah dilamar sebelumnya'],
        'success'         => ['step' => 'success',           'description' => 'Berhasil melamar pekerjaan baru'],
        'error'           => ['step' => 'success',           'description' => 'Terjadi kesalahan pada sistem'],
    ];

    public function init()
    {
        $this->isReady = true;
    }


    #[On('job-status-updated')]
    public function onJobStatus($data = null, $status = null, $provider = null, $job_id = null, $job_title = null)
    {
        if (is_array($data)) {
            $status    = $data['status'] ?? $status;
            $provider  = $data['provider'] ?? $provider;
            $job_id    = $data['jobId'] ?? ($data['job_id'] ?? $job_id);
            $job_title = $data['jobTitle'] ?? ($data['job_title'] ?? $job_title);
        }

        $mappedData = $this->statusMap[$status] ?? null;
        $displayJob = $job_title ?? ($job_id ? substr($job_id, 0, 12) . '...' : '-');

        $this->dispatch('animate-status-step', [
            'status'   => $status,
            'provider' => $provider ?? '-',
            'jobId'    => $displayJob,
            'mapped'   => $mappedData
        ]);
    }

    #[On('queue-status-refreshed')]
    public function onQueueRefreshed($pending = 0)
    {
        $this->dispatch('update-remaining-jobs', ['pending' => $pending]);
    }
};
?>

<div class="saas-card p-6 xl:col-span-2" wire:init="init"
     x-data="{
        isReady: @entangle('isReady'),
        currentProvider: '-',
        currentJob: '-',
        currentStage: 'waiting',
        remainingJobs: 0,
        logLine: 'No active process',
        steps: @js($steps),
        eventQueue: [],
        isProcessingQueue: false,

        // Mengatur persentase progress bar secara dinamis di frontend
        get progressPercent() {
            let idx = this.steps.indexOf(this.currentStage);
            return idx !== -1 ? Math.round(((idx + 1) / this.steps.length) * 100) : 0;
        },

        // Mengantre event yang masuk berbarengan dari Pusher
        queueEvent(e) {
            this.eventQueue.push(e.detail[0]);
            if (!this.isProcessingQueue) {
                this.processNextEvent();
            }
        },

        // Memproses langkah satu per satu dengan delay buatan agar transisi UI terlihat jelas
        async processNextEvent() {
            if (this.eventQueue.length === 0) {
                this.isProcessingQueue = false;
                return;
            }

            this.isProcessingQueue = true;
            let data = this.eventQueue.shift();

            if (data.provider) this.currentProvider = data.provider;
            if (data.jobId) this.currentJob = data.jobId;

            if (data.mapped) {
                if (data.mapped.step) this.currentStage = data.mapped.step;
                this.logLine = data.mapped.description;
            }

            // Atur waktu jeda (600ms). Silakan kecilkan atau besarkan sesuai selera estetika UI kamu
            await new Promise(resolve => setTimeout(resolve, 600));

            this.processNextEvent();
        }
     }"
     @animate-status-step.window="queueEvent($event)"
     @update-remaining-jobs.window="remainingJobs = $event.detail[0].pending">

    {{-- SKELETON LOADING VIEW (Menggunakan x-show agar DOM tidak hancur saat transisi) --}}
    <div x-show="!isReady" class="space-y-5">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            @foreach(range(1, 3) as $i)
                <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
                    <div class="h-3 w-20 rounded-lg bg-[#222] animate-pulse"></div>
                    <div class="mt-2 h-4 w-28 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
            <div class="h-2 w-full rounded-full bg-[#222] animate-pulse"></div>
            <div class="mt-4 h-4 w-2/3 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
        </div>
    </div>

    {{-- REAL DATA AUTOMATION VIEW (Menggunakan x-show agar reactivity antrean Alpine bekerja sempurna) --}}
    <div x-show="isReady" x-cloak class="space-y-5">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
                <p class="text-xs text-[#a1a1aa]">Current provider</p>
                <p class="mt-1 text-sm font-medium text-[#fafafa]" x-text="currentProvider"></p>
            </div>
            <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
                <p class="text-xs text-[#a1a1aa]">Current job</p>
                <p class="mt-1 text-sm font-medium text-[#fafafa]" x-text="currentJob"></p>
            </div>
            <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
                <p class="text-xs text-[#a1a1aa]">Current stage</p>
                <p class="mt-1 text-sm font-medium text-[#fafafa]" x-text="currentStage.replace('_', ' ').toUpperCase()"></p>
            </div>
        </div>

        <div class="mt-5 rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
            <div class="flex items-center justify-between text-xs text-[#a1a1aa]">
                <span>Progress</span>
                <span>Estimated remaining jobs: <span x-text="remainingJobs"></span></span>
            </div>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#1b1b1b]">
                <div class="h-full rounded-full bg-blue-600 transition-all duration-500" :style="'width: ' + progressPercent + '%' integrity"></div>
            </div>

            {{-- BADGES STEP LOOPING DENGAN DUKUNGAN ALPINE REALTIME --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <template x-for="step in steps" :key="step">
                    <span class="progress-step" :class="step === currentStage ? 'active' : ''" x-text="step.replace('_', ' ')"></span>
                </template>
            </div>
            <p class="mt-4 text-sm text-[#a1a1aa]" x-text="logLine"></p>
        </div>
    </div>
</div>
