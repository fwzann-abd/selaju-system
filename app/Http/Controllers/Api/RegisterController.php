<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use App\Models\Participant;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Check if NISN exists and is available for registration.
     */
    public function checkNisn(Request $request)
    {
        $validated = $request->validate([
            'nisn' => ['required', 'string'],
        ]);

        $student = Student::where('nisn', $validated['nisn'])->first();

        if (! $student) {
            return response()->json([
                'valid' => false,
                'message' => 'NISN tidak ditemukan. Hubungi admin sekolah Anda.',
            ], 404);
        }

        if ($student->isRegistered()) {
            return response()->json([
                'valid' => false,
                'message' => 'NISN sudah terdaftar.',
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'NISN valid. Silakan lanjutkan pendaftaran.',
            'student' => [
                'nama' => $student->nama,
                'school' => $student->school->name ?? null,
            ],
        ], 200);
    }

    /**
     * Register a new participant.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nisn' => ['required', 'string', 'exists:students,nisn'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:participants,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'username' => ['required', 'string', 'max:255', 'unique:participants,username'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
        ]);

        // Verify NISN is not already registered
        $student = Student::where('nisn', $validated['nisn'])->first();
        if (! $student) {
            return response()->json(['message' => 'NISN tidak ditemukan'], 404);
        }
        if ($student->isRegistered()) {
            return response()->json(['message' => 'NISN sudah terdaftar'], 422);
        }

        // Generate unique 8-digit nomor_participant
        do {
            $nomor = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Participant::where('nomor_participant', $nomor)->exists());

        // Get active generation
        $activeGeneration = Generation::where('is_active', true)->first();
        if (! $activeGeneration) {
            // Fallback: if no active generation exists, create one
            $activeGeneration = Generation::create([
                'name' => 'Generasi Saat Ini',
                'start_years' => date('Y'),
                'end_years' => date('Y'),
                'is_active' => true,
            ]);
        }

        $participant = Participant::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'username' => $validated['username'],
            'no_telp' => $validated['no_telp'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'nomor_participant' => $nomor,
            'is_active' => true,
            'generation_id' => $activeGeneration->id,
            'school_id' => $student->school_id,
        ]);

        // Link student to the participant
        $student->update(['user_id' => $participant->id]);

        // Create Sanctum personal access token and return plain token to client
        // Optionally: to enforce single-device login, uncomment the tokens deletion line below
        // $participant->tokens()->delete();
        $plainToken = $participant->createToken('default')->plainTextToken;

        return response()->json([
            'message' => 'Participant registered successfully. Please select a school.',
            'participant' => $participant,
            'token' => $plainToken,
        ], 201);
    }

    /**
     * Update school for a participant.
     */
    public function updateSchool(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'school_id' => ['nullable', 'uuid', 'exists:schools,id'],
        ]);

        $participant->update([
            'school_id' => $validated['school_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'School updated successfully.',
            'participant' => $participant,
        ], 200);
    }
}
