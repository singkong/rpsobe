# 20 — Business Rules

## Aturan Bisnis

### BR-1: Aturan Umum Platform

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-1.1 | Multi-Tenancy | Setiap universitas adalah tenant terpisah. Data antar tenant tidak dapat diakses satu sama lain. |
| BR-1.2 | Role Hierarchy | Super Admin > Admin Univ > Admin Fakultas > Admin Prodi > Kaprodi > Reviewer/Dosen > Mahasiswa |
| BR-1.3 | One User, One Role | Seorang user hanya memiliki satu role utama dalam satu tenant. Role tambahan dapat ditambahkan. |
| BR-1.4 | Invitation Only | Pengguna tidak dapat self-register tanpa invitation code. |
| BR-1.5 | Soft Delete | Semua data menggunakan soft delete. Data tidak dihapus fisik kecuali oleh Super Admin setelah 90 hari. |

### BR-2: Aturan Kurikulum

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-2.1 | Kurikulum Tunggal Aktif | Satu prodi hanya memiliki satu kurikulum berstatus aktif pada satu waktu. |
| BR-2.2 | Kurikulum Berlaku | Kurikulum memiliki tahun mulai dan tahun berakhir. MK hanya dapat dibuat dalam kurikulum aktif. |
| BR-2.3 | SKS Minimum | Total SKS kurikulum minimal sesuai SN-DIKTI (D3: 108, S1: 144, S2: 54, S3: 48) |
| BR-2.4 | Tidak Bisa Dihapus | Kurikulum yang sudah memiliki mata kuliah tidak dapat dihapus. |

### BR-3: Aturan CPL

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-3.1 | CPL Kategori | CPL wajib dikategorikan ke dalam 4 jenis: Sikap, Pengetahuan, Keterampilan Umum, Keterampilan Khusus |
| BR-3.2 | CPL Coding | Format kode CPL: CPL-[Kategori]-[Nomor], contoh: CPL-S-01, CPL-P-02, CPL-KU-01, CPL-KK-01 |
| BR-3.3 | Minimal CPL | Setiap prodi minimal memiliki 1 CPL per kategori |
| BR-3.4 | CPL Profil Lulusan | CPL dapat (tidak wajib) dikaitkan dengan profil lulusan |
| BR-3.5 | CPL Tidak Dapat Dihapus | CPL yang sudah digunakan di RPS atau CPMK tidak dapat dihapus |

### BR-4: Aturan RPS

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-4.1 | Satu RPS per Semester | Satu mata kuliah hanya memiliki satu RPS per semester. Tidak bisa duplikat di semester yang sama. |
| BR-4.2 | CPL Minimal | RPS minimal mendukung 1 CPL |
| BR-4.3 | CPMK Minimal | RPS minimal memiliki 1 CPMK |
| BR-4.4 | CPMK Maksimal | Disarankan 4-8 CPMK per RPS, maksimal 12 |
| BR-4.5 | Sub-CPMK Minimal | Setiap CPMK minimal dijabarkan menjadi 1 Sub-CPMK |
| BR-4.6 | Jumlah Pertemuan | Default 16 pertemuan. Fleksibel untuk sistem blok atau format lain. |
| BR-4.7 | Bobot Assessment | Total bobot seluruh komponen assessment harus tepat 100% |
| BR-4.8 | Bobot per Assessment | Setiap komponen assessment minimal 5% dari total |
| BR-4.9 | Formatif & Sumatif | Minimal 1 assessment formatif dan 1 assessment sumatif |
| BR-4.10 | Minimal Referensi | RPS minimal memiliki 3 referensi, disarankan 5+ |
| BR-4.11 | Referensi Primer | Minimal 50% referensi adalah sumber primer (jurnal, buku teks terbaru) |

### BR-5: Aturan Taksonomi Bloom

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-5.1 | Level Taksonomi | Setiap Sub-CPMK harus memiliki level taksonomi Bloom: C1-C6 (kognitif), A1-A5 (afektif), P1-P5 (psikomotorik) |
| BR-5.2 | Kata Kerja Operasional | Deskripsi Sub-CPMK harus menggunakan kata kerja operasional sesuai level taksonomi |
| BR-5.3 | Progresivitas | Level taksonomi Sub-CPMK idealnya meningkat dari pertemuan awal ke akhir |
| BR-5.4 | Minimal C4 | Minimal 30% Sub-CPMK berada di level C4 (Analisis) atau lebih tinggi untuk jenjang S1 |

### BR-6: Aturan Constructive Alignment

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-6.1 | CPL-CPMK Alignment | Setiap CPMK harus dapat ditelusuri kembali ke CPL yang didukung |
| BR-6.2 | CPMK-SubCPMK Alignment | Setiap Sub-CPMK harus dapat ditelusuri kembali ke CPMK |
| BR-6.3 | Assessment Alignment | Setiap assessment harus mengukur Sub-CPMK yang relevan |
| BR-6.4 | Materi Alignment | Setiap materi pertemuan harus berkontribusi pada pencapaian Sub-CPMK |
| BR-6.5 | Gap Detection | Sistem harus mendeteksi CPL yang tidak memiliki CPMK (orphan CPL) |

