<?php

use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// LMS Authentication Routes
Route::prefix('lms')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

// LMS Super Admin Master Data Routes
Route::middleware(['auth:sanctum', 'role:super_admin'])->prefix('lms')->group(function () {
    Route::apiResource('accounts', \App\Http\Controllers\Api\AccountController::class);
    Route::apiResource('teachers', \App\Http\Controllers\Api\TeacherController::class);
    Route::apiResource('students', \App\Http\Controllers\Api\StudentController::class);
    Route::apiResource('classrooms', \App\Http\Controllers\Api\ClassroomController::class);
    Route::apiResource('subjects', \App\Http\Controllers\Api\SubjectController::class);
    Route::apiResource('student-positions', \App\Http\Controllers\Api\StudentPositionController::class);
    Route::apiResource('schedules', \App\Http\Controllers\Api\ScheduleController::class);

    Route::post('classrooms/assign-student', [\App\Http\Controllers\Api\ClassroomAssignmentController::class, 'assignStudent']);
});

// LMS Teacher Routes
Route::middleware(['auth:sanctum', 'role:teacher'])->prefix('lms/teacher')->group(function () {
    Route::get('schedules', [\App\Http\Controllers\Api\Teacher\TeacherScheduleController::class, 'index']);
    Route::get('schedules/{schedule}/attendance-sheet', [\App\Http\Controllers\Api\Teacher\TeacherAttendanceController::class, 'sheet']);
    Route::apiResource('materials', \App\Http\Controllers\Api\Teacher\TeacherMaterialController::class);
    Route::post('attendances', [\App\Http\Controllers\Api\Teacher\TeacherAttendanceController::class, 'store']);
});

// LMS Student Routes
Route::middleware(['auth:sanctum', 'role:student'])->prefix('lms/student')->group(function () {
    Route::get('schedules', [\App\Http\Controllers\Api\Student\StudentScheduleController::class, 'index']);
    Route::get('materials', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'index']);
    Route::get('materials/classrooms/{classroomId}', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'byClassroom']);
    Route::get('materials/{material}', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'show']);
    Route::get('materials/{material}/download', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'download']);
    Route::get('attendances', [\App\Http\Controllers\Api\Student\StudentAttendanceController::class, 'index']);
});

Route::middleware(['api'])->group(function () {
    // Public auth
    Route::post('/login', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'login']);

    Route::options('/check-nisn', function () {
        return response('', 200);
    });
    Route::post('/check-nisn', [RegisterController::class, 'checkNisn']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{student}', [StudentController::class, 'show']);
    Route::patch('/register/{participant}/school', [RegisterController::class, 'updateSchool']);

    // Protected routes (require auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'me']);
        Route::post('/logout', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'logout']);
        Route::patch('/me', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'updateProfile']);
        Route::get('/username/check', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'checkUsername']);
        Route::post('/email/verification-notification', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'sendVerificationEmail']);
        Route::post('/email/verify', [\App\Http\Controllers\Api\ParticipantAuthController::class, 'verifyEmail']);
    });
});
