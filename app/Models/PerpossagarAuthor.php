<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Participant;

class PerpossagarAuthor extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'perpossagar_authors';

    protected $fillable = ['uuid','participant_id','author_at'];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id', 'uuid');
    }

    public function books() {
        return $this->hasMany(PerpossagarBook::class, 'author_id', 'uuid');
    }
}
