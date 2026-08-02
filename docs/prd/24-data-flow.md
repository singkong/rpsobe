# 24 — Data Flow

## Diagram Aliran Data (Data Flow Diagrams)

Bab ini mendeskripsikan aliran data untuk proses-proses utama dalam sistem RPS OBE. Setiap diagram aliran data menjelaskan bagaimana data bergerak dari titik awal hingga akhir, termasuk interaksi dengan sistem eksternal dan penyimpanan data.

---

## 1. User Registration Flow

```mermaid
sequenceDiagram
    participant Admin as Admin/Kaprodi
    participant System as RPS OBE
    participant Mail as Mail Server
    participant User as User Baru

    Admin->>System: POST /api/v1/users/invite<br/>{email, role, prodi_id}
    System->>System: Validasi input
    System->>System: Generate invitation_code (UUID)
    System->>System: Simpan di tabel invitations<br/>{email, code, role, tenant_id, prodi_id, expires_at}
    System->>Mail: Kirim email undangan
    Mail-->>User: Email: "Anda diundang bergabung<br/>di RPS OBE [Nama Univ]<br/>Kode: INV-2026-AB12CD<br/>Link: /register?code=INV-2026-AB12CD"
    User->>System: Buka halaman register<br/>dengan invitation_code
    System->>System: Validasi invitation_code<br/>(exists, not expired, not used)
    alt Kode valid
        System-->>User: Form registrasi
        User->>System: Isi form: nama, password, NIDN
        System->>System: Validasi input
        System->>System: Buat user di tabel users<br/>{name, email, password, nidn, tenant_id, prodi_id}
        System->>System: Assign role (dari invitation)
        System->>System: Tandai invitation sebagai "used"
        System->>System: Kirim email verifikasi
        Mail-->>User: Email verifikasi
        User->>System: Verifikasi email
        System->>System: Tandai email_verified_at
        System-->>User: Registrasi berhasil, redirect ke login
    else Kode tidak valid/expired
        System-->>User: Error: "Kode undangan tidak valid<br/>atau sudah kadaluarsa"
    end
```

### Data yang Terlibat

| Tabel | Operasi | Data |
|-------|---------|------|
| `invitations` | INSERT | email, invitation_code, role, tenant_id, prodi_id, expires_at |
| `invitations` | UPDATE | status = 'used', used_at = NOW() |
| `users` | INSERT | name, email, password, nidn, tenant_id, prodi_id |
| `model_has_roles` | INSERT | role_id, model_type, model_id |

---

## 2. RPS Creation Data Flow (8-Step Wizard)

