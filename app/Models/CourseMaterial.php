<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    protected $fillable = [
        'teacher_id',
        'classroom_id',
        'schedule_id',
        'title',
        'description',
        'file_path',
        'original_filename',
        'file_size',
        'file_type',
        'is_published',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_published' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
