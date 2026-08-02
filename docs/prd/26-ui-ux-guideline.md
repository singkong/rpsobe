# 26 — UI/UX Guideline

## Prinsip Desain

RPS OBE mengadopsi prinsip desain yang berfokus pada **kejelasan, efisiensi, dan konsistensi** untuk mendukung produktivitas pengguna dalam menyusun RPS.

| Prinsip | Deskripsi | Implementasi |
|---------|-----------|--------------|
| **Clarity (Kejelasan)** | Informasi disajikan dengan jelas, tanpa ambiguitas | Label deskriptif, hierarchy visual, tooltip, empty state guidance |
| **Efficiency (Efisiensi)** | Meminimalkan jumlah klik dan input untuk mencapai tujuan | Wizard 8-langkah, auto-save, keyboard shortcuts, bulk actions |
| **Consistency (Konsistensi)** | Pola interaksi dan visual yang seragam di seluruh aplikasi | Design system Tabler, komponen reusable, konvensi penamaan |
| **Feedback (Umpan Balik)** | Setiap aksi memberikan respons yang jelas | Toast notification, loading states, progress bar, validation messages |
| **Forgiveness (Toleransi)** | Mencegah error dan memudahkan pemulihan | Konfirmasi destructive actions, undo (future), auto-save, soft delete |
| **Accessibility (Aksesibilitas)** | Dapat digunakan oleh semua orang | WCAG 2.1 AA, keyboard navigation, screen reader, color contrast |
| **Progressive Disclosure** | Tampilkan informasi secara bertahap sesuai konteks | Expandable sections, step-by-step wizard, read more/less |

---

## Component Library

### Tabler.io

RPS OBE menggunakan **Tabler** sebagai library komponen UI utama. Tabler adalah framework UI open-source berbasis Bootstrap 5 yang menyediakan komponen siap pakai dengan desain minimalis dan profesional.

| Komponen | Sumber | Penggunaan |
|----------|--------|------------|
| Layout | Tabler Layout | Sidebar, header, content area |
| Navigation | Tabler Navbar | Sidebar navigation, breadcrumb, tabs |
| Cards | Tabler Cards | Stat cards, info cards, RPS cards |
| Tables | Tabler Tables | Data tables (responsive, sortable, striped) |
| Forms | Tabler Forms + Bootstrap | Input, select, textarea, checkbox, radio, toggle |
| Buttons | Tabler Buttons | Primary, secondary, danger, outline, loading states |
| Modals | Tabler Modals | Konfirmasi, form dalam modal, detail preview |
| Alerts | Tabler Alerts | Success, warning, error, info banners |
| Badges | Tabler Badges | Status indicator (Draft, Review, Published) |
| Avatars | Tabler Avatars | User avatars, initials fallback |
| Dropdowns | Tabler Dropdowns | Action menus, bulk actions |
| Tabs | Tabler Tabs | Wizard step indicator, content tabs |
| Toasts | Tabler Toasts | Notifikasi toast (success, error, info) |
| Progress | Tabler Progress | Progress bar, loading indicators |
| Spinners | Tabler Spinners | Loading states |
| Tooltips | Tabler Tooltips | Informasi tambahan pada hover |
| Empty States | Tabler Empty | Halaman kosong dengan ilustrasi dan CTA |
| Pagination | Tabler Pagination | Navigasi halaman pada tabel |
---

## Color Palette

### Warna Utama

| Nama | Hex | RGB | CSS Variable | Penggunaan |
|------|-----|-----|-------------|------------|
| Primary Blue | `#206bc4` | 32, 107, 196 | `--tblr-primary` | Primary buttons, links, active states |
| Primary Light | `#e9f0f9` | 233, 240, 249 | `--tblr-primary-light` | Background hover, selected row |
| Secondary | `#656d77` | 101, 109, 119 | `--tblr-secondary` | Secondary buttons, muted text |
| Success Green | `#2fb344` | 47, 179, 68 | `--tblr-success` | Success states, active, published |
| Warning Amber | `#f59f00` | 245, 159, 0 | `--tblr-warning` | Warning states, pending, draft |
| Danger Red | `#d63939` | 214, 57, 57 | `--tblr-danger` | Error states, delete, danger actions |
| Info Cyan | `#4299e1` | 66, 153, 225 | `--tblr-info` | Info messages, tips |

