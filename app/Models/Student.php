<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'participant_id',
        'school_id',
        'nama',
        'nipd',
        'nisn',
        'jk',
    ];

    /**
     * Get the participant (user) associated with this student (if registered)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    /**
     * Get the school this student belongs to
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Check if student has registered (has user_id)
     */
    public function isRegistered(): bool
    {
        return ! is_null($this->participant_id);
    }
}
