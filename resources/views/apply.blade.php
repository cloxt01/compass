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
        <form method="POST" hidden action="{{ route('api.search.location', ['provider' => 'glints']) }}">
            @csrf
            <input type="text" name="keyword" placeholder="Masukan minimal 2 karakter">
            <button type="submit">Submit</button>
        </form>
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

@endsection

<script>
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
    });
</script>
