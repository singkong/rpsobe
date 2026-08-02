# Architecture Decision Records (ADR)

Dokumentasi keputusan arsitektur untuk proyek RPS OBE. Setiap ADR mencatat keputusan penting, konteks yang melatarbelakangi, alternatif yang dipertimbangkan, serta konsekuensi dari keputusan tersebut.

Format penulisan ADR mengikuti pola:

- **Judul**: ADR-NNN: Nama keputusan
- **Status**: Accepted / Proposed / Deprecated / Superseded
- **Tanggal**: Tanggal keputusan diambil
- **Konteks**: Latar belakang dan permasalahan
- **Keputusan**: Keputusan yang diambil
- **Konsekuensi**: Dampak positif dan negatif
- **Alternatif yang Dipertimbangkan**: Opsi lain beserta alasan penolakan

---

## ADR-001: Menggunakan Laravel 13 sebagai Framework Utama

- **Status**: Accepted
- **Tanggal**: 2025-01-10

### Konteks

Proyek RPS OBE membutuhkan framework backend yang mampu menangani kompleksitas multi-tenant, RBAC (Role-Based Access Control), workflow engine, AI integration, dan document generation. Kami mengevaluasi empat framework utama: Laravel (PHP), Django (Python), Express.js (Node.js), dan Spring Boot (Java). Kriteria penilaian mencakup kecepatan pengembangan (time-to-market), ketersediaan library pendukung, ekosistem package, ketersediaan talenta developer di tim dan pasar lokal, performa, serta kemudahan deployment.

### Keputusan

Kami memutuskan untuk menggunakan **Laravel 13** (versi terbaru saat proyek dimulai) sebagai framework utama backend.

### Konsekuensi

**Positif:**
- Ekosistem package yang sangat matang untuk kebutuhan spesifik: Spatie Permission (RBAC), Laravel Sanctum (API authentication), Laravel Excel (bulk import), Laravel Notifications, dan Laravel Auditing.
- Eloquent ORM menyederhanakan operasi database kompleks dengan relasi nested, eager loading, dan soft deletes.
- Blade + Livewire untuk UI dan API resource untuk backend, memungkinkan arsitektur monolit yang terstruktur tanpa perlu frontend terpisah.
- Artisan CLI mempercepat scaffolding dan automasi development tasks.
- Tinker untuk debugging dan eksplorasi data secara interaktif.
- Dokumentasi resmi yang sangat lengkap dan komunitas besar di Indonesia.
- Deployment sederhana di shared hosting (cPanel) maupun VPS, sesuai preferensi universitas di Indonesia.

**Negatif:**
- PHP sering dianggap kurang modern dibandingkan bahasa lain, meskipun PHP 8.x telah mengatasi banyak kritik historis.
- Performa mentah PHP lebih rendah dibandingkan Go atau Java untuk high-concurrency scenarios (tidak kritis untuk use case RPS OBE).
- Arsitektur monolitik dapat menjadi tantangan jika aplikasi tumbuh secara signifikan di masa depan.

### Alternatif yang Dipertimbangkan

1. **Django (Python)**: Memiliki admin panel built-in dan ORM yang solid, namun talenta Python dengan spesialisasi web framework lebih sulit ditemukan di pasar Indonesia. Ekosistem package untuk RBAC dan multi-tenancy tidak sematang Laravel.
2. **Express.js (Node.js)**: Keunggulan pada performa async non-blocking, namun arsitektur yang terlalu bebas (unopinionated) berisiko menghasilkan codebase yang tidak konsisten di tim dengan skill bervariasi. Package manager yang terlalu banyak pilihan (Prisma vs Sequelize vs TypeORM vs Knex) menimbulkan decision fatigue.
3. **Spring Boot (Java)**: Performa dan type-safety sangat baik, namun development velocity lambat, boilerplate code tinggi, dan learning curve curam. Tidak cocok untuk tim dengan deadline ketat.

---

## ADR-002: Menggunakan Livewire 3 + Volt untuk Frontend

- **Status**: Accepted
- **Tanggal**: 2025-01-12

### Konteks

Aplikasi RPS OBE membutuhkan antarmuka pengguna yang interaktif dengan fitur seperti form wizard untuk penyusunan RPS multi-step, real-time validation, drag-and-drop untuk mapping CPL-CPMK, dan dashboard dengan chart. Kami mengevaluasi tiga pendekatan frontend: Livewire 3 + Volt (full-stack Laravel), Inertia.js + Vue/React (SPA hybrid), dan tradisional Blade + jQuery.

