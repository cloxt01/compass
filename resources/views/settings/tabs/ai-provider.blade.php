@php
    $configuration = auth()->user()->apply_configuration ?? [];
    $careerAi = is_array($configuration) && is_array($configuration['career_ai'] ?? null) ? $configuration['career_ai'] : [];
    $envModel = config('services.openrouter.model') ?: \App\Services\OpenRouterService::DEFAULT_MODEL;
    $currentModel = trim((string) ($careerAi['model'] ?? '')) !== '' ? $careerAi['model'] : $envModel;
    $currentTemperature = isset($careerAi['temperature']) ? (float) $careerAi['temperature'] : \App\Services\OpenRouterService::DEFAULT_TEMPERATURE;
    $currentMaxTokens = isset($careerAi['max_tokens']) ? (int) $careerAi['max_tokens'] : \App\Services\OpenRouterService::DEFAULT_MAX_TOKENS;
    $hasApiKey = !empty($careerAi['api_key']);
    $maskedKey = $hasApiKey ? substr($careerAi['api_key'], 0, 6) . str_repeat('*', 12) . substr($careerAi['api_key'], -4) : '';
    $presetModels = [
        'deepseek/deepseek-v4-flash' => 'DeepSeek V4 Flash (default, cepat)',
        'deepseek/deepseek-chat' => 'DeepSeek Chat',
        'openai/gpt-4o-mini' => 'OpenAI GPT-4o Mini',
        'anthropic/claude-3.5-haiku' => 'Anthropic Claude 3.5 Haiku',
        'google/gemini-2.0-flash-001' => 'Google Gemini 2.0 Flash',
        'meta-llama/llama-3.3-70b-instruct' => 'Meta Llama 3.3 70B',
    ];
@endphp

