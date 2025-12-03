<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerpossagarAuthor extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'perpossagar_authors';

    protected $fillable = ['uuid','participant_id','author_at'];

    public function participant() {
        return $this->belongsTo(User::class, 'participant_id', 'id');
    }

    public function books() {
        return $this->hasMany(PerpossagarBook::class, 'author_id', 'uuid');
    }
}

