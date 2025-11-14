<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SchoolController;

Route::middleware('api')->group(function () {
    // Public routes
    // API login for SPA clients
    Route::post('/login', function (Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = \App\Models\Participant::where('email', $validated['email'])->first();
        if (! $user || ! \Illuminate\Support\Facades\Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Optionally check is_active
        if (property_exists($user, 'is_active') && ! $user->is_active) {
            return response()->json(['message' => 'Account is disabled'], 403);
        }

        // Enforce single-session: remove existing tokens for this user then create a new one
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        // Create token
        $token = $user->createToken('default')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'email_verified_at' => $user->email_verified_at,
            ],
        ]);
    });

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

        // Send email verification link for authenticated user (for token-based clients)
        Route::post('/email/verification-notification', function (Request $request) {
            $user = $request->user();
            if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Already verified'], 200);
            }

            $user->sendEmailVerificationNotification();
            return response()->json(['status' => 'verification-link-sent']);
        });

        // Verify email via API using the signed backend URL wrapped by the frontend.
        // Expects { verify_url: 'http://.../verify-email/{id}/{hash}?expires=...&signature=...' }
        Route::post('/email/verify', function (Request $request) {
            $user = $request->user();
            if (!$user) return response()->json(['message' => 'Unauthenticated'], 401);

            $verifyUrl = $request->input('verify_url');
            if (!$verifyUrl) return response()->json(['message' => 'verify_url is required'], 400);

            try {
                // Create a request object from the signed url so URL::hasValidSignature can validate it
                $fakeRequest = Request::create($verifyUrl);
            } catch (\Throwable $e) {
                return response()->json(['message' => 'Invalid verify_url format'], 400);
            }

            // Validate signature & expiration
            if (!\Illuminate\Support\Facades\URL::hasValidSignature($fakeRequest)) {
                return response()->json(['message' => 'Invalid or expired verification link'], 400);
            }

            // Extract {id} and {hash} from the path segments
            $path = parse_url($verifyUrl, PHP_URL_PATH);
            $segments = explode('/', trim($path, '/'));
            $hash = array_pop($segments);
            $id = array_pop($segments);

            if ((string) $user->getKey() !== (string) $id) {
                return response()->json(['message' => 'This verification link does not belong to the authenticated user'], 403);
            }

            if ($hash !== sha1($user->getEmailForVerification())) {
                return response()->json(['message' => 'Invalid verification hash'], 400);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Already verified'], 200);
            }

            $user->markEmailAsVerified();
            return response()->json(['message' => 'Email verified'], 200);
        });
    });
});
