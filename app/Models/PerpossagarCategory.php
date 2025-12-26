<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerpossagarCategory extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'perpossagar_book_categories';

    protected $fillable = ['uuid', 'name'];

    public function books()
    {
        return $this->belongsToMany(PerpossagarBook::class,
            'perpossagar_book_categories_pivots',
            'category_id',
            'book_id',
            'uuid',
            'uuid'
        );
    }
}
