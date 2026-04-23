# 🔍 Selaju System — Feature Gap Analysis

> **Date**: 23 April 2026
> **Branch**: `fzn` (v2)
> **Scope**: Web Admin + Mobile (Student & Teacher platform)
> **Analyst**: Antigravity AI

---

## 📊 Executive Summary

Selaju System memiliki **7 modul utama** yang sudah terdefinisi. Namun, banyak fitur yang **hanya tersedia di sisi API/Admin Web** dan **belum ada implementasi mobile**. Tidak ditemukan **ROADMAP.md**, **BACKLOG**, atau **`.planning/`** directory — project ini belum memiliki dokumen perencanaan formal.

---

## 📋 Module-by-Module Implementation Status

### Legend
| Icon | Meaning |
|------|---------|
| ✅ | Fully implemented |
| ⚠️ | Partially implemented |
| ❌ | Not implemented |
| 🔲 | Not applicable |

---

### 1. 🛒 Sejajan — Marketplace Pelajar

| Feature | API | Admin Web | Mobile (Student) | Mobile (Teacher) |
|---------|-----|-----------|-------------------|------------------|
| List toko | ✅ | ✅ | ⚠️ API ready | 🔲 |
| Create toko | ✅ | ❌ (view only) | ⚠️ API ready | 🔲 |
| Update toko | ✅ | ✅ | ⚠️ API ready | 🔲 |
| Delete toko | ✅ | ✅ | ⚠️ API ready | 🔲 |
| Produk CRUD | ✅ | ❌ (no admin view) | ⚠️ API ready | 🔲 |
| Kategori per toko | ✅ | ❌ | ⚠️ API ready | 🔲 |
| Keranjang belanja | ✅ | 🔲 | ⚠️ API ready | 🔲 |
| Buat pesanan | ✅ | 🔲 | ⚠️ API ready | 🔲 |
| Track pesanan (buyer) | ✅ | ❌ | ⚠️ API ready | 🔲 |
| Manage pesanan (seller) | ✅ | ❌ | ⚠️ API ready | 🔲 |
| **Real-time notifikasi pesanan** | ⚠️ Event defined | ❌ | ❌ | 🔲 |
| Sistem rating & review | ❌ | ❌ | ❌ | 🔲 |
| Wishlist / favorit | ❌ | ❌ | ❌ | 🔲 |
| Riwayat transaksi | ✅ (my-orders) | ❌ | ⚠️ API ready | 🔲 |
| Laporan penjualan seller | ❌ | ❌ | ❌ | 🔲 |

> **⚠️ Broadcasting belum aktif** — `BROADCAST_CONNECTION=log`, Reverb server belum dijalankan. Event `NewOrderReceived` dan `OrderStatusUpdated` sudah defined tapi **belum di-dispatch** dari controller.

---

### 2. 💰 Donasi — Sistem Pembayaran

| Feature | API | Admin Web | Mobile |
|---------|-----|-----------|--------|
| Leaderboard donasi | ✅ | ❌ (no admin view) | ⚠️ API ready |
| Buat donasi (publik) | ✅ | 🔲 | ⚠️ API ready |
| Buat donasi (auth) | ✅ | 🔲 | ⚠️ API ready |
| Riwayat donasi saya | ✅ | ❌ | ⚠️ API ready |
| DOKU QRIS payment | ⚠️ **Dummy data** | ❌ | ❌ |
| DOKU Virtual Account | ⚠️ **Dummy data** | ❌ | ❌ |
| Manual transfer + upload bukti | ✅ | ⚠️ (index/show only) | ⚠️ API ready |
| **Verifikasi manual transfer** | ❌ (no verify endpoint) | ❌ | 🔲 |
| Admin dashboard donasi | ❌ | ❌ | 🔲 |
| Email notification | ❌ | ❌ | ❌ |

> **⚠️ DOKU payment gateway masih dummy** — belum actual API call. Package `doku/doku-php-library` installation blocked by PHP GD extension issue. HMAC signature verification juga belum diimplementasi.

