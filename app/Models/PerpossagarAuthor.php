<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerpossagarAuthor extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'perpossagar_authors';

    protected $fillable = ['uuid','account_id','author_at'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'uuid');
    }

    public function books() {
        return $this->hasMany(PerpossagarBook::class, 'author_id', 'uuid');
    }
}
