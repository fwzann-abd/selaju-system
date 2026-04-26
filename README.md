# Selaju LMS System API

**Selaju LMS System** adalah backend API untuk Learning Management System — platform terintegrasi yang menyediakan layanan manajemen pembelajaran untuk sekolah. System ini dibangun dengan Laravel 12 dan menyediakan modul LMS lengkap meliputi jadwal KBM, materi pembelajaran, presensi siswa, dan manajemen akademik.

Proyek ini merupakan sistem backend yang melayani berbagai aplikasi frontend melalui RESTful API dengan real-time capabilities menggunakan Laravel Reverb.

## ✨ Fitur Utama

- **LMS Melesat**: Learning Management System lengkap — jadwal, materi, absensi, dan manajemen akademik
- **Multi-Role API**: Endpoint terpisah untuk Super Admin, Teacher, dan Student
- **Authentication & Authorization**: Sistem autentikasi berbasis token (Sanctum) dengan role-based permissions
- **Admin Dashboard**: Blade + Alpine.js dashboard untuk manajemen data
- **Dynamic Menu System**: Sistem menu dinamis berdasarkan permission user
- **Real-time Broadcasting**: Laravel Reverb untuk notifikasi materi baru ke kelas

## 🚀 Cara Instalasi

### Persyaratan Sistem

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+

### Langkah Instalasi

1. **Clone Repository**

    ```bash
    git clone <repository-url>
    cd selaju-system
    ```

2. **Install Dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Setup Environment**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Setup Database**

    ```bash
    php artisan migrate:fresh --seed
    ```

5. **Build Frontend & Jalankan**

    ```bash
    npm run build
    php artisan serve
    ```

## 🔑 Kredensial Default

- **Email**: `dev@gncs.dev`
- **Password**: `programmer123`
- **Role**: Super Admin

## 📡 Dokumentasi API

### Authentication

- `POST /api/login` — Login dan dapatkan token
- `POST /api/register` — Registrasi akun baru
- `POST /api/logout` — Logout user
- `GET /api/me` — Profil user terautentikasi
- `PATCH /api/me` — Update profil

### LMS Admin (Role: super_admin)

- `CRUD /api/lms/classrooms` — Manajemen kelas
- `CRUD /api/lms/teachers` — Manajemen guru
- `CRUD /api/lms/students` — Manajemen siswa
- `CRUD /api/lms/subjects` — Manajemen mata pelajaran
- `CRUD /api/lms/schedules` — Manajemen jadwal KBM
- `CRUD /api/lms/rooms` — Manajemen ruangan
- `GET /api/lms/schools` — Daftar sekolah
- `GET /api/lms/generations` — Daftar angkatan
- `GET /api/lms/attendances` — Data presensi

### LMS Teacher (Role: teacher)

- `GET /api/lms/teacher/schedules` — Jadwal guru
- `GET /api/lms/teacher/schedules/{id}/attendance-sheet` — Lembar absensi
- `CRUD /api/lms/teacher/materials` — Materi pembelajaran
- `CRUD /api/lms/teacher/attendances` — Presensi kelas

### LMS Student (Role: student)

- `GET /api/lms/student/schedules` — Jadwal siswa
- `GET /api/lms/student/materials` — Materi pelajaran
- `GET /api/lms/student/materials/{id}/download` — Download materi
- `GET /api/lms/student/attendances` — Riwayat kehadiran

## 🛠 Tech Stack

- **Framework**: Laravel 12
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum (Token-based)
- **Real-time**: Laravel Reverb (WebSocket)
- **Admin UI**: Blade + Alpine.js + Tailwind CSS v4
- **Testing**: PHPUnit 11 (31 tests, 117 assertions)
- **Queue**: Database/Redis driver

## 📖 Dokumentasi Lengkap

Dokumentasi lengkap aplikasi tersedia di:

➡️ **[`docs/DOKUMENTASI_APLIKASI.md`](docs/DOKUMENTASI_APLIKASI.md)**

---

**Status**: ✅ Active Development | Branch `dev` | Laravel 12 | PHP 8.2+
