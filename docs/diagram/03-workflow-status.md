# Diagram: Workflow Status RPS

Diagram state ini menunjukkan seluruh status RPS, transisi antar status, dan siapa yang dapat memicu setiap transisi.

```mermaid
stateDiagram-v2
    [*] --> Draft: Dosen membuat RPS baru

    state Draft {
        [*] --> Mengisi: Dosen mengisi formulir
        Mengisi --> Menyimpan: Auto-save / Simpan
        Menyimpan --> Mengisi: Dosen melanjutkan edit
        Mengisi --> DraftSelesai: Semua langkah terisi
    }

    Draft --> Review: Dosen mengajukan review

    state Review {
        [*] --> MenungguReviewer: Sistem assign reviewer
        MenungguReviewer --> SedangDitinjau: Reviewer mulai review
        SedangDitinjau --> SelesaiDitinjau: Reviewer selesai menilai
    }

    Review --> Revision: Reviewer/Kaprodi minta revisi
    Revision --> Review: Dosen submit ulang
    Revision --> Draft: Dosen tarik ke draft

    Review --> Approved: Kaprodi menyetujui

    state Approved {
        [*] --> Disetujui: RPS disetujui
        Disetujui --> SiapPublish: LPM verifikasi final
    }

    Approved --> Published: Kaprodi/LPM publikasi
    Published --> Archived: Admin arsipkan/kurikulum baru
    Archived --> Draft: Admin clone untuk revisi kurikulum

    Review --> Rejected: Kaprodi tolak permanen
    Rejected --> [*]

    Archived --> [*]
    Published --> [*]
```

| Status | Deskripsi | Dipicu Oleh |
|---|---|---|
| **Draft** | RPS dalam tahap pengisian oleh Dosen | Dosen |
| **Review** | RPS sedang ditinjau oleh Reviewer | Dosen (submit) |
| **Revision** | RPS perlu perbaikan | Reviewer, Kaprodi |
| **Approved** | RPS telah disetujui | Kaprodi |
| **Published** | RPS dipublikasikan dan dapat diakses | Kaprodi, LPM |
| **Rejected** | RPS ditolak permanen | Kaprodi |
| **Archived** | RPS diarsipkan (kurikulum lama) | Admin Univ |

**Cara membaca:**
- Setiap kotak mewakili status RPS; panah menunjukkan transisi yang valid.
- Status `Draft` memiliki sub-state internal (Mengisi, Menyimpan, Selesai).
- Status `Review` memiliki sub-state internal (Menunggu, Sedang Ditinjau, Selesai).
- Tabel di bawah diagram merangkum setiap status beserta pihak yang dapat memicu transisi.
