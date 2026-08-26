@extends('layouts.app')

@section('title', 'CareerMatch AI · Compass')
@section('titleNavbar', 'CareerMatch AI')

@section('content')
<div class="mx-auto max-w-[1400px] space-y-6 px-4 pb-10 pt-2" id="careerMatchApp" data-testid="career-match-page">
    <section class="relative overflow-hidden rounded-2xl border border-[#263653] bg-gradient-to-br from-[#111c35] via-[#111827] to-[#0a0a0a] p-6 md:p-8">
        <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="relative max-w-3xl">
            <p class="mb-3 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-300">CareerMatch AI</p>
            <h1 class="text-3xl font-semibold tracking-tight text-white md:text-4xl">Lamaran lebih tajam, jawaban lebih siap.</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300">Atur cara Compass menilai lowongan dan buat jawaban screening yang tetap sesuai pengalaman Anda.</p>
        </div>
    </section>

    <div id="careerMatchNotice" data-testid="career-match-notice" class="hidden rounded-xl border px-4 py-3 text-sm"></div>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <article class="saas-card p-6" data-testid="match-settings-card">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-300">01 / Preferensi</p>
                    <h2 class="mt-2 text-xl font-semibold text-white">Bobot kecocokan lowongan</h2>
                    <p class="mt-1 text-sm text-slate-400">Geser prioritas Anda. Total bobot harus 100%.</p>
                </div>
                <div id="weightTotal" data-testid="match-weight-total" class="rounded-full border border-indigo-400/30 bg-indigo-400/10 px-3 py-1 text-sm font-semibold text-indigo-200">100%</div>
            </div>

            {{-- Preset chips --}}
            <div class="mt-5" data-testid="weight-presets">
                <p class="mb-2 text-xs uppercase tracking-widest text-slate-500">Preset cepat</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-preset="balanced" data-weights='{"skills":35,"experience":25,"location":15,"salary":15,"education":10}' data-testid="preset-balanced" class="weight-preset-btn rounded-lg border border-slate-600/60 bg-slate-800/40 px-3 py-1.5 text-xs font-medium text-slate-200 hover:border-indigo-400 hover:text-white transition">Seimbang</button>
                    <button type="button" data-preset="tech-heavy" data-weights='{"skills":55,"experience":25,"location":5,"salary":5,"education":10}' data-testid="preset-tech-heavy" class="weight-preset-btn rounded-lg border border-slate-600/60 bg-slate-800/40 px-3 py-1.5 text-xs font-medium text-slate-200 hover:border-emerald-400 hover:text-white transition">Tech-heavy</button>
                    <button type="button" data-preset="location-first" data-weights='{"skills":20,"experience":15,"location":45,"salary":10,"education":10}' data-testid="preset-location-first" class="weight-preset-btn rounded-lg border border-slate-600/60 bg-slate-800/40 px-3 py-1.5 text-xs font-medium text-slate-200 hover:border-sky-400 hover:text-white transition">Location-first</button>
                    <button type="button" data-preset="salary-first" data-weights='{"skills":20,"experience":20,"location":10,"salary":40,"education":10}' data-testid="preset-salary-first" class="weight-preset-btn rounded-lg border border-slate-600/60 bg-slate-800/40 px-3 py-1.5 text-xs font-medium text-slate-200 hover:border-amber-400 hover:text-white transition">Salary-first</button>
                    <button type="button" data-preset="fresh-grad" data-weights='{"skills":25,"experience":10,"location":15,"salary":15,"education":35}' data-testid="preset-fresh-grad" class="weight-preset-btn rounded-lg border border-slate-600/60 bg-slate-800/40 px-3 py-1.5 text-xs font-medium text-slate-200 hover:border-rose-400 hover:text-white transition">Fresh Grad</button>
                </div>
            </div>

            <div class="mt-6 space-y-5">
                @foreach(['skills' => 'Skill & kompetensi', 'experience' => 'Pengalaman kerja', 'location' => 'Lokasi & cara kerja', 'salary' => 'Gaji & kompensasi', 'education' => 'Pendidikan'] as $key => $label)
                    <label class="block" data-testid="weight-{{ $key }}-control">
                        <div class="mb-2 flex items-center justify-between text-sm"><span class="text-slate-200">{{ $label }}</span><output id="{{ $key }}Value" data-testid="weight-{{ $key }}-value" class="font-mono text-indigo-300">{{ $weights[$key] }}%</output></div>
                        <input id="{{ $key }}Weight" data-weight-key="{{ $key }}" data-testid="weight-{{ $key }}-input" type="range" min="0" max="100" value="{{ $weights[$key] }}" class="w-full accent-indigo-400">
                    </label>
                @endforeach
            </div>
            <button id="saveWeights" data-testid="save-match-weights-button" type="button" class="mt-7 inline-flex w-full items-center justify-center rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-400">Simpan preferensi</button>
        </article>

        <article class="saas-card p-6" data-testid="match-calculator-card">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-300">02 / Analisis</p>
                <h2 class="mt-2 text-xl font-semibold text-white">Cek tingkat kecocokan</h2>
                <p class="mt-1 text-sm text-slate-400">Bandingkan profil dengan deskripsi lowongan secara langsung.</p>
            </div>
            <div class="mt-6 space-y-4">
                <label class="block text-sm text-slate-300">Profil kandidat<textarea id="candidateProfile" data-testid="candidate-profile-input" rows="5" class="saas-input mt-2" placeholder="Contoh: 4 tahun pengalaman Laravel, tinggal di Jakarta, S1 Teknik Informatika..."></textarea></label>
                <label class="block text-sm text-slate-300">Deskripsi lowongan<textarea id="jobDescription" data-testid="job-description-input" rows="5" class="saas-input mt-2" placeholder="Tempel kebutuhan, lokasi, gaji, dan pendidikan dari lowongan..."></textarea></label>
                <button id="calculateScore" data-testid="calculate-match-button" type="button" class="inline-flex w-full items-center justify-center rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-400/20">Hitung tingkat kecocokan</button>
            </div>
            <div id="scoreResult" data-testid="match-score-result" class="mt-6 hidden rounded-xl border border-[#263653] bg-[#0a1020] p-5">
                <div class="flex items-end justify-between gap-4"><div><p class="text-xs uppercase tracking-widest text-slate-400">Skor kecocokan</p><p id="scoreNumber" data-testid="match-score-number" class="mt-1 text-5xl font-semibold text-white">0%</p></div><div id="scoreReason" data-testid="match-score-reason" class="max-w-[220px] text-right text-sm leading-5 text-slate-300"></div></div>
                <div id="scoreBreakdown" data-testid="match-score-breakdown" class="mt-5 grid grid-cols-2 gap-3"></div>
            </div>
        </article>
    </section>

    <section class="saas-card p-6" data-testid="ai-answer-card">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
            <div><p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-300">03 / Auto AI Answer</p><h2 class="mt-2 text-xl font-semibold text-white">Jawab screening dengan percaya diri</h2><p class="mt-1 text-sm text-slate-400">AI menyusun jawaban Bahasa Indonesia berdasarkan konteks yang Anda berikan.</p></div>
            <div class="flex items-center gap-2">
                <span data-testid="ai-model-label" class="rounded-full border border-amber-400/20 bg-amber-400/10 px-3 py-1 text-xs text-amber-200">{{ $model }}</span>
                <a href="{{ route('settings', ['tab' => 'ai-provider']) }}" data-testid="ai-provider-config-link" class="inline-flex items-center gap-1.5 rounded-full border border-[#333] bg-[#0a0a0a] px-3 py-1 text-xs text-slate-300 hover:text-white hover:border-indigo-400 transition">
                    <i data-lucide="settings-2" class="w-3.5 h-3.5"></i>
                    Ubah model
                </a>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
            <label class="block text-sm text-slate-300">Pertanyaan screening<textarea id="screeningQuestion" data-testid="screening-question-input" rows="6" class="saas-input mt-2" placeholder="Contoh: Mengapa Anda tertarik dengan posisi ini?"></textarea></label>
            <label class="block text-sm text-slate-300">Profil kandidat<textarea id="answerContext" data-testid="answer-candidate-context-input" rows="6" class="saas-input mt-2" placeholder="Pengalaman, pencapaian, skill, dan preferensi Anda..."></textarea></label>
            <label class="block text-sm text-slate-300">Konteks lowongan <span class="text-slate-500">(opsional)</span><textarea id="answerJobContext" data-testid="answer-job-context-input" rows="6" class="saas-input mt-2" placeholder="Judul posisi, perusahaan, dan kebutuhan utama..."></textarea></label>
        </div>
        <div class="mt-4 flex flex-col gap-4 md:flex-row md:items-center"><button id="generateAnswer" data-testid="generate-ai-answer-button" type="button" class="inline-flex items-center justify-center rounded-xl bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">Buat jawaban AI</button><p id="answerStatus" data-testid="ai-answer-status" class="text-sm text-slate-400"></p></div>
        <div id="answerResult" data-testid="ai-answer-result" class="mt-5 hidden whitespace-pre-wrap rounded-xl border border-amber-400/20 bg-amber-400/5 p-5 text-sm leading-7 text-slate-200"></div>
    </section>

    {{-- ANSWER HISTORY --}}
    <section class="saas-card p-6" data-testid="answer-history-card">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-300">04 / Riwayat Jawaban</p>
                <h2 class="mt-2 text-xl font-semibold text-white">Riwayat jawaban AI</h2>
                <p class="mt-1 text-sm text-slate-400">Cari dan gunakan kembali jawaban yang pernah dibuat. Menampilkan 50 riwayat terbaru.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input id="historySearch" data-testid="history-search-input" type="search" placeholder="Cari pertanyaan / jawaban..." class="saas-input pr-9" style="min-width:260px">
                    <i data-lucide="search" class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-500"></i>
                </div>
                <button id="historyRefresh" type="button" data-testid="history-refresh-button" class="rounded-xl border border-slate-600/60 bg-slate-800/40 px-3 py-2 text-xs font-medium text-slate-200 hover:border-sky-400 hover:text-white transition">
                    <i data-lucide="rotate-cw" class="inline h-3.5 w-3.5"></i> Muat ulang
                </button>
            </div>
        </div>
        <div id="historyStatus" data-testid="history-status" class="mt-4 text-sm text-slate-400">Memuat riwayat...</div>
        <div id="historyList" data-testid="history-list" class="mt-3 space-y-3"></div>
    </section>
