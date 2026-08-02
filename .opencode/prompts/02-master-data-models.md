# Prompt — Module 2: Core Models & Migrations (Master Data)

## Ringkasan Konteks (PRD)

Platform RPS OBE memerlukan struktur data master yang mencerminkan hierarki institusi pendidikan tinggi Indonesia: Tenant (Universitas) → Fakultas → Program Studi → Kurikulum → Mata Kuliah. Setiap entitas terkait dengan Dosen, Profil Lulusan, Capaian Pembelajaran Lulusan (CPL), dan Referensi pembelajaran. Semua data master terisolasi per tenant (`tenant_id`), mendukung soft delete, dan memiliki timestamps otomatis. Model-model ini adalah fondasi untuk seluruh modul berikutnya (RPS Builder, Workflow, Export, AI Engine).

---

## Tugas

Buat semua **Eloquent Models**, **database migrations**, dan **PHP Enums** untuk data master platform RPS OBE. Setiap model dan migrasi harus memenuhi relasi yang telah didefinisikan, menggunakan soft delete, timestamps, dan foreign key constraint yang benar. Buat juga **seeder** minimal untuk data referensi (Enum), **factory** untuk testing, dan **Form Request** validators dasar.

---

## Daftar Model & Migrasi

### 1. Tenant (Universitas)

**Model**: `app/Models/Tenant.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | Primary key |
| kode | string(20) | Kode unik tenant (contoh: "UNIV-001") |
| nama | string(255) | Nama universitas |
| alamat | text | Alamat lengkap |
| logo | string(255) | Path/URL logo (nullable) |
| domain | string(255) | Domain kustom (nullable, unique) |
| is_active | boolean | Status aktif, default true |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Relasi**:
- `hasMany(Fakultas)`
- `hasMany(User)`
- `hasMany(ProgramStudi)`
- `hasMany(Kurikulum)`

---

### 2. Fakultas

**Model**: `app/Models/Fakultas.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| tenant_id | foreignId | FK → tenants.id |
| kode | string(20) | Kode fakultas |
| nama | string(255) | Nama fakultas |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Indeks**: `unique: [tenant_id, kode]`

**Relasi**:
- `belongsTo(Tenant)`
- `hasMany(ProgramStudi)`
- `hasMany(Dosen)`

---

### 3. Program Studi

**Model**: `app/Models/ProgramStudi.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| tenant_id | foreignId | FK → tenants.id |
| fakultas_id | foreignId | FK → fakultas.id |
| kode | string(20) | Kode prodi (contoh: "S1-IF") |
| nama | string(255) | Nama program studi |
| jenjang | enum(Jenjang) | D3, S1, S2, S3 |
| akreditasi | string(10) | Nilai akreditasi (nullable) |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Indeks**: `unique: [tenant_id, kode]`

**Relasi**:
- `belongsTo(Tenant)`
- `belongsTo(Fakultas)`
- `hasMany(Kurikulum)`
- `hasMany(MataKuliah)`
- `hasMany(ProfilLulusan)`
- `hasMany(CPL)`
- `hasMany(User)` — dosen yang mengajar di prodi ini

---

### 4. Kurikulum

**Model**: `app/Models/Kurikulum.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| tenant_id | foreignId | FK → tenants.id |
| program_studi_id | foreignId | FK → program_studi.id |
| kode | string(20) | Kode kurikulum (contoh: "K-2024") |
| nama | string(255) | Nama kurikulum |
| tahun_berlaku | year | Tahun mulai berlaku |
| is_active | boolean | Status aktif, default true |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Indeks**: `unique: [tenant_id, program_studi_id, kode]`

**Relasi**:
- `belongsTo(Tenant)`
- `belongsTo(ProgramStudi)`
- `hasMany(MataKuliah)`
- `hasMany(CPL)`
- `hasMany(ProfilLulusan)`

---

### 5. Mata Kuliah

**Model**: `app/Models/MataKuliah.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| tenant_id | foreignId | FK → tenants.id |
| program_studi_id | foreignId | FK → program_studi.id |
| kurikulum_id | foreignId | FK → kurikulum.id |
| kode | string(20) | Kode MK |
| nama | string(255) | Nama mata kuliah |
| sks_teori | integer | Jumlah SKS teori, default 0 |
| sks_praktik | integer | Jumlah SKS praktik, default 0 |
| semester_wajib | integer | Semester pelaksanaan |
| is_wajib | boolean | Wajib / pilihan, default true |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Indeks**: `unique: [tenant_id, program_studi_id, kode]`

**Relasi**:
- `belongsTo(Tenant)`
- `belongsTo(ProgramStudi)`
- `belongsTo(Kurikulum)`
- `belongsToMany(Dosen)` → tabel pivot `dosen_mata_kuliah`
- `hasMany(CPL)` → melalui pivot (relasi CPL-MK)
- `hasMany(Referensi)`

---

### 6. Dosen

**Model**: `app/Models/Dosen.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| tenant_id | foreignId | FK → tenants.id |
| fakultas_id | foreignId | FK → fakultas.id |
| user_id | foreignId | FK → users.id (nullable, jika login) |
| nidn | string(10) | NIDN dosen (unique per tenant) |
| nama | string(255) | Nama dosen |
| gelar_depan | string(50) | Gelar depan (nullable) |
| gelar_belakang | string(50) | Gelar belakang (nullable) |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Indeks**: `unique: [tenant_id, nidn]`

