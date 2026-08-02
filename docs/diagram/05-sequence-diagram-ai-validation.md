# Diagram: Sequence Diagram Validasi AI

Diagram ini menunjukkan alur komunikasi antara Dosen, Sistem, dan OpenAI API saat proses validasi CPL/CPMK menggunakan AI.

```mermaid
sequenceDiagram
    actor DS as Dosen
    participant UI as Livewire UI
    participant SVC as ValidationService
    participant RPS as RPS Repository
    participant AI as OpenAI API
    participant LOG as Error Logger

    DS->>UI: Klik tombol "Validasi CPL/CPMK"
    UI->>UI: Tampilkan loading spinner
    UI->>SVC: validateCplCpmk(rpsId)

    SVC->>RPS: ambil data CPL dan CPMK
    RPS-->>SVC: Data CPL & CPMK

    SVC->>SVC: Susun prompt validasi
    SVC->>SVC: Siapkan konteks (kurikulum, prodi)

    SVC->>AI: POST /v1/chat/completions
    Note over SVC,AI: Prompt: validasi keselarasan<br/>CPL ke CPMK, cek kelengkapan,<br/>beri saran perbaikan

    alt Respons Sukses (dalam 30s)
        AI-->>SVC: JSON: daftar validasi per item
        SVC->>SVC: Parse & validasi struktur respons
        SVC->>SVC: Kategorikan: valid, warning, error
        SVC-->>UI: HasilValidasi[] (per item)
        UI-->>DS: Tampilkan hasil validasi
        Note over DS,UI: ✅ Valid<br/>⚠️ Warning dengan saran<br/>❌ Error perlu diperbaiki

    else Timeout (>30 detik)
        AI-->>SVC: Timeout error
        SVC->>LOG: Catat error timeout
        SVC-->>UI: Error: "Layanan AI tidak merespons"
        UI->>UI: Hentikan spinner
        UI-->>DS: Tampilkan error + tombol coba lagi

    else Respons Invalid / Format Salah
        AI-->>SVC: Respons tidak sesuai JSON schema
        SVC->>LOG: Catat format error + raw response
        SVC->>SVC: Retry dengan prompt lebih ketat

        alt Retry ke-1 (sukses)
            SVC->>AI: POST ulang dengan strict prompt
            AI-->>SVC: JSON valid
            SVC-->>UI: Hasil validasi
            UI-->>DS: Tampilkan hasil
        else Retry ke-1 (gagal lagi)
            AI-->>SVC: Masih invalid
            SVC->>LOG: Catat kegagalan retry
            SVC-->>UI: Error: "Gagal memproses validasi AI"
            UI-->>DS: Tampilkan error + saran coba manual
        end

    else API Error (4xx/5xx)
        AI-->>SVC: HTTP error (429/500/dll)
        SVC->>LOG: Catat API error
        SVC-->>UI: Error: "Layanan AI sedang sibuk"
        UI-->>DS: Tampilkan error + retry countdown
    end

    opt Dosen klik saran AI
        DS->>UI: Klik "Terapkan Saran"
        UI->>SVC: applySuggestion(itemId, suggestion)
        SVC->>RPS: Update data CPMK
        RPS-->>SVC: Update sukses
        SVC-->>UI: Data terupdate
        UI-->>DS: Tampilkan notifikasi sukses
    end
```

**Cara membaca:**
- Garis vertikal menunjukkan partisipan (aktor/sistem).
- Panah horizontal menunjukkan pesan/kiriman data dari pengirim ke penerima.
- Blok `alt` menunjukkan cabang kondisional (sukses/timeout/error).
- Blok `opt` menunjukkan langkah opsional (menerapkan saran AI).
- Diagram dibaca dari atas ke bawah mengikuti urutan kronologis.