---

### 3. 📚 Perpossagar — Perpustakaan Digital

| Feature | API | Admin Web | Mobile (Student) |
|---------|-----|-----------|------------------|
| Katalog buku | ✅ | ✅ | ⚠️ API ready |
| Detail buku | ✅ | ✅ | ⚠️ API ready |
| Hero books | ✅ | ✅ | ⚠️ API ready |
| Popular books | ✅ | ❌ | ⚠️ API ready |
| Community books | ✅ | ❌ | ⚠️ API ready |
| Kategori CRUD | ✅ | ✅ | ⚠️ API ready |
| Bahasa buku CRUD | ✅ | ✅ | 🔲 |
| Upload buku (participant) | ✅ | ✅ | ⚠️ API ready |
| Baca PDF in-app | ⚠️ (file path only) | ❌ | ❌ |
| Read count tracking | ✅ | ❌ | ⚠️ API ready |
| Approval system buku | ✅ (field exists) | ❌ (no toggle) | 🔲 |
| Bookmark / reading list | ❌ | ❌ | ❌ |
| Review & rating buku | ❌ | ❌ | ❌ |
| Download offline | ❌ | ❌ | ❌ |

---

### 4. 🎓 LMS Melesat — Learning Management System

| Feature | API | Admin Web | Mobile (Teacher) | Mobile (Student) |
|---------|-----|-----------|------------------|------------------|
| **Master Data** | | | | |
| CRUD Kelas | ✅ | ⚠️ List only | 🔲 | 🔲 |
| CRUD Guru | ✅ | ⚠️ List only | 🔲 | 🔲 |
| CRUD Siswa | ✅ | ⚠️ List only | 🔲 | 🔲 |
| CRUD Mata Pelajaran | ✅ | ❌ | 🔲 | 🔲 |
| CRUD Jabatan Siswa | ✅ | ❌ | 🔲 | 🔲 |
| Assign siswa ke kelas | ✅ | ❌ | 🔲 | 🔲 |
| **Jadwal KBM** | | | | |
| CRUD Jadwal | ✅ | ⚠️ (store/update/destroy) | ❌ | ❌ |
| Lihat jadwal guru | ✅ | ❌ | ❌ **CRITICAL** | ❌ |
| Lihat jadwal siswa | ✅ | ❌ | 🔲 | ❌ **CRITICAL** |
| **Materi Pembelajaran** | | | | |
| Upload materi (guru) | ✅ | ❌ | ❌ **CRITICAL** | 🔲 |
| List materi | ✅ | ❌ | ❌ | ❌ **CRITICAL** |
| Download materi | ✅ | ❌ | 🔲 | ❌ |
| **Presensi** | | | | |
| Input presensi (guru) | ✅ | ❌ | ❌ **CRITICAL** | 🔲 |
| Lihat riwayat presensi | ✅ | ❌ | ❌ | ❌ **CRITICAL** |
| **Real-time** | | | | |
| Notifikasi materi baru | ⚠️ Event defined | ❌ | 🔲 | ❌ |
| **Tambahan (Belum ada)** | | | | |
| Nilai / gradebook | ❌ | ❌ | ❌ | ❌ |
| Tugas / assignment | ❌ | ❌ | ❌ | ❌ |
| Quiz / ujian online | ❌ | ❌ | ❌ | ❌ |
| Forum diskusi kelas | ❌ | ❌ | ❌ | ❌ |
| Progress siswa | ❌ | ❌ | ❌ | ❌ |

> **⛔ LMS Admin Web hanya READ-only (index pages)** — tidak ada form Create/Edit di halaman admin untuk kelas, guru, siswa, dan jadwal. API sudah full CRUD tapi admin dashboard belum bisa mutate data melalui web.

---

### 5. 🏫 Webex Ekskul — Ekstrakurikuler

