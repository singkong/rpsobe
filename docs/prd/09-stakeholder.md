# 09 — Stakeholder

## Pemangku Kepentingan

### Stakeholder Internal (Tim Pengembang)

| Stakeholder | Peran | Kepentingan |
|-------------|------|-------------|
| CEO / Founder | Keputusan strategis | Bisnis berjalan, revenue, growth |
| Product Manager | Penanggung jawab produk | PRD akurat, roadmap jelas |
| Software Architect | Desain arsitektur | Arsitektur scalable, maintainable |
| UI/UX Designer | Desain antarmuka | UX intuitif, aksesibilitas |
| Frontend Developer | Implementasi UI | Komponen Livewire/Volt/Tabler |
| Backend Developer | Implementasi API dan logic | API performant, secure |
| QA Engineer | Quality assurance | Produk bebas bug |
| DevOps Engineer | Infrastruktur | Deployment, scaling, monitoring |
| Technical Writer | Dokumentasi | Dokumentasi teknis dan user guide |
| Customer Success | Dukungan pelanggan | Onboarding, training, support |

### Stakeholder Eksternal (Pengguna & Regulator)

| Stakeholder | Peran | Kepentingan |
|-------------|------|-------------|
| Rektor/Pimpinan Universitas | Pengambil keputusan | ROI investasi, akreditasi |
| Dekan | Pimpinan fakultas | Kualitas RPS fakultas |
| Kaprodi | Pimpinan prodi | RPS prodi lengkap dan berkualitas |
| Dosen | Penyusun RPS | Tools yang memudahkan |
| Reviewer | Penjamin mutu | Validasi dan feedback |
| Mahasiswa | Pengguna RPS | Transparansi pembelajaran |
| LPM | Penjaminan mutu institusi | Standar mutu, akreditasi |
| BAN-PT | Akreditasi institusi | Dokumen akreditasi valid |
| LAM | Akreditasi prodi | Dokumen akreditasi valid |
| Kemdikbud | Regulator | Kepatuhan regulasi |
| LLDikti | Koordinator wilayah | Pembinaan mutu |

### Stakeholder Teknis

| Stakeholder | Kepentingan |
|-------------|-------------|
| Tim Infrastruktur Kampus | Integrasi SSO, network |
| Tim Akademik Kampus | Integrasi data akademik (future) |
| Vendor LMS | Integrasi API (future) |

## Matriks Pengaruh vs Kepentingan

```
Tinggi  |  Kemdikbud (Keep Satisfied)    |  Rektor, Dekan, Kaprodi (Manage Closely)
P       |  BAN-PT, LAM                    |  Dosen, Reviewer, LPM
E       |                                 |
N       |---------------------------------|-----------------------------------
G       |                                 |
A       |  Stakeholder Industri (Monitor) |  Mahasiswa (Keep Informed)
R       |                                 |
U       |                                 |
H       |                                 |
Rendah  |                                 |
        |---------------------------------|-----------------------------------
        Rendah                        KEPENTINGAN                       Tinggi
```

## Strategi Komunikasi

| Stakeholder | Frekuensi | Metode | Konten |
|-------------|-----------|--------|--------|
| Rektor | Per semester | Presentasi | Progress, hasil akreditasi |
| Dekan | Bulanan | Dashboard + email | Status RPS fakultas |
| Kaprodi | Mingguan | Dashboard + meeting | Progress RPS, issue |
| Dosen | Harian (saat penyusunan) | In-app notification | Status review, reminder |
| Reviewer | Sesuai assignment | Email + in-app | Tugas review baru |
| LPM | Bulanan | Dashboard + laporan | Statistik mutu |
| Tim Dev | Harian | Standup + Slack | Progress sprint |

## Eskalasi

| Level | Trigger | Eskalasi ke |
|-------|---------|-------------|
| L1 | Bug minor, pertanyaan | Customer Success |
| L2 | Bug mengganggu workflow | Tech Lead |
| L3 | Down, data loss | CTO & DevOps |
| L4 | Masalah billing/akun | Product Manager |

---

**Navigasi:** [Sebelumnya: User Persona](08-user-persona.md) | [Daftar Isi](../README.md) | [Berikutnya: Scope](10-scope.md)
