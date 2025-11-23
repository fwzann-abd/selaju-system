<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookReview extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid','participant_id','book_id','desc','rate'
    ];

    public function book() {
        return $this->belongsTo(Book::class, 'book_id', 'uuid');
    }
    public function participant() {
        return $this->belongsTo(Participant::class, 'participant_id', 'uuid');
    }
}

