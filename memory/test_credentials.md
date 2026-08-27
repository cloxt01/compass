# Test Credentials

## Test User (Local Dev - Compass Laravel)
- **Email**: `test-ai-profile@example.com`
- **Password**: `password123`
- **Purpose**: Testing Settings → AI Profile form (add skill/sertifikasi/bahasa buttons)
- **DB**: SQLite at `/app/database/testing.sqlite` (seeded via PackageSeeder + tinker)

## App URL
- Local artisan serve: `http://localhost:8000`
- Login: `http://localhost:8000/login`
- AI Profile tab: `http://localhost:8000/settings?tab=ai-profile`

## AI Config (for auto-answer flow)
- OpenRouter API key belum di-set (mocked di test via `Http::fake`).
- Default model: `deepseek/deepseek-v4-flash`
