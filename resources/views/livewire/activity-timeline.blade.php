<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public bool $isReady = false;

    public int $limit = 15;

    public array $activities = [];

    public function mount(): void
    {
        $this->isReady = true;
        $this->loadActivities();
    }

    public function loadActivities(): void
    {
        $this->activities = Application::where('user_id', Auth::id())
            ->latest()
            ->limit($this->limit)
            ->get()
            ->map(fn ($item) => [
                'id'          => $item->id,
                'job_id'      => $item->job_id,
                'status'      => $item->status,
                'provider'    => $item->provider,
                'job_title'   => $item->job_title,
                'job_company' => $item->job_company ?? 'Unknown Company',
                'created_at'  => $item->created_at->toIso8601String(),
            ])
            ->toArray();
    }

    #[On('job-status-updated')]
    public function appendActivity($payload = []): void
    {
        $items = $payload['batch'] ?? [$payload['data'] ?? $payload];

        foreach ($items as $data) {
            if (empty($data['status'])) {
                continue;
            }

            $isDuplicate = false;
            foreach ($this->activities as $activity) {
                if (
                    $activity['job_id'] === ($data['jobId'] ?? '')
                    && $activity['status'] === $data['status']
                ) {
                    $isDuplicate = true;
                    break;
                }
            }
            if ($isDuplicate) {
                continue;
            }

            array_unshift($this->activities, [
                'id'          => uniqid(),
                'job_id'      => $data['jobId'] ?? md5($data['jobTitle'] ?? ''),
                'status'      => $data['status'],
                'provider'    => $data['provider'] ?? '-',
                'job_title'   => $data['jobTitle'] ?? 'Unknown',
                'job_company' => $data['jobCompany'] ?? 'Unknown',
                'created_at'  => now()->toIso8601String(),
            ]);
        }

        $this->activities = array_slice($this->activities, 0, $this->limit);
    }
};
?>

{{-- Root div harus flex-col dan tinggi penuh --}}
<div class="flex h-full flex-col">
    {{-- Header --}}
    <div class="flex items-center gap-2">
        <span class="relative flex h-1.5 w-1.5 sm:h-2 sm:w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-blue-500 sm:h-2 sm:w-2"></span>
        </span>
        <h2 class="text-sm font-semibold tracking-tight text-[#fafafa] sm:text-base">Timeline Activity</h2>
    </div>

    <p class="mt-0.5 text-[10px] text-[#a1a1aa] sm:mt-1 sm:text-xs">
        Log automasi berjalan real-time
    </p>

    {{-- Container daftar aktivitas --}}
    {{-- Hapus max-h & overflow-y-auto, ganti dengan flex-1 min-h-0 --}}
    <div class="custom-scroll mt-3 flex-1 min-h-0 space-y-2 pr-1 sm:mt-5 sm:space-y-4 sm:pr-2">
        @if(!$isReady)
            @foreach(range(1,3) as $i)
                <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-2.5 sm:p-4">
                    <div class="h-3 w-3/4 rounded bg-[#222] animate-pulse"></div>
                    <div class="mt-1.5 h-2.5 w-1/3 rounded bg-[#1c1c1e] animate-pulse"></div>
                    <div class="mt-3 ml-1 space-y-2 border-l border-[#262626] pl-2.5 sm:ml-2 sm:space-y-3 sm:pl-4">
                        <div class="h-2 w-1/2 rounded bg-[#1c1c1e] animate-pulse"></div>
                        <div class="h-2 w-1/3 rounded bg-[#1c1c1e] animate-pulse"></div>
                    </div>
                </div>
            @endforeach
        @else
            @php
                $groupedActivities = collect($activities)->groupBy('job_id');
            @endphp

            @forelse($groupedActivities as $jobId => $groupEvents)
                @php
                    $latestEvent = $groupEvents->first();
                    $cardBorder = match($latestEvent['status']) {
                        'success' => 'border-emerald-500/30 bg-[#0a0a0a]/80',
                        'error'   => 'border-rose-500/30 bg-[#0a0a0a]/80',
                        default   => 'border-[#262626] bg-[#0a0a0a]',
                    };
                @endphp

                <div class="rounded-xl border {{ $cardBorder }} p-2.5 transition-colors sm:p-4" wire:key="job-{{ $jobId }}">
                    <div class="mb-2 sm:mb-3">
                        <p class="text-xs font-medium break-words text-[#fafafa] sm:text-sm">
                            {{ $latestEvent['job_title'] }}
                        </p>
                        <p class="mt-0.5 break-words text-[10px] text-[#a1a1aa] sm:mt-1 sm:text-xs">
                            {{ $latestEvent['job_company'] }}
                            ·
                            {{ ucfirst($latestEvent['provider']) }}
                        </p>
                    </div>

                    <div class="relative ml-1 space-y-2.5 border-l border-[#262626] pl-2.5 sm:ml-2 sm:space-y-4 sm:pl-4">
                        @foreach($groupEvents as $activity)
                            @php
                                $dotColor = match($activity['status']) {
                                    'success' => 'bg-emerald-400',
                                    'applied' => 'bg-violet-400',
                                    'resume' => 'bg-yellow-400',
                                    'questionnaire' => 'bg-amber-400',
                                    'linkout' => 'bg-blue-400',
                                    'error' => 'bg-rose-500',
                                    'start', 'load_job', 'load_profile', 'load_userConfig', 'inspect', 'build_payload', 'apply'
                                        => 'bg-sky-400 animate-pulse',
                                    default => 'bg-[#555]',
                                };
                                $statusLabel = match($activity['status']) {
                                    'resume' => 'Butuh Resume',
                                    'success' => 'Sukses Melamar',
                                    'applied' => 'Sudah Dilamar',
                                    'questionnaire' => 'Butuh Screening',
                                    'linkout' => 'Linkout',
                                    'start' => 'Memulai proses...',
                                    'load_job' => 'Memuat detail pekerjaan...',
                                    'load_profile' => 'Memuat profil...',
                                    'load_userConfig' => 'Memuat konfigurasi...',
                                    'inspect' => 'Menggali informasi...',
                                    'build_payload' => 'Menyusun payload...',
                                    'apply' => 'Mengirim lamaran...',
                                    'error' => 'Gagal',
                                    default => ucfirst($activity['status']),
                                };
                            @endphp

                            <div wire:key="activity-{{ $activity['id'] }}" class="relative">
                                <span class="absolute -left-[0.82rem] top-1 h-1.5 w-1.5 rounded-full {{ $dotColor }} outline outline-4 outline-[#0a0a0a] sm:-left-[1.32rem] sm:top-1.5 sm:h-2 sm:w-2"></span>
                                <div class="flex flex-col line-leading-none">
                                    <span class="text-[10px] leading-tight sm:text-xs {{ $loop->first ? 'text-[#fafafa]' : 'text-[#a1a1aa]' }}">
                                        {{ $statusLabel }}
                                    </span>
                                    <span class="text-[9px] text-[#71717a] sm:text-[10px]">
                                        {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-xs italic text-[#71717a] sm:text-sm">
                    Menunggu aktivitas...
                </div>
            @endforelse
        @endif
    </div>
</div>
