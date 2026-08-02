# 41 — MVP Definition

## Ikhtisar

MVP (Minimum Viable Product) RPS OBE adalah platform minimum yang dapat digunakan oleh satu universitas untuk menyusun, mereview, dan mengekspor Rencana Pembelajaran Semester (RPS) sesuai standar SN-DIKTI. Dokumen ini mendefinisikan tujuan MVP, scope exact yang termasuk dan tidak termasuk, kriteria keberhasilan untuk melanjutkan ke Fase 2, timeline 4 bulan (6 sprint), komposisi tim, serta deliverables setiap sprint.

---

## MVP Goal

Platform minimum yang dapat digunakan oleh **1 (satu) universitas** untuk:

1. Menyusun RPS melalui wizard 8 langkah yang terstruktur
2. Melakukan pemetaan CPL → CPMK → Sub-CPMK → Assessment secara sistematis
3. Mereview dan menyetujui RPS melalui workflow (Draft → Review → Revision → Approved → Published)
4. Mengekspor RPS dalam format Word (.docx) dan PDF (.pdf) sesuai template SN-DIKTI
5. Mengelola pengguna, role, dan data master secara mandiri

`mermaid
graph TB
    subgraph "MVP Scope — RPS OBE"
        AUTH[Authentication<br/>Login, Register, Forgot Password]
        USERS[User Management<br/>CRUD, Roles, Invitation]
        MASTER[Master Data<br/>Univ, Fakultas, Prodi, Kurikulum, Semester, MK, Dosen, CPL]
        BUILDER[RPS Builder<br/>8-Step Wizard, Auto-Save, Validation]
        MAPPING[Mapping<br/>CPL → CPMK → Sub-CPMK → Assessment]
        WORKFLOW[Workflow<br/>Draft, Review, Revision, Approved, Published, Archived]
        EXPORT[Export<br/>Word (.docx) + PDF (.pdf)]
        DASHBOARD[Dashboard<br/>Dosen + Kaprodi]
        NOTIF[Notification<br/>Email + In-App]
        VERSION[Versioning<br/>Auto-Version on Submit]
        AUDIT[Audit Log<br/>Semua Aktivitas CRUD]
        TEMPLATE[Template<br/>Default SN-DIKTI Template]
    end

    AUTH --> USERS
    USERS --> MASTER
    MASTER --> BUILDER
    BUILDER --> MAPPING
    MAPPING --> WORKFLOW
    WORKFLOW --> EXPORT
    DASHBOARD --> BUILDER
    NOTIF --> WORKFLOW
    VERSION --> WORKFLOW
    AUDIT --> BUILDER
    TEMPLATE --> EXPORT

    style BUILDER fill:#206BC4,color:#fff
    style WORKFLOW fill:#2FB344,color:#fff
    style EXPORT fill:#D63939,color:#fff
`

---

## MVP Scope — Exactly What's Included

### 1. Authentication

| Komponen | Spesifikasi | Detail |
|----------|-------------|--------|
| **Login** | Email + Password | Rate limiting: 5 percobaan / 15 menit; session timeout 8 jam; remember me (opsional) |
| **Register via Invitation** | Hanya melalui invitation link | Link invitation dikirim via email; expired dalam 7 hari; satu kali pakai |
| **Forgot Password** | Reset via email | Token reset expired dalam 60 menit; email dengan link reset |
| **Logout** | Single-click logout | Hapus session; redirect ke halaman login |

### 2. User Management

| Komponen | Spesifikasi | Detail |
|----------|-------------|--------|
| **CRUD Users** | Create, Read, Update, Delete (soft delete) | Admin Tenant dan Superadmin dapat mengelola user |
| **Role Assignment** | Superadmin, Admin Tenant, Kaprodi, Dosen | 4 role default; assignment saat invitation |
| **Invitation System** | Generate invitation link via email | Admin Tenant / Superadmin mengirim invitation; status invitation terlacak (pending/accepted/expired) |
| **Profile Management** | Edit nama, email, foto profil (opsional) | Pengguna dapat mengedit profil sendiri |
| **Password Change** | Change password (harus tahu password lama) | Validasi: minimal 8 karakter, kombinasi huruf + angka |
| **Account Deactivation** | Nonaktifkan akun pengguna | Tidak menghapus data RPS yang sudah dibuat; hanya menonaktifkan akses |

