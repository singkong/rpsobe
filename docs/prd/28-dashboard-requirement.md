# 28 — Dashboard Requirement

## Ikhtisar

Dashboard RPS OBE dirancang sebagai halaman pertama yang dilihat pengguna setelah login. Setiap peran pengguna memiliki konfigurasi dashboard yang berbeda, menampilkan widget dan data yang relevan dengan tanggung jawab dan keputusan yang perlu diambil. Dashboard mengadopsi prinsip **actionable analytics** — setiap informasi mengarahkan pengguna pada aksi konkret.

---

## Prinsip Desain Dashboard

| Prinsip | Deskripsi |
|---------|-----------|
| **At-a-Glance** | Informasi kunci terlihat tanpa scroll pada resolusi 1366x768 |
| **Actionable** | Setiap widget terkait dengan aksi yang dapat dilakukan |
| **Contextual** | Data ditampilkan sesuai scope peran dan tenant |
| **Real-time** | Data diperbarui secara berkala via Livewire polling |
| **Consistent** | Pola layout, warna, dan interaksi seragam antar peran |

---

## Layout Grid System

Dashboard menggunakan grid 12 kolom (Tabler UI grid) dengan breakpoint responsif:

| Breakpoint | Kolom Per Baris | Widget Width Default |
|------------|------------------|---------------------|
| >= 1200px (xl) | 12 kolom | Card 3 kolom, Chart 6 kolom, Table 12 kolom |
| 992-1199px (lg) | 12 kolom | Card 4 kolom, Chart 6 kolom, Table 12 kolom |
| 768-991px (md) | 12 kolom | Card 6 kolom, Chart 12 kolom, Table 12 kolom |
| < 768px (sm) | 12 kolom | Semua widget full-width 12 kolom |

---

## 1. Dashboard Dosen

Dashboard Dosen berfokus pada daftar RPS yang sedang dikerjakan dan aksi cepat untuk melanjutkan pekerjaan.

### Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│  Selamat Datang, Dr. Ahmad Fauzi                                    │
│  Program Studi Informatika · Semester Ganjil 2025/2026              │
├──────────┬──────────┬──────────┬────────────────────────────────────┤
│ Total    │ Disetujui│ Draft    │ Quick Actions                      │
│ RPS: 5   │ 3        │ 2        │ [+ Buat RPS Baru] [Lanjutkan Draft]│
├──────────┴──────────┴──────────┴────────────────────────────────────┤
│                                                                      │
│  📋 RPS Saya                                         [Lihat Semua →] │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ IF2120 Algoritma Pemrograman         🟡 Draft      60% ████░░ │ │
│  │ 3 SKS · Semester Ganjil 2025/2026              [Lanjutkan →]  │ │
│  ├────────────────────────────────────────────────────────────────┤ │
│  │ IF3110 Pemrograman Web               🔵 Review     95% █████░ │ │
│  │ 4 SKS · Semester Ganjil 2025/2026              [Lihat →]      │ │
│  ├────────────────────────────────────────────────────────────────┤ │
│  │ IF2123 Struktur Data                 🟢 Approved  100% ██████  │ │
│  │ 3 SKS · Semester Genap 2024/2025               [Lihat →]      │ │
│  ├────────────────────────────────────────────────────────────────┤ │
│  │ IF3230 Kecerdasan Buatan             🔴 Rejected   85% █████░ │ │
│  │ 3 SKS · Semester Ganjil 2025/2026           [Perbaiki →]      │ │
│  ├────────────────────────────────────────────────────────────────┤ │
│  │ IF4010 Machine Learning              🟢 Published 100% ██████  │ │
│  │ 3 SKS · Semester Genap 2024/2025               [Lihat →]      │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                      │
│  🕐 Aktivitas Terbaru                                                │
│  ┌────────────────────────────────────────────────────────────────┐ │
│  │ 10 menit lalu  ✏️ Mengedit CPMK pada RPS Algoritma Pemrograman  │ │
│  │ 2 jam lalu     📤 Submit RPS Pemrograman Web untuk review       │ │
│  │ Kemarin        ✅ RPS Struktur Data disetujui Kaprodi           │ │
│  │ 2 hari lalu    🤖 AI Assistant membantu penyusunan assessment   │ │
│  │ 3 hari lalu    📝 Membuat RPS baru: Kecerdasan Buatan           │ │
│  └────────────────────────────────────────────────────────────────┘ │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### Widget Spesifikasi

