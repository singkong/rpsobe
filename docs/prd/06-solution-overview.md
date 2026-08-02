# 06 — Solution Overview

## Gambaran Solusi

RPS OBE adalah platform SaaS berbasis web yang menyediakan solusi end-to-end untuk penyusunan, validasi, review, pengelolaan, dan publikasi RPS berbasis OBE.

## Tiga Pilar Solusi

```mermaid
graph TD
    RPS[RPS OBE Platform]
    RPS --> P1[1. RPS Builder]
    RPS --> P2[2. AI Engine]
    RPS --> P3[3. Workflow Engine]
    P1 --> P1A[Wizard Step-by-Step]
    P1 --> P1B[Constructive Alignment]
    P1 --> P1C[Export Word/PDF]
    P2 --> P2A[AI Assistant]
    P2 --> P2B[AI Validator]
    P2 --> P2C[AI Reviewer]
    P3 --> P3A[Draft → Review → Revision]
    P3 --> P3B[Approved → Published]
    P3 --> P3C[Versioning & Audit]
```

### Pilar 1: RPS Builder

Wizard interaktif 8 langkah yang memandu dosen menyusun RPS:

| Step | Komponen | Deskripsi |
|------|----------|-----------|
| 1 | Informasi Mata Kuliah | Nama MK, kode, SKS, semester, dosen |
| 2 | Pilih CPL | Memilih CPL yang didukung MK |
| 3 | CPMK | Menyusun CPMK dari CPL terpilih |
| 4 | Sub-CPMK | Menyusun Sub-CPMK dari CPMK |
| 5 | Materi | Menentukan materi per pertemuan |
| 6 | Metode Pembelajaran | Memilih metode pembelajaran |
| 7 | Assessment | Menyusun assessment dan bobot |
| 8 | Review | Pratinjau dan finalisasi |

Setiap langkah menampilkan progress bar, validasi inline, dan kemampuan menyimpan draft kapan saja.

### Pilar 2: AI Engine

Tiga komponen AI yang bekerja bersama:

| Komponen | Fungsi | Teknologi |
|----------|--------|-----------|
| **AI Assistant** | Generate CPMK, Sub-CPMK, materi, assessment, rubrik | OpenAI GPT-4o |
| **AI Validator** | Memeriksa constructive alignment, taksonomi, bobot, konsistensi | Rule engine + GPT |
| **AI Reviewer** | Memberikan skor, komentar, saran perbaikan | GPT + Scoring model |

### Pilar 3: Workflow Engine

Siklus hidup RPS yang terstruktur:

```mermaid
stateDiagram-v2
    [*] --> Draft
    Draft --> Review: Ajukan Review
    Review --> Revision: Perlu Revisi
    Review --> Approved: Disetujui
    Revision --> Draft: Simpan Revisi
    Approved --> Published: Publikasi
    Published --> Archived: Arsip
    Archived --> Draft: Duplikat ke Draft
```

## Arsitektur Multi-Tenant

```mermaid
graph TD
    SA[Super Admin] --> T1[Tenant Univ A]
    SA --> T2[Tenant Univ B]
    SA --> T3[Tenant Univ C]
    T1 --> F1[Fakultas A1]
    T1 --> F2[Fakultas A2]
    F1 --> P1[Prodi A1.1]
    F1 --> P2[Prodi A1.2]
```

- **Isolasi data penuh** antar tenant
- Satu database dengan `tenant_id` pada setiap tabel
- Row-level security berbasis Spatie Permission

## Key Differentiators

| Fitur | RPS OBE | Alternatif |
|-------|---------|------------|
| OBE-native | Ya — dirancang khusus OBE | Tidak — general purpose |
| Wizard Builder | 8 langkah terstruktur | Form bebas |
| AI Generate | Ya | Tidak |
| AI Validate | Ya | Tidak |
| AI Review | Ya | Tidak |
| Workflow | Built-in | Tidak ada |
| Versioning | Otomatis | Manual |
| Template Kustom | Per institusi | Terbatas |
| Multi-kampus | Native multi-tenant | Tidak ada |
| Akreditasi-ready | Format BAN-PT/LAM | Perlu konversi manual |

## Arsitektur Teknologi

```mermaid
graph TD
    subgraph Client
        B[Browser]
    end
    subgraph Web
        L[Laravel 13]
        LW[Livewire 3]
    end
    subgraph AI
        OAI[OpenAI API]
    end
    subgraph Storage
        DB[(MariaDB)]
        FS[File Storage]
    end
    B --> L
    L --> LW
    L --> DB
    L --> FS
    L --> OAI
```

---

**Navigasi:** [Sebelumnya: Problem Statement](05-problem-statement.md) | [Daftar Isi](../README.md) | [Berikutnya: Target Users](07-target-users.md)
