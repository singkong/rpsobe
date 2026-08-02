# 23 — API Overview

## Prinsip Desain API

API RPS OBE dirancang mengikuti prinsip **RESTful** dengan konsistensi di seluruh endpoint. Prinsip-prinsip utama:

| Prinsip | Implementasi |
|---------|--------------|
| **Resource-Oriented** | Setiap endpoint merepresentasikan resource (noun), bukan aksi (verb) |
| **Stateless** | Setiap request bersifat independen, autentikasi via Bearer Token |
| **JSON Response** | Semua response dalam format `application/json` |
| **HTTP Verbs** | GET (baca), POST (buat), PUT/PATCH (perbarui), DELETE (hapus) |
| **Consistent Naming** | Snake case untuk JSON keys, plural untuk resource URL |
| **Versioned** | Semua endpoint memiliki prefix `/api/v1/` |
| **Standard HTTP Codes** | 200/201 untuk sukses, 4xx untuk client error, 5xx untuk server error |
| **HATEOAS (Future)** | Link navigasi dalam response untuk discoverability |

---

## API Versioning

| Versi | Status | Base URL | Keterangan |
|-------|--------|----------|------------|
| v1 | Active | `/api/v1` | Versi pertama, mencakup seluruh fungsionalitas MVP |

**Versioning Strategy:** URL-based versioning (`/api/v{n}/`). Ketika versi baru dirilis, versi lama tetap didukung minimal 12 bulan dengan deprecation notice di header response.

```
Deprecation: true
Sunset: Sat, 01 Jan 2028 00:00:00 GMT
```

---

## Autentikasi

### Bearer Token (Sanctum)

Seluruh endpoint API (kecuali `login`, `register`, `password-reset`) mewajibkan autentikasi menggunakan **Bearer Token** yang diterbitkan oleh Laravel Sanctum.

```
Authorization: Bearer <token>
```

### Token Lifecycle

| Tipe Token | Lifetime | Refresh |
|------------|----------|---------|
| Access Token (Login) | 24 jam | Tidak, login ulang |
| Access Token (Remember Me) | 30 hari | Tidak, login ulang |
| Personal Access Token | Unlimited (manual revoke) | Tidak |

### Pembuatan Token

```http
POST /api/v1/auth/login
POST /api/v1/auth/token (Personal Access Token — future)
```

### Pencabutan Token

```http
POST /api/v1/auth/logout
DELETE /api/v1/auth/tokens/{id} (Personal Access Token — future)
```

---

## Base URL Structure

```
# Production
https://{tenant-subdomain}.rpsobe.com/api/v1/

# Development
http://localhost:8000/api/v1/

# Staging
https://staging.rpsobe.com/api/v1/
```

---

