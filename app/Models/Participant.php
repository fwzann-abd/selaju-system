<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;

class Participant extends Model implements MustVerifyEmail
{
    use HasUuids, HasApiTokens, Notifiable;

    public $incrementing = false;
    // Primary key column is 'uuid' (migration defines uuid primary key)
    // Primary key column is now 'id'
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
    // 'uuid' has been removed since we migrated to 'id' primary key
        'nomor_participant',
        'school_id',
        'username',
        'name',
        'birth_date',
        'no_telp',
        'email',
        'photo',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Whether the user's email has been verified.
     */
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified()
    {
        if ($this->hasVerifiedEmail()) {
            return false;
        }

        $this->forceFill(['email_verified_at' => now()])->save();
        event(new Verified($this));

        return true;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        // Use a frontend-friendly verification notification that points to the
        // SPA and includes the signed backend verify URL as a query parameter.
        $this->notify(new \App\Notifications\FrontendVerifyEmail());
    }

    /**
     * Get the e-mail address where verification links are sent.
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Ensure `id` column is populated on create so new rows keep parity with `uuid`.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // If the migration already copied uuid -> id for existing rows, new rows
            // still need `id` set. Prefer using the already-generated uuid.
            if (empty($model->id)) {
                $model->id = $model->uuid ?? (string) Str::uuid();
            }
        });
    }
}
