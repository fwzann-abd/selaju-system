# Phase 1: LMS Admin CRUD Forms — Implementation Plan

**Phase:** 01-lms-admin-crud-forms
**Created:** 2026-04-23
**Status:** Ready for execution

## Summary

Consolidate all LMS CRUD operations into the `Admin/` root namespace, implement missing Subject CRUD, add `type` column to subjects, wire AJAX schedule conflict detection, add student-classroom bulk assignment, and standardize all entity forms to use the `_form.blade.php` partial pattern.

## Wave Breakdown

### Wave 1: Database & Model Layer (no dependencies)

#### Plan 1.1: Add `type` column to subjects table

**Files:**
- [NEW] `database/migrations/XXXX_add_type_to_subjects_table.php`
- [MODIFY] `app/Models/Subject.php` — add `type` to `$fillable`

**Steps:**
1. Create migration: `php artisan make:migration add_type_to_subjects_table --table=subjects --no-interaction`
2. Add column: `$table->string('type')->nullable()->after('code')` — enum values: `Vocational`, `Theory`, `Practical`, `Workshop`
3. Add `type` to Subject model `$fillable` array

**Verification:** Run `php artisan migrate` — no errors

---

### Wave 2: Subject CRUD (depends on Wave 1)

#### Plan 2.1: SubjectController + FormRequests

**Files:**
- [NEW] `app/Http/Controllers/Admin/SubjectController.php` — Full CRUD (index/create/store/edit/update/destroy)
- [MODIFY] `app/Http/Requests/StoreSubjectRequest.php` — Fix `authorize()` → `true`, add rules
- [MODIFY] `app/Http/Requests/UpdateSubjectRequest.php` — Fix `authorize()` → `true`, add rules

**Controller pattern:** Follow `ClassroomController` — index with search/pagination, breadcrumb arrays for create/edit, FormRequest validation, redirect with flash messages.

**Validation rules:**
```php
'name' => ['required', 'string', 'max:255'],
'code' => ['nullable', 'string', 'max:50', 'unique:subjects,code'],
'type' => ['nullable', 'string', 'in:Vocational,Theory,Practical,Workshop'],
```

**Verification:** `php artisan route:list --name=admin.subjects` shows all 7 resource routes

#### Plan 2.2: Subject Views

**Files:**
- [NEW] `resources/views/admin/subjects/index.blade.php` — Table listing with search, pagination, type badge
- [NEW] `resources/views/admin/subjects/_form.blade.php` — Shared form partial (name, code, type dropdown)
- [NEW] `resources/views/admin/subjects/create.blade.php` — Wraps `_form` partial
- [NEW] `resources/views/admin/subjects/edit.blade.php` — Wraps `_form` partial

**Pattern:** Follow `admin/teachers/` structure — `_form.blade.php` accepts `$action`, `$method`, `$buttonLabel`, `$subject` (nullable).

**Type badges:** Color-coded per type using Tailwind UI flat badge pattern:
- Vocational → indigo
- Theory → blue
- Practical → green
- Workshop → purple

**Verification:** Navigate to `/admin/subjects` — create, edit, delete all functional

---

### Wave 3: Route Consolidation & Controller Migration (depends on Wave 2)

#### Plan 3.1: Schedule Controller Consolidation

**Files:**
- [NEW] `app/Http/Controllers/Admin/ScheduleController.php` — Consolidate from `Admin/Lms/ScheduleController` + keep existing inline-form pattern
- [DELETE] `app/Http/Controllers/Admin/Lms/ClassroomController.php`
- [DELETE] `app/Http/Controllers/Admin/Lms/TeacherController.php`
- [DELETE] `app/Http/Controllers/Admin/Lms/StudentController.php`
- [DELETE] `app/Http/Controllers/Admin/Lms/ScheduleController.php`

