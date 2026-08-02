# Skema Database RPS OBE

Dokumentasi lengkap skema database untuk aplikasi Rencana Pembelajaran Semester (RPS) berbasis Outcome-Based Education (OBE).

## Deskripsi ERD (Entity-Relationship Diagram)

Sistem RPS OBE menggunakan struktur multi-tenant dengan `tenant_id` sebagai pembatas data antar universitas. Berikut adalah gambaran relasi antar entitas dalam bentuk naratif:

### Bagian 1: Tenant dan User Management

- `tenants` sebagai root entity yang merepresentasikan satu universitas (1 tenant = 1 universitas).
- Setiap `tenant` memiliki banyak `users`, `fakultas`, dan seluruh data master lainnya.
- `users` memiliki role dan permission melalui sistem Spatie: `model_has_roles` dan `model_has_permissions` menghubungkan `users` ke `roles` dan `permissions`.
- Tabel `invitations` menyimpan data undangan user yang belum mendaftar.

### Bagian 2: Struktur Akademik

- `tenants` (1) ---< (N) `fakultas`: Satu universitas memiliki banyak fakultas.
- `fakultas` (1) ---< (N) `program_studi`: Satu fakultas membawahi banyak program studi.
- `program_studi` (1) ---< (N) `kurikulum`: Satu prodi dapat memiliki beberapa versi kurikulum.
- `program_studi` (1) ---< (N) `mata_kuliah`: Satu prodi memiliki banyak mata kuliah.
- `program_studi` (1) ---< (N) `profil_lulusan`: Satu prodi mendefinisikan beberapa profil lulusan.
- `program_studi` (1) ---< (N) `cpl`: Satu prodi menetapkan beberapa Capaian Pembelajaran Lulusan.

### Bagian 3: OBE Core

- `cpl` >---< `profil_lulusan` melalui pivot `profil_lulusan_cpl`: Relasi many-to-many antara CPL dan profil lulusan.
- `mata_kuliah` (1) ---< (N) `rps`: Satu mata kuliah dapat memiliki beberapa versi RPS (per semester/kurikulum).
- `dosen` >---< `mata_kuliah` melalui pivot `mata_kuliah_dosen`: Relasi many-to-many dosen pengampu mata kuliah.
- `rps` >---< `cpl` melalui pivot `rps_cpl`: Relasi many-to-many CPL yang didukung RPS.
- `rps` (1) ---< (N) `cpml`: Satu RPS memiliki beberapa CPMK (Capaian Pembelajaran Mata Kuliah).
- `cpml` (1) ---< (N) `sub_cpmk`: Satu CPMK dijabarkan menjadi beberapa Sub-CPMK.
- `rps` (1) ---< (N) `materi_pertemuan`: Satu RPS memiliki materi per pertemuan.
- `rps` (1) ---< (N) `assessment`: Satu RPS memiliki beberapa komponen assessment.
- `assessment` >---< `sub_cpmk` melalui pivot `assessment_sub_cpmk`: Relasi many-to-many assessment terhadap Sub-CPMK.

### Bagian 4: Metode dan Referensi

- `metode_pembelajaran` adalah tabel lookup yang dirujuk oleh `materi_pertemuan`.
- `referensi` adalah tabel pustaka/acuan yang dirujuk oleh `rps`.

### Bagian 5: Workflow dan Versioning

- `rps` (1) ---< (N) `rps_versions`: Setiap perubahan RPS disimpan sebagai versi.
- `rps` (1) ---< (N) `rps_reviews`: Satu RPS dapat direview beberapa kali.
- `rps` (1) ---< (N) `rps_approvals`: Satu RPS melalui beberapa tahap approval.

### Bagian 6: Supporting

- `notifications` menyimpan notifikasi untuk user (review, approval, reminder, dll).
- `audit_logs` mencatat setiap aktivitas user pada sistem (login, CRUD, workflow).
- `templates` menyimpan template dokumen (Word/PDF) untuk export RPS.
- `semesters` adalah tabel lookup periode akademik.
- `ai_generation_logs` mencatat riwayat penggunaan fitur AI (generate CPMK, Sub-CPMK, validasi, review).

### Diagram Relasi Ringkas

```
tenants
 ├── users ── model_has_roles ── roles
 ├── users ── model_has_permissions ── permissions
 ├── invitations
 ├── notifications
 ├── audit_logs
 ├── templates
 ├── fakultas
 │    └── program_studi
 │         ├── kurikulum
 │         ├── mata_kuliah ── mata_kuliah_dosen ── dosen
 │         │    └── rps ── rps_cpl ── cpl ── profil_lulusan_cpl ── profil_lulusan
 │         │         ├── cpml ── sub_cpmk ── assessment_sub_cpmk ── assessment
 │         │         ├── materi_pertemuan
 │         │         ├── rps_versions
 │         │         ├── rps_reviews
 │         │         └── rps_approvals
 │         ├── cpl ── profil_lulusan_cpl ── profil_lulusan
 │         └── profil_lulusan
 ├── semesters
 ├── metode_pembelajaran
 ├── referensi
 └── ai_generation_logs
```

Keterangan:
- `──` : relasi one-to-many (arah panah menuju child)
- `──` : relasi many-to-many melalui pivot (ditampilkan dengan pivot di tengah)

---

## Daftar Tabel

### tenants

Tabel utama yang merepresentasikan satu institusi/universitas. Setiap tenant memiliki data terisolasi melalui `tenant_id` di tabel-tabel terkait.

| Kolom        | Tipe Data          | Nullable | Default             | Deskripsi                                   |
|--------------|--------------------|----------|---------------------|---------------------------------------------|
| id           | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                                 |
| kode         | VARCHAR(20)        | Tidak    | -                   | Kode unik tenant (digunakan saat login)     |
| nama         | VARCHAR(200)       | Tidak    | -                   | Nama universitas                            |
| alamat       | TEXT               | Ya       | NULL                | Alamat lengkap universitas                  |
| telepon      | VARCHAR(20)        | Ya       | NULL                | Nomor telepon                               |
| email        | VARCHAR(100)       | Ya       | NULL                | Email resmi universitas                     |
| website      | VARCHAR(200)       | Ya       | NULL                | URL website universitas                     |
| logo         | VARCHAR(255)       | Ya       | NULL                | Path file logo universitas                  |
| status       | ENUM('aktif','nonaktif','pending') | Tidak | 'pending' | Status tenant                         |
| settings     | JSON               | Ya       | NULL                | Pengaturan tenant (JSON)                    |
| created_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                             |
| updated_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir                    |
| deleted_at   | TIMESTAMP          | Ya       | NULL                | Soft delete                                 |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `tenants_kode_unique` (`kode`)
- INDEX `tenants_status_index` (`status`)

---

### users

Tabel pengguna sistem. Setiap user terikat pada satu tenant.