### 3. Master Data

| Entitas | Atribut Utama | Sumber Data |
|---------|--------------|-------------|
| **Universitas** | nama, kode, alamat, website, logo | Diinput saat tenant onboarding |
| **Fakultas** | nama, kode, universitas_id | Diinput oleh Admin Tenant |
| **Program Studi (Prodi)** | nama, kode, jenjang (S1/S2/S3/D3/D4), fakultas_id, akreditasi | Diinput oleh Admin Tenant; dapat di-import via CSV |
| **Kurikulum** | nama, tahun_mulai, prodi_id, status (aktif/nonaktif) | Diinput oleh Admin Tenant / Kaprodi |
| **Semester** | nama (Ganjil/Genap/Periode), tahun_akademik (2026/2027), kurikulum_id | Diinput oleh Admin Tenant / Kaprodi |
| **Mata Kuliah (MK)** | kode_mk, nama_mk, sks (teori), sks_praktikum, semester_ke, jenis (wajib/pilihan), prodi_id, kurikulum_id | Diinput oleh Kaprodi / Admin; dapat di-import via CSV |
| **Dosen** | nidn, nama, email, gelar_depan, gelar_belakang, prodi_id | Diinput oleh Admin Tenant; dapat di-import via CSV |
| **CPL (Capaian Pembelajaran Lulusan)** | kode, deskripsi, kategori (Sikap/Pengetahuan/Keterampilan Umum/Keterampilan Khusus), prodi_id | Diinput oleh Kaprodi / Admin; biasanya di-copy dari dokumen kurikulum |
| **Profil Lulusan** | nama, deskripsi, prodi_id | Diinput oleh Kaprodi; opsional di MVP |

### 4. RPS Builder — 8-Step Wizard

`mermaid
graph LR
    S1["Step 1<br/>Informasi Mata Kuliah<br/><br/>Nama MK, Kode, SKS,<br/>Semester, Dosen Pengampu"] --> S2["Step 2<br/>Pemetaan CPL<br/><br/>Pilih CPL yang didukung<br/>oleh mata kuliah ini"]
    S2 --> S3["Step 3<br/>CPMK<br/><br/>Rumuskan CPMK<br/>berdasarkan CPL terpilih"]
    S3 --> S4["Step 4<br/>Sub-CPMK<br/><br/>Jabarkan Sub-CPMK<br/>per pertemuan"]
    S4 --> S5["Step 5<br/>Assessment & Bobot<br/><br/>Tentukan jenis assessment,<br/>bobot, dan rubrik"]
    S5 --> S6["Step 6<br/>Materi Pembelajaran<br/><br/>Materi per pertemuan<br/>dan metode pembelajaran"]
    S6 --> S7["Step 7<br/>Referensi<br/><br/>Daftar pustaka<br/>dan referensi"]
    S7 --> S8["Step 8<br/>Review & Submit<br/><br/>Tinjau ulang seluruh<br/>RPS sebelum submit"]

    style S1 fill:#206BC4,color:#fff
    style S2 fill:#2D7DD2,color:#fff
    style S3 fill:#4697E1,color:#fff
    style S4 fill:#70B0EA,color:#000
    style S5 fill:#99C9F2,color:#000
    style S6 fill:#B3D4F5,color:#000
    style S7 fill:#CCDFF7,color:#000
    style S8 fill:#E6EEFA,color:#000
`

#### Fitur Wizard

| Fitur | Deskripsi | Implementasi |
|-------|-----------|-------------|
| **Auto-Save** | Setiap perubahan disimpan otomatis | Livewire wire:model.debounce.1000ms + auto-save ke database |
| **Step Navigation** | Pindah antar step tanpa kehilangan data | Sidebar step indicator + tombol Sebelumnya/Selanjutnya |
| **Inline Validation** | Validasi setiap step sebelum melanjutkan | Validasi server-side via Livewire; pesan error inline |
| **Progress Indicator** | Menunjukkan persentase penyelesaian | Progress bar di atas wizard; checklist per step |
| **Draft Status** | RPS yang belum selesai disimpan sebagai Draft | Status draft di database; dapat diedit kapan saja |
| **Preview Mode** | Melihat RPS dalam format final sebelum submit | Step 8 menampilkan preview lengkap semua step |

#### Step Detail

