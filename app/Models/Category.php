<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['uuid','name'];

    public function books() {
        return $this->belongsToMany(Book::class,
            'books_categories_pivots',
            'category_id',
            'book_id',
            'uuid',
            'uuid'
        );
    }
}