| Kolom           | Tipe Data          | Nullable | Default             | Deskripsi                               |
|-----------------|--------------------|----------|---------------------|-----------------------------------------|
| id              | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                             |
| tenant_id       | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants                  |
| nama            | VARCHAR(200)       | Tidak    | -                   | Nama lengkap user                       |
| email           | VARCHAR(100)       | Tidak    | -                   | Email (unik dalam tenant)               |
| email_verified_at| TIMESTAMP         | Ya       | NULL                | Waktu verifikasi email                  |
| password        | VARCHAR(255)       | Tidak    | -                   | Hash password (bcrypt)                  |
| nidn            | VARCHAR(20)        | Ya       | NULL                | Nomor Induk Dosen Nasional              |
| nip             | VARCHAR(20)        | Ya       | NULL                | Nomor Induk Pegawai                     |
| avatar          | VARCHAR(255)       | Ya       | NULL                | Path foto profil                        |
| remember_token  | VARCHAR(100)       | Ya       | NULL                | Token "remember me"                     |
| last_login_at   | TIMESTAMP          | Ya       | NULL                | Waktu login terakhir                    |
| last_login_ip   | VARCHAR(45)        | Ya       | NULL                | IP login terakhir                       |
| is_active       | TINYINT(1)         | Tidak    | 1                   | Status aktif (1=aktif, 0=nonaktif)      |
| created_at      | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                         |
| updated_at      | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir                |
| deleted_at      | TIMESTAMP          | Ya       | NULL                | Soft delete                             |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `users_tenant_email_unique` (`tenant_id`, `email`)
- INDEX `users_nidn_index` (`nidn`)
- INDEX `users_is_active_index` (`is_active`)

**Foreign Keys:**
- `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE

---

### model_has_roles

Tabel pivot Spatie Permission untuk relasi user-role.

| Kolom       | Tipe Data          | Nullable | Default | Deskripsi                            |
|-------------|--------------------|----------|---------|--------------------------------------|
| role_id     | BIGINT UNSIGNED    | Tidak    | -       | Foreign key ke roles (PK bersama)    |
| model_type  | VARCHAR(255)       | Tidak    | -       | Tipe model (App\Models\User) (PK)    |
| model_id    | BIGINT UNSIGNED    | Tidak    | -       | ID user (PK bersama)                 |
| tenant_id   | BIGINT UNSIGNED    | Ya       | NULL    | Foreign key ke tenants               |

**Indexes:**
- PRIMARY KEY (`role_id`, `model_type`, `model_id`)
- INDEX `model_has_roles_model_id_model_type_index` (`model_id`, `model_type`)

**Foreign Keys:**
- `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
- `model_has_roles_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE SET NULL

---

### model_has_permissions

Tabel pivot Spatie Permission untuk relasi user-permission langsung (tanpa role).

| Kolom         | Tipe Data          | Nullable | Default | Deskripsi                            |
|---------------|--------------------|----------|---------|--------------------------------------|
| permission_id | BIGINT UNSIGNED    | Tidak    | -       | Foreign key ke permissions (PK)      |
| model_type    | VARCHAR(255)       | Tidak    | -       | Tipe model (PK bersama)              |
| model_id      | BIGINT UNSIGNED    | Tidak    | -       | ID user (PK bersama)                 |
| tenant_id     | BIGINT UNSIGNED    | Ya       | NULL    | Foreign key ke tenants               |

**Indexes:**
- PRIMARY KEY (`permission_id`, `model_type`, `model_id`)
- INDEX `model_has_permissions_model_id_model_type_index` (`model_id`, `model_type`)

**Foreign Keys:**
- `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
- `model_has_permissions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE SET NULL

---

### roles

Tabel role dari Spatie Permission.

| Kolom       | Tipe Data          | Nullable | Default             | Deskripsi                        |
|-------------|--------------------|----------|---------------------|----------------------------------|
| id          | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                      |
| name        | VARCHAR(255)       | Tidak    | -                   | Nama role (contoh: dosen, kaprodi) |
| guard_name  | VARCHAR(255)       | Tidak    | -                   | Guard name (web)                 |
| tenant_id   | BIGINT UNSIGNED    | Ya       | NULL                | Foreign key ke tenants           |
| created_at  | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                  |
| updated_at  | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir         |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `roles_name_guard_name_unique` (`name`, `guard_name`)
- INDEX `roles_tenant_id_index` (`tenant_id`)

**Foreign Keys:**
- `roles_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE

---

### permissions

Tabel permission dari Spatie Permission.

| Kolom       | Tipe Data          | Nullable | Default             | Deskripsi                                |
|-------------|--------------------|----------|---------------------|------------------------------------------|
| id          | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                              |
| name        | VARCHAR(255)       | Tidak    | -                   | Nama permission (contoh: rps.create)     |
| guard_name  | VARCHAR(255)       | Tidak    | -                   | Guard name (web)                         |
| group       | VARCHAR(100)       | Ya       | NULL                | Pengelompokan permission                 |
| created_at  | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                          |
| updated_at  | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir                 |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `permissions_name_guard_name_unique` (`name`, `guard_name`)
- INDEX `permissions_group_index` (`group`)

---

### fakultas

Tabel data fakultas dalam satu universitas.

| Kolom           | Tipe Data          | Nullable | Default             | Deskripsi                          |
|-----------------|--------------------|----------|---------------------|------------------------------------|
| id              | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                        |
| tenant_id       | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants             |
| kode            | VARCHAR(10)        | Tidak    | -                   | Kode fakultas (unik per tenant)    |
| nama            | VARCHAR(200)       | Tidak    | -                   | Nama fakultas                      |
| dekan           | VARCHAR(200)       | Ya       | NULL                | Nama dekan                         |
| akreditasi      | VARCHAR(10)        | Ya       | NULL                | Akreditasi fakultas                |
| alamat          | TEXT               | Ya       | NULL                | Alamat fakultas                    |
| telepon         | VARCHAR(20)        | Ya       | NULL                | Nomor telepon fakultas             |
| email           | VARCHAR(100)       | Ya       | NULL                | Email fakultas                     |
| created_at      | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                    |
| updated_at      | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir           |
| deleted_at      | TIMESTAMP          | Ya       | NULL                | Soft delete                        |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `fakultas_tenant_kode_unique` (`tenant_id`, `kode`)
- INDEX `fakultas_nama_index` (`nama`)

