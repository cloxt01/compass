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
    public function onJobStatus($payload = null, $status = null, $provider = null, $job_id = null, $job_title = null)
    {
        // Ekstrak isi pembungkus 'data' dari JS payload
        if (is_array($payload) && isset($payload['data'])) {
            $innerData = $payload['data'];
            $status    = $innerData['status'] ?? $status;
            $provider  = $innerData['provider'] ?? $provider;
            $job_id    = $innerData['jobId'] ?? ($innerData['job_id'] ?? $job_id);
            $job_title = $innerData['jobTitle'] ?? ($innerData['job_title'] ?? $job_title);
        } elseif (is_array($payload)) {
            // Fallback jika dikirim tanpa wrapper 'data'
            $status    = $payload['status'] ?? $status;
            $provider  = $payload['provider'] ?? $provider;
            $job_id    = $payload['jobId'] ?? ($payload['job_id'] ?? $job_id);
            $job_title = $payload['jobTitle'] ?? ($payload['job_title'] ?? $job_title);
        }

        $mappedData = $this->statusMap[$status] ?? null;

        // Utamakan jobTitle agar yang muncul di UI berupa nama posisi (bukan UUID)
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
        lastRawStatus: null, // <-- Tambahan: Menyimpan status asli terakhir untuk validasi duplikat
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
            let data = e.detail[0] || e.detail;

            // --- LOGIKA ANTI DUPLIKAT ---
            // 1. Cek dengan event terakhir di dalam antrean (jika antrean tidak kosong)
            let lastInQueue = this.eventQueue.length > 0 ? this.eventQueue[this.eventQueue.length - 1] : null;
            if (lastInQueue && lastInQueue.jobId === data.jobId && lastInQueue.status === data.status) {
                return; // Abaikan event, ini duplikat beruntun
            }

            // 2. Cek dengan event yang SEDANG tampil di layar saat ini
            if (this.currentJob === data.jobId && this.lastRawStatus === data.status) {
                return; // Abaikan event, layar sudah menampilkan ini
            }
            // ----------------------------

            this.eventQueue.push(data);

            if (!this.isProcessingQueue) {
                this.processNextEvent();
            }
        },

        // Memproses langkah secara instan tanpa delay
        async processNextEvent() {
            if (this.eventQueue.length === 0) {
                this.isProcessingQueue = false;
                return;
            }

            this.isProcessingQueue = true;
            let data = this.eventQueue.shift();

            // Render ke layar
            if (data.provider) this.currentProvider = data.provider;
            if (data.jobId) this.currentJob = data.jobId;
            if (data.status) this.lastRawStatus = data.status; // Simpan status aslinya untuk validasi

            if (data.mapped) {
                if (data.mapped.step) this.currentStage = data.mapped.step;
                this.logLine = data.mapped.description;
            }

            // Langsung lanjut ke event berikutnya (Delay dihapus)
            this.processNextEvent();
        }
     }"
     @animate-status-step.window="queueEvent($event)"
     @update-remaining-jobs.window="remainingJobs = $event.detail[0].pending">

    {{-- SKELETON LOADING VIEW --}}
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

    {{-- REAL DATA AUTOMATION VIEW --}}
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
                <div class="h-full rounded-full bg-blue-600 transition-all duration-500" :style="'width: ' + progressPercent + '%'"></div>
            </div>

            {{-- BADGES STEP LOOPING --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <template x-for="step in steps" :key="step">
                    <span
                        class="rounded-full border px-3 py-1 text-[11px] font-medium capitalize tracking-wide transition-colors duration-200"
                        :class="step === currentStage
                            ? 'border-blue-500 bg-blue-500/20 text-[#fafafa]'
                            : 'border-[#262626] bg-[#0a0a0a] text-[#a1a1aa]'"
                        x-text="step.replace('_', ' ')">
                    </span>
                </template>
            </div>
            <p class="mt-4 text-sm text-[#a1a1aa]" x-text="logLine"></p>
        </div>
    </div>
</div>