### Keputusan

Kami memutuskan untuk menggunakan **Livewire 3 + Volt** untuk seluruh frontend aplikasi.

### Konsekuensi

**Positif:**
- Developer hanya perlu menguasai PHP dan Blade, tidak perlu belajar framework JavaScript terpisah. Ini sangat penting mengingat komposisi tim developer.
- Volt menyediakan functional API dengan komponen single-file, mirip Vue SFC, yang membuat kode lebih ringkas dan mudah dibaca.
- State management handled secara otomatis di server-side, tanpa perlu Vuex/Pinia/Redux.
- Tidak perlu membangun, mengelola, dan mendeploy dua codebase terpisah (frontend + backend API).
- Integrasi dengan Tabler UI components bisa langsung via Blade attributes.
- Turbolinks-style navigation membuat navigasi halaman terasa seperti SPA.
- Morphdom-based DOM diffing untuk update yang efisien.
- Livewire 3 mendukung nested components, computed properties, dan offline indicators.

**Negatif:**
- Setiap interaksi user memerlukan round-trip ke server (HTTP request). Untuk fitur yang membutuhkan instant feedback (seperti drag-and-drop mapping), perlu diimplementasikan dengan Alpine.js companion.
- Tidak cocok untuk aplikasi dengan kebutuhan real-time tinggi (chat, kolaborasi simultan). Untuk fitur notifikasi, kompensasi dengan polling atau Laravel Echo.
- File komponen Livewire yang besar dapat menjadi sulit di-maintain tanpa disiplin struktur kode yang baik.
- SEO tidak optimal untuk SSR (tidak kritis karena aplikasi sepenuhnya di balik login).

### Alternatif yang Dipertimbangkan

1. **Inertia.js + Vue 3**: Memberikan pengalaman SPA penuh dengan reactivity client-side yang superior. Namun, memerlukan developer yang menguasai Vue 3 ecosystem (Composition API, Pinia, Vue Router). Build step webpack/vite menambah kompleksitas development dan deployment. Cocok untuk tim dengan frontend specialist, namun tim kami mayoritas full-stack PHP.
2. **Tradisional Blade + jQuery + Bootstrap**: Pendekatan klasik yang tidak memerlukan learning curve tambahan. Namun, kode JavaScript imperative yang tercampur dengan Blade directive akan sangat sulit di-maintain untuk fitur interaktif kompleks seperti mapping CPL-CPMK dan form wizard multi-step yang dibutuhkan RPS OBE.

---

## ADR-003: Menggunakan MariaDB sebagai Database Utama

- **Status**: Accepted
- **Tanggal**: 2025-01-10

### Konteks

Aplikasi RPS OBE perlu menyimpan data relasional yang kompleks dengan struktur hierarchical (universitas > fakultas > prodi > kurikulum > MK > RPS > CPMK > Sub-CPMK) dan memerlukan dukungan JSON untuk fleksibilitas data seperti checklist review, konfigurasi, dan snapshot versioning. Kami mengevaluasi tiga database: MariaDB, MySQL, dan PostgreSQL.

### Keputusan

Kami memutuskan untuk menggunakan **MariaDB 10.11+** sebagai database utama.

### Konsekuensi

**Positif:**
- MariaDB tersedia secara default di hampir semua shared hosting dan VPS di Indonesia (cPanel, Plesk, aaPanel), yang memudahkan deployment di lingkungan universitas.
- Kompatibilitas penuh dengan Laravel Eloquent tanpa perlu konfigurasi khusus.
- Performa baca yang sangat baik untuk query dengan banyak JOIN (pola akses dominan di RPS OBE).
- Dukungan JSON column sejak versi 10.2, memungkinkan penyimpanan data semi-terstruktur seperti rps_versions.data, rps_reviews.checklist, dan settings tanpa kehilangan kemampuan indexing.
- Lisensi GPL yang lebih bebas secara komersial dibandingkan Oracle-owned MySQL.
- Virtual columns dan generated columns mendukung indexing pada data JSON.
- Konektor Aria dan MyRocks untuk workload spesifik.

**Negatif:**
- Tidak memiliki full-text search secanggih PostgreSQL GIN/GiST (dikompensasi dengan LIKE/REGEXP atau di masa depan dengan Laravel Scout + Meilisearch).
- Tidak memiliki native UUID type (menggunakan CHAR(36) atau package third-party).
- Fitur CTE rekursif di MariaDB lebih terbatas dibanding PostgreSQL (namun kebutuhan queri rekursif minimal di RPS OBE).

