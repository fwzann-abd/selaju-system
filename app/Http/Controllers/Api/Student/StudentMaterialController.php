<?php
namespace App\Http\Controllers\Api\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\MaterialResource;
use App\Models\Material;

class StudentMaterialController extends Controller {
    public function index(Request $request) {
        $student = $request->user()->student;
        if (!$student) abort(403);

        $classroom_ids = $student->classrooms()->pluck('classrooms.id');
        
        $materials = Material::with('schedule.subject')
             ->whereHas('schedule', fn($q) => $q->whereIn('classroom_id', $classroom_ids))
             ->latest()
             ->get();
             
        return response()->json([
            'status' => 'success',
            'data' => MaterialResource::collection($materials)
        ]);
    }

    public function show(Request $request, Material $material) {
        $student = $request->user()->student;
        if (!$student) abort(403);
        
        $classroom_ids = $student->classrooms()->pluck('classrooms.id');
        
        $schedule = $material->schedule;
        if (!$classroom_ids->contains($schedule->classroom_id)) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $material->load('schedule.subject');
        return response()->json([
            'status' => 'success',
            'data' => new MaterialResource($material)
        ]);
    }
}