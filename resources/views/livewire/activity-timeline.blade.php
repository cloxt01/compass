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
        $data = $payload['data'] ?? $payload;

        if (empty($data['status'])) {
            return;
        }

        foreach ($this->activities as $activity) {
            if (
                $activity['job_id'] === ($data['jobId'] ?? '')
                && $activity['status'] === $data['status']
            ) {
                return;
            }
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

        $this->activities = array_slice($this->activities, 0, $this->limit);
    }
};
?>

<div>

    <h2 class="text-base font-semibold tracking-tight text-[#fafafa] sm:text-lg">
        Timeline Activity
    </h2>

    <p class="mt-1 text-xs text-[#a1a1aa] sm:text-sm">
        Detailed process per job
    </p>

    <div class="custom-scroll mt-4 max-h-[420px] space-y-3 overflow-y-auto pr-1 sm:mt-5 sm:max-h-[580px] sm:space-y-4 sm:pr-2">

        @if(!$isReady)

            @foreach(range(1,3) as $i)
                <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-3 sm:p-4">
                    <div class="h-3.5 w-3/4 rounded bg-[#222] animate-pulse"></div>
                    <div class="mt-2 h-3 w-1/3 rounded bg-[#1c1c1e] animate-pulse"></div>

                    <div class="mt-4 ml-1 space-y-3 border-l border-[#262626] pl-3 sm:ml-2 sm:pl-4">
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
                        'success' => 'border-emerald-500/40 bg-[#0a0a0a]/80',
                        'error'   => 'border-rose-500/40 bg-[#0a0a0a]/80',
                        default   => 'border-[#262626] bg-[#0a0a0a]',
                    };
                @endphp

                <div
                    class="rounded-xl border {{ $cardBorder }} p-3 transition-colors sm:p-4"
                    wire:key="job-{{ $jobId }}"
                >

                    <div class="mb-3">
                        <p class="text-sm font-medium break-words text-[#fafafa] sm:text-base">
                            {{ $latestEvent['job_title'] }}
                        </p>

                        <p class="mt-1 break-words text-[11px] text-[#a1a1aa] sm:text-xs">
                            {{ $latestEvent['job_company'] }}
                            ·
                            {{ ucfirst($latestEvent['provider']) }}
                        </p>
                    </div>

                    <div class="relative ml-1 space-y-3 border-l border-[#262626] pl-3 sm:ml-2 sm:space-y-4 sm:pl-4">

                        @foreach($groupEvents as $activity)

                            @php
                                $dotColor = match($activity['status']) {
                                    'success' => 'bg-emerald-400',
                                    'applied' => 'bg-violet-400',
                                    'questionnaire' => 'bg-amber-400',
                                    'linkout' => 'bg-blue-400',
                                    'error' => 'bg-rose-500',
                                    'start',
                                    'load_job',
                                    'load_profile',
                                    'load_userConfig',
                                    'inspect',
                                    'build_payload',
                                    'apply'
                                        => 'bg-sky-400 animate-pulse',
                                    default => 'bg-[#555]',
                                };

                                $statusLabel = match($activity['status']) {
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

                                <span class="absolute -left-[1rem] top-1.5 h-2 w-2 rounded-full {{ $dotColor }} outline outline-4 outline-[#0a0a0a] sm:-left-[1.32rem]"></span>

                                <div class="flex flex-col">

                                    <span class="text-[11px] sm:text-xs {{ $loop->first ? 'text-[#fafafa]' : 'text-[#a1a1aa]' }}">
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

                <div class="py-6 text-center text-sm italic text-[#71717a]">
                    Menunggu aktivitas...
                </div>

            @endforelse

        @endif

    </div>

</div>
