# 44 — Acceptance Criteria

## Ikhtisar

Dokumen ini mendefinisikan Acceptance Criteria (AC) untuk 20 user story paling kritis dalam RPS OBE. Acceptance criteria ditulis dalam format **Given/When/Then** (Gherkin-style) untuk memudahkan pengujian dan validasi. Setiap user story dilengkapi dengan edge cases dan negative scenarios untuk memastikan semua kemungkinan skenario tertangani. AC ini menjadi kontrak antara Product Manager, Developer, dan QA — sebuah user story hanya dianggap "Done" jika semua AC terpenuhi.

---

## Daftar User Story Kritis

| No | User Story ID | User Story | Modul |
|----|--------------|------------|-------|
| 1 | AUTH-001 | User Login | AUTH |
| 2 | AUTH-004 | Admin mengundang Dosen | AUTH |
| 3 | MASTER-001 | Membuat Universitas | MASTER |
| 4 | MASTER-017 | Membuat Mata Kuliah | MASTER |
| 5 | MASTER-028 | Membuat CPL | MASTER |
| 6 | BUILDER-001 | Memulai RPS baru (Step 1) | BUILDER |
| 7 | BUILDER-015 | Memetakan CPL ke CPMK (Step 3) | BUILDER |
| 8 | BUILDER-041 | Auto-save draft | BUILDER |
| 9 | WF-003 | Submit RPS untuk Review | WF |
| 10 | WF-006 | Reviewer mereview RPS | WF |
| 11 | WF-010 | Reviewer meminta revisi | WF |
| 12 | WF-012 | Dosen merevisi dan mengajukan ulang | WF |
| 13 | WF-009 | Kaprodi menyetujui RPS | WF |
| 14 | WF-015 | Mempublikasi RPS | WF |
| 15 | AI-004 | AI Generate CPMK | AI |
| 16 | AI-014 | AI Validasi RPS | AI |
| 17 | EXPORT-001 | Export RPS ke Word | EXPORT |
| 18 | EXPORT-005 | Export RPS ke PDF | EXPORT |
| 19 | DASH-005 | Dashboard Kaprodi | DASH |
| 20 | NOTIF-001 | Notifikasi saat status RPS berubah | NOTIF |

---

## 1. User Login

**User Story ID:** AUTH-001

**User Story:**
> Sebagai **pengguna**, saya ingin **login dengan email dan password** agar **dapat mengakses sistem sesuai role saya**.

### Acceptance Criteria

**AC-1: Login Berhasil**
| | |
|---|---|
| **Given** | Pengguna berada di halaman login dan memiliki akun aktif |
| **When** | Pengguna memasukkan email yang valid dan password yang benar, lalu klik "Masuk" |
| **Then** | Sistem mengotentikasi pengguna, membuat session, dan me-redirect ke dashboard sesuai role pengguna tersebut |

**AC-2: Login Gagal — Email Tidak Ditemukan**
| | |
|---|---|
| **Given** | Pengguna berada di halaman login |
| **When** | Pengguna memasukkan email yang tidak terdaftar di sistem |
| **Then** | Sistem menampilkan pesan error "Email tidak ditemukan" dan tidak me-redirect ke dashboard |

**AC-3: Login Gagal — Password Salah**
| | |
|---|---|
| **Given** | Pengguna berada di halaman login dengan akun terdaftar |
| **When** | Pengguna memasukkan email valid namun password salah |
| **Then** | Sistem menampilkan pesan error "Password salah" dan menambah counter percobaan gagal |

**AC-4: Rate Limiting — 5 Kali Gagal**
| | |
|---|---|
| **Given** | Pengguna telah gagal login sebanyak 5 kali dalam 15 menit |
| **When** | Pengguna mencoba login lagi dalam periode 15 menit yang sama |
| **Then** | Sistem menampilkan pesan "Terlalu banyak percobaan login. Silakan coba lagi dalam [X] menit." dan memblokir percobaan login |

**AC-5: Session Timeout — 8 Jam**
| | |
|---|---|
| **Given** | Pengguna telah login dan tidak melakukan aktivitas selama 8 jam |
| **When** | Pengguna mencoba mengakses halaman manapun |
| **Then** | Sistem me-redirect ke halaman login dengan pesan "Sesi Anda telah berakhir. Silakan login kembali." |

**AC-6: Remember Me**
| | |
|---|---|
| **Given** | Pengguna berada di halaman login |
| **When** | Pengguna mencentang "Ingat Saya" dan login berhasil |
| **Then** | Session cookie memiliki masa berlaku 30 hari; pengguna tidak perlu login ulang dalam periode tersebut |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Pengguna login di dua browser secara bersamaan | Kedua sesi valid dan independen; masing-masing memiliki session timeout sendiri |
| EC-2 | Pengguna login, lalu password diubah oleh admin | Sesi yang sedang aktif tetap berlaku hingga timeout atau logout |
| EC-3 | Browser menutup tanpa logout | Sesi tetap berlaku; pengguna masih login saat membuka browser kembali (jika session cookie belum expired) |
| EC-4 | Login saat maintenance mode | Sistem menampilkan halaman maintenance, bukan halaman login |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Mengisi email dengan format tidak valid (tanpa @) | Validasi client-side: "Format email tidak valid"; form tidak disubmit |
| NS-2 | Mengkosongkan field email dan password | Validasi client-side: "Email wajib diisi", "Password wajib diisi" |
| NS-3 | Akun dinonaktifkan oleh Admin | Pesan: "Akun Anda telah dinonaktifkan. Hubungi administrator." |
| NS-4 | SQL Injection di field login | Sistem menolak input berbahaya; tidak ada error 500; parameterisasi query aman |

---

## 2. Admin Mengundang Dosen

**User Story ID:** AUTH-004

**User Story:**
> Sebagai **Admin Tenant**, saya ingin **mengundang dosen melalui email invitation** agar **dosen dapat mengakses sistem tanpa perlu registrasi publik**.

### Acceptance Criteria

**AC-1: Kirim Invitation**
| | |
|---|---|
| **Given** | Admin Tenant berada di halaman "Undang Pengguna" |
| **When** | Admin mengisi email dosen, memilih role "Dosen", memilih Prodi, dan klik "Kirim Undangan" |
| **Then** | Sistem menyimpan data invitation dengan status "pending", membuat token unik, mengirim email invitation ke email dosen, dan menampilkan pesan sukses |

**AC-2: Email Invitation Diterima**
| | |
|---|---|
| **Given** | Admin telah mengirim invitation |
| **When** | Dosen membuka email |
| **Then** | Email berisi: nama pengundang, nama institusi, link invitation, instruksi registrasi, informasi masa berlaku 7 hari |

**AC-3: Registrasi via Link Invitation**
| | |
|---|---|
| **Given** | Dosen menerima email dengan link invitation yang masih valid |
| **When** | Dosen mengklik link invitation dan mengisi form registrasi (nama, password, konfirmasi password) |
| **Then** | Sistem memvalidasi data, membuat akun user, mengubah status invitation menjadi "accepted", dan me-redirect ke halaman login dengan pesan sukses |

**AC-4: Validasi Password**
| | |
|---|---|
| **Given** | Dosen berada di form registrasi via invitation |
| **When** | Dosen mengisi password kurang dari 8 karakter atau hanya terdiri dari huruf |
| **Then** | Sistem menampilkan pesan error "Password minimal 8 karakter dan harus mengandung kombinasi huruf dan angka" |

**AC-5: Lihat Status Invitation**
| | |
|---|---|
| **Given** | Admin Tenant berada di halaman "Daftar Undangan" |
| **When** | Admin melihat tabel invitation |
| **Then** | Tabel menampilkan: email, role, prodi, tanggal undangan, status (pending/accepted/expired); dapat difilter berdasarkan status |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Mengundang email yang sudah terdaftar | Pesan error: "Email sudah terdaftar sebagai pengguna aktif" |
| EC-2 | Mengundang email yang masih memiliki invitation pending | Pesan error: "Email ini sudah memiliki undangan yang belum kadaluarsa" |
| EC-3 | Dosen mengklik link invitation yang sudah digunakan | Halaman "Link Tidak Valid" dengan pesan "Link ini sudah digunakan" dan tombol "Minta Undangan Baru" |
| EC-4 | Resend invitation (max 3x) | Tombol resend muncul untuk invitation pending; klik resend → reset expiration timer; setelah 3x → tombol disabled |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Mengisi email kosong atau format salah | Validasi client-side: "Email wajib diisi dengan format yang benar" |
| NS-2 | Token invitation dimodifikasi manual di URL | Sistem mendeteksi token tidak valid; tampilkan halaman error "Link tidak valid" |
| NS-3 | Password dan konfirmasi password tidak cocok | Validasi client-side: "Konfirmasi password tidak cocok" |
| NS-4 | Email invitation masuk spam | Gunakan SMTP terautentikasi dengan DKIM/SPF untuk meningkatkan deliverability |

---

## 3. Membuat Universitas

**User Story ID:** MASTER-001