```mermaid
sequenceDiagram
    participant Dosen as Dosen
    participant Wizard as Builder Wizard
    participant RPS_SVC as RPSService
    participant DB as MariaDB
    participant Cache as Redis

    Note over Dosen,Cache: Step 1: Informasi Mata Kuliah
    Dosen->>Wizard: Pilih MK, Kurikulum, Semester, Dosen Pengampu
    Wizard->>RPS_SVC: createRPS(data)
    RPS_SVC->>DB: INSERT INTO rps<br/>{mata_kuliah_id, kurikulum_id, semester_id, user_id, tenant_id, status='draft'}
    DB-->>RPS_SVC: rps_id
    RPS_SVC->>DB: INSERT INTO rps_dosen_pengampu<br/>{rps_id, dosen_id}
    RPS_SVC-->>Wizard: RPS berhasil dibuat (progress: 5%)

    Note over Dosen,Cache: Step 2: Pilih CPL
    Dosen->>Wizard: Centang CPL yang didukung
    Wizard->>RPS_SVC: attachCPL(rps_id, cpl_ids)
    RPS_SVC->>DB: Validasi CPL milik prodi & kurikulum
    RPS_SVC->>DB: INSERT INTO rps_cpl (multiple)<br/>{rps_id, cpl_id}
    RPS_SVC-->>Wizard: CPL tersimpan (progress: 15%)

    Note over Dosen,Cache: Step 3: CPMK
    Dosen->>Wizard: Input CPMK atau klik "Generate AI"
    Wizard->>RPS_SVC: saveCPMK(rps_id, cpmk_data)
    RPS_SVC->>RPS_SVC: Validasi: setiap CPMK terkait min 1 CPL
    RPS_SVC->>DB: UPSERT INTO cpmk<br/>{rps_id, kode, deskripsi, level_taksonomi}
    RPS_SVC->>DB: UPSERT INTO cpmk_cpl<br/>{cpmk_id, cpl_id}
    RPS_SVC-->>Wizard: CPMK tersimpan (progress: 25%)

    Note over Dosen,Cache: Step 4: Sub-CPMK
    Dosen->>Wizard: Input Sub-CPMK per pertemuan
    Wizard->>RPS_SVC: saveSubCPMK(rps_id, subcpmk_data)
    RPS_SVC->>RPS_SVC: Validasi: setiap Sub-CPMK terkait CPMK
    RPS_SVC->>DB: UPSERT INTO sub_cpmk<br/>{rps_id, cpmk_id, kode, deskripsi, level_taksonomi, pertemuan}
    RPS_SVC-->>Wizard: Sub-CPMK tersimpan (progress: 35%)

    Note over Dosen,Cache: Step 5: Materi Pembelajaran
    Dosen->>Wizard: Input materi per pertemuan
    Wizard->>RPS_SVC: saveMateri(rps_id, materi_data)
    RPS_SVC->>DB: UPSERT INTO materi_pertemuan<br/>{rps_id, pertemuan, pokok_bahasan, estimasi_waktu}
    RPS_SVC->>DB: UPSERT INTO materi_sub_cpmk<br/>{materi_id, sub_cpmk_id}
    RPS_SVC-->>Wizard: Materi tersimpan (progress: 50%)

    Note over Dosen,Cache: Step 6: Metode & Aktivitas Pembelajaran
    Dosen->>Wizard: Input metode & aktivitas per pertemuan
    Wizard->>RPS_SVC: saveMetode(rps_id, metode_data)
    RPS_SVC->>DB: UPDATE materi_pertemuan<br/>SET metode_pembelajaran, aktivitas_pembelajaran
    RPS_SVC-->>Wizard: Metode tersimpan (progress: 65%)

    Note over Dosen,Cache: Step 7: Assessment
    Dosen->>Wizard: Input assessment, bobot, rubrik
    Wizard->>RPS_SVC: saveAssessment(rps_id, assessment_data)
    RPS_SVC->>RPS_SVC: Validasi: total bobot = 100%
    RPS_SVC->>DB: UPSERT INTO assessment<br/>{rps_id, nama, jenis, bobot}
    RPS_SVC->>DB: UPSERT INTO assessment_sub_cpmk<br/>{assessment_id, sub_cpmk_id}
    RPS_SVC->>DB: UPSERT INTO rubrik<br/>{assessment_id, kriteria, skala}
    RPS_SVC-->>Wizard: Assessment tersimpan (progress: 85%)

    Note over Dosen,Cache: Step 8: Review & Finalisasi
    Dosen->>Wizard: Input referensi, catatan
    Wizard->>RPS_SVC: saveReferensi(rps_id, referensi_data)
    RPS_SVC->>DB: UPSERT INTO referensi<br/>{rps_id, format_apa, jenis, sumber}
    Dosen->>Wizard: Klik "Selesai" atau "Ajukan Review"
    Wizard->>RPS_SVC: finalize(rps_id)
    RPS_SVC->>RPS_SVC: Jalankan validasi kelengkapan
    RPS_SVC->>Cache: Hapus cache rps:draft:{rps_id}
    RPS_SVC->>Cache: Invalidate query cache terkait
    RPS_SVC-->>Wizard: Progress 100%, siap review
```

### Auto-Save Mechanism

```mermaid
sequenceDiagram
    participant Wizard as Builder Wizard
    participant Livewire as Livewire Component
    participant Redis as Redis Cache
    participant DB as MariaDB

    loop Setiap 30 detik (debounced)
        Wizard->>Livewire: Perubahan form terdeteksi
        Livewire->>Redis: SET rps:draft:{rps_id}:{step}<br/>data form terbaru (serialized)
        Redis-->>Livewire: OK
    end

    Dosen->>Wizard: Klik "Simpan" atau "Next Step"
    Wizard->>Livewire: Trigger save
    Livewire->>Redis: GET rps:draft:{rps_id}:{step}
    Redis-->>Livewire: Data terbaru
    Livewire->>DB: UPSERT data ke database
    Livewire->>Redis: DEL rps:draft:{rps_id}:{step}
    Livewire-->>Wizard: Data tersimpan

    Note over Wizard, DB: Jika browser crash, data masih ada di Redis (TTL: 1 jam)
    Note over Wizard, DB: Saat user kembali, Livewire restore dari Redis
```