</div>
@endsection

@push('styles')
<style>
    .saas-card { background:#111111; border:1px solid #262626; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,.24); }
    .saas-input { width:100%; border:1px solid #263653; border-radius:12px; background:#0a1020; padding:.75rem; color:#e2e8f0; outline:0; transition:border-color .2s, box-shadow .2s; }
    .saas-input:focus { border-color:#818cf8; box-shadow:0 0 0 3px rgba(99,102,241,.15); }
    input[type=range] { height:5px; cursor:pointer; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const notice = document.getElementById('careerMatchNotice');
    const labels = {skills:'Skill', experience:'Pengalaman', location:'Lokasi', salary:'Gaji', education:'Pendidikan'};
    const showNotice = (message, ok = false) => { notice.textContent = message; notice.className = `rounded-xl border px-4 py-3 text-sm ${ok ? 'border-emerald-400/30 bg-emerald-400/10 text-emerald-200' : 'border-rose-400/30 bg-rose-400/10 text-rose-200'}`; };
    const weights = () => Object.fromEntries([...document.querySelectorAll('[data-weight-key]')].map(input => [input.dataset.weightKey, Number(input.value)]));
    const updateWeights = () => { const current = weights(); Object.entries(current).forEach(([key, value]) => document.getElementById(`${key}Value`).textContent = `${value}%`); document.getElementById('weightTotal').textContent = `${Object.values(current).reduce((a,b) => a+b, 0)}%`; };
    document.querySelectorAll('[data-weight-key]').forEach(input => input.addEventListener('input', updateWeights));

    // ---- Weight presets ----
    document.querySelectorAll('.weight-preset-btn').forEach(btn => btn.addEventListener('click', () => {
        try {
            const preset = JSON.parse(btn.dataset.weights || '{}');
            Object.entries(preset).forEach(([key, value]) => {
                const input = document.querySelector(`[data-weight-key="${key}"]`);
                if (input) input.value = value;
            });
            updateWeights();
            showNotice(`Preset "${btn.textContent.trim()}" diterapkan. Klik "Simpan preferensi" untuk menyimpan.`, true);
        } catch (e) { showNotice('Preset tidak valid.'); }
    }));

    const post = async (url, body) => { const response = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':token}, body:JSON.stringify(body) }); const raw = await response.text(); let json; try { json = JSON.parse(raw); } catch { throw new Error('Server mengembalikan respons yang tidak valid.'); } if (!response.ok) throw new Error(json.message || 'Terjadi kesalahan.'); return json; };
    const doDelete = async (url) => { const response = await fetch(url, { method:'DELETE', headers:{'Accept':'application/json','X-CSRF-TOKEN':token} }); const raw = await response.text(); let json; try { json = JSON.parse(raw); } catch { throw new Error('Server mengembalikan respons yang tidak valid.'); } if (!response.ok) throw new Error(json.message || 'Terjadi kesalahan.'); return json; };

    document.getElementById('saveWeights').addEventListener('click', async () => { try { if (Object.values(weights()).reduce((a,b)=>a+b,0) !== 100) throw new Error('Total bobot harus tepat 100%.'); await post('{{ route('career-match.weights') }}', {weights:weights()}); showNotice('Preferensi kecocokan berhasil disimpan.', true); } catch (e) { showNotice(e.message); } });
    document.getElementById('calculateScore').addEventListener('click', async () => { const candidate = document.getElementById('candidateProfile').value.trim(), job = document.getElementById('jobDescription').value.trim(); if (!candidate || !job) return showNotice('Isi profil kandidat dan deskripsi lowongan terlebih dahulu.'); try { const result = await post('{{ route('career-match.score') }}', {candidate, job}); document.getElementById('scoreResult').classList.remove('hidden'); document.getElementById('scoreNumber').textContent = `${result.score}%`; document.getElementById('scoreReason').textContent = result.reason; document.getElementById('scoreBreakdown').innerHTML = Object.entries(result.breakdown).map(([key, item]) => `<div class="rounded-lg border border-[#263653] p-3"><div class="flex justify-between text-xs text-slate-400"><span>${labels[key]}</span><span>${item.score}%</span></div><div class="mt-2 h-1.5 rounded-full bg-slate-800"><div class="h-full rounded-full bg-indigo-400" style="width:${item.score}%"></div></div></div>`).join(''); } catch(e) { showNotice(e.message); } });
    document.getElementById('generateAnswer').addEventListener('click', async () => { const question = document.getElementById('screeningQuestion').value.trim(), candidate_context = document.getElementById('answerContext').value.trim(), job_context = document.getElementById('answerJobContext').value.trim(), status = document.getElementById('answerStatus'), result = document.getElementById('answerResult'); if (!question || !candidate_context) return showNotice('Isi pertanyaan dan profil kandidat terlebih dahulu.'); status.textContent = 'Sedang menyusun jawaban...'; result.classList.add('hidden'); try { const data = await post('{{ route('career-match.answer') }}', {question, candidate_context, job_context}); result.textContent = data.answer; result.classList.remove('hidden'); status.textContent = 'Jawaban siap ditinjau dan disalin.'; loadHistory(); } catch(e) { status.textContent = ''; showNotice(e.message); } });

    // ---- History ----
    const historyList = document.getElementById('historyList');
    const historyStatus = document.getElementById('historyStatus');
    const historySearch = document.getElementById('historySearch');
    const escapeHtml = (s) => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    const renderHistory = (items) => {
        if (!items || items.length === 0) {
            historyList.innerHTML = '';
            historyStatus.textContent = 'Belum ada riwayat jawaban.';
            return;
        }
        historyStatus.textContent = `${items.length} riwayat ditampilkan.`;
        historyList.innerHTML = items.map(item => `
            <details class="group rounded-xl border border-[#263653] bg-[#0a1020] transition hover:border-sky-500/40" data-testid="history-item-${item.id}">
                <summary class="flex cursor-pointer items-start justify-between gap-3 p-4">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-100">${escapeHtml(item.question)}</p>
                        <p class="mt-1 flex items-center gap-2 text-[11px] text-slate-500">
                            <span>${escapeHtml(item.created_at_human || '')}</span>
                            ${item.model ? `<span class="rounded-full border border-slate-700/70 px-2 py-0.5 text-slate-400">${escapeHtml(item.model)}</span>` : ''}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="history-copy rounded-md border border-slate-600/60 bg-slate-800/40 px-2 py-1 text-[11px] text-slate-200 hover:border-emerald-400 hover:text-white transition" data-testid="history-copy-${item.id}" data-answer="${escapeHtml(item.answer)}">Salin</button>
                        <button type="button" class="history-reuse rounded-md border border-slate-600/60 bg-slate-800/40 px-2 py-1 text-[11px] text-slate-200 hover:border-indigo-400 hover:text-white transition" data-testid="history-reuse-${item.id}" data-question="${escapeHtml(item.question)}" data-job="${escapeHtml(item.job_context || '')}">Gunakan ulang</button>
                        <button type="button" class="history-delete rounded-md border border-slate-600/60 bg-slate-800/40 px-2 py-1 text-[11px] text-rose-300 hover:border-rose-400 hover:text-white transition" data-testid="history-delete-${item.id}" data-id="${item.id}">Hapus</button>
                    </div>
                </summary>
                <div class="whitespace-pre-wrap border-t border-[#263653] p-4 text-sm leading-6 text-slate-200">${escapeHtml(item.answer)}</div>
            </details>
        `).join('');
    };

    let searchTimer;
    const loadHistory = async (q = '') => {
        try {
            historyStatus.textContent = 'Memuat riwayat...';
            const url = '{{ route('career-match.history') }}' + (q ? ('?q=' + encodeURIComponent(q)) : '');
            const response = await fetch(url, { headers: {'Accept':'application/json','X-CSRF-TOKEN':token} });
            const data = await response.json();
            renderHistory(data.items || []);
        } catch (e) {
            historyStatus.textContent = 'Gagal memuat riwayat.';
        }
    };

    historySearch.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadHistory(historySearch.value.trim()), 250);
    });
    document.getElementById('historyRefresh').addEventListener('click', () => loadHistory(historySearch.value.trim()));

    historyList.addEventListener('click', async (e) => {
        const copyBtn = e.target.closest('.history-copy');
        const reuseBtn = e.target.closest('.history-reuse');
        const deleteBtn = e.target.closest('.history-delete');
        if (copyBtn) {
            e.preventDefault();
            try { await navigator.clipboard.writeText(copyBtn.dataset.answer || ''); showNotice('Jawaban disalin ke clipboard.', true); } catch { showNotice('Gagal menyalin. Coba salin manual.'); }
        }
        if (reuseBtn) {
            e.preventDefault();
            document.getElementById('screeningQuestion').value = reuseBtn.dataset.question || '';
            document.getElementById('answerJobContext').value = reuseBtn.dataset.job || '';
            document.getElementById('screeningQuestion').scrollIntoView({behavior:'smooth', block:'center'});
            showNotice('Konten dimuat ke form Auto AI Answer. Silakan ubah lalu klik "Buat jawaban AI".', true);
        }
        if (deleteBtn) {
            e.preventDefault();
            if (!confirm('Hapus riwayat ini?')) return;
            try {
                await doDelete('{{ url('/career-match/history') }}/' + deleteBtn.dataset.id);
                loadHistory(historySearch.value.trim());
                showNotice('Riwayat dihapus.', true);
            } catch (err) { showNotice(err.message); }
        }
    });

    loadHistory();
});
</script>
@endpush
