# 40 — Future Roadmap

## Ikhtisar

Peta jalan (roadmap) RPS OBE mendefinisikan visi evolusi produk selama 2+ tahun dalam lima fase — dari MVP hingga ekosistem penuh. Dokumen ini mencakup timeline visual Now > Next > Later, tujuan setiap fase, fitur kunci, metrik keberhasilan, proses permintaan fitur (voting pengguna, feedback board), dan rencana evolusi teknologi (framework upgrade, peningkatan infrastruktur).

---

## Timeline Visual: Now → Next → Later

`mermaid
timeline
    title RPS OBE — Product Roadmap 2026–2028
    section Now (Fase 1)
        Bulan 1-4 : MVP Core Platform
                  : Authentication & User Management
                  : Master Data (Univ, Fakultas, Prodi, Kurikulum, MK, CPL)
                  : RPS Builder 8-Step Wizard
                  : Mapping CPL→CPMK→SubCPMK→Assessment
                  : Workflow (Draft→Review→Revision→Approved→Published)
                  : Export Word & PDF
                  : Dashboard Dosen & Kaprodi
                  : Notification Email & In-App
                  : Auto-Versioning & Audit Log
                  : Default SN-DIKTI Template
    section Next (Fase 2-3)
        Bulan 5-10 : AI & Enterprise
                   : AI Assistant (Generate CPMK, Sub-CPMK, Assessment)
                   : AI Validator (8 Aspek Validasi)
                   : AI Reviewer (Skor, Komentar, Saran)
                   : Multi-Campus Management
                   : Advanced Dashboard & Reporting
                   : Template Builder & Batch Operations
                   : SSO Integration
    section Later (Fase 4-5)
        Bulan 11-24+ : Scale & Ecosystem
                      : MFA & Security Hardening
                      : Public REST API
                      : LMS Integration (Moodle, Canvas)
                      : Webhook & PWA
                      : Marketplace Template
                      : AI Custom Model Training
                      : Mobile Apps (iOS & Android)
                      : Internationalization (EN, AR)
                      : White Label Solution
                      : Active Directory Integration
`

---

## Fase 1 — MVP: Core Platform

**Periode:** Bulan 1–4 | **Tim:** 6 orang (2 Backend, 1 Frontend, 1 QA, 1 PM, 1 Designer)

### Tujuan

Membangun platform minimum yang dapat digunakan oleh satu universitas untuk menyusun, mereview, dan mengekspor RPS sesuai standar SN-DIKTI.

### Fitur Kunci

| Modul | Fitur | Detail |
|-------|-------|--------|
| **Authentication** | Login, Register via Invitation, Forgot Password | Sistem invitation-based; tidak ada self-registration publik |
| **User Management** | CRUD Users, Role Assignment, Invitation System | 4 role: Superadmin, Admin Tenant, Kaprodi, Dosen |
| **Master Data** | Universitas, Fakultas, Prodi, Kurikulum, Semester | Setup awal oleh Admin Tenant |
| **Master Data** | Mata Kuliah, Dosen, CPL, Profil Lulusan | Import CSV untuk data awal |
| **RPS Builder** | 8-Step Wizard dengan auto-save dan inline validation | Step 1: Info MK → Step 8: Review & Submit |
| **Mapping** | CPL → CPMK → Sub-CPMK → Assessment | Drag-and-drop atau form-based mapping |
| **Workflow** | Draft → Review → Revision → Approved → Published → Archived | Status transition dengan permission gate |
| **Export** | Word (.docx) dan PDF (.pdf) | PHPWord / DomPDF; template dokumen sesuai SN-DIKTI |
| **Dashboard** | Dosen (RPS saya, status, deadline) dan Kaprodi (semua RPS prodi, statistik) | Chart sederhana (counter + bar chart) |
| **Notification** | Email (SMTP) + In-App (database) | Notifikasi: invitation, review request, approval, rejection |
| **Versioning** | Auto-version pada setiap submit; version history viewer | Semantic versioning (v1.0.0); diff viewer |
| **Audit Log** | Semua aktivitas CRUD tercatat | Audit trail compliance BAN-PT |
| **Template** | Default SN-DIKTI template untuk export | Template dokumen RPS sesuai format Kemenristekdikti |

