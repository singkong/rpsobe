# 10 — Scope

## Lingkup Produk (In Scope)

### A. Modul Autentikasi & Otorisasi

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Login | Email + password | P0 — MVP |
| Register | Self-registration dengan invitation code | P0 — MVP |
| Forgot Password | Reset password via email | P0 — MVP |
| Role Management | RBAC dengan Spatie Permission | P0 — MVP |
| Permission Management | Permission granular per modul | P0 — MVP |
| SSO Ready | Arsitektur siap integrasi SAML/OAuth | P2 — Post-MVP |
| MFA Ready | Arsitektur siap TOTP/WebAuthn | P2 — Post-MVP |
| Session Management | Multi-device, force logout | P1 — MVP |

### B. Modul Manajemen Pengguna

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| User CRUD | Create, read, update, deactivate | P0 — MVP |
| Invitation | Undang pengguna via email | P0 — MVP |
| User Profile | Edit profil, ganti password | P0 — MVP |
| Avatar Upload | Foto profil | P2 — Post-MVP |
| Bulk Import | Import user via CSV | P1 — MVP |
| User Activity Log | Log aktivitas per user | P1 — MVP |

### C. Modul Master Data

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Universitas | Kelola data universitas | P0 — MVP |
| Fakultas | Kelola fakultas per universitas | P0 — MVP |
| Program Studi | Kelola prodi per fakultas | P0 — MVP |
| Kurikulum | Kelola kurikulum per prodi | P0 — MVP |
| Semester | Kelola periode semester | P0 — MVP |
| Mata Kuliah | Kelola mata kuliah per kurikulum | P0 — MVP |
| Dosen | Kelola data dosen | P0 — MVP |
| Profil Lulusan | Kelola profil lulusan per prodi | P0 — MVP |
| CPL | Kelola Capaian Pembelajaran Lulusan | P0 — MVP |
| Referensi | Kelola daftar referensi/pustaka | P1 — MVP |

### D. Modul RPS Builder

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Wizard Step 1 | Informasi Mata Kuliah | P0 — MVP |
| Wizard Step 2 | Pilih CPL | P0 — MVP |
| Wizard Step 3 | CPMK | P0 — MVP |
| Wizard Step 4 | Sub-CPMK | P0 — MVP |
| Wizard Step 5 | Materi Pembelajaran | P0 — MVP |
| Wizard Step 6 | Metode Pembelajaran | P0 — MVP |
| Wizard Step 7 | Assessment | P0 — MVP |
| Wizard Step 8 | Review & Finalisasi | P0 — MVP |
| Draft Auto-save | Simpan otomatis setiap 30 detik | P0 — MVP |
| Progress Indicator | Progress bar per step | P0 — MVP |
| Inline Validation | Validasi setiap step | P0 — MVP |
| Duplicate RPS | Duplikasi RPS existing | P1 — MVP |

### E. Modul Mapping

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| CPL → CPMK | Mapping CPL ke CPMK | P0 — MVP |
| CPMK → Sub-CPMK | Mapping CPMK ke Sub-CPMK | P0 — MVP |
| Sub-CPMK → Materi | Mapping Sub-CPMK ke materi | P0 — MVP |
| Materi → Assessment | Mapping materi ke assessment | P0 — MVP |
| Assessment → LO | Mapping assessment ke learning outcome | P1 — MVP |
| Visualisasi Mapping | Diagram mapping interaktif | P1 — MVP |

### F. Modul AI

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| AI Generate CPMK | Generate CPMK dari CPL | P1 — Phase 2 |
| AI Generate Sub-CPMK | Generate Sub-CPMK dari CPMK | P1 — Phase 2 |
| AI Generate Materi | Generate materi dari Sub-CPMK | P1 — Phase 2 |
| AI Generate Referensi | Generate daftar referensi | P1 — Phase 2 |
| AI Generate Assessment | Generate assessment | P1 — Phase 2 |
| AI Generate Rubrik | Generate rubrik penilaian | P1 — Phase 2 |
| AI Generate Learning Outcome | Generate learning outcome | P1 — Phase 2 |
| AI Generate Learning Activities | Generate aktivitas pembelajaran | P1 — Phase 2 |
| AI Validator — Taksonomi Bloom | Validasi level taksonomi | P1 — Phase 2 |
| AI Validator — Alignment | Validasi constructive alignment | P1 — Phase 2 |
| AI Validator — CPMK Count | Validasi jumlah CPMK | P1 — Phase 2 |
| AI Validator — Meetings | Validasi jumlah pertemuan | P1 — Phase 2 |
| AI Validator — Assessment | Validasi distribusi assessment | P1 — Phase 2 |
| AI Validator — Bobot | Validasi bobot nilai | P1 — Phase 2 |
| AI Validator — Referensi | Validasi kecukupan referensi | P1 — Phase 2 |
| AI Validator — Konsistensi | Validasi konsistensi antar komponen | P1 — Phase 2 |
| AI Reviewer — Skor | Skor otomatis berdasarkan kriteria | P2 — Phase 3 |
| AI Reviewer — Komentar | Komentar otomatis per komponen | P2 — Phase 3 |
| AI Reviewer — Saran | Saran perbaikan spesifik | P2 — Phase 3 |

