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
    <div class="flex flex-col gap-4">
        <livewire:stats-overview />
        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <livewire:panel-configuration :accounts="$accounts" :adapters="$adapters" />
            <livewire:live-monitoring />

            <div class="saas-card p-6">
                <livewire:activity-timeline />
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Geist', system-ui, sans-serif; background:#0A0A0A; color:#FAFAFA; }
        .font-mono { font-family: 'Geist Mono', monospace !important; }
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
        .badge-success { color:#22c55e; background:rgba(34,197,94,.16); border-color:rgba(34,197,94,.28); }
        .badge-screening { color: #fbbf24; background: rgba(245, 158, 11, 0.16); border-color: rgba(245, 158, 11, 0.28); }
        .badge-applied { color:#a78bfa; background:rgba(167,139,250,.16); border-color:rgba(167,139,250,.28); }
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
                questionnaire: { icon: 'fa-clipboard-list', label: 'Need Screening', description: 'Dilewati, perlu menjawab pertanyaan screening', badgeClass: 'badge-screening' },
                applied: { icon: 'fa-history', label: 'Already Applied', description: 'Dilewati, posisi ini sudah pernah dilamar sebelumnya', badgeClass: 'badge-applied' },
                success: { icon: 'fa-check-circle', label: 'Success', description: 'Berhasil melamar pekerjaan baru', badgeClass: 'badge-success' },
                error: { icon: 'fa-exclamation-circle', label: 'Error', description: 'Terjadi kesalahan', badgeClass: 'badge-error' }
            };
            return map[status] || { icon: 'fa-circle', label: status || 'Unknown', description: status || 'Unknown status', badgeClass: 'badge-default' };
        }

        const statusDot = document.getElementById('status-dot');
        const statusDotPing = document.getElementById('status-dot-ping');
        const statusLabelEl = document.getElementById('status-label');
        const saveBtn = document.getElementById('save-btn')
        const stopBtn = document.getElementById('stop-btn');
        const resumeBtn = document.getElementById('resume-btn');
        const runningList = document.getElementById('running-jobs-list');
        const todayProgress = document.getElementById('today-progress');
        const todayCount = document.getElementById('today-count');
        const queuedEl = document.getElementById('stat-queued');
        const runningStatEl = document.getElementById('stat-running');

        const successCountStatEl = document.getElementById('stat-success-count');
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
        let lastKnownAutomation = null;

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
            if (data) { lastKnownAutomation = data; }
            const source = data || lastKnownAutomation;
            const pending = queueData?.pending || 0;
            const processing = queueData?.processing || 0;

            if (!source && pending === 0 && processing === 0) {
                resetCurrentAutomation();
                return;
            }
            if (!source) return;

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
                questionnaire: 'questionnaire',
                applied: 'applied',
                apply: 'applying',
                success: 'success'
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
            if (activityTimeline.children.length > 50) activityTimeline.removeChild(activityTimeline.lastChild);
            setCurrentAutomation(data, null);
            appendDeploymentLog(data);
            fetchApplicationStats();
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
                const successCount = stats.success || 0;
                const appliedCount = stats.applied || 0;
                const total = stats.total || 1;

                if (successCountStatEl) successCountStatEl.textContent = successCount;
                if (appliedStatEl) appliedStatEl.textContent = appliedCount;
                if (successStatEl) {
                    const rate = Math.round(((successCount + appliedCount) / total) * 100);
                    successStatEl.textContent = rate + '%';
                }

                const dailyLimit = 200;
                const todayDone = stats.today_count || 0;
                const progressPct = Math.min((todayDone / dailyLimit) * 100, 100);

                if (todayProgress) todayProgress.style.width = progressPct + '%';
                if (todayCount) todayCount.textContent = todayDone + ' / ' + dailyLimit;
            }).catch(() => {});
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const providers = getSelectedProviders();
                if (providers.length === 0) { alert('Pilih minimal 1 provider.'); return; }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("apply.save") }}';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrf);

                const keyword = keywordInput ? keywordInput.value : '';
                const k = document.createElement('input');
                k.type = 'hidden';
                k.name = 'apply_configuration[keyword]';
                k.value = keyword;
                form.appendChild(k);

                const batch = document.querySelector('input[name="pageSize"]');
                if (batch) {
                    const b = document.createElement('input');
                    b.type = 'hidden';
                    b.name = 'apply_configuration[batch]';
                    b.value = batch.value;
                    form.appendChild(b);
                }

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

        Pusher.logToConsole = false; // Set ke false agar konsol browser tetap bersih

        // Inisialisasi koneksi Pusher / Reverb Websocket
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

        channel.bind("JobStatus", (data) => {
            Livewire.dispatch('job-status-updated', {
                data: {
                    status: data.status,
                    provider: data.provider,
                    jobId: data.job.id,
                    jobTitle: data.job.title,
                    jobCompany: data.job.company
                }
            });

            if (typeof fetchStatus === 'function') {
                fetchStatus();
            }
            if (typeof fetchApplicationStats === 'function') {
                fetchApplicationStats();
            }
        });
    });
</script>
