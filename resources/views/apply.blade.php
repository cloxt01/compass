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
    <div class="flex flex-col gap-6">
        <livewire:stats-overview />

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-3 ">

            <div class="lg:col-span-2 gap-6">
                <livewire:panel-configuration :accounts="$accounts" :adapters="$adapters" />
            </div>

            <div class="lg:col-span-1">
                <livewire:live-monitoring />
            </div>
        </section>

        <section class="grid grid-cols-1">
            <div class="saas-card p-6">
                <livewire:activity-timeline />
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        body { background:#0A0A0A; color:#FAFAFA; }
        .saas-card { background:#111111; border:1px solid #262626; border-radius:16px; }
        .custom-scroll::-webkit-scrollbar { width:6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background:#262626; border-radius:9999px; }
    </style>
@endpush

<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Pusher
        var pusher = new Pusher("{{ env('REVERB_APP_KEY') }}", {
            cluster: "",
            wsHost: "{{ env('REVERB_HOST', '127.0.0.1') }}",
            wsPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            enabledTransports: ['ws'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            }
        });

        var channel = pusher.subscribe("private-users.{{ auth()->id() }}");

        channel.bind("JobStatus", (incomingEvent) => {
            // Update Timeline & Monitoring
            Livewire.dispatch('job-status-updated', {
                payload: {
                    data: {
                        status: incomingEvent.status,
                        provider: incomingEvent.provider,
                        jobId: incomingEvent.data?.job?.id || null,
                        jobTitle: incomingEvent.data?.job?.title || null,
                        jobCompany: incomingEvent.data?.job?.company || null
                    }
                }
            });

            // Update Stats Overview
            Livewire.dispatch('queue-status-refreshed', {
                pending: incomingEvent.pending || 0
            });
        });
    });
</script>



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
        .provider-toggle[aria-expanded="true"] .fa-chevron-down { transform: rotate(180deg); }
    </style>
@endpush

<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const saveBtn = document.getElementById('save-btn');
        const stopBtn = document.getElementById('stop-btn');
        const resumeBtn = document.getElementById('resume-btn');
        const keywordInput = document.getElementById('keyword-input');

        function getSelectedProviders() {
            return Array.from(document.querySelectorAll('.provider-checkbox:checked')).map(cb => cb.value);
        }

        // --- Aksi Tombol (Save, Stop, Resume) ---
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

        // --- Inisialisasi Autocomplete Lokasi ---
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
        initLocationAutocomplete();

        const cb = document.getElementById('auto_answer_jobstreet');
        const st = document.getElementById('status-text-jobstreet');
        if (cb && st) {
            cb.addEventListener('change', function() {
                st.textContent = this.checked ? 'On' : 'Off';
                st.className = `text-xs font-medium transition-colors ${this.checked ? 'text-blue-400' : 'text-[#8b949e]'}`;
            });
        }

        // --- Inisialisasi Realtime WebSocket (Pusher / Reverb) ---
        Pusher.logToConsole = false;

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

        channel.bind("JobStatus", (incomingEvent) => {
            Livewire.dispatch('job-status-updated', {
                payload: {
                    data: {
                        status: incomingEvent.status,
                        provider: incomingEvent.provider,
                        jobId: incomingEvent.data?.job?.id || null,
                        jobTitle: incomingEvent.data?.job?.title || null,
                        jobCompany: incomingEvent.data?.job?.company || null
                    }
                }
            });



            Livewire.dispatch('queue-status-refreshed', {
                pending: data.pending || 0
            });
        });
    });
</script>