### Warna Status RPS

| Status | Warna | Badge |
|--------|-------|-------|
| Draft | `#f59f00` (Amber) | `<span class="badge bg-warning">Draft</span>` |
| Review | `#4299e1` (Info) | `<span class="badge bg-info">Review</span>` |
| Revision | `#d63939` (Danger) | `<span class="badge bg-danger">Revisi</span>` |
| Approved | `#ae3ec9` (Purple) | `<span class="badge bg-purple">Disetujui</span>` |
| Published | `#2fb344` (Success) | `<span class="badge bg-success">Published</span>` |
| Archived | `#656d77` (Secondary) | `<span class="badge bg-secondary">Arsip</span>` |

### Warna Kategori CPL

| Kategori | Warna | Penggunaan |
|----------|-------|------------|
| Sikap (S) | `#2fb344` (Green) | CPL-S-xx |
| Pengetahuan (P) | `#206bc4` (Blue) | CPL-P-xx |
| Keterampilan Umum (KU) | `#f59f00` (Amber) | CPL-KU-xx |
| Keterampilan Khusus (KK) | `#ae3ec9` (Purple) | CPL-KK-xx |

### Warna Level Taksonomi Bloom

| Level | Nama | Warna |
|-------|------|-------|
| C1 | Mengingat | `#e9ecef` |
| C2 | Memahami | `#c6d3fb` |
| C3 | Menerapkan | `#a5d8ff` |
| C4 | Menganalisis | `#74c0fc` |
| C5 | Mengevaluasi | `#4dabf7` |
| C6 | Mencipta | `#206bc4` |

### Warna Netral

| Nama | Hex | Penggunaan |
|------|-----|------------|
| Background | `#f6f8fb` | Latar halaman |
| Card Background | `#ffffff` | Latar card |
| Border | `#e6e8eb` | Border, garis pemisah |
| Text Primary | `#1d273b` | Teks utama |
| Text Secondary | `#656d77` | Teks sekunder |
| Text Muted | `#98a2b3` | Teks nonaktif/placeholder |

---

## Typography

### Font Family

| Penggunaan | Font | Fallback |
|------------|------|----------|
| UI (default) | `-apple-system, BlinkMacSystemFont, Segoe UI, Roboto` | `sans-serif` |
| Headings | Sama dengan UI | Same stack |
| Monospace (kode) | `Cascadia Code, Fira Code, JetBrains Mono` | `monospace` |
| Print/PDF | `Times New Roman, Times` | `serif` |

### Type Scale

| Level | Size | Line Height | Weight | Penggunaan |
|-------|------|-------------|--------|------------|
| H1 | 28px / 1.75rem | 1.3 | 600 | Judul halaman |
| H2 | 24px / 1.5rem | 1.3 | 600 | Judul section |
| H3 | 20px / 1.25rem | 1.3 | 600 | Judul sub-section |
| H4 | 18px / 1.125rem | 1.4 | 600 | Card title, modal title |
| Body | 16px / 1rem | 1.5 | 400 | Teks body default |
| Body Small | 14px / 0.875rem | 1.5 | 400 | Deskripsi, helper text |
| Caption | 12px / 0.75rem | 1.5 | 400 | Label kecil, meta info |
| Code | 14px / 0.875rem | 1.5 | 400 | Kode MK, kode CPL |

---

## Spacing and Layout

### Spacing Scale (Bootstrap 5)

| Token | Value | Penggunaan |
|-------|-------|------------|
| `0` | 0 | Remove spacing |
| `1` | 0.25rem (4px) | Tight spacing |
| `2` | 0.5rem (8px) | Related elements |
| `3` | 1rem (16px) | Default spacing |
| `4` | 1.5rem (24px) | Section separation |
| `5` | 3rem (48px) | Major separation |

### Layout Grid

```text
+--------------------------------------------------------------+
|                         HEADER (56px)                         |
+----------+---------------------------------------------------+
| SIDEBAR  |               CONTENT AREA                        |
| (260px)  |          (max-width: 1320px centered)             |
+----------+---------------------------------------------------+
```

