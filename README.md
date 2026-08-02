<h4 align="center">Rencana Pembelajaran Semester</h4>
<h4 align="center">Smart Outcome Based Education Platform</h4>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Livewire-3.x-FB70A9?logo=livewire&logoColor=white" alt="Livewire">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MariaDB-10.11-003545?logo=mariadb&logoColor=white" alt="MariaDB">
  <img src="https://img.shields.io/badge/Redis-7.x-DC382D?logo=redis&logoColor=white" alt="Redis">
  <img src="https://img.shields.io/badge/OpenAI-GPT--4o-412991?logo=openai&logoColor=white" alt="OpenAI">
  <img src="https://img.shields.io/badge/License-Proprietary-red" alt="License">
</p>

<p align="center">
  <a href="#tentang">Tentang</a> &bull;
  <a href="#fitur-utama">Fitur</a> &bull;
  <a href="#tech-stack">Tech Stack</a> &bull;
  <a href="#arsitektur">Arsitektur</a> &bull;
  <a href="#instalasi">Instalasi</a> &bull;
  <a href="#struktur-proyek">Struktur</a> &bull;
  <a href="#dokumentasi">Dokumentasi</a> &bull;
  <a href="#lisensi">Lisensi</a>
</p>

---

## Tentang

**RPS OBE** adalah platform SaaS berbasis web yang membantu perguruan tinggi di Indonesia dalam menyusun, memvalidasi, mereview, mengelola, dan mempublikasikan **Rencana Pembelajaran Semester (RPS)** berbasis **Outcome Based Education (OBE)**.

Platform ini dirancang untuk menjawab kebutuhan perguruan tinggi dalam beradaptasi dengan standar akreditasi nasional (BAN-PT, LAM) dan internasional yang mensyaratkan pendekatan OBE dalam kurikulum — sekaligus mempercepat penyusunan RPS hingga **70% lebih cepat** melalui bantuan AI.

### Mengapa RPS OBE?

- **OBE-Native** — Dirancang khusus untuk OBE, bukan tool general-purpose
- **AI-Augmented** — Generate, validasi, dan review RPS dengan bantuan AI (GPT-4o)
- **Collaborative** — Workflow review dan approval terintegrasi
- **Akreditasi-Ready** — RPS siap untuk BAN-PT, LAM, dan akreditasi internasional
- **Multi-Tenant** — Satu platform untuk banyak universitas dengan isolasi data penuh

---

## Fitur Utama

| Modul | Fitur |
|-------|-------|
| **Autentikasi** | Login, Register via invitation, Forgot password, Email verification, RBAC Spatie Permission (9 role, 40+ permission) |
| **Master Data** | CRUD Universitas, Fakultas, Program Studi, Kurikulum, Semester, Mata Kuliah, Dosen, Profil Lulusan, CPL, Referensi |
| **RPS Builder** | Wizard 8 langkah: Info MK -> Pilih CPL -> CPMK -> Sub-CPMK -> Materi -> Metode -> Assessment -> Review |
| **Mapping** | CPL -> CPMK -> Sub-CPMK -> Assessment (Constructive Alignment) |
| **AI Assistant** | Generate CPMK, Sub-CPMK, materi, assessment, rubrik, referensi, learning outcome, aktivitas pembelajaran |
| **AI Validator** | Periksa 8 aspek: Taksonomi Bloom, Alignment, Jumlah CPMK, Pertemuan, Assessment, Bobot, Referensi, Konsistensi |
| **AI Reviewer** | Skor otomatis, komentar per komponen, saran perbaikan spesifik |
| **Workflow** | Draft -> Review -> Revision -> Approved -> Published -> Archived + reviewer assignment + history |
| **Dashboard** | 6 dashboard per role: Dosen, Kaprodi, Fakultas, Universitas, LPM, Super Admin |
| **Reporting** | Statistik, grafik, filter, export Excel & PDF, laporan akreditasi |
| **Export** | RPS ke Word (.docx) via PHPWord dan PDF via DomPDF, template kustom per universitas |
| **Notifikasi** | Email + in-app notification center, real-time badge counter |
| **Versioning** | Auto-version setiap submit review, history, diff, rollback |
| **Audit Log** | Seluruh aktivitas tercatat dengan old/new values, IP address, tidak dapat dihapus |
| **Template** | Template RPS default SN-DIKTI + kustom per universitas |

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Framework** | Laravel 13 |
| **Frontend** | Livewire 3 + Volt (functional API) |
| **UI Kit** | Tabler.io |
| **Database** | MariaDB 10.11 |
| **Cache & Queue** | Redis 7.x |
| **Auth** | Laravel Sanctum + Spatie Permission (RBAC) |
| **AI** | OpenAI API (GPT-4o) |
| **Export** | PHPWord (.docx), DomPDF (.pdf), Maatwebsite Excel (.xlsx) |
| **Assets** | Vite + Tailwind CSS |
| **Testing** | Pest (unit/feature), Laravel Dusk (browser E2E) |

---

## Arsitektur

```
+---------------------------------------------------------+
|                    PRESENTATION LAYER                    |
|  Tabler UI  <->  Livewire 3 / Volt  <->  Blade Views     |
+---------------------------------------------------------+
|                   APPLICATION LAYER                      |
|  Controllers  <->  Services  <->  Actions  <->  Jobs      |
+---------------------------------------------------------+
|                     DOMAIN LAYER                         |
|  Models  <->  Enums  <->  DTOs  <->  Events & Listeners   |
+---------------------------------------------------------+
|                  INFRASTRUCTURE LAYER                    |
|  MariaDB  <->  Redis  <->  Queue  <->  Storage  <->  AI    |
+---------------------------------------------------------+
```

