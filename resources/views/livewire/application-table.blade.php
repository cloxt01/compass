<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    public $perPage = 15;

    // Properti penanda apakah data pertama kali sudah siap dimuat
    public $isReady = false;

    protected $queryString = [
        'perPage' => ['except' => 15, 'as' => 'per_page']
    ];

    // Memicu trigger setelah komponen selesai di-mount di browser
    public function init()
    {
        $this->isReady = true;
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $user = Auth::user();

        // Ambil data hanya jika state isReady bernilai true (Pemuatan Teks Menyusul)
        $query = Application::where('user_id', $user->id);

        $stats = [
            'total'         => $query->count(),
            'success'       => (clone $query)->where('status', 'success')->count(),
            'applied'       => (clone $query)->where('status', 'applied')->count(),
            'questionnaire' => (clone $query)->where('status', 'questionnaire')->count(),
        ];

        return [
            // Jika belum ready (pemuatan pertama), kita kirim paginator kosong terlebih dahulu
            'applications' => $this->isReady ? $query->latest()->paginate($this->perPage) : collect(),
            'stats'        => $stats,
            'isPaused'     => $user->automation_paused ?? false,
        ];
    }
};
?>

{{-- Memicu fungsi init() secara otomatis sesaat setelah elemen HTML ini masuk ke DOM browser --}}
<div wire:init="init">
    {{-- TOP STAT CARDS (STRUKTUR CARD SELALU ADA) --}}
    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5 mb-6">


        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TOTAL LAMARAN</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-16 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-16 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-[#fafafa]">{{ $stats['total'] }}</p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">SUCCESS</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-emerald-400">{{ $stats['success'] }}</p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">ALREADY APPLIED</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-violet-400">{{ $stats['applied'] }}</p>
            @endif
        </article>

        <article class="saas-card p-5">
            <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">NEED SCREENING</p>
            @if(!$isReady)
                <div class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
            @else
                <div wire:loading.delay class="mt-3.5 h-8 w-12 rounded-lg bg-[#222] animate-pulse"></div>
                <p wire:loading.remove class="mt-3 text-3xl font-semibold text-amber-400">{{ $stats['questionnaire'] }}</p>
            @endif
        </article>
    </section>

    {{-- KONTEN LAYOUT TABEL UTAMA (STRUKTUR TETAP RENDERING SEJAK FIRST LOAD) --}}
    <section class="saas-card overflow-hidden">
        <div class="border-b border-[#262626] px-6 py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between bg-[#111111] gap-3">
            <div>
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Log Pelamaran Terkini</h2>
                <p class="mt-1 text-xs text-[#a1a1aa]">Menampilkan riwayat entri pengiriman data dari sistem bot.</p>
            </div>

            <div class="flex items-center space-x-3 text-xs">
                <div class="flex items-center space-x-2">
                    <span class="text-[#a1a1aa]">Tampilkan:</span>
                    <select wire:model.live="perPage" class="h-8 rounded-xl border border-[#262626] bg-[#0a0a0a] text-[#fafafa] px-2.5 py-0.5 outline-none transition duration-150 hover:border-[#333] focus:border-blue-600 cursor-pointer">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="font-mono text-[#71717a] bg-[#0a0a0a] border border-[#262626] px-3 py-1.5 rounded-xl">
                    @if(!$isReady)
                        Showing ... to ... of ... entries
                    @else
                        Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} entries
                    @endif
                </div>
            </div>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#0a0a0a]">
                <tr class="border-b border-[#262626] text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                    <th class="px-6 py-4 font-medium">Posisi Pekerjaan</th>
                    <th class="px-6 py-4 font-medium">Perusahaan</th>
                    <th class="px-6 py-4 font-medium">Platform</th>
                    <th class="px-6 py-4 font-medium">Status Pengiriman</th>
                    <th class="px-6 py-4 font-medium">Waktu Dilamar</th>
                    <th class="px-6 py-4 font-medium text-center">Aksi</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-[#262626] text-sm text-[#fafafa]">
                @if(!$isReady)
                    {{-- RENDERING BARIS PALSU SAAT FIRST PAGE LOAD --}}
                    @foreach(range(1, 5) as $i)
                        <tr>
                            <td class="px-6 py-4"><div class="h-4 w-48 rounded-lg bg-[#222] animate-pulse"></div></td>
                            <td class="px-6 py-4"><div class="h-3 w-32 rounded-lg bg-[#1c1c1e] animate-pulse"></div></td>
                            <td class="px-6 py-4"><div class="h-5 w-16 rounded-md bg-[#222] animate-pulse"></div></td>
                            <td class="px-6 py-4"><div class="h-6 w-24 rounded-full bg-[#222] animate-pulse"></div></td>
                            <td class="px-6 py-4"><div class="h-3 w-20 rounded-lg bg-[#1c1c1e] animate-pulse"></div></td>
                            <td class="px-6 py-4 text-center"><div class="mx-auto h-8 w-20 rounded-xl bg-[#222] animate-pulse"></div></td>
                        </tr>
                    @endforeach
                @else
                    {{-- RENDERING SAAT DATA SUDAH READY ATAU KETIKA USER MENGGANTI NILAI PER PAGE --}}
                    @forelse ($applications as $app)
                        <tr class="hover:bg-[#171717]/40 transition duration-150" wire:key="app-{{ $app->id }}">
                            <td class="px-6 py-4 font-semibold tracking-tight">
                                <div wire:loading.delay class="h-4 w-48 rounded-lg bg-[#222] animate-pulse"></div>
                                <span wire:loading.remove>{{ $app->job_title }}</span>
                            </td>
                            <td class="px-6 py-4 text-[#a1a1aa]">
                                <div wire:loading.delay class="h-3 w-32 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
                                <span wire:loading.remove>{{ $app->job_company }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div wire:loading.delay class="h-5 w-16 rounded-md bg-[#222] animate-pulse"></div>
                                <span wire:loading.remove class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium border {{ $app->provider === 'glints' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20' }}">
                                    {{ $app->provider === 'glints' ? 'Glints' : 'JobStreet' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div wire:loading.delay class="h-6 w-24 rounded-full bg-[#222] animate-pulse"></div>
                                <div wire:loading.remove>
                                    @switch($app->status)
                                        @case('success') <span class="status-badge badge-success"><i class="fas fa-check-circle mr-1"></i>Success</span> @break
                                        @case('applied') <span class="status-badge badge-applied"><i class="fas fa-history mr-1"></i>Already Applied</span> @break
                                        @case('questionnaire') <span class="status-badge badge-screening"><i class="fas fa-clipboard-list mr-1"></i>Screening</span> @break
                                        @case('linkout') <span class="status-badge badge-start"><i class="fas fa-external-link-alt mr-1"></i>Linkout</span> @break
                                        @case('limit_provider') <span class="status-badge badge-limit"><i class="fas fa-exclamation-triangle mr-1"></i>Limit Provider</span> @break
                                        @default <span class="status-badge badge-default"><i class="fas fa-exclamation-circle mr-1"></i>Expired</span>
                                    @endswitch
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-[#a1a1aa]">
                                <div wire:loading.delay class="h-3 w-20 rounded-lg bg-[#1c1c1e] animate-pulse"></div>
                                <span wire:loading.remove>{{ $app->created_at ? $app->created_at->diffForHumans() : '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div wire:loading.delay class="mx-auto h-8 w-20 rounded-xl bg-[#222] animate-pulse"></div>
                                <div wire:loading.remove>
                                    @php
                                        $targetUrl = $app->provider === 'glints'
                                            ? "https://glints.com/id/opportunities/jobs/{$app->job_id}"
                                            : "https://www.jobstreet.co.id/id/job/{$app->job_id}";
                                    @endphp
                                    <a href="{{ $targetUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center h-8 px-4 rounded-xl border border-[#262626] bg-[#0a0a0a] text-xs font-medium text-[#fafafa] hover:bg-[#161618] hover:border-[#333] transition duration-150">
                                        <i class="fas fa-external-link-alt mr-1.5 text-[10px] text-[#a1a1aa]"></i> Preview
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 bg-[#0c0c0e]/30 text-center text-sm text-[#71717a]">
                                Belum ada aktivitas pelamaran yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                @endif
                </tbody>
            </table>
        </div>
        {{-- MOBILE --}}
        <div class="divide-y divide-[#262626] md:hidden">
            @if(!$isReady)

                @foreach(range(1,5) as $i)
                    <div class="space-y-3 p-4">
                        <div class="h-4 w-40 rounded bg-[#222] animate-pulse"></div>
                        <div class="h-3 w-28 rounded bg-[#1c1c1e] animate-pulse"></div>

                        <div class="flex justify-between">
                            <div class="h-5 w-20 rounded bg-[#222] animate-pulse"></div>
                            <div class="h-5 w-24 rounded bg-[#222] animate-pulse"></div>
                        </div>

                        <div class="h-8 w-full rounded-xl bg-[#222] animate-pulse"></div>
                    </div>
                @endforeach

            @else

                @forelse($applications as $app)

                    @php
                        $targetUrl = $app->provider === 'glints'
                            ? "https://glints.com/id/opportunities/jobs/{$app->job_id}"
                            : "https://www.jobstreet.co.id/id/job/{$app->job_id}";
                    @endphp

                    <div class="space-y-3 p-4" wire:key="mobile-app-{{ $app->id }}">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">
                                <p class="truncate font-semibold text-[#fafafa]">
                                    {{ $app->job_title }}
                                </p>

                                <p class="mt-1 truncate text-xs text-[#a1a1aa]">
                                    {{ $app->job_company }}
                                </p>
                            </div>

                            <div>
                                @switch($app->status)
                                    @case('success') <span class="status-badge badge-success"><i class="fas fa-check-circle mr-1"></i>Success</span> @break
                                    @case('applied') <span class="status-badge badge-applied"><i class="fas fa-history mr-1"></i>Already Applied</span> @break
                                    @case('questionnaire') <span class="status-badge badge-screening"><i class="fas fa-clipboard-list mr-1"></i>Screening</span> @break
                                    @case('linkout') <span class="status-badge badge-start"><i class="fas fa-external-link-alt mr-1"></i>Linkout</span> @break
                                    @default <span class="status-badge badge-default"><i class="fas fa-exclamation-circle mr-1"></i>Expired</span>
                                @endswitch
                            </div>

                        </div>

                        <div class="flex items-center justify-between">

                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium border {{ $app->provider === 'glints' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20' }}">
                        {{ $app->provider === 'glints' ? 'Glints' : 'JobStreet' }}
                    </span>

                            <span class="text-xs text-[#71717a]">
                        {{ $app->created_at ? $app->created_at->diffForHumans() : '-' }}
                    </span>

                        </div>

                        <a href="{{ $targetUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="flex h-10 w-full items-center justify-center rounded-xl border border-[#262626] bg-[#0a0a0a] text-sm font-medium text-[#fafafa] hover:bg-[#161618] transition">
                            <i class="fas fa-external-link-alt mr-2 text-[11px]"></i>
                            Preview
                        </a>

                    </div>

                @empty

                    <div class="p-6 text-center text-sm text-[#71717a]">
                        Belum ada aktivitas pelamaran yang tercatat.
                    </div>

                @endforelse

            @endif
        </div>
        @if($isReady && $applications->hasPages())
            <div class="border-t border-[#262626] bg-[#111111] px-4 py-4 sm:px-6">
                <div class="overflow-x-auto custom-scrollbar">
                    <div class="min-w-max">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        @endif
    </section>
</div>

@push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #111111;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #3f3f46;
            border-radius: 9999px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #52525b;
        }

        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #3f3f46 #111111;
        }
    </style>
@endpush