| Feature | API | Admin Web | Mobile (Student) |
|---------|-----|-----------|------------------|
| CRUD Ekskul | ✅ | ✅ | ⚠️ API ready |
| Peserta ekskul | ✅ | ❌ (no sub-view) | ⚠️ API ready |
| Pengurus ekskul | ✅ | ❌ | ⚠️ API ready |
| Presensi kegiatan | ✅ | ❌ | ⚠️ API ready |
| Laporan kegiatan | ✅ | ❌ | ⚠️ API ready |
| Pendaftaran ekskul (student) | ❌ | ❌ | ❌ |
| Gallery foto kegiatan | ❌ | ❌ | ❌ |
| Jadwal kegiatan | ❌ | ❌ | ❌ |
| Achievement / sertifikat | ❌ | ❌ | ❌ |

---

### 6. 📋 Eplin — Penegakan Disiplin

| Feature | API | Admin Web | Mobile (Teacher/Officer) | Mobile (Student) |
|---------|-----|-----------|--------------------------|------------------|
| Jenis pelanggaran (types) | ✅ | ❌ (no type management) | ❌ | 🔲 |
| CRUD Petugas piket | ✅ | ✅ | ❌ | 🔲 |
| Catat pelanggaran | ✅ | ✅ | ❌ **Should have** | 🔲 |
| Rekap pelanggaran | ✅ | ✅ | ❌ | ❌ |
| Export pelanggaran | ✅ | ❌ (no UI) | ❌ | 🔲 |
| Presensi piket | ✅ | ❌ | ❌ | 🔲 |
| **Lihat riwayat pelanggaran sendiri** | ❌ | 🔲 | 🔲 | ❌ **Needed** |
| Dashboard/statistik pelanggaran | ❌ | ❌ | ❌ | 🔲 |
| Notifikasi ke orang tua/wali | ❌ | ❌ | ❌ | ❌ |

---

### 7. 📰 Artikel & Konten

| Feature | API | Admin Web | Mobile / Public |
|---------|-----|-----------|-----------------|
| CRUD Kategori | ✅ | ✅ | ⚠️ API ready |
| CRUD Artikel | ✅ | ✅ | ⚠️ API ready |
| List artikel publik | ✅ | 🔲 | ⚠️ API ready |
| Detail artikel | ✅ | 🔲 | ⚠️ API ready |
| Media gallery | ❌ | ❌ | ❌ |
| Pengumuman / announcement | ❌ | ❌ | ❌ |
| Event / kegiatan sekolah | ❌ | ❌ | ❌ |

---

### 8. 👤 User & Account Management

| Feature | API | Admin Web | Mobile |
|---------|-----|-----------|--------|
| Admin user CRUD | 🔲 | ✅ | 🔲 |
| Participant list | ✅ | ✅ | 🔲 |
| Student import (Excel) | 🔲 | ✅ | 🔲 |
| Student export | 🔲 | ✅ | 🔲 |
| Login (participant) | ✅ | 🔲 | ⚠️ API ready |
| Register | ✅ | 🔲 | ⚠️ API ready |
| Profile update | ✅ | 🔲 | ⚠️ API ready |
| Email verification | ✅ | 🔲 | ⚠️ API ready |
| **Forgot password** | ❌ | ❌ | ❌ |
| **Change password** | ❌ | ❌ | ❌ |
| **Upload foto profil** | ❌ | ❌ | ❌ |
| Multi-device session mgmt | ✅ (single session) | ❌ | ⚠️ |
| Push notification token | ❌ | 🔲 | ❌ |
| School CRUD | ✅ | ✅ | 🔲 |
| Generation CRUD | ✅ | ✅ | 🔲 |

---

## 🚨 Critical Missing Features — Mobile Platform

