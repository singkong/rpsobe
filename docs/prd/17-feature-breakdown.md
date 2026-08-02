# 17 — Feature Breakdown

## Struktur Fitur

### Modul A: Authentication & Authorization (AUTH)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| AUTH-1 | Login | 3 | 3 SP | P0 |
| AUTH-2 | Register | 4 | 5 SP | P0 |
| AUTH-3 | Forgot/Reset Password | 3 | 3 SP | P0 |
| AUTH-4 | Email Verification | 2 | 2 SP | P0 |
| AUTH-5 | Role & Permission | 5 | 8 SP | P0 |
| AUTH-6 | Session Management | 3 | 3 SP | P0 |
| AUTH-7 | SSO Architecture (stub) | 2 | 3 SP | P2 |
| AUTH-8 | MFA Architecture (stub) | 2 | 3 SP | P2 |

**Total AUTH:** 24 User Stories, 30 SP

### Modul B: User Management (USER)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| USER-1 | User List & Search | 4 | 5 SP | P0 |
| USER-2 | Create User | 3 | 3 SP | P0 |
| USER-3 | Edit User | 3 | 3 SP | P0 |
| USER-4 | Deactivate User | 2 | 2 SP | P0 |
| USER-5 | Invitation System | 5 | 8 SP | P0 |
| USER-6 | Profile Management | 4 | 5 SP | P0 |
| USER-7 | Bulk Import CSV | 3 | 5 SP | P1 |
| USER-8 | Activity Log Viewer | 3 | 5 SP | P1 |

**Total USER:** 27 User Stories, 36 SP

### Modul C: Master Data (MASTER)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| MASTER-1 | Universitas (CRUD) | 5 | 5 SP | P0 |
| MASTER-2 | Fakultas (CRUD) | 5 | 5 SP | P0 |
| MASTER-3 | Program Studi (CRUD) | 5 | 5 SP | P0 |
| MASTER-4 | Kurikulum (CRUD + Multi) | 6 | 8 SP | P0 |
| MASTER-5 | Semester (CRUD) | 4 | 3 SP | P0 |
| MASTER-6 | Mata Kuliah (CRUD) | 6 | 8 SP | P0 |
| MASTER-7 | Dosen (CRUD) | 5 | 5 SP | P0 |
| MASTER-8 | Profil Lulusan (CRUD) | 4 | 5 SP | P0 |
| MASTER-9 | CPL (CRUD + Kategori) | 6 | 8 SP | P0 |
| MASTER-10 | Referensi (CRUD) | 4 | 5 SP | P1 |
| MASTER-11 | Bulk Import (CSV) | 3 | 5 SP | P1 |

**Total MASTER:** 53 User Stories, 62 SP

### Modul D: RPS Builder (BUILDER)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| BUILDER-1 | Wizard Framework | 6 | 13 SP | P0 |
| BUILDER-2 | Step 1: Informasi MK | 6 | 8 SP | P0 |
| BUILDER-3 | Step 2: Pilih CPL | 5 | 8 SP | P0 |
| BUILDER-4 | Step 3: CPMK | 7 | 13 SP | P0 |
| BUILDER-5 | Step 4: Sub-CPMK | 7 | 13 SP | P0 |
| BUILDER-6 | Step 5: Materi | 6 | 13 SP | P0 |
| BUILDER-7 | Step 6: Metode Pembelajaran | 5 | 8 SP | P0 |
| BUILDER-8 | Step 7: Assessment | 7 | 13 SP | P0 |
| BUILDER-9 | Step 8: Review & Finalisasi | 6 | 8 SP | P0 |
| BUILDER-10 | Auto-save | 3 | 5 SP | P0 |
| BUILDER-11 | Duplicate RPS | 3 | 5 SP | P1 |
| BUILDER-12 | Inline Validation | 5 | 8 SP | P0 |

**Total BUILDER:** 66 User Stories, 115 SP

### Modul E: Mapping & Constructive Alignment (MAP)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| MAP-1 | CPL → CPMK Mapping | 4 | 5 SP | P0 |
| MAP-2 | CPMK → Sub-CPMK Mapping | 4 | 5 SP | P0 |
| MAP-3 | Sub-CPMK → Materi Mapping | 4 | 5 SP | P0 |
| MAP-4 | Materi → Assessment Mapping | 4 | 5 SP | P0 |
| MAP-5 | Visualisasi Mapping | 5 | 13 SP | P1 |
| MAP-6 | Gap Detection | 4 | 8 SP | P1 |

**Total MAP:** 25 User Stories, 41 SP

### Modul F: AI Engine (AI)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| AI-1 | AI Infrastructure | 5 | 8 SP | P1 |
| AI-2 | AI Generate CPMK | 4 | 8 SP | P1 |
| AI-3 | AI Generate Sub-CPMK | 4 | 8 SP | P1 |
| AI-4 | AI Generate Materi | 4 | 8 SP | P1 |
| AI-5 | AI Generate Referensi | 3 | 5 SP | P1 |
| AI-6 | AI Generate Assessment | 4 | 8 SP | P1 |
| AI-7 | AI Generate Rubrik | 3 | 5 SP | P1 |
| AI-8 | AI Generate Learning Outcome | 3 | 5 SP | P1 |
| AI-9 | AI Generate Learning Activities | 3 | 5 SP | P1 |
| AI-10 | AI Validator — Core | 5 | 13 SP | P1 |
| AI-11 | AI Validator — 8 Aspek | 8 | 8 SP | P1 |
| AI-12 | AI Reviewer — Scoring | 5 | 13 SP | P2 |
| AI-13 | AI Reviewer — Comments & Suggestions | 5 | 8 SP | P2 |