#### a. Stats Cards

| Kartu | Data | Sumber Query |
|-------|------|-------------|
| Total RPS | `count(rps WHERE dosen_id = auth()->id())` | `rps` table |
| Disetujui | `count(rps WHERE status = 'approved')` | `rps` table |
| Draft | `count(rps WHERE status = 'draft')` | `rps` table |

#### b. RPS Card List

Setiap item RPS ditampilkan sebagai card horizontal:

| Elemen | Deskripsi |
|--------|-----------|
| **Kode & Nama MK** | Kode mata kuliah + nama lengkap |
| **SKS & Semester** | Jumlah SKS dan periode semester |
| **Status Badge** | Draft (kuning), Review (biru), Approved (hijau), Rejected (merah), Published (ungu) |
| **Progress Bar** | Persentase kelengkapan RPS (dihitung dari field yang terisi / total field wajib) |
| **CTA Button** | Kontekstual: "Lanjutkan" untuk draft, "Lihat" untuk status lain, "Perbaiki" untuk rejected |

#### c. Quick Actions

| Tombol | Aksi | Route |
|--------|------|-------|
| Buat RPS Baru | Redirect ke form pembuatan RPS | `rps.create` |
| Lanjutkan Draft | Redirect ke draft terakhir yang diedit | `rps.edit` (draft terakhir) |

#### d. Recent Activity

Menampilkan 5 aktivitas terakhir pengguna:

| Data | Sumber |
|------|--------|
| Timestamp relatif | `created_at` pada `audit_logs` |
| Ikon aksi | Berdasarkan tipe log |
| Deskripsi aktivitas | `description` pada `audit_logs` |

---

## 2. Dashboard Kaprodi

Dashboard Kaprodi berfokus pada monitoring status RPS program studi dan pengambilan keputusan approval.

### Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│  Dashboard Kaprodi — Program Studi Informatika                      │
│  Semester Ganjil 2025/2026                                          │
├──────────┬──────────┬──────────┬──────────┬─────────────────────────┤
│ Total    │ Menunggu │ Dalam    │ Disetujui│ Rata-rata Completion    │
│ RPS      │ Review   │ Review   │          │ Rate: 72%               │
│ 24       │ 5        │ 3        │ 16       │                         │
├──────────┴──────────┴──────────┴──────────┴─────────────────────────┤
│                                                                      │
│ 📊 Distribusi Status RPS              📈 Tren Penyelesaian RPS       │
│ ┌────────────────────────────┐        ┌────────────────────────────┐ │
│ │       (Pie Chart)          │        │    (Line Chart)            │ │
│ │    🟢 Approved: 66.7%      │        │  25 ┤          ╭───        │ │
│ │    🔵 Review: 12.5%        │        │  20 ┤      ╭───╯           │ │
│ │    🟡 Draft: 8.3%          │        │  15 ┤  ╭───╯               │ │
│ │    🟠 Pending: 8.3%        │        │  10 ┤──╯                   │ │
│ │    🔴 Rejected: 4.2%       │        │   5 ┤                      │ │
│ └────────────────────────────┘        │     └──┬──┬──┬──┬──┬──┬──  │ │
│                                       │     Agt Sep Okt Nov Des Jan │ │
│                                       └────────────────────────────┘ │
│                                                                      │
│ 📋 RPS Menunggu Approval                      [Lihat Semua →]        │
│ ┌──────────────────────────────────────────────────────────────────┐ │
│ │ # │ Kode MK    │ Nama Mata Kuliah      │ Dosen         │ Aksi    │ │
│ │───│────────────│───────────────────────│───────────────│─────────│ │
│ │ 1 │ IF3110     │ Pemrograman Web       │ Dr. Ahmad F.  │ Review  │ │
│ │ 2 │ IF2120     │ Algoritma Pemrograman │ Budi S., M.K. │ Review  │ │
│ │ 3 │ IF3230     │ Kecerdasan Buatan     │ Citra D., MT. │ Review  │ │
│ │ 4 │ IF4020     │ Data Mining           │ Dr. Ahmad F.  │ Review  │ │
│ │ 5 │ IF2150     │ Jaringan Komputer     │ Eko P., M.K.  │ Review  │ │
│ └──────────────────────────────────────────────────────────────────┘ │
│                                                                      │
│ 👥 Beban Kerja Reviewer                                              │
│ ┌──────────────────────────────────────────────────────────────────┐ │
│ │ Reviewer          │ Aktif │ Selesai │ Total │ Progress Bar        │ │
│ │───────────────────│───────│─────────│───────│─────────────────────│ │
│ │ Dr. Rina Wijaya   │ 2     │ 8       │ 10    │ ████████░░ 80%      │ │
│ │ Prof. Budi Santoso│ 3     │ 5       │ 8     │ ██████░░░░ 62%      │ │
│ │ Dr. Dedi Kusuma   │ 1     │ 12      │ 13    │ █████████░ 92%      │ │
│ └──────────────────────────────────────────────────────────────────┘ │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

