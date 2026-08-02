# Diagram: Flowchart RPS Builder

Diagram ini menunjukkan alur lengkap wizard RPS Builder dari login hingga submit review, termasuk interaksi AI dan validasi.

```mermaid
flowchart TD
    A([Mulai]) --> B[Login]
    B --> C{Otorisasi Valid?}
    C -- Tidak --> B
    C -- Ya --> D[Dashboard Dosen]
    D --> E[Buka RPS Builder]
    E --> F[Pilih Mata Kuliah]
    F --> G{MK Tersedia?}
    G -- Tidak --> F
    G -- Ya --> H[Langkah 1: Isi Identitas MK]
    H --> I[Langkah 2: Isi CPL/CPMK]
    I --> J{Gunakan Validasi AI?}
    J -- Ya --> K[Validasi AI CPL/CPMK]
    K --> L{Pengelompokan Valid?}
    L -- Tidak --> M[Tampilkan Saran AI]
    M --> N{Perbaiki Manual?}
    N -- Ya --> I
    N -- Tidak --> I
    L -- Ya --> O
    J -- Tidak --> O[Langkah 3: Isi Bahan Kajian]
    O --> P[Langkah 4: Isi Metode Pembelajaran]
    P --> Q{Gunakan Saran AI?}
    Q -- Ya --> R[Generate Saran Metode AI]
    R --> S{Saran Diterima?}
    S -- Ya --> T
    S -- Tidak --> P
    Q -- Tidak --> T[Langkah 5: Isi Penilaian]
    T --> U[Langkah 6: Isi Pustaka/Referensi]
    U --> V[Langkah 7: Isi Rencana Mingguan]
    V --> W{Gunakan AI Konten?}
    W -- Ya --> X[Generate Konten Mingguan AI]
    X --> Y{Hasil OK?}
    Y -- Tidak --> V
    Y -- Ya --> Z
    W -- Tidak --> Z[Langkah 8: Pratinjau RPS]
    Z --> AA{Validasi Kelengkapan}
    AA -- Gagal --> AB[Tampilkan Error]
    AB --> AC{Perbaiki?}
    AC -- Ya --> AD[Kembali ke Langkah Relevan]
    AD --> Z
    AC -- Tidak --> AE[Simpan Draft]
    AE --> AF{Draft Tersimpan}
    AF --> Z
    AA -- Lulus --> AG{Pilih Aksi}
    AG -- Simpan Draft --> AE
    AG -- Cetak Pratinjau --> AH[Ekspor PDF/Word]
    AH --> AG
    AG -- Ajukan Review --> AI[Submit ke Reviewer]
    AI --> AJ[Notifikasi ke Reviewer]
    AJ --> AK([Selesai])
```

**Cara membaca:**
- Mulai dari atas, ikuti panah sesuai keputusan di node berbentuk belah ketupat (decision).
- Node persegi panjang adalah proses/aksi, node dengan sudut melengkung adalah awal/selesai.
- Terdapat tiga titik keputusan AI: validasi CPL/CPMK, saran metode pembelajaran, dan generate konten mingguan.
- Jika validasi kelengkapan gagal, pengguna dapat kembali ke langkah relevan atau menyimpan sebagai draft.
