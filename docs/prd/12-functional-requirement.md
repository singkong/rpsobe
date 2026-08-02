# 12 — Functional Requirement

## Kebutuhan Fungsional

### FR-A: Autentikasi & Otorisasi

| ID | Requirement | Prioritas | User Story |
|----|-------------|-----------|------------|
| FR-A01 | Sistem menyediakan halaman login dengan email dan password | P0 | Sebagai pengguna, saya ingin login agar dapat mengakses platform |
| FR-A02 | Sistem menyediakan halaman register dengan invitation code | P0 | Sebagai dosen baru, saya ingin mendaftar menggunakan kode undangan |
| FR-A03 | Sistem mengirim email verifikasi setelah registrasi | P0 | Sebagai pengguna, saya ingin memverifikasi email saya |
| FR-A04 | Sistem menyediakan fitur forgot password via email | P0 | Sebagai pengguna, saya ingin mereset password yang lupa |
| FR-A05 | Sistem mendukung role-based access control (RBAC) | P0 | Sebagai admin, saya ingin mengatur akses berdasarkan peran |
| FR-A06 | Sistem mengelola session (login, logout, timeout) | P0 | Sebagai pengguna, saya ingin session saya aman |
| FR-A07 | Sistem melakukan auto-logout setelah 30 menit inactivity | P1 | Sebagai pengguna, saya ingin akun aman jika lupa logout |
| FR-A08 | Sistem mendukung multiple device login dengan notifikasi | P1 | Sebagai pengguna, saya ingin tahu jika akun saya diakses dari perangkat lain |
| FR-A09 | Sistem menyediakan arsitektur siap integrasi SSO (SAML/OAuth) | P2 | Sebagai admin univ, saya ingin integrasi SSO kampus |
| FR-A10 | Sistem menyediakan arsitektur siap MFA (TOTP/WebAuthn) | P2 | Sebagai pengguna, saya ingin keamanan berlapis |

### FR-B: Manajemen Pengguna

| ID | Requirement | Prioritas | User Story |
|----|-------------|-----------|------------|
| FR-B01 | Sistem menyediakan daftar pengguna dengan filter dan search | P0 | Sebagai admin, saya ingin melihat semua pengguna |
| FR-B02 | Admin dapat membuat pengguna baru | P0 | Sebagai admin, saya ingin menambahkan dosen baru |
| FR-B03 | Admin dapat mengedit data pengguna | P0 | Sebagai admin, saya ingin mengubah data pengguna |
| FR-B04 | Admin dapat menonaktifkan (deactivate) pengguna | P0 | Sebagai admin, saya ingin menonaktifkan akun dosen yang keluar |
| FR-B05 | Admin dapat mengundang pengguna via email | P0 | Sebagai kaprodi, saya ingin mengundang dosen bergabung |
| FR-B06 | Pengguna dapat mengedit profil sendiri | P0 | Sebagai dosen, saya ingin mengupdate profil saya |
| FR-B07 | Pengguna dapat mengganti password | P0 | Sebagai pengguna, saya ingin mengganti password |
| FR-B08 | Sistem mendukung bulk import pengguna via CSV | P1 | Sebagai admin, saya ingin mengimpor 50 dosen sekaligus |
| FR-B09 | Sistem menampilkan log aktivitas per pengguna | P1 | Sebagai admin, saya ingin melihat aktivitas pengguna |
| FR-B10 | Pengguna dapat upload foto profil (avatar) | P2 | Sebagai pengguna, saya ingin menambah foto profil |

### FR-C: Master Data — Universitas

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-C01 | Super Admin dapat menambah universitas (tenant) | P0 |
| FR-C02 | Super Admin dapat mengedit data universitas | P0 |
| FR-C03 | Super Admin dapat menangguhkan (suspend) universitas | P1 |
| FR-C04 | Admin Univ dapat mengedit data universitasnya | P0 |
| FR-C05 | Data universitas mencakup: nama, alamat, akronim, logo, website, akreditasi, kontak | P0 |

