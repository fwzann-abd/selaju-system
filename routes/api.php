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

    // Protected routes (require auth) - using Sanctum personal access tokens
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', function (Request $request) {
            return response()->json($request->user());
        });
        // Revoke current access token (logout)
        Route::post('/logout', function (Request $request) {
            $user = $request->user();
            if ($user && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }
            return response()->json(['message' => 'Logged out'], 200);
        });
    });
});