### Student Mobile App
| Priority | Feature | Status |
|----------|---------|--------|
| 🔴 P0 | Lihat jadwal pelajaran | API ✅, UI ❌ |
| 🔴 P0 | Lihat & download materi | API ✅, UI ❌ |
| 🔴 P0 | Lihat riwayat presensi | API ✅, UI ❌ |
| 🟠 P1 | Marketplace Sejajan (browse, cart, order) | API ✅, UI ❌ |
| 🟠 P1 | Perpossagar (browse, baca buku) | API ✅, UI ❌ |
| 🟡 P2 | Donasi | API ✅, UI ❌ |
| 🟡 P2 | Ekskul (lihat, daftar) | API ⚠️, UI ❌ |
| 🟡 P2 | Artikel & pengumuman | API ✅, UI ❌ |
| 🟡 P2 | Profile management | API ✅, UI ❌ |

### Teacher Mobile App
| Priority | Feature | Status |
|----------|---------|--------|
| 🔴 P0 | Lihat jadwal mengajar | API ✅, UI ❌ |
| 🔴 P0 | Upload materi ke kelas | API ✅, UI ❌ |
| 🔴 P0 | Input presensi siswa | API ✅, UI ❌ |
| 🟠 P1 | Manage materi (CRUD) | API ✅, UI ❌ |
| 🟡 P2 | Catat pelanggaran (Eplin) | API ✅, UI ❌ |

---

## 🔧 Technical Gaps & Incomplete Integrations

### 1. Broadcasting / Real-time ⚠️
- `BROADCAST_CONNECTION=log` (not reverb)
- Reverb env vars belum diset
- Event `NewOrderReceived` dan `OrderStatusUpdated` **tidak di-dispatch** dari controller
- Event `MaterialUploaded` defined tapi tidak ada dispatch

### 2. Payment Gateway (DOKU) ⚠️
- Package `doku/doku-php-library` belum terinstall
- Method `generateQrisPayment()` dan `generateVirtualAccountPayment()` return **dummy data**
- HMAC signature verification **not implemented**
- Admin verify manual transfer **not implemented**

### 3. Testing ⚠️
- Migration issue di test environment (SQLite incompatibility)
- 49 tests total — beberapa modul **tidak punya test** (Webex, Eplin, Perpossagar, LMS Teacher/Student routes)

### 4. Security Gaps
- Forgot password flow **missing**
- Rate limiting pada login endpoint **not configured**
- CORS configuration **default** (may need tuning for mobile)
- File upload validation bisa lebih strict

### 5. Admin Dashboard Gaps
- Dashboard (`/admin`) hanya summary page
- Tidak ada admin view untuk: donasi management, order management, presensi reports, statistik modul
- LMS admin hanya index pages (no CRUD forms)

---

## 📂 Technical Documents Inventory

| Document | Content | Status |
|----------|---------|--------|
| `README.md` | Project overview + API docs | ✅ Current |
| `docs/DOKUMENTASI_APLIKASI.md` | Comprehensive app docs | ✅ Good |
| `docs/DATABASE_ERD.md` | Full DB schema docs | ⚠️ Outdated (Nov 2025) |
| `docs/FINAL_STATUS.md` | LMS delivery status | ✅ |
| `docs/LMS_MELESAT_IMPLEMENTATION.md` | LMS implementation | ✅ |
| `docs/DONATION_FEATURE.md` | Donasi + DOKU | ⚠️ Has TODO list |
| `docs/GENERATIONS_FEATURE.md` | Angkatan feature | ✅ |
| `docs/PERMISSIONS_DOCUMENTATION.md` | Permission system | ✅ |
| `docs/info_LMS/API_DOCUMENTATION.md` | Melesat LMS API spec | ✅ |
| `docs/info_LMS/melesatt_postman_collection.json` | Postman collection | ✅ |
| `docs/info_LMS/BRAINBANGER.drawio` | Architecture diagram | ✅ |

### Missing Documents ❌
- **ROADMAP.md** — Product roadmap & milestones
- **BACKLOG.md** — Feature backlog & prioritization
- **CHANGELOG.md** — Version history
- **API_VERSIONING.md** — API versioning strategy
- **MOBILE_SPEC.md** — Mobile app specifications
- **DEPLOYMENT_GUIDE.md** — Production deployment

---

*Generated by Antigravity AI — 23 April 2026*
