# Phase 1: LMS Admin CRUD Forms — Research

**Date:** 2026-04-23
**Status:** Complete

## Architecture Audit

### Current Controller Map

| Entity | Controller | CRUD Status | Notes |
|--------|-----------|-------------|-------|
| Classroom | `Admin/ClassroomController` | ✅ Full CRUD (index/create/store/edit/update/show/destroy) | Gold standard reference |
| Teacher | `Admin/TeacherController` | ✅ Full CRUD (index/create/store/edit/update/destroy) | Uses `_form.blade.php` partial |
| Student | `Admin/StudentController` | ✅ Full CRUD + import/export | Bulk operations via Excel |
| Subject | ❌ None | No controller exists | `StoreSubjectRequest` exists (stub — `authorize()` returns false) |
| Schedule | `Admin/Lms/ScheduleController` | ⚠️ Partial (index/store/update/destroy — no create/edit pages) | Inline form in index view |

### LMS Stub Controllers (Admin/Lms/)

All four stubs are index-only wrappers rendering empty views:

| File | Purpose | Action |
|------|---------|--------|
| `Lms/ClassroomController` | `index()` → `admin.lms.classrooms.index` | Remove per D-01 |
| `Lms/TeacherController` | `index()` → `admin.lms.teachers.index` | Remove per D-01 |
| `Lms/StudentController` | `index()` → `admin.lms.students.index` | Remove per D-01 |
| `Lms/ScheduleController` | Full store/update/destroy + index with filters | Consolidate to `Admin/ScheduleController` |

### Form Request Status

| Request | `authorize()` | Rules | Status |
|---------|--------------|-------|--------|
| `StoreClassroomRequest` | ✅ `true` | ✅ Complete with messages | Ready |
| `UpdateClassroomRequest` | ✅ `true` | ✅ Complete | Ready |
| `StoreTeacherRequest` | ✅ `true` | ✅ Complete | Ready |
| `UpdateTeacherRequest` | ✅ `true` | ✅ Complete | Ready |
| `StoreStudentRequest` | ✅ `true` | ⚠️ Sparse | Needs review |
| `UpdateStudentRequest` | ✅ `true` | ⚠️ Sparse | Needs review |
| `StoreSubjectRequest` | ❌ `false` | ❌ Empty | Must implement |
| `UpdateSubjectRequest` | ❌ `false` | ❌ Empty | Must implement |
| `StoreScheduleRequest` | ✅ `true` | ✅ Complete + conflict checks | Ready |
| `UpdateScheduleRequest` | ✅ `true` | ✅ Complete + conflict checks | Ready |

### View Inventory

**Admin root views (full CRUD pages):**
- `admin/classrooms/` → `create.blade.php`, `edit.blade.php`, `index.blade.php`, `show.blade.php` (NO partial)
- `admin/teachers/` → `_form.blade.php`, `create.blade.php`, `edit.blade.php`, `index.blade.php` (uses partial ✅)
- `admin/students/` → `create.blade.php`, `edit.blade.php`, `import.blade.php`, `index.blade.php` (NO partial)

**LMS views (stubs — minimal content):**
- `admin/lms/classrooms/index.blade.php` (28.4K — complex listing)
- `admin/lms/teachers/index.blade.php` (9K)
- `admin/lms/students/index.blade.php` (23.3K — complex listing)
- `admin/lms/schedules/index.blade.php` (22.8K — schedule grid + inline forms)

### Database Schema

```
subjects: id (auto), name (string), code (string|nullable), timestamps
schedules: id (auto), classroom_id (uuid FK), teacher_id (uuid FK), subject_id (FK), room_id (nullable FK), day (string), start_time (time), end_time (time), timestamps
classroom_students: id (auto), classroom_id (uuid FK), student_id (uuid FK), student_position_id (nullable FK), timestamps
rooms: id (auto), name (string), building (string), capacity (int)
```

### Sidebar Navigation

LMS menu section (lines 172-197 in sidebar.blade.php) uses hardcoded URLs (`/admin/lms/schedules`) instead of named routes. Must be updated to use `route()` helper after consolidation.

## Pattern Analysis

### Gold Standard: Teacher Form Partial

The `_form.blade.php` pattern:
1. Accepts `$action`, `$method`, `$buttonLabel` + entity variable
2. Shows validation errors block at top
3. Wraps form in card container (`rounded-xl border ...`)
4. Uses `old()` helper with null-safe entity access (`$teacher?->name`)
5. Cancel button uses named route, submit button uses `$buttonLabel`

### Gold Standard: ClassroomController

Full CRUD pattern:
1. `index()`: Search query → filter → paginate → view with `$search` preserved
2. `create()`: Prepare dropdown data → breadcrumb → view
3. `store()`: FormRequest validation → Model::create → redirect with flash
4. `edit()`: Load model → prepare dropdowns → breadcrumb → view
5. `update()`: FormRequest validation → model->update → redirect with flash
6. `destroy()`: model->delete → redirect with flash

### Schedule Conflict Detection

`StoreScheduleRequest` and `UpdateScheduleRequest` already implement full overlap detection:
- Teacher conflict: Same teacher + same day + overlapping time
- Room conflict: Same room + same day + overlapping time
- Three overlap cases: start within, end within, completely encompassing

**Gap for D-07 (AJAX):** Need new API endpoint for real-time conflict check before form submission.

## Implementation Gap Summary

### Must Create
1. `Admin/SubjectController` — Full CRUD (new controller)
2. `Admin/ScheduleController` — Consolidate from `Lms/ScheduleController` + add create/edit pages
3. Subject views: `admin/subjects/` → index, create, edit, `_form.blade.php`
4. Schedule views: `admin/schedules/` → create, edit, `_form.blade.php` (index may remain inline or move)
5. Subject FormRequests — fill stubs
6. Migration: Add `type` column to `subjects` table (D-08)
7. AJAX conflict check endpoint for schedules (D-07)
8. Student-classroom assignment views (D-09)

### Must Modify
1. Sidebar: Update LMS section to use named routes pointing to `admin.*` controllers
2. Routes: Remove `admin/lms/` prefix group, register `subjects` and `schedules` resources under `admin`
3. Classroom views: Refactor `create.blade.php` / `edit.blade.php` to use `_form.blade.php` partial (D-04)
4. Student views: Refactor to use `_form.blade.php` partial
5. Subject model: Add `type` to `$fillable`

### Must Remove
1. `Admin/Lms/ClassroomController.php`
2. `Admin/Lms/TeacherController.php`
3. `Admin/Lms/StudentController.php`
4. `Admin/Lms/ScheduleController.php`
5. LMS stub views under `admin/lms/` (after confirming no unique functionality)

---

*Research complete — ready for PLAN.md generation*