### Widget Spesifikasi

#### a. Stats Cards

| Kartu | Data | Warna |
|-------|------|-------|
| Total RPS | Jumlah seluruh RPS dalam prodi | Biru |
| Menunggu Review | RPS status `submitted` (belum ditugaskan reviewer) | Oranye |
| Dalam Review | RPS status `in_review` | Kuning |
| Disetujui | RPS status `approved` + `published` | Hijau |
| Completion Rate | Rata-rata persentase kelengkapan seluruh RPS | Ungu |

#### b. Pie Chart — Distribusi Status RPS

| Properti | Nilai |
|----------|-------|
| **Library** | Chart.js (via Laravel Chart.js package atau inline) |
| **Tipe** | Doughnut / Pie |
| **Data** | `groupBy('status')` pada RPS dalam prodi |
| **Warna** | Approved: `#2e7d32`, Review: `#1565c0`, Draft: `#f9a825`, Pending: `#ef6c00`, Rejected: `#c62828` |
| **Label** | Status + jumlah + persentase |
| **Interaksi** | Klik segmen filter tabel RPS berdasarkan status |

#### c. Line Chart — Tren Penyelesaian RPS

| Properti | Nilai |
|----------|-------|
| **Library** | Chart.js |
| **Tipe** | Line chart |
| **Sumbu X** | Bulan (6 bulan terakhir) |
| **Sumbu Y** | Jumlah RPS kumulatif yang mencapai status approved |
| **Data** | `count(rps WHERE status = 'approved' GROUP BY MONTH(approved_at))` |
| **Interaksi** | Tooltip menampilkan bulan + jumlah |

#### d. Table — RPS Menunggu Approval

| Kolom | Deskripsi |
|-------|-----------|
| # | Nomor urut |
| Kode MK | Kode mata kuliah |
| Nama Mata Kuliah | Nama lengkap MK |
| Dosen | Nama dosen pengusul |
| Tanggal Submit | Tanggal RPS disubmit |
| Aksi | Tombol "Review" menuju halaman review |

#### e. Reviewer Workload

| Kolom | Deskripsi |
|-------|-----------|
| Reviewer | Nama reviewer |
| Aktif | Jumlah tugas review yang belum selesai |
| Selesai | Jumlah tugas review yang sudah selesai |
| Total | Total seluruh tugas |
| Progress Bar | Persentase selesai dari total |

---

## 3. Dashboard Admin Fakultas

Dashboard Admin Fakultas menampilkan ringkasan per program studi dalam satu fakultas.

### Layout