| Step | Field Wajib | Field Opsional | Validasi |
|------|------------|----------------|----------|
| **Step 1: Info MK** | mata_kuliah_id (select), semester_id (select), dosen_pengampu (multi-select), tim_dosen (multi-select) | deskripsi_mk (textarea), prasyarat (text) | MK dan Semester wajib dipilih; minimal 1 dosen pengampu |
| **Step 2: Pemetaan CPL** | cpl_ids (multi-select checkbox/tag) | kemampuan_akhir (textarea) | Minimal 1 CPL dipilih; CPL sesuai prodi MK |
| **Step 3: CPMK** | kode_cpmk (auto: CPMK-01), deskripsi_cpmk (textarea), cpl_terkait (multi-select) | — | Minimal 3 CPMK; setiap CPMK terkait minimal 1 CPL; maksimal 8 CPMK |
| **Step 4: Sub-CPMK** | kode_subcpmk (auto), deskripsi_subcpmk (textarea), cpmk_induk (select), pertemuan_ke (number), level_taksonomi (select), indikator (textarea), pengalaman_belajar (textarea) | — | Sub-CPMK mencakup semua pertemuan (14-16); setiap Sub-CPMK terkait 1 CPMK |
| **Step 5: Assessment** | nama_assessment (text), jenis (select: UTS/UAS/Tugas/Kuis/Praktikum/Proyek), bobot_persen (number), subcpmk_terkait (multi-select), rubrik (textarea), kriteria_penilaian (textarea) | waktu (menit) | Total bobot = 100%; setiap Sub-CPMK ter-assess minimal 1x |
| **Step 6: Materi** | pertemuan_ke (auto), materi (textarea), metode_pembelajaran (multi-select: Ceramah/Diskusi/Praktikum/Studi Kasus/PBL/dll), media_pembelajaran (text), estimasi_waktu (menit) | sumber_materi (text) | Semua pertemuan memiliki materi |
| **Step 7: Referensi** | judul (text), penulis (text), tahun (number), penerbit (text) | jenis (Buku/Jurnal/Website), url, edisi | Minimal 3 referensi; format APA |
| **Step 8: Review** | konfirmasi_submit (checkbox) | catatan_submit (textarea) | Semua step valid; konfirmasi eksplisit sebelum submit |

### 5. Mapping: CPL → CPMK → Sub-CPMK → Assessment

| Mapping | Deskripsi | Validasi |
|---------|-----------|----------|
| **CPL → CPMK** | Setiap CPMK harus mendukung minimal 1 CPL | Setiap CPL yang dipilih harus memiliki minimal 1 CPMK |
| **CPMK → Sub-CPMK** | Setiap Sub-CPMK merupakan penjabaran dari 1 CPMK | Setiap CPMK harus memiliki minimal 1 Sub-CPMK |
| **Sub-CPMK → Assessment** | Setiap assessment mengukur minimal 1 Sub-CPMK | Setiap Sub-CPMK harus ter-assess minimal 1 assessment |
| **Sub-CPMK → Pertemuan** | Setiap Sub-CPMK dialokasikan ke pertemuan tertentu | Semua 14-16 pertemuan tercakup |

### 6. Workflow

`mermaid
stateDiagram-v2
    [*] --> Draft : Dosen membuat RPS baru
    Draft --> Draft : Dosen mengedit RPS
    Draft --> Review : Dosen submit untuk review
    Review --> Revision : Kaprodi meminta revisi
    Review --> Approved : Kaprodi menyetujui
    Revision --> Draft : Dosen menerima revisi
    Approved --> Published : Admin publikasi
    Published --> Archived : RPS periode lama
    Draft --> [*] : Hapus (soft delete)
    Archived --> [*] : Hapus permanen

    note right of Draft : Hanya Dosen yang dapat mengedit
    note right of Review : Hanya Kaprodi yang dapat mereview
    note right of Approved : RPS terkunci, tidak dapat diedit
`

#### Status Transitions

| Dari | Ke | Oleh | Syarat | Trigger |
|------|----|-----|--------|---------|
| draft | eview | Dosen (pemilik RPS) | Semua step wizard valid; tidak ada error validasi | Klik \Submit untuk Review\ |
| eview | pproved | Kaprodi | Review selesai; tidak ada catatan revisi | Klik \Setujui\ |
| eview | evision | Kaprodi | Ada catatan revisi yang harus diisi | Klik \Minta Revisi\ + isi catatan |
| evision | draft | Dosen | Dosen menerima dan mulai mengedit | Klik \Mulai Revisi\ |
| pproved | published | Admin Tenant | RPS disetujui final | Klik \Publikasikan\ |
| published | rchived | Admin Tenant | Periode RPS sudah lewat | Klik \Arsipkan\ |

