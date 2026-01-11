<?php

namespace App\Http\Controllers\Api\WebexEkskul;

use App\Http\Controllers\Controller;
use App\Models\WebexEkskul;
use App\Models\WebexPengurus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengurusController extends Controller
{
    public function index(WebexEkskul $ekskul): JsonResponse
    {
        $pengurus = $ekskul->pengurus()
            ->with('student:id,name,nis,email,avatar')
            ->paginate(15);

        return response()->json($pengurus);
    }

    public function store(WebexEkskul $ekskul, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|uuid|exists:students,id',
            'role' => 'required|in:ketua,wakil,bendahara,sekretaris,anggota',
        ]);

        $exists = WebexPengurus::where('ekskul_id', $ekskul->id)
            ->where('student_id', $validated['student_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Pengurus already exists'], 409);
        }

        $pengurus = $ekskul->pengurus()->create($validated);
        $pengurus->load('student:id,name,nis,email,avatar');

        return response()->json($pengurus, 201);
    }

    public function update(WebexEkskul $ekskul, WebexPengurus $pengurus, Request $request): JsonResponse
    {
        if ($pengurus->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'role' => 'required|in:ketua,wakil,bendahara,sekretaris,anggota',
        ]);

        $pengurus->update($validated);
        $pengurus->load('student:id,name,nis,email,avatar');

        return response()->json($pengurus);
    }

    public function destroy(WebexEkskul $ekskul, WebexPengurus $pengurus): JsonResponse
    {
        if ($pengurus->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $pengurus->delete();

        return response()->json(['message' => 'Pengurus removed successfully']);
    }
}