### Success Metrics

| Metrik | Target |
|--------|--------|
| Active tenant (1 universitas) | 1 |
| RPS created | ≥ 10 |
| Average RPS creation time | < 4 jam |
| Review cycle duration | < 7 hari |
| Export success rate | 100% |
| Critical bugs in production | 0 |
| User satisfaction (CSAT) | ≥ 4.0 / 5.0 |

---

## Fase 2 — AI: Artificial Intelligence Integration

**Periode:** Bulan 5–7 | **Tim:** 7 orang (+1 AI/ML Engineer)

### Tujuan

Mengintegrasikan kecerdasan buatan untuk mempercepat penyusunan RPS, meningkatkan kualitas melalui validasi otomatis, dan memberikan review AI untuk mengurangi beban Kaprodi.

### Fitur Kunci

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| **AI Assistant — Generate CPMK** | Dosen memasukkan CPL terpilih, AI menghasilkan daftar CPMK | P0 — Must Have |
| **AI Assistant — Generate Sub-CPMK** | AI menghasilkan Sub-CPMK dari CPMK dengan level taksonomi dan pertemuan | P0 — Must Have |
| **AI Assistant — Generate Assessment** | AI menghasilkan assessment plan + bobot + rubrik | P1 — Should Have |
| **AI Assistant — Generate Materi** | AI menghasilkan materi per pertemuan | P1 — Should Have |
| **AI Assistant — Generate Referensi** | AI menghasilkan daftar referensi (format APA) | P2 — Could Have |
| **AI Validator** | 8 aspek validasi: Taksonomi Bloom, Constructive Alignment, Jumlah CPMK, Pertemuan, Assessment Distribution, Bobot, Referensi, Konsistensi | P0 — Must Have |
| **AI Reviewer** | AI memberikan skor, komentar per komponen, dan saran perbaikan | P1 — Should Have |
| **Streaming Response** | Tampilan hasil AI dengan typing effect (streaming dari API) | P2 — Could Have |
| **AI Cost Management** | Budget per tenant, rate limiting, cost tracking dashboard | P0 — Must Have |
| **Prompt Management** | Semua prompt disimpan sebagai file; version-controlled | P0 — Must Have |

### Success Metrics

| Metrik | Target |
|--------|--------|
| AI Generate adoption rate | ≥ 60% RPS menggunakan AI Generate |
| AI Validator adoption rate | ≥ 50% RPS divalidasi AI sebelum submit |
| AI quality score vs human review | AI score correlation ≥ 0.75 dengan human review |
| Average time to create RPS (with AI) | < 2 jam (dari 4 jam di Fase 1) |
| AI cost per RPS | < Rp 15.000 |
| AI error rate | < 2% |

### Technology Evolution

- Integrasi OpenAI API (GPT-4o-mini untuk generate, GPT-4o untuk review)
- Multi-provider support (Azure OpenAI, Google Gemini)
- AI Gateway service dengan retry, caching, dan circuit breaker
- Model observability (token usage, cost, latency, success rate per action type)

---

## Fase 3 — Enterprise: Multi-Tenant & Advanced Features

**Periode:** Bulan 8–10 | **Tim:** 8 orang (+1 DevOps)

### Tujuan

Mengembangkan fitur enterprise untuk mendukung multi-kampus, dashboard analitik lanjutan, template builder, batch operations, dan integrasi SSO — memungkinkan scaling ke 10+ tenant.

