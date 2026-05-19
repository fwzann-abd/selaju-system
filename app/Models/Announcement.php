<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'author_id',
        'classroom_id',
        'title',
        'body',
        'priority',
        'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'author_id', 'uuid');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function scopeForClassrooms($query, array $classroomIds)
    {
        return $query->where(function ($q) use ($classroomIds) {
            $q->whereIn('classroom_id', $classroomIds)
                ->orWhereNull('classroom_id'); // global announcements
        });
    }
}
