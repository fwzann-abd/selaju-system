<?php

namespace App\Events;

use App\Models\CourseMaterial;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MaterialUploaded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $material;

    /**
     * Create a new event instance.
     */
    public function __construct(CourseMaterial $material)
    {
        $this->material = $material;
        // Ensure classroom is loaded
        $this->material->loadMissing('classroom');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('classroom.'.$this->material->classroom_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'title' => $this->material->title,
            'file_link' => $this->material->file_path ? asset('storage/'.$this->material->file_path) : null,
            'classroom_name' => $this->material->classroom->name,
            'teacher_name' => $this->material->teacher->name,
        ];
    }
}