**Relasi**:
- `belongsTo(Tenant)`
- `belongsTo(Fakultas)`
- `belongsTo(User)` — nullable, untuk integrasi login
- `belongsToMany(MataKuliah)` → pivot `dosen_mata_kuliah`
- `hasMany(CPL)` — dosen sebagai penanggung jawab CPL (nullable)

---

### 7. Profil Lulusan

**Model**: `app/Models/ProfilLulusan.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| tenant_id | foreignId | FK → tenants.id |
| program_studi_id | foreignId | FK → program_studi.id |
| kurikulum_id | foreignId | FK → kurikulum.id |
| kode | string(10) | Kode profil (contoh: "PL-01") |
| deskripsi | text | Deskripsi profil lulusan |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Indeks**: `unique: [tenant_id, program_studi_id, kode]`

**Relasi**:
- `belongsTo(Tenant)`
- `belongsTo(ProgramStudi)`
- `belongsTo(Kurikulum)`
- `belongsToMany(CPL)` → pivot `cpl_profil_lulusan`

---

### 8. CPL (Capaian Pembelajaran Lulusan)

**Model**: `app/Models/CPL.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| tenant_id | foreignId | FK → tenants.id |
| program_studi_id | foreignId | FK → program_studi.id |
| kurikulum_id | foreignId | FK → kurikulum.id |
| dosen_id | foreignId | FK → dosen.id (nullable, penanggung jawab) |
| kode | string(15) | Kode CPL (contoh: "CPL-S-01") |
| deskripsi | text | Deskripsi CPL |
| kategori | enum(CPKategori) | Sikap, Pengetahuan, Keterampilan Umum, Keterampilan Khusus |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Indeks**: `unique: [tenant_id, program_studi_id, kurikulum_id, kode]`

**Relasi**:
- `belongsTo(Tenant)`
- `belongsTo(ProgramStudi)`
- `belongsTo(Kurikulum)`
- `belongsTo(Dosen)` — nullable
- `belongsToMany(ProfilLulusan)` → pivot `cpl_profil_lulusan`
- `belongsToMany(MataKuliah)` → pivot `cpl_mata_kuliah`

---

### 9. Referensi

**Model**: `app/Models/Referensi.php`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigIncrements | |
| tenant_id | foreignId | FK → tenants.id |
| mata_kuliah_id | foreignId | FK → mata_kuliah.id |
| judul | string(255) | Judul referensi |
| penulis | string(255) | Nama penulis |
| tahun | year | Tahun terbit |
| jenis | string(50) | Buku, Jurnal, Artikel, Website, dll. |
| is_active | boolean | Default true |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | softDeletes | |

**Relasi**:
- `belongsTo(Tenant)`
- `belongsTo(MataKuliah)`

---

### 10. User (Update)

**Model**: `app/Models/User.php` (modifikasi dari Module 1)

| Kolom Tambahan | Tipe | Keterangan |
|----------------|------|------------|
| tenant_id | foreignId | FK → tenants.id |
| program_studi_id | foreignId | FK → program_studi.id (nullable) |
| avatar | string(255) | Path avatar (nullable) |

**Relasi Tambahan**:
- `belongsTo(Tenant)`
- `belongsTo(ProgramStudi)` — nullable

---

## Tabel Pivot

### `dosen_mata_kuliah`
| Kolom | Tipe |
|-------|------|
| dosen_id | foreignId → dosen.id |
| mata_kuliah_id | foreignId → mata_kuliah.id |
| created_at | timestamp |

**Primary key**: composite `[dosen_id, mata_kuliah_id]`

### `cpl_profil_lulusan`
| Kolom | Tipe |
|-------|------|
| cpl_id | foreignId → cpl.id |
| profil_lulusan_id | foreignId → profil_lulusan.id |
| created_at | timestamp |

**Primary key**: composite `[cpl_id, profil_lulusan_id]`

### `cpl_mata_kuliah`
| Kolom | Tipe |
|-------|------|
| cpl_id | foreignId → cpl.id |
| mata_kuliah_id | foreignId → mata_kuliah.id |
| created_at | timestamp |

**Primary key**: composite `[cpl_id, mata_kuliah_id]`

---

## Enum Classes yang Dibutuhkan

Semua enum ditempatkan di `app/Enums/`.

### `CPKategori.php`
```php
enum CPKategori: string {
    case Sikap = 'sikap';
    case Pengetahuan = 'pengetahuan';
    case KeterampilanUmum = 'keterampilan_umum';
    case KeterampilanKhusus = 'keterampilan_khusus';
}
```

### `Jenjang.php`
```php
enum Jenjang: string {
    case D3 = 'D3';
    case S1 = 'S1';
    case S2 = 'S2';
    case S3 = 'S3';
}
```

### `SemesterTipe.php`
```php
enum SemesterTipe: string {
    case Ganjil = 'ganjil';
    case Genap = 'genap';
}
```