**Foreign Keys:**
- `fakultas_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE

---

### program_studi

Tabel data program studi dalam satu fakultas.

| Kolom           | Tipe Data          | Nullable | Default             | Deskripsi                                |
|-----------------|--------------------|----------|---------------------|------------------------------------------|
| id              | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                              |
| tenant_id       | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants                   |
| fakultas_id     | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke fakultas                  |
| kode            | VARCHAR(20)        | Tidak    | -                   | Kode program studi (unik per tenant)     |
| nama            | VARCHAR(200)       | Tidak    | -                   | Nama program studi                       |
| jenjang         | ENUM('D3','D4','S1','S2','S3','Profesi','Spesialis') | Tidak | 'S1' | Jenjang pendidikan            |
| akreditasi      | VARCHAR(10)        | Ya       | NULL                | Peringkat akreditasi (A, B, C, Unggul, Baik Sekali) |
| kaprodi         | VARCHAR(200)       | Ya       | NULL                | Nama ketua program studi                 |
| gelar_lulusan   | VARCHAR(50)        | Ya       | NULL                | Gelar akademik lulusan                   |
| created_at      | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                          |
| updated_at      | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir                 |
| deleted_at      | TIMESTAMP          | Ya       | NULL                | Soft delete                              |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `program_studi_tenant_kode_unique` (`tenant_id`, `kode`)
- INDEX `program_studi_fakultas_id_index` (`fakultas_id`)

**Foreign Keys:**
- `program_studi_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `program_studi_fakultas_id_foreign` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas`(`id`) ON DELETE CASCADE

---

### kurikulum

Tabel data kurikulum. Satu program studi dapat memiliki beberapa versi kurikulum.

| Kolom              | Tipe Data          | Nullable | Default             | Deskripsi                           |
|--------------------|--------------------|----------|---------------------|-------------------------------------|
| id                 | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| tenant_id          | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants              |
| program_studi_id   | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke program_studi        |
| nama               | VARCHAR(200)       | Tidak    | -                   | Nama kurikulum                      |
| tahun              | YEAR               | Tidak    | -                   | Tahun pemberlakuan                  |
| semester_mulai     | VARCHAR(50)        | Tidak    | -                   | Semester mulai berlaku              |
| status             | ENUM('aktif','nonaktif','draft') | Tidak | 'draft'     | Status kurikulum                    |
| deskripsi          | TEXT               | Ya       | NULL                | Deskripsi kurikulum                 |
| created_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |
| deleted_at         | TIMESTAMP          | Ya       | NULL                | Soft delete                         |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `kurikulum_program_studi_id_index` (`program_studi_id`)
- INDEX `kurikulum_tahun_index` (`tahun`)
- INDEX `kurikulum_status_index` (`status`)

**Foreign Keys:**
- `kurikulum_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `kurikulum_program_studi_id_foreign` FOREIGN KEY (`program_studi_id`) REFERENCES `program_studi`(`id`) ON DELETE CASCADE

---

### semesters

Tabel lookup periode semester akademik.

| Kolom            | Tipe Data          | Nullable | Default             | Deskripsi                           |
|------------------|--------------------|----------|---------------------|-------------------------------------|
| id               | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| tenant_id        | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants              |
| kode             | VARCHAR(10)        | Tidak    | -                   | Kode semester (contoh: 20251)       |
| nama             | VARCHAR(100)       | Tidak    | -                   | Nama semester                       |
| tanggal_mulai    | DATE               | Tidak    | -                   | Tanggal mulai semester              |
| tanggal_selesai  | DATE               | Tidak    | -                   | Tanggal selesai semester            |
| status           | ENUM('aktif','nonaktif','selesai') | Tidak | 'nonaktif' | Status semester            |
| created_at       | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at       | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `semesters_tenant_kode_unique` (`tenant_id`, `kode`)
- INDEX `semesters_status_index` (`status`)

**Foreign Keys:**
- `semesters_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE

---

### mata_kuliah

Tabel data mata kuliah.

| Kolom              | Tipe Data          | Nullable | Default             | Deskripsi                               |
|--------------------|--------------------|----------|---------------------|-----------------------------------------|
| id                 | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                             |
| tenant_id          | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants                  |
| program_studi_id   | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke program_studi            |
| kurikulum_id       | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke kurikulum                |
| kode               | VARCHAR(20)        | Tidak    | -                   | Kode mata kuliah (unik per tenant)      |
| nama               | VARCHAR(200)       | Tidak    | -                   | Nama mata kuliah (Bahasa Indonesia)     |
| nama_inggris       | VARCHAR(200)       | Ya       | NULL                | Nama mata kuliah (Bahasa Inggris)       |
| sks_teori          | TINYINT UNSIGNED   | Tidak    | 0                   | Jumlah SKS teori                        |
| sks_praktik        | TINYINT UNSIGNED   | Tidak    | 0                   | Jumlah SKS praktik                      |
| semester           | TINYINT UNSIGNED   | Tidak    | -                   | Semester ke berapa MK diajarkan         |
| jenis              | ENUM('wajib','pilihan','wajib_prodi','wajib_univ') | Tidak | 'wajib' | Jenis mata kuliah         |
| deskripsi          | TEXT               | Ya       | NULL                | Deskripsi mata kuliah                   |
| prasyarat_kode     | VARCHAR(20)        | Ya       | NULL                | Kode mata kuliah prasyarat              |
| status             | ENUM('aktif','nonaktif') | Tidak | 'aktif'          | Status mata kuliah                      |
| created_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                         |
| updated_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir                |
| deleted_at         | TIMESTAMP          | Ya       | NULL                | Soft delete                             |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `mata_kuliah_tenant_kode_unique` (`tenant_id`, `kode`)
- INDEX `mata_kuliah_program_studi_id_index` (`program_studi_id`)
- INDEX `mata_kuliah_kurikulum_id_index` (`kurikulum_id`)
- INDEX `mata_kuliah_semester_index` (`semester`)
- INDEX `mata_kuliah_jenis_index` (`jenis`)

**Foreign Keys:**
- `mata_kuliah_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `mata_kuliah_program_studi_id_foreign` FOREIGN KEY (`program_studi_id`) REFERENCES `program_studi`(`id`) ON DELETE CASCADE
- `mata_kuliah_kurikulum_id_foreign` FOREIGN KEY (`kurikulum_id`) REFERENCES `kurikulum`(`id`) ON DELETE CASCADE

---

### dosen

Tabel data dosen.

| Kolom                | Tipe Data          | Nullable | Default             | Deskripsi                           |
|----------------------|--------------------|----------|---------------------|-------------------------------------|
| id                   | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| tenant_id            | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants              |
| user_id              | BIGINT UNSIGNED    | Ya       | NULL                | Foreign key ke users (opsional)     |
| nidn                 | VARCHAR(20)        | Tidak    | -                   | NIDN (unik per tenant)              |
| nama                 | VARCHAR(200)       | Tidak    | -                   | Nama lengkap dosen                  |
| gelar_depan          | VARCHAR(50)        | Ya       | NULL                | Gelar akademik depan                |
| gelar_belakang       | VARCHAR(50)        | Ya       | NULL                | Gelar akademik belakang             |
| jenis_kelamin        | ENUM('L','P')      | Tidak    | -                   | Jenis kelamin                       |
| pendidikan_tertinggi | VARCHAR(10)        | Ya       | NULL                | S1, S2, S3                          |
| jabatan_fungsional   | VARCHAR(100)       | Ya       | NULL                | Jabatan fungsional                  |
| homebase_prodi_id    | BIGINT UNSIGNED    | Ya       | NULL                | Foreign key ke program_studi        |
| email                | VARCHAR(100)       | Ya       | NULL                | Email dosen                         |
| telepon              | VARCHAR(20)        | Ya       | NULL                | Nomor telepon                       |
| status               | ENUM('aktif','nonaktif','pensiun') | Tidak | 'aktif'   | Status dosen                        |
| created_at           | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at           | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |
| deleted_at           | TIMESTAMP          | Ya       | NULL                | Soft delete                         |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `dosen_tenant_nidn_unique` (`tenant_id`, `nidn`)
- INDEX `dosen_user_id_index` (`user_id`)
- INDEX `dosen_homebase_prodi_id_index` (`homebase_prodi_id`)