### BR-7: Aturan Workflow

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-7.1 | Status Flow | Draft > Review > (Revision > Draft > Review) > Approved > Published > Archived |
| BR-7.2 | Draft Editor | RPS dalam status Draft hanya dapat diedit oleh dosen pemilik |
| BR-7.3 | Review Lock | RPS dalam status Review tidak dapat diedit oleh dosen (read-only) |
| BR-7.4 | Single Reviewer | Satu RPS hanya direview oleh satu reviewer pada satu waktu (MVP) |
| BR-7.5 | Review Timeout | Jika RPS di status Review > 14 hari tanpa aksi, sistem mengirim reminder ke reviewer |
| BR-7.6 | Revisi Wajib | Reviewer yang meminta revisi wajib memberikan alasan spesifik |
| BR-7.7 | Approval Final | Hanya Kaprodi yang dapat melakukan approval akhir dan publish |
| BR-7.8 | Archival | RPS yang sudah di-archive tidak dapat diedit. Hanya bisa diduplikasi ke RPS baru. |
| BR-7.9 | Duplikasi | RPS yang diduplikasi menjadi Draft baru dengan versi baru, tidak mempengaruhi RPS sumber |

### BR-8: Aturan AI

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-8.1 | Human-in-the-Loop | Semua output AI wajib direview dan dapat diedit oleh pengguna sebelum disimpan |
| BR-8.2 | AI Label | Konten yang dihasilkan AI wajib ditandai sebagai AI-generated |
| BR-8.3 | AI Rate Limit | Maksimal 20 request AI per menit per user |
| BR-8.4 | AI Cost Control | Setiap tenant memiliki budget AI bulanan. Jika melampaui, AI masuk mode throttled. |
| BR-8.5 | AI Transparency | Pengguna dapat melihat prompt yang digunakan untuk menghasilkan konten AI |
| BR-8.6 | AI Disclaimer | AI memberikan disclaimer bahwa output mungkin mengandung kesalahan |

### BR-9: Aturan Notifikasi

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-9.1 | Email Wajib | Setiap perubahan status RPS wajib mengirim notifikasi email ke pihak terkait |
| BR-9.2 | In-App Wajib | Setiap notifikasi email juga ditampilkan di notification center in-app |
| BR-9.3 | Batch Digest | Jika user menerima > 5 notifikasi dalam 1 jam, kirim digest summary |
| BR-9.4 | Unsubscribe | User dapat mengatur preferensi notifikasi (tidak bisa unsubscribe dari notifikasi wajib) |

### BR-10: Aturan Versioning

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-10.1 | Auto-Version | Setiap submit review menghasilkan versi baru otomatis |
| BR-10.2 | Version Label | Format label: v[MAJOR].[MINOR] — v1.0, v1.1, v2.0 |
| BR-10.3 | Major Version | Setiap selesai review dan published = major version increment |
| BR-10.4 | Minor Version | Setiap edit dan simpan draft = minor version increment |
| BR-10.5 | Immutable | Versi yang sudah published tidak dapat diubah |
| BR-10.6 | Rollback Creates New | Rollback ke versi lama membuat versi baru, tidak menghapus versi setelahnya |

### BR-11: Aturan Export

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-11.1 | Format Lengkap | Export mencakup seluruh komponen RPS: cover, identitas, CPL, CPMK, Sub-CPMK, materi, assessment, referensi |
| BR-11.2 | Logo Universitas | Export menyertakan logo universitas di cover dan header |
| BR-11.3 | Watermark Draft | RPS dalam status Draft/Review/Revision yang di-export diberi watermark |
| BR-11.4 | Template Kampus | Export menggunakan template sesuai preferensi universitas |
| BR-11.5 | Tanda Tangan | Kolom tanda tangan di halaman pengesahan (diisi manual setelah print) |

### BR-12: Aturan Template

| ID | Aturan | Deskripsi |
|----|--------|-----------|
| BR-12.1 | Default Template | Sistem menyediakan template default sesuai format SN-DIKTI/Kemdikbud |
| BR-12.2 | Template per Universitas | Setiap universitas dapat mengunggah template kustom (format .docx) |
| BR-12.3 | Placeholder | Template menggunakan placeholder yang akan diisi sistem: {nama_mk}, {kode_mk}, {cpl_list}, dll |
| BR-12.4 | Template Aktif | Hanya satu template yang dapat aktif per universitas |

---

**Navigasi:** [Sebelumnya: Permission Matrix](19-permission-matrix.md) | [Daftar Isi](../README.md) | [Berikutnya: AI Integration](21-ai-integration.md)