**User Story:**
> Sebagai **Superadmin**, saya ingin **membuat data universitas baru** agar **tenant dapat melakukan onboarding dan mulai menggunakan platform**.

### Acceptance Criteria

**AC-1: Create Universitas — Data Lengkap**
| | |
|---|---|
| **Given** | Superadmin berada di halaman "Tambah Universitas" |
| **When** | Superadmin mengisi: nama universitas, kode universitas (unik), alamat, website, mengunggah logo, dan klik "Simpan" |
| **Then** | Sistem memvalidasi kode unik, menyimpan data universitas, auto-setup tenant (database schema, default role, template default), dan menampilkan pesan sukses "Universitas berhasil dibuat" |

**AC-2: Kode Universitas Unik**
| | |
|---|---|
| **Given** | Superadmin berada di halaman "Tambah Universitas" |
| **When** | Superadmin memasukkan kode universitas yang sudah terdaftar dan klik "Simpan" |
| **Then** | Sistem menampilkan pesan error "Kode universitas sudah digunakan" dan tidak menyimpan data |

**AC-3: Upload Logo**
| | |
|---|---|
| **Given** | Superadmin berada di form "Tambah Universitas" |
| **When** | Superadmin mengunggah file logo dengan format .jpg/.png ukuran > 2MB |
| **Then** | Sistem menampilkan pesan error "Ukuran file maksimal 2MB" atau "Format file tidak didukung (hanya JPG dan PNG)" |

**AC-4: Auto-Setup Tenant**
| | |
|---|---|
| **Given** | Universitas berhasil dibuat |
| **When** | Sistem melakukan setup tenant |
| **Then** | Tenant memiliki: Admin Tenant default (email yang didaftarkan saat create), role default (Superadmin, Admin Tenant, Kaprodi, Dosen), template default SN-DIKTI |

**AC-5: Daftar Universitas**
| | |
|---|---|
| **Given** | Superadmin berada di halaman "Daftar Universitas" |
| **When** | Superadmin melihat daftar |
| **Then** | Tabel menampilkan: nama, kode, alamat, website, jumlah fakultas, jumlah pengguna, status tenant; dapat dicari dan dipaginasi |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Membuat universitas dengan nama yang sudah ada tapi kode berbeda | Diizinkan (nama boleh mirip, kode harus unik) |
| EC-2 | Logo tidak diupload (opsional) | Sistem menggunakan logo placeholder default dengan inisial nama universitas |
| EC-3 | Website tanpa "https://" | Sistem otomatis menambahkan "https://" di depan URL |
| EC-4 | Admin Tenant default sudah ada di tenant lain | Sistem membuat user baru; satu user bisa menjadi admin di beberapa tenant |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Semua field wajib kosong | Validasi: "Nama universitas wajib diisi", "Kode universitas wajib diisi", "Alamat wajib diisi" |
| NS-2 | Mengupload file dengan ekstensi .exe atau .php | Sistem menolak file; pesan: "Format file tidak didukung" |
| NS-3 | Koneksi database gagal saat auto-setup tenant | Rollback semua data; pesan error: "Gagal membuat tenant. Silakan coba lagi." |

---

## 4. Membuat Mata Kuliah

**User Story ID:** MASTER-017

**User Story:**
> Sebagai **Kaprodi**, saya ingin **membuat data mata kuliah baru** agar **dapat digunakan sebagai dasar penyusunan RPS oleh dosen**.

### Acceptance Criteria

**AC-1: Create Mata Kuliah — Data Lengkap**
| | |
|---|---|
| **Given** | Kaprodi berada di halaman "Tambah Mata Kuliah" |
| **When** | Kaprodi mengisi: kode MK, nama MK, SKS teori, SKS praktikum, semester ke, jenis (wajib/pilihan), memilih Prodi, memilih Kurikulum aktif, dan klik "Simpan" |
| **Then** | Sistem memvalidasi kode MK unik dalam prodi, menyimpan data, dan menampilkan pesan sukses |

**AC-2: Kode MK Unik per Prodi**
| | |
|---|---|
| **Given** | Kaprodi mengisi form MK dengan kode yang sudah ada di prodi yang sama |
| **When** | Kaprodi klik "Simpan" |
| **Then** | Sistem menampilkan pesan error "Kode mata kuliah sudah digunakan di program studi ini" |

**AC-3: SKS Total Tervalidasi**
| | |
|---|---|
| **Given** | Kaprodi mengisi SKS teori = 0 dan SKS praktikum = 0 |
| **When** | Kaprodi klik "Simpan" |
| **Then** | Sistem menampilkan pesan "Total SKS minimal 1 (teori + praktikum)" |

**AC-4: Kurikulum Aktif Saja**
| | |
|---|---|
| **Given** | Kaprodi berada di form "Tambah Mata Kuliah" |
| **When** | Kaprodi membuka dropdown pilihan kurikulum |
| **Then** | Dropdown hanya menampilkan kurikulum dengan status "aktif" dari prodi yang dipilih |

**AC-5: Daftar Mata Kuliah dengan Filter**
| | |
|---|---|
| **Given** | Kaprodi berada di halaman "Daftar Mata Kuliah" |
| **When** | Kaprodi memfilter berdasarkan prodi, kurikulum, semester, dan jenis MK |
| **Then** | Tabel menampilkan data sesuai filter: kode, nama MK, SKS, semester, jenis, jumlah RPS; search by kode/nama MK |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Kode MK mengandung spasi atau karakter khusus | Sistem otomatis menghilangkan spasi; karakter khusus ditolak dengan pesan error |
| EC-2 | MK pilihan vs wajib — tidak ada perbedaan validasi | Keduanya diperlakukan sama; jenis hanya sebagai label |
| EC-3 | Mengedit kode MK yang sudah memiliki RPS | Konfirmasi: "Mata kuliah ini sudah memiliki [N] RPS. Mengubah kode MK akan mempengaruhi export dokumen." |
| EC-4 | Menghapus MK yang sudah memiliki RPS | Tidak diizinkan; pesan: "Mata kuliah ini sudah digunakan di [N] RPS. Hapus RPS terlebih dahulu atau nonaktifkan mata kuliah." |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | SKS negatif (-2) atau non-numerik | Validasi: "SKS harus berupa angka positif" |
| NS-2 | Semester ke di luar rentang 1-14 | Validasi: "Semester harus antara 1-14" |
| NS-3 | Tidak memilih prodi/kurikulum | Error: "Program studi wajib dipilih", "Kurikulum wajib dipilih" |

---

## 5. Membuat CPL

**User Story ID:** MASTER-028

**User Story:**
> Sebagai **Kaprodi**, saya ingin **membuat data CPL (Capaian Pembelajaran Lulusan)** agar **dapat digunakan sebagai acuan dalam penyusunan CPMK dan RPS**.

### Acceptance Criteria

**AC-1: Create CPL — Format Kode Otomatis**
| | |
|---|---|
| **Given** | Kaprodi berada di form "Tambah CPL" |
| **When** | Kaprodi memilih kategori "Sikap", mengisi deskripsi CPL, dan klik "Simpan" |
| **Then** | Sistem meng-generate kode otomatis "CPL-S-01" (format: CPL-{kode_kategori}-{nomor_urut_per_kategori}), menyimpan data, dan menampilkan pesan sukses |

**AC-2: Empat Kategori CPL**
| | |
|---|---|
| **Given** | Kaprodi berada di form "Tambah CPL" |
| **When** | Kaprodi membuka dropdown kategori |
| **Then** | Dropdown menampilkan 4 pilihan: Sikap (S), Pengetahuan (P), Keterampilan Umum (KU), Keterampilan Khusus (KK) |

**AC-3: Nomor Urut per Kategori**
| | |
|---|---|
| **Given** | Prodi sudah memiliki CPL-S-01, CPL-S-02, CPL-P-01 |
| **When** | Kaprodi membuat CPL baru dengan kategori "Sikap" |
| **Then** | Kode otomatis menjadi "CPL-S-03" (melanjutkan nomor urut di kategori Sikap) |

**AC-4: Kaitkan CPL dengan Profil Lulusan (Opsional)**
| | |
|---|---|
| **Given** | Kaprodi berada di form "Tambah CPL" |
| **When** | Kaprodi memilih satu atau lebih profil lulusan dari multi-select |
| **Then** | CPL tersimpan dengan relasi ke profil lulusan yang dipilih; mapping dapat dilihat di halaman detail CPL |