---

## Instalasi

### Prasyarat

- PHP 8.4+
- Composer 2.x
- MariaDB 10.11+ atau MySQL 8.0+
- Redis 7.x (opsional untuk production)
- Node.js 20+ & npm

### Langkah

```bash
# 1. Clone repository
git clone https://github.com/singkong/rpsobe.git
cd rpsobe

# 2. Install dependensi PHP
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=rps_obe
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Install dependensi frontend
npm install

# 8. Build assets
npm run build

# 9. Jalankan development server
php artisan serve

# 10. (Production) Konfigurasi queue worker
php artisan queue:work redis --queue=default,export,ai --tries=3
```

### Akun Demo

Setelah menjalankan seeder, tersedia akun demo:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@rpsobe.id` | `password` |
| Admin Universitas | `admin@univ.ac.id` | `password` |
| Kaprodi | `kaprodi@univ.ac.id` | `password` |
| Dosen | `dosen@univ.ac.id` | `password` |
| Reviewer | `reviewer@univ.ac.id` | `password` |

> **Peringatan:** Ubah semua password setelah instalasi untuk production.

### Konfigurasi AI (Optional)

```env
# .env
OPENAI_API_KEY=sk-xxxx
OPENAI_ORGANIZATION=org-xxxx
OPENAI_MODEL=gpt-4o
AI_RATE_LIMIT=20
AI_BUDGET_MONTHLY=500000
```

---

## Struktur Proyek

```
rps-obe/
├── app/
│   ├── Actions/Workflow/        # Action classes untuk workflow
│   ├── Enums/                   # 7 enum (Role, RPSStatus, CPKategori, dll)
│   ├── Events/                  # 7 event workflow
│   ├── Exports/                 # Maatwebsite Excel exports
│   ├── Helpers/                 # Helper functions global
│   ├── Http/
│   │   ├── Controllers/         # Auth, Export, Report controllers
│   │   └── Middleware/          # Tenant, user active, redirect auth
│   ├── Jobs/                    # Queue jobs (Word, PDF, Batch export)
│   ├── Listeners/               # Audit log listener, notification listener
│   ├── Livewire/                # 50+ Volt components
│   │   ├── Auth/                # Login, Register, Forgot/Reset Password
│   │   ├── Dashboard/           # 6 role-specific dashboards
│   │   ├── MasterData/          # 9 CRUD components
│   │   ├── RPS/Builder/         # Wizard + 8 step components
│   │   ├── RPS/Workflow/        # Review, Approval, History, Assignment
│   │   ├── Export/              # Export button component
│   │   ├── Notification/        # Notification center + list
│   │   ├── Reporting/           # Report index, completion, quality
│   │   └── Audit/               # Audit log viewer
│   ├── Mail/                    # RPS mailables, invitation
│   ├── Models/                  # 17 Eloquent models
│   ├── Notifications/           # 8 Laravel notification classes
│   ├── Observers/               # RPS model observer
│   ├── Providers/               # App, Event, Trait service providers
│   ├── Services/                # 8 service classes
│   │   ├── AI/                  # (future) AI Gateway, Assistant, Validator
│   │   ├── RPSService.php       # RPS CRUD + wizard orchestration
│   │   ├── WorkflowService.php  # Workflow state machine
│   │   ├── WordExportService.php
│   │   ├── PDFExportService.php
│   │   ├── DashboardService.php
│   │   ├── ReportingService.php
│   │   ├── NotificationService.php
│   │   └── RPSValidationService.php
│   └── Traits/                  # BelongsToTenant, Auditable
├── config/                      # 16 config files
├── database/
│   ├── migrations/              # 27 tabel + 5 pivot
│   └── seeders/                 # 5 seeders
├── docs/                        # Dokumentasi lengkap
│   ├── README.md                # Indeks PRD
│   ├── IMPLEMENTATION_PLAN.md   # Urutan pembangunan
│   ├── prd/                     # 50 bab PRD
│   ├── diagram/                 # 8 diagram Mermaid
│   ├── api/                     # Spesifikasi API
│   ├── database/                # Skema database
│   └── adr/                     # Architecture Decision Records
├── resources/views/             # 50+ Blade views
├── routes/                      # web.php, api.php, console.php
└── tests/                       # Unit + Feature
```

---

## Dokumentasi

Dokumentasi lengkap tersedia di direktori [`docs/`](docs/):

| Dokumen | Deskripsi |
|---------|-----------|
| [PRD Index](docs/README.md) | Indeks 50 bab Product Requirements Document |
| [PRD Bab 01-50](docs/prd/) | Product Requirements Document lengkap |
| [Implementation Plan](docs/IMPLEMENTATION_PLAN.md) | Urutan pembangunan 10 modul |
| [Diagram](docs/diagram/) | Use case, flowchart, workflow, sequence, component, deployment, arsitektur |
| [API](docs/api/) | Spesifikasi 40+ endpoint REST API |
| [Database](docs/database/) | Skema 27+ tabel dengan relasi |
| [ADR](docs/adr/) | 8 Architecture Decision Records |

---

## Lisensi

**Proprietary.** Hak cipta dilindungi. Tidak untuk didistribusikan tanpa izin tertulis.

---

<p align="center">
  <sub>Dibangun dengan ❤️ untuk pendidikan Indonesia</sub>
</p>
