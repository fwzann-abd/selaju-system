# Selaju System

## What This Is

Platform ekosistem digital sekolah berbasis Laravel 12 yang menyediakan marketplace pelajar (Sejajan), perpustakaan digital (Perpossagar), LMS (Melesat), manajemen ekskul (Webex), penegakan disiplin (Eplin), dan sistem donasi. Backend API sudah functional untuk 7 modul, admin dashboard tersedia, namun mobile frontend dan beberapa integrasi backend masih incomplete.

## Core Value

Menyediakan ekosistem digital sekolah yang utuh — dari akademik (LMS, presensi, materi) hingga non-akademik (marketplace, perpustakaan, ekskul) — yang bisa diakses oleh siswa dan guru melalui mobile app.

## Requirements

### Validated

<!-- Shipped and confirmed valuable. -->

- ✓ AUTH-V01: Participant login via email/password — v0.9
- ✓ AUTH-V02: Participant registration with school selection — v0.9
- ✓ AUTH-V03: Email verification flow — v0.9
- ✓ AUTH-V04: Single device session enforcement — v0.9
- ✓ SEJ-V01: Store CRUD (API + Admin) — v0.9
- ✓ SEJ-V02: Product CRUD API — v0.9
- ✓ SEJ-V03: Cart & order flow API — v0.9
- ✓ SEJ-V04: Order status tracking API — v0.9
- ✓ PERP-V01: Book catalog & detail API — v0.9
- ✓ PERP-V02: Book category & language CRUD — v0.9
- ✓ PERP-V03: Book upload (participant) — v0.9
- ✓ LMS-V01: Classroom CRUD API — v0.9
- ✓ LMS-V02: Teacher CRUD API — v0.9
- ✓ LMS-V03: Student CRUD API — v0.9
- ✓ LMS-V04: Schedule CRUD API — v0.9
- ✓ LMS-V05: Material upload & list API — v0.9
- ✓ LMS-V06: Attendance input & history API — v0.9
- ✓ WBX-V01: Ekskul CRUD (API + Admin) — v0.9
- ✓ EPL-V01: Violation recording & recap (API + Admin) — v0.9
- ✓ DON-V01: Donation leaderboard API — v0.9
- ✓ DON-V02: Manual transfer upload API — v0.9
- ✓ ART-V01: Article & category CRUD (API + Admin) — v0.9
- ✓ GEN-V01: Generation CRUD with auto-assignment — v0.9
- ✓ ADM-V01: Admin dashboard with sidebar navigation — v0.9
- ✓ ADM-V02: Participant import/export (Excel) — v0.9

### Active

<!-- Current scope. Building toward these. -->

- [ ] LMS Admin CRUD forms (kelas, guru, siswa, jadwal)
- [ ] Auth: forgot password & change password
- [ ] Fix test environment (SQLite migration compatibility)
- [ ] Broadcasting activation (Reverb + event dispatch)
- [ ] DOKU payment gateway actual implementation
- [ ] Admin manual transfer verification

### Out of Scope

<!-- Explicit boundaries. Includes reasoning to prevent re-adding. -->

- Mobile app UI — deferred to v3.0 milestone (backend must be complete first)
- Quiz / ujian online — LMS enhancement, not core MVP
- Forum diskusi kelas — nice-to-have, deferred
- Rating & review system — enhancement for Sejajan & Perpossagar
- Push notifications — requires mobile app first
- Parent/wali portal — future scope

## Context

- **Stack:** Laravel 12, PHP 8.2, MySQL 8.0, Tailwind v4, Alpine.js v3
- **Auth:** Hybrid — `web` guard (admin/Blade), `sanctum` guard (API/mobile)
- **Database:** UUID-based primary keys across all tables
- **Real-time:** Laravel Reverb configured but `BROADCAST_CONNECTION=log` (inactive)
- **Payment:** DOKU integration stubbed but returns dummy data (GD extension blocked)
- **Tests:** 49 tests exist, SQLite migration incompatibility blocks test suite
- **Admin views:** 18 admin sections, but LMS sections are read-only (no CRUD forms)
- **Branch:** `fzn` (development), docs in `/docs/`
- **Analysis:** Full gap analysis at `docs/analysis/2026-04-23_feature_gap_analysis.md`

## Constraints

- **Tech stack**: Laravel 12 monolith — no microservices
- **Database**: MySQL 8.0 with UUID PKs — maintain consistency
- **Auth**: Sanctum for API, web guard for admin — do not mix
- **Broadcasting**: Must use Laravel Reverb (already configured)
- **Payment**: DOKU is the chosen gateway — no alternatives
- **Styling**: Tailwind v4 + Alpine.js for admin — follow existing patterns

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| UUID primary keys | Scalability, prevent ID enumeration | ✓ Good |
| Sanctum for mobile API | Laravel native, token-based | ✓ Good |
| Laravel Reverb for real-time | Laravel ecosystem, WebSocket support | — Pending (not activated) |
| DOKU for payments | Indonesian payment gateway, QRIS + VA | — Pending (dummy) |
| Monolith architecture | Single team, simpler deployment | ✓ Good |
| 3-tier milestone plan | Foundation → Integration → Mobile | — Pending |

## Current Milestone: v1.0 Backend Foundation

**Goal:** Melengkapi backend infrastructure yang masih incomplete agar sistem siap digunakan oleh mobile app.

**Target features:**
- LMS Admin CRUD forms (kelas, guru, siswa, jadwal)
- Auth flow lengkap (forgot password, change password)
- Fix test environment (SQLite migration compatibility)
- Broadcasting activation (Reverb + event dispatch)
- DOKU payment gateway actual implementation
- Admin manual transfer verification

## Evolution

This document evolves at phase transitions and milestone boundaries.

**After each phase transition** (via `/gsd-transition`):
1. Requirements invalidated? → Move to Out of Scope with reason
2. Requirements validated? → Move to Validated with phase reference
3. New requirements emerged? → Add to Active
4. Decisions to log? → Add to Key Decisions
5. "What This Is" still accurate? → Update if drifted

**After each milestone** (via `/gsd-complete-milestone`):
1. Full review of all sections
2. Core Value check — still the right priority?
3. Audit Out of Scope — reasons still valid?
4. Update Context with current state

---
*Last updated: 2026-04-23 after milestone v1.0 initialization*
