# 07 — Target Users

## Segmentasi Pengguna

### Pengguna Internal Platform

| No | Role | Deskripsi | Estimasi per Kampus |
|----|------|-----------|---------------------|
| 1 | **Super Admin** | Mengelola seluruh tenant, konfigurasi platform, dan billing | 1-2 (internal) |
| 2 | **Admin Universitas** | Mengelola data universitas, fakultas, prodi, user invitation | 2-5 |
| 3 | **Admin Fakultas** | Mengelola data fakultas, program studi, kurikulum | 1-2 per fakultas |
| 4 | **Admin Program Studi** | Mengelola data prodi, kurikulum, mata kuliah | 1 per prodi |
| 5 | **Kaprodi** | Mengelola RPS di program studi, melakukan review dan approval | 1 per prodi |
| 6 | **Reviewer** | Mereview RPS, memberikan skor, komentar, dan rekomendasi | 2-5 per prodi |
| 7 | **Dosen** | Menyusun, mengedit, dan mengajukan RPS untuk review | 5-50 per prodi |
| 8 | **Mahasiswa** | Melihat RPS yang telah dipublikasikan (future) | Ratusan per prodi |
| 9 | **LPM** | Memonitor kualitas RPS, melakukan audit mutu | 3-10 per kampus |

### Pengguna Eksternal Platform

| No | Role | Deskripsi |
|----|------|-----------|
| 10 | **Auditor BAN-PT** | Memeriksa dokumen RPS untuk akreditasi institusi |
| 11 | **Auditor LAM** | Memeriksa dokumen RPS untuk akreditasi program studi |
| 12 | **Stakeholder Industri** | Melihat profil lulusan dan CPL (future) |

## Kebutuhan per Role

### Super Admin

- Mengelola tenant (universitas) — create, suspend, delete
- Mengelola paket langganan dan billing
- Konfigurasi global platform
- Monitoring sistem

### Admin Universitas

- Mengelola data universitas
- Menambah/mengelola fakultas
- Mengundang admin fakultas
- Mengelola template RPS universitas
- Dashboard universitas

### Admin Fakultas

- Mengelola data fakultas
- Menambah/mengelola program studi
- Mengundang admin prodi dan kaprodi
- Dashboard fakultas

### Admin Program Studi

- Mengelola data program studi
- Mengelola kurikulum
- Mengelola daftar mata kuliah
- Mengundang dosen

### Kaprodi

- Dashboard prodi (status RPS, progress)
- Menugaskan reviewer
- Melakukan approval akhir
- Monitoring kualitas RPS prodi

### Reviewer

- Menerima tugas review
- Melihat daftar RPS yang perlu direview
- Memberikan skor dan komentar
- AI-assisted review

### Dosen

- Membuat RPS baru
- Mengedit RPS existing
- Menyimpan draft
- Mengajukan review
- Melihat hasil review
- Melakukan revisi
- Melihat RPS yang sudah published

### Mahasiswa (Future)

- Melihat RPS mata kuliah yang diambil
- Mengunduh RPS
- Memberikan feedback

### LPM

- Dashboard mutu — seluruh RPS
- Audit trail
- Statistik constructive alignment
- Export laporan

## Matriks Kebutuhan vs Role

| Kebutuhan | SA | AU | AF | AP | KP | RV | DS | MH | LPM |
|-----------|----|----|----|----|----|----|----|----|-----|
| Manajemen Tenant | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Manajemen Fakultas | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Manajemen Prodi | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Manajemen Kurikulum | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Manajemen Mata Kuliah | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Buat/Edit RPS | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Review RPS | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Approval RPS | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Lihat RPS | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ |
| Export | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| AI Generate | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| AI Validate | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ✅ |
| AI Review | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |

**Keterangan:** SA=Super Admin, AU=Admin Univ, AF=Admin Fakultas, AP=Admin Prodi, KP=Kaprodi, RV=Reviewer, DS=Dosen, MH=Mahasiswa, LPM=LPM

---

**Navigasi:** [Sebelumnya: Solution Overview](06-solution-overview.md) | [Daftar Isi](../README.md) | [Berikutnya: User Persona](08-user-persona.md)
