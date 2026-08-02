# Prompt — Module 1: Authentication & Authorization

## Ringkasan Konteks (PRD)

Platform RPS OBE adalah sistem multi-tenant untuk institusi pendidikan tinggi. Setiap universitas (Tenant) memiliki admin, dosen, reviewer, dan user dengan role berbeda. Sistem memerlukan autentikasi lengkap dengan verifikasi email, reset password, session expiration, dan authorization berbasis Spatie Permission untuk membatasi akses ke modul-modul sesuai peran.

---

## Tugas

Bangun sistem autentikasi dan otorisasi lengkap menggunakan **Livewire 3 Volt functional API**, **Tabler UI**, dan **Spatie Laravel Permission**. Sistem harus mencakup pendaftaran tenant + admin, login multi-role, lupa/reset password, verifikasi email, redirect dashboard berdasarkan role, seeding role dan permission awal.

---

## File yang Harus Dibuat / Dimodifikasi

### Models
| File | Deskripsi |
|------|-----------|
| `app/Models/User.php` | Model user; implementasikan Spatie HasRoles trait |
| `app/Models/Tenant.php` | Model tenant (universitas); relasi hasMany ke User |

### Migrations
| File | Deskripsi |
|------|-----------|
| `database/migrations/xxxx_create_tenants_table.php` | Tabel tenants |
| `database/migrations/xxxx_update_users_table.php` | Tambah tenant_id, email_verified_at, dll ke tabel users |

### Livewire Volt Components (functional API)
| File | Deskripsi |
|------|-----------|
| `app/Livewire/Auth/Login.php` | Halaman login |
| `app/Livewire/Auth/Register.php` | Halaman registrasi tenant + admin |
| `app/Livewire/Auth/ForgotPassword.php` | Form lupa password |
| `app/Livewire/Auth/ResetPassword.php` | Form reset password |
| `app/Livewire/Auth/VerifyEmail.php` | Halaman verifikasi email |
| `app/Livewire/Dashboard/AdminDashboard.php` | Dashboard admin (redirect target) |
| `app/Livewire/Dashboard/DosenDashboard.php` | Dashboard dosen |
| `app/Livewire/Dashboard/ReviewerDashboard.php` | Dashboard reviewer |

### Blade Views
| File | Deskripsi |
|------|-----------|
| `resources/views/layouts/auth.blade.php` | Layout halaman auth (login, register, forgot, reset) |
| `resources/views/layouts/app.blade.php` | Layout utama (Tabler sidebar + topbar) |
| `resources/views/livewire/auth/login.blade.php` | View login |
| `resources/views/livewire/auth/register.blade.php` | View register |
| `resources/views/livewire/auth/forgot-password.blade.php` | View lupa password |
| `resources/views/livewire/auth/reset-password.blade.php` | View reset password |
| `resources/views/livewire/auth/verify-email.blade.php` | View verifikasi email |
| `resources/views/livewire/dashboard/admin-dashboard.blade.php` | View dashboard admin |
| `resources/views/livewire/dashboard/dosen-dashboard.blade.php` | View dashboard dosen |
| `resources/views/livewire/dashboard/reviewer-dashboard.blade.php` | View dashboard reviewer |

### Routes
| File | Deskripsi |
|------|-----------|
| `routes/web.php` | Semua route auth dan dashboard |

### Seeders
| File | Deskripsi |
|------|-----------|
| `database/seeders/RoleSeeder.php` | Seed default roles: SuperAdmin, Admin, Dosen, Reviewer |
| `database/seeders/PermissionSeeder.php` | Seed semua permission per modul |
| `database/seeders/DatabaseSeeder.php` | Call order seeder |

### Middleware
| File | Deskripsi |
|------|-----------|
| `app/Http/Middleware/RedirectByRole.php` | Middleware redirect berdasarkan role setelah login |
| `app/Http/Middleware/SessionTimeout.php` | Middleware auto-logout setelah 30 menit inactivity |

### Lainnya
| File | Deskripsi |
|------|-----------|
| `app/Enums/RoleEnum.php` | Enum: SuperAdmin, Admin, Dosen, Reviewer |
| `config/auth.php` | Konfigurasi guard dan provider (jika perlu modifikasi) |

---

## Persyaratan Implementasi

### 1. Login (`Login.php`)
- Gunakan Volt functional API: `use function Livewire\Volt\{state, rules, mount}`.
- State: `email`, `password`, `remember`.
- Rules: email required|email, password required|min:8.
- Method `login()`: validasi, attempt auth, regenerate session, redirect berdasarkan role.
- Tampilkan error "Email atau password salah." di atas form.
- Link ke halaman "Lupa Password" dan "Daftar".
- Rate limiting: maksimal 5 percobaan per menit per IP.

### 2. Register (`Register.php`)
- Form multi-step (wizard):
  - Step 1: Data Tenant — `tenant_name`, `tenant_code`, `tenant_domain` (opsional).
  - Step 2: Data Admin — `name`, `email`, `password`, `password_confirmation`.
