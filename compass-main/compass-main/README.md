# Compass

> Platform otomasi pencarian dan pengelolaan lamaran kerja berbasis web.

Compass mengotomatisasi workflow pencarian lowongan, pengelolaan akun job provider, proses aplikasi, dan monitoring aktivitas otomasi — semuanya dalam satu dashboard. Dibangun dengan Laravel sebagai platform yang terstruktur, terukur, dan mudah dikembangkan.

**Repository:** [github.com/cloxt01/compass](https://github.com/cloxt01/compass)

**Author:** [Cloxt01](https://github.com/cloxt01)

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Arsitektur](#arsitektur)
- [Tech Stack](#tech-stack)
- [Struktur Project](#struktur-project)
- [Automation Workflow](#automation-workflow)
- [Scheduler](#scheduler)
- [Billing Workflow](#billing-workflow)
- [Rate Limiting](#rate-limiting)
- [Getting Started](#getting-started)
- [Environment Variables](#environment-variables)
- [Development](#development)
- [Testing](#testing)
- [Deployment](#deployment)
- [Security](#security)
- [Roadmap](#roadmap)
- [License](#license)

---

## Fitur Utama

### 🔄 Automation
- Proses otomasi terjadwal (scheduled jobs)
- Manajemen round otomasi (create, pause, resume, stop)
- Pencarian lowongan berbasis konfigurasi akun
- Pemrosesan lowongan bertahap menggunakan chunking
- Rate limiting untuk mencegah penggunaan API berlebihan

### 📋 Job Application
- Pengelolaan daftar lowongan yang ditemukan
- Pencatatan status proses lamaran
- Relasi lowongan ↔ akun provider
- Histori aktivitas otomasi

### 🔌 Provider Integration
Provider yang didukung saat ini:

| Provider | Status |
|---|---|
| Glints | ✅ Aktif |
| JobStreet | ✅ Aktif |

Struktur provider dirancang modular sehingga integrasi platform baru dapat ditambahkan tanpa mengubah keseluruhan sistem.

### 📊 Dashboard
Menyediakan visibilitas real-time terhadap:
- Status automation & aktivitas pencarian lowongan
- Proses aplikasi dan akun provider
- Status sistem dan penggunaan layanan

### 💳 Billing
Sistem billing untuk mengelola paket layanan dan langganan:
- Manajemen paket layanan & status subscription
- Renewal otomatis dengan grace period
- Integrasi payment gateway: **Midtrans** & **Xendit**

### 🤖 AI Usage & Limiting

Pembatasan penggunaan fitur AI berdasarkan paket layanan:

| Paket | Harga | Limit Harian | Limit Bulanan |
|---|---:|---:|---:|
| Free | Rp0 | 5 | 50 |
| Starter | Rp15.000 | — | — |
| Pro | Rp45.000 | — | — |
| Premium | Rp99.000 | — | — |

> Limit dapat disesuaikan melalui konfigurasi aplikasi (`.env` / config file).

---

## Arsitektur

```
User
  │
  ▼
Web Dashboard
  │
  ├─────────────────┬─────────────────┐
  ▼                 ▼
Application      Automation       Billing
Management        Scheduler      Scheduler
                     │
                     ▼
                Search Jobs
                     │
                     ▼
                Job Provider
                /          \
               ▼            ▼
           Glints       JobStreet
               │
               ▼
        Job Applications
```

Workflow automation dijalankan secara terjadwal dan dikelola melalui Laravel Scheduler.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP, Laravel |
| Database | MySQL / MariaDB |
| Frontend | Blade, JavaScript |
| API | REST API |
| Background Jobs | Laravel Queue & Scheduler |
| Payment | Midtrans, Xendit |
| Version Control | Git |

---

## Struktur Project

```
compass/
├── app/
│   ├── Console/
│   ├── Http/
│   ├── Models/
│   ├── Services/
│   └── ...
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
└── README.md
```

---

## Automation Workflow

```
Scheduler
   │
   ▼
Create Round
   │
   ▼
Load Job Accounts
   │
   ▼
Search Jobs
   │
   ▼
Process Jobs (chunked)
   │
   ▼
Create Applications
   │
   ▼
Update Status
```

Job diproses secara bertahap menggunakan chunking untuk mengurangi beban sistem dan mengontrol jumlah request.

---

## Scheduler

Beberapa proses sistem dijalankan melalui Laravel Scheduler:

```
Automation Scheduler
        │
        ├── Job Search
        ├── Application Processing
        ├── Billing Renewal
        ├── Subscription Grace
        └── Subscription Expiration
```

Scheduler automation berjalan periodik, sementara proses billing memiliki scheduler terpisah untuk menangani renewal dan expiration.

---

## Billing Workflow

```
Active Subscription
        │
        ▼
     Renewal
        │
   ┌────┴────┐
   ▼         ▼
Success    Failed
   │         │
   ▼         ▼
Active    Grace Period
             │
             ▼
          Expired
```

Sistem tidak langsung mengubah subscription menjadi expired saat pembayaran gagal — terdapat grace period sebelum akses layanan dihentikan.

---

## Rate Limiting

Compass menerapkan pembatasan request pada proses automation untuk:

- Mengurangi risiko request berlebihan ke provider
- Mengontrol penggunaan resource
- Mencegah proses automation berjalan tanpa batas
- Menjaga stabilitas sistem

```
Automation
    │
    ▼
Request Counter
    │
    ├── Limit belum tercapai → Continue
    │
    └── Limit tercapai       → Stop / Exception
```

---

## Getting Started

### Prasyarat

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL / MariaDB

### Instalasi

```bash
# Clone repository
git clone https://github.com/cloxt01/compass.git
cd compass

# Install dependency
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env, lalu jalankan migration
php artisan migrate

# Build frontend
npm run build
```

### Menjalankan Aplikasi

```bash
php artisan serve
```

---

## Environment Variables

Contoh konfigurasi utama (`.env`):

```env
APP_NAME=Compass
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=compass
DB_USERNAME=root
DB_PASSWORD=
```

> Konfigurasi payment gateway dan provider disimpan melalui environment variable dan **tidak boleh** di-hardcode ke dalam kode maupun repository.

---

## Development

```bash
# Jalankan server development
php artisan serve

# Jalankan frontend dev server
npm run dev

# Jalankan scheduler (local)
php artisan schedule:work

# Jalankan queue worker
php artisan queue:work
```

---

## Testing

```bash
# Menjalankan seluruh test
php artisan test

# Atau menggunakan Pest
./vendor/bin/pest
```

Testing digunakan untuk memastikan fitur utama aplikasi tetap berjalan setelah perubahan kode.

---

## Deployment

```
Git Repository
      │
      ▼
Pull Source Code
      │
      ▼
Install Dependencies
      │
      ▼
Environment Configuration
      │
      ▼
Database Migration
      │
      ▼
Build Frontend
      │
      ▼
Start Application
      │
      ▼
Scheduler / Queue Worker (as service)
```

Pastikan scheduler dan queue worker berjalan sebagai service (mis. `systemd` atau `supervisor`) yang dikelola dengan benar pada server production.

---

## Security

Repository **tidak boleh** menyimpan:

- File `.env`
- API key / secret key / access token
- Credential database
- Private key
- Credential payment gateway

Gunakan `.env.example` sebagai template konfigurasi. Jika menemukan celah keamanan, silakan laporkan melalui issue privat atau kontak langsung ke maintainer — jangan dipublikasikan di issue publik.

---

## Roadmap

- [ ] Stabilitas automation
- [ ] Integrasi job provider tambahan
- [ ] Monitoring automation yang lebih granular
- [ ] Penyempurnaan billing & subscription
- [ ] Pengembangan fitur AI lanjutan
- [ ] Peningkatan test coverage
- [ ] Peningkatan keamanan & reliability

---

## License

Lisensi project mengikuti ketentuan yang ditetapkan oleh pemilik repository.

---

<div align="center">

Dikembangkan oleh **[Cloxt01](https://github.com/cloxt01)**

</div>
