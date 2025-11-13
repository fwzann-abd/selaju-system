<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasUuids, HasApiTokens;

    public $incrementing = false;
    // Primary key column is 'uuid' (migration defines uuid primary key)
    // Primary key column is now 'id'
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
    // 'uuid' has been removed since we migrated to 'id' primary key
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

    /**
     * Ensure `id` column is populated on create so new rows keep parity with `uuid`.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // If the migration already copied uuid -> id for existing rows, new rows
            // still need `id` set. Prefer using the already-generated uuid.
            if (empty($model->id)) {
                $model->id = $model->uuid ?? (string) Str::uuid();
            }
        });
    }
}