### Fitur Kunci

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| **Multi-Campus Management** | Satu universitas dengan banyak fakultas; admin fakultas mandiri; data terisolasi per fakultas (opsional) | P0 — Must Have |
| **Advanced Dashboard** | Dashboard analitik untuk LPM dan Rektorat; tren penyusunan RPS, compliance rate, AI usage stats | P0 — Must Have |
| **Advanced Reporting** | Laporan compliance BAN-PT; rekap RPS per prodi; laporan akreditasi | P0 — Must Have |
| **Template Builder** | Admin dapat membuat dan mengkustomisasi template export (Word/PDF) dengan drag-and-drop builder | P1 — Should Have |
| **Batch Operations** | Batch export (pilih > 1 RPS → export ZIP); batch status change; batch assign reviewer | P1 — Should Have |
| **SSO Integration** | SAML 2.0 / OAuth 2.0 / OpenID Connect untuk integrasi dengan sistem universitas | P1 — Should Have |
| **Custom Workflow** | Admin tenant dapat mendefinisikan custom workflow (jumlah step review, approval chain) | P2 — Could Have |
| **Audit Report Generator** | Generate laporan audit compliance (BAN-PT) otomatis dalam format PDF | P2 — Could Have |
| **Role-Based Dashboard** | Dashboard berbeda untuk setiap role: LPM, Rektorat, Dekan, Kaprodi, Dosen | P1 — Should Have |

### Success Metrics

| Metrik | Target |
|--------|--------|
| Active tenants | ≥ 10 universitas |
| Active users | ≥ 200 |
| RPS created through platform | ≥ 500 |
| Feature adoption — AI | ≥ 75% pengguna aktif menggunakan AI |
| Feature adoption — Template Builder | ≥ 40% tenant membuat minimal 1 custom template |
| NPS | ≥ 50 |
| Monthly churn rate | < 3% |
| System uptime | ≥ 99.9% |

### Technology Evolution

- Database read replicas untuk scaling query dashboard
- Redis cluster untuk session dan cache yang lebih resilient
- Queue worker scaling (Laravel Horizon auto-scaling)
- Horizontal scaling untuk web servers
- IaC (Infrastructure as Code) — Terraform/Ansible untuk provisioning otomatis
- CI/CD pipeline matang dengan automated testing + deployment

---

## Fase 4 — Scale: Platform Maturity & Integration

**Periode:** Bulan 11–13 | **Tim:** 9 orang (+1 Integration Engineer)

### Tujuan

Mencapai kematangan platform dengan keamanan tingkat lanjut (MFA), public API untuk integrasi eksternal, koneksi LMS (Moodle, Canvas), webhook untuk event-driven automation, dan PWA untuk akses mobile.

### Fitur Kunci

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| **Multi-Factor Authentication (MFA)** | TOTP (Google Authenticator) dan/atau WebAuthn; mandatory untuk admin role | P0 — Must Have |
| **Public REST API** | API terdokumentasi (OpenAPI/Swagger) dengan token-based authentication; rate limiting; versioning | P0 — Must Have |
| **LMS Integration — Moodle** | Sinkronisasi data MK, dosen, mahasiswa; embed RPS viewer di Moodle course page; LTI 1.3 | P1 — Should Have |
| **LMS Integration — Canvas** | Sama dengan Moodle; Canvas API integration | P2 — Could Have |
| **Webhook System** | Event-driven webhook untuk notifikasi ke sistem eksternal (RPS published → trigger webhook) | P1 — Should Have |
| **Progressive Web App (PWA)** | Offline support untuk view RPS; installable di homescreen; push notification | P1 — Should Have |
| **Granular Permissions** | Permission matrix yang dapat dikustomisasi per tenant; role builder untuk admin | P2 — Could Have |
| **Advanced Search** | Full-text search RPS, CPMK, dan MK; filter multi-kriteria; saved search | P2 — Could Have |
| **API Rate Limiting & Throttling** | Tiered API access per paket tenant; usage dashboard; quota alerts | P1 — Should Have |

### Success Metrics

| Metrik | Target |
|--------|--------|
| Active tenants | ≥ 25 universitas |
| API integrations active | ≥ 50% tenant menggunakan API atau webhook |
| LMS integrated tenants | ≥ 30% tenant terhubung dengan LMS |
| MFA adoption (admin users) | 100% wajib |
| PWA install rate | ≥ 15% monthly active users |
| API uptime | ≥ 99.95% |
| Incident response time (P1) | < 30 menit |