**AC-5: Daftar CPL dengan Filter Kategori**
| | |
|---|---|
| **Given** | Kaprodi berada di halaman "Daftar CPL" |
| **When** | Kaprodi memfilter berdasarkan kategori (S/P/KU/KK) |
| **Then** | Tabel menampilkan: kode, deskripsi, kategori, jumlah CPMK terkait, profil lulusan terkait |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Deskripsi CPL sangat panjang (> 500 karakter) | Validasi: "Deskripsi CPL maksimal 500 karakter"; counter karakter di textarea |
| EC-2 | Menghapus CPL yang sudah digunakan di minimal 1 RPS | Tidak diizinkan; pesan: "CPL ini sudah digunakan di [N] RPS/CPMK. Tidak dapat dihapus." |
| EC-3 | Mengedit CPL setelah digunakan di RPS | Diperbolehkan edit deskripsi; kode tidak dapat diubah |
| EC-4 | Semua kategori harus memiliki minimal 1 CPL | Warning di dashboard Kaprodi jika ada kategori tanpa CPL |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Deskripsi CPL kosong | Error: "Deskripsi CPL wajib diisi" |
| NS-2 | Tidak memilih kategori | Error: "Kategori CPL wajib dipilih" |
| NS-3 | Deskripsi terlalu pendek (< 10 karakter) | Warning: "Deskripsi CPL terlalu singkat. Disarankan minimal 10 karakter." (tetap dapat disimpan) |

---

## 6. Memulai RPS Baru (Step 1)

**User Story ID:** BUILDER-001

**User Story:**
> Sebagai **Dosen**, saya ingin **memulai pembuatan RPS baru melalui wizard 8 langkah** agar **dapat menyusun RPS secara terstruktur dan sistematis**.

### Acceptance Criteria

**AC-1: Mulai RPS Baru**
| | |
|---|---|
| **Given** | Dosen berada di dashboard atau halaman daftar RPS |
| **When** | Dosen mengklik tombol "Buat RPS Baru" |
| **Then** | Muncul modal/popup untuk memilih Mata Kuliah (dropdown searchable) dan Semester (dropdown). Setelah pilih → masuk ke wizard Step 1 |

**AC-2: Validasi Pemilihan MK**
| | |
|---|---|
| **Given** | Dosen berada di modal "Buat RPS Baru" |
| **When** | Dosen mengklik "Mulai" tanpa memilih MK atau Semester |
| **Then** | Sistem menampilkan pesan error "Mata kuliah wajib dipilih" dan/atau "Semester wajib dipilih" |

**AC-3: RPS Tersimpan sebagai Draft**
| | |
|---|---|
| **Given** | Dosen telah memilih MK dan Semester, lalu mengklik "Mulai" |
| **When** | Sistem membuat record RPS baru |
| **Then** | RPS tersimpan dengan status "Draft"; muncul di daftar "RPS Saya"; dosen diarahkan ke Step 1 wizard |

**AC-4: Progress Indicator**
| | |
|---|---|
| **Given** | Dosen berada di wizard RPS baru |
| **When** | Dosen melihat bagian atas wizard |
| **Then** | Progress bar menampilkan "0%" atau "12.5%"; sidebar menunjukkan 8 step dengan indikator Step 1 aktif (highlight), step lainnya disabled |

**AC-5: Navigasi Step**
| | |
|---|---|
| **Given** | Dosen berada di Step 1 dan sudah mengisi semua field wajib |
| **When** | Dosen mengklik tombol "Selanjutnya" |
| **Then** | Sistem memvalidasi Step 1; jika valid → Step 1 ditandai selesai (centang hijau), progress bertambah, dan navigasi ke Step 2 |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Dosen sudah memiliki RPS untuk MK dan semester yang sama | Peringatan: "Anda sudah memiliki RPS untuk mata kuliah ini di semester yang sama. Lanjutkan RPS yang ada atau buat duplikat?" |
| EC-2 | MK belum memiliki CPL di data master | Step 2 menampilkan pesan: "Belum ada CPL untuk program studi ini. Hubungi Kaprodi untuk menambahkan CPL." |
| EC-3 | Koneksi terputus saat memulai RPS baru | Data MK dan Semester yang sudah dipilih tersimpan; RPS Draft tetap dibuat |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | MK yang dipilih dari prodi lain (bukan prodi dosen) | Dropdown MK hanya menampilkan MK dari prodi dosen yang bersangkutan |
| NS-2 | Dosen mencoba akses URL wizard langsung tanpa memilih MK/Semester | Redirect ke halaman daftar RPS dengan pesan "Pilih mata kuliah dan semester terlebih dahulu" |

---

## 7. Memetakan CPL ke CPMK (Step 3)

**User Story ID:** BUILDER-015

**User Story:**
> Sebagai **Dosen**, saya ingin **merumuskan CPMK berdasarkan CPL yang sudah dipilih di Step 2** agar **setiap CPMK memiliki keterkaitan yang jelas dengan CPL dan constructive alignment terjaga**.

### Acceptance Criteria

**AC-1: Tambah CPMK**
| | |
|---|---|
| **Given** | Dosen berada di Step 3 wizard dengan CPL yang sudah dipilih di Step 2 |
| **When** | Dosen mengklik "Tambah CPMK" |
| **Then** | Form baru muncul dengan: kode CPMK otomatis (CPMK-01, CPMK-02...), textarea deskripsi, multi-select "CPL Terkait" (hanya menampilkan CPL yang dipilih di Step 2) |

**AC-2: Auto-Generate Kode CPMK**
| | |
|---|---|
| **Given** | Dosen menambahkan CPMK pertama, kedua, ketiga |
| **When** | Form CPMK baru ditambahkan |
| **Then** | Kode otomatis terisi: CPMK-01, CPMK-02, CPMK-03, dan seterusnya secara berurutan |

**AC-3: Mapping CPL-CPMK**
| | |
|---|---|
| **Given** | Dosen telah menambahkan 3 CPMK dengan mapping ke berbagai CPL |
| **When** | Dosen melihat tabel matriks CPL vs CPMK |
| **Then** | Tabel menampilkan baris CPL, kolom CPMK; sel yang terkait berisi tanda centang; sel kosong menandakan tidak ada keterkaitan |

**AC-4: Validasi Setiap CPMK Terkait Minimal 1 CPL**
| | |
|---|---|
| **Given** | Dosen menambahkan CPMK tanpa memilih CPL terkait |
| **When** | Dosen mengklik "Selanjutnya" |
| **Then** | Sistem menampilkan pesan error: "CPMK-0[X] belum dikaitkan dengan CPL manapun" dan tidak melanjutkan ke Step 4 |

**AC-5: Validasi Jumlah CPMK Minimal 3**
| | |
|---|---|
| **Given** | Dosen hanya menambahkan 2 CPMK |
| **When** | Dosen mengklik "Selanjutnya" |
| **Then** | Sistem menampilkan pesan error: "Minimal terdapat 3 CPMK dalam satu RPS" |

**AC-6: Validasi Jumlah CPMK Maksimal 12**
| | |
|---|---|
| **Given** | Dosen menambahkan 13 CPMK |
| **When** | Dosen mengklik "Tambah CPMK" untuk yang ke-13 |
| **Then** | Sistem menampilkan peringatan: "Jumlah CPMK maksimal 12. Pertimbangkan untuk menggabungkan CPMK yang serupa." (tetap bisa ditambahkan, hanya warning) |

**AC-7: Edit dan Hapus CPMK**
| | |
|---|---|
| **Given** | Dosen memiliki daftar CPMK |
| **When** | Dosen mengklik ikon edit pada CPMK tertentu |
| **Then** | Form CPMK menjadi editable (inline edit atau modal); dapat mengubah deskripsi dan CPL terkait |
| **When** | Dosen mengklik ikon hapus pada CPMK tertentu |
| **Then** | Konfirmasi hapus muncul; jika dikonfirmasi → CPMK terhapus; kode CPMK di-reorder |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | CPMK dihapus, lalu Sub-CPMK yang terkait ikut terhapus | Konfirmasi: "Menghapus CPMK ini juga akan menghapus [N] Sub-CPMK yang terkait. Lanjutkan?" |
| EC-2 | CPL yang dipilih di Step 2 tidak satupun memiliki CPMK | Warning saat validasi: "CPL berikut belum memiliki CPMK: [daftar]. Setiap CPL yang dipilih harus memiliki minimal 1 CPMK." |
| EC-3 | Duplikasi deskripsi CPMK (mirip/tidak disengaja) | Validasi: "Deskripsi CPMK ini mirip dengan CPMK-0[X]. Periksa kembali." (warning, tidak memblokir) |
| EC-4 | Auto-save saat mengetik CPMK | Setiap 1 detik setelah selesai mengetik, data tersimpan; indikator "Tersimpan" muncul |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Deskripsi CPMK kosong | Error: "Deskripsi CPMK wajib diisi" |
| NS-2 | Deskripsi CPMK menggunakan kata kerja non-operasional | Warning: "Deskripsi CPMK sebaiknya menggunakan kata kerja operasional (contoh: menganalisis, mengevaluasi, merancang)" |
| NS-3 | Semua CPMK dihapus | Error saat validasi: "Minimal terdapat 1 CPMK" |

---

## 8. Auto-Save Draft

**User Story ID:** BUILDER-041

**User Story:**
> Sebagai **Dosen**, saya ingin **RPS saya tersimpan otomatis setiap kali ada perubahan** agar **saya tidak kehilangan data jika koneksi terputus atau browser tertutup tiba-tiba**.

### Acceptance Criteria

