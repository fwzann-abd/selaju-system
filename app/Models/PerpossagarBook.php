<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerpossagarBook extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'uuid';

    protected $table = 'perpossagar_books';

    protected $fillable = [
        'uuid', 'author_id', 'language_id', 'author_name', 'title', 'slug', 'subtitle', 'desc', 'language',
        'photo', 'color_hex', 'filename', 'is_approved', 'published_at', 'is_hero', 'hero_order', 'read_count',
    ];

    public function author()
    {
        return $this->belongsTo(PerpossagarAuthor::class, 'author_id', 'uuid');
    }

    /**
     * Return the display name for the author.
     * If `author` relation exists, use that name; otherwise fall back to `author_name` column.
     */
    public function getAuthorDisplayNameAttribute(): ?string
    {
        return $this->author?->name ?? $this->author_name ?? null;
    }

    public function categories()
    {
        return $this->belongsToMany(PerpossagarCategory::class,
            'perpossagar_book_categories_pivots',
            'book_id',
            'category_id',
            'uuid',
            'uuid'
        );
    }

    public function languageOption()
    {
        return $this->belongsTo(PerpossagarBookLanguage::class, 'language_id', 'uuid');
    }

    public function reviews()
    {
        return $this->hasMany(PerpossagarBookReview::class, 'book_id', 'uuid');
    }
}
