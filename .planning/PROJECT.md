# Selaju System

## What This Is

Platform Learning Management System berbasis Laravel 12 yang menyediakan manajemen akademik lengkap — jadwal KBM, materi pembelajaran, presensi siswa, manajemen kelas/guru/siswa. Backend API sudah functional, admin dashboard tersedia, mobile frontend masih dalam pengembangan.

## Core Value

Menyediakan sistem LMS yang komprehensif untuk sekolah — dari manajemen jadwal, materi, presensi, hingga admin dashboard — yang bisa diakses oleh siswa dan guru melalui mobile app.

## Requirements

### Validated

<!-- Shipped and confirmed valuable. -->

- ✓ AUTH-V01: Participant login via email/password — v0.9
- ✓ AUTH-V02: Participant registration with school selection — v0.9
- ✓ AUTH-V03: Email verification flow — v0.9
- ✓ AUTH-V04: Single device session enforcement — v0.9
- ✓ LMS-V01: Classroom CRUD API — v0.9
- ✓ LMS-V02: Teacher CRUD API — v0.9
- ✓ LMS-V03: Student CRUD API — v0.9
- ✓ LMS-V04: Schedule CRUD API — v0.9
- ✓ LMS-V05: Material upload & list API — v0.9
- ✓ LMS-V06: Attendance input & history API — v0.9
- ✓ GEN-V01: Generation CRUD with auto-assignment — v0.9
- ✓ ADM-V01: Admin dashboard with sidebar navigation — v0.9
- ✓ ADM-V02: Participant import/export (Excel) — v0.9
- ✓ CLEAN-V01: Eliminated non-LMS features (Sejajan, Donasi, Artikel, Perpossagar, Eplin, Webex) — v2.0

### Active

<!-- Current scope. Building toward these. -->

- [ ] LMS Admin CRUD forms (kelas, guru, siswa, jadwal)
- [ ] Auth: forgot password & change password
- [ ] Fix test environment
- [ ] Broadcasting activation (Reverb + LMS event dispatch)

### Out of Scope

<!-- Explicit boundaries. Includes reasoning to prevent re-adding. -->

- Mobile app UI — deferred to v3.0 milestone (backend must be complete first)
- Quiz / ujian online — LMS enhancement, not core MVP
- Forum diskusi kelas — nice-to-have, deferred
- Push notifications — requires mobile app first
- Parent/wali portal — future scope
- Sejajan, Donasi, Artikel, Perpossagar, Eplin, Webex — **REMOVED** from codebase (April 2026)

## Context

- **Stack:** Laravel 12, PHP 8.2, MySQL 8.0, Tailwind v4, Alpine.js v3
- **Auth:** Hybrid — `web` guard (admin/Blade), `sanctum` guard (API/mobile)
- **Database:** UUID-based primary keys across all tables
- **Real-time:** Laravel Reverb configured but `BROADCAST_CONNECTION=log` (inactive)
- **Tests:** 31 tests, 117 assertions (all passing)
- **Admin views:** LMS + Sekolah + Pengaturan sections
- **Branch:** `dev` (development), docs in `/docs/`
- **Cleanup:** Non-LMS features eliminated (April 2026)

## Constraints

- **Tech stack**: Laravel 12 monolith — no microservices
- **Database**: MySQL 8.0 with UUID PKs — maintain consistency
- **Auth**: Sanctum for API, web guard for admin — do not mix
- **Broadcasting**: Must use Laravel Reverb (already configured)
- **Styling**: Tailwind v4 + Alpine.js for admin — follow existing patterns
- **Scope**: LMS only — no marketplace, library, or discipline features

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| UUID primary keys | Scalability, prevent ID enumeration | ✓ Good |
| Sanctum for mobile API | Laravel native, token-based | ✓ Good |
| Laravel Reverb for real-time | Laravel ecosystem, WebSocket support | — Pending (not activated) |
| Monolith architecture | Single team, simpler deployment | ✓ Good |
| LMS-only focus | Reduce scope, ship faster | ✓ Good (eliminated non-LMS April 2026) |

## Current Milestone: v1.0 Backend Foundation

**Goal:** Melengkapi backend infrastructure yang masih incomplete agar sistem siap digunakan oleh mobile app.

**Target features:**
- LMS Admin CRUD forms (kelas, guru, siswa, jadwal)
- Auth flow lengkap (forgot password, change password)
- Fix test environment
- Broadcasting activation (Reverb + LMS event dispatch)

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
*Last updated: 2026-04-26 after non-LMS feature elimination*