### FR-D: Master Data — Fakultas

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-D01 | Admin Univ/Admin Fakultas dapat menambah fakultas | P0 |
| FR-D02 | Admin Univ/Admin Fakultas dapat mengedit fakultas | P0 |
| FR-D03 | Admin Univ/Admin Fakultas dapat menonaktifkan fakultas | P1 |
| FR-D04 | Data fakultas mencakup: nama, kode, dekan, akreditasi | P0 |

### FR-E: Master Data — Program Studi

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-E01 | Admin Fakultas/Admin Prodi dapat menambah program studi | P0 |
| FR-E02 | Admin Fakultas/Admin Prodi dapat mengedit program studi | P0 |
| FR-E03 | Admin Fakultas/Admin Prodi dapat menonaktifkan program studi | P1 |
| FR-E04 | Data prodi mencakup: nama, kode, jenjang (D3/S1/S2/S3), akreditasi, kaprodi, profil lulusan | P0 |

### FR-F: Master Data — Kurikulum

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-F01 | Admin Prodi/Kaprodi dapat menambah kurikulum | P0 |
| FR-F02 | Admin Prodi/Kaprodi dapat mengedit kurikulum | P0 |
| FR-F03 | Sistem mendukung multiple kurikulum per prodi | P0 |
| FR-F04 | Kurikulum memiliki status: aktif / tidak aktif | P0 |
| FR-F05 | Data kurikulum mencakup: nama, tahun mulai, tahun berakhir, jumlah SKS total, status | P0 |

### FR-G: Master Data — Semester

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-G01 | Admin Univ dapat mengelola periode semester | P0 |
| FR-G02 | Data semester mencakup: nama (Ganjil 2026/2027), tipe (Ganjil/Genap), tanggal mulai, tanggal selesai | P0 |

### FR-H: Master Data — Mata Kuliah

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-H01 | Admin Prodi/Kaprodi dapat menambah mata kuliah ke kurikulum | P0 |
| FR-H02 | Admin Prodi/Kaprodi dapat mengedit mata kuliah | P0 |
| FR-H03 | Mata kuliah dapat memiliki dosen pengampu (multiple) | P0 |
| FR-H04 | Data mata kuliah mencakup: nama, kode, SKS, semester, jenis (wajib/pilihan), deskripsi | P0 |
| FR-H05 | Sistem mendukung bulk import mata kuliah via CSV | P1 |

### FR-I: Master Data — Profil Lulusan

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-I01 | Kaprodi dapat menambah profil lulusan | P0 |
| FR-I02 | Kaprodi dapat mengedit profil lulusan | P0 |
| FR-I03 | Profil lulusan dapat dikaitkan dengan CPL | P0 |
| FR-I04 | Data profil lulusan mencakup: nama profil, deskripsi | P0 |

### FR-J: Master Data — CPL

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-J01 | Kaprodi dapat menambah CPL | P0 |
| FR-J02 | Kaprodi dapat mengedit CPL | P0 |
| FR-J03 | Kaprodi dapat menghapus CPL (jika belum digunakan) | P0 |
| FR-J04 | CPL dapat dikategorikan: Sikap, Pengetahuan, Keterampilan Umum, Keterampilan Khusus | P0 |
| FR-J05 | CPL memiliki kode otomatis atau manual | P0 |
| FR-J06 | CPL dapat dikaitkan dengan profil lulusan | P0 |

### FR-K: Master Data — Dosen

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-K01 | Admin Prodi/Kaprodi dapat menambah data dosen | P0 |
| FR-K02 | Admin Prodi/Kaprodi dapat mengedit data dosen | P0 |
| FR-K03 | Data dosen mencakup: NIDN/NIDK, nama, gelar, jabatan fungsional, bidang keahlian, email, kontak | P0 |
| FR-K04 | Dosen dapat dikaitkan dengan mata kuliah (dosen pengampu) | P0 |
| FR-K05 | Satu dosen dapat mengampu banyak mata kuliah | P0 |

### FR-L: Master Data — Referensi

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-L01 | Dosen dapat menambah referensi | P1 |
| FR-L02 | Dosen dapat mengedit referensi | P1 |
| FR-L03 | Referensi memiliki format: APA, IEEE, Harvard, atau bebas | P1 |
| FR-L04 | Referensi dapat digunakan di banyak RPS (shared) | P1 |
| FR-L05 | AI dapat men-generate referensi | P2 |

