<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EplinOfficer;
use App\Models\EplinViolationType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EplinOfficerController extends Controller
{
    public function getViolationTypes(): JsonResponse
    {
        $types = EplinViolationType::all();

        return response()->json($types);
    }

    public function index(Request $request): JsonResponse
    {
        $officers = EplinOfficer::with([
            'student' => fn ($q) => $q->select('id', 'name', 'nis', 'email'),
        ])
            ->where('is_active', true)
            ->paginate(15);

        return response()->json($officers);
    }

    public function show(EplinOfficer $officer): JsonResponse
    {
        $officer->load(['student', 'recordedViolations.violationType']);

        return response()->json($officer);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|uuid|exists:students,id',
            'role' => 'required|in:ketua,wakil,anggota',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $officer = EplinOfficer::create($validated);
        $officer->load('student');

        return response()->json($officer, 201);
    }

    public function update(EplinOfficer $officer, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|in:ketua,wakil,anggota',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $officer->update($validated);

        return response()->json($officer);
    }

    public function destroy(EplinOfficer $officer): JsonResponse
    {
        $officer->delete();

        return response()->json(['message' => 'Officer deleted successfully']);
    }
}
