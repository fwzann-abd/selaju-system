<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'original_filename' => $this->original_filename,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'is_published' => $this->is_published,
            'download_url' => $this->when($this->is_published, Storage::url($this->file_path)),
            'file_path' => $this->when($request->user()?->role === 'teacher', $this->file_path),
            'teacher' => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->account->full_name ?? 'Unknown',
            ],
            'classroom' => [
                'id' => $this->classroom->id,
                'name' => $this->classroom->name,
            ],
            'schedule_id' => $this->schedule_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