**Foreign Keys:**
- `dosen_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `dosen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
- `dosen_homebase_prodi_id_foreign` FOREIGN KEY (`homebase_prodi_id`) REFERENCES `program_studi`(`id`) ON DELETE SET NULL

---

### mata_kuliah_dosen

Tabel pivot many-to-many antara mata kuliah dan dosen pengampu.

| Kolom          | Tipe Data          | Nullable | Default             | Deskripsi                           |
|----------------|--------------------|----------|---------------------|-------------------------------------|
| id             | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| mata_kuliah_id | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke mata_kuliah          |
| dosen_id       | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke dosen                |
| peran          | ENUM('koordinator','pengampu','tamu') | Tidak | 'pengampu' | Peran dosen dalam MK     |
| created_at     | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at     | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `mata_kuliah_dosen_unique` (`mata_kuliah_id`, `dosen_id`)
- INDEX `mata_kuliah_dosen_dosen_id_index` (`dosen_id`)

**Foreign Keys:**
- `mata_kuliah_dosen_mk_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah`(`id`) ON DELETE CASCADE
- `mata_kuliah_dosen_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `dosen`(`id`) ON DELETE CASCADE

---

### profil_lulusan

Tabel profil lulusan yang diharapkan dari suatu program studi.

| Kolom              | Tipe Data          | Nullable | Default             | Deskripsi                           |
|--------------------|--------------------|----------|---------------------|-------------------------------------|
| id                 | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| tenant_id          | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants              |
| program_studi_id   | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke program_studi        |
| kode               | VARCHAR(20)        | Tidak    | -                   | Kode profil lulusan                 |
| nama               | VARCHAR(200)       | Tidak    | -                   | Nama profil (contoh: Web Developer) |
| deskripsi          | TEXT               | Ya       | NULL                | Deskripsi profil lulusan            |
| created_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |
| deleted_at         | TIMESTAMP          | Ya       | NULL                | Soft delete                         |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `profil_lulusan_tenant_kode_unique` (`tenant_id`, `kode`)
- INDEX `profil_lulusan_program_studi_id_index` (`program_studi_id`)

**Foreign Keys:**
- `profil_lulusan_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `profil_lulusan_prodi_id_foreign` FOREIGN KEY (`program_studi_id`) REFERENCES `program_studi`(`id`) ON DELETE CASCADE

---

### cpl

Tabel Capaian Pembelajaran Lulusan.

| Kolom              | Tipe Data          | Nullable | Default             | Deskripsi                                   |
|--------------------|--------------------|----------|---------------------|---------------------------------------------|
| id                 | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                                 |
| tenant_id          | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants                      |
| program_studi_id   | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke program_studi                |
| kurikulum_id       | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke kurikulum                    |
| kode               | VARCHAR(10)        | Tidak    | -                   | Kode CPL (contoh: CPL-01)                   |
| deskripsi          | TEXT               | Tidak    | -                   | Deskripsi CPL                               |
| kategori           | ENUM('sikap','pengetahuan','keterampilan_umum','keterampilan_khusus') | Tidak | - | Kategori CPL           |
| level_taksonomi    | VARCHAR(10)        | Ya       | NULL                | Level taksonomi Bloom (C1-C6, P1-P5, A1-A5)|
| created_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                             |
| updated_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir                    |
| deleted_at         | TIMESTAMP          | Ya       | NULL                | Soft delete                                 |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `cpl_tenant_kode_unique` (`tenant_id`, `kode`)
- INDEX `cpl_program_studi_id_index` (`program_studi_id`)
- INDEX `cpl_kurikulum_id_index` (`kurikulum_id`)
- INDEX `cpl_kategori_index` (`kategori`)

**Foreign Keys:**
- `cpl_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `cpl_program_studi_id_foreign` FOREIGN KEY (`program_studi_id`) REFERENCES `program_studi`(`id`) ON DELETE CASCADE
- `cpl_kurikulum_id_foreign` FOREIGN KEY (`kurikulum_id`) REFERENCES `kurikulum`(`id`) ON DELETE CASCADE

---

### profil_lulusan_cpl

Tabel pivot many-to-many antara profil lulusan dan CPL.

| Kolom             | Tipe Data          | Nullable | Default             | Deskripsi                           |
|-------------------|--------------------|----------|---------------------|-------------------------------------|
| id                | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| profil_lulusan_id | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke profil_lulusan       |
| cpl_id            | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke cpl                  |
| created_at        | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at        | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `profil_lulusan_cpl_unique` (`profil_lulusan_id`, `cpl_id`)
- INDEX `profil_lulusan_cpl_cpl_id_index` (`cpl_id`)

**Foreign Keys:**
- `profil_lulusan_cpl_pl_id_foreign` FOREIGN KEY (`profil_lulusan_id`) REFERENCES `profil_lulusan`(`id`) ON DELETE CASCADE
- `profil_lulusan_cpl_cpl_id_foreign` FOREIGN KEY (`cpl_id`) REFERENCES `cpl`(`id`) ON DELETE CASCADE

---

### referensi

Tabel pustaka/referensi yang digunakan dalam RPS.

| Kolom       | Tipe Data                    | Nullable | Default | Deskripsi                               |
|-------------|------------------------------|----------|---------|-----------------------------------------|
| id          | BIGINT UNSIGNED              | Tidak    | AUTO_INCREMENT | Primary key                       |
| tenant_id   | BIGINT UNSIGNED              | Tidak    | -       | Foreign key ke tenants                  |
| judul       | VARCHAR(500)                 | Tidak    | -       | Judul referensi                         |
| penulis     | VARCHAR(300)                 | Tidak    | -       | Nama penulis                            |
| tahun       | SMALLINT UNSIGNED            | Tidak    | -       | Tahun terbit                            |
| edisi       | VARCHAR(50)                  | Ya       | NULL    | Edisi                                   |
| penerbit    | VARCHAR(200)                 | Ya       | NULL    | Nama penerbit                           |
| isbn        | VARCHAR(30)                  | Ya       | NULL    | Nomor ISBN                              |
| jenis       | ENUM('buku_utama','buku_pendukung','jurnal','prosiding','website','modul') | Tidak | 'buku_utama' | Jenis referensi |
| url         | VARCHAR(500)                 | Ya       | NULL    | URL sumber online                       |
| created_at  | TIMESTAMP                    | Ya       | NULL    | Waktu pembuatan                         |
| updated_at  | TIMESTAMP                    | Ya       | NULL    | Waktu pembaruan terakhir                |
| deleted_at  | TIMESTAMP                    | Ya       | NULL    | Soft delete                             |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `referensi_tenant_id_index` (`tenant_id`)
- INDEX `referensi_jenis_index` (`jenis`)
- INDEX `referensi_tahun_index` (`tahun`)

**Foreign Keys:**
- `referensi_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE

