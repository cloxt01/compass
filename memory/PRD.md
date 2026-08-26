# PRD — Compass · Auto AI Answer & Mode Kustomisasi Kecocokan Lowongan

## Original Problem Statement
> Buatkan fitur **Auto AI Answer** dan **Mode kostumisasi tingkat kecocokan lowongan**.
> Provider: OpenRouter (default `deepseek/deepseek-v4-flash`). Output: skor persentase + alasan. Bahasa jawaban: Bahasa Indonesia. Slug model harus bisa diganti via UI konfigurasi tanpa mengedit kode.

## Tech Stack
- Laravel 11 (Blade + Livewire + Tailwind, dark theme #0a0a0a)
- MySQL (JSON column `users.apply_configuration`)
- Vite + JS
- OpenRouter API (`https://openrouter.ai/api/v1/chat/completions`)

## User Personas
- **Job seeker (kandidat)** — ingin (a) skor kecocokan sesuai bobot pribadi, (b) draft jawaban screening berbahasa Indonesia.

## Core Requirements
- Auto AI Answer via OpenRouter (Bahasa Indonesia)
- Kustomisasi bobot: skills, experience, location, salary, education (total 100)
- Output score: persentase + reasoning (AI-generated bila API key tersedia, fallback deterministik)
- Konfigurasi UI untuk model slug + API key + temperature + max_tokens **per user** tanpa edit kode
- Semua field disimpan di kolom JSON `users.apply_configuration`

## Data Model
`users.apply_configuration` (JSON, per user):
```json
{
  "career_match": {
    "weights": { "skills": 35, "experience": 25, "location": 15, "salary": 15, "education": 10 }
  },
  "career_ai": {
    "model": "deepseek/deepseek-v4-flash",
    "api_key": "sk-or-v1-...",
    "temperature": 0.35,
    "max_tokens": 800
  }
}
```

## Implemented
### Sesi sebelumnya
- `OpenRouterService`, `CareerMatchController`, view `career-match.blade.php`
- Route `/career-match`, `/career-match/weights`, `/career-match/answer`, `/career-match/score`
- Bobot per user (skills/experience/location/salary/education) di `career_match.weights`

### Sesi ini (Jan 2026) — AI Provider Settings
- **Settings → AI Provider (tab baru)** `resources/views/settings/tabs/ai-provider.blade.php`
  - Model slug (input + preset chips + datalist)
  - API key (masked, dukung clear key)
  - Temperature (range slider 0.0-2.0 dengan output live)
  - Max tokens (64-4096)
- `SettingsController::saveAiProvider` — validasi & simpan ke `career_ai`
- Route `POST /settings/ai-provider` (`settings.ai-provider.save`)
- Sidebar settings menambah menu "AI Provider" (icon sparkles)
- `OpenRouterService::resolveConfig(User $user)` — user config → fallback env
- `answerScreening` memakai model/temperature/max_tokens per user
- `reasonMatch` — endpoint score sekarang mengembalikan reasoning AI (Bahasa Indonesia) dengan fallback deterministik
- Link "Ubah model" di halaman CareerMatch menuju Settings

### Sesi ini (Jan 2026) — Follow-ups: History, Test Connection, Weight Presets
- **Answer History** (searchable log per user)
  - Migration `2026_01_15_100000_create_ai_answer_histories_table.php` — `ai_answer_histories` (user_id, model, question, candidate_context, job_context, answer, timestamps)
  - Model `App\Models\AiAnswerHistory` (belongsTo User)
  - `CareerMatchController::answer` → menyimpan hasil ke history
  - `CareerMatchController::historyIndex` (GET `/career-match/history?q=...`, 50 terbaru, LIKE search di question/answer/job_context)
  - `CareerMatchController::historyDestroy` (DELETE `/career-match/history/{id}`)
  - UI panel "Riwayat Jawaban" di `career-match.blade.php` — search debounce 250ms, salin ke clipboard, gunakan ulang (prefill form), hapus, expand-collapse (`<details>`)
- **Test Connection Button** di halaman AI Provider
  - `SettingsController::testAiProvider` (POST `/settings/ai-provider/test`)
  - `OpenRouterService::testConnection(overrides, user)` — kirim ping minimal (`max_tokens=8`, "ping"→"OK"), timeout 20s
  - Tombol "Test Connection" pada `ai-provider.blade.php` — memakai nilai model + api_key dari form (belum perlu save dulu), tampilkan latency ms dan pesan sukses/gagal
- **Weight Presets** di halaman CareerMatch
  - 5 preset chip: Seimbang, Tech-heavy, Location-first, Salary-first, Fresh Grad
  - Klik chip → auto-fill slider (belum tersimpan sampai user klik "Simpan preferensi")

## Files of Reference
- `app/Services/OpenRouterService.php`
- `app/Http/Controllers/CareerMatchController.php`
- `app/Http/Controllers/SettingsController.php`
- `app/Models/AiAnswerHistory.php`
- `database/migrations/2026_01_15_100000_create_ai_answer_histories_table.php`
- `resources/views/career-match.blade.php`
- `resources/views/settings/tabs/ai-provider.blade.php`
- `resources/views/settings/index.blade.php`
- `resources/views/layouts/settings.blade.php`
- `routes/web.php`, `config/services.php`

## Testing Notes
- Container tidak punya PHP/Composer/Vite → verifikasi runtime dilakukan user secara lokal
- **Wajib jalankan** `php artisan migrate` untuk membuat table `ai_answer_histories` sebelum menguji fitur history
- Endpoint yang harus dites lokal:
  - `GET /settings?tab=ai-provider` (render tab baru)
  - `POST /settings/ai-provider` (simpan + redirect + flash success)
  - `POST /settings/ai-provider/test` (test connection ke OpenRouter)
  - `POST /career-match/answer` (memakai config user, output Bahasa Indonesia, tersimpan ke history)
  - `POST /career-match/score` (reasoning AI + fallback)
  - `GET  /career-match/history?q=...` (list + search)
  - `DELETE /career-match/history/{id}` (hapus riwayat)

## Backlog / Next
- P2 — Enkripsi at-rest untuk `api_key` (Laravel `encrypted` cast)
- P2 — Dukungan multi-provider (abstract `AiProvider` interface)
- P2 — Export riwayat ke PDF/Markdown
- P2 — Analytics: tren skor kecocokan seiring waktu, insight kategori terlemah
