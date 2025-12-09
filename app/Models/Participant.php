<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;

class Participant extends Model implements MustVerifyEmail
{
    use HasUuids, HasApiTokens, Notifiable;

    public $incrementing = false;
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'nomor_participant',
        'school_id',
        'generation_id',
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

}
