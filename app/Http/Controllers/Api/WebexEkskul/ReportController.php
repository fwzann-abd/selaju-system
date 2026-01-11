<?php

namespace App\Http\Controllers\Api\WebexEkskul;

use App\Http\Controllers\Controller;
use App\Models\WebexEkskul;
use App\Models\WebexReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(WebexEkskul $ekskul): JsonResponse
    {
        $reports = $ekskul->reports()
            ->with('createdBy:id,name,nis,email,avatar')
            ->latest('activity_date')
            ->paginate(15);

        return response()->json($reports);
    }

    public function show(WebexEkskul $ekskul, WebexReport $report): JsonResponse
    {
        if ($report->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $report->load('createdBy:id,name,nis,email,avatar');

        return response()->json($report);
    }

    public function store(WebexEkskul $ekskul, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'activity_date' => 'required|date',
            'image' => 'nullable|string',
        ]);

        $report = $ekskul->reports()->create([
            'created_by' => auth()->user()->student_id,
            ...$validated,
        ]);

        $report->load('createdBy:id,name,nis,email,avatar');

        return response()->json($report, 201);
    }

    public function update(WebexEkskul $ekskul, WebexReport $report, Request $request): JsonResponse
    {
        if ($report->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($report->created_by !== auth()->user()->student_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'activity_date' => 'required|date',
            'image' => 'nullable|string',
        ]);

        $report->update($validated);
        $report->load('createdBy:id,name,nis,email,avatar');

        return response()->json($report);
    }

    public function destroy(WebexEkskul $ekskul, WebexReport $report): JsonResponse
    {
        if ($report->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($report->created_by !== auth()->user()->student_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $report->delete();

        return response()->json(['message' => 'Report deleted successfully']);
    }
}
