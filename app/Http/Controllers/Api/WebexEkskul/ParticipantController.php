<?php

namespace App\Http\Controllers\Api\WebexEkskul;

use App\Http\Controllers\Controller;
use App\Models\WebexEkskul;
use App\Models\WebexParticipant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index(WebexEkskul $ekskul): JsonResponse
    {
        $participants = $ekskul->participants()
            ->with('student:id,name,nis,email,avatar')
            ->paginate(15);

        return response()->json($participants);
    }

    public function store(WebexEkskul $ekskul, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|uuid|exists:students,id',
            'status' => 'required|in:registered,active,inactive',
        ]);

        $exists = WebexParticipant::where('ekskul_id', $ekskul->id)
            ->where('student_id', $validated['student_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Participant already exists'], 409);
        }

        $participant = $ekskul->participants()->create([
            'student_id' => $validated['student_id'],
            'status' => $validated['status'],
            'registered_at' => now(),
        ]);

        $participant->load('student:id,name,nis,email,avatar');

        return response()->json($participant, 201);
    }

    public function update(WebexEkskul $ekskul, WebexParticipant $participant, Request $request): JsonResponse
    {
        if ($participant->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:registered,active,inactive',
        ]);

        $participant->update($validated);
        $participant->load('student:id,name,nis,email,avatar');

        return response()->json($participant);
    }

    public function destroy(WebexEkskul $ekskul, WebexParticipant $participant): JsonResponse
    {
        if ($participant->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $participant->delete();

        return response()->json(['message' => 'Participant removed successfully']);
    }
}