### 7. Export

| Format | Library | Output | Fitur |
|--------|---------|--------|-------|
| **Word (.docx)** | PHPWord | Dokumen RPS lengkap sesuai template SN-DIKTI | Heading, tabel, daftar, kop surat universitas |
| **PDF (.pdf)** | DomPDF / TCPDF | Dokumen RPS lengkap (sama dengan Word, konversi ke PDF) | Format tidak berubah dari Word; dapat ditandatangani digital |

#### Template Export

- Template default mengikuti format SN-DIKTI (Kemenristekdikti)
- Kop surat: Logo universitas, nama universitas, fakultas, program studi
- Informasi MK: Kode MK, Nama MK, SKS, Semester, Dosen Pengampu, Tim Dosen
- CPL yang didukung
- Tabel CPMK
- Tabel Sub-CPMK
- Rencana Assessment
- Rencana Pembelajaran per Pertemuan (Minggu ke-, Sub-CPMK, Indikator, Materi, Metode, Media, Waktu, Pengalaman Belajar, Bobot)
- Daftar Referensi

### 8. Dashboard

#### Dosen Dashboard

| Widget | Deskripsi | Tipe |
|--------|-----------|------|
| RPS Saya (counter) | Jumlah RPS yang dibuat: Draft, Review, Approved, Published | Stat counter cards |
| RPS Terbaru | Daftar 5 RPS terakhir dengan status dan tanggal update | Tabel ringkas |
| Deadline Review | RPS yang menunggu review oleh Kaprodi (dari dosen lain) | — |
| Quick Action | Tombol \Buat RPS Baru\ | Button |
| Notifikasi Terbaru | 5 notifikasi terbaru | List |

#### Kaprodi Dashboard

| Widget | Deskripsi | Tipe |
|--------|-----------|------|
| Statistik Prodi | Total RPS, Draft, Review, Approved, Published di prodi | Stat counter cards |
| RPS Menunggu Review | Daftar RPS dengan status eview | Tabel dengan action |
| RPS per Dosen | Chart batang: jumlah RPS per dosen di prodi | Bar chart sederhana |
| Aktivitas Terbaru | Feed aktivitas terbaru di prodi | Timeline list |
| Quick Action | Tombol \Buat RPS Baru\ | Button |

### 9. Notification

| Tipe | In-App | Email | Trigger |
|------|--------|-------|---------|
| Invitation | ✅ | ✅ | Admin mengirim invitation ke user baru |
| Review Request | ✅ | ✅ | Dosen submit RPS untuk review |
| Revision Request | ✅ | ✅ | Kaprodi meminta revisi RPS |
| RPS Approved | ✅ | ✅ | Kaprodi menyetujui RPS |
| RPS Published | ✅ | ✅ | Admin mempublikasi RPS |
| Deadline Reminder | ❌ | ✅ | Sistem mengingatkan RPS yang belum disubmit (jadwal cron) |

### 10. Versioning

| Fitur | Deskripsi |
|-------|-----------|
| **Auto-Version** | Setiap kali RPS di-submit (draft → review), versi baru dibuat otomatis |
| **Format Versi** | Semantic versioning: v1.0.0 (MAJOR.MINOR.PATCH) |
| **Version History** | Daftar semua versi RPS dengan tanggal, author, dan catatan perubahan |
| **Version Diff** | Tampilan perbandingan antar versi (highlight perubahan) — basic implementation |
| **RPS Saat Ini** | Versi terbaru selalu menjadi versi aktif |

### 11. Audit Log

| Aktivitas | Data Dicatat |
|-----------|-------------|
| Login / Logout | user_id, email, timestamp, IP address |
| CRUD RPS | rps_id, action (create/update/delete/submit), old_values, new_values, user_id, timestamp |
| CRUD CPMK/Sub-CPMK | id, action, old_values, new_values, user_id, timestamp |
| Workflow Change | rps_id, from_status, to_status, actor_user_id, catatan, timestamp |
| Export | rps_id, format, user_id, timestamp |
| User Management | affected_user_id, action, role_change, performed_by, timestamp |

