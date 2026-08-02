# 11 — Out of Scope

## Di Luar Lingkup Produk

### Apa yang TIDAK akan dibangun oleh RPS OBE

#### 1. Learning Management System (LMS)

RPS OBE adalah platform perencanaan pembelajaran, bukan platform pelaksanaan pembelajaran. Kami tidak akan menggantikan LMS.

**Yang tidak disediakan:**
- Upload materi kuliah (PDF, video, dll)
- Forum diskusi mahasiswa
- Kuis online dan UTS/UAS online
- Presensi mahasiswa
- Penilaian dan grading mahasiswa
- Dashboard akademik mahasiswa

**Yang disediakan (future):**
- Integrasi dengan LMS populer (Moodle, Canvas, Google Classroom) untuk sinkronisasi data

#### 2. Sistem Informasi Akademik (SIAKAD)

Kami tidak menggantikan SIAKAD kampus. Data akademik seperti KRS, KHS, transkrip bukan bagian dari platform.

**Yang disediakan (future):**
- API untuk integrasi data mata kuliah dan dosen dari SIAKAD

#### 3. E-Learning Content Authoring

Platform tidak menyediakan:
- Pembuatan konten multimedia interaktif
- Video editor
- SCORM/xAPI package builder
- Gamifikasi konten pembelajaran

#### 4. Akreditasi Automation

Platform membantu menyiapkan dokumen RPS untuk akreditasi, tetapi tidak mengotomatisasi seluruh proses akreditasi:
- Tidak submit borang akreditasi otomatis
- Tidak generate seluruh dokumen akreditasi
- Tidak tracking status akreditasi

#### 5. Aplikasi Mobile Native

Fase awal tidak menyediakan aplikasi mobile native (iOS/Android). Platform menggunakan responsive web design yang dapat diakses via mobile browser.

**Future consideration:** PWA (Progressive Web App) untuk akses offline.

#### 6. Fitur Sosial/Komunitas

Tidak menyediakan:
- Social feed/Timeline
- Komunitas dosen
- Forum diskusi publik
- Chat/messaging antar pengguna

#### 7. Marketplace/Konten Pihak Ketiga

Tidak menyediakan:
- Marketplace template RPS dari pihak ketiga
- Jual-beli konten pembelajaran
- Integrasi payment gateway untuk end-user

#### 8. White Label (MVP)

MVP tidak menyediakan white label/custom domain per tenant. Menggunakan domain utama `rpsobe.id` dengan subdomain opsional di fase enterprise.

#### 9. Multi-Bahasa

MVP hanya mendukung Bahasa Indonesia. Internasionalisasi (i18n) dipertimbangkan untuk fase scale.

#### 10. Fitur Offline

MVP tidak mendukung mode offline. Semua operasi memerlukan koneksi internet.

#### 11. Integrasi Feeder DIKTI

Tidak ada integrasi otomatis dengan Feeder DIKTI (PD-DIKTI) di MVP. Dipertimbangkan untuk fase enterprise.

#### 12. AI Training Custom Model

MVP menggunakan OpenAI API (GPT-4o) tanpa fine-tuning custom model. Custom model dipertimbangkan di masa depan jika volume penggunaan sangat tinggi.

## Batasan Teknis

| Batasan | Deskripsi |
|---------|-----------|
| Browser Support | Chrome, Firefox, Edge, Safari versi 2 tahun terakhir |
| IE11 | Tidak didukung |
| File Upload Maks | 10 MB per file |
| Concurrent Users | 500 per tenant (arsitektur mendukung, infrastructure scaling terpisah) |
| API Rate Limit | 60 request/menit per user |
| AI Rate Limit | 20 request/menit per user |

## Perbandingan dengan Produk Sejenis

| Fitur | RPS OBE | SISTER | LMS | SIAKAD |
|-------|---------|--------|-----|--------|
| RPS Builder | ✅ | ❌ | ❌ | ❌ |
| AI Assistant | ✅ | ❌ | ❌ | ❌ |
| Workflow Review | ✅ | ❌ | ❌ | ❌ |
| Pelaksanaan Kuliah | ❌ | ❌ | ✅ | ❌ |
| Manajemen Akademik | ❌ | ❌ | ❌ | ✅ |
| Pelaporan DIKTI | Future | ✅ | ❌ | ✅ |

---

**Navigasi:** [Sebelumnya: Scope](10-scope.md) | [Daftar Isi](../README.md) | [Berikutnya: Functional Requirement](12-functional-requirement.md)
