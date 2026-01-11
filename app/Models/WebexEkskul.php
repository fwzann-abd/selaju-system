<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebexEkskul extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'webex_ekskuls';

    protected $fillable = [
        'name',
        'slug',
        'bio',
        'logo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function pengurus(): HasMany
    {
        return $this->hasMany(WebexPengurus::class, 'ekskul_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(WebexParticipant::class, 'ekskul_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(WebexReport::class, 'ekskul_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(WebexAttendance::class, 'ekskul_id');
    }
}
