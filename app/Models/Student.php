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
        'account_id',
        'school_id',
        'generation_id',
        'name',
        'student_number',
        'national_id',
        'gender',
    ];

    /**
     * Get the account (user) associated with this student (if registered)
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'uuid');
    }

    /**
     * Get the school this student belongs to
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the generation this student belongs to
     */
    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class);
    }

    /**
     * Check if student has registered (has account_id)
     */
    public function isRegistered(): bool
    {
        return ! is_null($this->account_id);
    }
}
