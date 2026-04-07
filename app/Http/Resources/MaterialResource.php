<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class MaterialResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'file_path' => $this->file_path,
            'file_url' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'file_type' => $this->file_type,
            'schedule' => new ScheduleResource($this->whenLoaded('schedule')),
            'created_at' => $this->created_at,
        ];
    }
}