### 12. Template

- Satu template default: **SN-DIKTI** (format sesuai Peraturan Kemenristekdikti)
- Template menentukan struktur dan format dokumen export (Word/PDF)
- Template berisi: header, footer, font, ukuran font, tata letak tabel, placeholder untuk data RPS

---

## MVP Out of Scope

Fitur berikut secara eksplisit TIDAK termasuk dalam MVP. Fitur-fitur ini masuk dalam backlog Fase 2, 3, 4, atau 5.

| Kategori | Fitur | Fase Target |
|----------|-------|-------------|
| **AI** | AI Assistant (Generate CPMK, Sub-CPMK, Assessment) | Fase 2 |
| **AI** | AI Validator (8 aspek validasi otomatis) | Fase 2 |
| **AI** | AI Reviewer (skor, komentar, saran perbaikan) | Fase 2 |
| **Dashboard** | Dashboard Multi-Fakultas (kompleks) | Fase 3 |
| **Dashboard** | Dashboard LPM (Lembaga Penjaminan Mutu) | Fase 3 |
| **Dashboard** | Dashboard Rektorat | Fase 3 |
| **Export** | Batch Export (pilih > 1 RPS → export ZIP) | Fase 3 |
| **Template** | Template Builder (drag-and-drop custom template) | Fase 3 |
| **Template** | Multiple Template Management | Fase 3 |
| **Auth** | SSO (Single Sign-On) SAML/OAuth/OIDC | Fase 3 |
| **Auth** | MFA (Multi-Factor Authentication) | Fase 4 |
| **Integration** | LMS Integration (Moodle, Canvas) | Fase 4 |
| **Integration** | Public REST API | Fase 4 |
| **Integration** | Webhook System | Fase 4 |
| **Integration** | AD/LDAP Integration | Fase 5 |
| **Mobile** | PWA (Progressive Web App) | Fase 4 |
| **Mobile** | Native Mobile Apps (iOS & Android) | Fase 5 |
| **Multi-Tenant** | Multi-Campus / Multi-Fakultas Management | Fase 3 |
| **Internationalization** | Multi-language (EN, AR) | Fase 5 |
| **Marketplace** | Template Marketplace | Fase 5 |
| **Advanced** | Collaborative Editing (real-time) | Fase 5 |
| **Advanced** | Plagiarism Checker | Fase 5 |
| **Advanced** | Analytics Benchmarking | Fase 5 |
| **Advanced** | Custom Workflow Builder | Fase 3 |

---

## MVP Success Criteria

Kriteria keberhasilan harus **semua terpenuhi** sebelum melanjutkan ke Fase 2 (AI).

### Critical Success Criteria (Must Pass — Go/No-Go untuk Fase 2)

| ID | Kriteria | Metode Pengukuran | Target | Bobot |
|----|----------|-------------------|--------|-------|
| SC-01 | 1 tenant aktif menggunakan platform | System metrics | 1 universitas | GO/NO-GO |
| SC-02 | RPS dibuat melalui platform | System metrics | ≥ 10 RPS | GO/NO-GO |
| SC-03 | Rata-rata waktu penyusunan RPS | System metrics (timer per RPS) | < 4 jam | GO/NO-GO |
| SC-04 | Siklus review berfungsi | System metrics | < 7 hari dari submit ke approved | GO/NO-GO |
| SC-05 | Export berfungsi benar | Manual testing + automated | 100% sukses; output sesuai template SN-DIKTI | GO/NO-GO |
| SC-06 | Zero critical bugs in production | Bug tracker | 0 bug severity Critical / P1 | GO/NO-GO |

### Success Metrics (Nice to Have — Mengukur Kualitas)

| ID | Kriteria | Metode Pengukuran | Target |
|----|----------|-------------------|--------|
| SM-01 | User satisfaction (CSAT) | Survei in-app setelah RPS pertama selesai | ≥ 4.0 / 5.0 |
| SM-02 | Wizard completion rate | Analytics funnel | ≥ 50% user yang memulai wizard menyelesaikan hingga submit |
| SM-03 | Page load time (p95) | Lighthouse / Web Vitals | < 2 detik |
| SM-04 | Time to Interactive (dashboard) | Lighthouse | < 3 detik |
| SM-05 | Mobile responsiveness | Manual testing (320px – 1920px) | Semua halaman dapat digunakan di mobile |
| SM-06 | Browser compatibility | Automated testing | Chrome, Firefox, Safari, Edge (2 versi terbaru) |
| SM-07 | Test coverage (backend) | PHPUnit coverage report | ≥ 70% code coverage |
| SM-08 | Test coverage (critical flows) | E2E testing (Cypress/Dusk) | Semua critical paths tercover |
| SM-09 | Email deliverability | Email testing | ≥ 95% email terkirim (tidak bounce/spam) |
| SM-10 | Accessibility audit | axe-core / Lighthouse | Score ≥ 85 |

