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

## Implemented (All features)

### CareerMatch Core
- `OpenRouterService` — resolveConfig (user → env fallback), answerScreening, reasonMatch, testConnection
- `CareerMatchController` — index, saveWeights, answer, score, historyIndex, historyDestroy
- View `career-match.blade.php` — Weight sliders, score calculator, Auto AI Answer form, History panel
- Routes: `/career-match`, `/career-match/weights`, `/career-match/answer`, `/career-match/score`, `/career-match/history`, `/career-match/history/{id}`

### AI Provider Settings (Settings → AI Provider tab)
- `resources/views/settings/tabs/ai-provider.blade.php`
  - Model slug (input + preset chips + datalist)
  - API key (masked, dukung clear key)
  - Temperature (range slider 0.0-2.0 dengan output live)
  - Max tokens (64-4096)
- `SettingsController::saveAiProvider` — validasi & simpan ke `career_ai`
- `SettingsController::testAiProvider` — test connection ke OpenRouter
- Routes: `POST /settings/ai-provider`, `POST /settings/ai-provider/test`
- Sidebar settings menambah menu "AI Provider" (icon sparkles)

### Answer History
- Migration `2026_01_15_100000_create_ai_answer_histories_table.php`
- Model `App\Models\AiAnswerHistory` (belongsTo User)
- Searchable log (50 terbaru, LIKE search), salin ke clipboard, gunakan ulang, hapus

### Weight Presets
- 5 preset chip: Seimbang, Tech-heavy, Location-first, Salary-first, Fresh Grad
- Klik chip → auto-fill slider (belum tersimpan sampai user klik "Simpan preferensi")

### Test Connection Button
- Tombol di halaman AI Provider — test pakai nilai dari form (belum perlu save dulu)
- Tampilkan latency ms dan pesan sukses/gagal

## Critical Fix (Feb 2026)
- **File location fix**: Semua file fitur baru dipindahkan dari `/app/compass-main/compass-main/` ke root `/app/` agar ikut terpush ke GitHub. Agent sebelumnya menulis kode di folder salinan zip, bukan di root project.

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
- **Wajib jalankan** `php artisan migrate` untuk membuat table `ai_answer_histories`
- Endpoint yang harus dites lokal:
  - `GET /settings?tab=ai-provider`
  - `POST /settings/ai-provider`
  - `POST /settings/ai-provider/test`
  - `POST /career-match/answer`
  - `POST /career-match/score`
  - `GET /career-match/history?q=...`
  - `DELETE /career-match/history/{id}`

## Backlog / Next
- P2 — Enkripsi at-rest untuk `api_key` (Laravel `encrypted` cast)
- P2 — Dukungan multi-provider (abstract `AiProvider` interface)

## Feb 2026 — Auto AI Answer di Flow Apply (Glints)

### Yang ditambahkan
- **Auto-answer screening question saat auto-apply** untuk Glints.
- Panel Configuration (halaman `/apply`) sekarang punya toggle "AI Powered Auto Answer" yang benar (bukan disabled placeholder).
  - Simpan ke `users.apply_configuration.auto_answer.enabled`.
  - Toggle tidak bisa diaktifkan kalau API key OpenRouter belum di-set di Settings → AI Provider.
  - Menampilkan model AI yang dipakai (dari `career_ai.model`).
- **ApplyUseCase**: kalau `auto_answer.enabled=true`, questionnaire tidak diblokir (bukan hard-issue lagi). AutoAnswerService dijalankan untuk mengisi jawaban sebelum submit.
- **`PlatformFactory::build_payload` (Glints)**: bila ada `ai_answer`, jawaban ditransform ke format `employerScreeningQuestionAnswers` sesuai payload Glints.
- **History per lamaran**: `application_ai_answers` menyimpan prompt, response, model, tokens, match_score, breakdown per-question, jawaban akhir. Setelah lamaran ter-record, `application_id` di-link.
- **UI**: badge "AI XX%" pada baris tabel Applications yang punya history AI, klik → halaman `ai-answer-detail` (Match Score, Terjawab, Token Usage, Durasi + Rincian Per Pertanyaan dengan confidence + Profile Context + Raw Response + Final Payload).
- **Match score**: rata-rata confidence AI per pertanyaan (0-100). Pertanyaan yang tidak bisa dijawab (data profil tidak tersedia) dihitung 0.

### File baru
- `app/Services/AI/AutoAnswerService.php` — orchestrator (normalize question, build profile, call OpenRouter dengan JSON mode, parse, hitung score, simpan history)
- `app/Services/AI/AnswerTransformer.php` — AI JSON → format submit provider (Glints saat ini)
- `app/Services/AI/ProfileBuilder.php` — gabung profile platform + manual dari `apply_configuration.auto_answer.profile`
- `app/Models/ApplicationAiAnswer.php`
- `database/migrations/2026_02_20_000000_create_application_ai_answers_table.php`
- `database/migrations/2026_02_20_000001_add_auto_answer_failed_to_applications_status.php`
- `resources/views/ai-answer-detail.blade.php`
- Route: `GET /applications/ai-answer/{id}`, `GET /applications/{applicationId}/ai-answer`

### File diubah
- `app/Clients/Application/UseCase/ApplyUseCase.php`
- `app/Infrastructure/Factory/PlatformFactory.php`
- `app/Jobs/ProcessApplications.php` (link ai_answer ke application)
- `app/Http/Controllers/ApplicationController.php`
- `app/Livewire/Traits/HasConfiguration.php` (default `auto_answer`)
- `resources/views/livewire/panel-configuration.blade.php`
- `resources/views/livewire/application-table.blade.php`
- `routes/web.php`

### Setup di lokal user
```bash
php artisan migrate
```
Migrations baru:
- `application_ai_answers` table
- ALTER enum status pada `applications` untuk menerima `auto_answer_failed`

Pastikan API key OpenRouter sudah di-set di Settings → AI Provider (per-user override), atau di `.env` `OPENROUTER_API_KEY`.

### Belum diimplementasi (deferred)
- Jobstreet auto-answer — QuestionnaireParser masih random. Akan dikerjakan setelah Glints stabil.
- Preview jawaban sebelum submit (saat ini AI langsung isi & submit).
- Edit manual jawaban AI.
- P2 — Export riwayat ke PDF/Markdown
- P2 — Analytics: tren skor kecocokan seiring waktu, insight kategori terlemah
