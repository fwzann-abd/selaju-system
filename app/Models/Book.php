<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'uuid','author_id','title','slug','subtitle','desc','language',
        'photo','color_hex','filename','is_approved','published_at'
    ];

    public function author() {
        return $this->belongsTo(Author::class, 'author_id', 'uuid');
    }

    public function categories() {
        return $this->belongsToMany(Category::class,
            'books_categories_pivots',
            'book_id',
            'category_id',
            'uuid',
            'uuid'
        );
    }

    public function reviews() {
        return $this->hasMany(BookReview::class, 'book_id', 'uuid');
    }
}