**Total AI:** 56 User Stories, 102 SP

### Modul G: Workflow (WF)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| WF-1 | Status Machine | 5 | 8 SP | P0 |
| WF-2 | Submit for Review | 4 | 5 SP | P0 |
| WF-3 | Review & Scoring | 6 | 13 SP | P0 |
| WF-4 | Revision Management | 5 | 8 SP | P0 |
| WF-5 | Approval | 4 | 5 SP | P0 |
| WF-6 | Publish & Archive | 4 | 5 SP | P0 |
| WF-7 | Workflow History | 3 | 5 SP | P0 |
| WF-8 | Reviewer Assignment | 4 | 5 SP | P0 |
| WF-9 | Batch Operations | 4 | 8 SP | P1 |

**Total WF:** 39 User Stories, 62 SP

### Modul H: Dashboard (DASH)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| DASH-1 | Dashboard Dosen | 5 | 8 SP | P0 |
| DASH-2 | Dashboard Kaprodi | 6 | 13 SP | P0 |
| DASH-3 | Dashboard Fakultas | 5 | 8 SP | P1 |
| DASH-4 | Dashboard Universitas | 5 | 8 SP | P1 |
| DASH-5 | Dashboard LPM | 5 | 8 SP | P2 |
| DASH-6 | Dashboard Super Admin | 5 | 8 SP | P1 |

**Total DASH:** 31 User Stories, 53 SP

### Modul I: Reporting (REPORT)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| REPORT-1 | Statistik & Grafik | 5 | 13 SP | P1 |
| REPORT-2 | Filter & Search | 4 | 5 SP | P1 |
| REPORT-3 | Export Excel | 3 | 5 SP | P1 |
| REPORT-4 | Export PDF | 3 | 5 SP | P1 |
| REPORT-5 | Laporan Akreditasi | 4 | 8 SP | P2 |

**Total REPORT:** 19 User Stories, 36 SP

### Modul J: Notification (NOTIF)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| NOTIF-1 | Email Notification | 5 | 8 SP | P0 |
| NOTIF-2 | In-App Notification | 5 | 8 SP | P0 |
| NOTIF-3 | Notification Center | 4 | 5 SP | P0 |
| NOTIF-4 | Notification Preferences | 3 | 5 SP | P1 |
| NOTIF-5 | Notification Templates | 4 | 5 SP | P0 |

**Total NOTIF:** 21 User Stories, 31 SP

### Modul K: Versioning (VER)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| VER-1 | Version History | 4 | 8 SP | P0 |
| VER-2 | Version Diff | 4 | 13 SP | P1 |
| VER-3 | Version Rollback | 3 | 5 SP | P1 |
| VER-4 | Version Labeling | 3 | 3 SP | P0 |

**Total VER:** 14 User Stories, 29 SP

### Modul L: Audit Log (AUDIT)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| AUDIT-1 | Activity Logging | 5 | 8 SP | P0 |
| AUDIT-2 | Audit Viewer | 5 | 8 SP | P0 |
| AUDIT-3 | Export Audit | 3 | 5 SP | P1 |
| AUDIT-4 | Retention Policy | 3 | 3 SP | P2 |

**Total AUDIT:** 16 User Stories, 24 SP

### Modul M: Template (TEMP)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| TEMP-1 | Default Template | 3 | 5 SP | P0 |
| TEMP-2 | Template Upload | 4 | 8 SP | P1 |
| TEMP-3 | Template Builder | 5 | 13 SP | P1 |
| TEMP-4 | Template Selection | 3 | 3 SP | P0 |

**Total TEMP:** 15 User Stories, 29 SP

### Modul N: Export (EXPORT)

| Epic | Feature | User Stories | Estimasi | Prioritas |
|------|---------|-------------|----------|-----------|
| EXPORT-1 | Export Word (.docx) | 5 | 13 SP | P0 |
| EXPORT-2 | Export PDF | 5 | 8 SP | P0 |
| EXPORT-3 | Template-based Export | 4 | 8 SP | P1 |
| EXPORT-4 | Batch Export | 4 | 8 SP | P1 |

**Total EXPORT:** 18 User Stories, 37 SP

---

## Rekapitulasi

| Modul | Epics | User Stories | Story Points | Fase |
|-------|-------|-------------|-------------|------|
| AUTH | 8 | 24 | 30 | Phase 1 |
| USER | 8 | 27 | 36 | Phase 1 |
| MASTER | 11 | 53 | 62 | Phase 1 |
| BUILDER | 12 | 66 | 115 | Phase 1 |
| MAP | 6 | 25 | 41 | Phase 1 |
| AI | 13 | 56 | 102 | Phase 2 |
| WF | 9 | 39 | 62 | Phase 1 |
| DASH | 6 | 31 | 53 | Phase 1 |
| REPORT | 5 | 19 | 36 | Phase 1 |
| NOTIF | 5 | 21 | 31 | Phase 1 |
| VER | 4 | 14 | 29 | Phase 1 |
| AUDIT | 4 | 16 | 24 | Phase 1 |
| TEMP | 4 | 15 | 29 | Phase 1 |
| EXPORT | 4 | 18 | 37 | Phase 1 |
| **TOTAL** | **99** | **424** | **687** | |

---

**Navigasi:** [Sebelumnya: Workflow](16-workflow.md) | [Daftar Isi](../README.md) | [Berikutnya: Module Breakdown](18-module-breakdown.md)
