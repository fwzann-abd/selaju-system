<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Register a new participant.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:participants,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'username' => ['required', 'string', 'max:255', 'unique:participants,username'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
        ]);

        // Generate unique 8-digit nomor_participant
        do {
            $nomor = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Participant::where('nomor_participant', $nomor)->exists());

        $participant = Participant::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'username' => $validated['username'],
            'no_telp' => $validated['no_telp'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'nomor_participant' => $nomor,
            'is_active' => true,
        ]);

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
