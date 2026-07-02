@extends('layouts.app')

@section('title', 'Apply · Compass')
@section('titleNavbar', 'Apply')

@php
    $apply_configuration = auth()->user()->apply_configuration ?? [];
    $jobstreet_profile = null;
    $glints_profile = null;

    $hasGlints = isset($accounts['glints']) && isset($adapters['glints']);
    $hasJobstreet = isset($accounts['jobstreet']) && isset($adapters['jobstreet']);
    $isPaused = $user->automation_paused ?? false;

    if ($hasJobstreet) {
        $jobstreet_profile = $adapters['jobstreet']->loadProfile();
        $jobstreet_config = $accounts['jobstreet']->getConfig() ?? [];
    }
    if ($hasGlints) {
        $glints_profile = $adapters['glints']->loadProfile();
        $glints_config = $accounts['glints']->getConfig() ?? [];
    }
@endphp

@section('content')
    <div class="mx-auto max-w-[1400px] space-y-6 px-1 pb-6 pt-2">
        <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">STATUS</p>
                <div id="main-status" class="mt-3 inline-flex items-center gap-2 rounded-full border border-[#262626] bg-[#0a0a0a] px-3 py-1.5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span id="status-dot-ping" class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60 animate-ping"></span>
                        <span id="status-dot" class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                    </span>
                    <span id="status-label" class="text-sm font-medium text-[#fafafa]">Running</span>
                </div>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TOTAL PROSES</p>
                <p id="stat-running" class="mt-3 text-3xl font-semibold text-[#fafafa]">0</p>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">ANTRIAN</p>
                <p id="stat-queued" class="mt-3 text-3xl font-semibold text-[#fafafa]">0</p>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">PROGRES HARI INI</p>
                <div class="mt-3 flex items-center gap-3">
                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-[#1b1b1b]">
                        <div id="today-progress" class="h-full rounded-full bg-blue-600 transition-all duration-300" style="width:0%"></div>
                    </div>
                    <span id="today-count" class="text-xs font-medium text-[#fafafa]">0 / 200</span>
                </div>
            </article>
        </section>
        <section id="analytics-section" class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TOTAL LAMARAN</p>
                <p id="stat-applied" class="mt-3 text-3xl font-semibold text-[#fafafa]">0</p>
            </article>
            <article class="saas-card p-5">
                <p class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">TINGKAT KEBERHASILAN</p>
                <p id="stat-success" class="mt-3 text-3xl font-semibold text-emerald-400">0%</p>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="saas-card p-6 xl:col-span-2">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Panel</h2>
                        <p class="mt-1 text-sm text-[#a1a1aa]">Atur konfigurasi lamaran anda</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
{{--                        <button id="push-btn" class="h-8 cursor-pointer rounded-md bg-white/85 px-5 text-sm font-semibold text-black transition hover:bg-white {{ $isPaused ? 'pointer-events-none opacity-50' : '' }}">Push</button>--}}
                        <button id="save-btn" class="h-8 cursor-pointer rounded-md bg-white/85 px-5 text-sm font-semibold text-black transition hover:bg-white">Simpan</button>

                        @if ($isPaused)
                            <button id="resume-btn" class="h-8 cursor-pointer rounded-md bg-white/85 px-5 text-sm font-semibold text-[#222] transition hover:bg-white">
                                Resume
                            </button>
                        @else
                            <button id="stop-btn" class="h-8 cursor-pointer rounded-md bg-white/85 px-5 text-sm font-semibold text-[#222] transition hover:bg-white">
                                Stop
                            </button>
                        @endif
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Kata Kunci</label>
                        <input type="text" name="keyword" id="keyword-input" required value="{{ $apply_configuration['keyword'] ?? '' }}" placeholder="Web Developer" class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa] placeholder:text-[#71717a]" />
                    </div>
                    <div>
                        <label class="mb-2 block text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Batch</label>
                        <input type="number" name="pageSize" required value="{{ $apply_configuration['batch'] ?? '1' }}" min="1" max="40" class="saas-input h-11 w-full rounded-xl px-4 text-sm text-[#fafafa]" />
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-[#fafafa]">
                        <input type="checkbox" name="providers[]" value="jobstreet" class="provider-checkbox h-4 w-4 rounded border-[#3f3f46] bg-[#0a0a0a] text-blue-600 focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50"
                            {{ in_array('jobstreet', $apply_configuration['providers'] ?? []) ? 'checked' : '' }}
                            {{ !$hasJobstreet ? 'disabled' : '' }}>
                        JobStreet
                    </label>

                    <label class="inline-flex items-center gap-2 text-sm text-[#fafafa]">
                        <input type="checkbox" name="providers[]" value="glints" class="provider-checkbox h-4 w-4 rounded border-[#3f3f46] bg-[#0a0a0a] text-blue-600 focus:ring-0 disabled:cursor-not-allowed disabled:opacity-50"
                            {{ in_array('glints', $apply_configuration['providers'] ?? []) ? 'checked' : '' }}
                            {{ !$hasGlints ? 'disabled' : '' }}>
                        Glints
                    </label>

                    @if($isPaused)
                        <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-1 text-xs font-medium text-amber-400">Paused</span>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
                        <p class="text-xs text-[#a1a1aa]">Current provider</p>
                        <p id="current-provider" class="mt-1 text-sm font-medium text-[#fafafa]">-</p>
                    </div>
                    <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
                        <p class="text-xs text-[#a1a1aa]">Current job</p>
                        <p id="current-job" class="mt-1 text-sm font-medium text-[#fafafa]">-</p>
                    </div>
                    <div class="rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
                        <p class="text-xs text-[#a1a1aa]">Current stage</p>
                        <p id="current-stage" class="mt-1 text-sm font-medium text-[#fafafa]">Waiting</p>
                    </div>
                </div>

                <div class="mt-5 rounded-xl border border-[#262626] bg-[#0a0a0a] p-4">
                    <div class="flex items-center justify-between text-xs text-[#a1a1aa]">
                        <span>Progress</span>
                        <span id="remaining-jobs">Estimated remaining jobs: 0</span>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-[#1b1b1b]">
                        <div id="current-progress-bar" class="h-full w-0 rounded-full bg-blue-600 transition-all duration-500"></div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="progress-step" data-step="loading_job">Loading Job</span>
                        <span class="progress-step" data-step="loading_profile">Loading Profile</span>
                        <span class="progress-step" data-step="inspecting">Inspecting</span>
                        <span class="progress-step" data-step="building_payload">Building Payload</span>
                        <span class="progress-step" data-step="applying">Applying</span>
                        <span class="progress-step" data-step="success">Success</span>
                    </div>
                    <p id="current-log-line" class="mt-4 text-sm text-[#a1a1aa]">No active process</p>
                </div>
            </div>

            <div id="activity-section" class="saas-card p-6">
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Timeline Activity</h2>
                <p class="mt-1 text-sm text-[#a1a1aa]">Newest events first</p>
                <div id="activity-timeline" class="custom-scroll mt-5 max-h-[520px] space-y-3 overflow-y-auto pr-1">
                    <div class="italic text-[#8b949e]">Menunggu aktivitas...</div>
                </div>
            </div>
        </section>

        <section id="queue-section" class="saas-card overflow-hidden">
            <div class="border-b border-[#262626] px-6 py-4">
                <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">ANTRIAN</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[#0a0a0a]">
                        <tr class="text-left text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">
                            <th class="px-6 py-3">Provider</th>
                            <th class="px-6 py-3">Keyword</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Attempts</th>
                            <th class="px-6 py-3">Created</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="running-jobs-list" class="divide-y divide-[#262626]">
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-sm italic text-[#8b949e]">No active jobs</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="saas-card p-6">
            <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Deployment Log</h2>
            <p class="mt-1 text-sm text-[#a1a1aa]">Pantau lamaran melalui konsol</p>
            <pre id="debug-output" class="custom-scroll mt-4 max-h-72 overflow-y-auto rounded-xl border border-[#262626] bg-[#0a0a0a] p-4 font-mono text-xs text-[#fafafa]">[00:00:00] [IDLE] [SYSTEM] Menunggu event masuk...</pre>
        </section>


        <section id="settings-section" class="space-y-4">
            <h2 class="text-lg font-semibold tracking-tight text-[#fafafa]">Providers</h2>

            <div class="saas-card overflow-hidden">
                <button type="button" class="provider-toggle flex w-full items-center justify-between px-5 py-4 transition hover:bg-[#171717]">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-briefcase text-xs text-[#a1a1aa]"></i>
                        <span class="text-sm font-medium text-[#fafafa]">JobStreet</span>
                        @if($hasJobstreet)
                            <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-0.5 text-xs text-emerald-400">Connected</span>
                        @else
                            <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-0.5 text-xs text-amber-400">Not connected</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-xs text-[#a1a1aa] transition-transform duration-200"></i>
                </button>
                <div class="provider-content hidden border-t border-[#262626] px-5 pb-5 pt-4">
                    @if($hasJobstreet)
                        <form action="{{ route('platform.save-config', ['provider' => 'jobstreet']) }}" method="POST" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            @csrf
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-[#a1a1aa]">Auto-answer</span>
                                    <div class="flex items-center gap-2">
                                        <label for="auto_answer_jobstreet" class="relative inline-flex cursor-pointer items-center">
                                            <input type="checkbox" name="auto_answer" id="auto_answer_jobstreet" value="1" class="peer sr-only" {{ ($jobstreet_config['auto_answer'] ?? false) ? 'checked' : '' }}>
                                            <span class="h-5 w-9 rounded-full bg-[#27272a] transition-colors peer-checked:bg-blue-600"></span>
                                            <span class="absolute left-[2px] top-[2px] h-4 w-4 rounded-full bg-white transition-transform peer-checked:translate-x-4"></span>
                                        </label>
                                        <span id="status-text-jobstreet" class="text-xs font-medium {{ ($jobstreet_config['auto_answer'] ?? false) ? 'text-blue-400' : 'text-[#8b949e]' }}">{{ ($jobstreet_config['auto_answer'] ?? false) ? 'On' : 'Off' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Resume</label>
                                    <select name="resume" class="saas-input mt-1 h-11 w-full rounded-xl px-3 text-sm text-[#fafafa]">
                                        @if(isset($jobstreet_profile['resumes']))
                                            @php($selected = $jobstreet_config['resume'] ?? null)
                                            <option value="">Select</option>
                                            @foreach($jobstreet_profile['resumes'] as $r)
                                                <option value="{{ $r['id'] }}" {{ $selected == $r['id'] ? 'selected' : '' }}>{{ $r['fileMetadata']['name'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="">No resumes</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Role</label>
                                    <select name="role" class="saas-input mt-1 h-11 w-full rounded-xl px-3 text-sm text-[#fafafa]">
                                        @if(isset($jobstreet_profile['roles']))
                                            @php($selected = $jobstreet_config['role'] ?? null)
                                            <option value="">Select</option>
                                            @foreach($jobstreet_profile['roles'] as $r)
                                                <option value="{{ $r['id'] }}" {{ $selected == $r['id'] ? 'selected' : '' }}>{{ $r['title']['text'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="">No roles</option>
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Location</label>
                                    <input type="text" name="location" placeholder="Indonesia" value="{{ old('location', $jobstreet_config['location'] ?? '') }}" class="saas-input mt-1 h-11 w-full rounded-xl px-3 text-sm text-[#fafafa]">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <button type="submit" class="h-11 rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-500">Save</button>
                            </div>
                        </form>
                    @else
                        <div class="py-4 text-center text-sm text-amber-400">Not connected. <a href="{{ route('profile') }}" class="underline">Connect now</a></div>
                    @endif
                </div>
            </div>

            <div class="saas-card overflow-hidden">
                <button type="button" class="provider-toggle flex w-full items-center justify-between px-5 py-4 transition hover:bg-[#171717]">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-globe text-xs text-[#a1a1aa]"></i>
                        <span class="text-sm font-medium text-[#fafafa]">Glints</span>
                        @if($hasGlints)
                            <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2 py-0.5 text-xs text-emerald-400">Connected</span>
                        @else
                            <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-0.5 text-xs text-amber-400">Not connected</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-xs text-[#a1a1aa] transition-transform duration-200"></i>
                </button>
                <div class="provider-content hidden border-t border-[#262626] px-5 pb-5 pt-4">
                    @if($hasGlints)
                        <form action="{{ route('platform.save-config', ['provider' => 'glints']) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-[#a1a1aa]">Auto-answer</span>
                                <div class="pointer-events-none flex items-center gap-2 opacity-40">
                                    <span class="h-5 w-9 rounded-full bg-[#27272a]"></span>
                                    <span class="text-xs text-[#a1a1aa]">Off</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs uppercase tracking-[0.14em] text-[#a1a1aa]">Locations</label>
                                <div id="location-tags-container" class="mt-2 flex flex-wrap gap-1.5 empty:hidden"></div>
                                <div class="relative mt-1">
                                    <input type="text" id="location-search-input" placeholder="Type location..." autocomplete="off" class="saas-input h-11 w-full rounded-xl px-3 text-sm text-[#fafafa] placeholder:text-[#71717a]">
                                    <div id="location-loading" class="absolute right-3 top-3 hidden"><i class="fas fa-spinner fa-spin text-xs text-[#a1a1aa]"></i></div>
                                </div>
                                <div id="location-hidden-inputs"></div>
                                <div id="glints-initial-locations" data-ids="{{ json_encode(old('location_ids', $glints_config['location_ids'] ?? [])) }}" data-names="{{ json_encode(old('location_names', $glints_config['location_names'] ?? [])) }}" class="hidden"></div>
                                <ul id="location-results" class="absolute z-50 mt-1 hidden max-h-44 w-full overflow-y-auto rounded-xl border border-[#262626] bg-[#111111] py-1 shadow-2xl"></ul>
                            </div>
                            <button type="submit" class="h-11 rounded-xl bg-blue-600 px-5 text-sm font-semibold text-white transition hover:bg-blue-500">Save</button>
                        </form>
                    @else
                        <div class="py-4 text-center text-sm text-amber-400">Not connected. <a href="{{ route('profile') }}" class="underline">Connect now</a></div>
                    @endif
                </div>
            </div>
        </section>

        <div id="debug-panel" class="hidden"></div>
    </div>
@endsection

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: Inter, system-ui, sans-serif; background:#0A0A0A; color:#FAFAFA; }
        .saas-card { background:#111111; border:1px solid #262626; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.28); transition:all .2s ease; }
        .saas-card:hover { border-color:#333333; }
        .saas-input { border:1px solid #262626; background:#0A0A0A; outline:0; transition:.2s ease; }
        .saas-input:focus { border-color:#3B82F6; box-shadow:0 0 0 2px rgba(59,130,246,.15); }
        .custom-scroll::-webkit-scrollbar { width:6px; height:6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background:#262626; border-radius:9999px; }
        .animate-ping { animation: ping 1.6s cubic-bezier(0,0,.2,1) infinite; }
        @keyframes ping { 0% { transform: scale(1); opacity: .6; } 100% { transform: scale(2.4); opacity: 0; } }
        .provider-toggle[aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); }
        .activity-item { animation: fadeIn .25s ease; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
        .progress-step { border:1px solid #262626; background:#0a0a0a; color:#a1a1aa; border-radius:9999px; padding:.25rem .6rem; font-size:.72rem; transition:.2s ease; }
        .progress-step.active { border-color:#3b82f6; color:#fafafa; background:rgba(59,130,246,.2); }
        .status-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.15rem .6rem; border-radius:9999px; font-size:.65rem; font-weight:500; border:1px solid rgba(255,255,255,.08); white-space:nowrap; }
        .badge-start,.badge-load_job { color:#60a5fa; background:rgba(59,130,246,.16); border-color:rgba(59,130,246,.28); }
        .badge-load_profile,.badge-load_userConfig { color:#f59e0b; background:rgba(245,158,11,.16); border-color:rgba(245,158,11,.28); }
        .badge-inspect { color:#f59e0b; background:rgba(245,158,11,.16); border-color:rgba(245,158,11,.28); }
        .badge-success,.badge-applied { color:#22c55e; background:rgba(34,197,94,.16); border-color:rgba(34,197,94,.28); }
        .badge-error { color:#ef4444; background:rgba(239,68,68,.16); border-color:rgba(239,68,68,.28); }
        .badge-default { color:#a1a1aa; background:rgba(161,161,170,.16); border-color:rgba(161,161,170,.28); }
    </style>
@endpush

<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatTime(ts) {
            if (!ts) return 'Now';
            const d = typeof ts === 'number' ? new Date(ts * 1000) : new Date(ts);
            if (isNaN(d.getTime())) return 'Now';
            const now = new Date();
            const diff = Math.floor((now - d) / 1000);
            if (diff < 60) return diff + 's ago';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        function getStatusInfo(status) {
            const map = {
                start: { icon: 'fa-play-circle', label: 'Start', description: 'Memulai proses lamaran', badgeClass: 'badge-start' },
                load_job: { icon: 'fa-file-alt', label: 'Load Job', description: 'Membaca detail pekerjaan', badgeClass: 'badge-load_job' },
                load_profile: { icon: 'fa-user', label: 'Load Profile', description: 'Membaca profil pelamar', badgeClass: 'badge-load_profile' },
                load_userConfig: { icon: 'fa-cog', label: 'Load Config', description: 'Membaca konfigurasi pengguna', badgeClass: 'badge-load_userConfig' },
                inspect: { icon: 'fa-search', label: 'Inspect', description: 'Memeriksa apakah dapat dilamar', badgeClass: 'badge-inspect' },
                build_payload: { icon: 'fa-cog', label: 'Build', description: 'Membangun payload lamaran', badgeClass: 'badge-load_userConfig' },
                apply: { icon: 'fa-paper-plane', label: 'Apply', description: 'Mengirim lamaran', badgeClass: 'badge-start' },
                success: { icon: 'fa-check-circle', label: 'Success', description: 'Berhasil melamar pekerjaan', badgeClass: 'badge-success' },
                applied: { icon: 'fa-check-circle', label: 'Applied', description: 'Lamaran terkirim', badgeClass: 'badge-applied' },
                error: { icon: 'fa-exclamation-circle', label: 'Error', description: 'Terjadi kesalahan', badgeClass: 'badge-error' }
            };
            return map[status] || { icon: 'fa-circle', label: status || 'Unknown', description: status || 'Unknown status', badgeClass: 'badge-default' };
        }

        const statusDot = document.getElementById('status-dot');
        const statusDotPing = document.getElementById('status-dot-ping');
        const statusLabelEl = document.getElementById('status-label');
        // const pushBtn = document.getElementById('push-btn');
        const saveBtn = document.getElementById('save-btn')
        const stopBtn = document.getElementById('stop-btn');
        const resumeBtn = document.getElementById('resume-btn');
        const runningList = document.getElementById('running-jobs-list');
        const todayProgress = document.getElementById('today-progress');
        const todayCount = document.getElementById('today-count');
        const queuedEl = document.getElementById('stat-queued');
        const runningStatEl = document.getElementById('stat-running');
        const appliedStatEl = document.getElementById('stat-applied');
        const successStatEl = document.getElementById('stat-success');
        const activityTimeline = document.getElementById('activity-timeline');
        const keywordInput = document.getElementById('keyword-input');
        const currentProvider = document.getElementById('current-provider');
        const currentJob = document.getElementById('current-job');
        const currentStage = document.getElementById('current-stage');
        const currentProgressBar = document.getElementById('current-progress-bar');
        const currentLogLine = document.getElementById('current-log-line');
        const remainingJobs = document.getElementById('remaining-jobs');
        const debugOutput = document.getElementById('debug-output');
        let lastKnownAutomation = null; // simpan data job terakhir yang diterima

        function setProgressStep(step) {
            document.querySelectorAll('.progress-step').forEach(el => el.classList.remove('active'));
            if (!step) return;
            const target = document.querySelector(`.progress-step[data-step="${step}"]`);
            if (target) target.classList.add('active');
        }

        function resetCurrentAutomation() {
            if (currentProvider) currentProvider.textContent = '-';
            if (currentJob) currentJob.textContent = '-';
            if (currentStage) currentStage.textContent = 'Idle';
            if (currentProgressBar) currentProgressBar.style.width = '0%';
            if (remainingJobs) remainingJobs.textContent = 'Estimated remaining jobs: 0';
            if (currentLogLine) currentLogLine.textContent = 'No active process';
            setProgressStep(null);
            lastKnownAutomation = null;
        }

        function setCurrentAutomation(data, queueData) {
            // simpan data terbaru kalau ada (dari pusher event)
            if (data) {
                lastKnownAutomation = data;
            }

            const source = data || lastKnownAutomation;
            const pending = queueData?.pending || 0;
            const processing = queueData?.processing || 0;

            // beneran stopped: gak ada antrian & gak ada data yang diketahui -> baru direset
            if (!source && pending === 0 && processing === 0) {
                resetCurrentAutomation();
                return;
            }

            // masih running/ada antrian: tampilin data terakhir yang diketahui
            if (!source) return; // masih ada antrian tapi belum ada data job spesifik, biarin apa adanya

            const status = source.status || 'idle';
            const provider = source.provider || '-';
            const jobId = source.job_id ? source.job_id.substring(0, 12) + '...' : '-';
            const pct = pending + processing > 0 ? Math.max(8, Math.min(95, Math.round((processing / (pending + processing)) * 100))) : 0;

            if (currentProvider) currentProvider.textContent = provider;
            if (currentJob) currentJob.textContent = jobId;
            if (currentStage) currentStage.textContent = getStatusInfo(status).label;
            if (currentProgressBar) currentProgressBar.style.width = pct + '%';
            if (remainingJobs) remainingJobs.textContent = 'Estimated remaining jobs: ' + pending;
            if (currentLogLine) currentLogLine.textContent = getStatusInfo(status).description;

            const mapStep = {
                start: 'searching',
                inspect: 'inspecting',
                load_job: 'loading_job',
                load_profile: 'loading_profile',
                load_userConfig: 'loading_profile',
                build_payload: 'building_payload',
                apply: 'applying',
                success: 'success',
                applied: 'success'
            };
            setProgressStep(mapStep[status] || null);
        }
        function appendDeploymentLog(data) {
            if (!debugOutput) return;
            const info = getStatusInfo(data.status || 'idle');
            const time = new Date().toLocaleTimeString('id-ID');
            const provider = (data.provider || 'system').toUpperCase();
            const line = `[${time}] [${info.label.toUpperCase()}] [${provider}] ${info.description}`;
            const initial = 'Menunggu event masuk...';
            if (debugOutput.textContent.includes(initial)) debugOutput.textContent = '';
            debugOutput.textContent = line + "\n" + debugOutput.textContent;
        }

        function getSelectedProviders() {
            return Array.from(document.querySelectorAll('.provider-checkbox:checked')).map(cb => cb.value);
        }

        function updateMainStatus(status) {
            if (!statusDot || !statusDotPing || !statusLabelEl) return;
            const colorMap = { running: 'bg-emerald-400', stopped: 'bg-red-400', idle: 'bg-amber-400' };
            const labelMap = { running: 'Running', stopped: 'Stopped', idle: 'Idle' };
            const color = colorMap[status] || 'bg-white/30';
            statusDot.className = `relative inline-flex h-2.5 w-2.5 rounded-full ${color}`;
            statusDotPing.className = `absolute inline-flex h-full w-full rounded-full ${color} opacity-60 animate-ping`;
            statusLabelEl.textContent = labelMap[status] || 'Unknown';
        }

        function renderRunningJobs(jobs) {
            if (!runningList) return;
            if (!jobs || jobs.length === 0) {
                runningList.innerHTML = '<tr><td colspan="6" class="px-6 py-6 text-sm italic text-[#8b949e]">No active jobs</td></tr>';
                return;
            }
            const active = jobs.slice(0, 10);
            runningList.innerHTML = active.map(job => {
                const status = job.reserved_at ? 'Processing' : 'Pending';
                const statusClass = job.reserved_at ? 'text-amber-400' : 'text-blue-400';
                const provider = (job.queue || 'automation').toString().replace('default', 'System');
                return `
                    <tr class="text-sm">
                        <td class="px-6 py-4 text-[#fafafa]">${provider}</td>
                        <td class="px-6 py-4 text-[#a1a1aa]">${keywordInput ? (keywordInput.value || '-') : '-'}</td>
                        <td class="px-6 py-4"><span class="${statusClass}">${status}</span></td>
                        <td class="px-6 py-4 text-[#a1a1aa]">${job.attempts}</td>
                        <td class="px-6 py-4 text-[#a1a1aa]">${formatTime(job.created_at)}</td>
                        <td class="px-6 py-4 text-[#a1a1aa]">Auto</td>
                    </tr>
                `;
            }).join('');
        }

        function addActivityItem(data) {
            if (!activityTimeline) return;
            const placeholder = activityTimeline.querySelector('.text-\\[\\#8b949e\\].italic');
            if (placeholder && activityTimeline.children.length === 1) activityTimeline.innerHTML = '';
            const status = data.status || 'unknown';
            const info = getStatusInfo(status);
            const provider = data.provider ? data.provider.charAt(0).toUpperCase() + data.provider.slice(1) : 'System';
            const jobId = data.job_id ? data.job_id.substring(0, 8) + '…' : 'N/A';
            const time = formatTime(new Date());
            const item = document.createElement('div');
            item.className = 'activity-item rounded-xl border border-[#262626] bg-[#0a0a0a] p-3';
            item.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-start gap-3">
                        <span class="mt-0.5 inline-flex h-7 w-7 items-center justify-center rounded-lg border border-[#262626] bg-[#111111] text-xs text-[#a1a1aa]"><i class="fas ${info.icon}"></i></span>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="text-[#a1a1aa]">${time}</span>
                                <span class="rounded-full border border-[#262626] bg-[#111111] px-2 py-0.5 text-[#fafafa]">${provider}</span>
                                <span class="status-badge ${info.badgeClass}"><i class="fas ${info.icon}"></i>${info.label}</span>
                            </div>
                            <p class="mt-2 text-sm text-[#fafafa]">${info.description} · ${jobId}</p>
                        </div>
                    </div>
                </div>
            `;
            activityTimeline.prepend(item);
            while (activityTimeline.children.length > 50) activityTimeline.removeChild(activityTimeline.lastChild);
            setCurrentAutomation(data, null);
            appendDeploymentLog(data);
        }

        function fetchStatus() {
            fetch('{{ route("user.queue.status") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                credentials: 'same-origin'
            }).then(r => r.json()).then(data => {
                const pending = data.pending || 0;
                const processing = data.processing || 0;
                if (queuedEl) queuedEl.textContent = pending;
                if (runningStatEl) runningStatEl.textContent = processing;
                updateMainStatus((processing > 0) ? 'running' : 'stopped');
                if (todayProgress && todayCount) {
                    const total = 200;
                    const done = Math.min(pending + processing + (data.recent ? data.recent.length : 0), total);
                    todayProgress.style.width = Math.round((done / total) * 100) + '%';
                    todayCount.textContent = done + ' / ' + total;
                }
                renderRunningJobs(data.recent || []);
                setCurrentAutomation(null, data);
            }).catch(() => {
                updateMainStatus('stopped');
                if (runningList) runningList.innerHTML = '<tr><td colspan="6" class="px-6 py-6 text-sm italic text-[#8b949e]">Failed to load</td></tr>';
            });
        }


        function fetchApplicationStats() {
            fetch('{{ route("user.applications") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            }).then(r => r.json()).then(data => {
                const stats = data.stats || {};

                // Update Total Lamaran
                if (appliedStatEl) appliedStatEl.textContent = stats.applied || 0;

                // Update Tingkat Keberhasilan
                if (successStatEl) {
                    const success = stats.success || 0 + stats.applied || 0;
                    const total = stats.total || 1;
                    successStatEl.textContent = Math.round((success / total) * 100) + '%';
                }

                // --- UPDATE PROGRES HARI INI ---
                const dailyLimit = 200;
                const todayDone = stats.today_count || 0;
                const progressPct = Math.min((todayDone / dailyLimit) * 100, 100);

                if (todayProgress) {
                    todayProgress.style.width = progressPct + '%';
                }
                if (todayCount) {
                    todayCount.textContent = todayDone + ' / ' + dailyLimit;
                }
            }).catch(() => {});
        }

        {{--if (pushBtn) {--}}
        {{--    pushBtn.addEventListener('click', function(e) {--}}
        {{--        e.preventDefault();--}}
        {{--        const providers = getSelectedProviders();--}}
        {{--        if (providers.length === 0) { alert('Select at least one provider.'); return; }--}}
        {{--        const form = document.createElement('form');--}}
        {{--        form.method = 'POST';--}}
        {{--        form.action = '{{ route("apply.save") }}';--}}
        {{--        const csrf = document.createElement('input');--}}
        {{--        csrf.type = 'hidden';--}}
        {{--        csrf.name = '_token';--}}
        {{--        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');--}}
        {{--        form.appendChild(csrf);--}}
        {{--        const keyword = keywordInput ? keywordInput.value : '';--}}
        {{--        const k = document.createElement('input');--}}
        {{--        k.type = 'hidden';--}}
        {{--        k.name = 'keyword';--}}
        {{--        k.value = keyword;--}}
        {{--        form.appendChild(k);--}}
        {{--        const batch = document.querySelector('input[name="pageSize"]');--}}
        {{--        if (batch) {--}}
        {{--            const b = document.createElement('input');--}}
        {{--            b.type = 'hidden';--}}
        {{--            b.name = 'pageSize';--}}
        {{--            b.value = batch.value;--}}
        {{--            form.appendChild(b);--}}
        {{--        }--}}
        {{--        providers.forEach(p => {--}}
        {{--            const input = document.createElement('input');--}}
        {{--            input.type = 'hidden';--}}
        {{--            input.name = 'providers[]';--}}
        {{--            input.value = p;--}}
        {{--            form.appendChild(input);--}}
        {{--        });--}}
        {{--        document.body.appendChild(form);--}}
        {{--        form.submit();--}}
        {{--    });--}}
        {{--}--}}

        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const providers = getSelectedProviders();
                if (providers.length === 0) {
                    alert('Pilih minimal 1 provider.');
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("apply.save") }}';

                // CSRF Token
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrf);

                // apply_configuration[keyword]
                const keyword = keywordInput ? keywordInput.value : '';
                const k = document.createElement('input');
                k.type = 'hidden';
                k.name = 'apply_configuration[keyword]'; // Diubah menjadi bentuk array
                k.value = keyword;
                form.appendChild(k);

                // apply_configuration[batch]
                const batch = document.querySelector('input[name="pageSize"]');
                if (batch) {
                    const b = document.createElement('input');
                    b.type = 'hidden';
                    b.name = 'apply_configuration[batch]'; // Diubah menjadi bentuk array
                    b.value = batch.value;
                    form.appendChild(b);
                }

                // providers[] (tetap sebagai array terpisah)
                providers.forEach(p => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'apply_configuration[providers][]';
                    input.value = p;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            });
        }

        if (stopBtn) {
            stopBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm('Stop all automation?')) return;
                fetch('{{ route("apply.stop") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    credentials: 'same-origin'
                }).then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload();
                    else alert('Failed to stop: ' + (data.errors?.general?.[0] || 'Unknown error'));
                }).catch(() => alert('Failed to stop automation.'));
            });
        }

        if (resumeBtn) {
            resumeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('{{ route("apply.resume") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    credentials: 'same-origin'
                }).then(r => r.json()).then(data => {
                    if (data.status === 'success') location.reload();
                    else alert('Failed to resume: ' + (data.errors?.general?.[0] || 'Unknown error'));
                }).catch(() => alert('Failed to resume automation.'));
            });
        }

        document.querySelectorAll('.provider-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !expanded);
                if (content) content.classList.toggle('hidden');
            });
        });

        function initLocationAutocomplete() {
            const input = document.getElementById('location-search-input');
            const results = document.getElementById('location-results');
            const tagsContainer = document.getElementById('location-tags-container');
            const hiddenInputs = document.getElementById('location-hidden-inputs');
            const loading = document.getElementById('location-loading');
            const initialData = document.getElementById('glints-initial-locations');
            if (!input) return;
            let selectedLocations = [];
            try {
                const ids = JSON.parse(initialData?.dataset?.ids || '[]');
                const names = JSON.parse(initialData?.dataset?.names || '[]');
                ids.forEach((id, i) => { if (names[i]) selectedLocations.push({ id, name: names[i] }); });
            } catch (e) {}

            const renderTags = () => {
                if (!tagsContainer || !hiddenInputs) return;
                tagsContainer.innerHTML = '';
                hiddenInputs.innerHTML = '';
                selectedLocations.forEach((loc, index) => {
                    const tag = document.createElement('div');
                    tag.className = 'flex items-center gap-1 rounded-md bg-[#262626] px-2.5 py-1 text-xs text-[#fafafa]';
                    const span = document.createElement('span');
                    span.textContent = loc.name;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'text-[10px] text-[#a1a1aa] hover:text-red-400';
                    btn.innerHTML = 'x';
                    btn.onclick = () => { selectedLocations.splice(index, 1); renderTags(); };
                    tag.appendChild(span);
                    tag.appendChild(btn);
                    tagsContainer.appendChild(tag);

                    const h1 = document.createElement('input');
                    h1.type = 'hidden';
                    h1.name = 'location_ids[]';
                    h1.value = loc.id;
                    const h2 = document.createElement('input');
                    h2.type = 'hidden';
                    h2.name = 'location_names[]';
                    h2.value = loc.name;
                    hiddenInputs.appendChild(h1);
                    hiddenInputs.appendChild(h2);
                });
            };
            renderTags();

            const debounce = (fn, ms) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); }; };
            input.addEventListener('input', debounce(async (e) => {
                const kw = e.target.value.trim();
                if (kw.length < 2) { if (results) results.classList.add('hidden'); return; }
                if (loading) loading.classList.remove('hidden');
                try {
                    const fd = new FormData();
                    fd.append('keyword', kw);
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    const r = await fetch('{{ route("api.search.location", ["provider" => "glints"]) }}', {
                        method: 'POST',
                        body: fd,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!r.ok) throw new Error();
                    const data = await r.json();
                    let list = data.data?.searchHierarchicalLocations?.list || data.data?.searchHierarchicalLocations || data.list || [];
                    if (!results) return;
                    results.innerHTML = '';
                    if (!list.length) {
                        results.innerHTML = '<div class="px-3 py-2 text-sm italic text-[#8b949e]">No results</div>';
                        results.classList.remove('hidden');
                        return;
                    }
                    let count = 0;
                    list.forEach(item => {
                        if (selectedLocations.some(l => l.id === item.id)) return;
                        const li = document.createElement('div');
                        li.className = 'cursor-pointer px-3 py-2 text-sm text-[#fafafa] transition hover:bg-[#1b1b1b]';
                        li.textContent = item.name;
                        li.onclick = () => {
                            selectedLocations.push({ id: item.id, name: item.name });
                            renderTags();
                            input.value = '';
                            results.classList.add('hidden');
                        };
                        results.appendChild(li);
                        count++;
                    });
                    if (!count) results.innerHTML = '<div class="px-3 py-2 text-sm italic text-[#8b949e]">All selected</div>';
                    results.classList.remove('hidden');
                } catch (e) {
                    if (results) {
                        results.innerHTML = '<div class="px-3 py-2 text-sm text-red-400/70">Failed to load</div>';
                        results.classList.remove('hidden');
                    }
                } finally {
                    if (loading) loading.classList.add('hidden');
                }
            }, 500));

            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && results && !results.contains(e.target)) results.classList.add('hidden');
            });
        }

        const cb = document.getElementById('auto_answer_jobstreet');
        const st = document.getElementById('status-text-jobstreet');
        if (cb && st) {
            cb.addEventListener('change', function() {
                st.textContent = this.checked ? 'On' : 'Off';
                st.className = `text-xs font-medium transition-colors ${this.checked ? 'text-blue-400' : 'text-[#8b949e]'}`;
            });
        }

        fetchStatus();
        fetchApplicationStats();
        setInterval(fetchStatus, 60000);
        setInterval(fetchApplicationStats, 60000);
        initLocationAutocomplete();

        Pusher.logToConsole = true;
        var pusher = new Pusher("{{ env('REVERB_APP_KEY') }}", {
            cluster: "",
            wsHost: "{{ env('REVERB_HOST', '127.0.0.1') }}",
            wsPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            enabledTransports: ['ws'],
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } }
        });
        var userId = '{{ auth()->id() }}';
        var channel = pusher.subscribe("private-users." + userId);
        channel.bind("JobStatus", (data) => {
            addActivityItem(data);
            fetchStatus();
        });
    });
</script>
