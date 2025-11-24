<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleCors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:3001',
            'http://localhost:8080',
            'http://localhost:5173',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:3001',
            'http://127.0.0.1:8080',
            'http://127.0.0.1:5173',
            'https://selaju.dev',
            'https://www.selaju.dev',
        ];

        $origin = $request->header('Origin');

        if (in_array($origin, $allowedOrigins)) {
            return $next($request)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        return $next($request);
    }
}
