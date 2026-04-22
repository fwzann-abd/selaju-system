// LMS Student Routes
Route::middleware(['auth:sanctum', 'role:student'])->prefix('lms/student')->group(function () {
    Route::get('schedules', [\App\Http\Controllers\Api\Student\StudentScheduleController::class, 'index']);
    
    // Materials routes
    Route::get('materials', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'index']);
    Route::get('materials/classrooms/{classroomId}', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'byClassroom']);
    Route::get('materials/{material}', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'show']);
    Route::get('materials/{material}/download', [\App\Http\Controllers\Api\Student\StudentMaterialController::class, 'download']);
    
    Route::get('attendances', [\App\Http\Controllers\Api\Student\StudentAttendanceController::class, 'index']);
});
