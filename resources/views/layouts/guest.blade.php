<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Selaju') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
            .auth-gradient { background: linear-gradient(135deg, #0f172a 0%, #0d1a3a 50%, #112a5c 100%); }
            .glass-card { background: rgba(255,255,255,0.04); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.08); }
            @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
            .fade-up { animation: fadeUp 0.5s ease both; }
        </style>
    </head>
    <body class="auth-gradient min-h-screen text-white antialiased">
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8 fade-up">
                <div class="text-center">
                    <a href="/" class="inline-flex items-center gap-3 group">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#136dec] shadow-lg shadow-blue-500/25 transition-transform group-hover:scale-105">
                            <span class="text-lg font-bold text-white">S</span>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-white">Selaju<span class="text-sky-400">System</span></span>
                    </a>
                    <h2 class="mt-8 text-3xl font-bold text-white">
                        {{ $heading ?? 'Selamat Datang' }}
                    </h2>
                    <p class="mt-2 text-sm text-slate-400">
                        {{ $subheading ?? 'Silakan masuk ke akun Anda' }}
                    </p>
                </div>

                <div class="glass-card py-8 px-6 rounded-2xl shadow-xl shadow-black/20">
                    {{ $slot }}
                </div>

                <div class="text-center">
                    <p class="text-xs text-slate-500">
                        &copy; {{ date('Y') }} Selaju System. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
