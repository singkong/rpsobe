# 15 — User Journey

## User Journey 1: Dosen Menyusun RPS Baru

### Journey Map

| Tahap | Aktivitas | Pikiran | Emosi | Touchpoint |
|-------|-----------|---------|-------|------------|
| **1. Kesadaran** | Kaprodi mengirim email: "Semua dosen wajib isi RPS semester depan" | "Lagi? Baru juga semester lalu..." | 😟 Frustrasi ringan | Email |
| **2. Login** | Membuka rpsobe.id dan login | "Semoga mudah..." | 😐 Netral | Halaman login |
| **3. Mulai** | Klik "Buat RPS Baru", pilih MK | "OK, mirip form biasa" | 😐 Netral | Dashboard |
| **4. Alur** | Melihat wizard 8 langkah | "Banyak juga ya langkahnya..." | 😟 Sedikit overwhelmed | Wizard Step 1 |
| **5. Terbantu** | AI men-generate CPMK dari CPL | "Wah, ini keren! Jadi lebih cepat." | 😊 Senang | AI Assistant |
| **6. Isi** | Mengisi materi per pertemuan | "Ini bagian paling lama..." | 😐 Fokus | Step 5-7 |
| **7. Validasi** | Klik "Validasi AI", lihat hasil | "Bagus, ternyata alignment saya sudah benar" | 😊 Lega | AI Validator |
| **8. Selesai** | Klik "Ajukan Review" | "Akhirnya selesai! Semoga cepat direview." | 😊 Bangga | Step 8 |
| **9. Menunggu** | Cek status RPS di dashboard | "Sudah 3 hari belum direview..." | 😟 Menunggu | Dashboard |
| **10. Hasil** | Dapat notifikasi: RPS disetujui | "YES! Selesai!" | 🎉 Senang | Notifikasi |

### Timeline Ideal

```
Hari 1: Dosen mulai menyusun (2-4 jam)
Hari 2: Revisi kecil, AI Validator
Hari 3: Ajukan review
Hari 4-6: Reviewer mereview
Hari 6: Hasil review → revisi (jika ada)
Hari 7: Revisi selesai → Approval
Hari 7: Published ✅
```

---

## User Journey 2: Kaprodi Memonitor dan Menyetujui

| Tahap | Aktivitas | Pikiran | Emosi | Touchpoint |
|-------|-----------|---------|-------|------------|
| **1. Mingguan** | Buka dashboard prodi | "Wah, baru 40% dosen yang isi RPS" | 😟 Khawatir | Dashboard |
| **2. Reminder** | Kirim notifikasi massal ke dosen yang belum | "Semoga minggu depan sudah pada isi" | 😐 Netral | Notifikasi |
| **3. Review** | Dapat notifikasi RPS siap review | "Ada 5 RPS baru nih..." | 😟 Sedikit beban | Notifikasi |
| **4. AI Review** | Gunakan AI Reviewer untuk bantu review | "AI kasih skor dan komentar, tinggal saya cek ulang" | 😊 Terbantu | AI Reviewer |
| **5. Approval** | Klik "Setujui" | "RPS ini sudah bagus" | 🙂 Puas | Review page |
| **6. Monitor** | Lihat statistik: 80% approved | "Target 100% minggu depan" | 🙂 Optimis | Dashboard |
| **7. Laporan** | Export laporan untuk dekan | "Data lengkap, siap presentasi" | 😊 Profesional | Report |

---

## User Journey 3: Reviewer Mereview RPS

| Tahap | Aktivitas | Pikiran | Emosi | Touchpoint |
|-------|-----------|---------|-------|------------|
| **1. Notifikasi** | Email: "Anda ditugaskan mereview 3 RPS" | "OK, harus selesai minggu ini" | 😐 Netral | Email + In-app |
| **2. Checklist** | Buka RPS, lihat AI Validator | "AI sudah cek alignment, saya fokus ke substansi" | 🙂 Terbantu | Review page |
| **3. Review** | Periksa CPMK, materi, assessment | "Sub-CPMK ke-3 kurang jelas..." | 😐 Analitis | Review page |
| **4. Skor** | Isi skor per komponen | "Alignment: 8/10, Materi: 7/10" | 😐 Objektif | Scoring form |
| **5. Komentar** | Tulis saran perbaikan spesifik | "Tambah studi kasus di pertemuan 5-7" | 🙂 Membantu | Comment box |
| **6. Submit** | Klik "Minta Revisi" | "Semoga dosen cepat merevisi" | 😐 Netral | Review submit |
| **7. Follow-up** | Cek apakah dosen sudah revisi | "Sudah direvisi, OK bisa approve" | 🙂 Lega | Dashboard |

