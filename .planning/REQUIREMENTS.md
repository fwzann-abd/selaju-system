# Requirements: Selaju System

**Defined:** 2026-04-23
**Core Value:** Ekosistem digital sekolah yang utuh — akademik hingga non-akademik — diakses via mobile app

## v1.0 Requirements

Requirements for milestone v1.0 Backend Foundation. Each maps to roadmap phases.

### LMS Admin

- [ ] **LMS-01**: Admin can create a new classroom with name, grade level, and academic year
- [ ] **LMS-02**: Admin can edit existing classroom details
- [ ] **LMS-03**: Admin can create a new teacher record with user account linking
- [ ] **LMS-04**: Admin can edit existing teacher details
- [ ] **LMS-05**: Admin can create a new student record and assign to classroom
- [ ] **LMS-06**: Admin can edit existing student details
- [ ] **LMS-07**: Admin can create a new schedule (teacher, classroom, subject, time slot)
- [ ] **LMS-08**: Admin can edit existing schedule details
- [ ] **LMS-09**: Admin can manage subjects (mata pelajaran) via CRUD interface
- [ ] **LMS-10**: Admin can assign/unassign students to classrooms

### Authentication

- [ ] **AUTH-01**: User can request password reset via email link
- [ ] **AUTH-02**: User can reset password using valid token from email
- [ ] **AUTH-03**: Authenticated user can change password (requires current password)
- [ ] **AUTH-04**: Login endpoint has rate limiting to prevent brute force

### Testing Infrastructure

- [ ] **TEST-01**: Test environment runs migrations successfully (fix SQLite incompatibility)
- [ ] **TEST-02**: Existing 49 tests pass in CI-compatible environment
- [ ] **TEST-03**: LMS Admin CRUD operations have feature tests
- [ ] **TEST-04**: Auth password reset flow has feature tests

### Broadcasting

- [ ] **BCAST-01**: Reverb WebSocket server can be started and accepts connections
- [ ] **BCAST-02**: Sejajan order events (NewOrderReceived, OrderStatusUpdated) are dispatched from controllers
- [ ] **BCAST-03**: LMS material upload event (MaterialUploaded) is dispatched when teacher uploads
- [ ] **BCAST-04**: Broadcasting config switched from `log` to `reverb` driver

### Payment Gateway

- [ ] **PAY-01**: DOKU PHP library installed and configured with sandbox credentials
- [ ] **PAY-02**: QRIS payment generation returns actual DOKU API response (not dummy)
- [ ] **PAY-03**: Virtual Account payment generation returns actual DOKU API response
- [ ] **PAY-04**: DOKU webhook receives payment notification and updates donation status
- [ ] **PAY-05**: HMAC signature verification validates incoming DOKU webhooks

### Manual Transfer Verification

- [ ] **XFER-01**: Admin can view list of pending manual transfers with upload bukti
- [ ] **XFER-02**: Admin can approve a manual transfer and mark donation as paid
- [ ] **XFER-03**: Admin can reject a manual transfer with rejection reason
- [ ] **XFER-04**: Donation status updates reflect in participant's donation history

## v2.0 Requirements (Deferred)

### Real-time Notifications
- **NOTIF-01**: Student receives in-app notification when new material uploaded
- **NOTIF-02**: Seller receives in-app notification when new order placed
- **NOTIF-03**: Buyer receives notification when order status changes

### Admin Dashboard Enhancement
- **DASH-01**: Admin dashboard shows module-level statistics
- **DASH-02**: Admin can view donation management dashboard
- **DASH-03**: Admin can view order management (Sejajan)
- **DASH-04**: Admin can view attendance reports (LMS)

### Security Hardening
- **SEC-01**: CORS configured for mobile app domains
- **SEC-02**: File upload validation strengthened (type, size, content)
- **SEC-03**: API versioning strategy documented and implemented

## v3.0 Requirements (Deferred)

### Mobile Student App
- **MOB-01**: Student can login and manage profile
- **MOB-02**: Student can view class schedule
- **MOB-03**: Student can view and download materials
- **MOB-04**: Student can view attendance history
- **MOB-05**: Student can browse Sejajan marketplace
- **MOB-06**: Student can read books in Perpossagar

### Mobile Teacher App
- **TEACH-01**: Teacher can login and manage profile
- **TEACH-02**: Teacher can view teaching schedule
- **TEACH-03**: Teacher can upload materials to classroom
- **TEACH-04**: Teacher can input student attendance

## Out of Scope

| Feature | Reason |
|---------|--------|
| Quiz / ujian online | LMS enhancement, not core MVP |
| Forum diskusi kelas | Nice-to-have, deferred post-v3.0 |
| Rating & review (Sejajan/Perpossagar) | Enhancement, not blocking functionality |
| Push notifications | Requires mobile app (v3.0) first |
| Parent/wali portal | Future scope, separate user role needed |
| OAuth / social login | Email/password sufficient for school context |
| Multi-language support | Indonesian only for initial release |

## Traceability

Which phases cover which requirements. Updated during roadmap creation.

| Requirement | Phase | Status |
|-------------|-------|--------|
| LMS-01 | Phase 1 | Pending |
| LMS-02 | Phase 1 | Pending |
| LMS-03 | Phase 1 | Pending |
| LMS-04 | Phase 1 | Pending |
| LMS-05 | Phase 1 | Pending |
| LMS-06 | Phase 1 | Pending |
| LMS-07 | Phase 1 | Pending |
| LMS-08 | Phase 1 | Pending |
| LMS-09 | Phase 1 | Pending |
| LMS-10 | Phase 1 | Pending |
| AUTH-01 | Phase 2 | Pending |
| AUTH-02 | Phase 2 | Pending |
| AUTH-03 | Phase 2 | Pending |
| AUTH-04 | Phase 2 | Pending |
| TEST-01 | Phase 3 | Pending |
| TEST-02 | Phase 3 | Pending |
| TEST-03 | Phase 3 | Pending |
| TEST-04 | Phase 3 | Pending |
| BCAST-01 | Phase 4 | Pending |
| BCAST-02 | Phase 4 | Pending |
| BCAST-03 | Phase 4 | Pending |
| BCAST-04 | Phase 4 | Pending |
| PAY-01 | Phase 5 | Pending |
| PAY-02 | Phase 5 | Pending |
| PAY-03 | Phase 5 | Pending |
| PAY-04 | Phase 5 | Pending |
| PAY-05 | Phase 5 | Pending |
| XFER-01 | Phase 6 | Pending |
| XFER-02 | Phase 6 | Pending |
| XFER-03 | Phase 6 | Pending |
| XFER-04 | Phase 6 | Pending |

**Coverage:**
- v1.0 requirements: 27 total
- Mapped to phases: 27
- Unmapped: 0 ✓

---
*Requirements defined: 2026-04-23*
*Last updated: 2026-04-23 after initial definition*
