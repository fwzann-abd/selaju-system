<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $loginField = $validated['login'];

        $account = Account::where('email', $loginField)
            ->orWhere('username', $loginField)
            ->first();

        if (! $account || ! Hash::check($validated['password'], $account->password)) {
            return response()->json([
                'message' => 'Kredensial tidak valid.',
            ], 401);
        }

        $token = $account->createToken('auth_token')->plainTextToken;

        // Determine role
        $role = 'super_admin';
        $name = $account->username ?? $account->email;
        if ($account->teacher()->exists()) {
            $role = 'teacher';
            $name = $account->teacher->name;
        } elseif ($account->student()->exists()) {
            $role = 'student';
            $name = $account->student->name;
        }

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'role' => $role,
            'user' => [
                'id' => $account->uuid,
                'name' => $name,
                'email' => $account->email,
                'username' => $account->username,
                'nisn' => $account->student?->national_id,
                'nip' => $account->teacher?->nip,
                'photo' => $account->photo,
            ],
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ], 200);
    }

    /**
     * Update the authenticated account's profile (name and email).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var Account $account */
        $account = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'email' => ['sometimes', 'email', 'max:191', Rule::unique('accounts', 'email')->ignore($account->uuid, 'uuid')],
        ]);

        // Update name on the related profile (teacher or student)
        if (isset($validated['name'])) {
            if ($account->teacher()->exists()) {
                $account->teacher()->update(['name' => $validated['name']]);
            } elseif ($account->student()->exists()) {
                $account->student()->update(['name' => $validated['name']]);
            }
        }

        // Update email on the account itself
        if (isset($validated['email'])) {
            $account->update(['email' => $validated['email']]);
        }

        $account->refresh()->load(['teacher', 'student']);

        $name = $account->username ?? $account->email;
        if ($account->teacher) {
            $name = $account->teacher->name;
        } elseif ($account->student) {
            $name = $account->student->name;
        }

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'user' => [
                'id' => $account->uuid,
                'name' => $name,
                'email' => $account->email,
                'username' => $account->username,
                'nisn' => $account->student?->national_id,
                'nip' => $account->teacher?->nip,
                'photo' => $account->photo,
            ],
        ]);
    }
}