```
┌──────────────────────────────────────────────────────────────────────┐
│  Dashboard Admin — Fakultas Ilmu Komputer                            │
│  Universitas Teknologi Nusantara                                     │
├──────────┬──────────┬──────────┬──────────┬──────────────────────────┤
│ Total    │ Total    │ Total    │ Rata-rata│ Prodi Terbaik            │
│ Prodi: 4 │ RPS: 96  │ Dosen:38 │ Align:82%│ Informatika (89%)        │
├──────────┴──────────┴──────────┴──────────┴──────────────────────────┤
│                                                                       │
│ 📊 Ringkasan per Program Studi                                        │
│ ┌───────────────────────────────────────────────────────────────────┐ │
│ │ Prodi          │ Total RPS │ Approved │ Draft │ Align Score │ %   │ │
│ │────────────────│───────────│──────────│───────│─────────────│─────│ │
│ │ Informatika    │ 24        │ 16       │ 2     │ 89%         │ ██▌ │ │
│ │ Sistem Inform. │ 22        │ 14       │ 3     │ 85%         │ ██▌ │ │
│ │ Teknik Komputer│ 28        │ 20       │ 1     │ 78%         │ ██  │ │
│ │ Data Science   │ 22        │ 10       │ 5     │ 76%         │ ██  │ │
│ └───────────────────────────────────────────────────────────────────┘ │
│                                                                       │
│ 📈 Completion Rate per Prodi           📊 Alignment Score Distribution│
│ ┌────────────────────────────────┐     ┌────────────────────────────┐ │
│ │     (Horizontal Bar Chart)     │     │     (Radar Chart)          │ │
│ │  Informatika    ████████░ 80%  │     │         CPL 1              │ │
│ │  Sistem Inform. ████████░ 75%  │     │           ╱╲               │ │
│ │  Teknik Komp.   ████████░ 72%  │     │          ╱  ╲              │ │
│ │  Data Science   ██████░░░ 60%  │     │    CPL 6╱    ╲CPL 2        │ │
│ └────────────────────────────────┘     │        ╱   ◆  ╲            │ │
│                                        │       ╱  ╱    ╲ ╲          │ │
│                                        │   CPL 5──────CPL 3          │ │
│                                        │        ╲      ╱            │ │
│                                        │         ╲    ╱             │ │
│                                        │          CPL 4             │ │
│                                        └────────────────────────────┘ │
│                                                                       │
│ 📋 RPS Terbaru Disubmit                                               │
│ ┌───────────────────────────────────────────────────────────────────┐ │
│ │ Tanggal    │ Kode MK  │ Nama MK              │ Prodi      │ Status │ │
│ │────────────│──────────│──────────────────────│────────────│────────│ │
│ │ 01-08-2026 │ IF4020   │ Data Mining          │ Informatika│ Review │ │
│ │ 31-07-2026 │ SI3120   │ Manajemen Proyek TI  │ Sist. Inf. │ Review │ │
│ │ 30-07-2026 │ TK2210   │ Arsitektur Komputer  │ Tek. Komp. │ Review │ │
│ └───────────────────────────────────────────────────────────────────┘ │
│                                                                       │
└──────────────────────────────────────────────────────────────────────┘
```

### Widget Spesifikasi

#### a. Stats Cards

| Kartu | Data |
|-------|------|
| Total Prodi | `count(program_studi WHERE fakultas_id)` |
| Total RPS | `count(rps JOIN mata_kuliah JOIN program_studi WHERE fakultas_id)` |
| Total Dosen | `count(dosen WHERE prodi IN fakultas)` |
| Rata-rata Alignment | AVG dari seluruh alignment score RPS |
| Prodi Terbaik | Prodi dengan alignment score rata-rata tertinggi |

#### b. Table — Ringkasan per Prodi

| Kolom | Deskripsi |
|-------|-----------|
| Prodi | Nama program studi |
| Total RPS | Jumlah RPS dalam prodi |
| Approved | Jumlah RPS disetujui |
| Draft | Jumlah RPS masih draft |
| Align Score | Rata-rata skor keselarasan CPL-CPMK |
| % Bar | Visual progress bar |

#### c. Horizontal Bar Chart — Completion Rate per Prodi

| Properti | Nilai |
|----------|-------|
| **Library** | Chart.js |
| **Tipe** | Horizontal bar chart |
| **Data** | Persentase RPS approved vs total per prodi |
| **Warna** | Gradient hijau berdasarkan nilai |

#### d. Radar Chart — Alignment Score Distribution

| Properti | Nilai |
|----------|-------|
| **Library** | Chart.js |
| **Tipe** | Radar chart |
| **Data** | Rata-rata persentase RPS yang mencakup setiap CPL |
| **Sumbu** | CPL-1 sampai CPL-N |
| **Interaksi** | Tooltip menampilkan nama CPL + persentase |

---

## 4. Dashboard Admin Universitas

Dashboard Admin Universitas memberikan gambaran menyeluruh seluruh fakultas.

