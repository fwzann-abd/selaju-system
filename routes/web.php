<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenerationController;
use App\Http\Controllers\Admin\MenuManagementController;
use App\Http\Controllers\Admin\ModuleManagementController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\PerpossagarBookController;
use App\Http\Controllers\Admin\PerpossagarCategoryController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\SejajanController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
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
        // Article management
        Route::resource('article-categories', \App\Http\Controllers\Admin\ArticleCategoryController::class);
        Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class);
        Route::resource('schools', SchoolController::class)->except('show');
        Route::resource('generations', GenerationController::class)->except('show');
        Route::patch('generations/{generation}/toggle-active', [GenerationController::class, 'toggleActive'])->name('generations.toggle-active');
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
        // Sejajan (Shop) management
        Route::resource('sejajan', SejajanController::class);
        // Perpossagar (Library) management
        Route::resource('perpossagar-categories', PerpossagarCategoryController::class);
        Route::resource('perpossagar-books', PerpossagarBookController::class);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