- Saat submit: buat Tenant, buat User (admin tenant), assign role `Admin`, kirim email verifikasi.
- Auto-login setelah registrasi berhasil (dengan flag `email_verified_at` null).
- Tampilkan pesan sukses "Pendaftaran berhasil! Silakan cek email Anda untuk verifikasi."

### 3. Forgot Password (`ForgotPassword.php`)
- Input email, kirim password reset link via Laravel built-in notification.
- Pesan sukses: "Link reset password telah dikirim ke email Anda."
- Pesan error: "Email tidak ditemukan." — tidak spesifik demi keamanan.

### 4. Reset Password (`ResetPassword.php`)
- Menerima `token` dan `email` dari URL query parameter.
- Form: `email` (readonly), `password`, `password_confirmation`.
- Reset password, auto-login, redirect ke dashboard.

### 5. Verify Email (`VerifyEmail.php`)
- Halaman yang menerima `id` dan `hash` dari signed URL.
- Verifikasi email user, set `email_verified_at`, redirect ke dashboard.
- Pesan sukses: "Email berhasil diverifikasi."

### 6. Dashboard Redirect by Role (`RedirectByRole.php` middleware)
- Attach ke route `/dashboard`.
- Role `SuperAdmin` → `/super-admin/dashboard`
- Role `Admin` → `/admin/dashboard`
- Role `Dosen` → `/dosen/dashboard`
- Role `Reviewer` → `/reviewer/dashboard`

### 7. Role Seeder (`RoleSeeder.php`)
Gunakan Spatie Permission:
```php
Role::create(['name' => 'SuperAdmin']);
Role::create(['name' => 'Admin']);
Role::create(['name' => 'Dosen']);
Role::create(['name' => 'Reviewer']);
```

### 8. Permission Seeder (`PermissionSeeder.php`)
Buat permission terstruktur per modul:
```
user.create, user.read, user.update, user.delete
role.create, role.read, role.update, role.delete
tenant.create, tenant.read, tenant.update, tenant.delete
fakultas.create, fakultas.read, fakultas.update, fakultas.delete
prodi.create, prodi.read, prodi.update, prodi.delete
kurikulum.create, kurikulum.read, kurikulum.update, kurikulum.delete
mata-kuliah.create, mata-kuliah.read, mata-kuliah.update, mata-kuliah.delete
dosen.create, dosen.read, dosen.update, dosen.delete
cpl.create, cpl.read, cpl.update, cpl.delete
rps.create, rps.read, rps.update, rps.delete
rps.submit, rps.review, rps.approve, rps.publish
rps.export, rps.import
dashboard.access, report.access, audit.access
ai.access
```
Assign semua permission ke SuperAdmin; permission spesifik ke Admin, Dosen, Reviewer.

### 9. Layout (`layouts/auth.blade.php` dan `layouts/app.blade.php`)
- Gunakan Tabler CSS framework (CDN atau compiled via Vite).
- `auth.blade.php`: layout centered card, logo, background gradient.
- `app.blade.php`: sidebar dengan menu, topbar dengan user dropdown (profil, logout), main content area.
- Sidebar menu harus dinamis berdasarkan role user (menggunakan `@can` directive).

### 10. Session Timeout
- Middleware `SessionTimeout.php`: cek `last_activity` di session, jika > 30 menit, logout dan redirect ke login dengan pesan "Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali."

---

## Acceptance Criteria

1. [ ] User baru dapat mendaftar dengan membuat tenant dan akun admin; email verifikasi terkirim.
2. [ ] User dapat login dengan email dan password yang valid; redirect ke dashboard sesuai role.
3. [ ] User tidak dapat mengakses halaman tanpa login (redirect ke login dengan `intended` URL).
4. [ ] User dapat mereset password melalui flow lupa password yang lengkap.
5. [ ] Setelah verifikasi email, status email_verified_at terisi; user dapat mengakses semua fitur role-nya.
6. [ ] Menu sidebar hanya menampilkan item yang sesuai dengan permission user.
7. [ ] Setelah 30 menit inactivity, user di-logout otomatis dan redirect ke halaman login.
8. [ ] Semua form menampilkan validasi error dalam Bahasa Indonesia.
9. [ ] UI menggunakan komponen Tabler yang konsisten dan mobile-responsive.

---

## Tips

- Gunakan `laravel/breeze` atau `laravel/fortify` sebagai referensi arsitektur, tetapi **implementasikan dengan Livewire 3 Volt functional API**, bukan Blade statis atau Inertia.
- Install Spatie Permission dengan `composer require spatie/laravel-permission`, publish migrasi dan config, lalu gunakan trait `HasRoles` di model `User`.
- Untuk live search dan reactive state di Livewire, gunakan `wire:model.live` (bukan `.defer`).
- Gunakan `$this->redirect()` (Livewire 3) untuk redirect setelah login/register.
- Jangan gunakan `auth()->login()` langsung — gunakan `Auth::attempt()` atau method built-in Livewire/Laravel.
- Untuk verifikasi email, gunakan `Illuminate\Auth\Listeners\SendEmailVerificationNotification` dan implementasikan `MustVerifyEmail` interface di model User jika diinginkan, atau buat notifikasi kustom.