### Layout

```
┌──────────────────────────────────────────────────────────────────────┐
│  Dashboard — Universitas Teknologi Nusantara                         │
│  Tahun Akademik 2025/2026                                            │
├──────────┬──────────┬──────────┬──────────┬──────────────────────────┤
│ Fakultas │ Prodi    │ Dosen    │ RPS      │ Rata-rata Completion     │
│ 6        │ 24       │ 156      │ 512      │ 74%                      │
├──────────┴──────────┴──────────┴──────────┴──────────────────────────┤
│                                                                       │
│ 📊 Overview per Fakultas                                              │
│ ┌───────────────────────────────────────────────────────────────────┐ │
│ │ Fakultas             │ Prodi │ RPS │ Approved │ Align │ Comp %    │ │
│ │──────────────────────│───────│─────│──────────│───────│───────────│ │
│ │ Ilmu Komputer        │ 4     │ 96  │ 60       │ 82%   │ ██████▌ 65%│ │
│ │ Ekonomi & Bisnis     │ 3     │ 72  │ 48       │ 78%   │ ██████▌ 67%│ │
│ │ Teknik               │ 5     │ 120 │ 72       │ 75%   │ ██████░ 60%│ │
│ │ Kedokteran           │ 2     │ 48  │ 30       │ 85%   │ ██████▌ 62%│ │
│ │ Hukum                │ 1     │ 24  │ 18       │ 70%   │ ███████ 75%│ │
│ │ Psikologi            │ 1     │ 24  │ 12       │ 68%   │ █████░░ 50%│ │
│ └───────────────────────────────────────────────────────────────────┘ │
│                                                                       │
│ 📈 Tren Completion Rate Universitas     📊 Distribusi Status RPS      │
│ ┌─────────────────────────────────┐     ┌───────────────────────────┐ │
│ │      (Line Chart)               │     │      (Pie Chart)          │ │
│ │  100%┤               ╭────      │     │   🟢 Approved: 58%        │ │
│ │   80%┤          ╭────╯          │     │   🔵 Review: 15%          │ │
│ │   60%┤      ╭───╯               │     │   🟡 Draft: 18%           │ │
│ │   40%┤  ╭───╯                   │     │   🔴 Rejected: 9%         │ │
│ │   20%┤──╯                       │     │                           │ │
│ │      └──┬──┬──┬──┬──┬──┬──      │     │                           │ │
│ │      Agt Sep Okt Nov Des Jan    │     │                           │ │
│ └─────────────────────────────────┘     └───────────────────────────┘ │
│                                                                       │
└──────────────────────────────────────────────────────────────────────┘
```

### Widget Spesifikasi

#### a. Aggregate Stats Cards

| Kartu | Sumber |
|-------|--------|
| Fakultas | `count(fakultas WHERE universitas_id)` |
| Prodi | `count(program_studi WHERE fakultas.universitas_id)` |
| Dosen | `count(dosen WHERE prodi.fakultas.universitas_id)` |
| RPS | `count(rps WHERE prodi.fakultas.universitas_id)` |
| Completion Rate | Rata-rata seluruh prodi |

#### b. Table — Overview per Fakultas

| Kolom | Deskripsi |
|-------|-----------|
| Fakultas | Nama fakultas |
| Prodi | Jumlah program studi |
| RPS | Total RPS |
| Approved | Jumlah RPS disetujui |
| Align | Rata-rata alignment score |
| Comp % | Progress bar completion rate |

---

## 5. Dashboard LPM

Dashboard LPM berfokus pada monitoring mutu, penjaminan kualitas, dan kesiapan audit.

### Layout

