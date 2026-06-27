@extends('layouts.app')

@section('title', 'Apply · Compass')
@section('titleNavbar', 'Apply')

@php
    $jobstreet_profile = null;
    $glints_profile    = null;

    $hasGlints     = isset($accounts['glints'])     && isset($adapters['glints']);
    $hasJobstreet  = isset($accounts['jobstreet'])  && isset($adapters['jobstreet']);

    // ✅ Flag paused sekarang di USER, bukan per account
    $isPaused = $user->automation_paused ?? false;

    if ($hasJobstreet) {
        $jobstreet_profile = $adapters['jobstreet']->loadProfile();
        $jobstreet_config  = $accounts['jobstreet']->getConfig() ?? [];
    }
    if ($hasGlints) {
        $glints_profile = $adapters['glints']->loadProfile();
        $glints_config  = $accounts['glints']->getConfig() ?? [];
    }
@endphp

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

        {{-- ═══ HEADER ═══ --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-white tracking-tight">Auto Apply</h1>
                <p class="text-sm text-white/40">Automation across JobStreet &amp; Glints</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Status Badge --}}
                <div id="main-status" class="flex items-center gap-2 px-3 py-1.5 rounded-full border border-white/10 bg-white/5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60 animate-ping" id="status-dot-ping"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400" id="status-dot"></span>
                    </span>
                    <span class="text-sm font-medium text-white/80" id="status-label">Running</span>
                </div>
                {{-- Settings link --}}
                <a href="{{ route('profile') }}" class="p-2 rounded-lg hover:bg-white/5 transition">
                    <i class="fas fa-sliders-h text-white/30 text-sm"></i>
                </a>
            </div>
        </div>

        {{-- ═══ HERO CARD ═══ --}}
        <div class="bg-[#111317] border border-white/5 rounded-2xl shadow-xl p-6 space-y-5">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div class="flex-1 space-y-3">
                    {{-- Keyword & Batch --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-white/30 uppercase tracking-wider mb-1.5">Keyword</label>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-white/20 text-sm"></i>
                                <input type="text" name="keyword" id="keyword-input" required placeholder="Web Developer"
                                       class="w-full h-10 rounded-xl border border-white/10 bg-[#0d0f16] pl-9 pr-3
                                          text-sm text-white placeholder-white/20
                                          focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/20 outline-none transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-white/30 uppercase tracking-wider mb-1.5">Batch</label>
                            <input type="number" name="pageSize" value="5" min="1" max="40"
                                   class="w-full h-10 rounded-xl border border-white/10 bg-[#0d0f16] px-3
                                      text-sm text-white
                                      focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/20 outline-none transition">
                        </div>
                    </div>

                    {{-- Providers + Today's Goal --}}
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-4">
                            {{-- JobStreet checkbox --}}
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-white/70">
                                <input type="checkbox" name="providers[]" value="jobstreet"
                                       class="provider-checkbox w-4 h-4 rounded border-white/20 bg-[#0d0f16] text-blue-500 focus:ring-0"
                                    {{ $hasJobstreet ? 'checked' : 'disabled' }}>
                                JobStreet
                            </label>
                            {{-- Glints checkbox --}}
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-white/70">
                                <input type="checkbox" name="providers[]" value="glints"
                                       class="provider-checkbox w-4 h-4 rounded border-white/20 bg-[#0d0f16] text-blue-500 focus:ring-0"
                                    {{ $hasGlints ? 'checked' : 'disabled' }}>
                                Glints
                            </label>
                            @if($isPaused)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center gap-1">
                                    <i class="fas fa-pause-circle text-[10px]"></i> Paused
                                </span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-[120px]">
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-white/30">Today</span>
                                <div class="flex-1 h-1.5 rounded-full bg-white/10 overflow-hidden">
                                    <div id="today-progress" class="h-full bg-blue-500 rounded-full" style="width:0%"></div>
                                </div>
                                <span class="text-xs font-medium text-white/60" id="today-count">0 / 200</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                    {{-- Push / Start --}}
                    <button id="push-btn"
                            class="inline-flex items-center justify-center gap-2 px-5 h-10 rounded-xl
                   bg-blue-600 hover:bg-blue-500 active:scale-[0.97]
                   text-sm font-semibold text-white transition-all duration-150 shadow-lg shadow-blue-600/20
                   {{ $isPaused ? 'opacity-50 pointer-events-none' : '' }}">
                        <i class="fas fa-plus-circle text-xs"></i>
                        Push
                    </button>

                    {{-- Stop --}}
                    <button id="stop-btn"
                            class="inline-flex items-center justify-center gap-2 px-5 h-10 rounded-xl
                   bg-red-600 hover:bg-red-500 active:scale-[0.97]
                   text-sm font-semibold text-white transition-all duration-150 shadow-lg shadow-red-600/20
                   {{ $isPaused ? 'opacity-50 pointer-events-none' : '' }}">
                        <i class="fas fa-stop-circle text-xs"></i>
                        Stop
                    </button>

                    {{-- Resume --}}
                    <button id="resume-btn"
                            class="inline-flex items-center justify-center gap-2 px-5 h-10 rounded-xl
                   bg-emerald-600 hover:bg-emerald-500 active:scale-[0.97]
                   text-sm font-semibold text-white transition-all duration-150 shadow-lg shadow-emerald-600/20
                   {{ !$isPaused ? 'opacity-50 pointer-events-none' : '' }}">
                        <i class="fas fa-play-circle text-xs"></i>
                        Resume
                    </button>
                </div>
            </div>

            {{-- Running Jobs (live) --}}
            <div id="running-jobs-container" class="space-y-2">
                <div class="flex items-center gap-2 text-xs text-white/30">
                    <i class="fas fa-sync-alt fa-spin text-[10px]"></i>
                    <span>Live jobs</span>
                </div>
                <div id="running-jobs-list" class="space-y-1.5">
                    <div class="text-sm text-white/20 italic">No active jobs</div>
                </div>
            </div>
        </div>

        {{-- ═══ QUICK STATS ═══ --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-[#111317] border border-white/5 rounded-xl p-4 text-center">
                <div class="text-xs font-medium text-white/30 uppercase tracking-wider">Queued</div>
                <div id="stat-queued" class="text-2xl font-bold text-white mt-1">0</div>
            </div>
            <div class="bg-[#111317] border border-white/5 rounded-xl p-4 text-center">
                <div class="text-xs font-medium text-white/30 uppercase tracking-wider">Running</div>
                <div id="stat-running" class="text-2xl font-bold text-amber-400 mt-1">0</div>
            </div>
            <div class="bg-[#111317] border border-white/5 rounded-xl p-4 text-center">
                <div class="text-xs font-medium text-white/30 uppercase tracking-wider">Applied</div>
                <div id="stat-applied" class="text-2xl font-bold text-blue-400 mt-1">0</div>
            </div>
            <div class="bg-[#111317] border border-white/5 rounded-xl p-4 text-center">
                <div class="text-xs font-medium text-white/30 uppercase tracking-wider">Success</div>
                <div id="stat-success" class="text-2xl font-bold text-emerald-400 mt-1">0%</div>
            </div>
        </div>

        {{-- ═══ ACTIVITY TIMELINE ═══ --}}
        <div class="bg-[#111317] border border-white/5 rounded-2xl p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-white/80">Activity</h3>
                <span class="text-xs text-white/20">Last 10</span>
            </div>
            <div id="activity-timeline" class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scroll">
                <div class="text-sm text-white/20 italic">No activity yet</div>
            </div>
        </div>

        {{-- ═══ DEBUG PANEL (Raw Pusher) ═══ --}}
        <div id="debug-panel" class="fixed bottom-6 right-6 bg-[#111317]/95 border border-white/20 p-4 rounded-xl z-50 w-80 shadow-2xl backdrop-blur-md">
            <div class="flex justify-between items-center mb-2 border-b border-white/10 pb-2">
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Raw Pusher Debug</span>
                <button onclick="document.getElementById('debug-panel').style.display='none'" class="text-white/40 hover:text-white/80 text-xs">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <pre id="debug-output" class="text-[10px] text-green-400 font-mono whitespace-pre-wrap break-words max-h-48 overflow-y-auto custom-scroll">Menunggu event masuk...</pre>
        </div>

        {{-- ═══ PROVIDERS (ACCORDION) ═══ --}}
        <div class="space-y-3">
            {{-- JobStreet --}}
            <div class="bg-[#111317] border border-white/5 rounded-2xl overflow-hidden">
                <button type="button" class="provider-toggle w-full px-5 py-4 flex items-center justify-between hover:bg-white/5 transition">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-briefcase text-white/30 text-sm"></i>
                        <span class="text-sm font-medium text-white/80">JobStreet</span>
                        @if($hasJobstreet)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                Connected
                            </span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Not connected</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-white/20 text-xs transition-transform duration-200"></i>
                </button>
                <div class="provider-content px-5 pb-5 hidden">
                    @if($hasJobstreet)
                        <form action="{{ route('platform.save-config', ['provider' => 'jobstreet']) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @csrf
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-white/40">Auto-answer</span>
                                    <div class="flex items-center gap-2">
                                        <label for="auto_answer_jobstreet" class="relative inline-flex cursor-pointer items-center">
                                            <input type="checkbox" name="auto_answer" id="auto_answer_jobstreet" value="1"
                                                   class="peer sr-only" {{ ($jobstreet_config['auto_answer'] ?? false) ? 'checked' : '' }}>
                                            <div class="h-5 w-9 rounded-full bg-white/10 transition-colors peer-checked:bg-blue-600
                                                    after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4
                                                    after:rounded-full after:bg-white after:transition-transform
                                                    peer-checked:after:translate-x-full"></div>
                                        </label>
                                        <span id="status-text-jobstreet" class="text-xs font-medium {{ ($jobstreet_config['auto_answer'] ?? false) ? 'text-blue-400' : 'text-white/30' }}">
                                            {{ ($jobstreet_config['auto_answer'] ?? false) ? 'On' : 'Off' }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-white/30 uppercase tracking-wider">Resume</label>
                                    <select name="resume" class="w-full mt-1 h-9 rounded-lg border border-white/10 bg-[#0d0f16] px-3 text-sm text-white">
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
                                    <label class="text-xs font-medium text-white/30 uppercase tracking-wider">Role</label>
                                    <select name="role" class="w-full mt-1 h-9 rounded-lg border border-white/10 bg-[#0d0f16] px-3 text-sm text-white">
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
                                    <label class="text-xs font-medium text-white/30 uppercase tracking-wider">Location</label>
                                    <input type="text" name="location" placeholder="Indonesia" value="{{ old('location', $jobstreet_config['location'] ?? '') }}"
                                           class="w-full mt-1 h-9 rounded-lg border border-white/10 bg-[#0d0f16] px-3 text-sm text-white">
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <button type="submit" class="w-full sm:w-auto px-6 h-9 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-sm font-medium text-white/80 transition">Save</button>
                            </div>
                        </form>
                    @else
                        <div class="py-4 text-center text-sm text-amber-400/70">
                            <i class="fas fa-plug-circle-xmark mr-2"></i> Not connected.
                            <a href="{{ route('profile') }}" class="underline hover:text-amber-300">Connect now</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Glints --}}
            <div class="bg-[#111317] border border-white/5 rounded-2xl overflow-hidden">
                <button type="button" class="provider-toggle w-full px-5 py-4 flex items-center justify-between hover:bg-white/5 transition">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-globe text-white/30 text-sm"></i>
                        <span class="text-sm font-medium text-white/80">Glints</span>
                        @if($hasGlints)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                Connected
                            </span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Not connected</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-white/20 text-xs transition-transform duration-200"></i>
                </button>
                <div class="provider-content px-5 pb-5 hidden">
                    @if($hasGlints)
                        <form action="{{ route('platform.save-config', ['provider' => 'glints']) }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-white/40">Auto-answer</span>
                                <div class="flex items-center gap-2 opacity-40 pointer-events-none">
                                    <div class="h-5 w-9 rounded-full bg-white/10 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:bg-white/40"></div>
                                    <span class="text-xs font-medium text-white/30">Off</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-white/30 uppercase tracking-wider">Locations</label>
                                <div id="location-tags-container" class="flex flex-wrap gap-1.5 mt-2 empty:hidden"></div>
                                <div class="relative mt-1">
                                    <input type="text" id="location-search-input" placeholder="Type location…" autocomplete="off"
                                           class="w-full h-9 rounded-lg border border-white/10 bg-[#0d0f16] px-3 text-sm text-white placeholder-white/20 focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/20 outline-none transition">
                                    <div id="location-loading" class="absolute right-3 top-2.5 hidden"><i class="fas fa-spinner fa-spin text-white/25 text-xs"></i></div>
                                </div>
                                <div id="location-hidden-inputs"></div>
                                <div id="glints-initial-locations"
                                     data-ids="{{ json_encode(old('location_ids', $glints_config['location_ids'] ?? [])) }}"
                                     data-names="{{ json_encode(old('location_names', $glints_config['location_names'] ?? [])) }}"
                                     class="hidden"></div>
                                <ul id="location-results" class="absolute z-50 w-full mt-1 max-h-44 overflow-y-auto rounded-lg border border-white/10 bg-[#111317] py-1 shadow-2xl hidden"></ul>
                            </div>
                            <button type="submit" class="px-6 h-9 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-sm font-medium text-white/80 transition">Save</button>
                        </form>
                    @else
                        <div class="py-4 text-center text-sm text-amber-400/70">
                            <i class="fas fa-plug-circle-xmark mr-2"></i> Not connected.
                            <a href="{{ route('profile') }}" class="underline hover:text-amber-300">Connect now</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #09090b; color: #e8edf4; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 10px; }

        .animate-ping { animation: ping 1.6s cubic-bezier(0,0,0.2,1) infinite; }
        @keyframes ping { 0% { transform: scale(1); opacity: 0.6; } 100% { transform: scale(2.4); opacity: 0; } }

        .provider-content { transition: all 0.2s ease; }
        .provider-toggle .fa-chevron-down { transition: transform 0.25s ease; }
        .provider-toggle[aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); }

        .running-job-item {
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .progress-bar {
            background: linear-gradient(90deg, #4F8CFF, #22C55E);
            border-radius: 9999px;
            height: 4px;
            transition: width 0.6s ease;
        }
    </style>
@endpush

<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ─── HELPERS ──────────────────────────────────────────────
        function formatTime(ts) {
            if (!ts) return 'Now';
            const d = typeof ts === 'number' ? new Date(ts * 1000) : new Date(ts);
            if (isNaN(d.getTime())) return 'Now';
            const now = new Date();
            const diff = Math.floor((now - d) / 1000);
            if (diff < 60) return diff + 's ago';
            if (diff < 3600) return Math.floor(diff/60) + 'm ago';
            if (diff < 86400) return Math.floor(diff/3600) + 'h ago';
            return d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
        }

        function statusColor(status) {
            const map = {
                'running':   'bg-emerald-400',
                'idle':      'bg-amber-400',
                'stopped':   'bg-red-400',
                'error':     'bg-red-400',
                'success':   'bg-emerald-400',
                'applied':   'bg-blue-400',
                'linkout':   'bg-amber-400',
                'questionnaire': 'bg-purple-400',
                'pending':   'bg-blue-400',
                'processing':'bg-amber-400',
            };
            return map[status] || 'bg-white/30';
        }

        function statusLabel(status) {
            const map = {
                'running': 'Running',
                'idle': 'Idle',
                'stopped': 'Stopped',
                'error': 'Error',
                'success': 'Success',
                'applied': 'Applied',
                'linkout': 'Link Out',
                'questionnaire': 'Screening',
                'pending': 'Pending',
                'processing': 'Processing',
            };
            return map[status] || status;
        }

        // ─── DOM REFS ──────────────────────────────────────────────
        const statusDot = document.getElementById('status-dot');
        const statusDotPing = document.getElementById('status-dot-ping');
        const statusLabelEl = document.getElementById('status-label');
        const pushBtn = document.getElementById('push-btn');
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

        // ─── STATE ──────────────────────────────────────────────────
        let isRunning = false;

        // ─── GET SELECTED PROVIDERS ──────────────────────────────
        function getSelectedProviders() {
            return Array.from(document.querySelectorAll('.provider-checkbox:checked'))
                .map(cb => cb.value);
        }

        // ─── UPDATE MAIN STATUS ────────────────────────────────────
        function updateMainStatus(status) {
            if (!statusDot || !statusDotPing || !statusLabelEl) return;

            const colorMap = {
                'running': 'bg-emerald-400',
                'stopped': 'bg-red-400',
                'idle': 'bg-amber-400'
            };
            const labelMap = {
                'running': 'Running',
                'stopped': 'Stopped',
                'idle': 'Idle'
            };
            const color = colorMap[status] || 'bg-white/30';
            const label = labelMap[status] || 'Unknown';

            statusDot.className = `relative inline-flex h-2.5 w-2.5 rounded-full ${color}`;
            statusDotPing.className = `absolute inline-flex h-full w-full rounded-full ${color} opacity-60 animate-ping`;
            statusLabelEl.textContent = label;

            isRunning = (status === 'running');
        }

        // ─── RENDER RUNNING JOBS ──────────────────────────────────
        function renderRunningJobs(jobs) {
            if (!runningList) return;
            if (!jobs || jobs.length === 0) {
                runningList.innerHTML = '<div class="text-sm text-white/20 italic">No active jobs</div>';
                return;
            }
            const active = jobs.slice(0, 5);
            let html = '';
            active.forEach(job => {
                const status = job.reserved_at ? 'processing' : 'pending';
                const label = statusLabel(status);
                const color = statusColor(status);
                const time = formatTime(job.created_at);
                html += `
                    <div class="running-job-item flex items-center justify-between bg-[#0d0f16] border border-white/5 rounded-xl px-4 py-2.5">
                        <div class="flex items-center gap-3">
                            <span class="inline-block w-2 h-2 rounded-full ${color}"></span>
                            <span class="text-sm font-medium text-white/80">Job #${job.id}</span>
                            <span class="text-xs text-white/40">${label}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-white/30">
                            <span>${job.attempts} attempts</span>
                            <span>·</span>
                            <span>${time}</span>
                        </div>
                    </div>
                `;
            });
            runningList.innerHTML = html;
        }

        // ─── RENDER ACTIVITY TIMELINE ──────────────────────────────
        function renderActivity(items) {
            if (!activityTimeline) return;
            if (!items || items.length === 0) {
                activityTimeline.innerHTML = '<div class="text-sm text-white/20 italic">No activity yet</div>';
                return;
            }
            const recent = items.slice(0, 10);
            let html = '';
            recent.forEach(item => {
                const status = item.status || 'applied';
                const label = statusLabel(status);
                const color = statusColor(status);
                const time = formatTime(item.updated_at);
                const provider = item.provider ? item.provider.charAt(0).toUpperCase() + item.provider.slice(1) : '';
                const jobId = item.job_id ? item.job_id.substring(0, 8) + '…' : 'Unknown';
                html += `
                    <div class="flex items-start gap-3 border-b border-white/5 pb-3 last:border-0 last:pb-0">
                        <span class="inline-block w-2 h-2 rounded-full ${color} mt-1.5"></span>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-white/80">${label}</span>
                                <span class="text-xs text-white/40">${provider}</span>
                            </div>
                            <div class="text-xs text-white/30">${jobId} · ${time}</div>
                        </div>
                    </div>
                `;
            });
            activityTimeline.innerHTML = html;
        }

        // ─── FETCH STATUS ──────────────────────────────────────────
        function fetchStatus() {
            fetch('{{ route("user.queue.status") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            })
                .then(r => r.json())
                .then(data => {
                    const pending = data.pending || 0;
                    const processing = data.processing || 0;

                    if (queuedEl) queuedEl.textContent = pending;
                    if (runningStatEl) runningStatEl.textContent = processing;

                    const status = (processing > 0) ? 'running' : 'stopped';
                    updateMainStatus(status);

                    if (todayProgress && todayCount) {
                        const total = 200;
                        const done = Math.min(pending + processing + (data.recent ? data.recent.length : 0), total);
                        const pct = Math.round((done / total) * 100);
                        todayProgress.style.width = pct + '%';
                        todayCount.textContent = done + ' / ' + total;
                    }

                    renderRunningJobs(data.recent || []);
                })
                .catch(() => {
                    updateMainStatus('stopped');
                    if (runningList) {
                        runningList.innerHTML = '<div class="text-sm text-white/20 italic">Failed to load</div>';
                    }
                });
        }

        function fetchApplicationStats() {
            fetch('{{ route("user.jobs.status") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(r => r.json())
                .then(data => {
                    const stats = data.stats || {};
                    if (appliedStatEl) appliedStatEl.textContent = stats.applied || 0;
                    if (successStatEl) {
                        const success = stats.success || 0;
                        const total = stats.total || 1;
                        successStatEl.textContent = Math.round((success / total) * 100) + '%';
                    }
                    renderActivity(data.recent || []);
                })
                .catch(() => {
                    if (activityTimeline) {
                        activityTimeline.innerHTML = '<div class="text-sm text-white/20 italic">Failed to load activity</div>';
                    }
                });
        }

        // ─── PUSH BUTTON ────────────────────────────────────────────
        if (pushBtn) {
            pushBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const providers = getSelectedProviders();
                if (providers.length === 0) {
                    alert('Select at least one provider.');
                    return;
                }
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("apply.push") }}';
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrf);

                const keyword = keywordInput ? keywordInput.value : '';
                const k = document.createElement('input');
                k.type = 'hidden';
                k.name = 'keyword';
                k.value = keyword;
                form.appendChild(k);

                const batch = document.querySelector('input[name="pageSize"]');
                if (batch) {
                    const b = document.createElement('input');
                    b.type = 'hidden';
                    b.name = 'pageSize';
                    b.value = batch.value;
                    form.appendChild(b);
                }

                providers.forEach(p => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'providers[]';
                    input.value = p;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            });
        }

        // ─── STOP BUTTON ────────────────────────────────────────────
        if (stopBtn) {
            stopBtn.addEventListener('click', function(e) {
                e.preventDefault();

                if (!confirm('Stop all automation?')) return;

                fetch('{{ route("apply.stop") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload();
                        } else {
                            alert('Failed to stop: ' + (data.errors?.general?.[0] || 'Unknown error'));
                        }
                    })
                    .catch(() => alert('Failed to stop automation.'));
            });
        }

        // ─── RESUME BUTTON ──────────────────────────────────────────
        if (resumeBtn) {
            resumeBtn.addEventListener('click', function(e) {
                e.preventDefault();

                fetch('{{ route("apply.resume") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'success') {
                            location.reload();
                        } else {
                            alert('Failed to resume: ' + (data.errors?.general?.[0] || 'Unknown error'));
                        }
                    })
                    .catch(() => alert('Failed to resume automation.'));
            });
        }

        // ─── ACCORDION PROVIDERS ───────────────────────────────────
        document.querySelectorAll('.provider-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !expanded);
                if (content) content.classList.toggle('hidden');
            });
        });

        // ─── LOCATION AUTOCOMPLETE (Glints) ──────────────────────
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
            } catch(e) {}

            const renderTags = () => {
                if (!tagsContainer || !hiddenInputs) return;
                tagsContainer.innerHTML = '';
                hiddenInputs.innerHTML = '';
                selectedLocations.forEach((loc, index) => {
                    const tag = document.createElement('div');
                    tag.className = 'flex items-center gap-1 bg-white/10 text-white/70 px-2.5 py-1 rounded-md text-xs';
                    const span = document.createElement('span');
                    span.textContent = loc.name;
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'text-white/30 hover:text-red-400 text-[10px]';
                    btn.innerHTML = '×';
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
                if (kw.length < 2) {
                    if (results) results.classList.add('hidden');
                    return;
                }
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
                        results.innerHTML = '<div class="px-3 py-2 text-sm text-white/30 italic">No results</div>';
                        results.classList.remove('hidden');
                        return;
                    }
                    let count = 0;
                    list.forEach(item => {
                        if (selectedLocations.some(l => l.id === item.id)) return;
                        const li = document.createElement('div');
                        li.className = 'cursor-pointer px-3 py-2 text-sm text-white/70 hover:bg-white/5 hover:text-white transition';
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
                    if (!count) results.innerHTML = '<div class="px-3 py-2 text-sm text-white/30 italic">All selected</div>';
                    results.classList.remove('hidden');
                } catch(e) {
                    if (results) {
                        results.innerHTML = '<div class="px-3 py-2 text-sm text-red-400/70">Failed to load</div>';
                        results.classList.remove('hidden');
                    }
                } finally {
                    if (loading) loading.classList.add('hidden');
                }
            }, 500));

            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && results && !results.contains(e.target)) {
                    results.classList.add('hidden');
                }
            });
        }

        // ─── TOGGLE TEXT ───────────────────────────────────────────
        const cb = document.getElementById('auto_answer_jobstreet');
        const st = document.getElementById('status-text-jobstreet');
        if (cb && st) {
            cb.addEventListener('change', function() {
                st.textContent = this.checked ? 'On' : 'Off';
                st.className = `text-xs font-medium transition-colors ${this.checked ? 'text-blue-400' : 'text-white/30'}`;
            });
        }

        // ─── START & INITIALIZATIONS ────────────────────────────────
        fetchStatus();
        fetchApplicationStats();

        // Interval dikurangi menjadi 60 detik sebagai cadangan (fallback)
        setInterval(fetchStatus, 60000);
        setInterval(fetchApplicationStats, 60000);

        initLocationAutocomplete();

        // ─── REAL-TIME PUSHER (REVERB) INTEGRATION ──────────────────
        Pusher.logToConsole = true;

        var pusher = new Pusher("{{ env('REVERB_APP_KEY') }}", {
            cluster: "",
            wsHost: "{{ env('REVERB_HOST', '127.0.0.1') }}",
            wsPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            enabledTransports: ['ws'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }
        });

        var userId = '{{ auth()->id() }}';
        var channel = pusher.subscribe("private-users." + userId);

        channel.bind("App\\Events\\JobStatus", (data) => {
            console.log("Data Event Masuk:", data);

            const debugOutput = document.getElementById('debug-output');
            if (debugOutput) {
                const time = new Date().toLocaleTimeString('id-ID');
                const rawJson = JSON.stringify(data, null, 2);

                if (debugOutput.textContent.includes('Menunggu')) {
                    debugOutput.textContent = '';
                }
                debugOutput.textContent = `[${time}]\n${rawJson}\n\n` + debugOutput.textContent;
            }

            // Fungsi ini sekarang berada di dalam scope yang sama,
            // sehingga bisa dipanggil saat event masuk!
            fetchStatus();
            fetchApplicationStats();
        });
    });
</script>