---

### rps

Tabel utama Rencana Pembelajaran Semester.

| Kolom               | Tipe Data                                                     | Nullable | Default    | Deskripsi                                     |
|---------------------|---------------------------------------------------------------|----------|------------|-----------------------------------------------|
| id                  | BIGINT UNSIGNED                                               | Tidak    | AUTO_INCREMENT | Primary key                               |
| tenant_id           | BIGINT UNSIGNED                                               | Tidak    | -          | Foreign key ke tenants                        |
| mata_kuliah_id      | BIGINT UNSIGNED                                               | Tidak    | -          | Foreign key ke mata_kuliah                    |
| semester_id         | BIGINT UNSIGNED                                               | Tidak    | -          | Foreign key ke semesters                      |
| kode                | VARCHAR(50)                                                   | Tidak    | -          | Kode unik RPS (auto-generated)                |
| status              | ENUM('draft','review','revisi','disetujui','publish','arsip') | Tidak    | 'draft'    | Status workflow RPS                           |
| deskripsi           | TEXT                                                          | Ya       | NULL       | Deskripsi umum RPS                            |
| prasyarat           | VARCHAR(500)                                                  | Ya       | NULL       | Mata kuliah prasyarat                         |
| bahan_kajian        | TEXT                                                          | Ya       | NULL       | Bahan kajian / pokok bahasan                  |
| catatan             | TEXT                                                          | Ya       | NULL       | Catatan tambahan                              |
| created_by          | BIGINT UNSIGNED                                               | Tidak    | -          | Foreign key ke users (pembuat)                |
| last_updated_by     | BIGINT UNSIGNED                                               | Ya       | NULL       | Foreign key ke users (pengedit terakhir)      |
| submitted_for_review_at | TIMESTAMP                                                 | Ya       | NULL       | Waktu pengajuan review                        |
| created_at          | TIMESTAMP                                                     | Ya       | NULL       | Waktu pembuatan                               |
| updated_at          | TIMESTAMP                                                     | Ya       | NULL       | Waktu pembaruan terakhir                      |
| deleted_at          | TIMESTAMP                                                     | Ya       | NULL       | Soft delete                                   |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `rps_kode_unique` (`kode`)
- INDEX `rps_tenant_id_index` (`tenant_id`)
- INDEX `rps_mata_kuliah_id_index` (`mata_kuliah_id`)
- INDEX `rps_semester_id_index` (`semester_id`)
- INDEX `rps_status_index` (`status`)
- INDEX `rps_created_by_index` (`created_by`)

**Foreign Keys:**
- `rps_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `rps_mata_kuliah_id_foreign` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah`(`id`) ON DELETE CASCADE
- `rps_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters`(`id`) ON DELETE CASCADE
- `rps_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT
- `rps_last_updated_by_foreign` FOREIGN KEY (`last_updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL

---

### rps_cpl

Tabel pivot many-to-many antara RPS dan CPL yang didukung.

| Kolom      | Tipe Data          | Nullable | Default             | Deskripsi                        |
|------------|--------------------|----------|---------------------|----------------------------------|
| id         | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                      |
| rps_id     | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke rps               |
| cpl_id     | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke cpl               |
| created_at | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                  |
| updated_at | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir         |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `rps_cpl_unique` (`rps_id`, `cpl_id`)
- INDEX `rps_cpl_cpl_id_index` (`cpl_id`)

**Foreign Keys:**
- `rps_cpl_rps_id_foreign` FOREIGN KEY (`rps_id`) REFERENCES `rps`(`id`) ON DELETE CASCADE
- `rps_cpl_cpl_id_foreign` FOREIGN KEY (`cpl_id`) REFERENCES `cpl`(`id`) ON DELETE CASCADE

---

### cpml

Tabel Capaian Pembelajaran Mata Kuliah.

| Kolom      | Tipe Data          | Nullable | Default             | Deskripsi                           |
|------------|--------------------|----------|---------------------|-------------------------------------|
| id         | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| rps_id     | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke rps                  |
| kode       | VARCHAR(10)        | Tidak    | -                   | Kode CPMK (CPMK-01, dst)            |
| deskripsi  | TEXT               | Tidak    | -                   | Deskripsi CPMK                      |
| bobot      | DECIMAL(5,2)       | Ya       | NULL                | Bobot penilaian (%)                 |
| created_at | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `cpml_rps_id_index` (`rps_id`)
- INDEX `cpml_kode_index` (`kode`)

**Foreign Keys:**
- `cpml_rps_id_foreign` FOREIGN KEY (`rps_id`) REFERENCES `rps`(`id`) ON DELETE CASCADE

---

### sub_cpmk

Tabel Sub-Capaian Pembelajaran Mata Kuliah.

| Kolom        | Tipe Data          | Nullable | Default             | Deskripsi                           |
|--------------|--------------------|----------|---------------------|-------------------------------------|
| id           | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| cpml_id      | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke cpml                 |
| kode         | VARCHAR(15)        | Tidak    | -                   | Kode Sub-CPMK (Sub-CPMK-01, dst)    |
| deskripsi    | TEXT               | Tidak    | -                   | Deskripsi Sub-CPMK                  |
| pertemuan    | TINYINT UNSIGNED   | Tidak    | -                   | Nomor pertemuan                     |
| created_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `sub_cpmk_cpml_id_index` (`cpml_id`)
- INDEX `sub_cpmk_pertemuan_index` (`pertemuan`)

**Foreign Keys:**
- `sub_cpmk_cpml_id_foreign` FOREIGN KEY (`cpml_id`) REFERENCES `cpml`(`id`) ON DELETE CASCADE

---

### materi_pertemuan

Tabel materi pembelajaran per pertemuan dalam satu RPS.

| Kolom                    | Tipe Data          | Nullable | Default             | Deskripsi                           |
|--------------------------|--------------------|----------|---------------------|-------------------------------------|
| id                       | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| rps_id                   | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke rps                  |
| pertemuan_ke             | TINYINT UNSIGNED   | Tidak    | -                   | Nomor pertemuan                     |
| materi                   | TEXT               | Tidak    | -                   | Materi pembelajaran                  |
| indikator                | TEXT               | Ya       | NULL                | Indikator pencapaian                |
| sub_cpmk_ids             | JSON               | Ya       | NULL                | JSON array ID Sub-CPMK terkait      |
| metode_pembelajaran_ids  | JSON               | Ya       | NULL                | JSON array ID metode pembelajaran   |
| pengalaman_belajar       | TEXT               | Ya       | NULL                | Deskripsi pengalaman belajar        |
| estimasi_waktu           | SMALLINT UNSIGNED  | Ya       | NULL                | Estimasi waktu (menit)              |
| created_at               | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at               | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `materi_pertemuan_rps_id_index` (`rps_id`)
- INDEX `materi_pertemuan_pertemuan_ke_index` (`pertemuan_ke`)

**Foreign Keys:**
- `materi_pertemuan_rps_id_foreign` FOREIGN KEY (`rps_id`) REFERENCES `rps`(`id`) ON DELETE CASCADE