```
┌──────────────────────────────────────────────────────────────────────┐
│  Dashboard LPM — Penjaminan Mutu                                     │
│  Universitas Teknologi Nusantara                                     │
├──────────┬──────────┬──────────┬──────────┬──────────────────────────┤
│ Skor     │ Align    │ RPS      │ Audit    │ Semester Lalu vs         │
│ Mutu: 78 │ Rata: 82%│ Audit OK │ Ready:   │ Sekarang                  │
│          │          │ 68%      │ 72%      │ ▲ +5%                    │
├──────────┴──────────┴──────────┴──────────┴──────────────────────────┤
│                                                                       │
│ 📊 Skor Mutu per Prodi (Bar Chart)      📈 Perbandingan Semester      │
│ ┌─────────────────────────────────┐     ┌───────────────────────────┐ │
│ │ Informatika      ██████████ 88  │     │ Indikator    Gnp24  Gnp25  │ │
│ │ Sistem Informasi █████████░ 82  │     │───────────────────────────│ │
│ │ Teknik Komputer  ████████░░ 75  │     │ Skor Mutu    72     78    │ │
│ │ Data Science     ███████░░░ 68  │     │ Alignment    78%    82%   │ │
│ │ Ekonomi          ████████░░ 76  │     │ Audit Ready  65%    72%   │ │
│ └─────────────────────────────────┘     │ Completion   60%    68%   │ │
│                                         └───────────────────────────┘ │
│                                                                       │
│ 📋 Indikator Audit Readiness                                          │
│ ┌───────────────────────────────────────────────────────────────────┐ │
│ │ Indikator                        │ Target │ Capaian │ Status      │ │
│ │──────────────────────────────────│────────│─────────│─────────────│ │
│ │ Kelengkapan RPS (semester aktif) │ 100%   │ 68%     │ ⚠ Perlu     │ │
│ │ CPL tercakup dalam RPS           │ 100%   │ 82%     │ ⚠ Perlu     │ │
│ │ Assessment sesuai CPMK           │ 90%    │ 78%     │ ⚠ Perlu     │ │
│ │ Referensi mutakhir (<5 thn)      │ 80%    │ 85%     │ ✅ Baik     │ │
│ │ RPS disetujui Kaprodi            │ 100%   │ 72%     │ ⚠ Perlu     │ │
│ └───────────────────────────────────────────────────────────────────┘ │
│                                                                       │
└──────────────────────────────────────────────────────────────────────┘
```

### Widget Spesifikasi

#### a. Stats Cards

| Kartu | Data | Deskripsi |
|-------|------|-----------|
| Skor Mutu | Rata-rata quality score seluruh RPS | Skala 0-100 |
| Alignment Rata-rata | Rata-rata alignment score CPL-CPMK | Persentase |
| RPS Audit OK | Persentase RPS yang memenuhi standar audit | Minimal skor >70 |
| Audit Ready | Persentase prodi yang siap audit | Semua indikator terpenuhi |

#### b. Bar Chart — Skor Mutu per Prodi

| Properti | Nilai |
|----------|-------|
| **Library** | Chart.js |
| **Tipe** | Horizontal bar chart |
| **Data** | AVG(quality_score) GROUP BY prodi |
| **Threshold Line** | Garis vertikal pada skor 70 (batas minimal audit) |
| **Warna** | Hijau (>80), Kuning (70-80), Merah (<70) |

#### c. Comparison Table — Semester Comparison

| Kolom | Deskripsi |
|-------|-----------|
| Indikator | Nama indikator mutu |
| Semester Lalu | Nilai semester sebelumnya |
| Semester Ini | Nilai semester berjalan |
| Delta | Perubahan (▲ naik / ▼ turun) |

#### d. Audit Readiness Indicators

| Kolom | Deskripsi |
|-------|-----------|
| Indikator | Nama indikator audit |
| Target | Nilai target minimal |
| Capaian | Nilai capaian saat ini |
| Status | ✅ Baik / ⚠ Perlu Perbaikan / 🔴 Kritis |

---

## 6. Dashboard Super Admin

Dashboard Super Admin memberikan gambaran global seluruh platform dan tenant.

### Layout