---

## 3. Review Cycle Data Flow

```mermaid
sequenceDiagram
    participant Dosen as Dosen
    participant System as RPSService
    participant DB as MariaDB
    participant Reviewer as Reviewer
    participant Kaprodi as Kaprodi
    participant Mail as Mail Server

    Note over Dosen,Mail: Fase 1: Submit Review
    Dosen->>System: submitForReview(rps_id)
    System->>DB: Validasi kelengkapan<br/>(progress=100%, bobot=100%, ref≥3)
    System->>DB: UPDATE rps SET status='review'
    System->>DB: INSERT INTO rps_versions<br/>{rps_id, version='v2.0', snapshot_data}
    System->>DB: INSERT INTO audit_logs<br/>{action='submit_review', rps_id, user_id}
    System->>Mail: Kirim email ke Kaprodi & Reviewer

    Note over Dosen,Mail: Fase 2: Penugasan Reviewer
    Kaprodi->>System: assignReviewer(rps_id, reviewer_id)
    System->>DB: UPDATE rps SET reviewer_id
    System->>DB: INSERT INTO audit_logs<br/>{action='assign_reviewer', rps_id}
    System->>Mail: Kirim email ke Reviewer<br/>"Anda ditugaskan mereview RPS [nama MK]"

    Note over Dosen,Mail: Fase 3: Review
    Reviewer->>System: Buka RPS (status=review)
    System->>DB: SELECT rps + semua relasi
    System-->>Reviewer: RPS lengkap (read-only)
    Reviewer->>System: Jalankan AI Validator (opsional)
    System->>System: Panggil OpenAI, parsing hasil
    System-->>Reviewer: Hasil validasi 8 aspek
    alt Reviewer Setuju
        Reviewer->>System: approve(rps_id, {skor, komentar})
        System->>DB: INSERT INTO reviews<br/>{rps_id, reviewer_id, skor, komentar, status='approved'}
        System->>DB: UPDATE rps SET status='approved'
        System->>DB: INSERT INTO audit_logs
        System->>Mail: Email ke Dosen: "RPS disetujui<br/>oleh reviewer"
        System->>Mail: Email ke Kaprodi: "RPS siap<br/>dipublikasikan"
    else Reviewer Minta Revisi
        Reviewer->>System: requestRevision(rps_id, {alasan, komentar})
        System->>DB: INSERT INTO reviews<br/>{rps_id, reviewer_id, komentar, status='revision_requested'}
        System->>DB: UPDATE rps SET status='revision'
        System->>DB: INSERT INTO audit_logs
        System->>Mail: Email ke Dosen: "RPS perlu revisi:<br/>{alasan}"
    end

    Note over Dosen,Mail: Fase 4: Revisi (jika diperlukan)
    Dosen->>System: Buka RPS (status=revision)
    System-->>Dosen: Komentar reviewer + RPS (editable)
    Dosen->>System: Edit sesuai masukan
    Dosen->>System: Simpan, status otomatis → 'draft'
    Dosen->>System: submitForReview(rps_id) (lagi)
    Note over Dosen,System: Kembali ke Fase 1

    Note over Dosen,Mail: Fase 5: Publikasi
    Kaprodi->>System: publish(rps_id)
    System->>DB: UPDATE rps SET status='published', published_at=NOW()
    System->>DB: INSERT INTO rps_versions<br/>{rps_id, version='v1.0-final', immutable=true}
    System->>DB: INSERT INTO audit_logs
    System->>Mail: Email ke Dosen & Reviewer
    System-->>Kaprodi: RPS dipublikasikan
```

---

## 4. AI Generation Data Flow

