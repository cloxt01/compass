@extends('layouts.app')

@section('title', 'Apply · Compass')

@section('content')
    @if (session('success'))
        <div class="mb-4 rounded-md border border-green-800 bg-[#0f2a1c] p-4 text-sm text-green-400 flex items-center gap-2">
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

        {{-- CARD --}}
        <div class="flex flex-col md:flex-row gap-6">

            {{-- AUTO APPLY CONFIG --}}
            <div class="flex-1 bg-[#161b22] border border-[#30363d] rounded-md">

                {{-- HEADER --}}
                <div class="px-6 py-4 border-b border-[#30363d]">
                    <h1 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                        <i class="fas fa-paper-plane text-[#8b949e]"></i>
                        Auto Apply Configuration
                    </h1>
                    <p class="text-xs text-[#8b949e] mt-1">
                        Configure how Compass submits applications automatically.
                    </p>
                </div>

                {{-- BODY --}}
                <div class="p-6">
                    <form method="POST" action="{{ route('apply.start') }}" class="space-y-6">
                        @csrf

                        {{-- SEARCH --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-[#8b949e] mb-1">
                                    Keyword
                                </label>
                                <input
                                    type="text"
                                    name="keyword"
                                    required
                                    placeholder="Web Developer"
                                    class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                    px-3 py-2 text-sm text-[#e6edf3]
                                    placeholder-[#6e7681]
                                    focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-[#8b949e] mb-1">
                                    Location
                                </label>
                                <input
                                    type="text"
                                    name="location"
                                    placeholder="Jakarta"
                                    class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                    px-3 py-2 text-sm text-[#e6edf3]
                                    placeholder-[#6e7681]
                                    focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]"
                                >
                            </div>
                        </div>

                        {{-- LIMITS --}}
                        <div class="grid grid-cols-3 gap-4">
                            @foreach ([
                                ['Interval (sec)', 'interval', 5],
                                ['Per Batch', 'pageSize', 5],
                                ['Max Apply', 'max_applications', 10],
                            ] as [$label, $name, $value])
                                <div>
                                    <label class="block text-xs font-medium text-[#8b949e] mb-1">
                                        {{ $label }}
                                    </label>
                                    <input
                                        type="number"
                                        name="{{ $name }}"
                                        value="{{ $value }}"
                                        min="1"
                                        class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                        px-3 py-2 text-sm text-[#e6edf3]
                                        focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]"
                                    >
                                </div>
                            @endforeach
                        </div>

                        {{-- PROVIDER --}}
                        <div class="border border-[#30363d] rounded-md p-4 bg-[#0d1117]">
                            <label class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        name="providers[]"
                                        value="jobstreet"
                                        class="h-4 w-4 rounded border-[#30363d] bg-[#0d1117] text-[#238636]
                                        focus:ring-[#238636]"
                                        {{ $hasJobstreet ? '' : 'disabled' }}
                                    >
                                    <span class="text-sm font-medium text-[#e6edf3]">
                                    JobStreet
                                </span>
                                </div>

                                @if(!$hasJobstreet)
                                    <span class="text-xs px-2 py-0.5 rounded border border-yellow-700
                                            text-yellow-400 bg-[#2d1b00]">
                                    Not connected
                                </span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded border border-green-700
                                            text-green-400 bg-[#0f2a1c]">
                                    Ready
                                </span>
                                @endif
                                <div class="flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        name="providers[]"
                                        value="glints"
                                        class="h-4 w-4 rounded border-[#30363d] bg-[#0d1117] text-[#238636]
                                        focus:ring-[#238636]"
                                        {{ $hasGlints ? '' : 'disabled' }}
                                    >
                                    <span class="text-sm font-medium text-[#e6edf3]">
                                    Glints
                                </span>
                                </div>

                                @if(!$hasGlints)
                                    <span class="text-xs px-2 py-0.5 rounded border border-yellow-700
                                            text-yellow-400 bg-[#2d1b00]">
                                    Not connected
                                </span>
                                @else
                                    <span class="text-xs px-2 py-0.5 rounded border border-green-700
                                            text-green-400 bg-[#0f2a1c]">
                                    Ready
                                </span>
                                @endif
                            </label>
                        </div>

                        {{-- SUBMIT --}}
                        <div class="pt-2">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-md
                                bg-[#238636] px-5 py-2.5
                                text-sm font-semibold text-white
                                hover:bg-[#2ea043] transition">
                                Start Apply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PLATFORM CONFIGURATION --}}

            {{-- JOBSTREET CONFIG --}}
            <div class="w-full md:w-128 bg-[#161b22] border border-[#30363d] rounded-md">
                {{-- HEADER --}}
                <div class="px-6 py-4 border-b border-[#30363d]">
                    <h1 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                        <i class="fas fa-paper-plane text-[#8b949e]"></i>
                        Jobstreet Configuration
                    </h1>
                    <p class="text-xs text-[#8b949e] mt-1">
                        Konfigurasi platform untuk auto apply. Pastikan platform sudah terhubung di halaman profile.
                    </p>
                </div>
                {{-- BODY --}}
                <div class="p-6">
                    @if (!$hasJobstreet)
                        <p class="text-sm text-yellow-400">
                            JobStreet is not connected. Please connect your JobStreet account in the profile page.
                        </p>
                    @else
                        <form action="{{ route('platform.save-config', ['provider' => 'jobstreet']) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-[#8b949e] mb-2">
                                            Auto-answer
                                        </label>
                                        <div class="flex items-center">
                                            <label for="auto_answer_jobstreet" class="relative inline-flex cursor-pointer items-center">
                                                <input type="checkbox"
                                                       name="auto_answer"
                                                       id="auto_answer_jobstreet"
                                                       value="1"
                                                       class="peer sr-only"
                                                    {{ ($jobstreet_config['auto_answer'] ?? false) ? 'checked' : '' }}>

                                                {{-- Toggle Switch --}}
                                                <div class="h-6 w-11 rounded-full bg-[#30363d] transition-colors
                                                        after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5
                                                        after:rounded-full after:border after:border-gray-300 after:bg-white
                                                        after:transition-all after:content-['']
                                                        peer-checked:bg-[#238636] peer-checked:after:translate-x-full
                                                        peer-checked:after:border-white focus:outline-none">
                                                </div>

                                                {{-- Teks yang akan berubah --}}
                                                <span id="status-text_jobstreet" class="ml-3 text-sm font-medium transition-colors {{ ($jobstreet_config['auto_answer'] ?? false) ? 'text-[#238636]' : 'text-[#8b949e]' }}">
                                                {{ ($jobstreet_config['auto_answer'] ?? false) ? 'Enabled' : 'Disabled' }}
                                            </span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- ROLES SELECTION --}}
                                    <div>
                                        <label class="block text-xs font-medium text-[#8b949e] mb-1">
                                            Resume
                                        </label>
                                        <select name="resume" class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                        px-3 py-2 text-sm text-[#e6edf3]
                                        focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
                                            @if(isset($jobstreet_profile['resumes']))
                                                @php
                                                    $selected_resume = $jobstreet_config['resume'] ?? null;
                                                @endphp

                                                <option value="">Select Resume</option>

                                                @foreach ($jobstreet_profile['resumes'] as $resume)
                                                    <option value="{{ $resume['id'] }}" {{ $selected_resume == $resume['id'] ? 'selected' : '' }}>{{ $resume['fileMetadata']['name']}}

                                                        @if($selected_resume == $resume['id']) (Selected) @endif
                                                        @if($loop->first)
                                                            (Terbaru)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">No resumes found</option>
                                            @endif

                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-[#8b949e] mb-1">
                                            Roles
                                        </label>
                                        <select name="role" class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                        px-3 py-2 text-sm text-[#e6edf3]
                                        focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]">
                                            @if(isset($jobstreet_profile['roles']))

                                                @php
                                                    $selected_role = $jobstreet_config['role'] ?? null;
                                                @endphp

                                                <option value="">Select Role</option>

                                                @foreach ($jobstreet_profile['roles'] as $role)
                                                    <option value="{{ $role['id'] }}" {{ $selected_role == $role['id'] ? 'selected' : '' }}>{{ $role['title']['text']}}

                                                        @if($selected_role == $role['id']) (Selected) @endif
                                                        @if($loop->first)
                                                            (Terbaru)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="">No roles found</option>
                                            @endif

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-[#238636] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#2ea043] transition">
                                Save Configuration
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- GLINTS CONFIG --}}
            <div class="w-full md:w-128 bg-[#161b22] border border-[#30363d] rounded-md">
                {{-- HEADER --}}
                <div class="px-6 py-4 border-b border-[#30363d]">
                    <h1 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                        <i class="fas fa-paper-plane text-[#8b949e]"></i>
                        Glints Configuration
                    </h1>
                    <p class="text-xs text-[#8b949e] mt-1">
                        Konfigurasi platform untuk auto apply. Pastikan platform sudah terhubung di halaman profile.
                    </p>
                </div>
                {{-- BODY --}}
                <div class="p-6">
                    @if (!$hasGlints)
                        <p class="text-sm text-yellow-400">
                            Glints is not connected. Please connect your JobStreet account in the profile page.
                        </p>
                    @else
                        <form action="{{ route('platform.save-config', ['provider' => 'glints']) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <div class="grid grid-cols-1 gap-4">

                                    {{-- AUTO-ANSWER --}}
                                    <div>
                                        <label class="block text-xs font-medium text-[#8b949e] mb-2">
                                            Auto-answer
                                        </label>
                                        <div class="flex items-center">
                                            <label for="auto_answer_glints" class="relative inline-flex cursor-pointer items-center">
                                                <input type="checkbox"
                                                       name="auto_answer"
                                                       id="auto_answer_glints"
                                                       value="1"
                                                       class="peer sr-only"
                                                    {{ ($glints_config['auto_answer'] ?? false) ? 'checked' : '' }}>

                                                {{-- Toggle Switch --}}
                                                <div class="h-6 w-11 rounded-full bg-[#30363d] transition-colors
                                                        after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5
                                                        after:rounded-full after:border after:border-gray-300 after:bg-white
                                                        after:transition-all after:content-['']
                                                        peer-checked:bg-[#238636] peer-checked:after:translate-x-full
                                                        peer-checked:after:border-white focus:outline-none">
                                                </div>

                                                {{-- Teks yang akan berubah --}}
                                                <span id="status-text_glints" class="ml-3 text-sm font-medium transition-colors {{ ($glints_config['auto_answer'] ?? false) ? 'text-[#238636]' : 'text-[#8b949e]' }}">
                                                {{ ($glints_config['auto_answer'] ?? false) ? 'Enabled' : 'Disabled' }}
                                            </span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- AUTOCOMPLETE LOCATION (MULTI SELECT) --}}
                                    <div class="relative w-full">
                                        <label class="block text-xs font-medium text-[#8b949e] mb-2">
                                            Locations (Glints)
                                        </label>

                                        {{-- Tempat Tag Lokasi Terpilih Akan Muncul --}}
                                        <div id="location-tags-container" class="flex flex-wrap gap-2 mb-2"></div>

                                        <div class="relative">
                                            <input
                                                type="text"
                                                id="location-search-input"
                                                placeholder="Ketik lokasi (mis: Jakarta)..."
                                                autocomplete="off"
                                                class="w-full rounded-md border border-[#30363d] bg-[#0d1117]
                                                   px-3 py-2 text-sm text-[#e6edf3]
                                                   placeholder-[#6e7681]
                                                   focus:border-[#58a6ff] focus:ring-1 focus:ring-[#58a6ff]"
                                            >
                                            <div id="location-loading" class="absolute right-3 top-2.5 hidden">
                                                <i class="fas fa-spinner fa-spin text-[#8b949e]"></i>
                                            </div>
                                        </div>

                                        {{-- Container untuk menyimpan input hidden berupa array --}}
                                        <div id="location-hidden-inputs"></div>

                                        {{-- Data awal dari config/old input (Format JSON) agar terbaca oleh JS --}}
                                        <div id="glints-initial-locations"
                                             data-ids="{{ json_encode(old('location_ids', $glints_config['location_ids'] ?? [])) }}"
                                             data-names="{{ json_encode(old('location_names', $glints_config['location_names'] ?? [])) }}"
                                             class="hidden">
                                        </div>

                                        <ul id="location-results" class="absolute z-50 w-full mt-1 max-h-48 overflow-y-auto rounded-md border border-[#30363d] bg-[#161b22] py-1 text-sm shadow-xl hidden custom-scrollbar">
                                        </ul>
                                    </div>

                                </div>
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-[#238636] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#2ea043] transition">
                                Save Configuration
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>


        <p class="mt-4 text-xs text-[#8b949e] text-center">
            Keep your connection stable while automation is running.
        </p>
    </div>
    {{-- QUEUE STATUS --}}
    <div class="mt-8 bg-[#161b22] border border-[#30363d] rounded-md">
        <div class="px-6 py-4 border-b border-[#30363d] flex justify-between items-center">
            <div>
                <h2 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                    <i class="fas fa-tasks text-[#8b949e]"></i>
                    Status Antrian
                </h2>
                <p class="text-xs text-[#8b949e] mt-1">
                    Real-time status lamaran Anda.
                </p>
            </div>
            <span id="last-updated" class="text-xs text-[#8b949e]"></span>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
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
                <h4 class="text-xs font-medium text-[#8b949e] uppercase tracking-wide mb-2">Recent Jobs (Last 5)</h4>
                <ul id="recent-jobs" class="space-y-1 text-sm text-[#e6edf3]">
                    <li class="text-[#8b949e]">Belum ada data...</li>
                </ul>
            </div>
        </div>
    </div>
    {{-- APPLICATION STATUS --}}
    <div class="mt-8 bg-[#161b22] border border-[#30363d] rounded-md">
        <div class="px-6 py-4 border-b border-[#30363d] flex justify-between items-center">
            <div>
                <h2 class="text-sm font-semibold text-[#e6edf3] flex items-center gap-2">
                    <i class="fas fa-file-alt text-[#8b949e]"></i>
                    Status Lamaran (Glints)
                </h2>
                <p class="text-xs text-[#8b949e] mt-1">
                    Ringkasan hasil lamaran otomatis Anda.
                </p>
            </div>
            <span id="app-last-updated" class="text-xs text-[#8b949e]"></span>
        </div>

        <div class="p-6">
            {{-- STATISTIK --}}
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
                    <div class="text-xs text-[#8b949e] uppercase tracking-wide">📝 Questionnaire</div>
                    <div id="stat-questionnaire" class="text-xl font-bold text-purple-400">0</div>
                </div>
            </div>

            {{-- DAFTAR TERBARU --}}
            <div>
                <h4 class="text-xs font-medium text-[#8b949e] uppercase tracking-wide mb-2">
                    Aplikasi Terbaru (Last 5)
                </h4>
                <ul id="recent-apps" class="space-y-1 text-sm text-[#e6edf3]">
                    <li class="text-[#8b949e]">Belum ada aplikasi.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

<script>
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

        // State untuk menyimpan daftar lokasi terpilih
        let selectedLocations = [];

        // Muat data awal (jika ada config yang sudah tersimpan / ada old() dari Laravel)
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
            console.error("Gagal parse data lokasi awal", e);
        }

        // Fungsi Render Tag UI & Hidden Inputs
        const renderTags = () => {
            els.tagsContainer.innerHTML = '';
            els.hiddenInputs.innerHTML = '';

            selectedLocations.forEach((loc, index) => {
                // 1. Buat Tag UI
                const tag = document.createElement('div');
                tag.className = 'flex items-center gap-1 bg-[#1f6feb] border border-[#388bfd] text-white px-2 py-1 rounded text-xs font-medium';

                const textSpan = document.createElement('span');
                textSpan.textContent = loc.name;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'ml-1 hover:text-red-300 focus:outline-none font-bold';
                removeBtn.innerHTML = '&times;';

                // Pastikan fungsi hapus memanggil splice dan render ulang
                removeBtn.onclick = function(e) {
                    e.preventDefault(); // Mencegah form submit jika tombol di dalam form
                    selectedLocations.splice(index, 1);
                    renderTags();
                };

                tag.appendChild(textSpan);
                tag.appendChild(removeBtn);
                els.tagsContainer.appendChild(tag);

                // 2. Buat Hidden Input untuk disubmit ke Controller Laravel
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

        // Render tag pertama kali (jika ada data tersimpan)
        renderTags();

        // 2. Utility: Debounce Function
        const debounce = (func, delay) => {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => func(...args), delay);
            };
        };

        // 3. Logic: Fetch Data dari Backend
        const fetchLocationData = async (keyword) => {
            const formData = new FormData();
            formData.append('keyword', keyword);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            const response = await fetch('{{ route("api.search.location", ["provider" => "glints"]) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');
            return await response.json();
        };

        // 4. UI: Render List Lokasi
        const renderResults = (list) => {
            els.results.innerHTML = '';

            if (!list || list.length === 0) {
                els.results.innerHTML = '<li class="px-3 py-2 text-[#8b949e] italic">Lokasi tidak ditemukan</li>';
                els.results.classList.remove('hidden');
                return;
            }

            let renderedCount = 0;

            list.forEach(item => {
                // Abaikan lokasi yang sudah dipilih agar tidak muncul di hasil pencarian
                if (selectedLocations.some(loc => loc.id === item.id)) return;

                const li = document.createElement('li');
                li.className = 'cursor-pointer px-3 py-2 text-[#e6edf3] hover:bg-[#1f242c] transition-colors border-b border-[#30363d] last:border-0';

                // Format teks lokasi (ex: Jakarta Selatan, DKI Jakarta)
                let fullName = item.name;
                if (item.parents && item.parents.length > 0) {
                    const parentNames = item.parents.map(p => p.name).join(', ');
                    fullName += `<span class="text-xs text-[#8b949e] block mt-0.5">${parentNames}</span>`;
                }

                li.innerHTML = fullName;
                li.addEventListener('click', () => {
                    // Tambahkan ke array state
                    selectedLocations.push({ id: item.id, name: item.name });
                    renderTags();

                    // Kosongkan form pencarian
                    els.input.value = '';
                    els.results.classList.add('hidden');
                    els.input.focus();
                });
                els.results.appendChild(li);
                renderedCount++;
            });

            // Jika semua hasil pencarian sudah dipilih sebelumnya, beritahu user
            if (renderedCount === 0) {
                els.results.innerHTML = '<li class="px-3 py-2 text-[#8b949e] italic">Lokasi sudah dipilih</li>';
            }

            els.results.classList.remove('hidden');
        };

        // 6. Logic: Handle Input Typing
        const handleInput = async (e) => {
            const keyword = e.target.value.trim();

            // Reset jika karakter kurang dari 2
            if (keyword.length < 2) {
                els.results.classList.add('hidden');
                els.results.innerHTML = '';
                return;
            }

            els.loading.classList.remove('hidden');

            try {
                const data = await fetchLocationData(keyword);

                // Ekstrak list dari struktur JSON Glints / Backend
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
                els.results.innerHTML = '<li class="px-3 py-2 text-red-400">Gagal mengambil data</li>';
                els.results.classList.remove('hidden');
            } finally {
                els.loading.classList.add('hidden');
            }
        };

        // 7. Attach Event Listeners
        els.input.addEventListener('input', debounce(handleInput, 500));

        document.addEventListener('click', (e) => {
            if (!els.input.contains(e.target) && !els.results.contains(e.target)) {
                els.results.classList.add('hidden');
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Jobstreet toggle
        const checkboxJS = document.getElementById('auto_answer_jobstreet');
        const statusTextJS = document.getElementById('status-text_jobstreet');
        if (checkboxJS && statusTextJS) {
            checkboxJS.addEventListener('change', function() {
                statusTextJS.textContent = this.checked ? 'Enabled' : 'Disabled';
                statusTextJS.classList.toggle('text-[#238636]', this.checked);
                statusTextJS.classList.toggle('text-[#8b949e]', !this.checked);
            });
        }

        // Glints toggle
        const checkboxGlints = document.getElementById('auto_answer_glints');
        const statusTextGlints = document.getElementById('status-text_glints');
        if (checkboxGlints && statusTextGlints) {
            checkboxGlints.addEventListener('change', function() {
                statusTextGlints.textContent = this.checked ? 'Enabled' : 'Disabled';
                statusTextGlints.classList.toggle('text-[#238636]', this.checked);
                statusTextGlints.classList.toggle('text-[#8b949e]', !this.checked);
            });
        }

        // Queue Jobs User
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
                    document.getElementById('pending-count').textContent = data.pending;
                    document.getElementById('processing-count').textContent = data.processing;
                    document.getElementById('failed-count').textContent = data.failed;

                    const list = document.getElementById('recent-jobs');
                    list.innerHTML = '';
                    if (data.recent && data.recent.length > 0) {
                        data.recent.forEach(job => {
                            const status = job.reserved_at ? '🟡 Processing' : '🟢 Pending';
                            const li = document.createElement('li');
                            li.textContent = `#${job.id} - ${status} (Attempts: ${job.attempts})`;
                            list.appendChild(li);
                        });
                    } else {
                        list.innerHTML = '<li class="text-[#8b949e]">Belum ada job.</li>';
                    }

                    document.getElementById('last-updated').textContent = `🔄 Updated: ${data.updated_at}`;
                })
                .catch(error => {
                    console.error('Error fetching queue status:', error);
                });
        }

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
                    // Update statistik
                    document.getElementById('stat-success').textContent       = data.stats.success || 0;
                    document.getElementById('stat-applied').textContent       = data.stats.applied || 0;
                    document.getElementById('stat-linkout').textContent       = data.stats.linkout || 0;
                    document.getElementById('stat-questionnaire').textContent = data.stats.questionnaire || 0;

                    // Update daftar terbaru
                    const list = document.getElementById('recent-apps');
                    list.innerHTML = '';
                    if (data.recent && data.recent.length > 0) {
                        data.recent.forEach(app => {
                            const statusMap = {
                                'success': '✅ Success',
                                'applied': '📩 Applied',
                                'linkout': '🔗 Link Out',
                                'questionnaire': '📝 Questionnaire'
                            };
                            const statusLabel = statusMap[app.status] || app.status;
                            const li = document.createElement('li');
                            li.textContent = `Job: ${app.job_id} - ${statusLabel} (${new Date(app.created_at).toLocaleString()})`;
                            list.appendChild(li);
                        });
                    } else {
                        list.innerHTML = '<li class="text-[#8b949e]">Belum ada aplikasi.</li>';
                    }

                    document.getElementById('app-last-updated').textContent = `🔄 Updated: ${data.updated_at}`;
                })
                .catch(error => {
                    console.error('Error fetching application status:', error);
                    document.getElementById('recent-apps').innerHTML = '<li class="text-red-400">Gagal memuat data.</li>';
                });
        }


        fetchApplicationStatus();
        setInterval(fetchApplicationStatus, 5000);
        fetchQueueStatus();
        setInterval(fetchQueueStatus, 5000);

        // Panggil init Location Autocomplete sekali saja
        initLocationAutocomplete();

    });
</script>
