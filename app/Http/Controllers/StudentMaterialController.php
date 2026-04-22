<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseMaterialResource;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;

class StudentMaterialController extends Controller
{
    public function index(Request $request)
    {
        // Asumsikan student memiliki classroom_id
        $classroomId = $request->user()->classroom_id; // Sesuaikan dengan model User

        $materials = CourseMaterial::where('classroom_id', $classroomId)->with(['teacher', 'classroom'])->paginate();

        return CourseMaterialResource::collection($materials);
    }

    public function show(CourseMaterial $material)
    {
        // Pastikan material dari classroom siswa
        if ($material->classroom_id !== request()->user()->classroom_id) {
            abort(403);
        }

        return new CourseMaterialResource($material->load(['teacher', 'classroom']));
    }
}