---

### metode_pembelajaran

Tabel lookup metode pembelajaran.

| Kolom       | Tipe Data          | Nullable | Default             | Deskripsi                           |
|-------------|--------------------|----------|---------------------|-------------------------------------|
| id          | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| tenant_id   | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants              |
| nama        | VARCHAR(200)       | Tidak    | -                   | Nama metode pembelajaran            |
| deskripsi   | TEXT               | Ya       | NULL                | Deskripsi metode                    |
| is_default  | TINYINT(1)         | Tidak    | 0                   | Apakah metode default sistem        |
| created_at  | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at  | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `metode_pembelajaran_tenant_id_index` (`tenant_id`)

**Foreign Keys:**
- `metode_pembelajaran_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE

---

### assessment

Tabel komponen penilaian dalam RPS.

| Kolom       | Tipe Data          | Nullable | Default             | Deskripsi                           |
|-------------|--------------------|----------|---------------------|-------------------------------------|
| id          | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| rps_id      | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke rps                  |
| jenis       | ENUM('UTS','UAS','tugas','kuis','proyek','presentasi','praktikum','partisipasi','portofolio') | Tidak | - | Jenis assessment |
| nama        | VARCHAR(200)       | Tidak    | -                   | Nama assessment                     |
| deskripsi   | TEXT               | Ya       | NULL                | Deskripsi detail assessment         |
| bobot       | DECIMAL(5,2)       | Tidak    | -                   | Bobot penilaian (%)                 |
| teknik      | VARCHAR(100)       | Ya       | NULL                | Teknik penilaian                    |
| instrumen   | VARCHAR(200)       | Ya       | NULL                | Instrumen penilaian                 |
| rubrik      | TEXT               | Ya       | NULL                | Rubrik penilaian                    |
| created_at  | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at  | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `assessment_rps_id_index` (`rps_id`)
- INDEX `assessment_jenis_index` (`jenis`)

**Foreign Keys:**
- `assessment_rps_id_foreign` FOREIGN KEY (`rps_id`) REFERENCES `rps`(`id`) ON DELETE CASCADE

---

### assessment_sub_cpmk

Tabel pivot many-to-many antara assessment dan Sub-CPMK.

| Kolom         | Tipe Data          | Nullable | Default | Deskripsi                              |
|---------------|--------------------|----------|---------|----------------------------------------|
| id            | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT | Primary key                      |
| assessment_id | BIGINT UNSIGNED    | Tidak    | -       | Foreign key ke assessment              |
| sub_cpmk_id   | BIGINT UNSIGNED    | Tidak    | -       | Foreign key ke sub_cpmk                |
| created_at    | TIMESTAMP          | Ya       | NULL    | Waktu pembuatan                        |
| updated_at    | TIMESTAMP          | Ya       | NULL    | Waktu pembaruan terakhir               |

**Indexes:**
- PRIMARY KEY (`id`)
- UNIQUE INDEX `assessment_sub_cpmk_unique` (`assessment_id`, `sub_cpmk_id`)
- INDEX `assessment_sub_cpmk_sub_cpmk_id_index` (`sub_cpmk_id`)

**Foreign Keys:**
- `assessment_sub_cpmk_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessment`(`id`) ON DELETE CASCADE
- `assessment_sub_cpmk_sub_cpmk_id_foreign` FOREIGN KEY (`sub_cpmk_id`) REFERENCES `sub_cpmk`(`id`) ON DELETE CASCADE

---

### rps_versions

Tabel versioning RPS. Menyimpan snapshot data RPS setiap kali perubahan signifikan terjadi.

| Kolom        | Tipe Data          | Nullable | Default             | Deskripsi                           |
|--------------|--------------------|----------|---------------------|-------------------------------------|
| id           | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| rps_id       | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke rps                  |
| version      | SMALLINT UNSIGNED  | Tidak    | 1                   | Nomor versi                         |
| data         | JSON               | Tidak    | -                   | Snapshot data RPS (JSON penuh)      |
| perubahan    | TEXT               | Ya       | NULL                | Deskripsi perubahan                  |
| created_by   | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke users (pembuat versi)|
| created_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `rps_versions_rps_id_index` (`rps_id`)
- INDEX `rps_versions_rps_id_version_index` (`rps_id`, `version`)
- INDEX `rps_versions_created_by_index` (`created_by`)

**Foreign Keys:**
- `rps_versions_rps_id_foreign` FOREIGN KEY (`rps_id`) REFERENCES `rps`(`id`) ON DELETE CASCADE
- `rps_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT

---

### rps_reviews

Tabel hasil review RPS. Menyimpan catatan dan rekomendasi dari reviewer.

| Kolom        | Tipe Data                                       | Nullable | Default             | Deskripsi                           |
|--------------|-------------------------------------------------|----------|---------------------|-------------------------------------|
| id           | BIGINT UNSIGNED                                 | Tidak    | AUTO_INCREMENT      | Primary key                         |
| rps_id       | BIGINT UNSIGNED                                 | Tidak    | -                   | Foreign key ke rps                  |
| reviewer_id  | BIGINT UNSIGNED                                 | Tidak    | -                   | Foreign key ke users (reviewer)     |
| status       | ENUM('disetujui','revisi','ditolak')             | Tidak    | -                   | Hasil review                        |
| catatan      | TEXT                                            | Ya       | NULL                | Catatan review                      |
| checklist    | JSON                                            | Ya       | NULL                | Checklist komponen review (JSON)    |
| reviewed_at  | TIMESTAMP                                       | Ya       | NULL                | Waktu review                        |
| created_at   | TIMESTAMP                                       | Ya       | NULL                | Waktu pembuatan                     |
| updated_at   | TIMESTAMP                                       | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `rps_reviews_rps_id_index` (`rps_id`)
- INDEX `rps_reviews_reviewer_id_index` (`reviewer_id`)
- INDEX `rps_reviews_status_index` (`status`)

**Foreign Keys:**
- `rps_reviews_rps_id_foreign` FOREIGN KEY (`rps_id`) REFERENCES `rps`(`id`) ON DELETE CASCADE
- `rps_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT

---

### rps_approvals

Tabel persetujuan RPS. Setiap RPS dapat melalui beberapa tahap approval.

| Kolom        | Tipe Data          | Nullable | Default             | Deskripsi                           |
|--------------|--------------------|----------|---------------------|-------------------------------------|
| id           | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| rps_id       | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke rps                  |
| approver_id  | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke users (approver)     |
| tahap        | ENUM('kaprodi','dekan','warek','rektor') | Tidak | - | Tahap approval              |
| status       | ENUM('disetujui','ditolak','pending') | Tidak | 'pending' | Status approval              |
| catatan      | TEXT               | Ya       | NULL                | Catatan approval                    |
| approved_at  | TIMESTAMP          | Ya       | NULL                | Waktu persetujuan                   |
| created_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `rps_approvals_rps_id_index` (`rps_id`)
- INDEX `rps_approvals_approver_id_index` (`approver_id`)
- INDEX `rps_approvals_tahap_index` (`tahap`)
- INDEX `rps_approvals_status_index` (`status`)

**Foreign Keys:**
- `rps_approvals_rps_id_foreign` FOREIGN KEY (`rps_id`) REFERENCES `rps`(`id`) ON DELETE CASCADE
- `rps_approvals_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT

