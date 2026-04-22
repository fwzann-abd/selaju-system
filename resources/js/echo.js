import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

/**
 * Laravel Echo + Reverb (WebSocket)
 *
 * Digunakan untuk real-time notifications di modul Sejajan:
 * - orders.seller.{userId} → seller dapat notif order baru
 * - orders.buyer.{userId}  → buyer dapat update status pesanan
 *
 * Agar berfungsi penuh:
 * 1. Set VITE_REVERB_* di .env (lihat .env.example)
 * 2. Jalankan: php artisan reverb:start
 * 3. Jalankan: php artisan queue:work
 *
 * Jika VITE_REVERB_APP_KEY tidak diset, Echo TIDAK akan diinisialisasi
 * dan fitur real-time akan non-aktif (tapi tidak menyebabkan error).
 */
const reverbAppKey = import.meta.env.VITE_REVERB_APP_KEY;

if (reverbAppKey) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbAppKey,
        wsHost: import.meta.env.VITE_REVERB_HOST ?? 'localhost',
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else {
    console.info('[Echo] Reverb not configured (VITE_REVERB_APP_KEY missing). Real-time features disabled.');
}