```mermaid
sequenceDiagram
    participant User as User (Dosen)
    participant Frontend as Livewire UI
    participant Gateway as AIGatewayService
    participant Cache as Redis
    participant Queue as Redis Queue
    participant Worker as Queue Worker
    participant OpenAI as OpenAI API
    participant DB as MariaDB

    User->>Frontend: Klik "Generate CPMK dengan AI"
    Frontend->>Gateway: generate(type='cpmk', context)

    Gateway->>Gateway: Check rate limit
    alt Rate limit exceeded
        Gateway-->>Frontend: 429: Too Many Requests
        Frontend-->>User: Error: "Terlalu banyak request.<br/>Coba lagi dalam {n} detik."
    end

    Gateway->>Gateway: Check budget tenant
    alt Budget exceeded
        Gateway-->>Frontend: AI_001: Kuota AI habis
        Frontend-->>User: Error: "Kuota AI bulan ini habis."
    end

    Gateway->>Cache: GET ai:response:cpmk:{hash_context}
    alt Cache hit
        Cache-->>Gateway: Cached result
        Gateway-->>Frontend: Langsung return hasil
        Frontend-->>User: Tampilkan hasil (instant)
    else Cache miss
        Gateway->>Gateway: Build prompt dari template
        Gateway->>Queue: dispatch(GenerateCPMKJob)
        Gateway-->>Frontend: 202 Accepted {job_id}
        Frontend-->>User: "AI sedang bekerja..." (spinner)

        Queue->>Worker: Proses GenerateCPMKJob
        Worker->>Gateway: executeAICall()
        Gateway->>OpenAI: POST /v1/chat/completions<br/>{model: 'gpt-4o', messages, temperature, max_tokens}
        OpenAI-->>Gateway: Stream response (tokens)
        Gateway->>Gateway: Parse response ke JSON
        Gateway->>Cache: SET ai:response:cpmk:{hash}<br/>TTL: 60 menit
        Gateway->>DB: INSERT INTO ai_usage_logs<br/>{tenant_id, user_id, type, tokens_used, cost}
        Gateway->>DB: UPDATE tenant SET ai_budget_used += cost
        Gateway->>DB: UPDATE ai_jobs SET status='completed', result=...
        Worker-->>Queue: Done
    end

    loop Polling setiap 3 detik
        Frontend->>Gateway: GET /api/v1/ai/jobs/{job_id}
        Gateway->>DB: Check status
        alt completed
            Gateway-->>Frontend: Result data
            Frontend-->>User: Tampilkan hasil<br/>(typing animation)
        else processing
            Gateway-->>Frontend: Status: processing
            Frontend-->>User: Loading bar update
        else failed
            Gateway-->>Frontend: Error message
            Frontend-->>User: "Generate gagal. Coba lagi."
        end
    end
```

### AI Cost Tracking Data Flow

```mermaid
graph LR
    A[AI Request] --> B{Check Budget}
    B -->|Available| C[Call OpenAI API]
    B -->|Exceeded| D[Reject Request]
    C --> E[Log Usage]
    E --> F[(ai_usage_logs)]
    E --> G[(tenants)]
    F --> H[Dashboard Stats]
    G --> H
    H --> I[Admin Alert if > 80%]
```

---

## 5. Export Generation Data Flow

```mermaid
sequenceDiagram
    participant User as User
    participant UI as Export UI
    participant Service as ExportService
    participant Template as TemplateService
    participant Queue as Redis Queue
    participant Worker as Queue Worker
    participant Storage as File Storage
    participant Mail as Mail Server

    User->>UI: Klik "Export Word" atau "Export PDF"
    UI->>Service: exportWord(rps_id, options)

    Service->>Service: Check rate limit (5 req/menit)
    Service->>Service: Ambil data RPS lengkap dari DB
    Service->>Template: getTemplate(tenant_id)
    Template-->>Service: Template .docx / HTML template

    Service->>Queue: dispatch(WordExportJob)<br/>{rps_id, data, template, options}
    Service-->>UI: 202 Accepted {job_id}
    UI-->>User: "Export sedang diproses..."<br/>(progress bar)

    Queue->>Worker: Proses WordExportJob

    alt Word Export
        Worker->>Worker: PHPWord: load template .docx
        Worker->>Worker: Isi placeholder dengan data RPS
        Worker->>Worker: Tambahkan cover page
        Worker->>Worker: Tambahkan tabel CPL, CPMK, Sub-CPMK
        Worker->>Worker: Tambahkan tabel assessment
        Worker->>Worker: Tambahkan halaman pengesahan
        Worker->>Worker: Tambahkan watermark jika draft
        Worker->>Storage: Simpan file .docx ke storage/exports/
    else PDF Export
        Worker->>Worker: Render Blade template dengan data RPS
        Worker->>Worker: DomPDF: HTML → PDF
        Worker->>Storage: Simpan file .pdf ke storage/exports/
    end

    Worker->>Worker: Generate download URL with hash
    Worker->>DB: UPDATE export_jobs<br/>SET status='completed', file_path='...', download_hash='...'

    loop Polling
        UI->>Service: GET /api/v1/export/jobs/{job_id}
        Service->>DB: Check status
        alt completed
            Service-->>UI: Download URL
            UI-->>User: Tombol "Download" muncul
            User->>UI: Klik Download
            UI->>Storage: Stream file binary
            Storage-->>User: File .docx / .pdf
        else failed
            Service-->>UI: Error message
        end
    end

    Note over Storage: File otomatis dihapus setelah 24 jam<br/>(scheduled job: cleanup:temp-exports)
```

