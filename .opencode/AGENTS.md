# OpenCode Agent Configuration — RPS OBE

## Deskripsi Proyek

**RPS OBE** (Rencana Pembelajaran Semester — Outcome Based Education) adalah platform pintar berbasis web untuk membantu institusi pendidikan tinggi di Indonesia dalam merancang, mengelola, mengevaluasi, dan mengekspor dokumen RPS secara kolaboratif sesuai standar OBE nasional. Platform mendukung **multi-tenant** per universitas dengan isolasi data ketat, alur kerja (workflow) persetujuan, dan AI engine untuk rekomendasi penyusunan RPS.

## Technology Stack

| Layer       | Komponen                                                                 |
|-------------|---------------------------------------------------------------------------|
| Backend     | PHP 8.3, Laravel 13, Livewire 3, Volt (functional API)                   |
| Frontend    | Tabler UI (HTML/CSS), Alpine.js (via Livewire)                           |
| Database    | MariaDB 10.11                                                            |
| Auth & ACL  | Laravel Breeze/Fortify-inspired custom auth, Spatie Laravel Permission   |
| AI          | OpenAI API (GPT-4o)                                                      |
| Dokumen     | PHPWord (docx), DomPDF (pdf)                                             |
| Testing     | Pest (unit & feature), Laravel Dusk (browser/E2E)                        |
| Tooling     | Composer, NPM, Vite, Pint (code style), Rector (refactor)                |

## Konvensi Kode

- **PSR-12** — semua kode PHP wajib mengikuti standar PSR-12, di-enforce via Laravel Pint.
- **Tidak ada komentar** — kecuali benar-benar diperlukan untuk menjelaskan logika bisnis kompleks.
- **Constructor Injection** — gunakan dependency injection via constructor; hindari pemanggilan `app()`, `resolve()`, atau facade di method body.
- **Service Classes** — logika bisnis kompleks diletakkan di `app/Services/NamaService.php`.
- **Action Classes** — operasi single-responsibility ditempatkan di `app/Actions/NamaDomain/NamaAction.php`.
- **DTOs** — object transfer data ditempatkan di `app/DTO/NamaDto.php`, immutable, menggunakan `readonly` properties.
- **Enums** — semua enumerasi menggunakan PHP 8.3 native enums (`app/Enums/NamaEnum.php`).
- **Otorisasi** — seluruh permission check menggunakan Spatie Permission; tidak menggunakan Gate/Policy manual.
- **Validasi** — gunakan Form Request classes (`php artisan make:request NamaRequest`).
- **Naming** — nama class: PascalCase, method/fungsi: camelCase, konstanta: UPPER_SNAKE_CASE, tabel: snake_case plural.

## Konvensi Direktori

```
app/
  Actions/            # Action classes (single responsibility)
  Contracts/          # Interfaces
  DTO/                # Data Transfer Objects
  Enums/              # PHP 8.3 native enums
  Exceptions/         # Custom exceptions
  Http/
    Controllers/      # Route controllers (tipis, hanya delegasi)
    Middleware/       # Custom middleware (TenantMiddleware, etc.)
    Requests/         # Form Request validators
  Livewire/           # Livewire Volt components (functional API)
    Auth/             # Login, Register, ForgotPassword, etc.
    Dashboard/        # Dashboard per role
    MasterData/       # CRUD master data
    RpsBuilder/       # RPS builder wizard
    Workflow/         # Workflow approval
  Models/             # Eloquent models
  Services/           # Service classes (business logic)
  Integrations/       # OpenAI, external API integrations
database/
  seeders/            # Database seeders
  migrations/         # Migrations
resources/
  views/
    livewire/         # Blade views untuk Livewire components
    components/       # Shared Blade components
routes/
  web.php             # Web routes
tests/
  Unit/               # Pest unit tests
  Feature/            # Pest feature tests
  Browser/            # Laravel Dusk E2E tests
```

## Testing

- **Framework**: Pest (unit & feature), Laravel Dusk (browser)
- **Target coverage**: 80%+ pada kode backend (models, services, actions, DTOs, enums)
- **Konvensi test**: gunakan `describe()` blocks untuk grouping, `it()` untuk assertions; gunakan RefreshDatabase trait
- **CI**: semua test wajib lulus sebelum merge ke `main`

