# Phase 1: LMS Admin CRUD Forms - Discussion Log

> **Audit trail only.** Do not use as input to planning, research, or execution agents.
> Decisions are captured in CONTEXT.md — this log preserves the alternatives considered.

**Date:** 2026-04-23
**Phase:** 01-lms-admin-crud-forms
**Areas discussed:** Controller Architecture, Form Pattern, Schedule Management, Student-Classroom Assignment

---

## Controller Architecture

| Option | Description | Selected |
|--------|-------------|----------|
| Konsolidasi ke Admin/ root | Hapus Lms/ stub controllers, semua CRUD tetap di Admin/ root. LMS sidebar links langsung ke controller yang sudah ada. | ✓ |
| Pindah semua ke Admin/Lms/ | Migrate full CRUD ke namespace Admin\Lms\, hapus duplikat di root. LMS jadi self-contained sub-module. | |
| Hybrid | Lms/ untuk view-only dashboard, Admin/ root handle actual CRUD. | |

**User's choice:** Konsolidasi ke Admin/ root
**Notes:** Paling simple, reuse yang sudah jalan. Menghindari duplikasi controller.

---

## Form Pattern

| Option | Description | Selected |
|--------|-------------|----------|
| Separate page + shared partial | Tiap entity punya create/edit blade + _form.blade.php partial untuk shared fields. Pattern Teacher. | ✓ |
| Separate page tanpa partial | create/edit lengkap tanpa shared partial. Pattern Classroom saat ini. | |
| Modal-based | CRUD form sebagai modal di atas index page pakai Alpine.js. | |

**User's choice:** Separate page + shared partial (opsi 1)
**Notes:** User confirmed ini sudah jadi existing pattern di Teacher views. Prefer konsistensi dengan yang sudah ada.

---

## Schedule Management — Time Slot Input

| Option | Description | Selected |
|--------|-------------|----------|
| Dropdown hari + jam mulai/selesai | Pilih hari, input jam mulai & selesai manual. Free-form time. | ✓ |
| Grid-based time picker | Visual grid hari × jam, klik slot untuk assign. | |
| Predefined slot | Admin definisikan slot standar (Jam ke-1, dst), lalu pilih slot. | |

**User's choice:** Dropdown hari + jam mulai/selesai
**Notes:** User provided mobile schedule design screenshot showing free-form times (08:00, 10:00, 13:00, 15:00) with varying durations. Predefined slots too rigid for this design.

## Schedule Management — Subject Category

| Option | Description | Selected |
|--------|-------------|----------|
| Field `type` di tabel subjects | Tiap mata pelajaran punya kategori tetap. | ✓ |
| Field `type` di tabel schedules | Kategori bisa beda per jadwal. | |

**User's choice:** Field `type` di tabel subjects (opsi 1)
**Notes:** User noted opsi 2 sebagai catatan untuk potensi refactor saat mobile design membutuhkan fleksibilitas lebih.

## Schedule Management — Conflict Detection

| Option | Description | Selected |
|--------|-------------|----------|
| Server-side validation saja | Backend cek overlap saat submit, tolak dengan error. | |
| Real-time check + server validation | AJAX warning saat pilih guru/hari/waktu, plus server validation. | ✓ |
| Agent discretion | Agent tentukan pendekatan terbaik. | |

**User's choice:** Real-time check + server validation
**Notes:** None

---

## Student-Classroom Assignment

| Option | Description | Selected |
|--------|-------------|----------|
| Per-student | Dropdown pilih kelas di form edit student. Satu-satu. | |
| Bulk dari halaman kelas | Multi-select siswa dari detail kelas. Bulk assignment. | |
| Dua arah | Keduanya: per-student dropdown + bulk dari detail kelas. | ✓ |

**User's choice:** Dua arah (opsi 3)
**Notes:** Bulk assignment penting untuk efisiensi awal tahun ajaran.

---

## Agent's Discretion

- Form validation rules — follow existing patterns
- Pagination, search, sorting — follow existing controller patterns
- Flash message wording — follow existing "berhasil" pattern
- Delete confirmation mechanism

## Deferred Ideas

- Schedule-level `type` field — potensi refactor dari subjects ke schedules table untuk fleksibilitas mobile design (v3.0)
