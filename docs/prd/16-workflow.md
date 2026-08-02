# 16 — Workflow

## Workflow RPS — Siklus Hidup Dokumen

```mermaid
stateDiagram-v2
    [*] --> Draft: Buat RPS Baru
    Draft --> Review: Ajukan Review
    Draft --> Draft: Edit & Simpan
    Review --> Revision: Reviewer Minta Revisi
    Review --> Approved: Reviewer Setujui
    Revision --> Draft: Dosen Revisi
    Revision --> Review: Ajukan Ulang
    Approved --> Published: Kaprodi Publikasi
    Approved --> Revision: Kaprodi Minta Revisi
    Published --> Archived: Arsipkan
    Published --> Draft: Duplikat ke RPS Baru
    Archived --> Draft: Duplikat ke RPS Baru
```

## Deskripsi Status

| Status | Deskripsi | Siapa yang bisa mengubah? | Status Berikutnya |
|--------|-----------|---------------------------|-------------------|
| **Draft** | RPS dalam proses penyusunan. Hanya dosen bersangkutan yang bisa mengakses. | Dosen | Review |
| **Review** | RPS diajukan untuk review. Hanya reviewer yang bisa aksi, dosen read-only. | Reviewer | Revision, Approved |
| **Revision** | RPS dikembalikan ke dosen untuk diperbaiki. | Dosen | Draft, Review |
| **Approved** | RPS telah disetujui reviewer dan kaprodi. Siap dipublikasikan. | Kaprodi | Published, Revision |
| **Published** | RPS sudah dipublikasikan dan tersedia untuk diakses mahasiswa. | Kaprodi, Admin | Archived |
| **Archived** | RPS tidak berlaku lagi (kurikulum lama, MK tidak dibuka). | Kaprodi, Admin | Draft (via duplikasi) |

## Workflow Detail

### 1. Draft → Review

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as Sistem
    participant R as Reviewer
    
    D->>S: Klik "Ajukan Review"
    S->>S: Validasi kelengkapan RPS
    alt RPS tidak lengkap
        S-->>D: Tampilkan error: "Lengkapi semua step"
    else RPS lengkap
        S->>S: Ubah status → Review
        S->>R: Kirim notifikasi: "RPS siap review"
        S->>D: Konfirmasi: "RPS diajukan"
        S->>S: Catat di audit log
    end
```

### 2. Review → Revision atau Approved

```mermaid
sequenceDiagram
    participant R as Reviewer
    participant S as Sistem
    participant D as Dosen
    participant K as Kaprodi
    
    R->>S: Buka RPS (status Review)
    R->>S: Jalankan AI Validator (opsional)
    S-->>R: Hasil validasi
    R->>S: Isi skor dan komentar per komponen
    
    alt Reviewer setuju
        R->>S: Klik "Setujui"
        S->>S: Ubah status → Approved
        S->>K: Notifikasi: "RPS menunggu publikasi"
        S->>D: Notifikasi: "RPS Anda disetujui"
    else Reviewer minta revisi
        R->>S: Klik "Minta Revisi" + alasan
        S->>S: Ubah status → Revision
        S->>D: Notifikasi: "RPS perlu revisi"
    end
    S->>S: Catat di audit log
```

### 3. Revision → Draft → Review

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as Sistem
    participant R as Reviewer
    
    D->>S: Buka RPS (status Revision)
    D->>S: Edit sesuai saran reviewer
    D->>S: Simpan → status kembali ke Draft
    D->>S: Jalankan AI Validator (opsional)
    D->>S: Klik "Ajukan Review Ulang"
    S->>S: Ubah status → Review
    S->>R: Notifikasi: "RPS diajukan ulang untuk review"
```

### 4. Approved → Published

```mermaid
sequenceDiagram
    participant K as Kaprodi
    participant S as Sistem
    participant M as Mahasiswa
    
    K->>S: Buka RPS (status Approved)
    K->>S: Review final
    K->>S: Klik "Publikasikan"
    S->>S: Ubah status → Published
    S->>S: Generate versi publik (v1.0)
    alt Mahasiswa feature tersedia
        S->>M: RPS tersedia di portal mahasiswa
    end
    S->>S: Catat di audit log
```

### 5. Published → Archived

```mermaid
sequenceDiagram
    participant K as Kaprodi
    participant S as Sistem
    
    K->>S: Buka RPS (status Published)
    K->>S: Klik "Arsipkan"
    S->>S: Konfirmasi: "RPS yang diarsipkan tidak bisa diakses mahasiswa"
    K->>S: Konfirmasi
    S->>S: Ubah status → Archived
    S->>S: Catat di audit log
```

## Workflow Assignment Reviewer

```mermaid
sequenceDiagram
    participant KP as Kaprodi
    participant S as Sistem
    participant RV as Reviewer
    
    KP->>S: Buka daftar RPS dalam Review
    KP->>S: Pilih RPS → Assign Reviewer
    S-->>KP: Daftar reviewer yang tersedia
    KP->>S: Pilih reviewer, klik Assign
    S->>RV: Notifikasi: "Anda ditugaskan mereview RPS [nama MK]"
    S->>RV: RPS tampil di "Review Saya"
    S->>S: Catat assignment di audit log
```

## Workflow Multi-Reviewer (Future)

```mermaid
sequenceDiagram
    participant KP as Kaprodi
    participant RV1 as Reviewer 1
    participant RV2 as Reviewer 2
    participant S as Sistem
    
    KP->>S: Assign Reviewer 1 & 2
    RV1->>S: Review → Setuju
    RV2->>S: Review → Setuju
    S->>S: Semua reviewer setuju
    S->>S: Ubah status → Approved
```

## Workflow Batch Operations

```mermaid
graph TD
    A[Kaprodi memilih multiple RPS] --> B{Aksi}
    B --> C[Assign Reviewer massal]
    B --> D[Batch Approval]
    B --> E[Batch Archive]
    B --> F[Batch Export]
    C --> G[Semua RPS ter-assign]
    D --> H[Semua RPS Approved]
    E --> I[Semua RPS Archived]
    F --> J[Download ZIP]
```

## Rules & Constraints

| Aturan | Deskripsi |
|--------|-----------|
| **Single Active Reviewer** | Satu RPS hanya bisa direview oleh satu reviewer pada satu waktu (MVP). Multi-reviewer di future. |
| **Status Lock** | RPS di status Review/Approved/Published tidak bisa diedit dosen |
| **Revision Visibility** | Dosen hanya bisa melihat komentar reviewer di status Revision |
| **Auto-version** | Setiap perubahan status Draft → Review membuat versi baru |
| **Audit Trail** | Semua perubahan status, assignment, approval tercatat |
| **Notifikasi** | Setiap perubahan status mengirim notifikasi ke semua pihak terkait |
| **Expired Review** | Jika RPS di status Review > 14 hari tanpa aksi, kirim reminder ke reviewer |
| **Rollback** | Hanya Super Admin yang bisa rollback status (untuk kasus force majeure) |

---

**Navigasi:** [Sebelumnya: User Journey](15-user-journey.md) | [Daftar Isi](../README.md) | [Berikutnya: Feature Breakdown](17-feature-breakdown.md)
