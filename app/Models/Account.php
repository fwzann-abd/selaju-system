<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;

class Account extends Model implements MustVerifyEmail
{
    use HasUuids, HasApiTokens, Notifiable;

    protected $table = 'accounts';
    public $incrementing = false;
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'nomor_participant',
        'school_id',
        'generation_id',
        'username',
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

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class);
    }

    public function deviceSessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class, 'account_id', 'uuid');
    }

    public function activeDeviceSessions(): HasMany
    {
        return $this->hasMany(DeviceSession::class, 'account_id', 'uuid')
            ->where('is_active', true);
    }

    public function sejajans(): HasMany
    {
        return $this->hasMany(Sejajan::class, 'account_id', 'uuid');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'uuid', 'account_id');
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
     * Check if the account has reached the maximum number of devices
     */
    public function hasReachedMaxDevices(): bool
    {
        $maxDevices = config('auth.max_devices_per_account', 4);
        return $this->activeDeviceSessions()->count() >= $maxDevices;
    }

    /**
     * Remove the oldest device session to make room for a new one
     */
    public function removeOldestDeviceSession(): void
    {
        $oldestSession = $this->activeDeviceSessions()
            ->orderBy('last_activity', 'asc')
            ->first();

        if ($oldestSession) {
            $oldestSession->update(['is_active' => false]);
        }
    }
}
