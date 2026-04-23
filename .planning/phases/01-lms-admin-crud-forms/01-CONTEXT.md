# Phase 1: LMS Admin CRUD Forms - Context

**Gathered:** 2026-04-23
**Status:** Ready for planning

<domain>
## Phase Boundary

Admin dapat melakukan Create/Edit untuk kelas, guru, siswa, jadwal, dan mata pelajaran melalui admin dashboard. Semua CRUD operations menggunakan existing admin infrastructure — bukan module baru, tapi melengkapi yang sudah ada agar tidak read-only.

</domain>

<decisions>
## Implementation Decisions

### Controller Architecture
- **D-01:** Konsolidasi semua LMS CRUD ke `Admin/` root namespace. `Admin/Lms/` stub controllers (ClassroomController, ScheduleController, StudentController, TeacherController) dihapus. Sidebar LMS links langsung ke existing controllers di `Admin/`.
- **D-02:** Existing `Admin/ClassroomController` sudah full CRUD — tidak perlu ditulis ulang. Focus pada entity yang belum punya CRUD methods.

### Form Pattern
- **D-03:** Standarisasi ke pattern **separate page + `_form.blade.php` partial** (seperti Teacher yang sudah ada). Setiap entity punya `create.blade.php`, `edit.blade.php`, dan `_form.blade.php` shared partial untuk form fields.
- **D-04:** Classroom yang saat ini punya create/edit tanpa partial harus di-refactor ke pattern yang sama untuk konsistensi.

### Schedule Management
- **D-05:** Time slot input menggunakan **dropdown hari (Senin-Jumat) + jam mulai/selesai** — free-form time, bukan predefined slot. Ini sesuai dengan mobile design yang menunjukkan waktu bervariasi (08:00, 10:00, 13:00, 15:00) dengan durasi berbeda.
- **D-06:** Schedule form fields: hari, jam mulai, jam selesai, guru (dropdown), kelas (dropdown), mata pelajaran (dropdown), ruangan/lab (text input).
- **D-07:** **Real-time conflict detection via AJAX** — saat admin pilih guru + hari + waktu, langsung tampilkan warning jika ada jadwal overlap. Server-side validation tetap berlaku sebagai safety net saat submit.

### Subject Category
- **D-08:** Field `type` disimpan di **tabel `subjects`** — tiap mata pelajaran punya kategori tetap (Vocational, Theory, Practical, Workshop). Ini mencukupi untuk v1.0.

### Student-Classroom Assignment
- **D-09:** Assignment **dua arah**: (1) per-student via dropdown kelas di form edit student, (2) bulk assignment via multi-select siswa dari halaman detail kelas. Bulk assignment penting untuk efisiensi awal tahun ajaran.

### Agent's Discretion
- Form validation rules — agent mengikuti pattern existing `StoreClassroomRequest` dan `StoreScheduleRequest`
- Pagination, search, dan sorting di index pages — mengikuti pattern existing controllers
- Flash message wording — mengikuti pattern "berhasil ditambahkan/diperbarui/dihapus"
- Delete confirmation — mengikuti pattern existing (jika ada)

</decisions>

<canonical_refs>
## Canonical References

**Downstream agents MUST read these before planning or implementing.**

### Existing CRUD Patterns
- `app/Http/Controllers/Admin/ClassroomController.php` — Full CRUD reference implementation (index, create, store, edit, update, show, destroy)
- `app/Http/Controllers/Admin/TeacherController.php` — Teacher CRUD with existing methods
- `app/Http/Controllers/Admin/StudentController.php` — Student CRUD with import/export
- `app/Http/Controllers/Admin/GenerationController.php` — Another complete CRUD example

### Form Request Patterns
- `app/Http/Requests/StoreClassroomRequest.php` — Validation pattern reference
- `app/Http/Requests/StoreScheduleRequest.php` — Schedule validation with relationship checks
- `app/Http/Requests/StoreTeacherRequest.php` — Teacher validation pattern
- `app/Http/Requests/StoreStudentRequest.php` — Student validation pattern
- `app/Http/Requests/StoreSubjectRequest.php` — Subject validation pattern

### View Patterns
- `resources/views/admin/teachers/_form.blade.php` — Shared form partial pattern (D-03 reference)
- `resources/views/admin/classrooms/create.blade.php` — Full page form pattern
- `resources/views/admin/classrooms/index.blade.php` — Index with search/pagination pattern

### Models & Migrations
- `app/Models/Classroom.php` — Classroom model with relationships
- `app/Models/Teacher.php` — Teacher model
- `app/Models/Schedule.php` — Schedule model with relationships
- `app/Models/Subject.php` — Subject model
- `app/Models/ClassroomStudent.php` — Pivot model for student-classroom assignment
- `database/migrations/2026_04_06_033249_create_subjects_table.php` — Subject table schema
- `database/migrations/2026_04_06_033251_create_schedules_table.php` — Schedule table schema

### Blade Components
- `resources/views/components/text-input.blade.php` — Text input component
- `resources/views/components/input-label.blade.php` — Label component
- `resources/views/components/input-error.blade.php` — Error display component
- `resources/views/components/modal.blade.php` — Modal component (for delete confirmation)

### Gap Analysis
- `docs/analysis/2026-04-23_feature_gap_analysis.md` — Full gap analysis with LMS findings

### Mobile Design Reference
- User-provided mobile schedule screenshot — shows timeline view with subject categories (Vocational, Theory, Practical, Workshop), room/lab, duration, teacher name

</canonical_refs>

<code_context>
## Existing Code Insights

### Reusable Assets
- `ClassroomController` (Admin): Full CRUD already implemented — can serve as template for Subject CRUD
- `_form.blade.php` partial (Teacher): Reusable pattern for all entity forms
- Form Request classes: `StoreClassroomRequest`, `StoreScheduleRequest`, `StoreTeacherRequest`, `StoreStudentRequest`, `StoreSubjectRequest` — all exist, may need completion
- Blade components: `text-input`, `input-label`, `input-error`, `modal`, `primary-button`, `secondary-button`, `toast`

### Established Patterns
- Controller pattern: `index` with search + pagination, `create`/`edit` with breadcrumb array, `store`/`update` with FormRequest validation, redirect with flash message
- View pattern: Tailwind v4 + Alpine.js, `<x-admin-layout>` wrapper, breadcrumb navigation
- Model pattern: UUID primary keys, eager loading relationships with `->with()` and `->load()`

### Integration Points
- Sidebar navigation: LMS section links need to point to Admin/ root controllers
- Routes: `admin.classrooms.*`, `admin.teachers.*`, `admin.students.*` routes already registered — need schedule and subject routes
- LMS Lms/ sub-controllers: Must be removed/redirected to avoid conflicts

</code_context>

<specifics>
## Specific Ideas

- Mobile schedule design reference provided by user — timeline view with color-coded subject categories, room/lab info, and duration display. Admin form must capture all data needed to render this mobile view.
- Schedule conflict detection should show visual warning (not just block submission) so admin can override if intentional (e.g., co-teaching scenarios).

</specifics>

<deferred>
## Deferred Ideas

- **Schedule-level `type` field** — Currently `type` lives on `subjects` table. If mobile design needs flexibility (same subject appearing as Theory in one slot and Practical in another), refactor `type` to `schedules` table. Evaluate during mobile app development (v3.0).

</deferred>

---

*Phase: 01-lms-admin-crud-forms*
*Context gathered: 2026-04-23*