### FR-M: RPS Builder — Wizard

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M01 | Sistem menyediakan wizard 8 langkah untuk membuat RPS | P0 |
| FR-M02 | Pengguna dapat navigasi Next/Previous antar langkah | P0 |
| FR-M03 | Setiap langkah memiliki validasi sebelum lanjut | P0 |
| FR-M04 | Progress disimpan otomatis setiap 30 detik (draft) | P0 |
| FR-M05 | Progress bar menunjukkan persentase penyelesaian | P0 |
| FR-M06 | Pengguna dapat menyimpan sebagai draft dan melanjutkan nanti | P0 |
| FR-M07 | Pengguna dapat menduplikasi RPS yang sudah ada | P1 |
| FR-M08 | Setiap langkah memiliki tooltip dan bantuan konteks | P0 |

#### FR-M Step 1: Informasi Mata Kuliah

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M1-01 | Pilih kurikulum (auto-filter semester dan MK) | P0 |
| FR-M1-02 | Pilih mata kuliah dari daftar | P0 |
| FR-M1-03 | Auto-fill data MK: nama, kode, SKS, semester | P0 |
| FR-M1-04 | Pilih dosen pengampu (multiple) | P0 |
| FR-M1-05 | Pilih semester aktif | P0 |
| FR-M1-06 | Isi deskripsi mata kuliah (opsional — bisa dari AI) | P1 |

#### FR-M Step 2: Pilih CPL

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M2-01 | Menampilkan daftar CPL dari kurikulum terpilih | P0 |
| FR-M2-02 | Filter CPL berdasarkan kategori (Sikap, Pengetahuan, dll) | P0 |
| FR-M2-03 | Pilih CPL yang didukung oleh mata kuliah ini (checkbox) | P0 |
| FR-M2-04 | Minimal 1 CPL harus dipilih | P0 |
| FR-M2-05 | Tampilkan informasi detail setiap CPL | P0 |

#### FR-M Step 3: CPMK

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M3-01 | Menampilkan CPL yang sudah dipilih | P0 |
| FR-M3-02 | Tambah CPMK secara manual (form per CPMK) | P0 |
| FR-M3-03 | Setiap CPMK harus dipetakan ke minimal 1 CPL | P0 |
| FR-M3-04 | AI dapat men-generate CPMK dari CPL terpilih | P1 |
| FR-M3-05 | Setiap CPMK memiliki: kode, deskripsi, bobot terhadap CPL | P0 |
| FR-M3-06 | Minimal 1 CPMK | P0 |
| FR-M3-07 | Visualisasi mapping CPL → CPMK | P0 |

#### FR-M Step 4: Sub-CPMK

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M4-01 | Menampilkan CPMK yang sudah dibuat | P0 |
| FR-M4-02 | Tambah Sub-CPMK secara manual | P0 |
| FR-M4-03 | Setiap Sub-CPMK dipetakan ke minimal 1 CPMK | P0 |
| FR-M4-04 | AI dapat men-generate Sub-CPMK dari CPMK | P1 |
| FR-M4-05 | Setiap Sub-CPMK memiliki: kode, deskripsi, pertemuan terkait, level taksonomi Bloom | P0 |
| FR-M4-06 | Visualisasi mapping CPMK → Sub-CPMK | P0 |

#### FR-M Step 5: Materi Pembelajaran

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M5-01 | Menampilkan tabel pertemuan (1-16) | P0 |
| FR-M5-02 | Setiap pertemuan: materi, Sub-CPMK terkait, indikator, referensi | P0 |
| FR-M5-03 | AI dapat men-generate materi dari Sub-CPMK | P1 |
| FR-M5-04 | Drag-and-drop untuk menyusun urutan materi | P1 |
| FR-M5-05 | Jumlah pertemuan fleksibel (default 16) | P0 |

