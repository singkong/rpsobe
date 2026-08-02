# 13 — Non Functional Requirement

## Kebutuhan Non-Fungsional

### NFR-1: Performa

| ID | Requirement | Target | Metode Verifikasi |
|----|-------------|--------|-------------------|
| NFR-P01 | Page load time (First Contentful Paint) | < 1.5 detik | Lighthouse Audit |
| NFR-P02 | Time to Interactive (TTI) | < 3 detik | Lighthouse Audit |
| NFR-P03 | API response time (p95) | < 500 ms | Load testing (k6) |
| NFR-P04 | AI response time (p95) | < 15 detik | Monitoring |
| NFR-P05 | Concurrent users supported (per tenant) | 500 | Load testing |
| NFR-P06 | Database query time (95th percentile) | < 100 ms | Query profiling |
| NFR-P07 | Export Word/PDF generation time | < 30 detik untuk 1 RPS | Manual testing |
| NFR-P08 | Time to first AI generation | < 20 detik | Monitoring |

### NFR-2: Keamanan

| ID | Requirement | Detail |
|----|-------------|--------|
| NFR-S01 | Enkripsi data at rest | AES-256 |
| NFR-S02 | Enkripsi data in transit | TLS 1.3 |
| NFR-S03 | Password hashing | bcrypt/argon2 |
| NFR-S04 | SQL injection prevention | Laravel Eloquent ORM + parameterized queries |
| NFR-S05 | XSS prevention | Laravel Blade auto-escaping |
| NFR-S06 | CSRF protection | Laravel CSRF tokens |
| NFR-S07 | Rate limiting API | 60 req/min per user |
| NFR-S08 | Session timeout | 30 menit inactivity |
| NFR-S09 | Input validation | Server-side + client-side |
| NFR-S10 | File upload scanning | Validasi tipe MIME + ukuran |
| NFR-S11 | Security headers | HSTS, CSP, X-Frame-Options, X-Content-Type-Options |
| NFR-S12 | Audit log tamper-proof | Log tidak dapat dihapus/dimodifikasi |
| NFR-S13 | Data isolation antar tenant | Row-level security via tenant_id |
| NFR-S14 | API key untuk integrasi eksternal | Rotatable API keys |
| NFR-S15 | Vulnerability scanning berkala | OWASP ZAP / Snyk |

### NFR-3: Ketersediaan (Availability)

| ID | Requirement | Target |
|----|-------------|--------|
| NFR-A01 | System uptime | 99.9% (SLA) |
| NFR-A02 | Scheduled maintenance window | Minggu 00:00-04:00 WIB |
| NFR-A03 | Maximum downtime per incident | < 1 jam |
| NFR-A04 | Disaster Recovery Time (RTO) | < 4 jam |
| NFR-A05 | Disaster Recovery Point (RPO) | < 1 jam |
| NFR-A06 | Graceful degradation | Fitur non-kritikal boleh unavailable saat peak |

### NFR-4: Skalabilitas

| ID | Requirement | Target |
|----|-------------|--------|
| NFR-SC01 | Horizontal scaling support | Web server stateless |
| NFR-SC02 | Database read replicas | Minimal 1 replica di production |
| NFR-SC03 | Caching layer | Redis untuk session, cache, queue |
| NFR-SC04 | Queue system | Asynchronous jobs (AI generation, export, email) |
| NFR-SC05 | Auto-scaling ready | Arsitektur siap auto-scale |
| NFR-SC06 | Maximum tenants | 1.000+ |
| NFR-SC07 | Maximum RPS per tenant | 10.000+ |

### NFR-5: Keandalan (Reliability)

| ID | Requirement | Target |
|----|-------------|--------|
| NFR-R01 | Error rate | < 0.1% dari total request |
| NFR-R02 | Data durability | 99.999999999% (11 nines) via backup |
| NFR-R03 | Mean Time Between Failures (MTBF) | > 720 jam (30 hari) |
| NFR-R04 | Idempotency untuk operasi kritikal | Ya (payment, status change) |

### NFR-6: Kompatibilitas

| ID | Requirement | Detail |
|----|-------------|--------|
| NFR-C01 | Browser support | Chrome 100+, Firefox 100+, Edge 100+, Safari 16+ |
| NFR-C02 | Responsive design | Desktop (1280px+), Tablet (768px-1279px), Mobile (320px-767px) |
| NFR-C03 | Database | MariaDB 10.11+ |
| NFR-C04 | PHP version | PHP 8.4+ |
| NFR-C05 | Web server | Nginx 1.25+ |
| NFR-C06 | OS | Linux (Ubuntu 24.04 LTS) |

### NFR-7: Maintainability

| ID | Requirement | Detail |
|----|-------------|--------|
| NFR-M01 | Code style standard | PSR-12, Laravel conventions |
| NFR-M02 | Static analysis | Laravel Pint + PHPStan level 5+ |
| NFR-M03 | Test coverage minimum | 80% (backend), 60% (frontend) |
| NFR-M04 | Documentation | PHPDoc pada semua method publik |
| NFR-M05 | Modular architecture | Service class pattern, Action classes |
| NFR-M06 | Dependency injection | Constructor injection di semua class |

### NFR-8: Usability

| ID | Requirement | Target |
|----|-------------|--------|
| NFR-U01 | Onboarding time (dosen baru) | < 15 menit untuk membuat RPS pertama |
| NFR-U02 | Task completion rate | > 90% pengguna menyelesaikan wizard |
| NFR-U03 | Error rate (user errors) | < 5% dari interaksi |
| NFR-U04 | Accessibility | WCAG 2.1 Level AA |
| NFR-U05 | UI consistency | Design system terpusat (Tabler) |
| NFR-U06 | Bahasa antarmuka | Bahasa Indonesia |
| NFR-U07 | Help context | Tooltip dan helper text di setiap komponen |

### NFR-9: Data Management

| ID | Requirement | Detail |
|----|-------------|--------|
| NFR-D01 | Backup frequency | Harian (full), setiap 6 jam (incremental) |
| NFR-D02 | Backup retention | 30 hari (harian), 12 bulan (bulanan) |
| NFR-D03 | Data portability | Export data dalam format terbuka (CSV, JSON) |
| NFR-D04 | Data deletion (right to erasure) | Soft delete + hard delete after 90 days |

### NFR-10: Legal & Compliance

| ID | Requirement | Detail |
|----|-------------|--------|
| NFR-L01 | UU PDP (Perlindungan Data Pribadi) | Compliance dengan UU No. 27 Tahun 2022 |
| NFR-L02 | Privacy policy | Tersedia dan mudah diakses |
| NFR-L03 | Terms of service | Tersedia dan wajib disetujui saat registrasi |
| NFR-L04 | Cookie consent | Banner persetujuan cookie |
| NFR-L05 | Data processing agreement | Untuk tenant enterprise |

### NFR-11: Observability

| ID | Requirement | Detail |
|----|-------------|--------|
| NFR-O01 | Centralized logging | JSON structured logs |
| NFR-O02 | APM (Application Performance Monitoring) | Request tracing, error tracking |
| NFR-O03 | Metrics dashboard | CPU, memory, disk, DB queries |
| NFR-O04 | Alerting | Email/Slack alert untuk error kritis |
| NFR-O05 | Health check endpoint | `/api/health` untuk load balancer |

---

**Navigasi:** [Sebelumnya: Functional Requirement](12-functional-requirement.md) | [Daftar Isi](../README.md) | [Berikutnya: Use Case](14-use-case.md)
