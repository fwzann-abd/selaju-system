<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class FrontendVerifyEmail extends BaseVerifyEmail
{
    /**
     * Build a verification URL that points to the frontend and contains the
     * signed backend verification URL as a query parameter (verify_url).
     *
     * This allows SPA clients to receive the link, open the frontend, and
     * then have the frontend call the API to complete verification using the
     * signed backend URL.
     */
    protected function verificationUrl($notifiable)
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $frontend = env('FRONTEND_URL', 'http://localhost:3000');

        return rtrim($frontend, '/') . '/verify-email?verify_url=' . urlencode($signedUrl);
    }
}
