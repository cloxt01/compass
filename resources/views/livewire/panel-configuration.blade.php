<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\HasConfiguration;

new class extends Component
{
    use HasConfiguration;

    public $isReady = false;
    public $isPaused = false;

    public $jobstreetEnabled = false;
    public $glintsEnabled = false;

    public $hasJobstreet = false;
    public $hasGlints = false;

    // Limit masing-masing provider
    public $jobstreetLimit = false;
    public $glintsLimit = false;

    public function mount($accounts = [], $adapters = [])
    {
        $this->hasJobstreet =
            isset($accounts['jobstreet']) &&
            isset($adapters['jobstreet']);

        $this->hasGlints =
            isset($accounts['glints']) &&
            isset($adapters['glints']);

        /*
         * Adapter hanya digunakan di mount()
         * untuk mengambil status limit.
         *
         * Jangan simpan $adapters sebagai public property
         * karena berisi object dan tidak didukung Livewire.
         */
        $this->jobstreetLimit =
            isset($adapters['jobstreet']) &&
            $adapters['jobstreet']->isLimit();

        $this->glintsLimit =
            isset($adapters['glints']) &&
            $adapters['glints']->isLimit();
    }

    public function init()
    {
        $user = Auth::user();

        $this->isPaused = $user->automation_paused ?? false;

        $config = $this->configuration();

        $this->jobstreetEnabled =
            $config['jobstreet']['enabled'] ?? false;

        $this->glintsEnabled =
            $config['glints']['enabled'] ?? false;

        $this->isReady = true;
    }

    public function toggleProvider($provider, $enabled)
    {
        /*
         * Validasi provider terlebih dahulu.
         */
        if (!in_array($provider, ['jobstreet', 'glints'], true)) {
            return;
        }

        /*
         * Provider yang sudah mencapai limit
         * tidak boleh diaktifkan.
         */
        if ($provider === 'jobstreet' && $this->jobstreetLimit) {
            return;
        }

        if ($provider === 'glints' && $this->glintsLimit) {
            return;
        }

        $enabled = filter_var(
            $enabled,
            FILTER_VALIDATE_BOOLEAN
        );

        $this->saveConfiguration([
            $provider => [
                'enabled' => $enabled,
            ]
        ]);

        $this->{$provider . 'Enabled'} = $enabled;

        $this->dispatch('config-saved');

        $this->dispatch(
            'queue-status-updated',
            processing: 0
        );
    }

    public function stop()
    {
        Auth::user()->update([
            'automation_paused' => true,
        ]);

        $this->isPaused = true;

        $this->dispatch(
            'queue-status-updated',
            processing: 0
        );
    }

    public function resume()
    {
        Auth::user()->update([
            'automation_paused' => false,
        ]);

        $this->isPaused = false;
    }
};
?>

{{-- resources/views/livewire/panel-configuration.blade.php --}}

<div
    class="saas-card p-4 sm:p-6 xl:col-span-2"
    wire:init="init"