---

## User Journey 4: LPM Melakukan Audit Mutu

| Tahap | Aktivitas | Pikiran | Emosi | Touchpoint |
|-------|-----------|---------|-------|------------|
| **1. Siklus** | Awal semester: buka dashboard mutu | "Saatnya audit RPS semester ini" | 😐 Profesional | Dashboard LPM |
| **2. Sampling** | Pilih sampling RPS dari berbagai prodi | "Ambil 20% dari 500 RPS = 100 RPS" | 😐 Analitis | Filter dashboard |
| **3. Validasi Massal** | Jalankan AI Validator massal | "AI bisa validasi 100 RPS dalam 10 menit!" | 😊 Terkesan | AI Validator |
| **4. Analisis** | Lihat statistik hasil validasi | "15% RPS perlu perbaikan alignment" | 😐 Analitis | Report |
| **5. Rekomendasi** | Buat rekomendasi ke kaprodi | "Kirim feedback ke prodi A, B, C" | 🙂 Berkontribusi | Notifikasi |
| **6. Audit Trail** | Export audit trail untuk BAN-PT | "Semua perubahan tercatat rapi" | 😊 Lega | Audit log |
| **7. Laporan** | Buat laporan ke rektorat | "Kualitas RPS meningkat 20% dari semester lalu" | 😊 Bangga | Report export |

---

## User Journey 5: Super Admin Onboarding Universitas Baru

| Tahap | Aktivitas | Pikiran | Emosi | Touchpoint |
|-------|-----------|---------|-------|------------|
| **1. Sales** | Tim sales closing deal dengan universitas | "Universitas X mau pakai paket Enterprise" | 😊 Senang | CRM/Sales |
| **2. Buat Tenant** | Login Super Admin, buat tenant baru | "Isi data universitas, assign paket" | 😐 Fokus | Admin panel |
| **3. Konfigurasi** | Setup paket langganan, billing period | "Paket Enterprise, 1 tahun" | 😐 Fokus | Billing config |
| **4. Admin Pertama** | Buat admin universitas, kirim invitation | "Admin univ nanti yang kelola sendiri" | 🙂 Selesai | Invitation email |
| **5. Monitoring** | Cek status tenant: aktif, usage | "Tenant X sudah mulai pakai, 50 RPS dibuat" | 🙂 Pantau | Dashboard SA |

---

## User Journey 6: Dosen Mendapat Revisi dan Memperbaiki

| Tahap | Aktivitas | Pikiran | Emosi | Touchpoint |
|-------|-----------|---------|-------|------------|
| **1. Notifikasi** | "RPS Anda perlu revisi" | "Yah, ada yang salah..." | 😟 Kecewa | Notifikasi |
| **2. Cek** | Buka hasil review | "Oh, Sub-CPMK kurang measurable" | 😐 Mengerti | Review result |
| **3. Revisi** | Edit Sub-CPMK sesuai saran | "Tinggal perbaiki 2 Sub-CPMK, tidak susah" | 😐 Fokus | RPS Editor |
| **4. Validasi Ulang** | Jalankan AI Validator lagi | "Oke, sekarang alignment 90%, pass!" | 🙂 Lega | AI Validator |
| **5. Ajukan Ulang** | Klik "Ajukan Review Kembali" | "Semoga kali ini approve" | 😐 Optimis | Submit |
| **6. Approved** | "RPS Anda telah disetujui" | "YES! Akhirnya!" | 🎉 Senang | Notifikasi |

---

## Pain Point Visualization

```
Level of User Frustration Per Stage

Tinggi  │                    ╱╲
        │                   ╱  ╲
        │                  ╱    ╲
        │        ╱╲       ╱      ╲
        │       ╱  ╲     ╱        ╲_____
        │      ╱    ╲   ╱
Rendah  │_____╱      ╲_╱
        │
        └────┬────┬────┬────┬────┬────┬────
            Start  Step  Step  Step  Step  Review  Approved
                    1      3     5     7
```

Penurunan frustrasi signifikan terjadi di step di mana AI Assistant membantu (Step 3, 5, 7).

---

**Navigasi:** [Sebelumnya: Use Case](14-use-case.md) | [Daftar Isi](../README.md) | [Berikutnya: Workflow](16-workflow.md)
