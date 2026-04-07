<?php
namespace App\Http\Controllers\Api\Teacher;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Material;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherMaterialController extends Controller {
    public function index(Request $request) {
        $teacherId = tap($request->user()->teacher, fn($t) => abort_if(!$t, 403, 'Teacher profile required'))->id;
        
        $materials = Material::with('schedule.subject')
             ->whereHas('schedule', fn($q) => $q->where('teacher_id', $teacherId))
             ->latest()->get();
             
        return response()->json(['status' => 'success', 'data' => MaterialResource::collection($materials)]);
    }

    public function store(StoreMaterialRequest $request) {
        $validated = $request->validated();
        
        $schedule = Schedule::findOrFail($validated['schedule_id']);
        if ($schedule->teacher_id !== $request->user()->teacher->id) {
            return response()->json(['message' => 'Akses ditolak pada jadwal ini.'], 403);
        }
        
        $path = $request->file('file')->store('materials', 'public');
        $type = $request->file('file')->getClientOriginalExtension();
        
        $material = Material::create([
            'schedule_id' => $schedule->id,
            'title' => $validated['title'],
            'file_path' => $path,
            'file_type' => $type
        ]);
        
        event(new \App\Events\MaterialUploaded($material));
        
        return response()->json(['status' => 'success', 'message' => 'Material successfully uploaded', 'data' => new MaterialResource($material)], 201);
    }
    
    public function show(Material $material) {
        $material->load('schedule.subject');
        return response()->json(['status' => 'success', 'data' => new MaterialResource($material)]);
    }

    public function update(UpdateMaterialRequest $request, Material $material) {
        if ($material->schedule->teacher_id !== $request->user()->teacher->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validated();
        if ($request->hasFile('file')) {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('materials', 'public');
            $validated['file_type'] = $request->file('file')->getClientOriginalExtension();
            unset($validated['file']);
        }

        $material->update($validated);
        return response()->json(['status' => 'success', 'message' => 'Material updated', 'data' => new MaterialResource($material)]);
    }

    public function destroy(Request $request, Material $material) {
        if ($material->schedule->teacher_id !== $request->user()->teacher->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        
        return response()->json(['status' => 'success', 'message' => 'Material successfully deleted']);
    }
}