```
┌──────────────────────────────────────────────────────────────────────┐
│  Dashboard Super Admin — Platform Overview                           │
├──────────┬──────────┬──────────┬──────────┬──────────────────────────┤
│ Tenant   │ Pengguna │ RPS      │ AI Call  │ System Health            │
│ Aktif: 12│ Aktif:   │ Total:   │ Bulan Ini│ 🟢 All Systems           │
│          │ 1,245    │ 3,840    │ 12,450   │ Operational              │
├──────────┴──────────┴──────────┴──────────┴──────────────────────────┤
│                                                                       │
│ 📊 Tenant Overview                                                    │
│ ┌───────────────────────────────────────────────────────────────────┐ │
│ │ Tenant                    │ Paket    │ Users │ RPS │ Status       │ │
│ │───────────────────────────│──────────│───────│─────│──────────────│ │
│ │ Univ. Teknologi Nusantara │ Premium  │ 245   │ 512 │ 🟢 Aktif     │ │
│ │ Univ. Pendidikan Mandiri  │ Standard │ 180   │ 340 │ 🟢 Aktif     │ │
│ │ Institut Bisnis Global    │ Premium  │ 120   │ 290 │ 🟢 Aktif     │ │
│ │ Politeknik Cendekia       │ Basic    │ 85    │ 150 │ 🟡 Trial     │ │
│ │ Akademi Keperawatan Bina  │ Basic    │ 45    │ 60  │ 🔴 Inaktif   │ │
│ └───────────────────────────────────────────────────────────────────┘ │
│                                                                       │
│ 📈 Pertumbuhan Pengguna           📊 Distribusi Paket Tenant          │
│ ┌────────────────────────────┐     ┌───────────────────────────────┐ │
│ │      (Line Chart)          │     │        (Pie Chart)             │ │
│ │  1,500┤              ╭────  │     │   🟣 Premium: 4 tenant       │ │
│ │  1,200┤          ╭───╯      │     │   🔵 Standard: 5 tenant      │ │
│ │    900┤      ╭───╯          │     │   🟢 Basic: 3 tenant         │ │
│ │    600┤  ╭───╯              │     │                               │ │
│ │    300┤──╯                  │     │                               │ │
│ │       └──┬──┬──┬──┬──┬──     │     │                               │ │
│ │       Jan Feb Mar Apr Mei Jun│     │                               │ │
│ └────────────────────────────┘     └───────────────────────────────┘ │
│                                                                       │
│ 🖥 System Health                                                      │
│ ┌───────────────────────────────────────────────────────────────────┐ │
│ │ Komponen        │ Status │ Detail                                 │ │
│ │─────────────────│────────│────────────────────────────────────────│ │
│ │ Web Server      │ 🟢     │ Uptime 99.9%, Response 120ms           │ │
│ │ Database        │ 🟢     │ Connections 12/50, Replication OK      │ │
│ │ Redis Cache     │ 🟢     │ Hit Rate 94%, Memory 256MB/1GB         │ │
│ │ Queue Worker    │ 🟢     │ Pending: 3, Processed: 45,600          │ │
│ │ Storage (S3)    │ 🟢     │ Used 45GB/500GB                        │ │
│ │ AI Gateway      │ 🟡     │ Latency 2.3s (threshold 2s)           │ │
│ └───────────────────────────────────────────────────────────────────┘ │
│                                                                       │
└──────────────────────────────────────────────────────────────────────┘
```

### Widget Spesifikasi

#### a. Stats Cards

| Kartu | Data |
|-------|------|
| Tenant Aktif | `count(tenants WHERE status = 'active')` |
| Pengguna Aktif | `count(users WHERE deleted_at IS NULL)` |
| RPS Total | `count(rps)` |
| AI Call Bulan Ini | `count(ai_usage_logs WHERE MONTH = current)` |
| System Health | Status aggregate dari health check endpoints |

#### b. Table — Tenant Overview

| Kolom | Deskripsi |
|-------|-----------|
| Tenant | Nama universitas/tenant |
| Paket | Nama paket langganan (Basic, Standard, Premium) |
| Users | Jumlah pengguna dalam tenant |
| RPS | Total RPS dalam tenant |
| Status | 🟢 Aktif / 🟡 Trial / 🔴 Inaktif |

#### c. System Health

| Kolom | Deskripsi |
|-------|-----------|
| Komponen | Nama layanan/komponen |
| Status | 🟢 Normal / 🟡 Warning / 🔴 Critical |
| Detail | Informasi spesifik (uptime, usage, latency) |

---

## Widget Specifications Umum

### a. Card Widget

| Properti | Spesifikasi |
|----------|-------------|
| **Ukuran** | col-3 (xl), col-4 (lg), col-6 (md), col-12 (sm) |
| **Konten** | Ikon (kiri atas), Judul (small caps), Nilai (font besar), Subtitle opsional |
| **Warna** | Primary (biru), Success (hijau), Warning (oranye), Danger (merah) |
| **Interaksi** | Dapat diklik, mengarahkan ke halaman terkait |
| **Skeleton Loader** | Placeholder animasi saat loading |

