<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

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
        ]);

        $participant = Participant::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'username' => $validated['username'],
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Participant registered successfully. Please select a school.',
            'participant' => $participant,
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
