<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['uuid','participant_id','author_at'];

    public function books() {
        return $this->hasMany(Book::class, 'author_id', 'uuid');
    }
}