### Page Structure

| Elemen | Deskripsi |
|--------|-----------|
| Header | Tinggi 56px. Logo, tenant name, notification bell, user dropdown |
| Sidebar | Lebar 260px (collapsed: 68px). Navigasi utama dengan icon + label |
| Content Area | Max-width 1320px, padding 1.5rem. Background `#f6f8fb` |

---

## Icons (Tabler Icons)

RPS OBE menggunakan **Tabler Icons** (4000+ ikon SVG).

### Ikon Berdasarkan Konteks

| Konteks | Ikon | Penggunaan |
|---------|------|------------|
| Dashboard | `ti ti-dashboard` | Menu dashboard |
| RPS List | `ti ti-books` | Menu daftar RPS |
| Buat RPS | `ti ti-file-plus` | Buat RPS baru |
| Edit | `ti ti-edit` | Edit |
| View | `ti ti-eye` | Lihat detail |
| Delete | `ti ti-trash` | Hapus |
| Submit | `ti ti-send` | Submit review |
| Review | `ti ti-clipboard-check` | Review |
| Approve | `ti ti-circle-check` | Approval |
| Reject/Revise | `ti ti-arrow-back-up` | Minta revisi |
| Publish | `ti ti-world-upload` | Publikasi |
| Archive | `ti ti-archive` | Arsipkan |
| Download/Export | `ti ti-download` | Export |
| AI Generate | `ti ti-sparkles` | AI generate |
| AI Validate | `ti ti-zoom-check` | AI validasi |
| Save | `ti ti-device-floppy` | Simpan |
| Search | `ti ti-search` | Search |
| Filter | `ti ti-filter` | Filter |
| Sort | `ti ti-arrows-sort` | Sort |
| User | `ti ti-user` | Manajemen user |
| Settings | `ti ti-settings` | Pengaturan |
| Notification | `ti ti-bell` | Notifikasi |
| Plus/Add | `ti ti-plus` | Tambah |
| Info | `ti ti-info-circle` | Informasi |
| Warning | `ti ti-alert-triangle` | Peringatan |
| Upload | `ti ti-upload` | Upload file |
| Template | `ti ti-template` | Template |
| Report | `ti ti-report` | Laporan |
| Log | `ti ti-history` | Audit log |
| Calendar | `ti ti-calendar` | Semester, tanggal |
| Building | `ti ti-building` | Universitas |
| School | `ti ti-school` | Fakultas |
| Certificate | `ti ti-certificate` | Program studi |
| Book | `ti ti-book` | Kurikulum, MK |
| Users | `ti ti-users` | Dosen, mahasiswa |

---

## Form Design Guidelines

### Prinsip Formulir

1. **Single Column Layout** untuk form yang fokus pada input berurutan
2. **Label di atas input** (bukan di samping) untuk readability
3. **Placeholder sebagai contoh** (bukan pengganti label)
4. **Required fields** ditandai dengan asterisk merah (`*`)
5. **Inline validation** — validasi muncul saat user selesai mengetik (on blur)
6. **Error messages** berwarna merah, ditampilkan di bawah input
7. **Success indicators** — centang hijau setelah input valid
8. **Help text** berwarna muted di bawah input

### Input States

| State | Visual Cue |
|-------|-----------|
| Default | Gray border, white background |
| Focus | Blue border, subtle blue glow |
| Error | Red border, red error text below |
| Success | Green border, green check icon |
| Disabled | Gray background, muted text |
| Read-only | No border highlight, muted |

### Tombol Aksi Form

| Tombol | Posisi | Style |
|--------|--------|-------|
| Simpan / Lanjut | Bottom-right | Primary |
| Batal | Bottom-left | Secondary/Outline |
| Kembali | Bottom-left | Outline |
| Hapus | Bottom-left | Danger Outline |
| Reset | Bottom-left | Muted |

---

## Table Design Guidelines

### Struktur Tabel Standar