<div class="space-y-6" data-testid="ai-provider-settings">
    @if(session('success'))
        <div class="rounded-md border border-emerald-500/40 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-300" data-testid="ai-provider-flash-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-md border border-rose-500/40 bg-rose-500/10 px-3 py-2 text-xs text-rose-300" data-testid="ai-provider-flash-error">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[#0a0a0a] border border-[#262626] rounded-md">
        <div class="px-5 py-4 border-b border-[#262626] flex items-start justify-between gap-4">
            <div>
                <h2 class="text-sm font-semibold text-[#fafafa]">OpenRouter — AI Provider</h2>
                <p class="mt-1 text-xs text-[#71717a]">
                    Atur model, API key, dan parameter generasi untuk fitur <strong class="text-[#e4e4e7]">Auto AI Answer</strong> &amp; <strong class="text-[#e4e4e7]">CareerMatch AI</strong>. Konfigurasi ini menimpa default sistem tanpa perlu mengubah kode.
                </p>
            </div>
            <span class="hidden md:inline-flex items-center gap-1.5 rounded-full border border-indigo-400/30 bg-indigo-400/10 px-2.5 py-1 text-[11px] font-medium text-indigo-200">
                <i data-lucide="sparkles" class="w-3 h-3"></i> Per user
            </span>
        </div>

        <form method="POST" action="{{ route('settings.ai-provider.save') }}" class="divide-y divide-[#262626]" data-testid="ai-provider-form">
            @csrf

            {{-- MODEL SLUG --}}
            <div class="p-5 space-y-3">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div class="md:w-1/3">
                        <h3 class="text-sm font-semibold text-[#fafafa]">Model Slug</h3>
                        <p class="text-xs text-[#71717a] mt-1">
                            Slug OpenRouter yang akan dipanggil. Pilih preset atau ketik slug kustom (mis. <code class="text-[#e4e4e7]">meta-llama/llama-3.3-70b-instruct</code>).
                        </p>
                    </div>
                    <div class="md:flex-1 space-y-2">
                        <input
                            type="text"
                            name="model"
                            id="ai-provider-model"
                            value="{{ old('model', $currentModel) }}"
                            list="ai-model-presets"
                            placeholder="{{ $envModel }}"
                            data-testid="ai-provider-model-input"
                            class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] placeholder-[#52525b] focus:outline-none focus:border-indigo-400"
                        >
                        <datalist id="ai-model-presets">
                            @foreach($presetModels as $slug => $label)
                                <option value="{{ $slug }}">{{ $label }}</option>
                            @endforeach
                        </datalist>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($presetModels as $slug => $label)
                                <button
                                    type="button"
                                    data-preset="{{ $slug }}"
                                    data-testid="ai-provider-preset-{{ \Illuminate\Support\Str::slug($slug) }}"
                                    class="ai-provider-preset text-[11px] px-2 py-1 rounded-md border border-[#333] bg-[#1a1a1a] text-[#a1a1aa] hover:border-indigo-400 hover:text-[#fafafa] transition"
                                >{{ $slug }}</button>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-[#52525b]">Default sistem: <code>{{ $envModel }}</code></p>
                    </div>
                </div>
            </div>

            {{-- API KEY --}}
            <div class="p-5 space-y-3">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div class="md:w-1/3">
                        <h3 class="text-sm font-semibold text-[#fafafa]">API Key</h3>
                        <p class="text-xs text-[#71717a] mt-1">
                            API key OpenRouter pribadi Anda. Dapatkan di <a href="https://openrouter.ai/keys" target="_blank" rel="noopener" class="text-indigo-300 hover:underline">openrouter.ai/keys</a>. Biarkan kosong untuk memakai key default server.
                        </p>
                    </div>
                    <div class="md:flex-1 space-y-2">
                        @if($hasApiKey)
                            <div class="flex items-center justify-between rounded-md border border-emerald-500/30 bg-emerald-500/5 px-3 py-2 text-xs text-emerald-200" data-testid="ai-provider-key-status">
                                <span><i data-lucide="check-circle-2" class="inline-block w-3.5 h-3.5 -mt-0.5"></i> Key tersimpan: <span class="font-mono">{{ $maskedKey }}</span></span>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="clear_api_key" value="1" class="accent-rose-400" data-testid="ai-provider-clear-key-checkbox">
                                    <span class="text-rose-300">Hapus key</span>
                                </label>
                            </div>
                        @endif
                        <input
                            type="password"
                            name="api_key"
                            autocomplete="new-password"
                            placeholder="{{ $hasApiKey ? 'Kosongkan untuk mempertahankan key saat ini' : 'sk-or-v1-...' }}"
                            data-testid="ai-provider-api-key-input"
                            class="w-full rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] placeholder-[#52525b] focus:outline-none focus:border-indigo-400 font-mono"
                        >
                        <p class="text-[11px] text-[#52525b]">Key hanya digunakan untuk memanggil OpenRouter. Tidak pernah ditampilkan kembali secara utuh.</p>
                    </div>
                </div>
            </div>

            {{-- TEMPERATURE --}}
            <div class="p-5 space-y-3">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div class="md:w-1/3">
                        <h3 class="text-sm font-semibold text-[#fafafa]">Temperature</h3>
                        <p class="text-xs text-[#71717a] mt-1">
                            Semakin tinggi, semakin kreatif; semakin rendah, semakin konsisten. Rentang 0.0 – 2.0.
                        </p>
                    </div>
                    <div class="md:flex-1 space-y-2">
                        <div class="flex items-center gap-3">
                            <input
                                type="range"
                                name="temperature"
                                id="ai-provider-temperature-range"
                                min="0" max="2" step="0.05"
                                value="{{ old('temperature', $currentTemperature) }}"
                                data-testid="ai-provider-temperature-range"
                                class="flex-1 accent-indigo-400"
                            >
                            <output id="ai-provider-temperature-output" data-testid="ai-provider-temperature-output" class="font-mono text-sm text-indigo-300 w-14 text-right">{{ number_format((float) old('temperature', $currentTemperature), 2) }}</output>
                        </div>
                        <p class="text-[11px] text-[#52525b]">Rekomendasi untuk jawaban lamaran: 0.30 – 0.60.</p>
                    </div>
                </div>
            </div>

            {{-- MAX TOKENS --}}
            <div class="p-5 space-y-3">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                    <div class="md:w-1/3">
                        <h3 class="text-sm font-semibold text-[#fafafa]">Max Tokens</h3>
                        <p class="text-xs text-[#71717a] mt-1">
                            Batas panjang jawaban yang di-generate. Rentang 64 – 4096.
                        </p>
                    </div>
                    <div class="md:flex-1">
                        <input
                            type="number"
                            name="max_tokens"
                            min="64" max="4096" step="16"
                            value="{{ old('max_tokens', $currentMaxTokens) }}"
                            data-testid="ai-provider-max-tokens-input"
                            class="w-40 rounded-md border border-[#333] bg-[#111] px-3 py-2 text-sm text-[#fafafa] focus:outline-none focus:border-indigo-400"
                        >
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="p-5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        id="ai-provider-test-btn"
                        data-testid="ai-provider-test-button"
                        data-endpoint="{{ route('settings.ai-provider.test') }}"
                        class="inline-flex items-center gap-2 rounded-md border border-emerald-500/40 bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-200 hover:bg-emerald-500/20 transition"
                    >
                        <i data-lucide="plug-zap" class="w-4 h-4"></i>
                        Test Connection
                    </button>
                    <span id="ai-provider-test-status" data-testid="ai-provider-test-status" class="text-xs text-[#71717a]"></span>
                </div>
                <button
                    type="submit"
                    data-testid="ai-provider-save-button"
                    class="inline-flex items-center gap-2 rounded-md bg-indigo-500 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-400 transition"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modelInput = document.getElementById('ai-provider-model');
        document.querySelectorAll('.ai-provider-preset').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (modelInput) {
                    modelInput.value = btn.dataset.preset;
                    modelInput.focus();
                }
            });
        });

        const range = document.getElementById('ai-provider-temperature-range');
        const output = document.getElementById('ai-provider-temperature-output');
        if (range && output) {
            const sync = function () {
                output.textContent = Number(range.value).toFixed(2);
            };
            range.addEventListener('input', sync);
            sync();
        }

        // ---- Test Connection ----
        const testBtn = document.getElementById('ai-provider-test-btn');
        const testStatus = document.getElementById('ai-provider-test-status');
        if (testBtn && testStatus) {
            testBtn.addEventListener('click', async function () {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const modelValue = document.getElementById('ai-provider-model')?.value || '';
                const apiKeyValue = document.querySelector('input[name="api_key"]')?.value || '';
                testBtn.disabled = true;
                testStatus.className = 'text-xs text-[#71717a]';
                testStatus.textContent = 'Menghubungi OpenRouter...';
                try {
                    const resp = await fetch(testBtn.dataset.endpoint, {
                        method: 'POST',
                        headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
                        body: JSON.stringify({ model: modelValue, api_key: apiKeyValue })
                    });
                    const data = await resp.json().catch(() => ({}));
                    if (resp.ok && data.ok) {
                        testStatus.className = 'text-xs text-emerald-300';
                        testStatus.textContent = `OK ${data.message} (${data.latency_ms} ms)`;
                    } else {
                        testStatus.className = 'text-xs text-rose-300';
                        testStatus.textContent = `X ${data.message || 'Gagal terhubung.'}`;
                    }
                } catch (err) {
                    testStatus.className = 'text-xs text-rose-300';
                    testStatus.textContent = 'X Kesalahan jaringan: ' + err.message;
                } finally {
                    testBtn.disabled = false;
                }
            });
        }

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
</script>
@endpush