---

### notifications

Tabel notifikasi untuk user.

| Kolom        | Tipe Data          | Nullable | Default             | Deskripsi                               |
|--------------|--------------------|----------|---------------------|-----------------------------------------|
| id           | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                             |
| tenant_id    | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants                  |
| user_id      | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke users (penerima)         |
| from_user_id | BIGINT UNSIGNED    | Ya       | NULL                | Foreign key ke users (pengirim)         |
| tipe         | ENUM('info','review','approval','warning','success','reminder') | Tidak | 'info' | Tipe notifikasi |
| judul        | VARCHAR(200)       | Tidak    | -                   | Judul notifikasi                        |
| pesan        | TEXT               | Tidak    | -                   | Isi notifikasi                          |
| data         | JSON               | Ya       | NULL                | Data tambahan (JSON)                    |
| is_read      | TINYINT(1)         | Tidak    | 0                   | Status dibaca                           |
| read_at      | TIMESTAMP          | Ya       | NULL                | Waktu dibaca                            |
| created_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                         |
| updated_at   | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir                |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `notifications_tenant_id_index` (`tenant_id`)
- INDEX `notifications_user_id_index` (`user_id`)
- INDEX `notifications_user_id_is_read_index` (`user_id`, `is_read`)
- INDEX `notifications_tipe_index` (`tipe`)

**Foreign Keys:**
- `notifications_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
- `notifications_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL

---

### audit_logs

Tabel audit trail. Mencatat seluruh aktivitas penting dalam sistem.

| Kolom        | Tipe Data          | Nullable | Default             | Deskripsi                           |
|--------------|--------------------|----------|---------------------|-------------------------------------|
| id           | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| tenant_id    | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants              |
| user_id      | BIGINT UNSIGNED    | Ya       | NULL                | Foreign key ke users (pelaku)       |
| action       | VARCHAR(50)        | Tidak    | -                   | Aksi (login, logout, created, updated, deleted, reviewed, approved, exported, etc.) |
| model_type   | VARCHAR(255)       | Ya       | NULL                | Tipe model yang terkena aksi        |
| model_id     | BIGINT UNSIGNED    | Ya       | NULL                | ID model yang terkena aksi          |
| old_values   | JSON               | Ya       | NULL                | Nilai sebelum perubahan (JSON)      |
| new_values   | JSON               | Ya       | NULL                | Nilai setelah perubahan (JSON)      |
| ip_address   | VARCHAR(45)        | Ya       | NULL                | Alamat IP pengguna                  |
| user_agent   | VARCHAR(500)       | Ya       | NULL                | User agent browser                  |
| created_at   | TIMESTAMP          | Ya       | NULL                | Waktu kejadian                      |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `audit_logs_tenant_id_index` (`tenant_id`)
- INDEX `audit_logs_user_id_index` (`user_id`)
- INDEX `audit_logs_action_index` (`action`)
- INDEX `audit_logs_model_type_model_id_index` (`model_type`, `model_id`)
- INDEX `audit_logs_created_at_index` (`created_at`)

**Foreign Keys:**
- `audit_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL

---

### templates

Tabel template dokumen untuk export RPS.

| Kolom       | Tipe Data                    | Nullable | Default             | Deskripsi                           |
|-------------|------------------------------|----------|---------------------|-------------------------------------|
| id          | BIGINT UNSIGNED              | Tidak    | AUTO_INCREMENT      | Primary key                         |
| tenant_id   | BIGINT UNSIGNED              | Tidak    | -                   | Foreign key ke tenants              |
| nama        | VARCHAR(200)                 | Tidak    | -                   | Nama template                       |
| jenis       | ENUM('word','pdf','excel')   | Tidak    | -                   | Jenis template                      |
| file_path   | VARCHAR(500)                 | Tidak    | -                   | Path file template                  |
| is_default  | TINYINT(1)                   | Tidak    | 0                   | Template default                    |
| deskripsi   | TEXT                         | Ya       | NULL                | Deskripsi template                  |
| created_by  | BIGINT UNSIGNED              | Tidak    | -                   | Foreign key ke users (pembuat)      |
| created_at  | TIMESTAMP                    | Ya       | NULL                | Waktu pembuatan                     |
| updated_at  | TIMESTAMP                    | Ya       | NULL                | Waktu pembaruan terakhir            |
| deleted_at  | TIMESTAMP                    | Ya       | NULL                | Soft delete                         |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `templates_tenant_id_index` (`tenant_id`)
- INDEX `templates_jenis_index` (`jenis`)

**Foreign Keys:**
- `templates_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT

---

### invitations

Tabel undangan user (belum memiliki akun).

| Kolom              | Tipe Data          | Nullable | Default             | Deskripsi                           |
|--------------------|--------------------|----------|---------------------|-------------------------------------|
| id                 | BIGINT UNSIGNED    | Tidak    | AUTO_INCREMENT      | Primary key                         |
| tenant_id          | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke tenants              |
| email              | VARCHAR(100)       | Tidak    | -                   | Email yang diundang                 |
| role               | VARCHAR(50)        | Tidak    | -                   | Role yang akan diberikan            |
| fakultas_id        | BIGINT UNSIGNED    | Ya       | NULL                | Foreign key ke fakultas             |
| program_studi_id   | BIGINT UNSIGNED    | Ya       | NULL                | Foreign key ke program_studi        |
| token              | VARCHAR(255)       | Tidak    | -                   | Token unik undangan                 |
| invited_by         | BIGINT UNSIGNED    | Tidak    | -                   | Foreign key ke users (pengundang)   |
| status             | ENUM('pending','accepted','expired','cancelled') | Tidak | 'pending' | Status undangan |
| accepted_at        | TIMESTAMP          | Ya       | NULL                | Waktu diterima                      |
| expires_at         | TIMESTAMP          | Tidak    | -                   | Waktu kadaluarsa token              |
| created_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembuatan                     |
| updated_at         | TIMESTAMP          | Ya       | NULL                | Waktu pembaruan terakhir            |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `invitations_tenant_id_index` (`tenant_id`)
- INDEX `invitations_email_index` (`email`)
- INDEX `invitations_token_index` (`token`)
- INDEX `invitations_status_index` (`status`)

**Foreign Keys:**
- `invitations_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `invitations_fakultas_id_foreign` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas`(`id`) ON DELETE SET NULL
- `invitations_program_studi_id_foreign` FOREIGN KEY (`program_studi_id`) REFERENCES `program_studi`(`id`) ON DELETE SET NULL
- `invitations_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users`(`id`) ON DELETE RESTRICT

---

### ai_generation_logs

Tabel log penggunaan fitur AI. Mencatat setiap permintaan generate, validasi, dan review oleh AI.