### G. Modul Workflow

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Draft | Status awal RPS | P0 — MVP |
| Review | RPS diajukan untuk review | P0 — MVP |
| Revision | RPS perlu revisi | P0 — MVP |
| Approved | RPS disetujui | P0 — MVP |
| Published | RPS dipublikasikan | P0 — MVP |
| Archived | RPS diarsipkan | P0 — MVP |
| Workflow History | Riwayat perubahan status | P0 — MVP |
| Rejection Reason | Alasan penolakan/revisi | P0 — MVP |

### H. Modul Dashboard

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Dashboard Dosen | RPS saya, status, to-do | P0 — MVP |
| Dashboard Kaprodi | Status RPS prodi, statistik | P0 — MVP |
| Dashboard Fakultas | Status RPS fakultas per prodi | P1 — MVP |
| Dashboard Universitas | Status RPS seluruh universitas | P1 — MVP |
| Dashboard LPM | Statistik mutu, audit | P2 — Phase 3 |
| Dashboard Admin | Tenant management, billing | P1 — MVP |

### I. Modul Reporting

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Statistik RPS | Jumlah, status, progres | P1 — MVP |
| Grafik & Chart | Visualisasi data | P1 — MVP |
| Export Excel | Download laporan .xlsx | P1 — MVP |
| Export PDF | Download laporan .pdf | P1 — MVP |
| Filter & Search | Filter laporan | P1 — MVP |

### J. Modul Notifikasi

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Email Notification | Kirim email untuk event penting | P0 — MVP |
| In-App Notification | Notifikasi dalam aplikasi | P0 — MVP |
| Notification Center | Pusat notifikasi | P0 — MVP |
| WhatsApp Notification | Notifikasi via WhatsApp (future) | P3 — Future |

### K. Modul Versioning

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Version History | Daftar versi RPS | P0 — MVP |
| Version Diff | Perbandingan antar versi | P1 — MVP |
| Version Rollback | Kembali ke versi sebelumnya | P1 — MVP |
| Version Label | Label (v1.0, v2.0, dll) | P0 — MVP |

### L. Modul Audit Log

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Activity Log | Semua aktivitas tersimpan | P0 — MVP |
| Audit Viewer | Tampilan log yang dapat difilter | P0 — MVP |
| Export Audit | Export log ke CSV/Excel | P1 — MVP |
| Retention Policy | Kebijakan retensi log | P2 — Phase 3 |

### M. Modul Template

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Template per Universitas | Template RPS kustom per kampus | P1 — MVP |
| Template Builder | Editor template RPS | P1 — MVP |
| Default Templates | Template bawaan sesuai SN-DIKTI | P0 — MVP |

### N. Modul Export

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| Export Word (.docx) | Download RPS sebagai Word | P0 — MVP |
| Export PDF | Download RPS sebagai PDF | P0 — MVP |
| Export dengan Template | Download sesuai template kampus | P1 — MVP |
| Batch Export | Download banyak RPS sekaligus | P1 — MVP |

---

## Ringkasan Prioritas

| Prioritas | Definisi | Jumlah Fitur |
|-----------|----------|--------------|
| P0 — MVP | Harus ada di rilis pertama | ~45 fitur |
| P1 — MVP/Near-term | Penting, bisa di rilis awal | ~25 fitur |
| P2 — Phase 2/3 | Penting, rilis berikutnya | ~15 fitur |
| P3 — Future | Roadmap jangka panjang | ~5 fitur |

---

**Navigasi:** [Sebelumnya: Stakeholder](09-stakeholder.md) | [Daftar Isi](../README.md) | [Berikutnya: Out of Scope](11-out-of-scope.md)