### Technology Evolution

- API Gateway untuk manajemen dan monitoring API traffic
- OpenAPI specification auto-generated dari kode (Laravel Scramble / Scribe)
- LTI 1.3 Advantage implementation
- Service worker untuk PWA offline support
- Push notification via Web Push API
- API versioning strategy (URL-based: /api/v1/, /api/v2/)

---

## Fase 5 — Ecosystem: Marketplace & Global Expansion

**Periode:** Tahun 2+ (Bulan 14–24+) | **Tim:** 10+ orang

### Tujuan

Membangun ekosistem di sekitar platform — marketplace template, pelatihan model AI kustom, aplikasi mobile native, internasionalisasi, white label, dan integrasi Active Directory — untuk ekspansi ke pasar global.

### Fitur Kunci

| Fitur | Deskripsi | Prioritas |
|-------|-----------|-----------|
| **Marketplace Template** | Komunitas berbagi template RPS, assessment, dan rubrik; rating dan review; free + premium template | P1 — Should Have |
| **AI Custom Model Training** | Tenant dapat melatih AI dengan data RPS mereka sendiri; fine-tuning model per universitas; domain-specific prompts | P2 — Could Have |
| **Mobile Apps (iOS & Android)** | Native mobile apps dengan push notification; offline mode; barcode/QR scanner untuk presensi | P2 — Could Have |
| **Internationalization (i18n)** | Multi-language support: Bahasa Indonesia, English, Arabic; RTL layout support | P2 — Could Have |
| **White Label Solution** | Tenant dapat menggunakan domain, logo, dan warna brand mereka sendiri; fully customizable UI | P2 — Could Have |
| **Active Directory / LDAP Integration** | Sinkronisasi user dan role dari AD/LDAP universitas; auto-provisioning dan de-provisioning | P2 — Could Have |
| **Plagiarism Checker Integration** | Integrasi dengan Turnitin atau Grammarly API untuk pengecekan originalitas konten RPS | P3 — Nice to Have |
| **Collaborative Editing** | Real-time collaborative RPS editing; comment dan suggestion system seperti Google Docs | P3 — Nice to Have |
| **Analytics Benchmarking** | Anonymous benchmarking antar universitas; insight \agaimana performa Anda dibanding institusi sejenis\ | P3 — Nice to Have |

### Success Metrics

| Metrik | Target |
|--------|--------|
| Active tenants | ≥ 50 universitas |
| Active users | ≥ 1.000 |
| Marketplace templates | ≥ 100 templates; ≥ 20 oleh komunitas |
| International tenants | ≥ 5 universitas non-Indonesia |
| Mobile app downloads | ≥ 500 installs |
| White label adoption | ≥ 30% tenant enterprise |
| ARR (Annual Recurring Revenue) | ≥ Rp 2.000.000.000 |
| NPS | ≥ 60 |

### Technology Evolution

- Microservices untuk scaling komponen independen (AI service, Export service, Notification service)
- Kubernetes orchestration untuk container management
- CDN global untuk static assets dan PWA
- Multi-region deployment (Asia Tenggara, Timur Tengah)
- GraphQL API sebagai alternatif REST untuk query kompleks
- Event-driven architecture dengan message broker (RabbitMQ / Kafka)
- Real-time collaboration via WebSocket (Laravel Reverb / Pusher)
- Mobile CI/CD pipeline (Fastlane untuk iOS, GitHub Actions untuk Android)

---

## Feature Request Process

### User Feedback Loop

`mermaid
graph LR
    A[Pengguna<br/>Submit Ide] --> B[Feedback Board<br/>Public / In-App]
    B --> C[Tim Produk<br/>Review and Triage]
    C --> D{Status}
    D -->|Under Review| E[Prioritization<br/>Value vs Effort]
    D -->|Planned| F[Assign ke Phase<br/>and Sprint]
    D -->|Not Planned| G[Arsip +<br/>Feedback ke User]
    E --> F
    F --> H[Development]
    H --> I[Release]
    I --> J[Notifikasi ke<br/>User yang Request]
    I --> K[Update<br/>Feedback Board]

    style A fill:#e3f2fd,color:#000
    style I fill:#4caf50,color:#fff
`