| Kolom          | Tipe Data                                                 | Nullable | Default | Deskripsi                                |
|----------------|-----------------------------------------------------------|----------|---------|------------------------------------------|
| id             | BIGINT UNSIGNED                                           | Tidak    | AUTO_INCREMENT | Primary key                        |
| tenant_id      | BIGINT UNSIGNED                                           | Tidak    | -       | Foreign key ke tenants                   |
| user_id        | BIGINT UNSIGNED                                           | Tidak    | -       | Foreign key ke users (peminta)           |
| rps_id         | BIGINT UNSIGNED                                           | Ya       | NULL    | Foreign key ke rps (jika terkait RPS)    |
| tipe           | ENUM('generate_cpmk','generate_subcpmk','validate','review') | Tidak | -    | Tipe permintaan AI                       |
| model          | VARCHAR(50)                                               | Tidak    | 'gpt-4o'| Model AI yang digunakan                 |
| prompt         | TEXT                                                      | Tidak    | -       | Prompt yang dikirim ke AI                |
| response       | JSON                                                      | Tidak    | -       | Respon lengkap dari AI (JSON)            |
| tokens_used    | INT UNSIGNED                                              | Tidak    | 0       | Jumlah token yang digunakan              |
| status         | ENUM('success','error','rate_limited')                     | Tidak    | -       | Status permintaan                        |
| error_message  | TEXT                                                      | Ya       | NULL    | Pesan error jika gagal                   |
| created_at     | TIMESTAMP                                                 | Ya       | NULL    | Waktu permintaan                         |

**Indexes:**
- PRIMARY KEY (`id`)
- INDEX `ai_generation_logs_tenant_id_index` (`tenant_id`)
- INDEX `ai_generation_logs_user_id_index` (`user_id`)
- INDEX `ai_generation_logs_rps_id_index` (`rps_id`)
- INDEX `ai_generation_logs_tipe_index` (`tipe`)
- INDEX `ai_generation_logs_status_index` (`status`)
- INDEX `ai_generation_logs_created_at_index` (`created_at`)

**Foreign Keys:**
- `ai_generation_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE
- `ai_generation_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
- `ai_generation_logs_rps_id_foreign` FOREIGN KEY (`rps_id`) REFERENCES `rps`(`id`) ON DELETE SET NULL

---

## Ringkasan Relasi Kunci

| Tabel Child            | Foreign Key          | Tabel Parent         | ON DELETE      | Keterangan                        |
|------------------------|----------------------|----------------------|----------------|-----------------------------------|
| users                  | tenant_id            | tenants              | CASCADE        | User milik tenant                 |
| fakultas               | tenant_id            | tenants              | CASCADE        | Fakultas milik universitas        |
| program_studi          | tenant_id, fakultas_id | tenants, fakultas  | CASCADE        | Prodi di bawah fakultas           |
| kurikulum              | tenant_id, program_studi_id | tenants, program_studi | CASCADE  | Kurikulum milik prodi             |
| semesters              | tenant_id            | tenants              | CASCADE        | Semester milik tenant             |
| mata_kuliah            | tenant_id, program_studi_id, kurikulum_id | tenants, program_studi, kurikulum | CASCADE | MK di prodi & kurikulum |
| dosen                  | tenant_id, user_id, homebase_prodi_id | tenants, users, program_studi | CASCADE/SET NULL | Profil dosen     |
| mata_kuliah_dosen      | mata_kuliah_id, dosen_id | mata_kuliah, dosen | CASCADE        | Pivot MK-Dosen                   |
| profil_lulusan         | tenant_id, program_studi_id | tenants, program_studi | CASCADE    | Profil lulusan prodi              |
| cpl                    | tenant_id, program_studi_id, kurikulum_id | tenants, program_studi, kurikulum | CASCADE | CPL per kurikulum prodi |
| profil_lulusan_cpl     | profil_lulusan_id, cpl_id | profil_lulusan, cpl | CASCADE      | Pivot Profil-CPL                 |
| referensi              | tenant_id            | tenants              | CASCADE        | Referensi milik tenant            |
| rps                    | tenant_id, mata_kuliah_id, semester_id, created_by | tenants, mata_kuliah, semesters, users | CASCADE/RESTRICT | RPS utama |
| rps_cpl                | rps_id, cpl_id       | rps, cpl             | CASCADE        | Pivot RPS-CPL                    |
| cpml                   | rps_id               | rps                  | CASCADE        | CPMK milik RPS                   |
| sub_cpmk               | cpml_id              | cpml                 | CASCADE        | Sub-CPMK milik CPMK              |
| materi_pertemuan       | rps_id               | rps                  | CASCADE        | Materi per pertemuan             |
| assessment             | rps_id               | rps                  | CASCADE        | Assessment milik RPS             |
| assessment_sub_cpmk    | assessment_id, sub_cpmk_id | assessment, sub_cpmk | CASCADE    | Pivot Assessment-Sub CPMK        |
| rps_versions           | rps_id, created_by   | rps, users           | CASCADE/RESTRICT | Versioning RPS                  |
| rps_reviews            | rps_id, reviewer_id  | rps, users           | CASCADE/RESTRICT | Review RPS                      |
| rps_approvals          | rps_id, approver_id  | rps, users           | CASCADE/RESTRICT | Approval RPS                    |
| notifications          | tenant_id, user_id, from_user_id | tenants, users, users | CASCADE/SET NULL | Notifikasi user      |
| audit_logs             | tenant_id, user_id   | tenants, users       | CASCADE/SET NULL | Audit trail                     |
| templates              | tenant_id, created_by | tenants, users      | CASCADE/RESTRICT | Template dokumen                |
| invitations            | tenant_id, fakultas_id, program_studi_id, invited_by | tenants, fakultas, program_studi, users | CASCADE/SET NULL/RESTRICT | Undangan user |
| ai_generation_logs     | tenant_id, user_id, rps_id | tenants, users, rps | CASCADE/RESTRICT/SET NULL | Log AI     |

---

## Catatan Implementasi

1. **Multi-Tenancy**: Hampir semua tabel memiliki kolom `tenant_id` untuk isolasi data. Query selalu difilter berdasarkan `tenant_id` dari user yang sedang login.

2. **Soft Delete**: Tabel yang menggunakan soft delete ditandai dengan kolom `deleted_at`. Laravel Eloquent secara otomatis menangani soft delete melalui trait `SoftDeletes`.

3. **Timestamps**: Seluruh tabel menggunakan `created_at` dan `updated_at` (nullable) sesuai standar Laravel.

4. **JSON Columns**: Beberapa tabel menggunakan kolom JSON untuk fleksibilitas data (settings, checklist, data, old_values, new_values, sub_cpmk_ids, metode_pembelajaran_ids). Kolom JSON ini didukung penuh oleh MariaDB 10.2+.

5. **Penamaan**: Konvensi penamaan mengikuti standar Laravel (snake_case, plural table names, foreign key dengan suffix `_id`, nama constraint dengan format `{tabel}_{kolom}_foreign`).

6. **Enum**: Kolom enum digunakan untuk nilai yang terbatas dan pasti. Nilai enum dapat diperluas melalui migration tanpa kehilangan data.