### Alternatif yang Dipertimbangkan

1. **PostgreSQL**: Memiliki fitur yang lebih kaya (native UUID, full-text search, CTE rekursif canggih, JSONB dengan indexing GIN). Namun, adopsi di shared hosting Indonesia sangat rendah, mempersulit deployment untuk university customer yang mayoritas menggunakan shared hosting. Memerlukan VPS/dedicated server yang meningkatkan biaya operasional.
2. **MySQL 8.0**: Secara fungsional setara dengan MariaDB untuk use case kami. Namun, MariaDB dipilih karena track recordnya yang lebih baik dalam inovasi open-source dan bebas dari kontrol Oracle. MariaDB juga memiliki beberapa optimasi query optimizer yang tidak ada di MySQL.

---

## ADR-004: Menggunakan Tabler untuk UI Framework

- **Status**: Accepted
- **Tanggal**: 2025-01-13

### Konteks**

Aplikasi RPS OBE membutuhkan UI framework untuk membangun antarmuka dashboard admin, form RPS yang kompleks, tabel data, dan komponen navigasi. Kami mengevaluasi tiga pilihan: Tabler (berbasis Bootstrap 5), Bootstrap 5 standar, dan Tailwind CSS dari nol.

### Keputusan

Kami memutuskan untuk menggunakan **Tabler** sebagai UI framework utama.

### Konsekuensi

**Positif:**
- Tabler menyediakan komponen dashboard siap pakai yang didesain khusus untuk aplikasi admin/dashboard — use case yang tepat untuk RPS OBE.
- >500 komponen UI built-in: charts, tables, forms, cards, navbar, sidebar, modals, notifications, dan banyak lagi.
- Berbasis Bootstrap 5 sehingga semua utility class Bootstrap tetap tersedia dan developer yang familiar dengan Bootstrap tidak perlu belajar dari nol.
- Dark mode built-in dengan satu toggle class.
- Responsive design mobile-first — penting untuk dosen yang mengakses RPS via tablet atau smartphone.
- Kompatibel dengan Livewire 3 dan Blade components melalui integrasi direktif.
- Kustomisasi via CSS variables dan SCSS.
- Active community dan dokumentasi yang baik dengan contoh kode copy-paste.

**Negatif:**
- Bundle CSS lebih besar (~200KB gzipped) dibandingkan Tailwind dengan purging (namun aplikasi ini di balik login, tidak perlu optimasi SEO/landing page).
- Beberapa JavaScript component Tabler memerlukan Bootstrap JS yang dapat bentrok dengan Alpine.js companion Livewire (perlu konfigurasi hati-hati).
- Tidak memiliki plugin ekosistem sebesar Bootstrap.

### Alternatif yang Dipertimbangkan

1. **Bootstrap 5 Standar**: Menyediakan grid, utilities, dan komponen dasar yang solid. Namun, mengembangkan komponen dashboard, charts, dan layout admin dari nol dengan Bootstrap standar akan memakan waktu signifikan (estimasi 3-4 minggu tambahan untuk UI development). Tabler sudah menyediakan semua ini siap pakai.
2. **Tailwind CSS dari Nol**: Memberikan fleksibilitas desain tanpa batas dan bundle size minimal dengan purging. Namun, membangun seluruh komponen dashboard admin (sidebar, navbar, tabel kompleks, filter, chart cards) dari utility-first classes akan memakan waktu sangat besar. Tailwind UI (berbayar) dapat mengkompensasi namun menambah biaya lisensi. Lebih cocok untuk tim dengan dedicated designer/frontend engineer.

---

## ADR-005: Menggunakan Spatie Permission untuk RBAC

- **Status**: Accepted
- **Tanggal**: 2025-01-11

### Konteks

Aplikasi RPS OBE memerlukan sistem Role-Based Access Control (RBAC) yang fleksibel dengan hierarki berikut:

- **Role**: super-admin, admin_universitas, admin_fakultas, kaprodi, reviewer, dosen
- **Permission granular**: rps.create, rps.read, rps.update, rps.delete, rps.submit, rps.approve, users.manage, master-data.*, dashboard.*, export.*, ai.*, dan seterusnya
- **Multi-tenant isolation**: Role dan permission harus terisolasi per tenant
- **Dynamic assignment**: Role dapat diberikan/dicabut oleh admin tenant