**AC-1: Auto-Save Setiap Perubahan**
| | |
|---|---|
| **Given** | Dosen sedang mengedit data di wizard RPS (step manapun) |
| **When** | Dosen mengetik atau mengubah nilai field, lalu berhenti mengetik selama 1 detik |
| **Then** | Sistem otomatis menyimpan perubahan ke database; indikator status berubah dari "Menyimpan..." menjadi "Tersimpan" |

**AC-2: Indikator Auto-Save**
| | |
|---|---|
| **Given** | Dosen membuat perubahan pada data RPS |
| **When** | Auto-save sedang berlangsung dan setelah selesai |
| **Then** | Indikator menampilkan ikon spinner + teks "Menyimpan..." saat proses; ikon centang + teks "Tersimpan [jam:menit]" saat selesai; teks "Semua perubahan tersimpan" saat idle |

**AC-3: Resume setelah Browser Tertutup**
| | |
|---|---|
| **Given** | Dosen sedang mengisi Step 3 CPMK, lalu browser tertutup tanpa sengaja (crash, listrik mati) |
| **When** | Dosen membuka kembali browser, login, dan membuka RPS yang sama |
| **Then** | Dosen diarahkan ke step terakhir yang dikerjakan (Step 3) dengan semua data yang sudah di-auto-save tetap ada |

**AC-4: Conflicting Changes (Multi-Tab)**
| | |
|---|---|
| **Given** | Dosen membuka RPS yang sama di dua tab browser berbeda |
| **When** | Dosen mengedit di Tab A dan Tab B secara bersamaan |
| **Then** | Auto-save terakhir yang menang (last write wins); tidak ada konflik data yang menyebabkan error |

**AC-5: Tidak Mengganggu UX**
| | |
|---|---|
| **Given** | Dosen sedang aktif mengetik |
| **When** | Auto-save berjalan di background |
| **Then** | Tidak ada UI freeze, cursor jump, atau kehilangan fokus pada field yang sedang diketik; debounce 1000ms memastikan save hanya saat user berhenti mengetik |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Koneksi internet terputus saat auto-save | Indikator berubah menjadi "Gagal menyimpan — Coba lagi"; retry 3x; jika tetap gagal → simpan ke localStorage sebagai backup |
| EC-2 | Database error saat auto-save (misal: disk full) | Sistem menampilkan toast error: "Gagal menyimpan. Silakan coba lagi."; data tidak hilang dari UI |
| EC-3 | Validasi field gagal di server (data tidak valid) | Sistem menampilkan field error; auto-save tidak menyimpan field yang tidak valid; field valid tetap tersimpan |
| EC-4 | User logout otomatis (session timeout) saat mengedit | Auto-save terakhir tetap tersimpan; setelah relogin → data masih ada |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | XSS injection di field deskripsi | Input disanitasi sebelum disimpan; karakter HTML di-escape |
| NS-2 | Data terlalu besar (misal: paste 10.000 karakter di textarea) | Validasi: "Deskripsi maksimal 5000 karakter"; kelebihan dipotong dengan notifikasi |
| NS-3 | Race condition: auto-save berjalan 2x bersamaan | Queue auto-save request; hanya request terbaru yang diproses |

---

## 9. Submit RPS untuk Review

**User Story ID:** WF-003

**User Story:**
> Sebagai **Dosen**, saya ingin **mengajukan RPS yang sudah lengkap untuk direview** agar **Kaprodi dapat memeriksa dan menyetujui RPS saya**.

### Acceptance Criteria

**AC-1: Submit dari Step 8**
| | |
|---|---|
| **Given** | Dosen berada di Step 8 (Review & Submit) dengan RPS yang sudah lengkap |
| **When** | Dosen mencentang "Saya menyatakan RPS sudah lengkap dan benar", lalu mengklik "Submit untuk Review" |
| **Then** | Sistem menampilkan modal konfirmasi: "Anda akan mengajukan RPS ini untuk direview. Setelah diajukan, Anda tidak dapat mengedit RPS ini hingga reviewer memberikan keputusan. Lanjutkan?" |

**AC-2: Konfirmasi Submit**
| | |
|---|---|
| **Given** | Modal konfirmasi submit ditampilkan |
| **When** | Dosen mengklik "Ya, Ajukan" |
| **Then** | Status RPS berubah dari "Draft" menjadi "Review"; auto-version dibuat (v1.0); notifikasi dikirim ke Kaprodi; audit log tercatat; dosen diarahkan ke halaman detail RPS dengan status "Review" |

**AC-3: Validasi Sebelum Submit**
| | |
|---|---|
| **Given** | Dosen mengklik "Submit untuk Review" tetapi ada step yang belum valid |
| **When** | Sistem melakukan validasi seluruh step |
| **Then** | Sistem menampilkan daftar error yang harus diperbaiki: "Step 3: CPMK-03 belum dikaitkan dengan CPL", "Step 6: Total bobot belum 100%"; Dosen tidak dapat submit |

**AC-4: RPS Lock setelah Submit**
| | |
|---|---|
| **Given** | RPS sudah di-submit (status Review) |
| **When** | Dosen membuka RPS tersebut |
| **Then** | RPS ditampilkan dalam mode read-only; tombol "Edit" dan navigasi step tidak tersedia; pesan "RPS sedang dalam proses review" ditampilkan |

**AC-5: Notifikasi ke Kaprodi**
| | |
|---|---|
| **Given** | RPS berhasil di-submit |
| **When** | Sistem mengirim notifikasi |
| **Then** | Email ke Kaprodi: subjek "RPS [nama MK] Siap Direview"; isi: nama dosen, MK, semester, link ke halaman review. In-app notification juga dikirim |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Submit dari halaman detail RPS (bukan dari wizard) | Tombol "Submit untuk Review" tersedia di halaman detail; alur sama dengan submit dari Step 8 |
| EC-2 | Dosen membatalkan submit di modal konfirmasi | Modal tertutup; RPS tetap di status Draft; dapat diedit kembali |
| EC-3 | Tidak ada Kaprodi aktif di prodi | Peringatan: "Tidak ada Kaprodi aktif di program studi ini. Hubungi Admin Tenant."; tetap dapat submit, notifikasi tertunda |
| EC-4 | Submit ulang setelah revisi | Alur sama dengan submit awal; versi baru dibuat (v2.0); reviewer dapat melihat riwayat versi |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Dosen tidak mencentang konfirmasi "RPS sudah lengkap" | Tombol "Submit untuk Review" disabled; tooltip: "Centang konfirmasi di atas" |
| NS-2 | Double click pada tombol submit | Hanya request pertama yang diproses; request kedua diabaikan (idempotent) |
| NS-3 | Submit RPS yang sudah pernah di-submit (status Review) | Tombol submit hidden/disabled; label "Sudah Diajukan" |

---

## 10. Reviewer Mereview RPS

**User Story ID:** WF-006

**User Story:**
> Sebagai **Kaprodi (Reviewer)**, saya ingin **membaca dan mereview RPS yang diajukan** agar **dapat memberikan penilaian dan memastikan kualitas RPS sesuai standar SN-DIKTI**.

### Acceptance Criteria

**AC-1: Akses RPS untuk Review**
| | |
|---|---|
| **Given** | Kaprodi memiliki RPS yang di-assign untuk direview (atau RPS status Review di prodi-nya) |
| **When** | Kaprodi mengklik RPS dari daftar "Menunggu Review" |
| **Then** | Kaprodi melihat halaman detail RPS lengkap (semua step); tombol "Mulai Review" tersedia |

**AC-2: Form Review**
| | |
|---|---|
| **Given** | Kaprodi mengklik "Mulai Review" |
| **When** | Form review ditampilkan |
| **Then** | Form berisi: (a) Skor per komponen (CPL-CPMK, Sub-CPMK, Materi, Metode, Assessment, Referensi, Alignment) dengan skala 1-10, (b) Textarea catatan per komponen, (c) Textarea catatan umum, (d) Tombol "Setujui" dan "Minta Revisi" |

**AC-3: Skor Otomatis Tervalidasi**
| | |
|---|---|
| **Given** | Kaprodi mengisi form review |
| **When** | Kaprodi mengisi skor di luar rentang 1-10 atau mengosongkan skor |
| **Then** | Sistem menampilkan pesan error: "Skor harus antara 1-10" atau "Semua komponen harus diberi skor" |

**AC-4: Setujui RPS**
| | |
|---|---|
| **Given** | Kaprodi telah mengisi semua skor dan catatan, merasa RPS sudah memenuhi standar |
| **When** | Kaprodi mengklik "Setujui" |
| **Then** | Konfirmasi: "Anda akan menyetujui RPS ini. RPS akan berstatus Approved dan tidak dapat diedit oleh dosen. Lanjutkan?" → "Ya" → status berubah ke "Approved"; notifikasi ke dosen: "RPS Anda disetujui" |

