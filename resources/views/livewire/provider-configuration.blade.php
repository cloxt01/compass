<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $isReady = false;
    public $activeProvider = 'jobstreet'; // Provider yang sedang diedit form-nya
    public $config = [];

    // Status koneksi (diterima dari mount)
    public $hasJobstreet = false;
    public $hasGlints = false;

    public function mount($accounts = [], $adapters = [])
    {
        $this->hasJobstreet = isset($accounts['jobstreet']) && isset($adapters['jobstreet']);
        $this->hasGlints = isset($accounts['glints']) && isset($adapters['glints']);

        // Default active provider jika salah satu tidak tersedia
        if (!$this->hasJobstreet && $this->hasGlints) $this->activeProvider = 'glints';
    }

    public function init()
    {
        $user = Auth::user();
        $this->config = $user->apply_configuration ?? [
            'jobstreet' => ['auto_answer' => false, 'resume' => '', 'role' => '', 'location' => ''],
            'glints'    => ['location_ids' => [], 'location_names' => []]
        ];
        $this->isReady = true;
    }

    public function save()
    {
        Auth::user()->update(['apply_configuration' => $this->config]);
        $this->dispatch('config-saved');
    }
};
?>

<div class="saas-card p-6 xl:col-span-2" wire:init="init">
    @if(!$isReady)
        <div class="h-48 w-full animate-pulse rounded-xl bg-[#111]"></div>
    @else
        {{-- HEADER & DROPDOWN --}}
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#262626] pb-5">
            <div>
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Konfigurasi Provider</h2>
                <p class="mt-1 text-sm text-[#a1a1aa]">Pilih provider dan sesuaikan pengaturannya</p>
            </div>

            <div class="flex items-center gap-3">
                <select wire:model.live="activeProvider" class="saas-input h-10 rounded-xl bg-[#0a0a0a] px-3 text-sm text-[#fafafa]">
                    @if($hasJobstreet) <option value="jobstreet">JobStreet</option> @endif
                    @if($hasGlints) <option value="glints">Glints</option> @endif
                </select>

                <button wire:click="save" class="h-10 rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-500">
                    Simpan Perubahan
                </button>
            </div>
        </div>

        {{-- DYNAMIC FORM AREA --}}
        <div class="mt-6">
            @if($activeProvider === 'jobstreet' && $hasJobstreet)
                <div class="grid gap-8 lg:grid-cols-2">

                    {{-- Basic --}}
                    <div class="space-y-5">

                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                                Resume ID
                            </label>

                            <input
                                wire:model.live="config.jobstreet.resume"
                                class="saas-input h-11 w-full rounded-xl px-4"
                            >
                        </div>

                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                                Role ID
                            </label>

                            <input
                                wire:model.live="config.jobstreet.role"
                                class="saas-input h-11 w-full rounded-xl px-4"
                            >
                        </div>

                    </div>

                    {{-- Advanced --}}
                    <div class="space-y-5">

                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                                Location
                            </label>

                            <input
                                wire:model.live="config.jobstreet.location"
                                class="saas-input h-11 w-full rounded-xl px-4"
                            >
                        </div>



                    </div>

                </div>

            @elseif($activeProvider === 'glints' && $hasGlints)
                <div class="grid gap-8 lg:grid-cols-2">

                    <div>

                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                            Cari Lokasi
                        </label>

                        <input
                            class="saas-input h-11 w-full rounded-xl px-4"
                            placeholder="Jakarta, Bandung..."
                        >

                    </div>

                    <div>

                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                            Lokasi Dipilih
                        </label>

                        <div class="min-h-[140px] rounded-xl border border-[#262626] p-3">
                            ...
                        </div>

                    </div>

                </div>
            @endif
        </div>
    @endif
</div>
