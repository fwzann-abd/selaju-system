<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebexReport extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'webex_reports';

    protected $fillable = [
        'ekskul_id',
        'created_by',
        'title',
        'content',
        'activity_date',
        'image',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function ekskul(): BelongsTo
    {
        return $this->belongsTo(WebexEkskul::class, 'ekskul_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'created_by');
    }
}