**AC-5: Riwayat Review Tersimpan**
| | |
|---|---|
| **Given** | Kaprodi telah menyelesaikan review |
| **When** | Kaprodi atau Dosen membuka halaman detail RPS |
| **Then** | Hasil review (skor + komentar) terlihat di tab/seksi "Hasil Review" sebagai bagian dari workflow history |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Kaprodi menutup form review tanpa menyimpan | Tidak ada data review yang tersimpan; RPS tetap di status Review |
| EC-2 | Kaprodi mereview RPS yang juga di-assign ke reviewer lain | Hanya reviewer yang di-assign yang bisa mereview; Kaprodi tetap bisa melihat |
| EC-3 | RPS di-review oleh Kaprodi yang juga dosen pengampu RPS tersebut | Diizinkan; tetapi sistem menampilkan peringatan: "Anda adalah dosen pengampu RPS ini. Pastikan review tetap objektif." |
| EC-4 | Review timeout > 14 hari | Sistem mengirim email reminder ke Kaprodi; badge "Overdue" di daftar Review |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Kaprodi mereview RPS yang bukan dari prodi-nya | Tidak bisa mengakses; pesan: "Anda tidak memiliki akses ke RPS ini" |
| NS-2 | Kaprodi mengosongkan semua catatan dan langsung approve | Diizinkan (approve tanpa catatan); tetapi tombol "Setujui" dengan tooltip "Catatan review kosong — pastikan Anda sudah mereview dengan teliti" |
| NS-3 | Skor diberikan tetapi tidak ada catatan sama sekali | Warning: "Anda belum memberikan catatan review. Tetap lanjutkan?" |

---

## 11. Reviewer Meminta Revisi

**User Story ID:** WF-010

**User Story:**
> Sebagai **Kaprodi (Reviewer)**, saya ingin **meminta revisi RPS dengan memberikan alasan spesifik** agar **dosen dapat memperbaiki RPS sesuai standar yang diharapkan**.

### Acceptance Criteria

**AC-1: Minta Revisi dengan Alasan Wajib**
| | |
|---|---|
| **Given** | Kaprodi berada di form review, merasa RPS belum memenuhi standar |
| **When** | Kaprodi mengklik "Minta Revisi" tanpa mengisi alasan/catatan |
| **Then** | Sistem menampilkan pesan error: "Alasan revisi wajib diisi. Berikan catatan spesifik agar dosen mengetahui apa yang harus diperbaiki." |

**AC-2: Minta Revisi dengan Alasan Lengkap**
| | |
|---|---|
| **Given** | Kaprodi telah mengisi skor per komponen dan catatan spesifik di textarea "Alasan Revisi" |
| **When** | Kaprodi mengklik "Minta Revisi" |
| **Then** | Konfirmasi muncul: "RPS akan dikembalikan ke dosen untuk direvisi. Pastikan alasan revisi sudah jelas. Lanjutkan?" → "Ya" → status RPS berubah ke "Revision"; notifikasi ke Dosen: "RPS Anda perlu direvisi" + alasan revisi |

**AC-3: Dosen Melihat Alasan Revisi**
| | |
|---|---|
| **Given** | RPS berstatus "Revision" |
| **When** | Dosen membuka RPS tersebut |
| **Then** | Halaman detail RPS menampilkan banner/alert "RPS Perlu Direvisi" + semua catatan reviewer (per komponen + alasan revisi) terlihat jelas; komponen yang mendapat skor rendah di-highlight |

**AC-4: Audit Trail**
| | |
|---|---|
| **Given** | Kaprodi telah meminta revisi |
| **When** | Audit log mencatat aktivitas |
| **Then** | Log: rps_id, from_status="Review", to_status="Revision", actor_user_id=[Kaprodi], catatan=[alasan revisi], timestamp |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Kaprodi meminta revisi tanpa memberikan skor | Diperbolehkan (skor opsional saat revisi); tetapi alasan revisi tetap wajib |
| EC-2 | Dosen sudah mulai mengedit RPS (via "Mulai Revisi"), lalu Kaprodi mencoba akses | Status sudah berubah ke Draft; Kaprodi tidak bisa akses kecuali RPS diajukan lagi |
| EC-3 | Kaprodi meminta revisi > 1x untuk RPS yang sama | Diperbolehkan; setiap revisi tercatat sebagai iterasi terpisah di workflow history |
| EC-4 | Alasan revisi sangat panjang (> 2000 karakter) | Validasi: "Alasan revisi maksimal 2000 karakter"; counter karakter |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Kaprodi mengklik "Minta Revisi" tanpa membaca RPS | Sistem tidak bisa mendeteksi ini; bergantung pada integritas reviewer |
| NS-2 | RPS sudah di status "Revision" lalu Kaprodi mengklik "Minta Revisi" lagi | Tombol "Minta Revisi" disabled karena status sudah Revision |

---

## 12. Dosen Merevisi dan Mengajukan Ulang

**User Story ID:** WF-012

**User Story:**
> Sebagai **Dosen**, saya ingin **merevisi RPS sesuai masukan reviewer dan mengajukannya kembali** agar **RPS saya dapat disetujui**.

### Acceptance Criteria

**AC-1: Mulai Revisi**
| | |
|---|---|
| **Given** | Dosen membuka RPS dengan status "Revision" dan membaca catatan reviewer |
| **When** | Dosen mengklik tombol "Mulai Revisi" |
| **Then** | Status RPS berubah ke "Draft"; Dosen dapat mengedit seluruh step wizard; catatan reviewer tetap terlihat sebagai panel referensi di samping/bawah |

**AC-2: Edit RPS saat Revisi**
| | |
|---|---|
| **Given** | Dosen dalam mode revisi (status Draft) dengan catatan reviewer terlihat |
| **When** | Dosen melakukan perubahan pada step-step yang perlu direvisi |
| **Then** | Auto-save berfungsi normal; setiap perubahan tersimpan; catatan reviewer tetap statis (tidak berubah) sebagai referensi |

**AC-3: Panel Catatan Reviewer**
| | |
|---|---|
| **Given** | Dosen mengedit RPS dalam mode revisi |
| **When** | Dosen berada di step yang memiliki catatan reviewer |
| **Then** | Panel catatan menampilkan: komponen yang direview, skor reviewer, komentar reviewer; indikator "Sudah diperbaiki" yang dapat dicentang dosen untuk tracking mandiri |

**AC-4: Ajukan Ulang (Re-submit)**
| | |
|---|---|
| **Given** | Dosen telah selesai merevisi RPS |
| **When** | Dosen mengklik "Ajukan Ulang" dari Step 8 atau halaman detail |
| **Then** | Konfirmasi muncul; setelah dikonfirmasi → status berubah ke "Review"; versi baru dibuat (increment minor: v1.1); notifikasi ke reviewer: "RPS [nama MK] telah direvisi dan diajukan ulang" |

**AC-5: Reviewer Melihat Perubahan**
| | |
|---|---|
| **Given** | RPS diajukan ulang setelah revisi |
| **When** | Reviewer membuka RPS |
| **Then** | Reviewer melihat versi terbaru; badge "Revisi" terlihat; reviewer dapat melihat riwayat review sebelumnya; tombol "Lihat Perubahan" untuk diff viewer |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Dosen memulai revisi tetapi tidak jadi mengedit (klik "Mulai Revisi" lalu keluar) | Status sudah berubah ke Draft; dosen bisa lanjutkan edit kapan saja dari daftar RPS |
| EC-2 | Dosen submit revisi tanpa mengubah apapun (RPS sama persis) | Sistem mendeteksi tidak ada perubahan; warning: "Tidak terdeteksi perubahan pada RPS. Apakah Anda yakin ingin mengajukan ulang?" |
| EC-3 | Dosen mengabaikan catatan reviewer (tidak memperbaiki poin yang diminta) | Tidak ada deteksi otomatis; reviewer manusia yang menilai saat review ulang |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Dosen mengklik "Ajukan Ulang" tanpa menyelesaikan revisi | Validasi normal; jika ada step yang tidak valid → error, tidak bisa submit |
| NS-2 | Dosen mengedit RPS tanpa klik "Mulai Revisi" (status masih Revision) | Tidak bisa; semua field read-only; harus klik "Mulai Revisi" terlebih dahulu |

---

## 13. Kaprodi Menyetujui RPS

**User Story ID:** WF-009

**User Story:**
> Sebagai **Kaprodi**, saya ingin **menyetujui RPS yang sudah memenuhi standar** agar **RPS dapat dipublikasikan dan digunakan sebagai acuan pembelajaran**.

### Acceptance Criteria

**AC-1: Setujui RPS**
| | |
|---|---|
| **Given** | Kaprodi telah mereview RPS dan semua skor >= standar minimal (atau Kaprodi puas dengan kualitas) |
| **When** | Kaprodi mengklik "Setujui" di form review |
| **Then** | Konfirmasi: "Anda akan menyetujui RPS ini. RPS akan terkunci dan tidak dapat diedit oleh dosen. Lanjutkan?" → "Ya" → status berubah menjadi "Approved"; RPS terkunci (read-only untuk semua, kecuali Kaprodi dan Admin) |

