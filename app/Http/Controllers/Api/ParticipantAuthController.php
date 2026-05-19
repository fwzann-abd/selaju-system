<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginParticipantRequest;
use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class ParticipantAuthController extends Controller
{
    /**
     * API login for SPA clients.
     */
    public function login(LoginParticipantRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = Participant::where('email', $validated['email'])->first();
        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->is_active === false) {
            return response()->json(['message' => 'Account is disabled'], 403);
        }

        // Enforce single-session: remove existing tokens then create a new one
        if (method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

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
    }

    /**
     * Revoke current access token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out'], 200);
    }

    /**
     * Get authenticated participant profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(null, 200);
        }

        $user->load(['school', 'student.classrooms.teacher']);

        $payload = $user->toArray();
        $payload['name'] = $user->name ?? ($user->student?->name ?? $user->username ?? null);

        // Add classroom info for easier frontend access
        if ($user->student && $user->student->classrooms->isNotEmpty()) {
            $currentClassroom = $user->student->classrooms->first();
            $payload['classroom'] = $currentClassroom;
            $payload['classroom_id'] = $currentClassroom->id;
        }

        return response()->json($payload);
    }

    /**
     * Update authenticated participant profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:accounts,username,'.$user->uuid.',uuid'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:accounts,email,'.$user->uuid.',uuid'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
        ]);

        $user->update($validated);
        $user->load('school');

        return response()->json($user->fresh());
    }

    /**
     * Check username availability (excludes authenticated user's own username).
     */
    public function checkUsername(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $username = $request->query('username');
        if (! $username) {
            return response()->json(['available' => false, 'message' => 'username is required'], 400);
        }

        $exists = Participant::where('username', $username)
            ->where('uuid', '!=', $user->getKey())
            ->exists();

        return response()->json(['available' => ! $exists]);
    }

    /**
     * Send email verification notification.
     */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified'], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification-link-sent']);
    }

    /**
     * Verify email via API using a signed backend URL wrapped by the frontend.
     *
     * Expects: { verify_url: 'http://.../verify-email/{id}/{hash}?expires=...&signature=...' }
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $verifyUrl = $request->input('verify_url');
        if (! $verifyUrl) {
            return response()->json(['message' => 'verify_url is required'], 400);
        }

        try {
            $fakeRequest = Request::create($verifyUrl);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid verify_url format'], 400);
        }

        if (! URL::hasValidSignature($fakeRequest)) {
            return response()->json(['message' => 'Invalid or expired verification link'], 400);
        }

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
            return response()->json(['message' => 'Already verified', 'status' => 'already_verified'], 200);
        }

        $user->markEmailAsVerified();

        $token = $user->createToken('email-verification')->plainTextToken;

        return response()->json([
            'message' => 'Email verified',
            'status' => 'verified',
            'token' => $token,
        ], 200);
    }
}