Kami mengevaluasi tiga pendekatan: Spatie Laravel Permission package, implementasi kustom dari nol, dan Laravel Gates + Policies saja tanpa package eksternal.

### Keputusan

Kami memutuskan untuk menggunakan **Spatie Laravel Permission** package.

### Konsekuensi

**Positif:**
- Package paling populer di ekosistem Laravel (15K+ stars di GitHub) dengan maintenance aktif dan komunitas besar.
- Menyediakan API lengkap: `$user->assignRole()`, `$user->hasRole()`, `$user->can()`, `$user->givePermissionTo()`, `$role->syncPermissions()`.
- Dukungan middleware built-in: `@can('rps.create')` di Blade dan `Route::middleware('permission:rps.create')`.
- Integrasi native dengan Laravel Gate sehingga `$user->can()` tetap bekerja.
- Caching permission untuk performa (default menggunakan cache Laravel).
- Mendukung multiple guards (web, api) dan role hierarchies.
- Mendukung team/permission context untuk multi-tenant scenario.
- Blade directives: `@role('admin')`, `@hasrole`, `@hasanyrole`.
- Migration table otomatis disediakan package.

**Negatif:**
- Setiap permission check akan melakukan query database (diminimalkan dengan caching).
- Struktur tabel permission Spatie perlu disesuaikan untuk menambahkan kolom `tenant_id` dan `group` (dilakukan melalui migration tambahan).
- Role hierarchy antar tenant harus di-manage dengan hati-hati agar tidak terjadi privilege leakage.

### Alternatif yang Dipertimbangkan

1. **Custom RBAC Implementation**: Memberikan kontrol penuh atas struktur data dan logika bisnis. Namun, mengembangkan sistem RBAC yang mature dengan caching, middleware, Blade directives, dan multi-tenant support dari nol memerlukan waktu estimasi 3-4 minggu dan berisiko bug security. Biaya pengembangan tidak sebanding dengan menggunakan package yang sudah terbukti.
2. **Laravel Gates + Policies Saja**: Cukup untuk aplikasi kecil dengan permission statis. Namun, untuk use case kami yang memerlukan dynamic permission assignment oleh admin tenant (bukan hard-coded), Gates/Policies saja tidak cukup. Perlu membangun UI manajemen role-permission dari nol, yang secara esensial akan menciptakan ulang apa yang sudah disediakan Spatie.

---

## ADR-006: Menggunakan OpenAI API GPT-4o untuk Fitur AI

- **Status**: Accepted
- **Tanggal**: 2025-01-14

### Konteks

Salah satu nilai jual utama RPS OBE adalah fitur AI-assisted RPS development yang mencakup:

1. **Generate CPMK** — Menghasilkan Capaian Pembelajaran Mata Kuliah dari CPL dan deskripsi mata kuliah.
2. **Generate Sub-CPMK** — Menjabarkan CPMK menjadi Sub-CPMK per pertemuan.
3. **Validate** — Memvalidasi kelengkapan dan kesesuaian komponen RPS dengan prinsip OBE.
4. **Review** — Memberikan rekomendasi perbaikan RPS dari perspektif OBE.

Kami mengevaluasi tiga pendekatan: OpenAI GPT-4o (API komersial), self-hosted LLM (Llama, Mistral, Qwen via Ollama/vLLM), dan provider AI alternatif (Claude Anthropic, Google Gemini).

### Keputusan

Kami memutuskan untuk menggunakan **OpenAI API dengan model GPT-4o** sebagai engine AI utama.

### Konsekuensi

**Positif:**
- GPT-4o adalah model paling capable saat ini untuk tugas reasoning terstruktur (pemetaan CPL-CPMK, analisis OBE) dan generation (deskripsi akademik dalam Bahasa Indonesia).
- Mendukung Bahasa Indonesia dengan sangat baik, termasuk pemahaman konteks akademik dan terminologi OBE (CPL, CPMK, Sub-CPMK, taksonomi Bloom).
- JSON mode dengan structured output (JSON Schema) memungkinkan pemrosesan respons yang konsisten dan dapat di-parse secara programatis.
- Function calling untuk workflow yang lebih kompleks jika diperlukan di masa depan.
- Latency yang dapat diterima (<10 detik untuk generate CPMK, <30 detik untuk review lengkap).
- Skalabilitas managed — tidak perlu mengelola infrastruktur GPU.
- OpenAI PHP client library tersedia dan di-maintain oleh komunitas Laravel (openai-php/client).

