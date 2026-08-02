# 14 — Use Case

## Diagram Use Case

### Level 1: Use Case Utama

```mermaid
graph TD
    subgraph "RPS OBE Platform"
        UC1[Autentikasi]
        UC2[Manajemen Pengguna]
        UC3[Manajemen Master Data]
        UC4[Menyusun RPS]
        UC5[Mereview RPS]
        UC6[Menyetujui RPS]
        UC7[Export RPS]
        UC8[Monitoring & Reporting]
        UC9[Manajemen Tenant]
    end
    
    SA[Super Admin] --> UC9
    SA --> UC1
    AU[Admin Univ] --> UC2
    AU --> UC3
    AF[Admin Fakultas] --> UC3
    AP[Admin Prodi] --> UC3
    DS[Dosen] --> UC4
    DS --> UC7
    KP[Kaprodi] --> UC5
    KP --> UC6
    KP --> UC8
    RV[Reviewer] --> UC5
    LPM[LPM] --> UC8
    DS --> UC1
    KP --> UC1
    RV --> UC1
    LPM --> UC1
```

### Level 2: Rincian Use Case

```mermaid
graph TD
    subgraph "Use Case: Autentikasi"
        A1[Login]
        A2[Register dengan Invitation]
        A3[Lupa Password]
        A4[Verifikasi Email]
        A5[Logout]
    end
    
    subgraph "Use Case: Manajemen Pengguna"
        B1[Lihat Daftar User]
        B2[Tambah User]
        B3[Edit User]
        B4[Nonaktifkan User]
        B5[Undang User]
        B6[Edit Profil Sendiri]
        B7[Ganti Password]
    end
    
    subgraph "Use Case: Master Data"
        C1[Kelola Universitas]
        C2[Kelola Fakultas]
        C3[Kelola Program Studi]
        C4[Kelola Kurikulum]
        C5[Kelola Semester]
        C6[Kelola Mata Kuliah]
        C7[Kelola Dosen]
        C8[Kelola Profil Lulusan]
        C9[Kelola CPL]
        C10[Kelola Referensi]
    end
    
    subgraph "Use Case: RPS Builder"
        D1[Buat RPS Baru]
        D2[Edit Draft RPS]
        D3[Isi Step 1-8]
        D4[Simpan Draft]
        D5[Ajukan Review]
        D6[Duplikasi RPS]
        D7[Gunakan AI Assistant]
    end
    
    subgraph "Use Case: Workflow"
        E1[Review RPS]
        E2[Beri Skor & Komentar]
        E3[Minta Revisi]
        E4[Setujui RPS]
        E5[Publish RPS]
        E6[Archive RPS]
        E7[Lihat History]
    end
    
    subgraph "Use Case: Export"
        F1[Export ke Word]
        F2[Export ke PDF]
        F3[Batch Export]
    end
    
    subgraph "Use Case: Monitoring"
        G1[Dashboard Dosen]
        G2[Dashboard Kaprodi]
        G3[Dashboard Fakultas]
        G4[Dashboard Universitas]
        G5[Dashboard LPM]
        G6[Generate Report]
    end
```

---

## Spesifikasi Use Case Detail

### UC-01: Login

| Atribut | Detail |
|---------|--------|
| **ID** | UC-01 |
| **Nama** | Login |
| **Aktor** | Semua pengguna |
| **Precondition** | Pengguna memiliki akun terverifikasi |
| **Trigger** | Pengguna mengakses halaman login |
| **Main Flow** | 1. Pengguna memasukkan email dan password<br>2. Sistem memvalidasi kredensial<br>3. Sistem membuat session<br>4. Sistem mengarahkan ke dashboard sesuai role |
| **Alternative Flow** | 2a. Kredensial salah → Tampilkan pesan error<br>3a. Akun dinonaktifkan → Tampilkan pesan hubungi admin |
| **Postcondition** | Pengguna login dan masuk dashboard |

### UC-02: Register dengan Invitation

| Atribut | Detail |
|---------|--------|
| **ID** | UC-02 |
| **Nama** | Register dengan Invitation Code |
| **Aktor** | Dosen, Reviewer, Admin (diundang) |
| **Precondition** | Admin telah mengirim invitation email |
| **Trigger** | Pengguna mengklik link invitation di email |
| **Main Flow** | 1. Pengguna mengklik link invitation<br>2. Sistem menampilkan form registrasi (nama, password)<br>3. Pengguna mengisi form dan submit<br>4. Sistem membuat akun dan mengirim email verifikasi<br>5. Pengguna verifikasi email |
| **Alternative Flow** | 2a. Invitation expired → Tampilkan pesan minta invitation baru<br>4a. Email sudah terdaftar → Tampilkan pesan login |
| **Postcondition** | Akun baru dibuat, pengguna dapat login |

### UC-03: Menyusun RPS Baru

| Atribut | Detail |
|---------|--------|
| **ID** | UC-03 |
| **Nama** | Menyusun RPS Baru |
| **Aktor** | Dosen |
| **Precondition** | Dosen login, MK sudah ada di master data |
| **Trigger** | Dosen klik "Buat RPS Baru" |
| **Main Flow** | 1. Pilih kurikulum dan mata kuliah<br>2. Sistem auto-fill informasi MK<br>3. Pilih CPL yang didukung MK<br>4. Tambah CPMK (manual atau AI)<br>5. Tambah Sub-CPMK (manual atau AI)<br>6. Isi materi per pertemuan<br>7. Pilih metode pembelajaran<br>8. Tambah assessment dan bobot<br>9. Review dan finalisasi<br>10. Simpan atau ajukan review |
| **Alternative Flow** | 3a. Tidak ada CPL → Harus tambah CPL dulu<br>Di setiap step bisa "Simpan Draft" kapan saja<br>4a-5a. AI generate → Review dan edit output AI |
| **Postcondition** | RPS tersimpan sebagai Draft atau diajukan Review |