## Standard Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operasi berhasil",
  "data": {
    "id": "01J2X5K...",
    "nama_mk": "Pemrograman Web",
    "kode_mk": "TIF-301",
    // ... resource fields
  },
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 95
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Validasi gagal",
  "errors": {
    "nama_mk": ["Nama mata kuliah wajib diisi"],
    "sks": ["SKS harus antara 1 dan 6"]
  }
}
```

### Standard HTTP Status Codes

| Kode | Deskripsi | Digunakan Untuk |
|------|-----------|-----------------|
| 200 | OK | GET, PUT/PATCH berhasil |
| 201 | Created | POST berhasil (resource baru dibuat) |
| 202 | Accepted | Job diterima (export, AI generation) diproses async |
| 204 | No Content | DELETE berhasil |
| 400 | Bad Request | Request tidak valid (parameter salah) |
| 401 | Unauthorized | Token tidak valid / expired |
| 403 | Forbidden | Tidak memiliki permission untuk mengakses resource |
| 404 | Not Found | Resource tidak ditemukan |
| 409 | Conflict | Konflik (misal: duplicate resource) |
| 422 | Unprocessable Entity | Validasi gagal |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |
| 503 | Service Unavailable | Maintenance / down |

---

## Rate Limiting

| Tipe Client | Limit | Window | Keterangan |
|-------------|-------|--------|------------|
| Authenticated User | 120 requests | per menit | Seluruh endpoint |
| Unauthenticated | 20 requests | per menit | Hanya auth endpoints |
| AI endpoints | 20 requests | per menit | Khusus `/api/v1/ai/*` |
| Export endpoints | 5 requests | per menit | Khusus `/api/v1/export/*` |

**Rate Limit Headers:**
```
X-RateLimit-Limit: 120
X-RateLimit-Remaining: 87
Retry-After: 33
```

---

## Pagination

Seluruh endpoint yang mengembalikan daftar resource menggunakan pagination berbasis halaman (page-based pagination).

### Query Parameters

| Parameter | Default | Maksimal | Deskripsi |
|-----------|---------|----------|-----------|
| `page` | 1 | — | Nomor halaman |
| `per_page` | 20 | 100 | Jumlah item per halaman |
| `search` | — | — | Pencarian teks penuh |
| `sort_by` | `created_at` | — | Kolom pengurutan |
| `sort_order` | `desc` | — | Arah pengurutan (`asc`/`desc`) |
| `filter[status]` | — | — | Filter spesifik (multiple allowed) |

### Contoh Request
```
GET /api/v1/rps?page=1&per_page=20&search=pemrograman&sort_by=updated_at&sort_order=desc&filter[status]=draft
```

### Meta Pagination Response
```json
{
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 20,
    "to": 20,
    "total": 95,
    "links": {
      "first": "/api/v1/rps?page=1",
      "last": "/api/v1/rps?page=5",
      "prev": null,
      "next": "/api/v1/rps?page=2"
    }
  }
}
```

---

## Daftar API Endpoints

### Modul 1: Authentication (Auth)

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 1 | `POST` | `/api/v1/auth/login` | Login dengan email & password, return token | ❌ |
| 2 | `POST` | `/api/v1/auth/register` | Registrasi dengan invitation code | ❌ |
| 3 | `POST` | `/api/v1/auth/logout` | Logout dan revoke token | ✅ |
| 4 | `POST` | `/api/v1/auth/forgot-password` | Kirim email reset password | ❌ |
| 5 | `POST` | `/api/v1/auth/reset-password` | Reset password dengan token | ❌ |
| 6 | `POST` | `/api/v1/auth/email/verification-notification` | Kirim ulang email verifikasi | ✅ |
| 7 | `GET` | `/api/v1/auth/me` | Dapatkan data user yang sedang login | ✅ |

#### Detail Endpoint

##### 1. Login
```
POST /api/v1/auth/login
Content-Type: application/json

Request Body:
{
  "email": "dosen@univ.ac.id",
  "password": "securepassword",
  "remember_me": true
}

Response (200):
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "1|abc123token...",
    "user": {
      "id": "01J2X5K...",
      "name": "Dr. Ahmad Fauzi",
      "email": "dosen@univ.ac.id",
      "role": "dosen",
      "tenant_id": "01J2X5L...",
      "tenant_name": "Universitas Indonesia",
      "permissions": ["rps.create", "rps.view-own", ...]
    }
  }
}

Response (422):
{
  "success": false,
  "message": "Kredensial tidak valid",
  "errors": {
    "email": ["Email atau password salah"]
  }
}
```

##### 7. Get Current User
```
GET /api/v1/auth/me
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "data": {
    "id": "01J2X5K...",
    "name": "Dr. Ahmad Fauzi",
    "email": "dosen@univ.ac.id",
    "nidn": "0012345678",
    "role": "dosen",
    "tenant": {
      "id": "01J2X5L...",
      "name": "Universitas Indonesia"
    },
    "program_studi": {
      "id": "01J2X5M...",
      "nama": "Teknik Informatika"
    }
  }
}
```

---

### Modul 2: User Management (User)

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 8 | `GET` | `/api/v1/users` | Daftar semua user dalam tenant | ✅ (user.view-any) |
| 9 | `GET` | `/api/v1/users/{id}` | Detail user | ✅ |
| 10 | `POST` | `/api/v1/users` | Tambah user baru | ✅ (user.create) |
| 11 | `PUT` | `/api/v1/users/{id}` | Update data user | ✅ (user.edit) |
| 12 | `DELETE` | `/api/v1/users/{id}` | Nonaktifkan user (soft delete) | ✅ (user.deactivate) |
| 13 | `POST` | `/api/v1/users/invite` | Undang user via email | ✅ (user.invite) |
| 14 | `POST` | `/api/v1/users/import` | Import user dari CSV | ✅ (user.create) |

#### Detail Endpoint

##### 10. Create User
```
POST /api/v1/users
Authorization: Bearer <token>

Request Body:
{
  "name": "Budi Santoso, M.Kom.",
  "email": "budi@univ.ac.id",
  "nidn": "0098765432",
  "role": "dosen",
  "program_studi_id": "01J2X5M...",
  "password": "TempPass123!",
  "force_password_change": true
}

Response (201):
{
  "success": true,
  "message": "User berhasil dibuat",
  "data": {
    "id": "01J2X5N...",
    "name": "Budi Santoso, M.Kom.",
    "email": "budi@univ.ac.id",
    "nidn": "0098765432",
    "role": "dosen",
    "created_at": "2026-08-01T10:30:00Z"
  }
}
```

##### 13. Invite User
```
POST /api/v1/users/invite
Authorization: Bearer <token>

Request Body:
{
  "email": "dosenbaru@univ.ac.id",
  "role": "dosen",
  "program_studi_id": "01J2X5M..."
}

Response (201):
{
  "success": true,
  "message": "Undangan berhasil dikirim ke dosenbaru@univ.ac.id",
  "data": {
    "invitation_code": "INV-2026-AB12CD",
    "expires_at": "2026-08-08T10:30:00Z"
  }
}
```

---

### Modul 3: Master Data

#### Universitas

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 15 | `GET` | `/api/v1/master/universitas/{id}` | Detail universitas tenant saat ini | ✅ |

#### Fakultas

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 16 | `GET` | `/api/v1/master/fakultas` | Daftar fakultas | ✅ |

#### Program Studi

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 17 | `GET` | `/api/v1/master/program-studi` | Daftar program studi | ✅ |

#### Kurikulum

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 18 | `GET` | `/api/v1/master/kurikulum` | Daftar kurikulum per prodi | ✅ |
| 19 | `GET` | `/api/v1/master/kurikulum/{id}` | Detail kurikulum beserta MK | ✅ |

##### 18. List Kurikulum
```
GET /api/v1/master/kurikulum?prodi_id=01J2X5M...&status=aktif

Response (200):
{
  "success": true,
  "data": [
    {
      "id": "01J2X5P...",
      "nama": "Kurikulum 2024",
      "tahun_mulai": 2024,
      "tahun_berakhir": 2028,
      "total_sks": 144,
      "status": "aktif",
      "jumlah_mk": 62
    }
  ],
  "meta": { ... }
}
```

#### Mata Kuliah

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 20 | `GET` | `/api/v1/master/mata-kuliah` | Daftar mata kuliah | ✅ |

#### CPL

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 21 | `GET` | `/api/v1/master/cpl` | Daftar CPL per prodi | ✅ |
| 22 | `GET` | `/api/v1/master/cpl/{id}` | Detail CPL beserta CPMK | ✅ |

##### 21. List CPL
```
GET /api/v1/master/cpl?prodi_id=01J2X5M...&kategori=SIKAP

Response (200):
{
  "success": true,
  "data": [
    {
      "id": "01J2X5Q...",
      "kode": "CPL-S-01",
      "deskripsi": "Bertakwa kepada Tuhan Yang Maha Esa dan mampu menunjukkan sikap religius",
      "kategori": "SIKAP",
      "profil_lulusan": ["Sarjana Informatika yang beretika"],
      "jumlah_cpmk": 12,
      "jumlah_mk_pendukung": 25
    }
  ],
  "meta": { ... }
}
```

#### Referensi

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 23 | `GET` | `/api/v1/master/referensi` | Daftar referensi | ✅ |
| 24 | `POST` | `/api/v1/master/referensi` | Tambah referensi baru | ✅ |

#### Dosen (Master List)

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 25 | `GET` | `/api/v1/master/dosen` | Daftar dosen | ✅ |

---

### Modul 4: RPS

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 26 | `GET` | `/api/v1/rps` | Daftar RPS (dengan filter) | ✅ (rps.view-own / rps.view-any) |
| 27 | `GET` | `/api/v1/rps/{id}` | Detail RPS lengkap | ✅ |
| 28 | `POST` | `/api/v1/rps` | Buat RPS baru (Draft) | ✅ (rps.create) |
| 29 | `PUT` | `/api/v1/rps/{id}` | Update RPS (Draft/Revision) | ✅ (rps.update-own) |
| 30 | `DELETE` | `/api/v1/rps/{id}` | Hapus RPS (soft delete) | ✅ (rps.delete-own) |
| 31 | `POST` | `/api/v1/rps/{id}/submit-review` | Ajukan RPS untuk review | ✅ (rps.submit-review) |
| 32 | `POST` | `/api/v1/rps/{id}/duplicate` | Duplikasi RPS | ✅ (rps.duplicate) |
| 33 | `GET` | `/api/v1/rps/{id}/versions` | Riwayat versi RPS | ✅ |
| 34 | `GET` | `/api/v1/rps/{id}/history` | Riwayat status/workflow RPS | ✅ |

#### Detail Endpoint

##### 26. List RPS
```
GET /api/v1/rps?page=1&per_page=20&filter[status]=draft&filter[prodi_id]=...&filter[semester_id]=...&search=pemrograman&sort_by=updated_at&sort_order=desc

Response (200):
{
  "success": true,
  "data": [
    {
      "id": "01J2X5R...",
      "kode_mk": "TIF-301",
      "nama_mk": "Pemrograman Web",
      "sks": 3,
      "semester": "Ganjil 2026/2027",
      "dosen_pengampu": "Dr. Ahmad Fauzi",
      "status": "draft",
      "progress": 65,
      "current_version": "v1.2",
      "tanggal_dibuat": "2026-07-15T08:30:00Z",
      "tanggal_diupdate": "2026-08-01T14:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 45,
    "filter": {
      "status": "draft"
    }
  }
}
```

##### 27. Detail RPS (Lengkap)
```
GET /api/v1/rps/01J2X5R...

Response (200):
{
  "success": true,
  "data": {
    "id": "01J2X5R...",
    "informasi": {
      "mata_kuliah": {
        "kode": "TIF-301",
        "nama": "Pemrograman Web",
        "sks": 3,
        "semester": 5,
        "deskripsi": "Mata kuliah ini membahas..."
      },
      "prodi": {
        "id": "...",
        "nama": "Teknik Informatika",
        "jenjang": "S1"
      },
      "kurikulum": {
        "id": "...",
        "nama": "Kurikulum 2024"
      },
      "dosen_pengampu": [
        { "id": "...", "nama": "Dr. Ahmad Fauzi", "nidn": "0012345678" }
      ],
      "tanggal_penyusunan": "2026-07-15"
    },
    "cpl_yang_didukung": [
      {
        "id": "...",
        "kode": "CPL-P-01",
        "deskripsi": "Menguasai konsep teoretis..."
      }
    ],
    "cpmk": [
      {
        "id": "...",
        "kode": "CPMK-01",
        "deskripsi": "Mahasiswa mampu merancang aplikasi web berbasis framework",
        "cpl_terkait": ["CPL-P-01", "CPL-KK-02"],
        "level_taksonomi": "C6"
      }
    ],
    "sub_cpmk": [
      {
        "id": "...",
        "kode": "Sub-CPMK-01",
        "deskripsi": "Mahasiswa mampu menjelaskan arsitektur MVC",
        "cpmk_terkait": "CPMK-01",
        "level_taksonomi": "C2",
        "pertemuan": 1
      }
    ],
    "materi_per_pertemuan": [
      {
        "pertemuan": 1,
        "materi": "Pengenalan MVC dan Laravel Framework",
        "sub_cpmk": ["Sub-CPMK-01"],
        "metode": "Ceramah, Diskusi",
        "aktivitas": "Praktikum instalasi Laravel",
        "estimasi_waktu": 150
      }
    ],
    "assessment": [
      {
        "id": "...",
        "nama": "UTS",
        "jenis": "sumatif",
        "bobot": 30,
        "sub_cpmk_diukur": ["Sub-CPMK-01", "Sub-CPMK-02"],
        "rubrik": { ... }
      }
    ],
    "referensi": [
      {
        "format_apa": "Stauffer, M. (2019). Laravel: Up & Running (2nd ed.). O'Reilly Media.",
        "jenis": "buku",
        "sumber": "primer"
      }
    ],
    "meta": {
      "status": "draft",
      "current_version": "v1.2",
      "dibuat_oleh": "Dr. Ahmad Fauzi",
      "progress": 65,
      "skor_validasi": 85
    }
  }
}
```

##### 28. Create RPS
```
POST /api/v1/rps
Authorization: Bearer <token>

Request Body:
{
  "mata_kuliah_id": "01J2X5S...",
  "kurikulum_id": "01J2X5P...",
  "semester_id": "01J2X5T...",
  "dosen_pengampu_ids": ["01J2X5K..."],
  "tanggal_penyusunan": "2026-08-01"
}

Response (201):
{
  "success": true,
  "message": "RPS berhasil dibuat",
  "data": {
    "id": "01J2X5R...",
    "status": "draft",
    "progress": 5,
    "redirect_url": "/api/v1/rps/01J2X5R..."
  }
}
```

##### 31. Submit for Review
```
POST /api/v1/rps/01J2X5R.../submit-review
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "message": "RPS berhasil diajukan untuk review",
  "data": {
    "rps_id": "01J2X5R...",
    "status_sebelum": "draft",
    "status_sesudah": "review",
    "version_baru": "v2.0",
    "tanggal_submit": "2026-08-02T09:00:00Z"
  }
}

Response (422):
{
  "success": false,
  "message": "RPS belum siap diajukan",
  "errors": {
    "progress": ["RPS harus 100% lengkap sebelum diajukan untuk review"],
    "bobot_assessment": ["Total bobot assessment harus 100%, saat ini 85%"],
    "minimal_referensi": ["Minimal 3 referensi, saat ini 2"]
  }
}
```

---

### Modul 5: AI

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 35 | `POST` | `/api/v1/ai/generate` | Generate konten RPS dengan AI | ✅ (ai.generate) |
| 36 | `POST` | `/api/v1/ai/validate/{rps_id}` | Validasi RPS dengan AI | ✅ (ai.validate) |
| 37 | `POST` | `/api/v1/ai/review/{rps_id}` | Review RPS dengan AI | ✅ (ai.review) |
| 38 | `GET` | `/api/v1/ai/jobs/{job_id}` | Cek status job AI async | ✅ |

#### Detail Endpoint

##### 35. AI Generate
```
POST /api/v1/ai/generate
Authorization: Bearer <token>

Request Body:
{
  "type": "cpmk",
  "context": {
    "rps_id": "01J2X5R...",
    "cpl_ids": ["01J2X5Q..."],
    "nama_mk": "Pemrograman Web",
    "sks": 3
  },
  "options": {
    "jumlah": 5,
    "temperature": 0.7
  }
}

Response (202):
{
  "success": true,
  "message": "Generasi AI sedang diproses",
  "data": {
    "job_id": "01J2X5U...",
    "status": "processing",
    "estimated_time": 15
  }
}
```

##### 38. Cek Status AI Job
```
GET /api/v1/ai/jobs/01J2X5U...

Response (200):
{
  "success": true,
  "data": {
    "job_id": "01J2X5U...",
    "status": "completed",
    "result": {
      "cpmk": [
        {
          "kode": "CPMK-01",
          "deskripsi": "Mahasiswa mampu merancang aplikasi web berbasis framework MVC",
          "cpl_terkait": ["CPL-P-01"],
          "level_taksonomi": "C6"
        }
      ]
    },
    "cost": {
      "tokens_used": 450,
      "estimated_cost_rp": 75
    }
  }
}
```

---

### Modul 6: Export

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 39 | `POST` | `/api/v1/export/rps/{id}/word` | Export RPS ke Word (.docx) | ✅ (rps.export) |
| 40 | `POST` | `/api/v1/export/rps/{id}/pdf` | Export RPS ke PDF | ✅ (rps.export) |
| 41 | `POST` | `/api/v1/export/rps/batch` | Batch export multiple RPS | ✅ (rps.export) |
| 42 | `GET` | `/api/v1/export/jobs/{job_id}` | Cek status job export | ✅ |
| 43 | `GET` | `/api/v1/export/download/{file_hash}` | Download file hasil export | ✅ |

#### Detail Endpoint

##### 39. Export to Word
```
POST /api/v1/export/rps/01J2X5R.../word
Authorization: Bearer <token>

Request Body:
{
  "template_id": "01J2X5V..." (opsional, gunakan default jika tidak diisi),
  "include_cover": true,
  "include_pengesahan": true,
  "watermark": "DRAFT" (opsional)
}

Response (202):
{
  "success": true,
  "message": "Export sedang diproses",
  "data": {
    "job_id": "01J2X5W...",
    "status": "processing",
    "estimated_time": 30
  }
}
```

##### 43. Download Export
```
GET /api/v1/export/download/abc123hash...

Response (200):
Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document
Content-Disposition: attachment; filename="RPS_TIF301_Pemrograman_Web_v1.2.docx"
```

---

### Modul 7: Dashboard

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 44 | `GET` | `/api/v1/dashboard/stats` | Statistik dashboard sesuai role | ✅ |
| 45 | `GET` | `/api/v1/dashboard/charts` | Data chart dashboard | ✅ |

#### Detail Endpoint

##### 44. Dashboard Stats
```
GET /api/v1/dashboard/stats
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "data": {
    "rps": {
      "total": 15,
      "draft": 5,
      "review": 3,
      "revision": 2,
      "approved": 3,
      "published": 2
    },
    "validasi": {
      "skor_rata_rata": 78.5,
      "perlu_perhatian": 4
    },
    "review": {
      "menunggu_review": 3,
      "sudah_direview": 12,
      "rata_rata_waktu_review_hari": 5.2
    },
    "ai": {
      "total_generate_bulan_ini": 45,
      "sisa_kuota": 155,
      "estimasi_biaya_bulan_ini_rp": 6750
    }
  }
}
```

---

### Modul 8: Report

| # | Method | Endpoint | Deskripsi | Auth |
|---|--------|----------|-----------|------|
| 46 | `GET` | `/api/v1/reports/rps-status` | Laporan status RPS per prodi | ✅ (report.view) |
| 47 | `GET` | `/api/v1/reports/alignment-summary` | Ringkasan constructive alignment | ✅ (report.view) |
| 48 | `GET` | `/api/v1/reports/export` | Export laporan (Excel/CSV) | ✅ (report.export) |

---

## Error Codes Reference

| Kode Error | Pesan | Penjelasan |
|------------|-------|------------|
| `AUTH_001` | Token tidak valid atau kedaluwarsa | Token Bearer tidak valid atau sudah expired |
| `AUTH_002` | Tidak memiliki akses | User tidak memiliki permission yang diperlukan |
| `AUTH_003` | Akun dinonaktifkan | Akun user telah dideaktivasi oleh admin |
| `AUTH_004` | Email belum terverifikasi | User harus memverifikasi email terlebih dahulu |
| `VAL_001` | Validasi input gagal | Parameter atau body request tidak valid |
| `VAL_002` | Data tidak ditemukan | Resource yang diminta tidak ditemukan di database |
| `BIZ_001` | Status transisi tidak valid | Workflow tidak mengizinkan perubahan status tersebut |
| `BIZ_002` | RPS belum lengkap | Progress RPS < 100%, tidak bisa diajukan review |
| `BIZ_003` | Bobot tidak 100% | Total bobot assessment harus tepat 100% |
| `BIZ_004` | Duplicate resource | Resource sudah ada untuk semester/kurikulum yang sama |
| `AI_001` | Kuota AI habis | Tenant melebihi budget AI bulanan |
| `AI_002` | AI timeout | OpenAI API timeout setelah 30 detik |
| `AI_003` | Rate limit AI | Melebihi 20 request per menit |
| `EXP_001` | Template tidak ditemukan | Template export untuk tenant tidak tersedia |
| `SYS_001` | Internal server error | Kesalahan tidak terduga di sisi server |

---

## Future: Public API untuk Integrasi SISTER/PDDIKTI

RPS OBE akan menyediakan **Public API** untuk integrasi dengan sistem eksternal seperti:

| Sistem | Tujuan | Prioritas |
|--------|--------|-----------|
| SISTER Kemdikbud | Pelaporan BKD dan RPS dosen | P2 |
| PDDIKTI Feeder | Sinkronisasi data MK dan mahasiswa | P2 |
| SIM Akademik Kampus | SSO dan sinkronisasi data | P2 |
| LMS (Moodle, dll) | Sinkronisasi RPS ke LMS | P3 |

Public API akan menggunakan:
- API Key (bukan Bearer Token user)
- Rate limiting yang berbeda (1000 req/menit per API key)
- Dokumentasi OpenAPI 3.0 (Swagger)
- Webhook untuk notifikasi event

---

## Changelog & API Stability

| Kebijakan | Detail |
|-----------|--------|
| **Deprecation Notice** | Minimal 12 bulan sebelum endpoint dihapus |
| **Breaking Changes** | Hanya pada major version API baru |
| **Non-Breaking Additions** | Field baru di response dianggap non-breaking |
| **Changelog** | Dipublikasikan di `/docs/api/changelog` |
| **Postman Collection** | Disediakan untuk development dan testing |

---

**Navigasi:** [Sebelumnya: System Architecture](22-system-architecture.md) | [Daftar Isi](../README.md) | [Berikutnya: Data Flow](24-data-flow.md)
