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
            // Ensure related school is loaded so frontend can display school.name
            $user = $request->user();
            if ($user) {
                $user->load('school');
            }
            return response()->json($user);
        });
        // Revoke current access token (logout)
        Route::post('/logout', function (Request $request) {
            $user = $request->user();
            if ($user && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }
            return response()->json(['message' => 'Logged out'], 200);
        });
        // Update authenticated participant profile
        Route::patch('/me', function (Request $request) {
            $user = $request->user();
            if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

            $validated = $request->validate([
                'username' => ['required', 'string', 'max:50', 'unique:participants,username,' . $user->id . ',id'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:participants,email,' . $user->id . ',id'],
                'no_telp' => ['nullable', 'string', 'max:20'],
                'birth_date' => ['nullable', 'date'],
            ]);

            $user->update($validated);
            $user->load('school');
            return response()->json($user->fresh());
        });

        // Check username availability for the authenticated user (exclude their own username)
        Route::get('/username/check', function (Request $request) {
            $user = $request->user();
            if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

            $username = $request->query('username');
            if (!$username) {
                return response()->json(['available' => false, 'message' => 'username is required'], 400);
            }

            $exists = \App\Models\Participant::where('username', $username)
                ->where('id', '!=', $user->id)
                ->exists();

            return response()->json(['available' => !$exists]);
        });
    });
});
