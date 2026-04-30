<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CourseMaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'original_filename' => $this->original_filename,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'category' => $this->category ?? 'other',
            'is_published' => $this->is_published,

            // URLs
            'file_url' => $this->when($this->is_published && $this->file_path, fn () => Storage::url($this->file_path)),
            'download_url' => $this->when($this->is_published, fn () => Storage::url($this->file_path)),
            'subtitle_url' => $this->when($this->subtitle_path, fn () => Storage::url($this->subtitle_path)),

            // Relations
            'teacher' => $this->whenLoaded('teacher', fn () => [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
            ]),
            'classroom' => $this->whenLoaded('classroom', fn () => [
                'id' => $this->classroom->id,
                'name' => $this->classroom->name,
            ]),
            'schedule_id' => $this->schedule_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
