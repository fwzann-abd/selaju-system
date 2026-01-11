<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebexPengurus extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'webex_pengurus';

    protected $fillable = [
        'ekskul_id',
        'student_id',
        'role',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(WebexEkskul::class, 'ekskul_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(WebexAttendance::class, 'pengurus_id');
    }
}