### Batch Export Data Flow

```mermaid
graph TD
    A[User: Pilih multiple RPS] --> B[ExportService: batchExport]
    B --> C[Queue: BatchExportJob]
    C --> D[Worker: Loop setiap RPS]
    D --> E1[Export RPS 1 - WordExportJob]
    D --> E2[Export RPS 2 - WordExportJob]
    D --> E3[Export RPS N - WordExportJob]
    E1 --> F1[storage/exports/rps1.docx]
    E2 --> F2[storage/exports/rps2.docx]
    E3 --> F3[storage/exports/rpsN.docx]
    F1 --> G[Worker: Create ZIP archive]
    F2 --> G
    F3 --> G
    G --> H[storage/exports/batch_xxx.zip]
    H --> I[User: Download ZIP]
```

---

## 6. Notification Data Flow

```mermaid
sequenceDiagram
    participant Action as System Action
    participant Observer as RPSObserver
    participant Event as Domain Event
    participant Listener as Event Listener
    participant NotifSvc as NotificationService
    participant DB as MariaDB
    participant Queue as Redis Queue
    participant Mail as Mail Server
    participant UI as Notification Center UI

    Note over Action,UI: Trigger: RPS disubmit untuk review

    Action->>Observer: RPS::submitForReview()
    Observer->>Event: dispatch(RPSSubmittedForReview(rps))
    Event->>Listener: SendReviewNotification::handle()

    Listener->>NotifSvc: notify(rps, event='rps.submitted')
    NotifSvc->>NotifSvc: Tentukan recipients:<br/>- Kaprodi (prodi RPS)<br/>- Reviewer (jika sudah assigned)

    loop Untuk setiap recipient
        NotifSvc->>DB: INSERT INTO notifications<br/>{user_id, type='rps_submitted', rps_id,<br/>title, message, metadata}
        NotifSvc->>Queue: dispatch(SendEmailJob)<br/>{recipient_email, notification_type, data}
    end

    Queue->>Mail: Kirim email ke setiap recipient
    Mail-->>Queue: Email terkirim
    Queue->>DB: UPDATE notifications SET email_sent=true

    User->>UI: Buka Notification Center
    UI->>DB: SELECT * FROM notifications<br/>WHERE user_id = ? ORDER BY created_at DESC
    DB-->>UI: List notifikasi
    UI-->>User: Tampilkan notifikasi (badge + list)

    User->>UI: Klik notifikasi
    UI->>DB: UPDATE notifications SET read_at = NOW()
    UI->>UI: Redirect ke halaman terkait<br/>(misal: /rps/{id}/detail)
```

### Notification Channel Mapping

```mermaid
graph LR
    subgraph "Event Sources"
        E1[RPS Submitted]
        E2[RPS Reviewed]
        E3[RPS Approved]
        E4[Revision Requested]
        E5[RPS Published]
        E6[Reviewer Assigned]
        E7[User Invited]
        E8[AI Quota Warning]
    end

    subgraph "Channels"
        C1[In-App Notification]
        C2[Email]
        C3[Future: Webhook]
        C4[Future: WhatsApp]
    end

    subgraph "Recipients"
        R1[Dosen]
        R2[Kaprodi]
        R3[Reviewer]
        R4[Admin Univ]
        R5[LPM]
    end

    E1 --> C1
    E1 --> C2
    E2 --> C1
    E2 --> C2
    E3 --> C1
    E3 --> C2
    E4 --> C1
    E4 --> C2
    E5 --> C1
    E5 --> C2
    E6 --> C1
    E6 --> C2
    E7 --> C2
    E8 --> C1
    E8 --> C2

    C1 --> R1
    C1 --> R2
    C1 --> R3
    C1 --> R4
    C1 --> R5
    C2 --> R1
    C2 --> R2
    C2 --> R3
    C2 --> R4
    C2 --> R5
```

