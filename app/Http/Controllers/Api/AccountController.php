<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of all accounts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Account::query()
            ->with(['student:id,account_id,name', 'teacher:id,account_id,name'])
            ->select('uuid', 'username', 'email', 'is_active', 'created_at');

        if ($request->has('search')) {
            $search = strtolower($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(username) LIKE ?', ["%{$search}%"]);
            });
        }

        $accounts = $query->latest()->paginate(20);

        return response()->json([
            'data' => AccountResource::collection($accounts->items()),
            'meta' => [
                'current_page' => $accounts->currentPage(),
                'last_page' => $accounts->lastPage(),
                'total' => $accounts->total(),
            ],
        ]);
    }

    /**
     * Display the specified account.
     */
    public function show(string $id): JsonResponse
    {
        $account = Account::with(['student', 'teacher'])->find($id);

        if (! $account) {
            return response()->json(['message' => 'Akun tidak ditemukan.'], 404);
        }

        return response()->json(['data' => new AccountResource($account)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Gunakan endpoint registrasi untuk membuat akun baru.'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json(['message' => 'Gunakan endpoint PATCH /lms/me untuk memperbarui profil.'], 501);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json(['message' => 'Penghapusan akun tidak diizinkan melalui API ini.'], 501);
    }
}
