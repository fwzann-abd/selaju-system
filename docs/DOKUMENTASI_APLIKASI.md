# 📖 Dokumentasi Aplikasi Selaju LMS System

> **Versi**: 3.0 (Branch `dev`)
> **Framework**: Laravel 12 | PHP 8.2+
> **Last Updated**: April 2026

---

## Daftar Isi

1. [Arsitektur Sistem](#arsitektur-sistem)
2. [Modul Aplikasi](#modul-aplikasi)
3. [Autentikasi & Otorisasi](#autentikasi--otorisasi)
4. [API Reference](#api-reference)
5. [Database Schema](#database-schema)
6. [Konfigurasi & Environment](#konfigurasi--environment)
7. [Testing](#testing)
8. [Deployment](#deployment)

---

## Arsitektur Sistem

### Gambaran Umum

Selaju LMS System adalah backend API monolitik yang melayani sistem Learning Management System. Sistem ini terdiri dari dua sisi utama:

1. **Admin Dashboard** — Blade + Alpine.js (web-based, `auth` guard)
2. **Public API** — RESTful JSON API (SPA clients, `sanctum` guard)

### Stack Teknologi

| Layer | Teknologi |
|-------|-----------|
| Framework | Laravel 12 |
| Language | PHP 8.2+ |
| Database | MySQL 8.0+ |
| Auth (API) | Laravel Sanctum (Token-based) |
| Auth (Admin) | Session-based (Blade) |
| Real-time | Laravel Reverb (WebSocket) |
| Queue | Database/Redis driver |
| CSS | Tailwind CSS v4 |
| JS (Admin) | Alpine.js v3 |
| Testing | PHPUnit 11 |

### Pola Arsitektur

```
┌─────────────────────────────────────────────────┐
│                   Frontend Apps                  │
│  (SPA / Mobile / Admin Dashboard)               │
└──────────────┬──────────────────┬────────────────┘
               │ REST API         │ WebSocket
               ▼                  ▼
┌──────────────────────┐  ┌─────────────────┐
│  Laravel Application │  │  Laravel Reverb  │
│  (Sanctum Auth)      │  │  (Broadcasting)  │
└──────────┬───────────┘  └────────┬────────┘
           │                       │
           ▼                       ▼
┌──────────────────────────────────────────┐
│              MySQL Database              │
└──────────────────────────────────────────┘
```

---

## Modul Aplikasi

### 1. 🎓 LMS Melesat — Learning Management System

Sistem manajemen pembelajaran untuk guru dan siswa dengan admin dashboard lengkap.

**Fitur Admin:**
- **Guru** — CRUD lengkap + halaman detail (show) dengan jadwal mengajar
- **Siswa** — CRUD lengkap + halaman detail dengan info kelas yang diikuti
- **Kelas** — CRUD lengkap + assign siswa + detail dengan daftar siswa
- **Mata Pelajaran** — CRUD lengkap + field `type` (Umum/Jurusan) + detail dengan statistik jadwal
- **Jadwal KBM** — CRUD via halaman dedicated (create/edit) + filter (kelas/guru/hari)
- Manajemen ruangan (room)
- Materi kursus (course material)
- Presensi siswa

**Navigasi:**
- Sidebar collapsible dengan auto-expand saat menu aktif
- Via-aware active state: menu yang di-share antara "Sekolah" dan "LMS Melesat" menggunakan query parameter `?via=` untuk menentukan konteks aktif
- Nama resource pada tabel index berfungsi sebagai link ke halaman detail

**Layout:** Semua view menggunakan component `<x-app-layout>` (bukan `@extends`)

**Model:** `Classroom`, `ClassroomStudent`, `Teacher`, `Student`, `Subject`, `Schedule`, `Room`, `CourseMaterial`, `Attendance`, `Material`

---

### 2. 👤 Manajemen User & Akun

Dua jenis user dalam sistem:

| Tipe | Model | Guard | Digunakan Untuk |
|------|-------|-------|-----------------|
| Admin/Staff | `User` | `web` | Dashboard admin |
| Peserta/Pelajar | `Account` | `sanctum` | API (SPA/Mobile) |

**Role System:**
- `User` → role dari `UserGroup` (super_admin, admin, guru, dll)
- `Account` → role ditentukan dari relasi `teacher()` / `student()`

**Model:** `User`, `UserGroup`, `UserGroupPermission`, `Account`, `Student`, `Teacher`, `School`, `Generation`

---

### 3. 🏫 Sekolah — Manajemen Institusi

Manajemen data sekolah, guru, siswa, dan angkatan.

**Model:** `School`, `Student`, `Teacher`, `Generation`

---

## Autentikasi & Otorisasi

### API Authentication (Sanctum)

```
POST /api/login          → Mendapatkan Bearer token
POST /api/logout         → Revoke token aktif
GET  /api/me             → Profil user terautentikasi
```

**Single-session enforcement:** Setiap login baru akan menghapus token sebelumnya.

### Role Middleware

```php
// Penggunaan di routes
Route::middleware(['auth:sanctum', 'role:super_admin'])->group(...)
Route::middleware(['auth:sanctum', 'role:teacher'])->group(...)
Route::middleware(['auth:sanctum', 'role:student'])->group(...)
```

**Resolusi role:**
- `User` → dari `userGroup->name`
- `Account` → dari relasi `teacher()` atau `student()`

### Permission System (Admin)

- `Module` → mendefinisikan modul aplikasi
- `ModuleAccess` → mengaitkan modul ke user/group
- `Menu` → navigasi dinamis berdasarkan permission

---

## API Reference

### Public Endpoints (Tanpa Auth)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/login` | Login, dapatkan token |
| POST | `/api/register` | Registrasi akun baru |
| POST | `/api/check-nisn` | Cek NISN siswa |
| GET | `/api/students` | Daftar siswa |
| GET | `/api/students/{id}` | Detail siswa |

### Protected Endpoints (Auth Required)

#### Profil & Akun
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/me` | Profil user |
| PATCH | `/api/me` | Update profil |
| POST | `/api/logout` | Logout |
| GET | `/api/username/check` | Cek ketersediaan username |
| POST | `/api/email/verification-notification` | Kirim email verifikasi |
| POST | `/api/email/verify` | Verifikasi email |

#### LMS Admin (Role: super_admin)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| CRUD | `/api/lms/classrooms` | Manajemen kelas |
| CRUD | `/api/lms/teachers` | Manajemen guru |
| CRUD | `/api/lms/students` | Manajemen siswa |
| CRUD | `/api/lms/subjects` | Manajemen mata pelajaran |
| CRUD | `/api/lms/schedules` | Manajemen jadwal KBM |
| CRUD | `/api/lms/rooms` | Manajemen ruangan |
| GET | `/api/lms/schools` | Daftar sekolah |
| GET | `/api/lms/generations` | Daftar angkatan |
| GET | `/api/lms/attendances` | Data presensi |

#### LMS Teacher (Role: teacher)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/lms/teacher/schedules` | Jadwal guru |
| GET | `/api/lms/teacher/schedules/{id}/attendance-sheet` | Lembar absensi |
| CRUD | `/api/lms/teacher/materials` | Materi pembelajaran |
| CRUD | `/api/lms/teacher/attendances` | Presensi kelas |

#### LMS Student (Role: student)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/lms/student/schedules` | Jadwal siswa |
| GET | `/api/lms/student/materials` | Materi pelajaran |
| GET | `/api/lms/student/materials/classrooms/{id}` | Materi per kelas |
| GET | `/api/lms/student/materials/{id}` | Detail materi |
| GET | `/api/lms/student/materials/{id}/download` | Download materi |
| GET | `/api/lms/student/attendances` | Riwayat kehadiran |

---

## Database Schema

### Diagram

Lihat [`DATABASE_ERD.md`](DATABASE_ERD.md) untuk dokumentasi lengkap.

### Model Utama & Relasi

```
Account (accounts)
├── hasOne  → Student
├── hasOne  → Teacher
└── belongsTo → School

User (users)
└── belongsTo → UserGroup

Teacher (teachers)
├── belongsTo → Account
├── belongsTo → School
├── hasMany → Classroom
├── hasMany → Schedule
└── hasMany → Material

Classroom (classrooms)
├── belongsTo → Teacher
├── belongsToMany → Student
├── hasMany → Schedule
└── hasMany → CourseMaterial

Schedule (schedules)
├── belongsTo → Classroom
├── belongsTo → Teacher
├── belongsTo → Subject
├── belongsTo → Room
└── hasMany → Attendance
```

---

## Konfigurasi & Environment

### Environment Variables

Salin `.env.example` ke `.env` dan sesuaikan:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=selaju
DB_USERNAME=root
DB_PASSWORD=

# Sanctum (SPA)
SANCTUM_STATEFUL_DOMAINS=localhost:3000

# Reverb (WebSocket) — opsional, untuk fitur real-time
BROADCAST_CONNECTION=log
REVERB_APP_ID=selaju-local
REVERB_APP_KEY=selaju-local-key
REVERB_APP_SECRET=selaju-local-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

## Testing

### Menjalankan Test

```bash
# Semua test
php artisan test

# Test spesifik
php artisan test --filter=ParticipantAuthControllerTest

# Dengan coverage
php artisan test --coverage
```

### Test Coverage

| Module | File | Tests | Status |
|--------|------|-------|--------|
| Auth | `ParticipantAuthControllerTest` | 16 | ✅ |
| LMS Classroom | `ClassroomControllerTest` | 8 | ✅ |
| Smoke | `ExampleTest` | 3 | ✅ |
| **Total** | | **31 tests, 117 assertions** | ✅ |

### Factories Tersedia

| Factory | States |
|---------|--------|
| `AccountFactory` | — |
| `UserFactory` | `unverified()` |
| `UserGroupFactory` | `superAdmin()`, `teacher()` |
| `SchoolFactory` | — |
| `TeacherFactory` | — |
| `ClassroomFactory` | — |

---

## Deployment

### Prerequisites

- PHP 8.2+ dengan extension: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- Composer 2.x
- MySQL 8.0+
- Node.js 18+ (untuk build assets)

### Langkah Deploy

```bash
# 1. Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Migrate database
php artisan migrate --force

# 4. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Start services
php artisan serve              # atau Nginx/Apache
php artisan queue:work          # Queue worker (jika digunakan)
```

---

## Dokumentasi Tambahan

Dokumentasi teknis detail tersedia di folder `docs/`:

| File | Isi |
|------|-----|
| [`QUICK_START.md`](QUICK_START.md) | Panduan cepat memulai |
| [`LMS_MELESAT_IMPLEMENTATION.md`](LMS_MELESAT_IMPLEMENTATION.md) | Implementasi LMS |
| [`DATABASE_ERD.md`](DATABASE_ERD.md) | Entity Relationship Diagram |
| [`PERMISSIONS_DOCUMENTATION.md`](PERMISSIONS_DOCUMENTATION.md) | Sistem permission |
| [`GENERATIONS_FEATURE.md`](GENERATIONS_FEATURE.md) | Fitur angkatan |
| [`info_LMS/API_DOCUMENTATION.md`](info_LMS/API_DOCUMENTATION.md) | API docs LMS |
