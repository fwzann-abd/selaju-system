<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of rooms.
     */
    public function index(): JsonResponse
    {
        $rooms = Room::orderBy('name')->get();

        return response()->json([
            'data' => $rooms,
            'total' => $rooms->count(),
        ]);
    }

    /**
     * Store a newly created room.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:rooms,name',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'data' => $room,
            'message' => 'Ruangan berhasil ditambahkan.',
        ], 201);
    }

    /**
     * Display the specified room.
     */
    public function show(Room $room): JsonResponse
    {
        return response()->json([
            'data' => $room,
        ]);
    }

    /**
     * Update the specified room.
     */
    public function update(Request $request, Room $room): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:rooms,name,' . $room->id,
            'capacity' => 'nullable|integer|min:1',
        ]);

        $room->update($validated);

        return response()->json([
            'data' => $room,
            'message' => 'Ruangan berhasil diperbarui.',
        ]);
    }

    /**
     * Remove the specified room.
     */
    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json([
            'message' => 'Ruangan berhasil dihapus.',
        ]);
    }
}
