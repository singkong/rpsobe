# Dokumentasi API RPS OBE

Dokumentasi lengkap API untuk aplikasi Rencana Pembelajaran Semester (RPS) berbasis Outcome-Based Education (OBE).

## Informasi Umum

| Item                | Keterangan                              |
|---------------------|-----------------------------------------|
| Base URL            | `https://api.rpsobe.id/v1`             |
| Format              | JSON                                    |
| Autentikasi         | Laravel Sanctum Bearer Token            |
| Metode Autentikasi  | Header `Authorization: Bearer <token>`  |

## Versi API

API ini menggunakan strategi versioning berbasis URL (`/v1`, `/v2`, dst). Setiap versi mayor menjamin kompatibilitas mundur selama siklus hidup versi tersebut. Versi lama akan dihentikan secara bertahap dengan pemberitahuan 6 bulan sebelumnya melalui:

- Email ke seluruh tenant administrator
- Notifikasi di dashboard aplikasi
- Changelog publik di halaman dokumentasi

### Siklus Hidup Versi API

| Tahap          | Durasi    | Keterangan                                        |
|----------------|-----------|---------------------------------------------------|
| Aktif          | -         | Versi saat ini, didukung penuh                    |
| Deprecated     | 6 bulan   | Masih berfungsi, tidak ada fitur baru             |
| Sunset         | -         | Dihapus sepenuhnya                                |

## Autentikasi

Seluruh endpoint (kecuali yang diberi tanda `Tidak` pada kolom Auth) memerlukan header:

```
Authorization: Bearer <token>
Content-Type: application/json
Accept: application/json
```

Token diperoleh melalui endpoint Login. Token tidak memiliki masa kadaluarsa (expiry) secara default, namun akan hangus saat user melakukan logout.

## Rate Limiting

| Tipe Limit          | Batas           | Periode  |
|---------------------|-----------------|----------|
| Global API          | 120 request     | per menit|
| Endpoint Login      | 5 request       | per menit|
| Endpoint AI         | 10 request      | per menit|
| Endpoint Export     | 5 request       | per menit|

Header rate limit yang dikembalikan:

```
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 118
X-Retry-After: 30
```

## Pagination

Endpoint dengan daftar data (list) mendukung pagination dengan parameter query:

| Parameter   | Default | Deskripsi                  |
|-------------|---------|----------------------------|
| `page`      | 1       | Nomor halaman              |
| `per_page`  | 15      | Jumlah item per halaman    |
| `sort`      | id      | Kolom pengurutan           |
| `order`     | asc     | Arah pengurutan (asc/desc) |

Format respons pagination:

```json
{
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "https://api.rpsobe.id/v1/users?page=1",
    "last": "https://api.rpsobe.id/v1/users?page=5",
    "prev": null,
    "next": "https://api.rpsobe.id/v1/users?page=2"
  }
}
```

## Format Error Response

