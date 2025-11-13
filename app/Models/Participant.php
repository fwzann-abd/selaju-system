<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasUuids, HasApiTokens;

    public $incrementing = false;
    // Primary key column is 'uuid' (migration defines uuid primary key)
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'nomor_participant',
        'school_id',
        'username',
        'name',
        'birth_date',
        'no_telp',
        'email',
        'photo',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
