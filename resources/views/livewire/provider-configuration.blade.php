<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $isReady = false;
    public $config = [];

    public string $activeProvider = 'jobstreet';
    public array $profileProvider = []; // berisi data dari adapter

    public $hasJobstreet = false;
    public $hasGlints = false;

    public function mount($accounts = [], $adapters = [])
    {
        $this->hasJobstreet = isset($accounts['jobstreet']) && isset($adapters['jobstreet']);
        $this->hasGlints = isset($accounts['glints']) && isset($adapters['glints']);

        // Load profile dari adapter yang tersedia
        if ($this->hasJobstreet || $this->hasGlints) {
            foreach ($adapters as $provider => $adapter) {
                $this->profileProvider[$provider] = $adapter->loadProfile();
            }
        }

        // Tentukan active provider
        $providers = collect([
            'jobstreet' => $this->hasJobstreet,
            'glints' => $this->hasGlints,
        ])->filter()->keys();

        $this->activeProvider = $providers->first() ?? 'jobstreet';
    }

    public function init()
    {
        $this->config = $this->configuration();
        $this->hydrateArrayFields();
        $this->isReady = true;
    }

    // ========== KONFIGURASI (tanpa trait) ==========

    protected function defaultConfiguration(): array
    {
        return [
            'jobstreet' => [
                'enabled' => false,
                'keyword' => [],
                'batch' => 1,
                'resume' => '',      // akan diisi ID resume
                'role' => '',        // akan diisi ID role
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

        Auth::user()->update([
            'apply_configuration' => $merged,
        ]);

        return $merged;
    }

    // ========== UTILITY ==========

    protected function providers(): array
    {
        return ['jobstreet', 'glints'];
    }

    protected function hydrateArrayText(string $provider, string $field): void
    {
        $this->config[$provider]["{$field}_text"] = implode(
            ', ',
            $this->config[$provider][$field] ?? []
        );
    }

    protected function parseArrayText(string $text): array
    {
        return collect(preg_split('/[,;\n]+/', $text))
            ->map('trim')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    protected function hydrateArrayFields(): void
    {
        foreach ($this->providers() as $provider) {
            $this->hydrateArrayText($provider, 'keyword');
            if ($provider === 'glints') {
                $this->hydrateArrayText($provider, 'location_ids');
            }
        }
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

        $changes = $this->config;

        // Proses field array (keyword, location_ids)
        foreach ([
                     'jobstreet' => ['keyword'],
                     'glints' => ['keyword', 'location_ids'],
                 ] as $provider => $fields) {
            foreach ($fields as $field) {
                $value = $this->parseArrayText(
                    $this->config[$provider]["{$field}_text"] ?? ''
                );

                if ($field === 'location_ids') {
                    $value = array_map('intval', $value);
                }

                $changes[$provider][$field] = $value;
            }
        }

        // Resume dan role sudah dalam bentuk ID, tidak perlu diubah

        $this->config = $this->saveConfiguration($changes);
        $this->hydrateArrayFields();

        $this->dispatch('config-saved');
        $this->dispatch('queue-status-updated', processing: 0);
    }

    // ========== HELPER UNTUK DROPDOWN ==========

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

                    <div class="space-y-5">
                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                                Kata Kunci
                            </label>
                            <input
                                wire:model.live="config.jobstreet.keyword_text"
                                placeholder="Backend Developer, Laravel, PHP"
                                class="saas-input h-11 w-full rounded-xl px-4"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                                Batch
                            </label>
                            <input
                                type="number"
                                wire:model.live="config.jobstreet.batch"
                                min="1"
                                max="25"
                                class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa]"
                            />
                        </div>

                        {{-- DROPDOWN RESUME --}}
                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                                Resume
                            </label>
                            <select
                                wire:model.live="config.jobstreet.resume"
                                class="saas-input h-11 w-full rounded-xl bg-[#0a0a0a] px-4 text-sm text-[#fafafa]"
                            >
                                <option value="">Pilih Resume</option>
                                @foreach($this->resumes as $resume)
                                    <option value="{{ $resume['id'] }}">
                                        {{ $resume['fileMetadata']['name'] ?? 'Resume' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-5">
                        {{-- DROPDOWN ROLE --}}
                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                                Role
                            </label>
                            <select
                                wire:model.live="config.jobstreet.role"
                                class="saas-input h-11 w-full rounded-xl bg-[#0a0a0a] px-4 text-sm text-[#fafafa]"
                            >
                                <option value="">Pilih Role</option>
                                @foreach($this->roles as $role)
                                    <option value="{{ $role['id'] }}">
                                        {{ $role['title']['text'] ?? 'Role' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                                Location
                            </label>
                            <input
                                wire:model.live="config.jobstreet.location"
                                class="saas-input h-11 w-full rounded-xl px-4"
                            />
                        </div>
                    </div>

                </div>

            @elseif($activeProvider === 'glints' && $hasGlints)
                <div class="grid gap-8 lg:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                            Kata Kunci
                        </label>
                        <input
                            wire:model.live="config.glints.keyword_text"
                            placeholder="Frontend, UI/UX, React"
                            class="saas-input h-11 w-full rounded-xl px-4"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                            Batch
                        </label>
                        <input
                            type="number"
                            wire:model.live="config.glints.batch"
                            min="1"
                            max="25"
                            class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa]"
                        />
                    </div>

                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-wide text-zinc-500">
                            Lokasi (ID)
                        </label>
                        <input
                            wire:model.live="config.glints.location_ids_text"
                            placeholder="1,2,3"
                            class="saas-input h-11 w-full rounded-xl px-4"
                        />
                    </div>

                </div>
            @endif
        </div>
    @endif
</div>
