<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SchoolController;

Route::middleware('api')->group(function () {
    // Public routes
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/schools', [SchoolController::class, 'index']);
    Route::patch('/register/{participant}/school', [RegisterController::class, 'updateSchool']);

    // Protected routes (require auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