#### FR-M Step 6: Metode Pembelajaran

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M6-01 | Pilih metode pembelajaran per pertemuan dari daftar | P0 |
| FR-M6-02 | Metode yang tersedia: Ceramah, Diskusi, PBL, CBL, PJBL, Simulasi, Praktikum, Studi Kasus, dll | P0 |
| FR-M6-03 | Kombinasi beberapa metode dalam 1 pertemuan | P0 |
| FR-M6-04 | AI dapat merekomendasikan metode pembelajaran | P2 |

#### FR-M Step 7: Assessment

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M7-01 | Tambah komponen assessment (UTS, UAS, Tugas, Kuis, Praktikum, dll) | P0 |
| FR-M7-02 | Setiap assessment memiliki: nama, bobot (%), jenis (formatif/sumatif), Sub-CPMK terkait | P0 |
| FR-M7-03 | Total bobot harus 100% | P0 |
| FR-M7-04 | AI dapat men-generate assessment dan rubrik | P1 |
| FR-M7-05 | Assessment dipetakan ke Sub-CPMK (constructive alignment) | P0 |

#### FR-M Step 8: Review & Finalisasi

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-M8-01 | Pratinjau seluruh isi RPS dalam format dokumen | P0 |
| FR-M8-02 | Tampilkan ringkasan constructive alignment | P0 |
| FR-M8-03 | Validasi final sebelum submit | P0 |
| FR-M8-04 | Tombol "Simpan Draft" dan "Ajukan Review" | P0 |

### FR-N: Mapping & Constructive Alignment

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-N01 | Sistem menyimpan relasi CPL → CPMK → Sub-CPMK → Assessment | P0 |
| FR-N02 | Sistem dapat menampilkan visualisasi mapping (diagram/tabel) | P1 |
| FR-N03 | Sistem mendeteksi CPL yang tidak memiliki CPMK (gap) | P1 |
| FR-N04 | Sistem mendeteksi Sub-CPMK yang tidak dipetakan ke assessment | P1 |
| FR-N05 | AI Validator memeriksa constructive alignment | P1 |

### FR-O: Workflow & Approval

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-O01 | RPS memiliki status: Draft, Review, Revision, Approved, Published, Archived | P0 |
| FR-O02 | Dosen dapat mengajukan RPS dari Draft ke Review | P0 |
| FR-O03 | Reviewer dapat menyetujui atau meminta revisi | P0 |
| FR-O04 | Jika revisi, dosen dapat mengedit dan mengajukan ulang | P0 |
| FR-O05 | Kaprodi melakukan approval akhir (Approved) | P0 |
| FR-O06 | RPS yang Approved dapat di-Publish | P0 |
| FR-O07 | RPS yang Published dapat di-Archive | P0 |
| FR-O08 | Setiap perubahan status tercatat di history | P0 |
| FR-O09 | Reviewer harus memberikan alasan jika meminta revisi | P0 |
| FR-O10 | Reviewer dapat memberikan skor dan komentar per komponen | P0 |
| FR-O11 | Sistem mengirim notifikasi setiap perubahan status | P0 |

### FR-P: AI Integration

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-P01 | AI Assistant dapat men-generate CPMK berdasarkan CPL | P1 |
| FR-P02 | AI Assistant dapat men-generate Sub-CPMK berdasarkan CPMK | P1 |
| FR-P03 | AI Assistant dapat men-generate materi pembelajaran | P1 |
| FR-P04 | AI Assistant dapat men-generate referensi | P1 |
| FR-P05 | AI Assistant dapat men-generate assessment | P1 |
| FR-P06 | AI Assistant dapat men-generate rubrik penilaian | P1 |
| FR-P07 | AI Validator memeriksa taksonomi Bloom | P1 |
| FR-P08 | AI Validator memeriksa constructive alignment | P1 |
| FR-P09 | AI Validator memeriksa jumlah CPMK (minimal) | P1 |
| FR-P10 | AI Validator memeriksa kecukupan pertemuan | P1 |
| FR-P11 | AI Validator memeriksa distribusi assessment | P1 |
| FR-P12 | AI Validator memeriksa bobot nilai (total 100%) | P1 |
| FR-P13 | AI Validator memeriksa kualitas dan kecukupan referensi | P1 |
| FR-P14 | AI Validator memeriksa konsistensi antar komponen | P1 |
| FR-P15 | AI Reviewer memberikan skor ringkasan RPS | P2 |
| FR-P16 | AI Reviewer memberikan komentar otomatis | P2 |
| FR-P17 | AI Reviewer memberikan saran perbaikan spesifik | P2 |
| FR-P18 | Semua output AI harus editable oleh pengguna | P1 |
| FR-P19 | Semua output AI ditandai sebagai "AI-generated" | P1 |

