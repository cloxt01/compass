<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

new class extends Component
{
    public $isReady = false;
    public $config = [];

    public string $activeProvider = 'jobstreet';
    public array $profileProvider = [];

    public $hasJobstreet = false;
    public $hasGlints = false;

    // Keyword input (per provider) untuk Livewire
    public array $keywordInput = [
        'jobstreet' => '',
        'glints' => '',
    ];

    // Location data (di-manage via Alpine, tapi disimpan di Livewire)
    // Tidak perlu property untuk search, karena Alpine yang handle

    public function mount($accounts = [], $adapters = [])
    {
        $this->hasJobstreet = isset($accounts['jobstreet']) && isset($adapters['jobstreet']);
        $this->hasGlints = isset($accounts['glints']) && isset($adapters['glints']);

        // Load profile dengan cache
        if ($this->hasJobstreet || $this->hasGlints) {
            $cacheKey = 'profile_' . Auth::id();
            $this->profileProvider = Cache::remember($cacheKey, 600, function () use ($adapters) {
                $profiles = [];
                foreach ($adapters as $provider => $adapter) {
                    $profiles[$provider] = $adapter->loadProfile();
                }
                return $profiles;
            });
        }

        $providers = collect([
            'jobstreet' => $this->hasJobstreet,
            'glints' => $this->hasGlints,
        ])->filter()->keys();

        $this->activeProvider = $providers->first() ?? 'jobstreet';
    }

    public function init()
    {
        $this->config = $this->configuration();
        $this->isReady = true;
    }

    // ========== KONFIGURASI ==========

    protected function defaultConfiguration(): array
    {
        return [
            'jobstreet' => [
                'enabled' => false,
                'keyword' => [],
                'batch' => 1,
                'resume' => '',
                'role' => '',
                'location' => '',
            ],
            'glints' => [
                'enabled' => false,
                'keyword' => [],
                'batch' => 1,
                'location_ids' => [],
                'location_names' => [],
            ],
        ];
    }

    protected function configuration(): array
    {
        $user = Auth::user();
        $saved = $user->fresh()->apply_configuration ?? [];

        return array_replace_recursive(
            $this->defaultConfiguration(),
            $saved
        );
    }

    protected function saveConfiguration(array $changes): array
    {
        $current = $this->configuration();
        $merged = array_replace_recursive($current, $changes);
        $listKeys = [
            'jobstreet' => ['keyword','batch', 'location'],
            'glints' => ['keyword', 'batch', 'location_ids', 'location_names'],
        ];

        foreach ($listKeys as $provider => $keys) {
            foreach ($keys as $key) {
                if (isset($changes[$provider][$key]) && is_array($changes[$provider][$key])) {
                    $merged[$provider][$key] = array_values($changes[$provider][$key]);
                }
            }
        }

        Auth::user()->update([
            'apply_configuration' => $merged,
        ]);

        return $merged;
    }

    // ========== SAVE ==========

    public function save()
    {
        $this->validate([
            'config.jobstreet.batch' => 'nullable|integer|min:1|max:25',
            'config.glints.batch'    => 'nullable|integer|min:1|max:25',
            'config.jobstreet.resume' => 'nullable|string',
            'config.jobstreet.role'   => 'nullable|string',
            'config.jobstreet.location' => 'nullable|string',
        ]);

        $this->config = $this->saveConfiguration($this->config);

        $this->dispatch('config-saved');
        $this->dispatch('queue-status-updated', processing: 0);
    }

    // ========== KEYWORD MANAGEMENT ==========

// ========== KEYWORD MANAGEMENT ==========

    public function addKeyword(string $provider): void
    {
        $keyword = trim($this->keywordInput[$provider] ?? '');
        $this->keywordInput[$provider] = ''; // reset selalu

        if ($keyword === '') {
            return;
        }

        if (!isset($this->config[$provider]['keyword'])) {
            $this->config[$provider]['keyword'] = [];
        }

        if (count($this->config[$provider]['keyword']) >= 5) {
            $this->addError("keyword.$provider", "Maksimal 5 kata kunci saja.");
            return;
        }

        if (!in_array($keyword, $this->config[$provider]['keyword'], true)) {
            $this->config[$provider]['keyword'][] = $keyword;

            // TIMPA KE DATABASE LANGSUNG
            $this->config = $this->saveConfiguration($this->config);

            $this->resetErrorBag("keyword.$provider");
        }
    }

    public function removeKeyword(string $provider, int $index): void
    {
        if (isset($this->config[$provider]['keyword'][$index])) {
            unset($this->config[$provider]['keyword'][$index]);

            // Reset susunan indeks array (0, 1, 2...)
            $this->config[$provider]['keyword'] = array_values($this->config[$provider]['keyword']);

            // TIMPA KE DATABASE LANGSUNG (Menggantikan data lama di DB dengan array yang baru)
            $this->config = $this->saveConfiguration($this->config);

            // Bersihkan string input untuk memaksa Livewire me-render ulang DOM secara bersih
            $this->keywordInput[$provider] = '';

            $this->resetErrorBag("keyword.$provider");
        }
    }

    // ========== LOCATION (dipanggil dari Alpine) ==========

    public function addLocation($locationId, $locationName)
    {
        if (in_array($locationId, $this->config['glints']['location_ids'] ?? [])) {
            return;
        }

        $this->config['glints']['location_ids'][] = $locationId;
        $this->config['glints']['location_names'][] = $locationName;
    }

    public function removeLocation($index)
    {
        unset($this->config['glints']['location_ids'][$index]);
        unset($this->config['glints']['location_names'][$index]);

        $this->config['glints']['location_ids'] = array_values($this->config['glints']['location_ids']);
        $this->config['glints']['location_names'] = array_values($this->config['glints']['location_names']);
    }

    // ========== HELPER DROPDOWN ==========

    public function getResumesProperty()
    {
        return $this->profileProvider['jobstreet']['resumes'] ?? [];
    }

    public function getRolesProperty()
    {
        return $this->profileProvider['jobstreet']['roles'] ?? [];
    }
};
?>

