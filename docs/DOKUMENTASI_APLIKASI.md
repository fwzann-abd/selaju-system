# 📖 Dokumentasi Aplikasi Selaju System

> **Versi**: 2.0 (Branch `fzn`)
> **Framework**: Laravel 12 | PHP 8.2+
> **Last Updated**: April 2026

---

## Daftar Isi

1. [Arsitektur Sistem](#arsitektur-sistem)
2. [Modul Aplikasi](#modul-aplikasi)
3. [Autentikasi & Otorisasi](#autentikasi--otorisasi)
4. [API Reference](#api-reference)
5. [Database Schema](#database-schema)
6. [Broadcasting & Real-time](#broadcasting--real-time)
7. [Konfigurasi & Environment](#konfigurasi--environment)
8. [Testing](#testing)
9. [Deployment](#deployment)

---

## Arsitektur Sistem

### Gambaran Umum

Selaju System adalah backend API monolitik yang melayani berbagai aplikasi frontend. Sistem ini terdiri dari dua sisi utama:

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

### 1. 🛒 Sejajan — Marketplace Pelajar

Platform jual-beli antar pelajar dengan fitur toko, produk, kategori, keranjang, dan pemesanan real-time.

**Fitur:**
- Registrasi toko per akun peserta
- Manajemen produk per toko (CRUD, foto, stok)
- Kategori produk per toko
- Keranjang belanja (multi-toko)
- Pemesanan dengan notifikasi real-time ke seller
- Tracking status pesanan (pending → processing → ready → completed/cancelled)

**Model:** `Sejajan`, `SejajanProduct`, `SejajanCategory`, `SejajanOrder`, `SejajanOrderItem`, `SejajanCartItem`

---

### 2. 💰 Donasi — Sistem Pembayaran

Modul donasi dengan integrasi payment gateway DOKU (sandbox/production).

**Fitur:**
- Donasi publik (tanpa login) dan authenticated
- Leaderboard donatur (ranking berdasar jumlah)
- Metode pembayaran: QRIS, Virtual Account (Permata), Manual Transfer
- DOKU callback verification (HMAC-SHA256)
- Verifikasi manual transfer oleh admin

**Model:** `Donation`, `ManualTransfer`, `BankAccount`

---

### 3. 📚 Perpossagar — Perpustakaan Digital

Perpustakaan digital dengan koleksi buku PDF, sistem kategori, dan hero books.

**Fitur:**
- Katalog buku dengan filter bahasa dan kategori
- Upload & baca PDF langsung di aplikasi
- Hero books (showcase di halaman utama, max 5)
- Sistem author yang terhubung ke akun peserta
- Pencarian buku (judul, author, kategori)

**Model:** `PerpossagarBook`, `PerpossagarCategory`, `PerpossagarAuthor`, `PerpossagarBookLanguage`

---

### 4. 🎓 LMS Melesat — Learning Management System

Sistem manajemen pembelajaran untuk guru dan siswa.

**Fitur:**
- Manajemen kelas (classroom) per guru
- Penjadwalan pelajaran (schedule)
- Daftar mata pelajaran (subject)
- Manajemen ruangan (room)
- Materi kursus (course material)
- Presensi siswa

**Model:** `Classroom`, `ClassroomStudent`, `Teacher`, `Student`, `Subject`, `Schedule`, `Room`, `CourseMaterial`, `Attendance`

---

### 5. 🏫 Webex Ekskul — Ekstrakurikuler

Manajemen kegiatan ekstrakurikuler sekolah.

**Fitur:**
- CRUD ekstrakurikuler
- Manajemen peserta ekskul
- Pengurus ekskul
- Presensi kegiatan
- Laporan kegiatan

**Model:** `WebexEkskul`, `WebexParticipant`, `WebexPengurus`, `WebexAttendance`, `WebexReport`

---

### 6. 📋 Eplin — Penegakan Disiplin

Sistem pencatatan pelanggaran dan tata tertib siswa.

**Fitur:**
- Jenis-jenis pelanggaran (tipe + poin)
- Pencatatan pelanggaran per siswa
- Petugas piket (officer)
- Rekap pelanggaran siswa

**Model:** `EplinViolation`, `EplinViolationType`, `EplinOfficer`, `EplinAttendance`

---

### 7. 📰 Artikel & Konten

CMS sederhana untuk publikasi artikel dan berita sekolah.

**Model:** `Article`, `ArticleCategory`

---

### 8. 👤 Manajemen User & Akun

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
| GET | `/api/sejajans` | List semua toko |
| GET | `/api/sejajans/{id}` | Detail toko + produk |
| GET | `/api/donations` | Leaderboard donasi |
| GET | `/api/donations/{id}` | Detail donasi |
| GET | `/api/donations/banks` | Daftar bank tersedia |
| GET | `/api/perpossagar/books` | Katalog buku |
| GET | `/api/perpossagar/categories` | Kategori buku |
| GET | `/api/articles` | Daftar artikel |
| POST | `/api/payment/callback` | DOKU payment callback |

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

#### Sejajan Marketplace
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/sejajans/my-stores` | Toko milik saya |
| POST | `/api/sejajans` | Buat toko baru |
| PUT | `/api/sejajans/{id}` | Update toko (owner only) |
| DELETE | `/api/sejajans/{id}` | Hapus toko (owner only) |
| POST | `/api/sejajans/{slug}/products` | Tambah produk |
| PUT | `/api/sejajans/{slug}/products/{id}` | Update produk |
| DELETE | `/api/sejajans/{slug}/products/{id}` | Hapus produk |
| GET | `/api/sejajans/{slug}/categories` | Kategori toko |
| POST | `/api/sejajans/{slug}/categories` | Tambah kategori |
| POST | `/api/sejajans/orders` | Buat pesanan |
| GET | `/api/sejajans/my-orders` | Pesanan saya (buyer) |
| GET | `/api/sejajans/{slug}/orders` | Pesanan toko (seller) |
| PUT | `/api/sejajans/{slug}/orders/{id}/status` | Update status pesanan |

#### Keranjang
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/sejajans/cart` | Lihat keranjang |
| POST | `/api/sejajans/cart` | Tambah item |
| PATCH | `/api/sejajans/cart/{id}` | Update quantity |
| DELETE | `/api/sejajans/cart/{id}` | Hapus item |
| DELETE | `/api/sejajans/cart` | Kosongkan keranjang |

#### Donasi
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/donations` | Buat donasi baru |
| GET | `/api/donations/history` | Riwayat donasi saya |
| POST | `/api/donations/manual-transfer` | Upload bukti transfer |

#### LMS (Role: super_admin)
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/lms/classrooms` | List kelas |
| POST | `/api/lms/classrooms` | Buat kelas |
| GET | `/api/lms/classrooms/{id}` | Detail kelas |
| PUT | `/api/lms/classrooms/{id}` | Update kelas |
| DELETE | `/api/lms/classrooms/{id}` | Hapus kelas |

#### Webex Ekskul
| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/webex/ekskuls` | List ekskul |
| POST | `/api/webex/ekskuls` | Buat ekskul |
| GET/PUT/DELETE | `/api/webex/ekskuls/{id}` | CRUD ekskul |
| */api/webex/ekskuls/{id}/participants* | Peserta ekskul |
| */api/webex/ekskuls/{id}/pengurus* | Pengurus ekskul |
| */api/webex/ekskuls/{id}/attendances* | Presensi |
| */api/webex/ekskuls/{id}/reports* | Laporan |

---

## Database Schema

### Diagram

- **ERD**: [`docs/erd.png`](erd.png)
- **UML**: [`docs/uml.png`](uml.png)
- **Skema Visual**: [`docs/skema.png`](skema.png)

### Model Utama & Relasi

```
Account (accounts)
├── hasMany → Sejajan (toko marketplace)
├── hasOne  → Student
├── hasOne  → Teacher
├── hasMany → Donation
├── hasMany → SejajanOrder (sebagai buyer)
├── hasMany → SejajanCartItem
└── belongsTo → School

User (users)
└── belongsTo → UserGroup

Sejajan (sejajans)
├── belongsTo → Account (owner)
├── hasMany → SejajanProduct
├── hasMany → SejajanCategory
└── hasMany → SejajanOrder

Donation (donations)
├── belongsTo → Account (nullable, anonymous OK)
└── hasOne → ManualTransfer

Teacher (teachers)
├── belongsTo → Account
├── belongsTo → School
├── hasMany → Classroom
└── hasMany → Schedule

Classroom (classrooms)
├── belongsTo → Teacher
├── belongsToMany → Student
└── hasMany → Schedule
```

---

## Broadcasting & Real-time

### Status: ⚠️ Dikonfigurasi Minimal — Belum Aktif

Reverb/Echo sudah ter-install dan **non-breaking** (tidak menyebabkan error), namun **belum aktif** karena:
- `.env` belum memiliki `VITE_REVERB_*` vars (Echo gracefully skip)
- `BROADCAST_CONNECTION=log` (events hanya di-log, tidak di-broadcast)
- Reverb server belum dijalankan

> **📌 TODO — Aktifkan saat fitur Sejajan siap production:**
> Fitur real-time order notification (buyer ↔ seller) membutuhkan Reverb aktif.
> Lihat panduan setup lengkap di bawah.

### Arsitektur

```
Browser (Alpine.js)                        Laravel Backend
┌───────────────────┐                     ┌──────────────────────┐
│  laravel-echo     │ ◄── WebSocket ──►   │  Laravel Reverb      │
│  pusher-js        │                     │  (php artisan        │
│                   │                     │   reverb:start)      │
└───────────────────┘                     └──────────┬───────────┘
                                                     │
                                          ┌──────────▼───────────┐
                                          │  Events              │
                                          │  - NewOrderReceived  │
                                          │  - OrderStatusUpdated│
                                          └──────────────────────┘
```

### Private Channels

| Channel | Digunakan Untuk |
|---------|-----------------|
| `orders.buyer.{accountId}` | Buyer menerima update status pesanan |
| `orders.seller.{accountId}` | Seller menerima pesanan baru |

### Events

| Event | Channel | Trigger |
|-------|---------|---------|
| `NewOrderReceived` | `orders.seller.*` | Pesanan baru dibuat |
| `OrderStatusUpdated` | `orders.buyer.*` + `orders.seller.*` | Status pesanan berubah |

### Safe Guard (Non-breaking)

File `resources/js/echo.js` memiliki guard:

```javascript
const reverbAppKey = import.meta.env.VITE_REVERB_APP_KEY;
if (reverbAppKey) {
    window.Echo = new Echo({ ... });
} else {
    console.info('[Echo] Reverb not configured. Real-time features disabled.');
}
```

Artinya: **jika `VITE_REVERB_APP_KEY` tidak diset, Echo tidak diinisialisasi** dan tidak ada error. Sidebar, navigation, dan seluruh Alpine.js tetap berfungsi normal.

### Panduan Setup Lengkap (Ketika Siap Mengaktifkan)

**1. Tambahkan env vars ke `.env`** (lihat template di `.env.example`):

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=selaju-local
REVERB_APP_KEY=selaju-local-key
REVERB_APP_SECRET=selaju-local-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**2. Rebuild frontend assets:**

```bash
npm run build
```

**3. Jalankan services (3 terminal):**

```bash
# Terminal 1: Laravel app
php artisan serve

# Terminal 2: Reverb WebSocket server
php artisan reverb:start --debug

# Terminal 3: Queue worker (events ShouldBroadcast butuh queue)
php artisan queue:work
```

**4. Tambahkan listener di frontend** (contoh untuk halaman seller):

```javascript
// Di Blade view toko seller
Echo.private(`orders.seller.${sellerId}`)
    .listen('.order.new', (data) => {
        // Tampilkan toast notification
        alert(`Pesanan baru dari ${data.customer_name}!`);
    });
```

**5. Dispatch event dari controller** (belum diimplementasi):

```php
// Di SejajanOrderController@store, setelah order berhasil dibuat:
event(new NewOrderReceived($order));

// Di SejajanOrderController@updateStatus:
event(new OrderStatusUpdated($order));
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

# DOKU Payment Gateway
DOKU_CLIENT_ID=<dari-dashboard-doku>
DOKU_SECRET_KEY=<dari-dashboard-doku>
DOKU_ENV=sandbox
DOKU_NOTIFICATION_URL=https://yourdomain.com/api/payment/callback

# Reverb (WebSocket) — opsional, untuk fitur real-time Sejajan
# Lihat bagian "Broadcasting & Real-time" untuk panduan lengkap
BROADCAST_CONNECTION=log  # Ganti ke 'reverb' saat siap mengaktifkan
REVERB_APP_ID=selaju-local
REVERB_APP_KEY=selaju-local-key
REVERB_APP_SECRET=selaju-local-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### DOKU Payment (Sandbox vs Production)

| Setting | Sandbox | Production |
|---------|---------|------------|
| `DOKU_ENV` | `sandbox` | `production` |
| Callback | Loopback (mock) | Real verification |
| HMAC Check | Skipped | **Mandatory** |

---

## Testing

### Menjalankan Test

```bash
# Semua test
php artisan test

# Test spesifik
php artisan test --filter=ParticipantAuthControllerTest

# Test per file
php artisan test tests/Feature/Api/SejajanControllerTest.php
```

### Test Coverage

| Module | File | Tests | Status |
|--------|------|-------|--------|
| Auth | `ParticipantAuthControllerTest` | 16 | ✅ |
| LMS Classroom | `ClassroomControllerTest` | 8 | ✅ |
| Sejajan | `SejajanControllerTest` | 12 | ✅ |
| Donation | `DonationControllerTest` | 10 | ✅ |
| Smoke | `ExampleTest` | 3 | ✅ |
| **Total** | | **49 tests, 183 assertions** | ✅ |

### Factories Tersedia

| Factory | States |
|---------|--------|
| `AccountFactory` | — |
| `UserFactory` | `unverified()` |
| `UserGroupFactory` | `superAdmin()`, `teacher()` |
| `SchoolFactory` | — |
| `TeacherFactory` | — |
| `ClassroomFactory` | — |
| `SejajanFactory` | `inactive()` |
| `DonationFactory` | `paid()` |

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
php artisan reverb:start       # WebSocket (jika digunakan)
php artisan queue:work          # Queue worker (jika digunakan)
```

---

## Dokumentasi Tambahan

Dokumentasi teknis detail tersedia di folder `docs/`:

| File | Isi |
|------|-----|
| [`QUICK_START.md`](QUICK_START.md) | Panduan cepat memulai |
| [`TESTING_GUIDE.md`](TESTING_GUIDE.md) | Panduan testing |
| [`DONATION_FEATURE.md`](DONATION_FEATURE.md) | Dokumentasi fitur donasi |
| [`LMS_MELESAT_IMPLEMENTATION.md`](LMS_MELESAT_IMPLEMENTATION.md) | Implementasi LMS |
| [`DATABASE_ERD.md`](DATABASE_ERD.md) | Entity Relationship Diagram |
| [`PERMISSIONS_DOCUMENTATION.md`](PERMISSIONS_DOCUMENTATION.md) | Sistem permission |
| [`GENERATIONS_FEATURE.md`](GENERATIONS_FEATURE.md) | Fitur angkatan |
| [`README DOKU PHP.md`](README%20DOKU%20PHP.md) | Integrasi DOKU |
| [`info_LMS/API_DOCUMENTATION.md`](info_LMS/API_DOCUMENTATION.md) | API docs LMS |
