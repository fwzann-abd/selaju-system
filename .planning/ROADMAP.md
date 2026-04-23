# Roadmap: Selaju System v1.0

**Milestone:** v1.0 Backend Foundation
**Goal:** Melengkapi backend infrastructure yang masih incomplete agar sistem siap digunakan oleh mobile app
**Phases:** 6
**Requirements covered:** 27/27 ✓

---

## Phase 1: LMS Admin CRUD Forms

**Goal:** Admin dapat melakukan Create/Edit untuk kelas, guru, siswa, jadwal, dan mata pelajaran melalui admin dashboard — bukan hanya read-only index.

**Requirements:** LMS-01, LMS-02, LMS-03, LMS-04, LMS-05, LMS-06, LMS-07, LMS-08, LMS-09, LMS-10

**Success criteria:**
1. Admin dapat membuat classroom baru via form modal/page dan data tersimpan di database
2. Admin dapat mengedit classroom existing dan perubahan ter-reflect di index page
3. Admin dapat CRUD guru dan siswa dengan form yang consistent dengan UI pattern existing
4. Admin dapat membuat jadwal KBM (pilih guru, kelas, mapel, slot waktu)
5. Admin dapat manage mata pelajaran (CRUD) dan assign siswa ke kelas

**Depends on:** Nothing (no dependencies)

---

## Phase 2: Auth — Forgot & Change Password

**Goal:** Melengkapi authentication flow dengan forgot password (via email) dan change password (authenticated user), plus rate limiting login.

**Requirements:** AUTH-01, AUTH-02, AUTH-03, AUTH-04

**Success criteria:**
1. User dapat request password reset dan menerima email dengan reset link
2. User dapat set password baru menggunakan valid reset token
3. Authenticated user dapat change password (wajib input current password)
4. Login endpoint memiliki rate limiting (max 5 attempts per minute)

**Depends on:** Nothing

---

## Phase 3: Fix Test Environment

**Goal:** Memperbaiki test environment agar seluruh migration berjalan dan semua test pass, plus menambah test coverage untuk fitur baru di Phase 1 & 2.

**Requirements:** TEST-01, TEST-02, TEST-03, TEST-04

**Success criteria:**
1. `php artisan migrate --env=testing` berjalan tanpa error
2. `php artisan test` — semua 49 existing tests pass
3. LMS Admin CRUD operations memiliki feature test (min 8 test cases)
4. Auth password reset & change password memiliki feature test (min 4 test cases)

**Depends on:** Phase 1 (LMS CRUD), Phase 2 (Auth flow)

---

## Phase 4: Broadcasting Activation

**Goal:** Mengaktifkan Laravel Reverb dan memastikan events di-dispatch dari controller saat order/material actions terjadi.

**Requirements:** BCAST-01, BCAST-02, BCAST-03, BCAST-04

**Success criteria:**
1. `php artisan reverb:start` berhasil dan WebSocket menerima koneksi
2. Event `NewOrderReceived` di-dispatch saat order Sejajan dibuat
3. Event `OrderStatusUpdated` di-dispatch saat status order berubah
4. Event `MaterialUploaded` di-dispatch saat guru upload materi
5. `.env` config `BROADCAST_CONNECTION=reverb` (bukan `log`)

**Depends on:** Nothing (can run in parallel with Phase 1-3)

---

## Phase 5: DOKU Payment Gateway

**Goal:** Mengganti dummy response DOKU dengan actual API call ke sandbox, termasuk QRIS, Virtual Account, dan webhook verification.

**Requirements:** PAY-01, PAY-02, PAY-03, PAY-04, PAY-05

**Success criteria:**
1. Package `doku/doku-php-library` terinstall dan PHP GD extension tersedia
2. `generateQrisPayment()` mengembalikan actual QRIS code dari DOKU sandbox
3. `generateVirtualAccountPayment()` mengembalikan actual VA number dari DOKU sandbox
4. Webhook endpoint menerima callback dari DOKU dan update status donasi
5. HMAC signature verification memvalidasi incoming webhooks

**Depends on:** Nothing (can run in parallel)

---

## Phase 6: Manual Transfer Verification

**Goal:** Admin dapat verify/reject manual transfer donations melalui admin dashboard.

**Requirements:** XFER-01, XFER-02, XFER-03, XFER-04

**Success criteria:**
1. Admin melihat list pending manual transfers dengan preview bukti transfer
2. Admin dapat approve transfer — status donasi berubah menjadi `paid`
3. Admin dapat reject transfer — dengan alasan penolakan yang tersimpan
4. Perubahan status ter-reflect di riwayat donasi participant

**Depends on:** Nothing (existing admin infra sufficient)

---

## Phase Summary

| # | Phase | Requirements | Depends On | Effort |
|---|-------|--------------|------------|--------|
| 1 | LMS Admin CRUD Forms | LMS-01..10 | — | Large |
| 2 | Auth Forgot & Change Password | AUTH-01..04 | — | Medium |
| 3 | Fix Test Environment | TEST-01..04 | Phase 1, 2 | Medium |
| 4 | Broadcasting Activation | BCAST-01..04 | — | Small |
| 5 | DOKU Payment Gateway | PAY-01..05 | — | Large |
| 6 | Manual Transfer Verification | XFER-01..04 | — | Small |

**Recommended execution order:**
- **Wave 1** (parallel): Phase 1 + Phase 2 + Phase 4 + Phase 6
- **Wave 2** (parallel): Phase 5
- **Wave 3** (after Wave 1): Phase 3

---
*Roadmap created: 2026-04-23*
*Last updated: 2026-04-23 after initial creation*