**AC-2: Notifikasi ke Dosen**
| | |
|---|---|
| **Given** | RPS disetujui |
| **When** | Sistem mengirim notifikasi |
| **Then** | Email ke Dosen: subjek "Selamat! RPS [nama MK] Disetujui"; isi: status, nama reviewer, link ke RPS; in-app notification juga dikirim |

**AC-3: RPS Terkunci**
| | |
|---|---|
| **Given** | RPS berstatus "Approved" |
| **When** | Dosen mencoba mengedit RPS |
| **Then** | Semua form edit disabled; pesan: "RPS sudah disetujui. Hubungi Kaprodi jika perlu perubahan." |

**AC-4: RPS Siap Dipublikasi**
| | |
|---|---|
| **Given** | RPS berstatus "Approved" |
| **When** | Admin melihat daftar RPS |
| **Then** | RPS muncul di daftar "Siap Publikasi"; Admin dapat klik "Publikasikan" |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Kaprodi menyetujui tanpa memberikan skor review sama sekali | Diizinkan; approval adalah keputusan subjektif Kaprodi; namun disarankan skor diisi |
| EC-2 | Kaprodi menyetujui RPS yang masih memiliki warning AI Validator | Warning: "AI Validator menemukan [N] peringatan pada RPS ini. Tetap setujui?" |
| EC-3 | RPS sudah approved, lalu ada perubahan kurikulum | RPS tetap di status Approved; tidak otomatis berubah; Kaprodi bisa rollback dengan meminta revisi |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Kaprodi menyetujui RPS yang sudah disetujui sebelumnya | Tombol "Setujui" tidak tersedia; status sudah "Approved" |
| NS-2 | Kaprodi menyetujui RPS tanpa isi skor dan tanpa isi catatan | Warning: "Skor review masih kosong. Disarankan mengisi skor untuk dokumentasi mutu. Tetap setujui?" |

---

## 14. Mempublikasi RPS

**User Story ID:** WF-015

**User Story:**
> Sebagai **Admin Tenant**, saya ingin **mempublikasi RPS yang sudah disetujui** agar **RPS tersedia secara resmi dan dapat diakses oleh pihak yang berkepentingan**.

### Acceptance Criteria

**AC-1: Publikasi RPS**
| | |
|---|---|
| **Given** | Admin Tenant membuka RPS dengan status "Approved" |
| **When** | Admin mengklik "Publikasikan" |
| **Then** | Konfirmasi muncul: "RPS akan dipublikasikan dan tersedia untuk diakses. Lanjutkan?" → "Ya" → status berubah menjadi "Published"; label versi di-update menjadi "Versi Publik"; audit log tercatat |

**AC-2: Notifikasi ke Dosen**
| | |
|---|---|
| **Given** | RPS berhasil dipublikasi |
| **When** | Sistem mengirim notifikasi |
| **Then** | Email ke Dosen: subjek "RPS [nama MK] Telah Dipublikasi"; isi: informasi publikasi, link ke RPS published; in-app notification juga dikirim |

**AC-3: RPS Published Read-Only**
| | |
|---|---|
| **Given** | RPS berstatus "Published" |
| **When** | Siapapun mencoba mengedit RPS |
| **Then** | Semua form edit disabled; pesan: "RPS sudah dipublikasikan."; hanya tersedia tombol "Export" dan "Arsipkan" |

**AC-4: Daftar RPS Published**
| | |
|---|---|
| **Given** | Admin atau Kaprodi melihat daftar RPS |
| **When** | Memfilter status "Published" |
| **Then** | Tabel menampilkan semua RPS Published dengan informasi: MK, dosen, semester, tanggal publish; tombol "Export", "Arsipkan" |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Admin mempublikasi RPS tanpa sepengetahuan Kaprodi | Diizinkan (permission Admin); Kaprodi mendapat notifikasi |
| EC-2 | RPS dipublikasi, lalu Admin ingin mengarsipkan | Diizinkan; flow: Published → Archived |
| EC-3 | Export RPS Published — watermark tidak ada | Ya, RPS Published tidak memiliki watermark "DRAFT" |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Admin mencoba publikasi RPS yang belum Approved (masih Draft/Review) | Tombol "Publikasikan" tidak tersedia; pesan: "RPS harus disetujui terlebih dahulu" |
| NS-2 | Admin mencoba publikasi RPS yang sudah Published | Tombol "Publikasikan" tidak tersedia; label "Sudah Dipublikasikan" |

---

## 15. AI Generate CPMK

**User Story ID:** AI-004

**User Story:**
> Sebagai **Dosen**, saya ingin **menghasilkan CPMK otomatis dari CPL terpilih menggunakan AI** agar **dapat mempercepat proses penyusunan RPS**.

### Acceptance Criteria

**AC-1: Generate CPMK dengan AI**
| | |
|---|---|
| **Given** | Dosen berada di Step 3 (CPMK) dengan CPL yang sudah dipilih di Step 2 |
| **When** | Dosen mengklik tombol "Generate CPMK dengan AI" |
| **Then** | Sistem menampilkan loading spinner dengan teks "AI sedang menghasilkan CPMK..."; setelah selesai (maksimal 30 detik), daftar CPMK hasil generate ditampilkan |

**AC-2: Hasil Generate Dapat Diedit**
| | |
|---|---|
| **Given** | AI telah menghasilkan daftar CPMK |
| **When** | Dosen mereview hasil generate |
| **Then** | Setiap CPMK ditampilkan dalam form yang dapat diedit: kode (read-only), deskripsi (editable textarea), CPL terkait (editable multi-select); setiap CPMK memiliki badge "AI" sebagai indikator; Dosen dapat mengedit, menghapus, atau menambah CPMK baru |

**AC-3: Generate Ulang**
| | |
|---|---|
| **Given** | AI telah menghasilkan CPMK, tetapi Dosen tidak puas |
| **When** | Dosen mengklik "Generate Ulang" |
| **Then** | AI menghasilkan CPMK baru; hasil sebelumnya ditimpa (dengan konfirmasi: "Hasil generate sebelumnya akan ditimpa. Lanjutkan?") |

**AC-4: Simpan atau Batalkan**
| | |
|---|---|
| **Given** | Dosen telah mereview dan mengedit hasil generate AI |
| **When** | Dosen mengklik "Simpan" |
| **Then** | Semua CPMK (baik hasil AI maupun manual) tersimpan ke RPS; badge "AI" tetap pada CPMK yang berasal dari AI |
| **When** | Dosen mengklik "Batal" |
| **Then** | Hasil generate AI tidak disimpan; kembali ke state sebelum generate |

**AC-5: Rate Limiting AI**
| | |
|---|---|
| **Given** | Dosen telah menggunakan fitur AI Generate CPMK sebanyak 5 kali dalam 1 menit |
| **When** | Dosen mengklik "Generate CPMK dengan AI" lagi |
| **Then** | Sistem menampilkan pesan: "Anda telah mencapai batas penggunaan AI. Silakan coba lagi dalam [X] detik."; tombol disabled dengan countdown timer |

**AC-6: AI Disclaimer**
| | |
|---|---|
| **Given** | Hasil generate AI ditampilkan |
| **When** | Dosen melihat hasil |
| **Then** | Banner disclaimer muncul: "Konten ini dihasilkan oleh AI. Harap periksa kembali kesesuaian dan akurasinya sebelum disimpan." |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | CPL yang dipilih sangat sedikit (hanya 1) | AI tetap menghasilkan CPMK; jumlah CPMK mungkin lebih sedikit (1-4) |
| EC-2 | CPL yang dipilih sangat banyak (> 10) | AI menghasilkan CPMK dalam jumlah yang sesuai; kemungkinan 6-12 CPMK |
| EC-3 | AI timeout (> 30 detik) | Retry 2x; jika tetap gagal → pesan error: "Gagal menghasilkan CPMK. Silakan coba lagi atau masukkan secara manual." |
| EC-4 | AI menghasilkan CPMK dengan kode yang salah format | Sistem mem-parse dan memformat ulang kode menjadi CPMK-01, CPMK-02, dst |
| EC-5 | AI menghasilkan CPMK yang tidak terkait dengan CPL manapun | Setiap CPMK hasil generate selalu memiliki mapping CPL (dipaksa oleh prompt); jika AI gagal → gunakan fallback mapping |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Budget AI tenant habis | Pesan: "Kuota AI bulanan telah habis. Fitur AI akan tersedia kembali bulan depan atau hubungi Admin untuk upgrade." |
| NS-2 | OpenAI API key invalid atau expired | Pesan: "Layanan AI sedang tidak tersedia. Silakan coba beberapa saat lagi."; log error untuk admin |
| NS-3 | CPL belum dipilih (Step 2 kosong) | Tombol "Generate CPMK dengan AI" disabled; tooltip: "Pilih CPL terlebih dahulu di Step 2" |

---

## 16. AI Validasi RPS

**User Story ID:** AI-014