| Komponen | Implementasi |
|----------|-------------|
| Header Bar | Judul + search + filter + CTA button |
| Table | `<table class="table table-vcenter table-striped">` |
| Sortable Headers | Ikon sort arrow, `wire:click="sortBy('column')"` |
| Row Actions | 3-dot dropdown menu |
| Selection Checkbox | Checkbox di kolom pertama untuk bulk action |
| Pagination | Navigasi halaman + info (1-20 of 200) |
| Empty State | Ilustrasi kosong + CTA |
| Loading State | Skeleton rows (shimmer effect) |

### Column Width Guide

| Tipe Kolom | Width |
|------------|-------|
| Checkbox | 40px |
| Kode/ID | 80-120px |
| Nama Utama | Auto (flex) |
| Status/Badge | 100-120px |
| Tanggal | 120-140px |
| Aksi | 80-120px |

---

## Wizard Design (8 Steps)

Wizard 8 langkah adalah komponen inti RPS Builder.

### Wizard Layout

```text
+------------------------------------------------------------------+
|  STEP INDICATOR                                                    |
|  [1]---[2]---[3]---[4]---[5]---[6]---[7]---[8]                   |
+------------------------------------------------------------------+
|  STEP TITLE: Langkah 3 — CPMK                                     |
|  Deskripsi: Tentukan CPMK untuk mata kuliah ini                    |
|                                                                    |
|  +--------------------------------------------------------------+  |
|  |  STEP CONTENT AREA (form, table, AI panel)                     |  |
|  |  Tinggi minimum: 400px                                        |  |
|  +--------------------------------------------------------------+  |
|                                                                    |
|  [Kembali]                        [Simpan Draft] [Lanjut]         |
+------------------------------------------------------------------+
```

### Step Indicator States

| State | Visual | Kondisi |
|-------|--------|---------|
| Completed | Check icon, green circle | Step sudah diisi dan divalidasi |
| Active | Primary blue circle, bold label | Step yang sedang aktif |
| Pending | Gray circle, muted label | Step belum dikunjungi |
| Error | Red circle, warning icon | Step memiliki error |

### Wizard Progress Bar

Progress bar menunjukkan persentase kelengkapan RPS (0-100%), dihitung dari step yang selesai dan data yang terisi.

### Wizard Navigation Rules

| Step | Kembali | Lanjut |
|------|---------|--------|
| Step 1 | Disabled | Validasi lalu ke Step 2 |
| Step 2-7 | Kembali ke sebelumnya | Validasi lalu lanjut |
| Step 8 | Kembali ke Step 7 | "Selesai" / "Ajukan Review" |

### Wizard Validation per Step

| Step | Validasi |
|------|----------|
| Step 1 | MK dipilih, kurikulum aktif, semester aktif, min 1 dosen |
| Step 2 | Minimal 1 CPL dipilih |
| Step 3 | Minimal 1 CPMK, setiap CPMK terkait CPL |
| Step 4 | Setiap CPMK punya minimal 1 Sub-CPMK |
| Step 5 | Materi per pertemuan sesuai Sub-CPMK |
| Step 6 | Metode dan aktivitas terisi |
| Step 7 | Minimal 1 assessment, total bobot = 100% |
| Step 8 | Minimal 3 referensi, tidak ada error validasi |

---

## Responsive Breakpoints

| Breakpoint | Min Width | Target Device | Behavior |
|------------|-----------|---------------|----------|
| XS | < 576px | Mobile portrait | Single column, collapsed sidebar |
| SM | >= 576px | Mobile landscape | Single column |
| MD | >= 768px | Tablet | Sidebar expandable |
| LG | >= 992px | Small Desktop | Default layout |
| XL | >= 1200px | Desktop | Full layout |
| XXL | >= 1400px | Large Desktop | Max 1320px container |

### Responsive Strategies

| Komponen | Mobile (< 768px) | Desktop (>= 768px) |
|----------|------------------|---------------------|
| Sidebar | Off-canvas hamburger | Fixed left, 260px |
| Tables | Horizontal scroll | Full table |
| Forms | Single column | 1-2 columns |
| Cards | 1 per row | 2-4 per row |
| Wizard Steps | Vertical/compact | Horizontal |
| Modals | Full screen | Centered, max 600px |

---

## Loading States

### Tipe Loading

