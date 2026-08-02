# Diagram: Activity Diagram Proses Review

Diagram ini menunjukkan alur aktivitas proses review RPS dengan swimlane antar aktor: Dosen, Reviewer, Kaprodi, dan Sistem.

```mermaid
flowchart TD
    subgraph DS[👤 Dosen]
        A[Mengisi RPS Lengkap] --> B[Klik Ajukan Review]
        B --> C[Menerima Notifikasi Hasil]
        C --> D{Hasil Review?}
        D -- Revisi --> E[Perbaiki Sesuai Komentar]
        E --> B
        D -- Disetujui --> F[Review Selesai]
    end

    subgraph SYS[⚙️ Sistem]
        G[Menerima Pengajuan] --> H[Assign Reviewer Otomatis]
        H --> I[Kirim Notifikasi ke Reviewer]
        I --> J[Menerima Hasil Review]
        J --> K{Skor >= Minimum?}
        K -- Ya --> L[Kirim ke Kaprodi]
        K -- Tidak --> M[Kirim Notifikasi Revisi ke Dosen]
        L --> N{Menerima Keputusan Kaprodi}
        N -- Setuju --> O[Update Status: Approved]
        N -- Tolak --> P[Update Status: Rejected]
        N -- Revisi --> M
        O --> Q[Kirim Notifikasi Persetujuan]
        P --> R[Kirim Notifikasi Penolakan]
        M --> S[Update Status: Revision]
    end

    subgraph RV[👤 Reviewer]
        T[Menerima Notifikasi Review] --> U[Buka RPS]
        U --> V[Tinjau Setiap Komponen]
        V --> W[Isi Form Penilaian]
        W --> X{Beri Skor per Kriteria}
        X --> Y[Tulis Komentar & Saran]
        Y --> Z{Rekomendasi?}
        Z -- Setuju --> AA[Submit: Disetujui]
        Z -- Revisi --> AB[Submit: Perlu Revisi]
        AA --> AC[Kirim ke Sistem]
        AB --> AC
    end

    subgraph KP[👤 Kaprodi]
        AD[Menerima Notifikasi RPS Direview] --> AE[Tinjau Hasil Review]
        AE --> AF[Periksa Skor & Komentar]
        AF --> AG{Keputusan Akhir?}
        AG -- Setuju --> AH[Approve RPS]
        AG -- Revisi --> AI[Minta Revisi Tambahan]
        AG -- Tolak --> AJ[Tolak RPS]
        AH --> AK[Kirim Keputusan ke Sistem]
        AI --> AK
        AJ --> AK
    end

    %% Cross-swimlane connections
    B -.-> G
    S -.-> T
    S -.-> AD
    AC -.-> J
    AK -.-> N
    Q -.-> C
    R -.-> C
    M -.-> C
```

**Cara membaca:**
- Diagram dibagi menjadi 4 swimlane (lajur) untuk masing-masing aktor: Dosen, Sistem, Reviewer, dan Kaprodi.
- Panah solid menunjukkan alur dalam swimlane yang sama.
- Panah putus-putus `.->` menunjukkan komunikasi/kiriman data antar swimlane.
- Alur dimulai dari Dosen mengajukan review dan berakhir dengan notifikasi hasil ke Dosen.
