<?php

namespace App\Events;

use App\Models\Material;
use Illuminate\Broadcasting\Channel;
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
    public function __construct(Material $material)
    {
        $this->material = $material;
        // Ensure schedule and subject are loaded for broadcast data
        $this->material->loadMissing('schedule.subject', 'schedule.classroom');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('classroom.' . $this->material->schedule->classroom_id),
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
            'subject_name' => collect($this->material->schedule->subject)->get('name', 'Mata Pelajaran'),
            'file_link' => $this->material->file_path ? asset('storage/' . $this->material->file_path) : null,
            'classroom_name' => collect($this->material->schedule->classroom)->get('name', 'Kelas'),
        ];
    }
}