**Negatif:**
- Biaya per API call — estimasi Rp 1.000-5.000 per operasi AI (masih dalam batas yang dapat diterima untuk nilai yang diberikan).
- Ketergantungan pada layanan pihak ketiga — jika OpenAI down, fitur AI tidak tersedia (diminimalkan dengan graceful fallback dan error handling).
- Data RPS dikirim ke server OpenAI — perlu memastikan tidak ada data PII (Personal Identifiable Information) dosen/mahasiswa yang ikut terkirim. Hanya mengirim data CPL, CPMK, deskripsi MK, dan struktur RPS.
- Rate limit API OpenAI perlu diperhitungkan (10 req/menit di level aplikasi, ditambah batasan OpenAI sendiri).

### Alternatif yang Dipertimbangkan

1. **Self-hosted LLM (Llama 3 70B via Ollama)**: Menghilangkan biaya API dan dependency pihak ketiga. Namun, memerlukan server GPU (minimal 2x A100 80GB untuk performa yang setara dengan GPT-4o) dengan biaya infrastruktur >Rp 20 juta/bulan. Kualitas reasoning dan pemahaman Bahasa Indonesia pada model open-source saat ini masih di bawah GPT-4o, terutama untuk tugas structured generation yang presisi.
2. **Claude Anthropic (Sonnet)**: Memiliki kemampuan reasoning yang sangat baik dan context window besar (200K tokens). Namun, kualitas Bahasa Indonesia sedikit di bawah GPT-4o dan ekosistem tooling/integrasi dengan PHP/Laravel kurang mature.
3. **Google Gemini Pro**: Pricing kompetitif dan multimodal capabilities. Namun, untuk use case structured output dan JSON mode, GPT-4o memiliki keunggulan reliability yang signifikan.

---

## ADR-007: Shared Database dengan tenant_id untuk Multi-Tenancy

- **Status**: Accepted
- **Tanggal**: 2025-01-11

### Konteks

Aplikasi RPS OBE adalah aplikasi SaaS multi-tenant yang melayani banyak universitas. Setiap universitas adalah satu tenant (berdasarkan `tenants` table) dan memerlukan isolasi data yang ketat. Kami mengevaluasi tiga strategi multi-tenancy:

1. **Shared Database + Shared Schema** (satu database, satu set tabel, difilter dengan `tenant_id`)
2. **Database per Tenant** (satu database terpisah untuk setiap universitas)
3. **Schema per Tenant** (satu database, satu schema PostgreSQL terpisah per tenant — tidak tersedia di MariaDB)

### Keputusan

Kami memutuskan untuk menggunakan strategi **Shared Database dengan tenant_id** (satu database, satu set tabel, difilter dengan kolom `tenant_id`).

### Konsekuensi

**Positif:**
- Implementasi paling sederhana — hanya menambahkan `tenant_id` ke setiap tabel yang perlu isolasi.
- Laravel Eloquent global scope (`TenantScope`) memastikan semua query otomatis difilter berdasarkan `tenant_id` user yang login, meminimalkan risiko human error (lupa memfilter query).
- Satu migration untuk seluruh tenant memudahkan maintenance schema update.
- Pool koneksi database tunggal — efisien dan tidak ada overhead multi-koneksi.
- Backup dan restore sederhana (satu database, satu dump file).
- Query lintas tenant (untuk admin global/super-admin) mudah dilakukan dengan `withoutGlobalScope()`.
- Cocok untuk skala tenant yang diharapkan (10-50 universitas di tahap awal, maksimal ~200 dalam 3 tahun).

**Negatif:**
- Isolasi data bergantung sepenuhnya pada disiplin implementasi global scope. Jika developer lupa menerapkan scope atau tidak menggunakan Eloquent (raw query), data bisa bocor.
- Ukuran tabel bersama tumbuh seiring penambahan tenant — untuk skala >500 tenant dengan data besar, performa index bisa terdegradasi. Namun, untuk skala yang diproyeksikan (<200 tenant, <1 juta record per tabel utama), MariaDB dengan indexing yang tepat akan menanganinya dengan baik.
- Tidak bisa melakukan disaster recovery per-tenant secara independen.
- Semua tenant harus menggunakan versi aplikasi yang sama (tidak bisa rolling update per tenant).