---

## MVP Timeline

### Overview: 4 Bulan, 6 Sprint (2 Minggu per Sprint)

`mermaid
gantt
    title MVP Timeline — RPS OBE (4 Bulan, 6 Sprint)
    dateFormat YYYY-MM-DD
    axisFormat %d %b

    section Sprint 1 (Foundation)
    Setup project & environment            :s1a, 2026-08-03, 5d
    Authentication & User Management       :s1b, 2026-08-06, 9d
    Database schema & migration            :s1c, 2026-08-03, 14d
    Sprint Review 1                        :milestone, m1, 2026-08-16, 1d

    section Sprint 2 (Master Data)
    Master Data CRUD (Universitas–CPL)     :s2a, 2026-08-17, 10d
    CSV Import data awal                   :s2b, 2026-08-24, 7d
    UI/UX core layout (Tabler)             :s2c, 2026-08-17, 14d
    Sprint Review 2                        :milestone, m2, 2026-08-30, 1d

    section Sprint 3 (RPS Builder)
    Wizard Step 1–4 (Info MK – Sub-CPMK)   :s3a, 2026-08-31, 10d
    Auto-save mechanism                    :s3b, 2026-09-07, 7d
    Inline validation rules                :s3c, 2026-09-03, 11d
    Sprint Review 3                        :milestone, m3, 2026-09-13, 1d

    section Sprint 4 (RPS Builder + Workflow)
    Wizard Step 5–8 (Assessment – Review)  :s4a, 2026-09-14, 10d
    Workflow engine (status transitions)   :s4b, 2026-09-21, 7d
    Mapping logic CPL→CPMK→SubCPMK→Assess  :s4c, 2026-09-14, 14d
    Sprint Review 4                        :milestone, m4, 2026-09-27, 1d

    section Sprint 5 (Export + Dashboard)
    Export (Word + PDF)                    :s5a, 2026-09-28, 10d
    SN-DIKTI template implementation       :s5b, 2026-09-28, 14d
    Dosen Dashboard                        :s5c, 2026-10-05, 9d
    Kaprodi Dashboard                      :s5d, 2026-10-08, 6d
    Sprint Review 5                        :milestone, m5, 2026-10-11, 1d

    section Sprint 6 (Polish + Stabilize)
    Notification (Email + In-App)          :s6a, 2026-10-12, 5d
    Versioning & Audit Log                 :s6b, 2026-10-12, 7d
    Bug fixing & stabilization             :s6c, 2026-10-16, 12d
    Testing & QA (full regression)         :s6d, 2026-10-19, 9d
    Deployment preparation                 :s6e, 2026-10-26, 4d
    Sprint Review 6 / MVP Release          :milestone, m6, 2026-10-30, 1d
`

### Sprint Detail

#### Sprint 1 — Foundation (Minggu 1–2)

| Tugas | Estimasi | Assignee |
|-------|----------|----------|
| Setup Laravel project + environment (dev, staging) | 3 hari | Backend 1 |
| Database schema design + migration (semua tabel) | 5 hari | Backend 1, Backend 2 |
| Authentication system (login, register via invitation, forgot password, logout) | 5 hari | Backend 2 |
| User CRUD + Role system (spatie/laravel-permission) | 5 hari | Backend 1 |
| Invitation system (generate link, send email, accept, track) | 4 hari | Backend 2 |
| Base layout (Tabler UI setup, navigation, breadcrumb) | 5 hari | Frontend |
| CI/CD pipeline setup (GitHub Actions — lint, test, deploy staging) | 3 hari | Backend 1 |

**Deliverable:** Sistem login berfungsi; user dapat diundang, register, dan memiliki role.

#### Sprint 2 — Master Data (Minggu 3–4)

