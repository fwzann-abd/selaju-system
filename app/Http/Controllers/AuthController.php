<?php

namespace App\Http\Controllers;

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
            
        if (!$account || !Hash::check($validated['password'], $account->password)) {
            return response()->json([
                'message' => 'Kredensial tidak valid.'
            ], 401);
        }
        
        // Token generation via Sanctum
        $token = $account->createToken('auth_token')->plainTextToken;
        
        // Determine role logic as specified
        $role = 'super_admin';
        if ($account->teacher()->exists()) {
            $role = 'teacher';
        } elseif ($account->student()->exists()) {
            $role = 'student';
        }
        
        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'role' => $role,
            'user_data' => $account
        ], 200);
    }
    
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }
}