| Tipe | Penggunaan |
|------|------------|
| Page Loading | Progress bar linear di atas halaman |
| Table Loading | Skeleton rows (shimmer effect) |
| Form Submit | Button loading spinner + disabled |
| AI Processing | Typing animation, progress bar |
| Export Processing | Modal progress bar |
| Inline Loading | Spinner kecil |

### Button Loading State

```html
<button class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
    <span wire:loading.remove wire:target="save">
        <i class="ti ti-device-floppy"></i> Simpan
    </span>
    <span wire:loading wire:target="save">
        <span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...
    </span>
</button>
```

### Livewire Global Loading

```html
<div wire:loading class="progress progress-sm w-100 position-fixed top-0"
     style="z-index: 9999; height: 3px;">
    <div class="progress-bar progress-bar-indeterminate bg-primary"></div>
</div>
```

---

## Empty States

Setiap halaman yang dapat kosong harus memiliki empty state informatif.

| Komponen Empty State | Deskripsi |
|----------------------|-----------|
| Ilustrasi/Ikon | Ikon besar (64px) yang relevan |
| Judul | Menjelaskan situasi |
| Deskripsi | Penjelasan dan ajakan bertindak |
| CTA Button | Tombol aksi untuk memulai |

### Variasi

| Tipe | Contoh |
|------|--------|
| Tidak Ada Data | "Belum ada RPS" + tombol Buat RPS Baru |
| Search No Results | "Tidak Ditemukan" + tombol Hapus Filter |
| No Access | "Tidak memiliki akses" + tombol Kembali |
| Error Load | "Gagal memuat data" + tombol Coba Lagi |

---

## Error States

### Tipe Error

| Tipe | Implementasi |
|------|-------------|
| Form Validation | Inline error message, field border merah |
| Form Summary | Alert banner di atas form |
| 404 Page | Error page dengan navigasi kembali |
| 500 Server | Error page dengan info kontak support |
| API Error | Toast notification + opsi retry |
| Network Error | Banner persistent di atas halaman |
| 403 Permission | Halaman khusus dengan pesan jelas |
| AI Error | Inline error + tombol retry |

### Validation Error Example

```html
<div class="mb-3">
    <label class="form-label required">Nama Mata Kuliah</label>
    <input type="text" class="form-control is-invalid" wire:model="nama_mk">
    <div class="invalid-feedback">Nama mata kuliah wajib diisi</div>
</div>
```

---

## Toast Notifications

### Tipe Toast

| Tipe | Warna | Ikon | Penggunaan |
|------|-------|------|------------|
| Success | `bg-success` | `ti ti-circle-check` | Simpan, publish, approve |
| Error | `bg-danger` | `ti ti-alert-circle` | Validasi gagal, server error |
| Warning | `bg-warning` | `ti ti-alert-triangle` | Peringatan, perhatian |
| Info | `bg-info` | `ti ti-info-circle` | Informasi netral |

### Konfigurasi

| Properti | Value |
|----------|-------|
| Posisi | Top-right |
| Durasi | 5s (success/info), 10s (warning), manual dismiss (error) |
| Max Visible | 3 toast |

### Livewire Toast Dispatch

```php
$this->dispatch('toast', [
    'type' => 'success',
    'title' => 'RPS berhasil disimpan',
    'message' => 'RPS "Pemrograman Web" telah disimpan sebagai Draft.',
]);
```

---

## Modal/Dialog Guidelines

### Tipe Modal

| Tipe | Ukuran | Penggunaan |
|------|--------|------------|
| Small (sm) | 400px | Konfirmasi sederhana |
| Default | 540px | Form sederhana, detail |
| Large (lg) | 720px | Form kompleks, preview |
| Extra Large (xl) | 960px | Preview RPS, detail lengkap |
| Full Screen | 100% | AI panel, mapping visualization |

### Guidelines

| Do | Don't |
|----|-------|
| Gunakan untuk aksi yang butuh konteks halaman | Jangan untuk konten panjang (jadikan halaman penuh) |
| Tutup dengan X, Batal, atau klik backdrop | Jangan hanya mengandalkan backdrop click |
| Konfirmasi destructive actions | Jangan langsung eksekusi tanpa konfirmasi |
| Fokuskan input pertama saat modal terbuka | Jangan modal di atas modal |
| Loading state saat proses async | Jangan tanpa feedback |

