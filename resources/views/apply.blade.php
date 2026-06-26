@extends('layouts.app')

@section('title', 'Apply · Compass')

@section('content')
    @if (session('success'))
        <div class="mb-6 rounded-md border border-green-800 bg-[#0f2a1c] p-4 text-sm text-green-400 flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $jobstreet_profile = null;
        $glints_profile = null;

        $hasGlints = isset($accounts['glints']) && isset($adapters['glints']);
        $hasJobstreet = isset($accounts['jobstreet']) && isset($adapters['jobstreet']);

        if ($hasJobstreet) {
            $jobstreet_profile = $adapters['jobstreet']->loadProfile();
            $jobstreet_config = $accounts['jobstreet']->getConfig() ?? [];
        }
        if ($hasGlints) {
            $glints_profile = $adapters['glints']->loadProfile();
            $glints_config = $accounts['glints']->getConfig() ?? [];
        }
    @endphp

    <div class="max-w-8xl py-10 px-4">

        {{-- ERROR --}}
        @if ($errors->any())
            <div class="mb-6 rounded-md border border-red-800 bg-[#2a1215] p-4 text-sm text-red-400">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- MAIN GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- AUTO APPLY --}}
            <div class="lg:col-span-1 bg-[#161b22] border border-[#30363d] rounded-md flex flex-col">
                <div class="px-6 py-4 border-b border-[#30363d]">
                    <h2 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                        <i class="fas fa-paper-plane text-[#8b949e] w-4"></i>
                        Auto Apply
                    </h2>
                    <p class="text-xs text-[#8b949e] mt-0.5">Configure and start automated applications</p>
                </div>
                <div class="p-6 flex-1">
                    <form method="POST" action="{{ route('apply.start') }}" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-3 gap-3">
{{--                            <div>--}}
{{--                                <label class="block text-xs font-medium text-[#8b949e] mb-1">Interval (s)</label>--}}
{{--                                <input type="number" name="interval" value="5" min="1"--}}
{{--                                       class="w-full rounded-md border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm text-[#e6edf3] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">--}}
{{--                            </div>--}}
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-[#8b949e] mb-1">Keyword</label>
                                <input type="text" name="keyword" required placeholder="Web Developer"
                                       class="w-full rounded-md border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm text-[#e6edf3] placeholder-[#6e7681] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
                            </div>
                            {{-- Pesan Peringatan PageSize JobStreet --}}

                            <div class="col-span-1">
                                <label class="block text-xs font-medium text-[#8b949e] mb-1">Per Batch (Maks. 40)</label>
                                <input type="number" name="pageSize" value="5" min="1" max="40"
                                       class="w-full rounded-md border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm text-[#e6edf3] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
                            </div>
{{--                            <div>--}}
{{--                                <label class="block text-xs font-medium text-[#8b949e] mb-1">Max Apply</label>--}}
{{--                                <input type="number" name="max_applications" value="10" min="1"--}}
{{--                                       class="w-full rounded-md border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm text-[#e6edf3] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">--}}
{{--                            </div>--}}
                        </div>
                        <p class="mt-2 text-[11px] text-yellow-500 italic">
                            * Dalam satu provider, maksimal 20 job / 10 menit, jika lebih dari itu maka sisanya akan masuk ke batch berikutnya
                        </p>
                        <div class="border border-[#30363d] rounded-md p-4 bg-[#0d1117] space-y-4">
                            {{-- JobStreet --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <label for="provider_jobstreet" class="relative inline-flex cursor-pointer items-center {{ $hasJobstreet ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        <input type="checkbox"
                                               name="providers[]"
                                               value="jobstreet"
                                               id="provider_jobstreet"
                                               class="peer sr-only"
                                            {{ $hasJobstreet ? '' : 'disabled' }}
                                            {{ $hasJobstreet ? 'checked' : '' }}>
                                        <div class="h-6 w-11 rounded-full bg-[#30363d] transition-colors
                            after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5
                            after:rounded-full after:border after:border-gray-300 after:bg-white
                            after:transition-all after:content-['']
                            peer-checked:bg-[#238636] peer-checked:after:translate-x-full
                            peer-checked:after:border-white focus:outline-none">
                                        </div>
                                    </label>
                                    <span class="text-sm font-medium text-[#e6edf3]">JobStreet</span>
                                </div>
                                @if(!$hasJobstreet)
                                    <span class="text-xs px-2 py-0.5 rounded border border-yellow-700 text-yellow-400 bg-[#2d1b00]">Not connected</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded border border-green-700 text-green-400 bg-[#0f2a1c]">Ready</span>
                                @endif
                            </div>

                            {{-- Glints --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <label for="provider_glints" class="relative inline-flex cursor-pointer items-center {{ $hasGlints ? '' : 'opacity-50 cursor-not-allowed' }}">
                                        <input type="checkbox"
                                               name="providers[]"
                                               value="glints"
                                               id="provider_glints"
                                               class="peer sr-only"
                                            {{ $hasGlints ? '' : 'disabled' }}
                                            {{ $hasGlints ? 'checked' : '' }}>
                                        <div class="h-6 w-11 rounded-full bg-[#30363d] transition-colors
                            after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5
                            after:rounded-full after:border after:border-gray-300 after:bg-white
                            after:transition-all after:content-['']
                            peer-checked:bg-[#238636] peer-checked:after:translate-x-full
                            peer-checked:after:border-white focus:outline-none">
                                        </div>
                                    </label>
                                    <span class="text-sm font-medium text-[#e6edf3]">Glints</span>
                                </div>
                                @if(!$hasGlints)
                                    <span class="text-xs px-2 py-0.5 rounded border border-yellow-700 text-yellow-400 bg-[#2d1b00]">Not connected</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded border border-green-700 text-green-400 bg-[#0f2a1c]">Ready</span>
                                @endif
                            </div>
                        </div>
                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-[#238636] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#2ea043] transition w-full">
                            Start Apply
                        </button>
                    </form>
                </div>
            </div>

            {{-- JOBSTREET CONFIG --}}
            <div class="bg-[#161b22] border border-[#30363d] rounded-md flex flex-col">
                <div class="px-6 py-4 border-b border-[#30363d]">
                    <h2 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                        <i class="fas fa-briefcase text-[#8b949e] w-4"></i>
                        JobStreet
                    </h2>
                    <p class="text-xs text-[#8b949e] mt-0.5">Platform configuration</p>
                </div>
                <div class="p-6 flex-1">
                    @if (!$hasJobstreet)
                        <div class="mt-2 text-sm text-yellow-400">
                            JobStreet is not connected.
                            <a href="{{ route('profile') }}" class="text-blue-400 hover:text-blue-300 underline font-medium">
                                Klik disini
                            </a> untuk menghubungkan akun Anda.
                        </div>
                    @else
                        <form action="{{ route('platform.save-config', ['provider' => 'jobstreet']) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-[#8b949e] mb-2">Auto-answer</label>
                                <div class="flex items-center">
                                    <label for="auto_answer_jobstreet" class="relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" name="auto_answer" id="auto_answer_jobstreet" value="1"
                                               class="peer sr-only" {{ ($jobstreet_config['auto_answer'] ?? false) ? 'checked' : '' }}>

                                        <div class="h-6 w-11 rounded-full bg-[#30363d] transition-colors after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#238636] peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                                        <span id="status-text_jobstreet" class="ml-3 text-sm font-medium transition-colors {{ ($jobstreet_config['auto_answer'] ?? false) ? 'text-[#238636]' : 'text-[#8b949e]' }}">
                {{ ($jobstreet_config['auto_answer'] ?? false) ? 'Enabled' : 'Disabled' }}
            </span>
                                    </label>
                                </div>
                                {{-- Pesan Peringatan JobStreet --}}
                                <p class="mt-2 text-[11px] text-yellow-500 italic">
                                    * Dengan mengaktifkan ini, pertanyaan screening akan dijawab secara random (Tidak Direkomendasikan).
                                </p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#8b949e] mb-1">Resume</label>
                                <select name="resume" class="w-full rounded-md border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm text-[#e6edf3] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
                                    @if(isset($jobstreet_profile['resumes']))
                                        @php $selected_resume = $jobstreet_config['resume'] ?? null; @endphp
                                        <option value="">Select Resume</option>
                                        @foreach ($jobstreet_profile['resumes'] as $resume)
                                            <option value="{{ $resume['id'] }}" {{ $selected_resume == $resume['id'] ? 'selected' : '' }}>
                                                {{ $resume['fileMetadata']['name'] }} @if($selected_resume == $resume['id']) (Selected) @endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No resumes found</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#8b949e] mb-1">Role</label>
                                <select name="role" class="w-full rounded-md border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm text-[#e6edf3] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
                                    @if(isset($jobstreet_profile['roles']))
                                        @php $selected_role = $jobstreet_config['role'] ?? null; @endphp
                                        <option value="">Select Role</option>
                                        @foreach ($jobstreet_profile['roles'] as $role)
                                            <option value="{{ $role['id'] }}" {{ $selected_role == $role['id'] ? 'selected' : '' }}>
                                                {{ $role['title']['text'] }} @if($selected_role == $role['id']) (Selected) @endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No roles found</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-[#8b949e] mb-1">Location</label>
                                <input type="text" name="location" placeholder="Banten" value="{{ old('location', $jobstreet_config['location'] ?? '') }}" class="w-full rounded-md border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm text-[#e6edf3] placeholder-[#6e7681] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-[#238636] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#2ea043] transition w-full">
                                Save Configuration
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- GLINTS CONFIG --}}
            <div class="bg-[#161b22] border border-[#30363d] rounded-md flex flex-col">
                <div class="px-6 py-4 border-b border-[#30363d]">
                    <h2 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                        <i class="fas fa-globe text-[#8b949e] w-4"></i>
                        Glints
                    </h2>
                    <p class="text-xs text-[#8b949e] mt-0.5">Platform configuration</p>
                </div>
                <div class="p-6 flex-1">
                    @if (!$hasGlints)
                        <p class="text-sm text-yellow-400">
                            Not connected <a class="text-blue-400" href="{{ route('profile') }}">Klik disini</a> untuk menghubungkan
                        </p>
                    @else
                        <form action="{{ route('platform.save-config', ['provider' => 'glints']) }}" method="POST" class="space-y-4">
                            @csrf
                            {{-- Glints Auto-answer Toggle (Disabled) --}}
                            <div>
                                <label class="block text-xs font-medium text-[#8b949e] mb-2">Auto-answer</label>
                                <div class="flex items-center opacity-60 cursor-not-allowed">
                                    <label for="auto_answer_glints" class="relative inline-flex cursor-not-allowed items-center">
                                        <input type="checkbox" name="auto_answer" id="auto_answer_glints" value="1"
                                               class="peer sr-only" disabled {{ ($glints_config['auto_answer'] ?? false) ? 'checked' : '' }}>

                                        <div class="h-6 w-11 rounded-full bg-[#30363d] transition-colors after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#30363d] peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
                                        <span class="ml-3 text-sm font-medium text-[#8b949e]">
                Disabled
            </span>
                                    </label>
                                </div>
                            </div>
                            <div class="relative w-full">
                                <label class="block text-xs font-medium text-[#8b949e] mb-1">Locations</label>
                                <div id="location-tags-container" class="flex flex-wrap gap-2 mb-2"></div>
                                <div class="relative">
                                    <input type="text" id="location-search-input" placeholder="Type location…" autocomplete="off"
                                           class="w-full rounded-md border border-[#30363d] bg-[#0d1117] px-3 py-2 text-sm text-[#e6edf3] placeholder-[#6e7681] focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
                                    <div id="location-loading" class="absolute right-3 top-2.5 hidden">
                                        <i class="fas fa-spinner fa-spin text-[#8b949e]"></i>
                                    </div>
                                </div>
                                <div id="location-hidden-inputs"></div>
                                <div id="glints-initial-locations"
                                     data-ids="{{ json_encode(old('location_ids', $glints_config['location_ids'] ?? [])) }}"
                                     data-names="{{ json_encode(old('location_names', $glints_config['location_names'] ?? [])) }}"
                                     class="hidden"></div>
                                <ul id="location-results" class="absolute z-50 w-full mt-1 max-h-48 overflow-y-auto rounded-md border border-[#30363d] bg-[#161b22] py-1 text-sm shadow-xl hidden custom-scrollbar"></ul>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-[#238636] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#2ea043] transition w-full">
                                Save Configuration
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <p class="mt-6 text-xs text-[#8b949e] text-center">Keep your connection stable while automation is running.</p>

        {{-- QUEUE STATUS --}}
        <div class="mt-8 bg-[#161b22] border border-[#30363d] rounded-md">
            <div class="px-6 py-4 border-b border-[#30363d] flex justify-between items-center flex-wrap gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                        <i class="fas fa-tasks text-[#8b949e] w-4"></i>
                        Queue Status
                    </h2>
                    <p class="text-xs text-[#8b949e] mt-0.5">Real-time status of your job queue</p>
                </div>
                <div class="flex items-center gap-4">
                    <div id="queue-status-badge" class="flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full bg-gray-500" id="queue-status-dot"></span>
                        <span class="text-xs font-medium text-[#e6edf3]" id="queue-status-text">Loading...</span>
                    </div>
                    <span id="last-updated" class="text-xs text-[#8b949e]">--</span>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-[#0d1117] border border-[#30363d] rounded-md p-4 text-center">
                        <div class="text-xs text-[#8b949e] uppercase tracking-wide">Pending</div>
                        <div id="pending-count" class="text-2xl font-bold text-[#e6edf3]">0</div>
                    </div>
                    <div class="bg-[#0d1117] border border-[#30363d] rounded-md p-4 text-center">
                        <div class="text-xs text-[#8b949e] uppercase tracking-wide">Processing</div>
                        <div id="processing-count" class="text-2xl font-bold text-yellow-400">0</div>
                    </div>
                    <div class="bg-[#0d1117] border border-[#30363d] rounded-md p-4 text-center">
                        <div class="text-xs text-[#8b949e] uppercase tracking-wide">Failed</div>
                        <div id="failed-count" class="text-2xl font-bold text-red-400">0</div>
                    </div>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-[#8b949e] uppercase tracking-wide mb-3">Recent Jobs (Last 5)</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-[#e6edf3]">
                            <thead class="text-xs uppercase text-[#8b949e] border-b border-[#30363d]">
                            <tr>
                                <th class="px-4 py-2">Job ID</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Attempts</th>
                                <th class="px-4 py-2">Created</th>
                            </tr>
                            </thead>
                            <tbody id="recent-jobs-table">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-[#8b949e]">No data yet…</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- APPLICATION STATUS --}}
        <div class="mt-8 bg-[#161b22] border border-[#30363d] rounded-md">
            <div class="px-6 py-4 border-b border-[#30363d] flex justify-between items-center flex-wrap gap-2">
                <div>
                    <h2 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                        <i class="fas fa-file-alt text-[#8b949e] w-4"></i>
                        Application Status (Glints)
                    </h2>
                    <p class="text-xs text-[#8b949e] mt-0.5">Summary of automated applications</p>
                </div>
                <span id="app-last-updated" class="text-xs text-[#8b949e]">--</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-[#0d1117] border border-[#30363d] rounded-md p-3 text-center">
                        <div class="text-xs text-[#8b949e] uppercase tracking-wide">✅ Success</div>
                        <div id="stat-success" class="text-xl font-bold text-green-400">0</div>
                    </div>
                    <div class="bg-[#0d1117] border border-[#30363d] rounded-md p-3 text-center">
                        <div class="text-xs text-[#8b949e] uppercase tracking-wide">📩 Applied</div>
                        <div id="stat-applied" class="text-xl font-bold text-blue-400">0</div>
                    </div>
                    <div class="bg-[#0d1117] border border-[#30363d] rounded-md p-3 text-center">
                        <div class="text-xs text-[#8b949e] uppercase tracking-wide">🔗 Link Out</div>
                        <div id="stat-linkout" class="text-xl font-bold text-yellow-400">0</div>
                    </div>
                    <div class="bg-[#0d1117] border border-[#30363d] rounded-md p-3 text-center">
                        <div class="text-xs text-[#8b949e] uppercase tracking-wide">📝 Screening</div>
                        <div id="stat-questionnaire" class="text-xl font-bold text-purple-400">0</div>
                    </div>
                </div>
                <div>
                    <h4 class="text-xs font-medium text-[#8b949e] uppercase tracking-wide mb-3">Latest Applications (Last 5)</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-[#e6edf3]">
                            <thead class="text-xs uppercase text-[#8b949e] border-b border-[#30363d]">
                            <tr>
                                <th class="px-4 py-2">Job ID</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Updated</th>
                            </tr>
                            </thead>
                            <tbody id="recent-apps-table">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-center text-[#8b949e]">No applications yet.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // Helper: format tanggal (menerima timestamp integer atau string)
    function formatDate(value) {
        if (!value) return 'N/A';
        let date;
        if (typeof value === 'number') {
            // Anggap sebagai UNIX timestamp (detik)
            date = new Date(value * 1000);
        } else if (typeof value === 'string') {
            date = new Date(value);
        } else {
            return 'N/A';
        }
        if (isNaN(date.getTime())) return 'N/A';
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    // Update status badge queue
    function updateQueueStatusBadge(status) {
        const dot = document.getElementById('queue-status-dot');
        const text = document.getElementById('queue-status-text');
        if (!dot || !text) return;
        const map = {
            'running': { color: 'bg-green-500', label: 'Running' },
            'idle': { color: 'bg-yellow-500', label: 'Idle' },
            'stopped': { color: 'bg-red-500', label: 'Stopped' },
            'default': { color: 'bg-gray-500', label: 'Unknown' }
        };
        const s = map[status] || map.default;
        dot.className = `inline-block w-2.5 h-2.5 rounded-full ${s.color}`;
        text.textContent = s.label;
    }

    // Fetch queue status
    function fetchQueueStatus() {
        fetch('{{ route("user.queue.status") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                const pending = data.pending || 0;
                const processing = data.processing || 0;
                const failed = data.failed || 0;

                document.getElementById('pending-count').textContent = pending;
                document.getElementById('processing-count').textContent = processing;
                document.getElementById('failed-count').textContent = failed;

                // Status queue
                const queueStatus = (pending > 0 || processing > 0) ? 'running' : 'idle';
                updateQueueStatusBadge(queueStatus);

                // Tabel recent jobs
                const tbody = document.getElementById('recent-jobs-table');
                tbody.innerHTML = '';
                if (data.recent && data.recent.length > 0) {
                    data.recent.forEach(job => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b border-[#30363d] last:border-0';

                        const isProcessing = job.reserved_at !== null;
                        const statusClass = isProcessing
                            ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500'
                            : 'bg-blue-500/20 text-blue-400 border-blue-500';
                        const statusText = isProcessing ? 'Processing' : 'Pending';

                        tr.innerHTML = `
                        <td class="px-4 py-2 font-mono text-xs">#${job.id}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${statusClass}">
                                ${statusText}
                            </span>
                        </td>
                        <td class="px-4 py-2">${job.attempts}</td>
                        <td class="px-4 py-2 text-xs text-[#8b949e]">${formatDate(job.created_at)}</td>
                    `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-[#8b949e]">No jobs in queue.</td></tr>';
                }

                document.getElementById('last-updated').textContent = `Updated: ${formatDate(data.updated_at)}`;
            })
            .catch(error => {
                console.error('Error fetching queue status:', error);
                document.getElementById('recent-jobs-table').innerHTML =
                    '<tr><td colspan="4" class="px-4 py-3 text-center text-red-400">Failed to load data.</td></tr>';
                updateQueueStatusBadge('stopped');
            });
    }

    // Fetch application status (tidak berubah)
    function fetchApplicationStatus() {
        fetch('{{ route("user.jobs.status") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                document.getElementById('stat-success').textContent = data.stats.success || 0;
                document.getElementById('stat-applied').textContent = data.stats.applied || 0;
                document.getElementById('stat-linkout').textContent = data.stats.linkout || 0;
                document.getElementById('stat-questionnaire').textContent = data.stats.questionnaire || 0;

                const tbody = document.getElementById('recent-apps-table');
                tbody.innerHTML = '';
                if (data.recent && data.recent.length > 0) {
                    data.recent.forEach(app => {
                        const map = {
                            'success': { label: 'Success', class: 'bg-green-500/20 text-green-400 border-green-500' },
                            'applied': { label: 'Applied', class: 'bg-blue-500/20 text-blue-400 border-blue-500' },
                            'linkout': { label: 'Link Out', class: 'bg-yellow-500/20 text-yellow-400 border-yellow-500' },
                            'questionnaire': { label: 'Screening', class: 'bg-purple-500/20 text-purple-400 border-purple-500' }
                        };
                        const status = map[app.status] || { label: app.status, class: 'bg-gray-500/20 text-gray-400 border-gray-500' };
                        const tr = document.createElement('tr');
                        tr.className = 'border-b border-[#30363d] last:border-0';
                        const jobIdShort = app.job_id ? app.job_id.substring(0, 8) + '…' : 'Unknown';
                        tr.innerHTML = `
                        <td class="px-4 py-2 font-mono text-xs">${jobIdShort}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${status.class}">
                                ${status.label}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-xs text-[#8b949e]">${formatDate(app.updated_at)}</td>
                    `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="3" class="px-4 py-3 text-center text-[#8b949e]">No applications yet.</td></tr>';
                }
                document.getElementById('app-last-updated').textContent = `Updated: ${formatDate(data.updated_at)}`;
            })
            .catch(error => {
                console.error('Error fetching application status:', error);
                document.getElementById('recent-apps-table').innerHTML =
                    '<tr><td colspan="3" class="px-4 py-3 text-center text-red-400">Failed to load data.</td></tr>';
            });
    }

    // Location autocomplete (sama seperti sebelumnya, tidak diubah)
    function initLocationAutocomplete() {
        const els = {
            input: document.getElementById('location-search-input'),
            results: document.getElementById('location-results'),
            tagsContainer: document.getElementById('location-tags-container'),
            hiddenInputs: document.getElementById('location-hidden-inputs'),
            loading: document.getElementById('location-loading'),
            initialData: document.getElementById('glints-initial-locations')
        };
        if (!els.input) return;

        let selectedLocations = [];
        try {
            const ids = JSON.parse(els.initialData.dataset.ids || '[]');
            const names = JSON.parse(els.initialData.dataset.names || '[]');
            if (Array.isArray(ids) && Array.isArray(names)) {
                ids.forEach((id, index) => {
                    if (names[index]) {
                        selectedLocations.push({ id: id, name: names[index] });
                    }
                });
            }
        } catch (e) {
            console.error("Failed to parse initial locations", e);
        }

        const renderTags = () => {
            els.tagsContainer.innerHTML = '';
            els.hiddenInputs.innerHTML = '';
            selectedLocations.forEach((loc, index) => {
                const tag = document.createElement('div');
                tag.className = 'flex items-center gap-1 bg-[#1f6feb] border border-[#388bfd] text-white px-2 py-1 rounded text-xs font-medium';
                const textSpan = document.createElement('span');
                textSpan.textContent = loc.name;
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'ml-1 hover:text-red-300 focus:outline-none font-bold';
                removeBtn.innerHTML = '&times;';
                removeBtn.onclick = function(e) {
                    e.preventDefault();
                    selectedLocations.splice(index, 1);
                    renderTags();
                };
                tag.appendChild(textSpan);
                tag.appendChild(removeBtn);
                els.tagsContainer.appendChild(tag);

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'location_ids[]';
                inputId.value = loc.id;
                const inputName = document.createElement('input');
                inputName.type = 'hidden';
                inputName.name = 'location_names[]';
                inputName.value = loc.name;
                els.hiddenInputs.appendChild(inputId);
                els.hiddenInputs.appendChild(inputName);
            });
        };
        renderTags();

        const debounce = (func, delay) => {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => func(...args), delay);
            };
        };

        const fetchLocationData = async (keyword) => {
            const formData = new FormData();
            formData.append('keyword', keyword);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            const response = await fetch('{{ route("api.search.location", ["provider" => "glints"]) }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Network response was not ok');
            return await response.json();
        };

        const renderResults = (list) => {
            els.results.innerHTML = '';
            if (!list || list.length === 0) {
                els.results.innerHTML = '<li class="px-3 py-2 text-[#8b949e] italic">Location not found</li>';
                els.results.classList.remove('hidden');
                return;
            }
            let renderedCount = 0;
            list.forEach(item => {
                if (selectedLocations.some(loc => loc.id === item.id)) return;
                const li = document.createElement('li');
                li.className = 'cursor-pointer px-3 py-2 text-[#e6edf3] hover:bg-[#1f242c] transition-colors border-b border-[#30363d] last:border-0';
                let fullName = item.name;
                if (item.parents && item.parents.length > 0) {
                    const parentNames = item.parents.map(p => p.name).join(', ');
                    fullName += `<span class="text-xs text-[#8b949e] block mt-0.5">${parentNames}</span>`;
                }
                li.innerHTML = fullName;
                li.addEventListener('click', () => {
                    selectedLocations.push({ id: item.id, name: item.name });
                    renderTags();
                    els.input.value = '';
                    els.results.classList.add('hidden');
                    els.input.focus();
                });
                els.results.appendChild(li);
                renderedCount++;
            });
            if (renderedCount === 0) {
                els.results.innerHTML = '<li class="px-3 py-2 text-[#8b949e] italic">All locations already selected</li>';
            }
            els.results.classList.remove('hidden');
        };

        const handleInput = async (e) => {
            const keyword = e.target.value.trim();
            if (keyword.length < 2) {
                els.results.classList.add('hidden');
                els.results.innerHTML = '';
                return;
            }
            els.loading.classList.remove('hidden');
            try {
                const data = await fetchLocationData(keyword);
                let list = [];
                if (Array.isArray(data.data?.searchHierarchicalLocations)) {
                    list = data.data.searchHierarchicalLocations;
                } else if (data.data?.searchHierarchicalLocations?.list) {
                    list = data.data.searchHierarchicalLocations.list;
                } else if (Array.isArray(data.list)) {
                    list = data.list;
                }
                renderResults(list);
            } catch (error) {
                console.error('Error fetching location:', error);
                els.results.innerHTML = '<li class="px-3 py-2 text-red-400">Failed to load data</li>';
                els.results.classList.remove('hidden');
            } finally {
                els.loading.classList.add('hidden');
            }
        };

        els.input.addEventListener('input', debounce(handleInput, 500));
        document.addEventListener('click', (e) => {
            if (!els.input.contains(e.target) && !els.results.contains(e.target)) {
                els.results.classList.add('hidden');
            }
        });
    }

    // DOM ready
    document.addEventListener("DOMContentLoaded", function() {
        // Toggle text
        const cbJS = document.getElementById('auto_answer_jobstreet');
        const stJS = document.getElementById('status-text_jobstreet');
        if (cbJS && stJS) {
            cbJS.addEventListener('change', function() {
                stJS.textContent = this.checked ? 'Enabled' : 'Disabled';
                stJS.classList.toggle('text-[#238636]', this.checked);
                stJS.classList.toggle('text-[#8b949e]', !this.checked);
            });
        }
        const cbGlints = document.getElementById('auto_answer_glints');
        const stGlints = document.getElementById('status-text_glints');
        if (cbGlints && stGlints) {
            cbGlints.addEventListener('change', function() {
                stGlints.textContent = this.checked ? 'Enabled' : 'Disabled';
                stGlints.classList.toggle('text-[#238636]', this.checked);
                stGlints.classList.toggle('text-[#8b949e]', !this.checked);
            });
        }

        fetchQueueStatus();
        fetchApplicationStatus();
        setInterval(fetchQueueStatus, 5000);
        setInterval(fetchApplicationStatus, 5000);

        initLocationAutocomplete();
    });
</script>