| Tugas | Estimasi | Assignee |
|-------|----------|----------|
| CRUD Universitas, Fakultas, Prodi | 5 hari | Backend 1 |
| CRUD Kurikulum, Semester | 3 hari | Backend 1 |
| CRUD Mata Kuliah | 4 hari | Backend 2 |
| CRUD Dosen | 3 hari | Backend 2 |
| CRUD CPL dan Profil Lulusan | 4 hari | Backend 2 |
| CSV Import (MK, Dosen, CPL) | 4 hari | Backend 1 |
| UI untuk semua master data (list, create, edit, delete) | 10 hari | Frontend |
| Form validation + error handling | — (on-going) | Frontend |

**Deliverable:** Semua data master dapat dikelola via UI; data awal dapat di-import via CSV.

#### Sprint 3 — RPS Builder (Part 1) (Minggu 5–6)

| Tugas | Estimasi | Assignee |
|-------|----------|----------|
| Wizard framework (step navigation, progress bar, auto-save) | 5 hari | Frontend + Backend 1 |
| Step 1: Informasi Mata Kuliah | 3 hari | Backend 2 + Frontend |
| Step 2: Pemetaan CPL | 3 hari | Backend 2 + Frontend |
| Step 3: CPMK | 4 hari | Backend 1 + Frontend |
| Step 4: Sub-CPMK | 5 hari | Backend 1 + Frontend |
| Auto-save mechanism (Livewire debounce + database) | 3 hari | Backend 1 |
| Inline validation rules untuk Step 1–4 | 3 hari | Backend 2 |

**Deliverable:** Wizard Step 1–4 berfungsi; data tersimpan otomatis; validasi inline berjalan.

#### Sprint 4 — RPS Builder (Part 2) + Workflow (Minggu 7–8)

| Tugas | Estimasi | Assignee |
|-------|----------|----------|
| Step 5: Assessment & Bobot | 5 hari | Backend 1 + Frontend |
| Step 6: Materi Pembelajaran | 4 hari | Backend 2 + Frontend |
| Step 7: Referensi | 3 hari | Backend 2 + Frontend |
| Step 8: Review & Submit | 4 hari | Backend 1 + Frontend |
| Mapping logic (CPL→CPMK→SubCPMK→Assessment validation) | 5 hari | Backend 1 |
| Workflow engine (status transitions, permissions) | 5 hari | Backend 2 |
| Submit RPS flow (draft → review) | 3 hari | Backend 2 + Frontend |
| Review UI (Kaprodi view, approve, reject) | 4 hari | Frontend |

**Deliverable:** Wizard lengkap 8 step; workflow draft → review → approved / revision berfungsi.

#### Sprint 5 — Export + Dashboard (Minggu 9–10)

| Tugas | Estimasi | Assignee |
|-------|----------|----------|
| PHPWord integration + SN-DIKTI template | 5 hari | Backend 1 |
| DomPDF/PDF export (dari template Word) | 4 hari | Backend 1 |
| Export UI (tombol export, download, loading state) | 2 hari | Frontend |
| Dosen Dashboard | 5 hari | Frontend + Backend 2 |
| Kaprodi Dashboard | 4 hari | Frontend + Backend 2 |
| Dashboard chart components (simple bar/counter) | 3 hari | Frontend |

**Deliverable:** Export Word dan PDF berfungsi; Dashboard Dosen dan Kaprodi menampilkan data.

#### Sprint 6 — Polish + Stabilize (Minggu 11–12)

| Tugas | Estimasi | Assignee |
|-------|----------|----------|
| Notification system (Email via SMTP + In-App via database) | 5 hari | Backend 2 |
| Notification UI (bell icon, list, mark read) | 3 hari | Frontend |
| Versioning system (auto-version, history, diff) | 4 hari | Backend 1 |
| Audit log implementation | 4 hari | Backend 2 |
| Bug fixing (seluruh platform) | 5 hari | Semua |
| Full regression testing | 5 hari | QA |
| Performance optimization (query, cache, assets) | 4 hari | Backend 1 |
| Accessibility audit + fixes | 3 hari | Frontend |
| Security review (OWASP top 10 check) | 3 hari | Backend 1 |
| Documentation (user manual, admin guide) | 4 hari | PM + Backend 2 |
| Deployment preparation (production environment, backup, monitoring) | 3 hari | Backend 1 |

