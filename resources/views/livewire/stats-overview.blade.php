<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public bool $isReady = false;

    public int $todayTotal = 200;

    public function init(): void
    {
        $this->isReady = true;
    }

    #[On('job-status-updated')]
    public function refreshStats(): void
    {
        // Livewire akan re-render otomatis lewat with()
    }

    public function with(): array
    {
        $userId = Auth::id();

        if (! $this->isReady) {
            return [
                'pending' => 0,
                'processing' => 0,
                'successCount' => 0,
                'appliedCount' => 0,
                'successRate' => 0,
                'todayDone' => 0,
                'roundId' => null,
                'nextRun' => null,
                'lastRun' => null,
                'lastStatus' => null,
            ];
        }

        $queueStats = DB::table('apply_queue')
            ->join('jobs', 'apply_queue.job_id', '=', 'jobs.id')
            ->where('apply_queue.user_id', $userId)
            ->selectRaw("
                COUNT(CASE WHEN jobs.reserved_at IS NULL THEN 1 END) AS pending,
                COUNT(CASE WHEN jobs.reserved_at IS NOT NULL THEN 1 END) AS processing
            ")
            ->first();

        $pending = $queueStats->pending ?? 0;
        $processing = $queueStats->processing ?? 0;

        $successCount = Application::whereUserId($userId)
            ->where('status', 'success')
            ->count();

        $appliedCount = Application::whereUserId($userId)
            ->where('status', 'applied')
            ->count();

        $totalApplications = Application::whereUserId($userId)->count();

        $successRate = $totalApplications
            ? round(($successCount / $totalApplications) * 100)
            : 0;

        $todayDone = Application::where('user_id', $userId)
            ->where('status', '=', 'success')
            ->whereDate('created_at', today())
            ->count();

        $schedule = DB::table('schedules')
            ->where('signature', 'app:apply-scheduler')
            ->first();
        $round = DB::table('rounds')
            ->latest()
            ->first();

        return [
            'pending' => $pending,
            'processing' => $processing,
            'successCount' => $successCount,
            'appliedCount' => $appliedCount,
            'successRate' => $successRate,
            'todayDone' => $todayDone,
            'roundId' => $round->id ?? null,
            'nextRun' => $schedule->next_run ?? null,
            'lastRun' => $schedule->last_run ?? null,
            'lastStatus' => $schedule->last_status ?? null,
        ];
    }
};
?>

<div wire:init="init" wire:poll.30s="refreshStats">

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-4">


        <article class="saas-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                    NEXT ROUND
                    @if($roundId)
                        <span class="text-[#52525b]">#{{ $roundId }}</span>
                    @endif
                </p>
            </div>

            @if(!$isReady)
                <div class="mt-3.5 h-8 w-32 rounded-lg bg-[#222] animate-pulse"></div>
            @elseif($nextRun)
                <div
                    x-data="{
                target: new Date('{{ \Carbon\Carbon::parse($nextRun)->toIso8601String() }}').getTime(),
                isRunning: false,
                tick() {
                    this.isRunning = (this.target - Date.now()) <= 0;
                }
            }"
                    x-init="tick(); setInterval(() => tick(), 1000)"
                >
                    <div class="mt-3 flex items-center gap-2">
                        <template x-if="isRunning">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-500 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                    </span>
                        </template>
                        <template x-if="!isRunning">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#3f3f46]"></span>
                    </span>
                        </template>

                        <p class="text-lg font-medium text-[#fafafa]">
                            {{ \Carbon\Carbon::parse($nextRun)->format('d M, H:i:s') }}
                        </p>
                    </div>

                    <p class="mt-1 text-[11px] text-[#71717a]">
                        Last run:
                        <span class="{{ $lastStatus === 'success' ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $lastStatus === 'success' ? 'Success' : 'Failed' }}
                </span>
                        · {{ $lastRun ? \Carbon\Carbon::parse($lastRun)->diffForHumans() : '-' }}
                    </p>
                </div>
            @else
                <p class="mt-3 text-lg font-medium text-[#71717a]">
                    Not scheduled
                </p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                TOTAL PROSES
            </p>

            @if(!$isReady)
                <div class="mt-3.5 h-8 w-14 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <p class="mt-3 text-3xl font-semibold text-[#fafafa] transition-all duration-300">
                    {{ $processing }}
                </p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                ANTRIAN
            </p>

            @if(!$isReady)
                <div class="mt-3.5 h-8 w-14 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <p class="mt-3 text-3xl font-semibold text-[#fafafa] transition-all duration-300">
                    {{ $pending }}
                </p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                PROGRES HARI INI
            </p>

            @if(!$isReady)
                <div class="mt-3.5 h-2 w-full rounded-full bg-[#222] animate-pulse"></div>
            @else
                <div class="mt-3 flex items-center gap-3 transition-all duration-300">
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-[#1b1b1b]">
                        <div
                            class="h-full rounded-full bg-blue-600 transition-all duration-500"
                            style="width: {{ round(($todayDone / $todayTotal) * 100) }}%">
                        </div>
                    </div>

                    <span class="text-xs font-medium text-[#fafafa]">
                        {{ $todayDone }} / {{ $todayTotal }}
                    </span>
                </div>
            @endif
        </article>

    </section>
    <section class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                TOTAL LAMARAN (SUCCESS)
            </p>

            @if(!$isReady)
                <div class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <p class="mt-3 text-3xl font-semibold text-emerald-400 transition-all duration-300">
                    {{ $successCount }}
                </p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                SUDAH PERNAH DILAMAR (APPLIED)
            </p>

            @if(!$isReady)
                <div class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <p class="mt-3 text-3xl font-semibold text-violet-400 transition-all duration-300">
                    {{ $appliedCount }}
                </p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                TINGKAT KEBERHASILAN
            </p>

            @if(!$isReady)
                <div class="mt-3.5 h-8 w-16 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <p class="mt-3 text-3xl font-semibold text-[#fafafa] transition-all duration-300">
                    {{ $successRate }}%
                </p>
            @endif
        </article>

    </section>

</div>