### `RPSStatus.php`
```php
enum RPSStatus: string {
    case Draft = 'draft';
    case Review = 'review';
    case Revision = 'revision';
    case Approved = 'approved';
    case Published = 'published';
    case Archived = 'archived';
}
```

### `RoleEnum.php` (dari Module 1)
```php
enum RoleEnum: string {
    case SuperAdmin = 'SuperAdmin';
    case Admin = 'Admin';
    case Dosen = 'Dosen';
    case Reviewer = 'Reviewer';
}
```

---

## Persyaratan Implementasi

### Migrations
- Semua foreign key menggunakan `->constrained()` cascade on delete (`cascadeOnDelete()` untuk relasi dependent, `nullOnDelete()` untuk nullable FK).
- Semua tabel memiliki indeks di `tenant_id`.
- Gunakan `$table->unique([...])` untuk composite unique constraints.
- Gunakan `$table->softDeletes()` di semua tabel.
- Urutan migrasi harus benar agar FK constraint tidak gagal — tentukan `->after()` jika diperlukan.
- Gunakan enum kolom: `$table->enum('jenjang', array_column(Jenjang::cases(), 'value'))`.

### Models
- Setiap model memiliki `$fillable` atau `$guarded` yang sesuai.
- Setiap model memiliki `$casts` untuk kolom enum, boolean, datetime.
- Setiap model menggunakan `SoftDeletes` trait.
- Setiap model menggunakan `belongsTo(Tenant::class)` sebagai default scope; implementasi dapat menggunakan global scope `TenantScope` (dibuat di Module 3).
- Semua relasi harus didefinisikan dengan tipe return yang benar (PHP 8.3 typed properties/methods).

### Seeders & Factories
- Buat `TenantFactory` — minimal 1 data dummy.
- Buat `FakultasFactory` — minimal 3 data dummy per tenant.
- Buat `ProgramStudiFactory` — minimal 2 data dummy per fakultas.
- Buat `KurikulumFactory` — minimal 1 data dummy per prodi.
- Buat `DosenFactory` — minimal 5 data dummy per tenant.
- Buat `MataKuliahFactory` — minimal 8 data dummy per prodi.
- Semua factory menggunakan `fake()` untuk data random.

### Form Requests (Opsional untuk Modul Ini)
- `app/Http/Requests/TenantRequest.php`
- `app/Http/Requests/FakultasRequest.php`
- `app/Http/Requests/ProgramStudiRequest.php`

### Testing Minimal (Opsional untuk Modul Ini — detail di Module 3-4)
- Setiap model lulus uji `php artisan test --filter=ModelTest` dengan assertions: dapat membuat record, relasi berfungsi, soft delete berfungsi, unique constraint berfungsi.

---

## Acceptance Criteria

1. [ ] Semua model (10 model + 3 pivot) dan migrasi terbentuk tanpa error saat `php artisan migrate`.
2. [ ] Semua foreign key constraint terdefinisi dengan benar dan cascade rule sesuai.
3. [ ] Setiap model memiliki relasi `belongsTo`, `hasMany`, dan `belongsToMany` yang benar sesuai spesifikasi di atas.
4. [ ] Semua tabel memiliki kolom `tenant_id`, `created_at`, `updated_at`, `deleted_at`.
5. [ ] Composite unique index berfungsi (contoh: tidak bisa insert kode fakultas yang sama dalam satu tenant).
6. [ ] Enum CPKategori, Jenjang, SemesterTipe, RPSStatus, dan RoleEnum terdefinisi sebagai PHP 8.3 native enum dengan method `labels()` untuk UI dan `values()` untuk form.
7. [ ] `php artisan db:seed` berhasil menjalankan semua seeder tanpa error.
8. [ ] Semua factory menghasilkan data dummy yang valid.
9. [ ] Semua model memiliki PHPDoc `@property` yang sesuai untuk properti dari `$casts` dan `$fillable`.
10. [ ] Migrasi dapat di-rollback (`php artisan migrate:rollback`) tanpa error.

---

## Tips

- Gunakan perintah `php artisan make:model NamaModel -mfs` untuk membuat model, migration, factory, dan seeder sekaligus.
- Gunakan perintah `php artisan make:enum NamaEnum` jika tersedia (atau buat manual).
- Definisikan relasi di model **sebelum** menulis migration, agar tidak ada inkonsistensi.
- Urutkan migration berdasarkan dependensi: `tenants → fakultas → program_studi → kurikulum → dosen → mata_kuliah → cpl → profil_lulusan → referensi → pivot tables`.
- Untuk enum, tambahkan trait `App\Enums\HasLabel` (jika dibutuhkan) untuk konversi label Bahasa Indonesia, misalnya: `Sikap`, `Pengetahuan`, `Keterampilan Umum`, `Keterampilan Khusus`.
- Gunakan `php artisan migrate:fresh --seed` untuk reset database dan test dari awal.
- Pertimbangkan untuk membuat `TenantScope` global scope di model yang otomatis menambahkan `where('tenant_id', auth()->user()->tenant_id)` — namun implementasikan penuh di Module 3.