### FR-Q: Export

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-Q01 | Sistem dapat mengekspor RPS ke format Word (.docx) | P0 |
| FR-Q02 | Sistem dapat mengekspor RPS ke format PDF | P0 |
| FR-Q03 | Export menggunakan template sesuai universitas | P1 |
| FR-Q04 | Batch export beberapa RPS sekaligus | P1 |
| FR-Q05 | Hasil export mencakup: cover, identitas, CPL, CPMK, Sub-CPMK, materi per pertemuan, assessment, referensi | P0 |
| FR-Q06 | Header/footer mencakup logo universitas dan nomor halaman | P0 |

### FR-R: Dashboard

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-R01 | Dashboard Dosen: jumlah RPS, status, progress, to-do | P0 |
| FR-R02 | Dashboard Kaprodi: status RPS semua dosen, statistik, grafik | P0 |
| FR-R03 | Dashboard Fakultas: status per prodi | P1 |
| FR-R04 | Dashboard Universitas: statistik seluruh fakultas | P1 |
| FR-R05 | Dashboard LPM: statistik kualitas, audit trail | P2 |
| FR-R06 | Dashboard Admin: tenant management, usage | P1 |

### FR-S: Reporting

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-S01 | Statistik jumlah RPS per status, prodi, fakultas | P1 |
| FR-S02 | Grafik tren penyusunan RPS per semester | P1 |
| FR-S03 | Export laporan ke Excel (.xlsx) | P1 |
| FR-S04 | Export laporan ke PDF | P1 |
| FR-S05 | Filter laporan: periode, prodi, fakultas, status | P1 |

### FR-T: Notifikasi

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-T01 | Sistem mengirim email notifikasi untuk event penting | P0 |
| FR-T02 | Sistem menampilkan notifikasi in-app | P0 |
| FR-T03 | Notifikasi mencakup: ajuan review, hasil review, approval, reminder | P0 |
| FR-T04 | Pengguna dapat mengatur preferensi notifikasi | P1 |
| FR-T05 | Notification center dengan badge counter | P0 |

### FR-U: Versioning

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-U01 | Setiap perubahan RPS tersimpan sebagai versi baru | P0 |
| FR-U02 | Pengguna dapat melihat daftar versi (history) | P0 |
| FR-U03 | Pengguna dapat melihat perbedaan antar versi (diff) | P1 |
| FR-U04 | Pengguna dapat mengembalikan ke versi sebelumnya (rollback) | P1 |
| FR-U05 | Setiap versi memiliki label versi (v1.0, v2.0) | P0 |

### FR-V: Audit Log

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-V01 | Semua aktivitas pengguna tercatat (create, update, delete, status change) | P0 |
| FR-V02 | Log mencakup: timestamp, user, action, entity, old value, new value, IP address | P0 |
| FR-V03 | Admin dapat melihat dan memfilter log | P0 |
| FR-V04 | Export log ke CSV/Excel | P1 |
| FR-V05 | Kebijakan retensi log (default 2 tahun) | P2 |

### FR-W: Template

| ID | Requirement | Prioritas |
|----|-------------|-----------|
| FR-W01 | Sistem menyediakan template RPS default sesuai SN-DIKTI | P0 |
| FR-W02 | Admin Univ dapat mengunggah template kustom | P1 |
| FR-W03 | Template kustom dapat memiliki section yang berbeda | P1 |
| FR-W04 | Export mengikuti template yang dipilih | P1 |

---

**Total Functional Requirements: ~130 FR**

---

**Navigasi:** [Sebelumnya: Out of Scope](11-out-of-scope.md) | [Daftar Isi](../README.md) | [Berikutnya: Non Functional Requirement](13-non-functional-requirement.md)