### Notification Digest Logic

```mermaid
flowchart TD
    A[Event: Notifikasi baru] --> B{User menerima > 5<br/>notifikasi dalam 1 jam?}
    B -->|Tidak| C[Kirim email individual]
    B -->|Ya| D[Tunda pengiriman email<br/>hingga window 1 jam selesai]
    D --> E[Generate digest summary]
    E --> F[Kirim 1 email berisi<br/>ringkasan semua notifikasi]
    C --> G[Update email_sent_at]
    F --> G
```

---

## 7. Audit Log Data Flow

```mermaid
sequenceDiagram
    participant User as User (any role)
    participant Middleware as AuditLogMiddleware
    participant Controller as Controller / Service
    participant AuditSvc as AuditService
    participant DB as MariaDB
    participant Viewer as Audit Viewer UI

    Note over User,Viewer: Fase 1: Pencatatan Otomatis via Middleware

    User->>Controller: HTTP Request (POST /api/v1/rps/{id})
    Controller->>Middleware: Request melewati middleware
    Middleware->>Middleware: Capture request info:<br/>- user_id<br/>- tenant_id<br/>- ip_address<br/>- user_agent<br/>- method + URL<br/>- request_body (truncated)
    Middleware->>AuditSvc: log('request', data)
    AuditSvc->>DB: INSERT INTO audit_logs<br/>{type='request', user_id, tenant_id, action,<br/>entity_type, entity_id, ip, user_agent, request_data}

    Note over User,Viewer: Fase 2: Pencatatan Aksi Spesifik

    Controller->>Controller: Proses bisnis: approveRPS(rps_id)
    Controller->>AuditSvc: log('workflow', {<br/>rps_id, action='approve',<br/>old_status='review', new_status='approved',<br/>by_user='kaprodi'})

    AuditSvc->>DB: INSERT INTO audit_logs<br/>{type='workflow', user_id, tenant_id,<br/>entity_type='rps', entity_id=rps_id,<br/>action='status_change',<br/>metadata: {old: 'review', new: 'approved'}}

    Controller-->>User: Response: RPS disetujui

    Note over User,Viewer: Fase 3: Viewing Audit Logs

    Viewer->>AuditSvc: getLogs(tenant_id, filters)
    AuditSvc->>DB: SELECT * FROM audit_logs<br/>WHERE tenant_id = ?<br/>AND created_at >= ?<br/>AND type = ?<br/>ORDER BY created_at DESC<br/>LIMIT 100
    DB-->>AuditSvc: Rows
    AuditSvc-->>Viewer: Data audit logs
    Viewer-->>User: Tabel audit log<br/>(timestamp, user, action, entity, detail)
```

### Audit Log Table Structure

```mermaid
graph LR
    subgraph "audit_logs table"
        A[id: ULID<br/>Primary Key]
        B[tenant_id: ULID<br/>Indexed]
        C[user_id: ULID<br/>Nullable]
        D[type: enum<br/>'request','workflow','data','system']
        E[action: varchar<br/>e.g. 'status_change', 'create_rps']
        F[entity_type: varchar<br/>e.g. 'rps', 'user', 'cpl']
        G[entity_id: ULID<br/>Nullable]
        H[old_values: JSON<br/>Nullable]
        I[new_values: JSON<br/>Nullable]
        J[metadata: JSON<br/>ip, user_agent, extra]
        K[created_at: timestamp]
    end

    subgraph "Common Queries"
        Q1[Filter by tenant + date range]
        Q2[Filter by entity_type + entity_id]
        Q3[Filter by user_id]
        Q4[Filter by action]
        Q5[Export all to CSV/Excel]
    end

    Q1 --> A
    Q2 --> F
    Q3 --> C
    Q4 --> E
    Q5 --> A
```

### Data Retention untuk Audit Log

| Level | Retensi | Tindakan |
|-------|---------|----------|
| Request logs | 90 hari | Auto-delete via scheduler |
| Workflow logs | Unlimited | Tetap disimpan (core audit) |
| Data change logs | 1 tahun | Archive ke S3, lalu hapus dari DB |
| AI usage logs | 2 tahun | Simpan di DB (cost tracking) |

---

**Navigasi:** [Sebelumnya: API Overview](23-api-overview.md) | [Daftar Isi](../README.md) | [Berikutnya: ERD Overview](25-erd-overview.md)