<div class="saas-card p-6 xl:col-span-2 h-full flex flex-col" wire:init="init">
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


                <div class="mt-6 space-y-6">
            @if($activeProvider === 'jobstreet' && $hasJobstreet)

                {{-- KEYWORD — FULL WIDTH, 5 SLOT TETAP TERLIHAT --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs uppercase tracking-wide text-zinc-500">Kata Kunci</label>
                        <span class="text-xs text-zinc-500">{{ count($config['jobstreet']['keyword'] ?? []) }}/5</span>
                    </div>
                    <div class="flex gap-2">
                        <input
                            wire:model="keywordInput.jobstreet"
                            wire:keydown.enter.prevent="addKeyword('jobstreet')"
                            placeholder="Tambah keyword..."
                            class="saas-input h-11 flex-1 rounded-xl px-4 text-sm text-[#fafafa]"
                            @if(count($config['jobstreet']['keyword'] ?? []) >= 5) disabled placeholder="Slot penuh" @endif
                        />
                        <button
                            type="button"
                            wire:click="addKeyword('jobstreet')"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-xl text-white hover:bg-blue-500 disabled:opacity-50"
                            @if(count($config['jobstreet']['keyword'] ?? []) >= 5) disabled @endif
                        >+</button>
                    </div>
                    @error('keyword.jobstreet') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5">
                        @for($i = 0; $i < 5; $i++)
                            @if(isset($config['jobstreet']['keyword'][$i]))
                                <span wire:key="kw-jobstreet-{{ $i }}"
                                      class="inline-flex items-center justify-between gap-1.5 rounded-lg border border-[#262626] bg-[#1b1b1b] px-3 py-1.5 text-xs text-[#d4d4d8]">
                            <span class="truncate">{{ $config['jobstreet']['keyword'][$i] }}</span>
                            <button type="button" wire:click="removeKeyword('jobstreet', {{ $i }})" class="shrink-0 text-zinc-500 hover:text-red-400 transition text-sm">×</button>
                        </span>
                            @else
                                <span class="inline-flex items-center justify-center rounded-lg border border-dashed border-[#262626] px-3 py-1.5 text-xs text-zinc-600">
                            Kosong
                        </span>
                            @endif
                        @endfor
                    </div>
                </div>

                {{-- BATCH · RESUME · ROLE · LOCATION — SATU BARIS --}}
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">Batch</label>
                        <input type="number" wire:model="config.jobstreet.batch" min="1" max="25"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa]" />
                    </div>
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">Resume</label>
                        <select wire:model="config.jobstreet.resume" class="saas-input h-11 w-full rounded-xl bg-[#0a0a0a] px-4 text-sm text-[#fafafa]">
                            <option value="">Pilih Resume</option>
                            @foreach($this->resumes as $resume)
                                <option value="{{ $resume['id'] }}">{{ $resume['fileMetadata']['name'] ?? 'Resume' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">Role</label>
                        <select wire:model="config.jobstreet.role" class="saas-input h-11 w-full rounded-xl bg-[#0a0a0a] px-4 text-sm text-[#fafafa]">
                            <option value="">Pilih Role</option>
                            @foreach($this->roles as $role)
                                <option value="{{ $role['id'] }}">{{ $role['title']['text'] ?? 'Role' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">Location</label>
                        <input wire:model="config.jobstreet.location" class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa]" placeholder="Jakarta, Indonesia" />
                    </div>
                </div>

            @elseif($activeProvider === 'glints' && $hasGlints)

                {{-- KEYWORD — FULL WIDTH, 5 SLOT TETAP TERLIHAT --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs uppercase tracking-wide text-zinc-500">Kata Kunci</label>
                        <span class="text-xs text-zinc-500">{{ count($config['glints']['keyword'] ?? []) }}/5</span>
                    </div>
                    <div class="flex gap-2">
                        <input
                            wire:model="keywordInput.glints"
                            wire:keydown.enter.prevent="addKeyword('glints')"
                            placeholder="Tambah keyword..."
                            class="saas-input h-11 flex-1 rounded-xl px-4 text-sm text-[#fafafa]"
                            @if(count($config['glints']['keyword'] ?? []) >= 5) disabled placeholder="Slot penuh" @endif
                        />
                        <button
                            type="button"
                            wire:click="addKeyword('glints')"
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-xl text-white hover:bg-blue-500 disabled:opacity-50"
                            @if(count($config['glints']['keyword'] ?? []) >= 5) disabled @endif
                        >+</button>
                    </div>
                    @error('keyword.glints') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror

                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5">
                        @for($i = 0; $i < 5; $i++)
                            @if(isset($config['glints']['keyword'][$i]))
                                <span wire:key="kw-glints-{{ $i }}"
                                      class="inline-flex items-center justify-between gap-1.5 rounded-lg border border-[#262626] bg-[#1b1b1b] px-3 py-1.5 text-xs text-[#d4d4d8]">
                            <span class="truncate">{{ $config['glints']['keyword'][$i] }}</span>
                            <button type="button" wire:click="removeKeyword('glints', {{ $i }})" class="shrink-0 text-zinc-500 hover:text-red-400 transition text-sm">×</button>
                        </span>
                            @else
                                <span class="inline-flex items-center justify-center rounded-lg border border-dashed border-[#262626] px-3 py-1.5 text-xs text-zinc-600">
                            Kosong
                        </span>
                            @endif
                        @endfor
                    </div>
                </div>

                {{-- BATCH · LOKASI — SATU BARIS --}}
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">Batch</label>
                        <input type="number" wire:model="config.glints.batch" min="1" max="25"
                               class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa]" />
                    </div>
                    <div class="sm:col-span-1 lg:col-span-3" x-data="glintsSearchLocation()">
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">Lokasi</label>
                        <div class="relative w-full">
                            <input
                                type="text"
                                x-model="query"
                                x-on:input.debounce.300ms="search()"
                                placeholder="Cari lokasi (min 2 karakter)..."
                                class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa]"
                            />
                            <div x-show="searching" x-cloak class="absolute right-4 top-3.5 text-xs text-zinc-500 animate-pulse">
                                Mencari...
                            </div>
                            <div
                                x-show="results.length > 0"
                                x-cloak
                                class="absolute left-0 right-0 z-50 mt-1 max-h-60 w-full min-w-full overflow-y-auto rounded-xl border border-[#262626] bg-[#0a0a0a] py-1 shadow-2xl custom-scroll"
                            >
                                <template x-for="loc in results" :key="loc.id">
                                    <button
                                        type="button"
                                        @click="selectLocation(loc.id, loc.formattedName)"
                                        class="w-full px-4 py-2.5 text-left text-sm text-[#d4d4d8] hover:bg-[#111] hover:text-white border-b border-zinc-900/50 last:border-0 transition-colors"
                                        x-text="loc.formattedName"
                                    ></button>
                                </template>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach($config['glints']['location_names'] ?? [] as $index => $name)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#1b1b1b] border border-[#262626] pl-3 pr-2 py-1 text-xs text-[#d4d4d8]">
                            {{ $name }}
                            <button type="button" wire:click="removeLocation({{ $index }})" class="text-zinc-500 hover:text-red-400 transition text-sm">×</button>
                        </span>
                            @endforeach
                        </div>
                    </div>
                </div>

            @endif
        </div>
                {{-- Keterangan Field --}}
                <div class="mt-6 rounded-xl border border-[#262626] bg-[#0a0a0a] p-5">
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                        Keterangan
                    </p>
                    <dl class="mt-3 space-y-2 text-xs text-[#a1a1aa] sm:text-sm">
                        <div class="flex gap-2">
                            <dt class="shrink-0 font-medium text-zinc-400">Kata Kunci</dt>
                            <dd>— pekerjaan yang ingin Anda cari.</dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="shrink-0 font-medium text-zinc-400">Resume</dt>
                            <dd>— resume/CV yang akan dikirim.</dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="shrink-0 font-medium text-zinc-400">Role</dt>
                            <dd>— role yang akan ditampilkan ke recruiter.</dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="shrink-0 font-medium text-zinc-400">Batch</dt>
                            <dd>— jumlah lowongan yang dicari dalam 1 ronde.</dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="shrink-0 font-medium text-zinc-400">Lokasi</dt>
                            <dd>— lokasi pekerjaan yang ingin dipilih.</dd>
                        </div>
                    </dl>
                </div>
                {{-- Catatan Penggunaan --}}
                <div class="mt-6 rounded-xl border border-[#262626] bg-[#0a0a0a] p-5">
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-500">
                        Catatan
                    </p>
                    <ul class="mt-3 space-y-2 text-xs text-[#a1a1aa] sm:text-sm">
                        <li class="flex gap-2">
                            <span class="text-zinc-600">1.</span>
                            <span>Jika tingkat keberhasilan terlalu rendah, coba untuk mengubah keyword.</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-zinc-600">2.</span>
                            <span>Pastikan memilih keyword yang relevan.</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-zinc-600">3.</span>
                            <span class="text-amber-400/90">Peringatan: semakin tinggi batch, semakin tinggi pula risiko banned.</span>
                        </li>
                    </ul>

                    <p class="mt-4 border-t border-[#262626] pt-3 text-[11px] leading-relaxed text-zinc-500 sm:text-xs">
                        Fitur ini tidak menjamin Anda mendapatkan pekerjaan, namun memperbesar peluang untuk dilirik recruiter. Dengan menggunakan fitur ini, Anda menyetujui dan menerima segala risiko yang mungkin timbul (termasuk risiko pembatasan akun oleh provider).
                    </p>
                </div>
        </div>
    @endif
</div>
