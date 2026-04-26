<?php

use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenerationController;
use App\Http\Controllers\Admin\LmsAttendanceController;
use App\Http\Controllers\Admin\MenuManagementController;
use App\Http\Controllers\Admin\ModuleManagementController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->userGroup && $user->userGroup->name === 'Super Admin') {
            return redirect()->route('dashboard');
        }
    }

    return view('welcome');
});

// Menu API Routes
Route::prefix('api/menus')->middleware('auth')->group(function () {
    Route::get('/sidebar', [MenuController::class, 'sidebar']);
    Route::get('/', [MenuController::class, 'index']);
    Route::get('/{id}', [MenuController::class, 'show']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Keep old paths working: redirect /dashboard and /admin/dashboard -> /admin
    Route::redirect('/dashboard', '/admin');
    Route::redirect('/admin/dashboard', '/admin');

    Route::get('/admin', [DashboardController::class, 'index'])->name('dashboard');

    // User Management Routes (now under /admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::resource('menus', MenuManagementController::class);
        Route::resource('modules', ModuleManagementController::class);
        Route::resource('schools', SchoolController::class)->except('show');
        Route::post('classrooms/{classroom}/bulk-assign', [ClassroomController::class, 'bulkAssign'])->name('classrooms.bulk-assign');
        Route::resource('classrooms', ClassroomController::class);
        Route::resource('teachers', TeacherController::class);
        Route::resource('generations', GenerationController::class)->except('show');
        Route::patch('generations/{generation}/toggle-active', [GenerationController::class, 'toggleActive'])->name('generations.toggle-active');
        Route::patch('generations/{generation}/set-as-current', [GenerationController::class, 'setAsCurrent'])->name('generations.set-as-current');
        // Participant management
        Route::resource('participants', ParticipantController::class);
        // Student management
        // Custom import form route must be registered before resource routes
        Route::get('students/import', [StudentController::class, 'showImportForm'])->name('students.import.form');
        Route::post('students-import/preview', [StudentController::class, 'previewImport'])->name('students.import.preview');
        Route::resource('students', StudentController::class);
        Route::get('students-template/download', [StudentController::class, 'downloadTemplate'])->name('students.template');
        Route::post('students-import', [StudentController::class, 'import'])->name('students.import');
        Route::get('students-export', [StudentController::class, 'export'])->name('students.export');

        // LMS Management (consolidated into Admin/ namespace)
        Route::resource('subjects', SubjectController::class);
        Route::resource('schedules', ScheduleController::class)->except('show');
        Route::post('schedules/check-conflict', [ScheduleController::class, 'checkConflict'])->name('schedules.check-conflict');
        // LMS Content & Attendance
        Route::resource('attendances', LmsAttendanceController::class);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