>

    {{-- =========================================================
         GLOBAL SUBSCRIPTION LIMIT
         Hanya warning.
         PANEL TETAP DITAMPILKAN.
    ========================================================== --}}
    @if(auth()->user()->getLastActiveSubscription()->isLimit())

        <div class="mb-5 rounded-xl border border-amber-500/20 bg-amber-500/10 p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h3 class="text-sm font-semibold text-amber-300">
                        Batas penggunaan tercapai
                    </h3>

                    <p class="mt-1 text-sm text-amber-200/80">
                        Batas penggunaan akun Anda untuk hari ini telah tercapai.
                        Silakan coba kembali besok.
                    </p>
                </div>

                <span class="inline-flex w-fit shrink-0 items-center rounded-md border border-amber-500/20 bg-amber-500/10 px-3 py-1.5 text-xs font-medium text-amber-400">
                    Coba lagi besok
                </span>

            </div>
        </div>

    @endif


    {{-- =========================================================
         SKELETON
    ========================================================== --}}
    @if(!$isReady)

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

            <div class="space-y-2">
                <div class="h-5 w-16 animate-pulse rounded-lg bg-[#222]"></div>

                <div class="h-3 w-48 animate-pulse rounded-lg bg-[#1c1c1e]"></div>
            </div>

            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto">

                <div class="h-9 w-full animate-pulse rounded-md bg-[#222] sm:w-20"></div>

                <div class="h-9 w-full animate-pulse rounded-md bg-[#222] sm:w-16"></div>

            </div>

        </div>


        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">

            @foreach(range(1, 3) as $i)

                <div class="space-y-2">

                    <div class="h-3 w-20 animate-pulse rounded-lg bg-[#1c1c1e]"></div>

                    <div class="h-11 w-full animate-pulse rounded-xl bg-[#222]"></div>

                </div>

            @endforeach

        </div>


        <div class="mt-5 flex flex-wrap gap-4 border-t border-[#262626] pt-4">

            <div class="h-5 w-24 animate-pulse rounded-lg bg-[#1c1c1e]"></div>

            <div class="h-5 w-16 animate-pulse rounded-lg bg-[#1c1c1e]"></div>

        </div>


    @else


        {{-- =====================================================
             PANEL HEADER
        ====================================================== --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

            <div>

                <h2 class="text-base font-semibold tracking-tight text-[#fafafa] sm:text-lg">
                    Panel
                </h2>

                <p class="mt-1 text-xs text-[#a1a1aa] sm:text-sm">
                    Kontrol otomatisasi lamaran
                </p>

            </div>


            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:flex-wrap">

                @if($isPaused)

                    <button
                        wire:click="resume"
                        wire:loading.attr="disabled"
                        wire:target="resume"
                        class="h-9 w-full cursor-pointer rounded-md bg-green-700 px-5 text-sm font-semibold text-white transition hover:bg-green-800 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                    >
                        Resume
                    </button>

                @else

                    <button
                        wire:click="stop"
                        wire:loading.attr="disabled"
                        wire:target="stop"
                        class="h-9 w-full cursor-pointer rounded-md bg-red-700 px-5 text-sm font-semibold text-white transition hover:bg-red-800 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                    >
                        Stop
                    </button>

                @endif

            </div>

        </div>


        {{-- =====================================================
             PROVIDER + AUTO ANSWER
        ====================================================== --}}
        <div class="mt-5 grid grid-cols-1 items-stretch gap-5 border-t border-[#262626] pt-5 lg:grid-cols-3">


            {{-- =================================================
                 PROVIDER
            ================================================== --}}
            <div class="flex flex-col">

                <p class="mb-3 text-xs font-medium uppercase tracking-[0.14em] text-[#71717a]">
                    Provider
                </p>


                <div class="flex min-h-[170px] flex-1 flex-col rounded-xl border border-[#262626] bg-[#0a0a0a] p-3 sm:p-4">

                    <div class="flex-1 space-y-3">


                        {{-- =====================================
                             JOBSTREET
                        ====================================== --}}
                        @if($jobstreetLimit)

                            <div class="flex items-center justify-between gap-3 rounded-lg border border-[#262626] bg-[#111111] px-3 py-3 opacity-50">

                                <div>

                                    <p class="text-sm font-medium text-[#fafafa]">
                                        JobStreet
                                    </p>

                                    <p class="mt-0.5 text-xs text-[#71717a]">
                                        Gunakan akun JobStreet
                                    </p>

                                </div>


                                <span class="inline-flex shrink-0 items-center rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-xs font-medium text-amber-400">
                                    Limit tercapai
                                </span>

                            </div>

                        @else

                            <label
                                class="flex items-center justify-between gap-3 rounded-lg border border-[#262626] bg-[#111111] px-3 py-3 transition hover:border-[#3a3a3a] {{ !$hasJobstreet ? 'cursor-not-allowed opacity-40' : 'cursor-pointer' }}"
                            >

                                <div>

                                    <p class="text-sm font-medium text-[#fafafa]">
                                        JobStreet
                                    </p>

                                    <p class="mt-0.5 text-xs text-[#71717a]">
                                        Gunakan akun JobStreet
                                    </p>

                                </div>


                                <label class="relative inline-flex items-center">

                                    <input
                                        type="checkbox"
                                        @checked($jobstreetEnabled)
                                        {{ !$hasJobstreet ? 'disabled' : '' }}
                                        wire:change="toggleProvider('jobstreet', $event.target.checked)"
                                        class="peer sr-only"
                                    >

                                    <div class="h-6 w-11 cursor-pointer rounded-full bg-[#1f1f1f] transition peer-checked:bg-blue-600 peer-disabled:bg-[#161616]"></div>

                                    <div class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-all peer-checked:translate-x-5"></div>

                                </label>

                            </label>

                        @endif


                        {{-- =====================================
                             GLINTS
                        ====================================== --}}
                        @if($glintsLimit)

                            <div class="flex items-center justify-between gap-3 rounded-lg border border-[#262626] bg-[#111111] px-3 py-3 opacity-50">

                                <div>

                                    <p class="text-sm font-medium text-[#fafafa]">
                                        Glints
                                    </p>

                                    <p class="mt-0.5 text-xs text-[#71717a]">
                                        Gunakan akun Glints
                                    </p>

                                </div>


                                <span class="inline-flex shrink-0 items-center rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-xs font-medium text-amber-400">
                                    Limit tercapai
                                </span>

                            </div>

                        @else

                            <label
                                class="flex items-center justify-between gap-3 rounded-lg border border-[#262626] bg-[#111111] px-3 py-3 transition hover:border-[#3a3a3a] {{ !$hasGlints ? 'cursor-not-allowed opacity-40' : 'cursor-pointer' }}"
                            >

                                <div>

                                    <p class="text-sm font-medium text-[#fafafa]">
                                        Glints
                                    </p>

                                    <p class="mt-0.5 text-xs text-[#71717a]">
                                        Gunakan akun Glints
                                    </p>

                                </div>


                                <label class="relative inline-flex items-center">

                                    <input
                                        type="checkbox"
                                        @checked($glintsEnabled)
                                        {{ !$hasGlints ? 'disabled' : '' }}
                                        wire:change="toggleProvider('glints', $event.target.checked)"
                                        class="peer sr-only"
                                    >

                                    <div class="h-6 w-11 cursor-pointer rounded-full bg-[#1f1f1f] transition peer-checked:bg-blue-600 peer-disabled:bg-[#161616]"></div>

                                    <div class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white transition-all peer-checked:translate-x-5"></div>

                                </label>

                            </label>

                        @endif

                    </div>


                    {{-- AUTOMATION PAUSED --}}
                    @if($isPaused)

                        <span class="mt-4 inline-flex w-fit rounded-md border border-amber-500/20 bg-amber-500/10 px-2 py-1 text-xs font-medium text-amber-400">
                            Automation Paused
                        </span>

                    @endif

                </div>

            </div>


            {{-- =================================================
                 AUTO ANSWER
            ================================================== --}}
            <div class="flex flex-col lg:col-span-2">

                <p class="mb-3 text-xs font-medium uppercase tracking-[0.14em] text-[#71717a]">
                    Auto Answer
                </p>


                <div class="flex min-h-[170px] flex-1 flex-col justify-center gap-5 rounded-xl border border-[#262626] bg-[#0a0a0a] p-4 opacity-55 sm:flex-row sm:items-center sm:justify-between sm:px-5">

                    <div>

                        <p class="text-sm font-medium text-[#fafafa]">
                            AI Powered Auto Answer
                        </p>

                        <p class="mt-1 max-w-full text-xs leading-5 text-[#71717a] sm:max-w-md">
                            Menjawab screening question secara otomatis menggunakan AI.
                        </p>

                    </div>


                    <div class="flex w-full items-center justify-between gap-3 sm:w-auto sm:justify-start">

                        <label class="relative inline-flex cursor-not-allowed items-center">

                            <input
                                type="checkbox"
                                disabled
                                class="peer sr-only"
                            >

                            <div class="h-6 w-11 rounded-full border border-[#333] bg-[#1e1e1e]"></div>

                            <div class="absolute left-[4px] top-[4px] h-4 w-4 rounded-full bg-[#666]"></div>

                        </label>


                        <span class="inline-flex items-center rounded-full border border-[#333] bg-[#161616] px-3 py-1 text-xs font-medium text-[#9ca3af]">
                            Belum tersedia
                        </span>

                    </div>

                </div>

            </div>

        </div>

    @endif

</div>