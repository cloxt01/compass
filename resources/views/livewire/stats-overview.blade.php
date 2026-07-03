<?php

use Livewire\Component;
use App\Models\Application;
use App\Models\ApplyQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $isReady = false;
    public $todayTotal = 200;

    public function init()
    {
        $this->isReady = true;
    }

    public function with(): array
    {
        $userId = Auth::id();

        if (!$this->isReady) {
            return [
                'pending'       => 0,
                'processing'    => 0,
                'successCount'  => 0,
                'appliedCount'  => 0,
                'successRate'   => 0,
                'todayDone'     => 0,
            ];
        }

        $queueStats = DB::table('apply_queue')
            ->join('jobs', 'apply_queue.job_id', '=', 'jobs.id')
            ->where('apply_queue.user_id', $userId)
            ->selectRaw("
                COUNT(CASE WHEN jobs.reserved_at IS NULL THEN 1 END) as pending,
                COUNT(CASE WHEN jobs.reserved_at IS NOT NULL THEN 1 END) as processing
            ")
            ->first();

        $pending = $queueStats->pending ?? 0;
        $processing = $queueStats->processing ?? 0;

        // 2. Mengambil total akumulasi global data riwayat pelamaran kerja milik user
        $successCount = Application::where('user_id', $userId)->where('status', 'success')->count();
        $appliedCount = Application::where('user_id', $userId)->where('status', 'applied')->count();

        // Total keseluruhan data pelamaran untuk pembagi rumus rate
        $totalApplications = Application::where('user_id', $userId)->count();

        // 3. Kalkulasi metrik analisis analytics
        $successRate = $totalApplications > 0 ? round(($successCount / $totalApplications) * 100) : 0;

        // Menghitung berapa progres antrean hari ini
        $todayDone = min($pending + $processing, $this->todayTotal);

        return [
            'pending'      => $pending,
            'processing'   => $processing,
            'successCount' => $successCount,
            'appliedCount' => $appliedCount,
            'successRate'  => $successRate,
            'todayDone'    => $todayDone,
        ];
    }
};
?>

<div wire:init="init" wire:poll.10s="$refresh">
    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TOTAL PROSES</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-14 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-14 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-[#fafafa]">{{ $processing }}</p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">ANTRIAN</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-14 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-14 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-[#fafafa]">{{ $pending }}</p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">PROGRES HARI INI</p>
            @if(!$isReady)
                <div class="mt-3.5 h-2 w-full rounded-full bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-2 w-full rounded-full bg-[#222] animate-pulse"></div>
                <div wire:loading.remove class="mt-3 flex items-center gap-3">
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-[#1b1b1b]">
                        <div class="h-full rounded-full bg-blue-600 transition-all duration-300"
                             style="width: {{ round(($todayDone / $todayTotal) * 100) }}%"></div>
                    </div>
                    <span class="text-xs font-medium text-[#fafafa]">{{ $todayDone }} / {{ $todayTotal }}</span>
                </div>
            @endif
        </article>
    </section>

    <section class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TOTAL LAMARAN (SUCCESS)</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-emerald-400">{{ $successCount }}</p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">SUDAH PERNAH DILAMAR (APPLIED)</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-violet-400">{{ $appliedCount }}</p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TINGKAT KEBERHASILAN</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-16 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-16 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-[#fafafa]">{{ $successRate }}%</p>
            @endif
        </article>
    </section>
</div>
