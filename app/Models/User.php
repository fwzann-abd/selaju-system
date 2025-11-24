<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Primary key is not auto-incrementing integer when using UUID.
     * We still keep the numeric `id` as primary key for now, and expose `uuid` as a unique identifier.
     */
    public $incrementing = true; // keep default behavior for existing id

    /**
     * The attribute type of the primary key.
     * Not changed; leaving as int for compatibility.
     */
    protected $keyType = 'int';

    /**
     * Boot: when creating a new user, if uuid is empty, generate one.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                // Use native UUID generation via PHP if available, else use ramsey/uuid if installed.
                if (function_exists('uuid_create')) {
                    $model->uuid = uuid_create(UUID_TYPE_RANDOM);
                } else {
                    // Fallback to DB generation placeholder — but set a v4-like random string.
                    $model->uuid = bin2hex(random_bytes(16));
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    'user_group_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class);
    }
}
