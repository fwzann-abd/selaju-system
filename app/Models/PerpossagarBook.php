<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerpossagarBook extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'perpossagar_books';

    protected $fillable = [
        'uuid','author_id','author_name','title','slug','subtitle','desc','language',
        'photo','color_hex','filename','is_approved','published_at'
    ];

    public function author() {
        return $this->belongsTo(PerpossagarAuthor::class, 'author_id', 'uuid');
    }

    public function categories() {
        return $this->belongsToMany(PerpossagarCategory::class,
            'perpossagar_book_categories_pivots',
            'book_id',
            'category_id',
            'uuid',
            'uuid'
        );
    }

    public function reviews() {
        return $this->hasMany(PerpossagarBookReview::class, 'book_id', 'uuid');
    }
}