### UC-04: Mereview RPS

| Atribut | Detail |
|---------|--------|
| **ID** | UC-04 |
| **Nama** | Mereview RPS |
| **Aktor** | Reviewer, Kaprodi |
| **Precondition** | RPS dalam status Review |
| **Trigger** | Reviewer mendapat notifikasi RPS siap review |
| **Main Flow** | 1. Reviewer membuka RPS<br>2. Reviewer memeriksa setiap komponen<br>3. Reviewer menggunakan AI Validator (opsional)<br>4. Reviewer memberikan skor per komponen<br>5. Reviewer menulis komentar dan saran<br>6. Reviewer menyetujui atau meminta revisi |
| **Alternative Flow** | 6a. Setujui → Status berubah ke Approved<br>6b. Minta revisi → Status berubah ke Revision, dosen dapat notifikasi |
| **Postcondition** | RPS disetujui atau kembali ke dosen untuk revisi |

### UC-05: Validasi AI

| Atribut | Detail |
|---------|--------|
| **ID** | UC-05 |
| **Nama** | Menjalankan AI Validator |
| **Aktor** | Dosen, Reviewer, Kaprodi, LPM |
| **Precondition** | RPS memiliki konten minimal (CPMK, Sub-CPMK) |
| **Trigger** | Pengguna klik "Validasi AI" |
| **Main Flow** | 1. Sistem mengirim konten RPS ke AI<br>2. AI menganalisis 8 aspek validasi<br>3. AI mengembalikan hasil validasi (pass/warning/error)<br>4. Sistem menampilkan hasil per aspek<br>5. Pengguna membaca dan menindaklanjuti |
| **Alternative Flow** | 2a. AI timeout → Tampilkan error, coba lagi<br>2b. Konten terlalu panjang → Pangkas atau chunking |
| **Postcondition** | Hasil validasi tersimpan dan terkait dengan RPS |

### UC-06: Export RPS

| Atribut | Detail |
|---------|--------|
| **ID** | UC-06 |
| **Nama** | Export RPS |
| **Aktor** | Dosen, Kaprodi, LPM |
| **Precondition** | RPS minimal dalam status Draft |
| **Trigger** | Pengguna klik "Export" |
| **Main Flow** | 1. Pengguna memilih format (Word/PDF)<br>2. Pengguna memilih template<br>3. Sistem generate dokumen<br>4. Sistem mengirim file untuk di-download |
| **Alternative Flow** | 3a. Generate gagal → Tampilkan pesan error<br>4a. File besar → Tampilkan progress bar |
| **Postcondition** | File terdownload ke perangkat pengguna |

### UC-07: Manajemen Tenant

| Atribut | Detail |
|---------|--------|
| **ID** | UC-07 |
| **Nama** | Manajemen Tenant/Universitas |
| **Aktor** | Super Admin |
| **Precondition** | Super Admin login |
| **Trigger** | Universitas baru mendaftar/membeli langganan |
| **Main Flow** | 1. Super Admin membuat tenant baru<br>2. Sistem membuat database tenant (schema/db)<br>3. Super Admin mengisi data universitas<br>4. Super Admin menetapkan paket langganan<br>5. Super Admin membuat admin universitas pertama<br>6. Admin universitas mendapat invitation |
| **Alternative Flow** | 2a. Tenant sudah ada → Notifikasi duplikasi<br>4a. Trial → Set expiry date |
| **Postcondition** | Tenant baru siap digunakan |

### UC-08: Generate Report

| Atribut | Detail |
|---------|--------|
| **ID** | UC-08 |
| **Nama** | Generate Report |
| **Aktor** | Kaprodi, Dekan, LPM |
| **Precondition** | Terdapat data RPS |
| **Trigger** | Pengguna membuka halaman report |
| **Main Flow** | 1. Pengguna memilih parameter laporan (periode, prodi, status)<br>2. Pengguna memilih tipe laporan (statistik, grafik, detail)<br>3. Sistem mengumpulkan dan menghitung data<br>4. Sistem menampilkan laporan<br>5. Pengguna dapat export ke Excel/PDF |
| **Postcondition** | Laporan tampil atau terdownload |

---

## Use Case Relationships

```mermaid
graph TD
    UC4[Menyusun RPS] --> UC5A[Gunakan AI Assistant: includes]
    UC4 --> UC5B[Gunakan AI Validator: includes]
    UC4 --> UC4A[Simpan Draft: extends]
    UC4 --> UC4B[Ajukan Review: extends]
    UC5[Mereview RPS] --> UC5B
    UC5 --> UC5C[Beri Skor: includes]
    UC5 --> UC5D[Minta Revisi: extends]
    UC5 --> UC5E[Setujui: extends]
    UC3[Manajemen Master Data] --> UC3A[Tambah MK: includes]
    UC3 --> UC3B[Tambah CPL: includes]
    UC3 --> UC3C[Tambah Kurikulum: includes]
```

---

**Navigasi:** [Sebelumnya: Non Functional Requirement](13-non-functional-requirement.md) | [Daftar Isi](../README.md) | [Berikutnya: User Journey](15-user-journey.md)