**User Story:**
> Sebagai **Dosen/Kaprodi**, saya ingin **memvalidasi RPS menggunakan AI Validator** agar **dapat mengetahui kualitas constructive alignment dan aspek lainnya sebelum submit atau approval**.

### Acceptance Criteria

**AC-1: Jalankan AI Validator**
| | |
|---|---|
| **Given** | Dosen atau Kaprodi membuka RPS di halaman detail atau wizard Step 8 |
| **When** | Pengguna mengklik tombol "Validasi dengan AI" |
| **Then** | Sistem menampilkan progress bar: "AI sedang memvalidasi RPS... Memeriksa [Aspek 1/8]"; setelah selesai, hasil validasi ditampilkan |

**AC-2: Hasil Validasi — 8 Aspek**
| | |
|---|---|
| **Given** | AI Validator selesai memproses RPS |
| **When** | Hasil validasi ditampilkan |
| **Then** | Panel hasil menampilkan: (a) Skor total (0-100) dengan indikator warna (hijau = PASS > 80, kuning = WARNING 60-80, merah = FAIL < 60), (b) 8 progress bar per aspek dengan skor dan status masing-masing, (c) Daftar temuan (jika ada) per aspek dengan detail lokasi, (d) Daftar rekomendasi perbaikan |

**AC-3: Navigasi dari Temuan ke Step Terkait**
| | |
|---|---|
| **Given** | Hasil validasi menampilkan temuan "Sub-CPMK-03 tidak memiliki assessment terkait" |
| **When** | Dosen mengklik temuan tersebut |
| **Then** | Sistem menavigasi ke Step 6 (Assessment) dan men-highlight Sub-CPMK-03; Dosen dapat langsung memperbaiki |

**AC-4: Validasi Ulang Setelah Perbaikan**
| | |
|---|---|
| **Given** | Dosen telah memperbaiki RPS berdasarkan hasil validasi AI |
| **When** | Dosen mengklik "Validasi Ulang" |
| **Then** | AI Validator berjalan kembali; hasil baru dibandingkan dengan hasil sebelumnya; jika skor meningkat, tampilkan badge "+X poin perbaikan" |

**AC-5: Cache Hasil Validasi**
| | |
|---|---|
| **Given** | AI Validator telah dijalankan untuk RPS versi tertentu |
| **When** | Pengguna mengklik "Validasi dengan AI" lagi tanpa mengubah RPS |
| **Then** | Sistem menampilkan hasil validasi yang di-cache (30 menit) dengan badge "Hasil Cache — [timestamp]" dan tombol "Validasi Ulang" untuk refresh |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | RPS masih kosong (hanya Step 1 terisi) | AI Validator menampilkan skor rendah (< 40) dengan temuan di hampir semua aspek; rekomendasi: "Lengkapi RPS terlebih dahulu sebelum validasi" |
| EC-2 | AI Validator menemukan 0 temuan (skor 100) | Tampilan khusus: badge "Sempurna" + confetti animation; tetap tampilkan semua aspek dengan skor |
| EC-3 | AI Validator timeout saat memproses aspek tertentu | Aspek yang sudah selesai ditampilkan; aspek gagal menampilkan "Gagal — Coba lagi"; tombol "Validasi Ulang" untuk retry |
| EC-4 | RPS menggunakan bahasa campuran (Indonesia + Inggris) | AI Validator tetap berfungsi; peringatan di aspek Konsistensi jika bahasa tidak seragam |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Budget AI tenant habis saat validasi | Pesan: "Kuota AI bulanan telah habis. Fitur AI Validator akan tersedia kembali bulan depan." |
| NS-2 | Proses validasi memakan waktu > 60 detik | Tampilkan progress real-time; jika > 60 detik → tawarkan untuk melanjutkan di background dengan notifikasi saat selesai |
| NS-3 | API error dengan response tidak valid | Pesan: "Validasi gagal — respons AI tidak valid. Tim teknis telah diberitahu. Silakan coba beberapa saat lagi." |

---

## 17. Export RPS ke Word

**User Story ID:** EXPORT-001

**User Story:**
> Sebagai **Dosen/Kaprodi**, saya ingin **mengekspor RPS ke format Word (.docx)** agar **dapat dicetak, ditandatangani, dan didistribusikan dalam format dokumen resmi**.

### Acceptance Criteria

**AC-1: Export Word**
| | |
|---|---|
| **Given** | Pengguna membuka RPS di halaman detail |
| **When** | Pengguna mengklik tombol "Export Word" |
| **Then** | Sistem menampilkan loading indicator "Menyiapkan dokumen..."; setelah selesai, file .docx otomatis terunduh ke browser |

**AC-2: Konten Lengkap**
| | |
|---|---|
| **Given** | File .docx berhasil terunduh |
| **When** | Pengguna membuka file di Microsoft Word atau Google Docs |
| **Then** | Dokumen berisi: (a) Cover dengan logo universitas, nama universitas, fakultas, prodi, (b) Identitas MK (kode, nama, SKS, semester, dosen pengampu), (c) CPL yang didukung, (d) Tabel CPMK dengan mapping CPL, (e) Tabel Sub-CPMK per pertemuan dengan level taksonomi, (f) Rencana Assessment dengan bobot, (g) Rencana Pembelajaran per pertemuan (minggu, Sub-CPMK, indikator, materi, metode, media, estimasi waktu), (h) Daftar Referensi format APA, (i) Halaman pengesahan dengan kolom tanda tangan |

**AC-3: Kop Surat dan Logo**
| | |
|---|---|
| **Given** | File .docx dibuka |
| **When** | Pengguna melihat header/cover dokumen |
| **Then** | Logo universitas muncul di kiri atas (jika ada); nama universitas, fakultas, program studi sesuai data master |

**AC-4: Watermark Status**
| | |
|---|---|
| **Given** | RPS berstatus "Draft" atau "Review" atau "Revision" |
| **When** | Dokumen diekspor dan dibuka |
| **Then** | Setiap halaman memiliki watermark diagonal bertuliskan "DRAFT" (untuk Draft), "DALAM REVIEW" (untuk Review), "REVISI" (untuk Revision); watermark berwarna abu-abu transparan |
| **Given** | RPS berstatus "Approved" atau "Published" |
| **When** | Dokumen diekspor |
| **Then** | Tidak ada watermark |

**AC-5: Format Sesuai SN-DIKTI**
| | |
|---|---|
| **Given** | Template default SN-DIKTI digunakan |
| **When** | Dokumen dibuka |
| **Then** | Format mengikuti standar: font Times New Roman 12pt, spasi 1.15, margin 2.5cm (atas/kiri) dan 2cm (kanan/bawah), tabel dengan border standar |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | RPS memiliki > 8 CPMK → tabel di dokumen melebar | Tabel menyesuaikan; rotasi teks/landscape untuk kolom banyak |
| EC-2 | Deskripsi Sub-CPMK sangat panjang | Kolom deskripsi melebar proporsional; tidak terpotong |
| EC-3 | Referensi > 20 item | Daftar referensi mengalir ke halaman berikutnya dengan page break yang rapi |
| EC-4 | Logo universitas belum diupload | Placeholder teks "[Logo Universitas]" muncul; tidak ada error |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Export dipicu saat server load tinggi | Export di-queue ke background job; tampilkan toast "Dokumen sedang disiapkan. Anda akan mendapat notifikasi saat file siap diunduh." |
| NS-2 | PHPWord library error (misal: corrupt template) | Pesan error: "Gagal mengekspor dokumen. Tim teknis telah diberitahu."; fallback ke export sederhana tanpa template |
| NS-3 | File terlalu besar (> 10 MB) | Tidak mungkin untuk RPS normal; jika terjadi → kompresi gambar; notifikasi jika > 5 MB |

---

## 18. Export RPS ke PDF

**User Story ID:** EXPORT-005

**User Story:**
> Sebagai **Dosen/Kaprodi**, saya ingin **mengekspor RPS ke format PDF** agar **dapat dibagikan dalam format yang tidak dapat diedit dan dibaca di berbagai perangkat**.

### Acceptance Criteria

**AC-1: Export PDF**
| | |
|---|---|
| **Given** | Pengguna membuka RPS di halaman detail |
| **When** | Pengguna mengklik tombol "Export PDF" |
| **Then** | Sistem menampilkan loading indicator; setelah selesai, file .pdf otomatis terunduh ke browser |

**AC-2: Konten Identik dengan Word**
| | |
|---|---|
| **Given** | Pengguna telah mengekspor RPS yang sama ke Word dan PDF |
| **When** | Pengguna membandingkan kedua file |
| **Then** | Konten, tabel, logo, dan format PDF identik dengan versi Word; tidak ada perbedaan layout yang signifikan |

**AC-3: Konversi yang Rapi**
| | |
|---|---|
| **Given** | File PDF dibuka |
| **When** | Pengguna memeriksa dokumen |
| **Then** | Tabel tidak terpotong di tengah baris; page break di tempat yang rapi; header/footer muncul di setiap halaman |