---

## Agent Definitions

### Agent 1: Builder — Scaffold & Core

**Tujuan**: Inisialisasi project, scaffolding modul, menjalankan perintah artisan, dan membangun fondasi sistem.

**Tools**: Bash, Write, Edit, Glob, Grep

**Instruksi**:
- Jalankan `php artisan make:model`, `make:migration`, `make:seeder`, `make:enum`, `make:dto`, `make:service`, `make:action` sesuai kebutuhan modul.
- Gunakan `pint` untuk auto-fix code style setelah setiap batch perubahan file.
- Selalu jalankan `php artisan migrate --pretend` atau `migrate:status` untuk verifikasi migrasi sebelum eksekusi.
- Login ke aplikasi via Dusk/Pest untuk verifikasi Auth sebelum membangun modul dependen.

---

### Agent 2: Coder — Business Logic & Livewire

**Tujuan**: Menulis logika bisnis di Service/Action/DTO/Enum classes, serta komponen Livewire Volt.

**Tools**: Write, Edit, Read, Grep, Glob

**Instruksi**:
- Gunakan functional API Volt (`use function Livewire\Volt\{state, computed, rules, on, mount}`).
- Setiap komponen Livewire wajib memiliki validasi properti via `rules()`.
- Gunakan Spatie Permission `@can` directive di Blade dan `$this->authorize()` di component.
- Semua query database menggunakan Eloquent; jangan gunakan Query Builder atau raw SQL kecuali benar-benar diperlukan.
- Implementasikan pagination, sorting, dan search di setiap komponen list/table.
- Gunakan Toast/flash message untuk feedback pengguna setelah operasi CRUD.

---

### Agent 3: Reviewer — Code Quality & Testing

**Tujuan**: Menulis test (Pest/Dusk), mereview kode terhadap konvensi, memastikan coverage target.

**Tools**: Read, Glob, Grep, Bash, Write, Edit

**Instruksi**:
- Tulis test Pest untuk setiap Service, Action, dan DTO yang dibuat Agent Coder.
- Tulis test Dusk untuk setiap alur pengguna (happy path + error states).
- Verifikasi bahwa semua Spatie Permission check memiliki test coverage.
- Pastikan semua migrasi memiliki `down()` method yang reversible.
- Verifikasi bahwa tidak ada N+1 query problem — gunakan `preventLazyLoading()` di development.
- Jalankan `php artisan test --coverage` setelah setiap batch dan laporkan hasilnya.

---

### Agent 4: Architect — Design Review & Refactor

**Tujuan**: Meninjau arsitektur, memastikan Dependency Inversion, memisahkan abstraksi dan implementasi.

**Tools**: Read, Glob, Grep, Write, Edit

**Instruksi**:
- Pastikan semua dependensi antar modul mengalir satu arah (unidirectional dependency).
- Pastikan Service classes tidak bergantung langsung pada Eloquent models — gunakan Contracts/Interfaces jika perlu.
- Review bahwa setiap Action class hanya memiliki satu public method (`execute` atau `handle`).
- Identifikasi potensi ekstraksi ke package terpisah (misal: workflow engine, AI engine).
- Validasi bahwa tidak ada logika bisnis di Controller atau Blade view.

---

### Agent 5: Full Stack — Frontend, UI & Integration

**Tujuan**: Membangun UI dengan Tabler, menulis Blade views, mengintegrasikan Livewire dengan frontend.

**Tools**: Write, Edit, Read, Glob, Grep, Bash

**Instruksi**:
- Semua komponen UI menggunakan komponen Tabler (`x-tabler::card`, `x-tabler::button`, dll).
- Gunakan Blade components untuk reusable UI elements (`<x-button>`, `<x-modal>`, `<x-table>`, `<x-badge>`).
- Styling harus mobile-responsive; semua tabel harus memiliki horizontal scroll pada mobile.
- Form harus memiliki error states dengan pesan berbahasa Indonesia.
- Gunakan Alpine.js untuk interaksi client-side ringan (toggle modal, dropdown, tabs).
- Pastikan aksesibilitas dasar (label pada form, aria attributes pada modal).