### Feedback Board — Kategori

| Kategori | Deskripsi | Handling |
|----------|-----------|----------|
| **Bug Report** | Bug yang dilaporkan pengguna | Triase dalam 24 jam; fix sesuai severity |
| **Feature Request** | Permintaan fitur baru | Voting oleh pengguna; top-voted direview setiap bulan |
| **Improvement** | Saran perbaikan fitur existing | Review dalam sprint planning |
| **Integration Request** | Permintaan integrasi dengan sistem lain | Evaluasi technical feasibility + market demand |

### Voting System

| Mekanisme | Deskripsi |
|-----------|-----------|
| **Upvote** | Setiap user dapat upvote maksimal 10 feature request per bulan |
| **Comment** | Diskusi dan use case tambahan di kolom komentar |
| **Subscribe** | Notifikasi saat status feature request berubah |
| **Transparency** | Setiap feature request memiliki status publik: Under Review → Planned → In Progress → Released → Not Planned |

---

## Technology Evolution Timeline

`mermaid
gantt
    title Technology Evolution — RPS OBE 2026–2028
    dateFormat YYYY-MM
    axisFormat %b %Y

    section Foundation
    Laravel 11 + PHP 8.3              :done, 2026-04, 2026-08
    MariaDB 11 + Redis 7             :done, 2026-04, 2026-08
    Livewire 3 + Alpine.js           :done, 2026-04, 2026-08
    Tabler UI + Bootstrap 5          :done, 2026-04, 2026-08

    section AI Integration
    OpenAI GPT-4o-mini / GPT-4o      :active, 2026-09, 2026-11
    Multi-Provider AI Gateway        :active, 2026-10, 2026-12
    AI Cost Tracking and Observability :active, 2026-09, 2026-11
    Prompt Version Control           :active, 2026-09, 2026-11

    section Enterprise
    Read Replicas (MariaDB)          :2026-12, 2027-02
    Redis Cluster                    :2026-12, 2027-02
    Horizon Auto-Scaling             :2027-01, 2027-02
    SAML 2.0 / OIDC SSO             :2027-01, 2027-03
    Terraform / Ansible IaC          :2026-11, 2027-02

    section Scale
    Public API (REST)                :2027-03, 2027-05
    LTI 1.3 Advantage                :2027-04, 2027-06
    Webhook System                   :2027-04, 2027-06
    PWA + Service Workers            :2027-05, 2027-07
    API Gateway                      :2027-05, 2027-07

    section Ecosystem
    Microservices Architecture       :2027-08, 2028-02
    Kubernetes Orchestration         :2027-08, 2028-03
    GraphQL API                      :2027-10, 2028-02
    Message Broker (RabbitMQ/Kafka)  :2027-10, 2028-03
    Multi-Region Deployment          :2028-01, 2028-06
`

### Planned Upgrades

| Komponen | Saat Ini | Upgrade Ke | Target Fase |
|----------|----------|------------|-------------|
| Laravel | 11.x | 12.x (LTS) | Fase 3 |
| PHP | 8.3 | 8.4 | Fase 3 |
| MariaDB | 11.4 | 11.7 (LTS) | Fase 4 |
| Redis | 7.2 | 7.4+ / Valkey | Fase 4 |
| Bootstrap | 5.3 | 5.x (latest stable) | Fase 3 |
| Livewire | 3.x | 3.x (latest) | Continuous |
| Alpine.js | 3.x | 3.x (latest) | Continuous |
| Composer | 2.x | 2.x (latest) | Continuous |
| Node.js | 20 LTS | 22 LTS | Fase 4 |

---

**Navigasi:** [Sebelumnya: Risk Analysis](39-risk-analysis.md) | [Daftar Isi](../README.md) | [Berikutnya: MVP Definition](41-mvp-definition.md)
