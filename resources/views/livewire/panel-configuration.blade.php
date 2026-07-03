<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $isReady = false;

    public $keyword = '';
    public $batch = 1;
    public $providers = [];
    public $isPaused = false;
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
        $config = $user->apply_configuration ?? [];

        $this->keyword = $config['keyword'] ?? '';
        $this->batch = $config['batch'] ?? 1;
        $this->providers = $config['providers'] ?? [];
        $this->isPaused = $user->automation_paused ?? false;

        $this->isReady = true;
    }

    public function save()
    {
        $this->validate([
            'keyword'   => 'required|string|max:255',
            'batch'     => 'required|integer|min:1|max:20',
            'providers' => 'array',
        ]);

        Auth::user()->update([
            'apply_configuration' => [
                'keyword'   => $this->keyword,
                'batch'     => $this->batch,
                'providers' => $this->providers,
            ],
        ]);

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

<div class="saas-card p-6 xl:col-span-2" wire:init="init">
    @if(!$isReady)
        {{-- SKELETON SAAT FIRST LOAD --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-2">
                <div class="h-5 w-16 rounded-lg bg-[#222] animate-pulse"></div>
                <div class="h-3 w-48 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
            </div>
            <div class="flex gap-2">
                <div class="h-8 w-20 rounded-md bg-[#222] animate-pulse"></div>
                <div class="h-8 w-16 rounded-md bg-[#222] animate-pulse"></div>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-3">
            @foreach(range(1, 3) as $i)
                <div class="space-y-2">
                    <div class="h-3 w-20 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
                    <div class="h-11 w-full rounded-xl bg-[#222] animate-pulse"></div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 border-t border-[#262626] pt-4 flex gap-6">
            <div class="h-5 w-24 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
            <div class="h-5 w-16 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
        </div>
    @else
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Panel</h2>
                <p class="mt-1 text-sm text-[#a1a1aa]">Atur konfigurasi lamaran anda</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="h-8 cursor-pointer rounded-md bg-white/85 px-5 text-sm font-semibold text-black transition hover:bg-white disabled:cursor-not-allowed disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>

                @if ($isPaused)
                    <button wire:click="resume"
                            wire:loading.attr="disabled"
                            wire:target="resume"
                            class="h-8 cursor-pointer rounded-md bg-white/85 px-5 text-sm font-semibold text-[#222] transition hover:bg-white disabled:cursor-not-allowed disabled:opacity-60">
                        Resume
                    </button>
                @else
                    <button wire:click="stop"
                            wire:loading.attr="disabled"
                            wire:target="stop"
                            class="h-8 cursor-pointer rounded-md bg-white/85 px-5 text-sm font-semibold text-[#222] transition hover:bg-white disabled:cursor-not-allowed disabled:opacity-60">
                        Stop
                    </button>
                @endif
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-3">
            <div>
                <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Kata Kunci</label>
                <input type="text" wire:model="keyword" placeholder="Web Developer"
                       class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" />
                @error('keyword') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Batch</label>
                <input type="number" wire:model="batch" min="1" max="20"
                       class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa]" />
                @error('batch') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Interval</label>
                <input type="number" disabled value="10" class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#888]" />
            </div>
        </div>

        <div class="mt-5 border-t border-[#262626] pt-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-6">
                <label class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-[#fafafa] select-none group">
                    <input type="checkbox" wire:model="providers" value="jobstreet" {{ !$hasJobstreet ? 'disabled' : '' }}
                    class="provider-checkbox h-4 w-4 rounded border-[#262626] bg-[#0a0a0a] text-blue-600 focus:ring-0 focus:ring-offset-0 disabled:cursor-not-allowed disabled:opacity-40 transition">
                    <span class="group-hover:text-[#fafafa] transition-colors {{ !$hasJobstreet ? 'opacity-40' : '' }}">JobStreet</span>
                </label>

                <label class="inline-flex cursor-pointer items-center gap-2.5 text-sm text-[#fafafa] select-none group">
                    <input type="checkbox" wire:model="providers" value="glints" {{ !$hasGlints ? 'disabled' : '' }}
                    class="provider-checkbox h-4 w-4 rounded border-[#262626] bg-[#0a0a0a] text-blue-600 focus:ring-0 focus:ring-offset-0 disabled:cursor-not-allowed disabled:opacity-40 transition">
                    <span class="group-hover:text-[#fafafa] transition-colors {{ !$hasGlints ? 'opacity-40' : '' }}">Glints</span>
                </label>

                @if($isPaused)
                    <span class="rounded-md border border-amber-500/20 bg-amber-500/10 px-2 py-0.5 text-xs font-medium text-amber-400">Paused</span>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-[#a1a1aa]">Auto Answer <i>(AI Powered)</i></span>
                <div class="flex items-center gap-2">
                    <label class="relative inline-flex cursor-not-allowed items-center opacity-40">
                        <input type="checkbox" disabled class="peer sr-only">
                        <div class="w-9 h-5 rounded-full bg-[#1e1e1e] border border-[#333]"></div>
                        <div class="absolute left-[4px] top-[4px] h-3 w-3 rounded-full bg-[#71717a]"></div>
                    </label>
                    <span class="text-xs font-medium text-[#71717a] uppercase tracking-wider">(Belum tersedia)</span>
                </div>
            </div>
        </div>
    @endif
</div>
