<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
}
