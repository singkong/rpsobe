# 19 — Permission Matrix

## Role Definisi

| No | Role | Slug | Deskripsi |
|----|------|------|-----------|
| 1 | Super Admin | `super-admin` | Akses penuh ke seluruh platform dan tenant |
| 2 | Admin Universitas | `admin-univ` | Akses penuh ke 1 universitas |
| 3 | Admin Fakultas | `admin-fakultas` | Akses ke 1 fakultas |
| 4 | Admin Program Studi | `admin-prodi` | Akses ke 1 program studi |
| 5 | Kaprodi | `kaprodi` | Akses ke 1 program studi + approval |
| 6 | Reviewer | `reviewer` | Akses mereview RPS |
| 7 | Dosen | `dosen` | Akses menyusun RPS sendiri |
| 8 | LPM | `lpm` | Akses monitoring mutu |
| 9 | Mahasiswa | `mahasiswa` | Akses melihat RPS published |

---

## Permission Matrix — Master Data

| Permission | Super Admin | Admin Univ | Admin Fak | Admin Prodi | Kaprodi | Dosen | Reviewer | LPM | Mahasiswa |
|------------|:-----------:|:----------:|:---------:|:-----------:|:-------:|:-----:|:--------:|:---:|:---------:|
| **Universitas** |||||||||
| universitas.view-any | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| universitas.view | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| universitas.create | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| universitas.update | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| universitas.delete | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Fakultas** |||||||||
| fakultas.view-any | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| fakultas.view | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| fakultas.create | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| fakultas.update | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| fakultas.delete | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Program Studi** |||||||||
| prodi.view-any | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| prodi.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| prodi.create | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| prodi.update | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| prodi.delete | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Kurikulum** |||||||||
| kurikulum.view-any | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ |
| kurikulum.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| kurikulum.create | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| kurikulum.update | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| kurikulum.delete | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Mata Kuliah** |||||||||
| matkul.view-any | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ |
| matkul.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| matkul.create | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| matkul.update | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| matkul.delete | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **CPL** |||||||||
| cpl.view-any | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ |
| cpl.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| cpl.create | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| cpl.update | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| cpl.delete | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Profil Lulusan** |||||||||
| profil-lulusan.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| profil-lulusan.create | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| profil-lulusan.update | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| profil-lulusan.delete | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Dosen** |||||||||
| dosen.view-any | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ |
| dosen.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| dosen.create | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| dosen.update | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| dosen.delete | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Referensi** |||||||||
| referensi.view-any | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ |
| referensi.view | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| referensi.create | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| referensi.update | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| referensi.delete | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |

---

## Permission Matrix — RPS

| Permission | Super Admin | Admin Univ | Admin Fak | Admin Prodi | Kaprodi | Dosen | Reviewer | LPM | Mahasiswa |
|------------|:-----------:|:----------:|:---------:|:-----------:|:-------:|:-----:|:--------:|:---:|:---------:|
| rps.view-any | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ |
| rps.view-own | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| rps.view-published | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| rps.create | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| rps.update-own | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| rps.update-any | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| rps.delete-own | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| rps.delete-any | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| rps.submit-review | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| rps.review | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ | ❌ | ❌ |
| rps.approve | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| rps.publish | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| rps.archive | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| rps.duplicate | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| rps.export | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## Permission Matrix — AI

| Permission | Super Admin | Admin Univ | Admin Fak | Admin Prodi | Kaprodi | Dosen | Reviewer | LPM | Mahasiswa |
|------------|:-----------:|:----------:|:---------:|:-----------:|:-------:|:-----:|:--------:|:---:|:---------:|
| ai.generate | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |
| ai.validate | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ | ✅ | ❌ |
| ai.review | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ | ❌ | ❌ |
| ai.validate-massal | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |

---

## Permission Matrix — Dashboard & Reporting

| Permission | Super Admin | Admin Univ | Admin Fak | Admin Prodi | Kaprodi | Dosen | Reviewer | LPM | Mahasiswa |
|------------|:-----------:|:----------:|:---------:|:-----------:|:-------:|:-----:|:--------:|:---:|:---------:|
| dashboard.super-admin | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| dashboard.univ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| dashboard.fakultas | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| dashboard.prodi | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| dashboard.dosen | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| dashboard.lpm | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| report.view | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ |
| report.export | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ |

---

## Permission Matrix — Admin

| Permission | Super Admin | Admin Univ | Admin Fak | Admin Prodi | Kaprodi | Dosen | Reviewer | LPM | Mahasiswa |
|------------|:-----------:|:----------:|:---------:|:-----------:|:-------:|:-----:|:--------:|:---:|:---------:|
| user.view-any | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| user.create | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| user.edit | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| user.deactivate | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| user.invite | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| audit.view | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ |
| audit.export | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| template.manage | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| notification.manage | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| tenant.manage | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| billing.manage | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## Tenant Scoping

Setiap role selain Super Admin memiliki `tenant_id` yang membatasi akses:

| Role | Scope |
|------|-------|
| Super Admin | Semua tenant |
| Admin Universitas | 1 universitas (tenant_id) |
| Admin Fakultas | 1 fakultas (tenant_id + fakultas_id) |
| Admin Prodi | 1 prodi (tenant_id + prodi_id) |
| Kaprodi | 1 prodi (tenant_id + prodi_id) |
| Reviewer | Multiple prodi (assignment-based) |
| Dosen | Multiple MK (tenant_id + prodi_id) |
| LPM | 1 universitas (tenant_id) |
| Mahasiswa | 1 universitas (tenant_id + prodi_id) |

---

## Implementasi Spatie Permission

```php
// Roles
Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
Spatie\Permission\Models\Role::create(['name' => 'admin-univ']);
Spatie\Permission\Models\Role::create(['name' => 'admin-fakultas']);
Spatie\Permission\Models\Role::create(['name' => 'admin-prodi']);
Spatie\Permission\Models\Role::create(['name' => 'kaprodi']);
Spatie\Permission\Models\Role::create(['name' => 'reviewer']);
Spatie\Permission\Models\Role::create(['name' => 'dosen']);
Spatie\Permission\Models\Role::create(['name' => 'lpm']);
Spatie\Permission\Models\Role::create(['name' => 'mahasiswa']);

// Permission contoh
Spatie\Permission\Models\Permission::create(['name' => 'rps.create']);
Spatie\Permission\Models\Permission::create(['name' => 'rps.view-own']);
Spatie\Permission\Models\Permission::create(['name' => 'rps.submit-review']);
Spatie\Permission\Models\Permission::create(['name' => 'ai.generate']);
// ... dan seterusnya
```

---

**Navigasi:** [Sebelumnya: Module Breakdown](18-module-breakdown.md) | [Daftar Isi](../README.md) | [Berikutnya: Business Rules](20-business-rules.md)