**Steps:**
1. Create `Admin/ScheduleController.php` — copy logic from `Lms/ScheduleController`, update namespace and route references
2. Keep the existing inline modal pattern from the schedule index (it's already 398 lines of working UI)
3. Delete all 4 Lms/ stub controllers

**Verification:** All existing schedule CRUD operations still work under new routes

#### Plan 3.2: Route & Sidebar Update

**Files:**
- [MODIFY] `routes/web.php` — Remove `lms` prefix group, register `subjects` and `schedules` resources, remove Lms imports
- [MODIFY] `resources/views/layouts/sidebar.blade.php` — Update LMS section to use `route()` helpers

**Route changes:**
```php
// Remove:
Route::prefix('lms')->name('lms.')->group(function () { ... });

// Add:
Route::resource('subjects', SubjectController::class)->except('show');
Route::resource('schedules', ScheduleController::class)->only(['index', 'store', 'update', 'destroy']);
```

**Sidebar changes:**
- LMS menu items point to: `route('admin.schedules.index')`, `route('admin.classrooms.index')`, `route('admin.teachers.index')`, `route('admin.students.index')`
- Add new "Mata Pelajaran" menu item: `route('admin.subjects.index')`

**Verification:**
- `php artisan route:list --name=admin.schedules` shows 4 routes
- `php artisan route:list --name=admin.subjects` shows 6 routes
- Sidebar links are clickable and navigate correctly

#### Plan 3.3: Update Schedule View References

**Files:**
- [MODIFY] `resources/views/admin/lms/schedules/index.blade.php` → move to `resources/views/admin/schedules/index.blade.php`
- Update route references from `admin.lms.schedules.*` to `admin.schedules.*`

**Verification:** Schedule page loads without errors after route rename

---

### Wave 4: Form Partial Standardization (depends on Wave 3)

#### Plan 4.1: Classroom Form Partial

**Files:**
- [NEW] `resources/views/admin/classrooms/_form.blade.php` — Extract shared form fields from create/edit
- [MODIFY] `resources/views/admin/classrooms/create.blade.php` — Include `_form` partial
- [MODIFY] `resources/views/admin/classrooms/edit.blade.php` — Include `_form` partial

**Pattern:** Same as teacher `_form.blade.php` — accepts `$action`, `$method`, `$buttonLabel`, `$classroom` (nullable). Preserve Alpine.js `generateName()` logic.

**Verification:** Classroom create and edit pages render identically to current state

#### Plan 4.2: Student Form Partial

**Files:**
- [NEW] `resources/views/admin/students/_form.blade.php` — Extract shared form fields
- [MODIFY] `resources/views/admin/students/create.blade.php` — Include `_form` partial
- [MODIFY] `resources/views/admin/students/edit.blade.php` — Include `_form` partial

**Pattern:** Same partial pattern. Add classroom assignment dropdown (D-09).

**Verification:** Student create and edit pages render correctly

---

### Wave 5: AJAX Conflict Detection & Bulk Assignment (depends on Wave 3)

#### Plan 5.1: AJAX Schedule Conflict Check

**Files:**
- [MODIFY] `app/Http/Controllers/Admin/ScheduleController.php` — Add `checkConflict()` method
- [MODIFY] `routes/web.php` — Add `POST admin/schedules/check-conflict` route
- [MODIFY] `resources/views/admin/schedules/index.blade.php` — Add AJAX conflict check in modal form

**Endpoint:** `POST /admin/schedules/check-conflict`
- Request: `{ teacher_id, room_id, day, start_time, end_time, ?exclude_id }`
- Response: `{ hasConflict: bool, conflicts: [{ type, message }] }`

**UI:** Yellow warning banner appears below time inputs when conflict detected. Does NOT block submission (per specifics — co-teaching scenarios).

**Verification:** Select overlapping time → warning shows. Submit anyway → server-side validation still catches it.

#### Plan 5.2: Student-Classroom Bulk Assignment

**Files:**
- [MODIFY] `app/Http/Controllers/Admin/ClassroomController.php` — Add `assignStudents()` and `removeStudent()` methods
- [MODIFY] `routes/web.php` — Add `POST admin/classrooms/{classroom}/assign-students` and `DELETE admin/classrooms/{classroom}/remove-student/{student}` routes
- [MODIFY] `resources/views/admin/classrooms/show.blade.php` — Add student assignment section with multi-select

**UI:** On classroom show page, add "Assign Siswa" section:
1. Multi-select dropdown of students not yet in this classroom
2. "Assign" button submits selected student IDs
3. Table of currently assigned students with "Remove" action

**Verification:** Assign 3 students → they appear in the list. Remove 1 → list updates.

---

### Wave 6: Cleanup (depends on all)

#### Plan 6.1: Remove LMS Stub Views

**Files:**
- [DELETE] `resources/views/admin/lms/classrooms/index.blade.php`
- [DELETE] `resources/views/admin/lms/teachers/index.blade.php`
- [DELETE] `resources/views/admin/lms/students/index.blade.php`
- [DELETE] `resources/views/admin/lms/` directory (after schedule view is moved)

**Verification:** No broken view references remain. `php artisan view:cache` succeeds.

---

## UAT Checklist

- [ ] Subject CRUD: Create, edit, delete subjects with name/code/type
- [ ] Schedule management: Create via modal, filter by day/class/teacher, delete
- [ ] Schedule conflict: Warning shows for overlapping teacher/room schedules
- [ ] Student-classroom assignment: Bulk assign students to classroom, remove individuals
- [ ] Classroom form partial: Create/edit classroom uses shared `_form.blade.php`
- [ ] Student form partial: Create/edit student uses shared `_form.blade.php`
- [ ] Sidebar navigation: All LMS menu items navigate to correct pages
- [ ] No broken routes: `php artisan route:list` clean, no references to `admin.lms.*`
- [ ] No broken views: `php artisan view:cache` passes

---

*Plan created: 2026-04-23*
*Estimated plans: 10 across 6 waves*