### b. Chart Widget

| Properti | Spesifikasi |
|----------|-------------|
| **Container** | Card dengan header (judul + periode) dan body (canvas) |
| **Library** | Chart.js v4+ |
| **Responsive** | `responsive: true`, `maintainAspectRatio: false` |
| **Tinggi** | 300px (desktop), 250px (mobile) |
| **Empty State** | Teks "Belum ada data" di tengah canvas |
| **Error State** | Teks "Gagal memuat data" + tombol retry |

### c. Table Widget

| Properti | Spesifikasi |
|----------|-------------|
| **Header** | Sticky header dengan background abu-abu muda |
| **Sorting** | Klik kolom untuk sort asc/desc |
| **Pagination** | 5-10 item per halaman dengan navigasi |
| **Empty State** | Ilustrasi + teks "Belum ada data" |
| **Row Hover** | Highlight baris saat hover |
| **Responsive** | Horizontal scroll pada mobile |

### d. Progress Bar Widget

| Properti | Spesifikasi |
|----------|-------------|
| **Warna** | 0-40%: Danger (merah), 41-70%: Warning (oranye), 71-100%: Success (hijau) |
| **Label** | Persentase di dalam atau di samping bar |
| **Animasi** | Transisi lebar 500ms ease-in-out |

---

## Data Refresh Strategy

### Livewire Polling

| Widget | Interval Polling | Keterangan |
|--------|-----------------|------------|
| Stats Cards | 60 detik | Data agregat jarang berubah drastis |
| RPS Card List | 30 detik | Status RPS bisa berubah setelah review |
| Menunggu Approval Table | 30 detik | Kaprodi perlu update real-time |
| System Health | 120 detik | Monitoring server |
| Charts | Manual refresh only | Data chart di-refresh saat navigasi atau tombol refresh |

### Implementasi Livewire

```php
// Contoh: Dashboard Dosen dengan polling
class DosenDashboard extends Component
{
    public function render()
    {
        return view('livewire.dosen.dashboard', [
            'totalRps' => $this->getTotalRps(),
            'rpsList' => $this->getRpsList(),
            'recentActivity' => $this->getRecentActivity(),
        ]);
    }
}
```

```blade
{{-- Blade view --}}
<div wire:poll.30s>
    @foreach($rpsList as $rps)
        <x-rps-card :rps="$rps" />
    @endforeach
</div>
```

### Strategi Cache

| Data | Cache Duration | Key Pattern |
|------|---------------|-------------|
| Stats Cards | 60 detik | `dashboard:stats:{role}:{user_id}` |
| Chart Data | 300 detik (5 menit) | `dashboard:chart:{type}:{scope}` |
| Recent Activity | 60 detik | `dashboard:activity:{user_id}` |
| System Health | 120 detik | `dashboard:system:health` |

Invalidasi cache dilakukan saat ada event yang relevan (RPS status berubah, review disubmit, dsb).

---

## Akses Dashboard per Role

| Role | Dashboard | Route | Livewire Component |
|------|-----------|-------|--------------------|
| Super Admin | Platform Overview | `/admin/dashboard` | `SuperAdminDashboard` |
| Admin Univ | Universitas Overview | `/univ/dashboard` | `AdminUnivDashboard` |
| Admin Fakultas | Fakultas Overview | `/fakultas/dashboard` | `AdminFakultasDashboard` |
| Admin Prodi | Prodi Overview | `/prodi/dashboard` | `AdminProdiDashboard` |
| Kaprodi | Prodi Management | `/kaprodi/dashboard` | `KaprodiDashboard` |
| Reviewer | Review Tasks | `/reviewer/dashboard` | `ReviewerDashboard` |
| Dosen | My RPS | `/dosen/dashboard` | `DosenDashboard` |
| LPM | Quality Monitoring | `/lpm/dashboard` | `LpmDashboard` |

---

**Navigasi:** [Sebelumnya: Navigation Structure](27-navigation-structure.md) | [Daftar Isi](../README.md) | [Berikutnya: Notification Requirement](29-notification-requirement.md)
