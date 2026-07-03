<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $isReady = false;
    public $limit = 20;
    public $activities = [];

    public function mount()
    {
        $rows = Application::where('user_id', Auth::id())
            ->latest()
            ->limit($this->limit)
            ->get();

        // Map data dari database agar strukturnya konsisten dengan event realtime
        $this->activities = $rows->map(function ($item) {
            return [
                'id'          => $item->id,
                'status'      => $item->status,
                'provider'    => $item->provider,
                'job_title'   => $item->job_title,
                'job_company' => $item->job_company ?? 'Unknown Company', // Pastikan kolom ini ada di DB atau fallback
                'created_at'  => $item->created_at->toIso8601String(),
            ];
        })->toArray();
    }

    public function init()
    {
        $this->isReady = true;
    }

    #[On('job-status-updated')]
    public function appendActivity($payload = [])
    {
        // 1. SESUAIKAN: Ambil data dari dalam wrapper 'data' yang dikirim JS
        $data = $payload['data'] ?? [];

        if (empty($data)) {
            return;
        }

        $status      = $data['status'] ?? null;
        $provider    = $data['provider'] ?? '-';
        $jobTitle    = $data['jobTitle'] ?? 'Unknown Position';
        $jobCompany  = $data['jobCompany'] ?? 'Unknown Company';

        // Tambahkan 'error' atau status awal jika ingin ditampilkan di timeline
        $finalStatuses = ['success', 'applied', 'questionnaire', 'linkout', 'error', 'start'];
        if (!in_array($status, $finalStatuses)) {
            return;
        }

        // 2. Dorong data baru ke urutan paling atas timeline
        array_unshift($this->activities, [
            'id'          => uniqid(),
            'status'      => $status,
            'provider'    => $provider,
            'job_title'   => $jobTitle,
            'job_company' => $jobCompany,
            'created_at'  => now()->toIso8601String(),
        ]);

        $this->activities = array_slice($this->activities, 0, $this->limit);
    }
};
?>

<div wire:init="init">
    <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Timeline Activity</h2>
    <p class="mt-1 text-sm text-[#a1a1aa]">Newest events first</p>

    <div class="custom-scroll mt-5 max-h-[520px] space-y-3 overflow-y-auto pr-1">
        @if(!$isReady)
            {{-- SKELETON SAAT FIRST LOAD --}}
            @foreach(range(1, 5) as $i)
                <div class="flex items-start gap-3 rounded-xl border border-[#262626] bg-[#0a0a0a] p-3">
                    <div class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-[#333] animate-pulse"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3.5 w-3/4 rounded-lg bg-[#222] animate-pulse"></div>
                        <div class="h-3 w-1/3 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
                    </div>
                </div>
            @endforeach
        @else
            @forelse ($activities as $activity)
                @php
                    $dotColor = match($activity['status']) {
                        'success'       => 'bg-emerald-400',
                        'applied'       => 'bg-violet-400',
                        'questionnaire' => 'bg-amber-400',
                        'linkout'       => 'bg-blue-400',
                        'start'         => 'bg-sky-400 animate-pulse',
                        'error'         => 'bg-rose-500',
                        default         => 'bg-[#555]',
                    };
                    $statusLabel = match($activity['status']) {
                        'success'       => 'Success Applied',
                        'applied'       => 'Already Applied',
                        'questionnaire' => 'Need Screening',
                        'linkout'       => 'Linkout',
                        'start'         => 'Processing...',
                        'error'         => 'Failed',
                        default         => 'Expired',
                    };
                @endphp
                <div class="flex items-start gap-3 rounded-xl border border-[#262626] bg-[#0a0a0a] p-3" wire:key="activity-{{ $activity['id'] }}">
                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $dotColor }}"></span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-[#fafafa]">{{ $activity['job_title'] }}</p>
                        <p class="text-xs text-[#a1a1aa] mt-0.5">{{ $activity['job_company'] }}</p>

                        <p class="mt-1.5 text-[11px] text-[#71717a]">
                            <span class="font-semibold text-[#a1a1aa]">{{ $statusLabel }}</span> · {{ ucfirst($activity['provider']) }} · {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="italic text-sm text-[#71717a] text-center py-4">Menunggu aktivitas...</div>
            @endforelse
        @endif
    </div>
</div>