### Alternatif yang Dipertimbangkan

1. **Database per Tenant**: Memberikan isolasi data paling kuat dan memungkinkan disaster recovery per-tenant. Namun, kompleksitas operasional sangat tinggi: migration harus dijalankan di setiap database, connection pooling rumit (satu pool per tenant), backup/restore per database, dan tidak ada query lintas-tenant. Overkill untuk skala yang diproyeksikan.
2. **Schema per Tenant**: Strategi PostgreSQL-only sehingga tidak tersedia di MariaDB. Memberikan isolasi yang baik dengan tetap menggunakan satu database host. Namun, memerlukan PostgreSQL dan tidak kompatibel dengan keputusan ADR-003 (MariaDB).

---

## ADR-008: Menggunakan PHPWord dan DomPDF untuk Document Export

- **Status**: Accepted
- **Tanggal**: 2025-01-14

### Konteks

Aplikasi RPS OBE memerlukan fitur export RPS ke format dokumen yang dapat dicetak dan disimpan sebagai arsip akademik. Dua format yang dibutuhkan:

1. **DOCX** (Microsoft Word) — format yang paling umum digunakan dosen untuk mengedit lebih lanjut.
2. **PDF** — format final yang tidak dapat diedit, untuk tanda tangan digital dan distribusi resmi.

Kami mengevaluasi empat pendekatan: PHPWord + DomPDF, headless browser (Puppeteer/Playwright via Node subprocess), library PHP alternatif (TCPDF, mPDF), dan menggunakan template yang sudah jadi (fill-in placeholder).

### Keputusan

Kami memutuskan untuk menggunakan **PHPWord** untuk export DOCX dan **DomPDF** untuk export PDF.

### Konsekuensi

**Positif:**
- PHPWord menyediakan API yang kaya untuk manipulasi dokumen Word: heading, tabel, merge cells, text formatting, header/footer, page numbering, orientation, dan image insertion.
- DomPDF mendukung CSS 2.1 dan sebagian CSS 3 sehingga template PDF dapat dibuat menggunakan HTML + CSS yang sama dengan yang digunakan di aplikasi.
- Keduanya adalah library PHP native, tidak memerlukan eksternal binary atau service tambahan (seperti Chrome/Chromium untuk headless browser).
- Instalasi sederhana via Composer: `composer require phpoffice/phpword` dan `composer require dompdf/dompdf`.
- Tidak ada dependency Node.js di server production.
- Mendukung font Bahasa Indonesia (UTF-8) dengan baik.
- Template-based approach: template DOCX dengan placeholder dapat dibuat oleh user (admin tenant) dan diisi datanya oleh PHPWord secara programatis.

**Negatif:**
- DomPDF tidak mendukung CSS modern (Flexbox, Grid, CSS Variables). Layouting kompleks memerlukan table-based layout (old-school) atau absolute positioning.
- Rendering PDF dari HTML bisa tidak 100% akurat (WYSIWYG gap), memerlukan iterasi styling yang teliti.
- PHPWord tidak dapat mengkonversi DOCX ke PDF — dua library terpisah dengan dua template berbeda untuk format yang berbeda.
- File DOCX yang dihasilkan PHPWord bisa saja tidak fully compatible dengan format yang sangat spesifik (macro, tracked changes), namun untuk use case RPS ini cukup.
- Performa DomPDF menurun untuk dokumen yang sangat besar (>50 halaman). Untuk RPS yang tipikalnya 10-20 halaman, tidak menjadi masalah.

### Alternatif yang Dipertimbangkan

1. **Headless Browser (Puppeteer/Playwright)**: Menghasilkan PDF dengan rendering yang sempurna (identik dengan tampilan browser). Namun, memerlukan Node.js runtime dan Chrome/Chromium binary (~300MB) di server production, yang meningkatkan kompleksitas deployment dan konsumsi resource secara signifikan. Overkill untuk dokumen RPS yang tipikalnya sederhana secara layout.
2. **mPDF**: Alternatif DomPDF dengan dukungan CSS yang lebih baik dan native UTF-8. Namun, lisensinya LGPL (bukan MIT seperti DomPDF) dan community support lebih kecil. DomPDF sudah mencukupi untuk kebutuhan RPS.
3. **TCPDF**: Library paling tua dan stabil, namun API-nya low-level dan verbose. Membangun tabel RPS yang kompleks dengan TCPDF akan memakan sangat banyak kode.
