<?php

use Illuminate\Support\Facades\Route;

// ── LMS Auth ─────────────────────────────────────────────────────────────────
Route::prefix('lms')->middleware('throttle:30,1')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::middleware('auth:sanctum')->post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});

// ── LMS Notifications (shared, user-scoped) ──────────────────────────────────
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('lms/notifications')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::patch('{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markRead']);
    Route::post('read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllRead']);
});

// ── LMS Super Admin ──────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:super_admin', 'throttle:120,1'])->prefix('lms')->group(function () {
    Route::apiResource('accounts', \App\Http\Controllers\Api\AccountController::class);
    Route::apiResource('teachers', \App\Http\Controllers\Api\TeacherController::class);
    Route::apiResource('students', \App\Http\Controllers\Api\StudentController::class);
    Route::apiResource('classrooms', \App\Http\Controllers\Api\ClassroomController::class);
    Route::apiResource('subjects', \App\Http\Controllers\Api\SubjectController::class);
    Route::apiResource('student-positions', \App\Http\Controllers\Api\StudentPositionController::class);
    Route::apiResource('schedules', \App\Http\Controllers\Api\ScheduleController::class);
    Route::apiResource('rooms', \App\Http\Controllers\Api\RoomController::class);

    Route::post('classrooms/assign-student', [\App\Http\Controllers\Api\ClassroomAssignmentController::class, 'assignStudent']);
});

// ── LMS Teacher ──────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:teacher', 'throttle:60,1'])->prefix('lms/teacher')->group(function () {
    // Schedules
    Route::get('schedules', [\App\Http\Controllers\Api\Teacher\TeacherScheduleController::class, 'index']);
    Route::get('schedules/{schedule}/attendance-sheet', [\App\Http\Controllers\Api\Teacher\TeacherAttendanceController::class, 'sheet']);

    // Materials
    Route::apiResource('materials', \App\Http\Controllers\Api\Teacher\TeacherMaterialController::class);
    Route::post('materials/batch', [\App\Http\Controllers\Api\Teacher\TeacherMaterialController::class, 'storeBatch']);
    Route::get('materials/{material}/content', [\App\Http\Controllers\Api\Teacher\TeacherMaterialController::class, 'content']);

    // Attendance
    Route::post('attendances', [\App\Http\Controllers\Api\Teacher\TeacherAttendanceController::class, 'store']);

    // Assignments
    Route::apiResource('assignments', \App\Http\Controllers\Api\Teacher\TeacherAssignmentController::class);
    Route::post('assignments/submissions/{submission}/grade', [\App\Http\Controllers\Api\Teacher\TeacherAssignmentController::class, 'grade']);

    // Announcements
    Route::apiResource('announcements', \App\Http\Controllers\Api\Teacher\TeacherAnnouncementController::class)->except(['show']);
});

// ── LMS Student ──────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:student', 'throttle:60,1'])->prefix('lms/student')->group(function () {
    // Schedules
    Route::get('schedules', [\App\Http\Controllers\Api\Student\StudentScheduleController::class, 'index']);

    // Materials
    Route::get('materials', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'index']);
    Route::get('materials/classrooms/{classroomId}', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'byClassroom']);
    Route::get('materials/{material}', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'show']);
    Route::get('materials/{material}/download', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'download']);
    Route::get('materials/{material}/content', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'content']);

    // Attendance
    Route::get('attendances', [\App\Http\Controllers\Api\Student\StudentAttendanceController::class, 'index']);

    // Assignments
    Route::get('assignments', [\App\Http\Controllers\Api\Student\StudentAssignmentController::class, 'index']);
    Route::get('assignments/{assignment}', [\App\Http\Controllers\Api\Student\StudentAssignmentController::class, 'show']);
    Route::post('assignments/{assignment}/submit', [\App\Http\Controllers\Api\Student\StudentAssignmentController::class, 'submit']);

    // Announcements
    Route::get('announcements', [\App\Http\Controllers\Api\Student\StudentAnnouncementController::class, 'index']);
});

// ── Public / Participant Auth ────────────────────────────────────────────────
Route::middleware(['api', 'throttle:60,1'])->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'login'])->middleware('throttle:5,1');

    Route::options('/check-nisn', fn () => response('', 200));
    Route::post('/check-nisn', [\App\Http\Controllers\Api\RegisterController::class, 'checkNisn']);
    Route::post('/register', [\App\Http\Controllers\Api\RegisterController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/students', [\App\Http\Controllers\Api\StudentController::class, 'index']);
    Route::get('/students/{student}', [\App\Http\Controllers\Api\StudentController::class, 'show']);
    Route::patch('/register/{participant}/school', [\App\Http\Controllers\Api\RegisterController::class, 'updateSchool']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'logout']);
        Route::patch('/me', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'updateProfile']);
        Route::get('/username/check', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'checkUsername']);
        Route::post('/email/verification-notification', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'sendVerificationEmail'])->middleware('throttle:3,1');
        Route::post('/email/verify', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'verifyEmail']);
    });
});