**Deliverable:** MVP siap rilis; semua critical bugs fixed; notifikasi, versioning, dan audit log berfungsi.

---

## MVP Team

| Peran | Jumlah | Tanggung Jawab | Keterangan |
|-------|--------|----------------|------------|
| **Backend Developer 1** | 1 | Arsitektur database, workflow engine, mapping logic, export, versioning, audit log | Senior Laravel Developer |
| **Backend Developer 2** | 1 | Authentication, user management, master data, notification, API, integration | Mid-level Laravel Developer |
| **Frontend Developer** | 1 | Seluruh UI/UX, wizard, dashboard, responsive design, accessibility | Livewire + Alpine.js + Tabler |
| **QA Engineer** | 1 | Test case, manual testing, automated testing (Cypress/Dusk), regression testing | Full-time QA |
| **Product Manager** | 1 | PRD, backlog management, stakeholder communication, sprint planning, acceptance criteria | PM + BA role |
| **UI/UX Designer** | 1 | Wireframe, mockup, design system, prototyping, usability testing | Paruh waktu (50% alokasi) |

---

## MVP Deliverables

### Code Deliverables

| Deliverable | Deskripsi |
|-------------|-----------|
| **Laravel Application** | Source code aplikasi lengkap di repository Git |
| **Database Migrations** | Semua migration files; dapat dijalankan dengan php artisan migrate |
| **Seeders** | Database seeders untuk data awal: role, permission, template default, data contoh |
| **Automated Tests** | Unit tests (≥ 70% coverage) + Feature tests (critical paths) + Browser tests (E2E key flows) |
| **CI/CD Pipeline** | GitHub Actions: lint, static analysis, test, deploy staging |

### Documentation Deliverables

| Deliverable | Deskripsi |
|-------------|-----------|
| **User Manual** | Panduan penggunaan dalam Bahasa Indonesia (PDF) |
| **Admin Guide** | Panduan administrator tenant (setup, user management, data master) |
| **Deployment Guide** | Panduan deployment ke production (server requirements, environment variables, setup steps) |
| **API Documentation** | Dokumentasi API internal (jika ada) |
| **Release Notes** | Catatan rilis MVP |

### Infrastructure Deliverables

| Deliverable | Deskripsi |
|-------------|-----------|
| **Staging Environment** | Server staging untuk testing dan demo |
| **Production Environment** | Server production siap pakai |
| **Database** | MariaDB production dengan backup harian |
| **Redis** | Redis untuk cache dan session |
| **Queue Worker** | Laravel Horizon untuk background jobs (export, email) |
| **Monitoring** | Health checks + basic alerting (lihat 36-monitoring.md) |
| **Backup** | Backup harian + off-site (lihat 34-backup-strategy.md) |

---

## MVP Constraints & Assumptions

### Constraints

| Constraint | Deskripsi |
|------------|-----------|
| **Single Tenant** | MVP hanya mendukung 1 tenant (1 universitas); multi-tenant arsitektur untuk Fase 3 |
| **No AI** | Tidak ada fitur AI di MVP; semua input manual |
| **Limited Roles** | Hanya 4 role: Superadmin, Admin Tenant, Kaprodi, Dosen |
| **Indonesian Only** | Bahasa Indonesia saja; tidak ada i18n |
| **No SSO** | Hanya email/password authentication |
| **No Mobile App** | Hanya web responsive; tidak ada PWA/native app |

### Assumptions

| Assumption | Deskripsi |
|------------|-----------|
| **Data Awal Tersedia** | Tenant memiliki data MK, Dosen, CPL yang siap diinput (manual atau CSV) |
| **Infrastruktur Tersedia** | Server VPS/Local dengan spesifikasi minimum (2 vCPU, 4 GB RAM, 50 GB SSD) |
| **Dosen Familiar** | Dosen familiar dengan penggunaan aplikasi web; kurva pembelajaran minimal |
| **Format SN-DIKTI Stabil** | Format RPS SN-DIKTI tidak mengalami perubahan signifikan selama pengembangan MVP |
| **Email Deliverability** | Layanan SMTP tersedia (SendGrid, Mailgun, atau server SMTP universitas) |

---

**Navigasi:** [Sebelumnya: Future Roadmap](40-future-roadmap.md) | [Daftar Isi](../README.md) | [Berikutnya: Product Backlog](42-product-backlog.md)
