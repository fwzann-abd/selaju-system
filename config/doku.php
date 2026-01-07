<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DOKU Client ID
    |--------------------------------------------------------------------------
    |
    | Your DOKU Client ID from merchant dashboard
    |
    */
    'client_id' => env('DOKU_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | DOKU Secret Key
    |--------------------------------------------------------------------------
    |
    | Your DOKU Secret Key from merchant dashboard
    |
    */
    'secret_key' => env('DOKU_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | DOKU Environment
    |--------------------------------------------------------------------------
    |
    | Set environment: 'sandbox' for testing, 'production' for live
    |
    */
    'env' => env('DOKU_ENV', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Callback URL
    |--------------------------------------------------------------------------
    |
    | URL where DOKU will send payment notifications
    |
    */
    'callback_url' => env('DOKU_CALLBACK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URL
    |--------------------------------------------------------------------------
    |
    | URL where users will be redirected after payment
    |
    */
    'redirect_url' => env('DOKU_REDIRECT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Token URL
    |--------------------------------------------------------------------------
    |
    | URL where DOKU will request token from merchant
    |
    */
    'token_url' => env('DOKU_TOKEN_URL'),

    /*
    |--------------------------------------------------------------------------
    | Merchant Keys Path
    |--------------------------------------------------------------------------
    |
    | Path to merchant's RSA private and public keys
    |
    */
    'merchant_private_key' => env('DOKU_MERCHANT_PRIVATE_KEY_PATH'),
    'merchant_public_key' => env('DOKU_MERCHANT_PUBLIC_KEY_PATH'),
];
