<?php

namespace App\Http\Controllers;

use App\Events\MaterialUploaded;
use App\Http\Requests\StoreCourseMaterialRequest;
use App\Http\Resources\CourseMaterialResource;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherMaterialController extends Controller
{
    public function index(Request $request)
    {
        $materials = CourseMaterial::where('teacher_id', $request->user()->id)->with(['teacher', 'classroom'])->paginate();

        return CourseMaterialResource::collection($materials);
    }

    public function store(StoreCourseMaterialRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('materials', 'public');

        $material = CourseMaterial::create([
            'teacher_id' => $request->user()->id,
            'classroom_id' => $request->classroom_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        broadcast(new MaterialUploaded($material))->toOthers();

        return new CourseMaterialResource($material);
    }

    public function show(CourseMaterial $material)
    {
        $this->authorize('view', $material);

        return new CourseMaterialResource($material->load(['teacher', 'classroom']));
    }

    public function update(Request $request, CourseMaterial $material)
    {
        $this->authorize('update', $material);

        $material->update($request->validated());

        return new CourseMaterialResource($material);
    }

    public function destroy(CourseMaterial $material)
    {
        $this->authorize('delete', $material);

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return response()->noContent();
    }
}