---

## Accessibility Guidelines

RPS OBE memenuhi standar **WCAG 2.1 Level AA**.

### Checklist

| Kategori | Requirement | Implementasi |
|----------|-------------|-------------|
| Warna | Kontras minimum 4.5:1 | Palet sudah diuji |
| Warna | Informasi tidak hanya warna | Status RPS: label + badge warna |
| Keyboard | Semua interaksi via keyboard | Tab order logis, focus visible |
| Keyboard | Skip navigation link | Tersedia di awal halaman |
| Screen Reader | Alt text pada gambar | `alt` attribute |
| Screen Reader | Ikon dekoratif hidden | `aria-hidden="true"` |
| Screen Reader | Label form input | `aria-label` atau `<label>` |
| Screen Reader | Dynamic content | `aria-live="polite"` pada toast |
| Semantics | HTML5 elements | `<header>`, `<nav>`, `<main>` |
| Focus | Focus management | Fokus pindah setelah modal |
| Form | Error identification | `aria-describedby` |
| Heading | Hierarki benar | h1 > h2 > h3 tanpa skip |

---

## Design Tokens Summary

### CSS Custom Properties

```css
:root {
    /* Colors */
    --rps-primary: #206bc4;
    --rps-primary-light: #e9f0f9;
    --rps-secondary: #656d77;
    --rps-success: #2fb344;
    --rps-warning: #f59f00;
    --rps-danger: #d63939;
    --rps-info: #4299e1;

    /* Status Colors */
    --rps-status-draft: #f59f00;
    --rps-status-review: #4299e1;
    --rps-status-revision: #d63939;
    --rps-status-approved: #ae3ec9;
    --rps-status-published: #2fb344;
    --rps-status-archived: #656d77;

    /* CPL Category Colors */
    --rps-cpl-sikap: #2fb344;
    --rps-cpl-pengetahuan: #206bc4;
    --rps-cpl-ket-umum: #f59f00;
    --rps-cpl-ket-khusus: #ae3ec9;

    /* Typography */
    --rps-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    --rps-font-mono: "Cascadia Code", "Fira Code", monospace;
    --rps-font-print: "Times New Roman", Times, serif;
    --rps-font-size-h1: 1.75rem;
    --rps-font-size-h2: 1.5rem;
    --rps-font-size-h3: 1.25rem;
    --rps-font-size-body: 1rem;
    --rps-font-size-small: 0.875rem;
    --rps-font-size-caption: 0.75rem;
    --rps-line-height: 1.5;

    /* Spacing */
    --rps-spacing-xs: 0.25rem;
    --rps-spacing-sm: 0.5rem;
    --rps-spacing-md: 1rem;
    --rps-spacing-lg: 1.5rem;
    --rps-spacing-xl: 3rem;

    /* Layout */
    --rps-sidebar-width: 260px;
    --rps-sidebar-collapsed: 68px;
    --rps-header-height: 56px;
    --rps-content-max-width: 1320px;
    --rps-border-radius: 4px;
    --rps-border-color: #e6e8eb;
    --rps-bg-page: #f6f8fb;
    --rps-bg-card: #ffffff;

    /* Shadows */
    --rps-shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --rps-shadow-md: 0 4px 6px rgba(0,0,0,0.07);
    --rps-shadow-lg: 0 10px 25px rgba(0,0,0,0.1);

    /* Transitions */
    --rps-transition-fast: 150ms ease;
    --rps-transition-normal: 300ms ease;
    --rps-transition-slow: 500ms ease;

    /* Z-Index */
    --rps-z-sidebar: 100;
    --rps-z-header: 200;
    --rps-z-modal-backdrop: 1050;
    --rps-z-modal: 1055;
    --rps-z-toast: 1060;
    --rps-z-tooltip: 1070;
}
```

---

**Navigasi:** [Sebelumnya: ERD Overview](25-erd-overview.md) | [Daftar Isi](../README.md) | [Berikutnya: Navigation Structure](27-navigation-structure.md)