**AC-4: Watermark PDF**
| | |
|---|---|
| **Given** | RPS berstatus non-published |
| **When** | PDF dibuka |
| **Then** | Watermark "DRAFT" / "DALAM REVIEW" / "REVISI" muncul di setiap halaman (sama seperti di Word) |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | PDF dibuka di mobile (layar kecil) | PDF tetap terbaca; scroll horizontal untuk tabel lebar; tidak ada elemen yang hilang |
| EC-2 | Font tidak tersedia di server (Times New Roman) | Gunakan font fallback: DejaVu Serif; embed font di PDF |
| EC-3 | RPS memiliki tabel sangat panjang (melebihi 1 halaman) | Tabel di-split dengan header berulang di setiap halaman (repeat header row) |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | DomPDF memory limit exceeded | Export di-queue ke background job dengan memory limit lebih tinggi; toast: "Dokumen besar sedang diproses di background..." |
| NS-2 | Gambar logo corrupt atau tidak bisa dirender di PDF | Logo di-skip; teks "[Logo Universitas]" muncul sebagai fallback |
| NS-3 | Font karakter khusus tidak muncul di PDF (misal: simbol matematika) | Gunakan font dengan dukungan karakter Unicode penuh (DejaVu) |

---

## 19. Dashboard Kaprodi

**User Story ID:** DASH-005

**User Story:**
> Sebagai **Kaprodi**, saya ingin **melihat dashboard dengan statistik RPS di program studi saya** agar **dapat memantau progress penyusunan RPS dan mengambil tindakan yang diperlukan**.

### Acceptance Criteria

**AC-1: Statistik Counter**
| | |
|---|---|
| **Given** | Kaprodi membuka dashboard |
| **When** | Halaman dashboard dimuat |
| **Then** | Card counter menampilkan: Total RPS di prodi, Jumlah Draft, Jumlah Menunggu Review (Review), Jumlah Disetujui (Approved + Published); setiap card memiliki ikon dan warna berbeda; data real-time |

**AC-2: RPS Menunggu Review**
| | |
|---|---|
| **Given** | Kaprodi melihat dashboard |
| **When** | Kaprodi melihat tabel "RPS Menunggu Review" |
| **Then** | Tabel menampilkan: nama MK, nama Dosen, tanggal submit, lama menunggu (hari); maksimal 10 item; tombol "Review" di setiap baris; klik → langsung ke halaman review RPS tersebut |

**AC-3: Grafik RPS per Dosen**
| | |
|---|---|
| **Given** | Kaprodi melihat dashboard |
| **When** | Kaprodi melihat chart "RPS per Dosen" |
| **Then** | Bar chart horizontal: sumbu Y = nama dosen, sumbu X = jumlah RPS; hover menampilkan detail (Draft, Review, Approved); diurutkan dari dosen dengan RPS terbanyak |

**AC-4: Grafik Distribusi Status**
| | |
|---|---|
| **Given** | Kaprodi melihat dashboard |
| **When** | Kaprodi melihat chart distribusi status |
| **Then** | Donut chart: segmen Draft, Review, Revision, Approved, Published; hover menampilkan jumlah dan persentase |

**AC-5: Aktivitas Terbaru**
| | |
|---|---|
| **Given** | Kaprodi melihat dashboard |
| **When** | Kaprodi melihat feed "Aktivitas Terbaru" |
| **Then** | Timeline list menampilkan 10 aktivitas terbaru: "[Dosen] mengajukan RPS [nama MK] untuk review — [timestamp]", "[Dosen] merevisi RPS [nama MK] — [timestamp]"; klik → navigasi ke RPS terkait |

**AC-6: Quick Action**
| | |
|---|---|
| **Given** | Kaprodi melihat dashboard |
| **When** | Kaprodi melihat area Quick Action |
| **Then** | Tombol prominent "Buat RPS Baru" (langsung ke wizard); tombol "Lihat Semua RPS" (ke halaman daftar RPS); tombol "Lihat Semua Review" (ke halaman review) |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Prodi belum memiliki RPS sama sekali | Card counter menampilkan 0 semua; chart kosong dengan ilustrasi "Belum ada data RPS" |
| EC-2 | Prodi memiliki > 50 dosen | Chart "RPS per Dosen" menampilkan top 20; sisanya digabung di "Lainnya" |
| EC-3 | Dashboard diakses via mobile | Card counter stack vertikal; chart menjadi lebih kecil; tabel menjadi card-based list |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Query dashboard terlalu lambat (> 3 detik) | Dashboard menggunakan cache (5 menit); badge "Data diperbarui [timestamp]" |
| NS-2 | Kaprodi mengakses dashboard prodi lain (bukan prodi-nya) | Data yang ditampilkan hanya prodi Kaprodi yang bersangkutan; tidak bisa melihat prodi lain |
| NS-3 | Tidak ada Kaprodi terdaftar untuk prodi | Fitur Kaprodi tidak muncul (tidak bisa akses dashboard) |

---

## 20. Notifikasi saat Status RPS Berubah

**User Story ID:** NOTIF-001

**User Story:**
> Sebagai **pengguna**, saya ingin **menerima notifikasi saat status RPS saya berubah** agar **dapat segera mengetahui update dan mengambil tindakan yang diperlukan**.

### Acceptance Criteria

**AC-1: Notifikasi Email — RPS Diajukan untuk Review**
| | |
|---|---|
| **Given** | Dosen mengajukan RPS untuk review (status Draft → Review) |
| **When** | Sistem memproses notifikasi |
| **Then** | Kaprodi menerima email: subjek "RPS [nama MK] Siap Direview", isi "Dosen [nama dosen] telah mengajukan RPS [nama MK] untuk direview. [Link ke RPS]"; email dikirim dalam waktu < 2 menit setelah status berubah |

**AC-2: Notifikasi In-App — RPS Direview**
| | |
|---|---|
| **Given** | Kaprodi menyelesaikan review RPS (approve atau revisi) |
| **When** | Sistem memproses notifikasi |
| **Then** | Dosen menerima notifikasi in-app di bell icon: badge counter bertambah; klik bell → dropdown menampilkan "RPS [nama MK] [Disetujui/Perlu Revisi]"; klik notifikasi → navigasi ke halaman detail RPS |

**AC-3: Mark as Read**
| | |
|---|---|
| **Given** | Dosen memiliki 3 notifikasi belum dibaca |
| **When** | Dosen mengklik salah satu notifikasi |
| **Then** | Notifikasi yang diklik otomatis ditandai "read" (read_at terisi); badge counter berkurang 1 |
| **When** | Dosen mengklik "Tandai Semua Telah Dibaca" |
| **Then** | Semua notifikasi ditandai read; badge counter menjadi 0 |

**AC-4: Notifikasi Wajib Tidak Bisa Dimatikan**
| | |
|---|---|
| **Given** | Pengguna berada di halaman Preferensi Notifikasi |
| **When** | Pengguna melihat toggle untuk notifikasi "Status RPS Berubah" |
| **Then** | Toggle dalam posisi ON dan disabled (tidak bisa dimatikan); tooltip: "Notifikasi wajib — tidak dapat dinonaktifkan" |

**AC-5: Riwayat Notifikasi**
| | |
|---|---|
| **Given** | Pengguna mengklik "Lihat Semua" di dropdown notifikasi |
| **When** | Halaman Notification Center terbuka |
| **Then** | Daftar semua notifikasi dengan pagination; filter by type; urutkan terbaru dulu; notifikasi lama (> 30 hari) otomatis dihapus |

### Edge Cases

| ID | Edge Case | Expected Behavior |
|----|-----------|-------------------|
| EC-1 | Pengguna menerima > 5 notifikasi dalam 1 jam | Sistem mengirim digest: "Anda memiliki [N] notifikasi baru untuk [daftar MK]" alih-alih email satu per satu |
| EC-2 | Email notifikasi gagal terkirim (SMTP error) | Sistem mencatat log error; retry 3x; jika tetap gagal → notifikasi in-app tetap tersimpan; admin mendapat alert email failure |
| EC-3 | Pengguna tidak login selama 2 minggu | Notifikasi in-app terakumulasi; saat login kembali, badge menampilkan jumlah total unread |
| EC-4 | Notifikasi untuk reviewer yang sudah tidak aktif | Notifikasi tetap dibuat; jika reviewer tidak aktif, Kaprodi mendapat notifikasi pengganti |

### Negative Scenarios

| ID | Scenario | Expected Behavior |
|----|----------|-------------------|
| NS-1 | Email notifikasi masuk spam | Gunakan SMTP terotentikasi; DKIM/SPF/DMARC di-setup; domain verified |
| NS-2 | Template email error (variabel tidak ditemukan) | Fallback: gunakan template minimalis; admin mendapat alert template error |
| NS-3 | Tabel notifikasi database penuh (jutaan record) | Scheduled job menghapus notifikasi > 90 hari; monitoring jumlah notifikasi |

---

**Navigasi:** [Sebelumnya: Sprint Planning](43-sprint-planning.md) | [Daftar Isi](../README.md) | [Berikutnya: Success Metrics](45-success-metrics.md)