Semua error dikembalikan dalam format JSON yang konsisten:

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Data yang dikirim tidak valid.",
    "details": {
      "email": ["Email sudah digunakan."],
      "nama": ["Nama wajib diisi."]
    }
  }
}
```

### Daftar Kode Error

| Kode                    | HTTP Status | Deskripsi                                 |
|-------------------------|-------------|-------------------------------------------|
| `VALIDATION_ERROR`      | 422         | Validasi input gagal                      |
| `AUTHENTICATION_ERROR`  | 401         | Token tidak valid atau kadaluarsa         |
| `AUTHORIZATION_ERROR`   | 403         | Tidak memiliki izin akses                 |
| `NOT_FOUND`             | 404         | Resource tidak ditemukan                  |
| `RATE_LIMIT_EXCEEDED`   | 429         | Batas request terlampaui                  |
| `INTERNAL_ERROR`        | 500         | Kesalahan server                          |
| `TENANT_NOT_FOUND`      | 404         | Tenant tidak dikenali                     |
| `BUSINESS_RULE_ERROR`   | 422         | Melanggar aturan bisnis aplikasi          |

## Daftar Endpoint

### 1. Auth

| Method | Endpoint              | Deskripsi                          | Auth    | Role          |
|--------|-----------------------|------------------------------------|---------|---------------|
| POST   | `/auth/login`         | Login dan dapatkan token           | Tidak   | Semua         |
| POST   | `/auth/register`      | Registrasi akun baru               | Tidak   | Semua         |
| POST   | `/auth/logout`        | Logout dan hapus token             | Ya      | Semua         |
| GET    | `/auth/me`            | Ambil data profil user login       | Ya      | Semua         |
| POST   | `/auth/forgot-password`| Kirim email reset password        | Tidak   | Semua         |
| POST   | `/auth/reset-password` | Reset password dengan token       | Tidak   | Semua         |

#### POST `/auth/login`

**Request Body:**

```json
{
  "email": "dosen@univ.ac.id",
  "password": "password123",
  "tenant_id": "univ-merdeka"
}
```

**Response (200):**

```json
{
  "data": {
    "token": "1|AbCDeFgHiJkLmNoPqRsTuVwXyZ0123456789",
    "user": {
      "id": 1,
      "nama": "Dr. Ahmad Fauzi",
      "email": "dosen@univ.ac.id",
      "role": "dosen",
      "fakultas_id": 2,
      "program_studi_id": 5
    }
  }
}
```

#### POST `/auth/register`

**Request Body:**

```json
{
  "tenant_id": "univ-merdeka",
  "nama": "Dr. Siti Rahayu",
  "email": "siti.rahayu@univ.ac.id",
  "password": "password123",
  "password_confirmation": "password123",
  "nidn": "0012057803",
  "fakultas_id": 2,
  "program_studi_id": 5
}
```

**Response (201):**

```json
{
  "data": {
    "user": {
      "id": 15,
      "nama": "Dr. Siti Rahayu",
      "email": "siti.rahayu@univ.ac.id",
      "role": "dosen",
      "created_at": "2025-01-15T08:30:00Z"
    }
  },
  "message": "Registrasi berhasil. Silakan login."
}
```

#### POST `/auth/logout`

**Response (200):**

```json
{
  "message": "Logout berhasil."
}
```

#### GET `/auth/me`

**Response (200):**

```json
{
  "data": {
    "id": 1,
    "nama": "Dr. Ahmad Fauzi",
    "email": "dosen@univ.ac.id",
    "role": "dosen",
    "permissions": ["rps.create", "rps.read", "rps.update"],
    "fakultas": {
      "id": 2,
      "nama": "Fakultas Teknik"
    },
    "program_studi": {
      "id": 5,
      "nama": "Teknik Informatika"
    }
  }
}
```

#### POST `/auth/forgot-password`

**Request Body:**

```json
{
  "email": "dosen@univ.ac.id",
  "tenant_id": "univ-merdeka"
}
```

**Response (200):**

```json
{
  "message": "Link reset password telah dikirim ke email Anda."
}
```

#### POST `/auth/reset-password`

**Request Body:**

```json
{
  "token": "60b9e1a2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0",
  "email": "dosen@univ.ac.id",
  "password": "passwordbaru123",
  "password_confirmation": "passwordbaru123"
}
```

**Response (200):**

```json
{
  "message": "Password berhasil direset. Silakan login."
}
```

---

### 2. Manajemen User

| Method   | Endpoint             | Deskripsi                           | Auth    | Role                  |
|----------|----------------------|-------------------------------------|---------|-----------------------|
| GET      | `/users`             | Daftar semua user                   | Ya      | Admin, Kaprodi       |
| GET      | `/users/{id}`        | Detail user                         | Ya      | Admin, Kaprodi       |
| POST     | `/users`             | Tambah user baru                    | Ya      | Admin                |
| PUT      | `/users/{id}`        | Perbarui data user                  | Ya      | Admin                |
| DELETE   | `/users/{id}`        | Hapus user (soft delete)            | Ya      | Admin                |
| POST     | `/users/invite`      | Undang user via email               | Ya      | Admin                |
| POST     | `/users/bulk-import` | Impor user dari file Excel/CSV      | Ya      | Admin                |

#### GET `/users`

**Query Parameters:** `?page=1&per_page=15&role=dosen&fakultas_id=2&program_studi_id=5&search=ahmad`

**Response (200):** (dengan metadata pagination)

```json
{
  "data": [
    {
      "id": 1,
      "nama": "Dr. Ahmad Fauzi",
      "email": "dosen@univ.ac.id",
      "nidn": "0012057803",
      "role": "dosen",
      "fakultas": "Fakultas Teknik",
      "program_studi": "Teknik Informatika",
      "created_at": "2025-01-10T07:00:00Z"
    }
  ]
}
```

#### POST `/users`

**Request Body:**

```json
{
  "nama": "Dr. Budi Santoso",
  "email": "budi.santoso@univ.ac.id",
  "password": "password123",
  "password_confirmation": "password123",
  "nidn": "0018038405",
  "role": "dosen",
  "fakultas_id": 2,
  "program_studi_id": 5
}
```

**Response (201):**

```json
{
  "data": {
    "id": 16,
    "nama": "Dr. Budi Santoso",
    "email": "budi.santoso@univ.ac.id",
    "role": "dosen",
    "created_at": "2025-01-15T09:00:00Z"
  },
  "message": "User berhasil ditambahkan."
}
```

#### PUT `/users/{id}`

**Request Body:**

```json
{
  "nama": "Dr. Budi Santoso, M.Kom.",
  "program_studi_id": 6
}
```

**Response (200):**

```json
{
  "data": {
    "id": 16,
    "nama": "Dr. Budi Santoso, M.Kom.",
    "program_studi": "Sistem Informasi"
  },
  "message": "Data user berhasil diperbarui."
}
```

#### DELETE `/users/{id}`

**Response (200):**

```json
{
  "message": "User berhasil dihapus."
}
```

#### POST `/users/invite`

**Request Body:**

```json
{
  "email": "dosen.baru@univ.ac.id",
  "role": "dosen",
  "fakultas_id": 2,
  "program_studi_id": 5
}
```

**Response (200):**

```json
{
  "message": "Undangan berhasil dikirim ke dosen.baru@univ.ac.id."
}
```

#### POST `/users/bulk-import`

**Request (multipart/form-data):**

| Field  | Tipe | Deskripsi                       |
|--------|------|----------------------------------|
| `file` | File | File Excel (.xlsx) atau CSV      |

**Response (200):**

```json
{
  "data": {
    "success_count": 12,
    "error_count": 2,
    "errors": [
      { "row": 5, "message": "Email sudah digunakan: duplikat@univ.ac.id" },
      { "row": 9, "message": "NIDN wajib diisi" }
    ]
  },
  "message": "Import selesai. 12 user berhasil ditambahkan, 2 gagal."
}
```

---

### 3. Master Data

#### 3.1 Universitas

| Method   | Endpoint              | Deskripsi                     | Auth    | Role         |
|----------|-----------------------|-------------------------------|---------|--------------|
| GET      | `/universitas`        | Daftar universitas (tenant)   | Ya      | Admin        |
| GET      | `/universitas/{id}`   | Detail universitas            | Ya      | Admin        |
| POST     | `/universitas`        | Tambah universitas            | Ya      | Super Admin  |
| PUT      | `/universitas/{id}`   | Perbarui universitas          | Ya      | Admin        |
| DELETE   | `/universitas/{id}`   | Hapus universitas (soft delete)| Ya     | Super Admin  |

#### POST `/universitas`

**Request Body:**

```json
{
  "nama": "Universitas Merdeka",
  "kode": "UNMER",
  "alamat": "Jl. Merdeka No. 123, Jakarta",
  "telepon": "021-5551234",
  "email": "info@unmer.ac.id",
  "website": "https://unmer.ac.id",
  "tenant_id": "univ-merdeka"
}
```

#### 3.2 Fakultas

| Method   | Endpoint           | Deskripsi                     | Auth    | Role         |
|----------|--------------------|-------------------------------|---------|--------------|
| GET      | `/fakultas`        | Daftar fakultas               | Ya      | Semua        |
| GET      | `/fakultas/{id}`   | Detail fakultas               | Ya      | Semua        |
| POST     | `/fakultas`        | Tambah fakultas               | Ya      | Admin        |
| PUT      | `/fakultas/{id}`   | Perbarui fakultas             | Ya      | Admin        |
| DELETE   | `/fakultas/{id}`   | Hapus fakultas (soft delete)  | Ya      | Admin        |

#### POST `/fakultas`

**Request Body:**

```json
{
  "universitas_id": 1,
  "nama": "Fakultas Teknik",
  "kode": "FT",
  "dekan": "Prof. Dr. Ir. Hendra Wijaya"
}
```

#### 3.3 Program Studi

| Method   | Endpoint                | Deskripsi                       | Auth    | Role         |
|----------|-------------------------|---------------------------------|---------|--------------|
| GET      | `/program-studi`        | Daftar program studi            | Ya      | Semua        |
| GET      | `/program-studi/{id}`   | Detail program studi            | Ya      | Semua        |
| POST     | `/program-studi`        | Tambah program studi            | Ya      | Admin        |
| PUT      | `/program-studi/{id}`   | Perbarui program studi          | Ya      | Admin        |
| DELETE   | `/program-studi/{id}`   | Hapus program studi (soft delete)| Ya     | Admin        |

#### POST `/program-studi`

**Request Body:**

```json
{
  "fakultas_id": 2,
  "nama": "Teknik Informatika",
  "kode": "TI",
  "jenjang": "S1",
  "akreditasi": "A",
  "kaprodi": "Dr. Rina Anggraini, M.Kom."
}
```

#### 3.4 Kurikulum

| Method   | Endpoint              | Deskripsi                       | Auth    | Role         |
|----------|-----------------------|---------------------------------|---------|--------------|
| GET      | `/kurikulum`          | Daftar kurikulum                | Ya      | Semua        |
| GET      | `/kurikulum/{id}`     | Detail kurikulum                | Ya      | Semua        |
| POST     | `/kurikulum`          | Tambah kurikulum                | Ya      | Kaprodi      |
| PUT      | `/kurikulum/{id}`     | Perbarui kurikulum              | Ya      | Kaprodi      |
| DELETE   | `/kurikulum/{id}`     | Hapus kurikulum (soft delete)   | Ya      | Kaprodi      |

#### POST `/kurikulum`

**Request Body:**

```json
{
  "program_studi_id": 5,
  "nama": "Kurikulum 2025",
  "tahun": 2025,
  "semester_mulai": "2025/2026 Ganjil",
  "status": "aktif",
  "deskripsi": "Kurikulum berbasis OBE sesuai KKNI Level 6."
}
```

#### 3.5 Semester

| Method   | Endpoint              | Deskripsi                     | Auth    | Role         |
|----------|-----------------------|-------------------------------|---------|--------------|
| GET      | `/semester`           | Daftar semester               | Ya      | Semua        |
| GET      | `/semester/{id}`      | Detail semester               | Ya      | Semua        |
| POST     | `/semester`           | Tambah semester               | Ya      | Admin        |
| PUT      | `/semester/{id}`      | Perbarui semester             | Ya      | Admin        |
| DELETE   | `/semester/{id}`      | Hapus semester                | Ya      | Admin        |

#### POST `/semester`

**Request Body:**

```json
{
  "kode": "20251",
  "nama": "Semester Ganjil 2025/2026",
  "tanggal_mulai": "2025-09-01",
  "tanggal_selesai": "2026-01-30",
  "status": "aktif"
}
```

#### 3.6 Mata Kuliah

| Method   | Endpoint            | Deskripsi                       | Auth    | Role         |
|----------|---------------------|---------------------------------|---------|--------------|
| GET      | `/matkul`           | Daftar mata kuliah              | Ya      | Semua        |
| GET      | `/matkul/{id}`      | Detail mata kuliah              | Ya      | Semua        |
| POST     | `/matkul`           | Tambah mata kuliah              | Ya      | Kaprodi      |
| PUT      | `/matkul/{id}`      | Perbarui mata kuliah            | Ya      | Kaprodi      |
| DELETE   | `/matkul/{id}`      | Hapus mata kuliah (soft delete) | Ya      | Kaprodi      |

#### POST `/matkul`

**Request Body:**

```json
{
  "program_studi_id": 5,
  "kurikulum_id": 3,
  "kode": "TIF-301",
  "nama": "Pemrograman Web Lanjut",
  "nama_inggris": "Advanced Web Programming",
  "sks_teori": 2,
  "sks_praktik": 1,
  "semester": 5,
  "jenis": "wajib",
  "deskripsi": "Mata kuliah ini membahas pengembangan aplikasi web modern."
}
```

#### 3.7 Dosen

| Method   | Endpoint          | Deskripsi                     | Auth    | Role         |
|----------|-------------------|-------------------------------|---------|--------------|
| GET      | `/dosen`          | Daftar dosen                  | Ya      | Semua        |
| GET      | `/dosen/{id}`     | Detail dosen                  | Ya      | Semua        |
| POST     | `/dosen`          | Tambah dosen                  | Ya      | Admin        |
| PUT      | `/dosen/{id}`     | Perbarui dosen                | Ya      | Admin        |
| DELETE   | `/dosen/{id}`     | Hapus dosen (soft delete)     | Ya      | Admin        |

#### POST `/dosen`

**Request Body:**

```json
{
  "user_id": 1,
  "nidn": "0012057803",
  "nama": "Dr. Ahmad Fauzi",
  "jenis_kelamin": "L",
  "pendidikan_tertinggi": "S3",
  "jabatan_fungsional": "Lektor Kepala",
  "fakultas_id": 2,
  "program_studi_ids": [5, 6]
}
```

#### 3.8 Capaian Pembelajaran Lulusan (CPL)

| Method   | Endpoint         | Deskripsi                   | Auth    | Role         |
|----------|------------------|-----------------------------|---------|--------------|
| GET      | `/cpl`           | Daftar CPL                  | Ya      | Semua        |
| GET      | `/cpl/{id}`      | Detail CPL                  | Ya      | Semua        |
| POST     | `/cpl`           | Tambah CPL                  | Ya      | Kaprodi      |
| PUT      | `/cpl/{id}`      | Perbarui CPL                | Ya      | Kaprodi      |
| DELETE   | `/cpl/{id}`      | Hapus CPL (soft delete)     | Ya      | Kaprodi      |

#### POST `/cpl`

**Request Body:**

```json
{
  "program_studi_id": 5,
  "kurikulum_id": 3,
  "kode": "CPL-01",
  "deskripsi": "Mampu merancang dan mengembangkan sistem perangkat lunak berbasis web yang memenuhi standar kualitas industri.",
  "kategori": "keterampilan_khusus",
  "level_taksonomi": "C6",
  "profil_lulusan_ids": [1, 2]
}
```

#### 3.9 Profil Lulusan

| Method   | Endpoint               | Deskripsi                      | Auth    | Role         |
|----------|------------------------|--------------------------------|---------|--------------|
| GET      | `/profil-lulusan`      | Daftar profil lulusan          | Ya      | Semua        |
| GET      | `/profil-lulusan/{id}` | Detail profil lulusan          | Ya      | Semua        |
| POST     | `/profil-lulusan`      | Tambah profil lulusan          | Ya      | Kaprodi      |
| PUT      | `/profil-lulusan/{id}` | Perbarui profil lulusan        | Ya      | Kaprodi      |
| DELETE   | `/profil-lulusan/{id}` | Hapus profil lulusan (soft delete)| Ya   | Kaprodi      |

#### POST `/profil-lulusan`

**Request Body:**

```json
{
  "program_studi_id": 5,
  "kode": "PL-01",
  "nama": "Web Developer",
  "deskripsi": "Lulusan yang mampu mengembangkan aplikasi web modern dengan mengikuti standar industri."
}
```

#### 3.10 Referensi

| Method   | Endpoint            | Deskripsi                     | Auth    | Role         |
|----------|---------------------|-------------------------------|---------|--------------|
| GET      | `/referensi`        | Daftar referensi              | Ya      | Semua        |
| GET      | `/referensi/{id}`   | Detail referensi              | Ya      | Semua        |
| POST     | `/referensi`        | Tambah referensi              | Ya      | Dosen        |
| PUT      | `/referensi/{id}`   | Perbarui referensi            | Ya      | Dosen        |
| DELETE   | `/referensi/{id}`   | Hapus referensi (soft delete) | Ya      | Dosen        |

#### POST `/referensi`

**Request Body:**

```json
{
  "judul": "Software Engineering: A Practitioner's Approach",
  "penulis": "Roger S. Pressman",
  "tahun": 2024,
  "edisi": "9th",
  "penerbit": "McGraw-Hill",
  "isbn": "978-1260570427",
  "jenis": "buku_utama",
  "url": "https://www.mheducation.com/pressman-se"
}
```

---

### 4. RPS (Rencana Pembelajaran Semester)

| Method   | Endpoint               | Deskripsi                           | Auth    | Role         |
|----------|------------------------|-------------------------------------|---------|--------------|
| GET      | `/rps`                 | Daftar RPS                          | Ya      | Semua        |
| GET      | `/rps/{id}`            | Detail RPS lengkap                  | Ya      | Semua        |
| POST     | `/rps`                 | Buat RPS baru                       | Ya      | Dosen        |
| PUT      | `/rps/{id}`            | Perbarui RPS                        | Ya      | Dosen        |
| DELETE   | `/rps/{id}`            | Hapus RPS (soft delete)             | Ya      | Dosen        |
| POST     | `/rps/{id}/submit-review` | Ajukan RPS untuk review         | Ya      | Dosen        |
| POST     | `/rps/{id}/duplicate`  | Duplikasi RPS                       | Ya      | Dosen        |

#### POST `/rps`

**Request Body:**

```json
{
  "mata_kuliah_id": 42,
  "semester_id": 5,
  "dosen_pengampu_ids": [1, 16],
  "cpl_ids": [3, 7],
  "deskripsi": "RPS untuk mata kuliah Pemrograman Web Lanjut.",
  "prasyarat": "TIF-201 Pemrograman Web Dasar",
  "bahan_kajian": "Framework modern, REST API, Single Page Application",
  "metode_pembelajaran_ids": [1, 2, 3],
  "referensi_ids": [10, 12],
  "assessment_ids": [1, 2, 5]
}
```

**Response (201):**

```json
{
  "data": {
    "id": 88,
    "kode": "RPS-TIF301-20251",
    "mata_kuliah": "Pemrograman Web Lanjut",
    "status": "draft",
    "created_at": "2025-01-15T10:00:00Z"
  },
  "message": "RPS berhasil dibuat."
}
```

#### PUT `/rps/{id}`

**Request Body:**

```json
{
  "deskripsi": "RPS diperbarui untuk mata kuliah Pemrograman Web Lanjut semester ganjil 2025/2026.",
  "prasyarat": "TIF-201 Pemrograman Web Dasar (minimal C)",
  "cpl_ids": [3, 7, 12],
  "referensi_ids": [10, 12, 25]
}
```

#### GET `/rps/{id}`

**Response (200):**

```json
{
  "data": {
    "id": 88,
    "kode": "RPS-TIF301-20251",
    "status": "draft",
    "mata_kuliah": {
      "id": 42,
      "kode": "TIF-301",
      "nama": "Pemrograman Web Lanjut",
      "sks_teori": 2,
      "sks_praktik": 1
    },
    "semester": {
      "id": 5,
      "nama": "Semester Ganjil 2025/2026"
    },
    "dosen_pengampu": [
      { "id": 1, "nama": "Dr. Ahmad Fauzi" },
      { "id": 16, "nama": "Dr. Budi Santoso" }
    ],
    "cpl": [
      { "id": 3, "kode": "CPL-03", "deskripsi": "Mampu menganalisis kebutuhan sistem..." }
    ],
    "cpml": [],
    "sub_cpmk": [],
    "materi_pertemuan": [],
    "referensi": [],
    "assessment": [],
    "versions": [],
    "created_at": "2025-01-15T10:00:00Z",
    "updated_at": "2025-01-15T10:00:00Z"
  }
}
```

#### POST `/rps/{id}/submit-review`

**Response (200):**

```json
{
  "data": {
    "rps_id": 88,
    "status": "review",
    "submitted_at": "2025-01-15T11:00:00Z"
  },
  "message": "RPS berhasil diajukan untuk review."
}
```

#### POST `/rps/{id}/duplicate`

**Request Body:**

```json
{
  "mata_kuliah_id": 43,
  "semester_id": 5
}
```

**Response (201):**

```json
{
  "data": {
    "id": 89,
    "kode": "RPS-TIF302-20251",
    "status": "draft",
    "sumber_rps_id": 88
  },
  "message": "RPS berhasil diduplikasi."
}
```

---

### 5. Workflow

| Method   | Endpoint                     | Deskripsi                         | Auth    | Role                  |
|----------|------------------------------|-----------------------------------|---------|-----------------------|
| POST     | `/workflow/rps/{id}/review`  | Kirim hasil review RPS            | Ya      | Reviewer, Kaprodi     |
| POST     | `/workflow/rps/{id}/approve` | Setujui RPS                       | Ya      | Kaprodi               |
| POST     | `/workflow/rps/{id}/publish` | Publikasikan RPS                  | Ya      | Kaprodi               |
| POST     | `/workflow/rps/{id}/archive` | Arsipkan RPS                      | Ya      | Kaprodi               |

#### POST `/workflow/rps/{id}/review`

**Request Body:**

```json
{
  "status": "revisi",
  "catatan": "CPL perlu disesuaikan dengan profil lulusan baru. Metode assessment pada CPMK-03 perlu diperjelas.",
  "checklist": {
    "kesesuaian_cpl": false,
    "kesesuaian_pustaka": true,
    "kesesuaian_rubrik": false
  }
}
```

**Response (200):**

```json
{
  "data": {
    "rps_id": 88,
    "status": "revisi",
    "reviewer_id": 5,
    "catatan": "CPL perlu disesuaikan dengan profil lulusan baru.",
    "reviewed_at": "2025-01-16T09:00:00Z"
  },
  "message": "Review RPS berhasil dikirim."
}
```

#### POST `/workflow/rps/{id}/approve`

**Request Body:**

```json
{
  "catatan": "RPS telah memenuhi seluruh ketentuan OBE."
}
```

**Response (200):**

```json
{
  "data": {
    "rps_id": 88,
    "status": "disetujui",
    "approved_by": 5,
    "approved_at": "2025-01-16T10:00:00Z"
  },
  "message": "RPS berhasil disetujui."
}
```

#### POST `/workflow/rps/{id}/publish`

**Response (200):**

```json
{
  "data": {
    "rps_id": 88,
    "status": "publish",
    "published_at": "2025-01-16T11:00:00Z"
  },
  "message": "RPS berhasil dipublikasikan."
}
```

#### POST `/workflow/rps/{id}/archive`

**Response (200):**

```json
{
  "data": {
    "rps_id": 88,
    "status": "arsip",
    "archived_at": "2025-01-16T14:00:00Z"
  },
  "message": "RPS berhasil diarsipkan."
}
```

---

### 6. AI (Kecerdasan Buatan)

| Method   | Endpoint               | Deskripsi                               | Auth    | Role         |
|----------|------------------------|-----------------------------------------|---------|--------------|
| POST     | `/ai/generate-cpmk`    | Generate CPMK dari CPL dan mata kuliah  | Ya      | Dosen        |
| POST     | `/ai/generate-subcpmk` | Generate Sub-CPMK dari CPMK             | Ya      | Dosen        |
| POST     | `/ai/validate`         | Validasi RPS dengan AI                  | Ya      | Dosen        |
| POST     | `/ai/review`           | Review RPS oleh AI                      | Ya      | Dosen        |

#### POST `/ai/generate-cpmk`

**Request Body:**

```json
{
  "mata_kuliah": "Pemrograman Web Lanjut",
  "deskripsi_mk": "Mata kuliah ini membahas pengembangan aplikasi web modern menggunakan framework Laravel, REST API, dan SPA.",
  "sks_teori": 2,
  "sks_praktik": 1,
  "semester": 5,
  "cpl": [
    {
      "kode": "CPL-03",
      "deskripsi": "Mampu merancang dan mengembangkan sistem perangkat lunak berbasis web."
    },
    {
      "kode": "CPL-05",
      "deskripsi": "Mampu menerapkan prinsip-prinsip rekayasa perangkat lunak dalam pengembangan aplikasi."
    }
  ]
}
```

**Response (200):**

```json
{
  "data": {
    "cpml": [
      {
        "kode": "CPMK-01",
        "deskripsi": "Mahasiswa mampu merancang arsitektur aplikasi web menggunakan framework Laravel.",
        "cpl_terkait": ["CPL-03"],
        "bobot": 30
      },
      {
        "kode": "CPMK-02",
        "deskripsi": "Mahasiswa mampu mengembangkan REST API yang memenuhi standar industri.",
        "cpl_terkait": ["CPL-03", "CPL-05"],
        "bobot": 40
      },
      {
        "kode": "CPMK-03",
        "deskripsi": "Mahasiswa mampu menerapkan prinsip clean code dan testing dalam pengembangan aplikasi.",
        "cpl_terkait": ["CPL-05"],
        "bobot": 30
      }
    ]
  },
  "meta": {
    "model": "gpt-4o",
    "tokens_used": 1250,
    "generated_at": "2025-01-15T12:00:00Z"
  }
}
```

#### POST `/ai/generate-subcpmk`

**Request Body:**

```json
{
  "mata_kuliah": "Pemrograman Web Lanjut",
  "cpml": [
    {
      "kode": "CPMK-01",
      "deskripsi": "Mahasiswa mampu merancang arsitektur aplikasi web menggunakan framework Laravel."
    }
  ],
  "jumlah_pertemuan": 16
}
```

**Response (200):**

```json
{
  "data": {
    "sub_cpmk": [
      {
        "kode": "Sub-CPMK-01",
        "deskripsi": "Mahasiswa mampu menjelaskan arsitektur MVC pada Laravel.",
        "cpml_terkait": "CPMK-01",
        "pertemuan": 1
      },
      {
        "kode": "Sub-CPMK-02",
        "deskripsi": "Mahasiswa mampu mengimplementasikan routing dan controller pada Laravel.",
        "cpml_terkait": "CPMK-01",
        "pertemuan": 2
      }
    ]
  },
  "meta": {
    "model": "gpt-4o",
    "tokens_used": 890,
    "generated_at": "2025-01-15T12:05:00Z"
  }
}
```

#### POST `/ai/validate`

**Request Body:**

```json
{
  "rps_id": 88
}
```

**Response (200):**

```json
{
  "data": {
    "valid": false,
    "skor_keseluruhan": 72,
    "rekomendasi": [
      {
        "kategori": "cpl_mapping",
        "pesan": "CPL-03 belum memiliki CPMK yang cukup. CPMK yang ada hanya mencakup 50% dimensi CPL.",
        "severity": "high"
      },
      {
        "kategori": "assessment_alignment",
        "pesan": "CPMK-02 belum memiliki instrument assessment yang sesuai.",
        "severity": "medium"
      },
      {
        "kategori": "learning_materials",
        "pesan": "Materi pertemuan 9-16 belum diisi.",
        "severity": "high"
      }
    ]
  },
  "meta": {
    "model": "gpt-4o",
    "tokens_used": 2100,
    "validated_at": "2025-01-15T12:10:00Z"
  }
}
```

#### POST `/ai/review`

**Request Body:**

```json
{
  "rps_id": 88
}
```

**Response (200):**

```json
{
  "data": {
    "skor_keseluruhan": 78,
    "review": "RPS ini secara umum sudah baik dengan struktur OBE yang jelas. Beberapa perbaikan yang disarankan: 1) Penyelarasan CPMK terhadap CPL perlu diperkuat, terutama pada dimensi afektif. 2) Metode pembelajaran pada Sub-CPMK-05 sebaiknya lebih interaktif. 3) Referensi perlu ditambahkan sumber terbaru (2023-2025).",
    "saran_per_komponen": [
      {
        "komponen": "cpl_cpmk",
        "skor": 70,
        "catatan": "Pemetaan CPL ke CPMK sudah tepat namun perlu penambahan CPMK untuk mencakup aspek soft skill."
      },
      {
        "komponen": "assessment",
        "skor": 75,
        "catatan": "Bobot assessment sudah proporsional namun rubrik penilaian perlu diperjelas."
      },
      {
        "komponen": "pembelajaran",
        "skor": 85,
        "catatan": "Metode pembelajaran sudah variatif dan sesuai dengan capaian."
      },
      {
        "komponen": "referensi",
        "skor": 80,
        "catatan": "Referensi cukup lengkap, tambahkan jurnal terbaru."
      }
    ]
  },
  "meta": {
    "model": "gpt-4o",
    "tokens_used": 3200,
    "reviewed_at": "2025-01-15T12:15:00Z"
  }
}
```

---

### 7. Export

| Method   | Endpoint               | Deskripsi                            | Auth    | Role                  |
|----------|------------------------|--------------------------------------|---------|-----------------------|
| POST     | `/export/word/{id}`    | Export RPS ke format DOCX            | Ya      | Dosen, Kaprodi        |
| POST     | `/export/pdf/{id}`     | Export RPS ke format PDF             | Ya      | Dosen, Kaprodi        |
| POST     | `/export/batch`        | Batch export multiple RPS            | Ya      | Kaprodi               |

#### POST `/export/word/{id}`

**Request Body (opsional):**

```json
{
  "template_id": 2,
  "include_review": true
}
```

**Response (200):**

```json
{
  "data": {
    "download_url": "https://api.rpsobe.id/v1/exports/RPS-TIF301-20251-20250115.docx",
    "expires_at": "2025-01-16T12:00:00Z"
  },
  "message": "Dokumen Word berhasil dibuat."
}
```

#### POST `/export/pdf/{id}`

**Request Body (opsional):**

```json
{
  "template_id": 1,
  "include_ttd_kaprodi": true,
  "include_logo_univ": true
}
```

**Response (200):**

```json
{
  "data": {
    "download_url": "https://api.rpsobe.id/v1/exports/RPS-TIF301-20251-20250115.pdf",
    "expires_at": "2025-01-16T12:00:00Z"
  },
  "message": "Dokumen PDF berhasil dibuat."
}
```

#### POST `/export/batch`

**Request Body:**

```json
{
  "rps_ids": [88, 89, 90],
  "format": "pdf",
  "template_id": 1
}
```

**Response (200):**

```json
{
  "data": {
    "job_id": "batch-export-550e8400-e29b-41d4-a716-446655440000",
    "status": "processing",
    "total_files": 3
  },
  "message": "Batch export sedang diproses. Anda akan menerima notifikasi setelah selesai."
}
```

---

### 8. Dashboard

| Method   | Endpoint                  | Deskripsi                           | Auth    | Role                  |
|----------|---------------------------|-------------------------------------|---------|-----------------------|
| GET      | `/dashboard/dosen`        | Dashboard statistik dosen           | Ya      | Dosen                 |
| GET      | `/dashboard/kaprodi`      | Dashboard statistik ketua prodi     | Ya      | Kaprodi               |
| GET      | `/dashboard/fakultas`     | Dashboard statistik dekan           | Ya      | Admin Fakultas        |
| GET      | `/dashboard/universitas`  | Dashboard statistik universitas     | Ya      | Admin Universitas     |

#### GET `/dashboard/dosen`

**Response (200):**

```json
{
  "data": {
    "total_rps": 5,
    "rps_draft": 1,
    "rps_review": 1,
    "rps_disetujui": 2,
    "rps_publish": 3,
    "total_matkul": 4,
    "rps_per_semester": [
      { "semester": "Ganjil 2025/2026", "jumlah": 5 }
    ],
    "recent_activities": [
      { "action": "RPS diajukan review", "rps_kode": "RPS-TIF301-20251", "timestamp": "2025-01-15T11:00:00Z" },
      { "action": "RPS dibuat", "rps_kode": "RPS-TIF302-20251", "timestamp": "2025-01-14T08:00:00Z" }
    ]
  }
}
```

#### GET `/dashboard/kaprodi`

**Response (200):**

```json
{
  "data": {
    "total_rps": 28,
    "rps_per_status": {
      "draft": 5,
      "review": 8,
      "disetujui": 10,
      "publish": 12,
      "arsip": 3
    },
    "rata_rata_skor_review": 78.5,
    "rps_per_dosen": [
      { "dosen": "Dr. Ahmad Fauzi", "jumlah": 5, "skor_rata": 82 },
      { "dosen": "Dr. Budi Santoso", "jumlah": 4, "skor_rata": 75 }
    ],
    "pending_review": 8,
    "rps_disetujui_bulan_ini": 3,
    "rps_per_semester": [
      { "semester": "Ganjil 2025/2026", "jumlah": 12 },
      { "semester": "Genap 2024/2025", "jumlah": 16 }
    ]
  }
}
```

#### GET `/dashboard/fakultas`

**Response (200):**

```json
{
  "data": {
    "total_prodi": 5,
    "total_matkul": 120,
    "total_dosen": 85,
    "total_rps": 320,
    "rps_publish": 245,
    "persentase_rps_publish": 76.6,
    "rps_per_prodi": [
      { "prodi": "Teknik Informatika", "total": 28, "publish": 22 },
      { "prodi": "Sistem Informasi", "total": 24, "publish": 18 },
      { "prodi": "Teknik Elektro", "total": 20, "publish": 16 }
    ],
    "rps_per_semester": [],
    "tren_pengisian": [
      { "bulan": "2025-01", "rps_dibuat": 12, "rps_disetujui": 8 }
    ]
  }
}
```

#### GET `/dashboard/universitas`

**Response (200):**

```json
{
  "data": {
    "total_fakultas": 7,
    "total_prodi": 28,
    "total_dosen": 560,
    "total_matkul": 980,
    "total_rps": 2450,
    "rps_publish": 1890,
    "persentase_rps_publish": 77.1,
    "rps_per_fakultas": [
      { "fakultas": "Fakultas Teknik", "total": 320, "publish": 245 },
      { "fakultas": "Fakultas Ekonomi", "total": 280, "publish": 210 }
    ],
    "tren_tahunan": [
      { "tahun": 2024, "rps_dibuat": 480, "rps_publish": 420 },
      { "tahun": 2025, "rps_dibuat": 320, "rps_publish": 280 }
    ]
  }
}
```

---

### 9. Laporan (Report)

| Method   | Endpoint                   | Deskripsi                           | Auth    | Role                  |
|----------|----------------------------|-------------------------------------|---------|-----------------------|
| GET      | `/report/completion`       | Laporan kelengkapan RPS             | Ya      | Kaprodi, Admin        |
| GET      | `/report/quality`          | Laporan kualitas RPS                | Ya      | Kaprodi, Admin        |
| GET      | `/report/audit`            | Laporan audit log aktivitas         | Ya      | Admin                |

#### GET `/report/completion`

**Query Parameters:** `?fakultas_id=2&program_studi_id=5&semester_id=5&kurikulum_id=3`

**Response (200):**

```json
{
  "data": {
    "total_matkul": 52,
    "rps_terisi": 40,
    "rps_belum": 12,
    "persentase_kelengkapan": 76.9,
    "detail_per_matkul": [
      {
        "matkul": "Pemrograman Web Lanjut",
        "kode": "TIF-301",
        "status_rps": "publish",
        "kelengkapan": 100
      },
      {
        "matkul": "Keamanan Jaringan",
        "kode": "TIF-402",
        "status_rps": null,
        "kelengkapan": 0
      }
    ]
  }
}
```

#### GET `/report/quality`

**Query Parameters:** `?fakultas_id=2&program_studi_id=5&semester_id=5`

**Response (200):**

```json
{
  "data": {
    "rata_rata_skor": 78.5,
    "kategori": [
      { "nama": "Sangat Baik", "rentang": "85-100", "jumlah": 5 },
      { "nama": "Baik", "rentang": "70-84", "jumlah": 18 },
      { "nama": "Cukup", "rentang": "55-69", "jumlah": 12 },
      { "nama": "Kurang", "rentang": "<55", "jumlah": 4 }
    ],
    "detail_per_dimensi": [
      { "dimensi": "CPL-CPMK Alignment", "rata_rata": 76 },
      { "dimensi": "Assessment Quality", "rata_rata": 72 },
      { "dimensi": "Learning Method", "rata_rata": 82 },
      { "dimensi": "Reference Quality", "rata_rata": 80 },
      { "dimensi": "Material Completeness", "rata_rata": 78 }
    ],
    "skor_per_rps": [
      { "rps_kode": "RPS-TIF301-20251", "skor": 82, "kategori": "Baik" },
      { "rps_kode": "RPS-TIF302-20251", "skor": 65, "kategori": "Cukup" }
    ]
  }
}
```

#### GET `/report/audit`

**Query Parameters:** `?start_date=2025-01-01&end_date=2025-01-31&user_id=1&action=login`

**Response (200):** (dengan metadata pagination)

```json
{
  "data": [
    {
      "id": 4521,
      "user": "Dr. Ahmad Fauzi",
      "action": "login",
      "model_type": "User",
      "model_id": 1,
      "old_values": null,
      "new_values": { "ip": "192.168.1.100", "user_agent": "Chrome/120.0" },
      "created_at": "2025-01-15T07:30:00Z"
    },
    {
      "id": 4522,
      "user": "Dr. Ahmad Fauzi",
      "action": "updated",
      "model_type": "RPS",
      "model_id": 88,
      "old_values": { "status": "draft" },
      "new_values": { "status": "review" },
      "created_at": "2025-01-15T11:00:00Z"
    }
  ]
}
```

---

## Webhooks (Rencana Versi v2)

Webhooks direncanakan untuk versi API mendatang. Beberapa event yang akan didukung:

- `rps.created` — RPS baru dibuat
- `rps.submitted_for_review` — RPS diajukan review
- `rps.reviewed` — RPS selesai direview
- `rps.approved` — RPS disetujui
- `rps.published` — RPS dipublikasikan
- `user.invited` — User baru diundang
- `export.completed` — Export batch selesai

Konfigurasi webhook akan tersedia melalui dashboard tenant.

---

## Changelog

### v1 (Saat Ini)

- Seluruh endpoint di atas tersedia
- Autentikasi Sanctum
- Rate limiting per endpoint
- Pagination standar

### v2 (Direncanakan)

- Webhooks
- API Key untuk integrasi eksternal
- GraphQL untuk query kompleks
- Batch endpoint untuk bulk CRUD
- Streaming response untuk export file besar
- SLA monitoring endpoint
