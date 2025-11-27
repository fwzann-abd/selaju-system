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
            body {
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }
        </style>
    </head>
    <body class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div class="text-center">
                    <a href="/" class="inline-block">
                        <x-application-logo class="mx-auto h-16 w-auto hover:scale-105 transition-transform duration-200" />
                    </a>
                    <h2 class="mt-6 text-3xl font-bold text-gray-900 dark:text-white">
                        Selamat Datang
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Silakan masuk ke akun Anda
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 py-8 px-6 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                    {{ $slot }}
                </div>

                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        © {{ date('Y') }} Selaju. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
