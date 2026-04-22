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
        </style>
    </head>
    <body class="min-h-screen bg-gray-50 antialiased dark:bg-gray-900">
        <!-- Theme Toggle -->
        <div class="fixed right-4 top-4 z-50">
            <x-theme-toggle />
        </div>

        <div class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md space-y-8">
                <div class="text-center">
                    <a href="/" class="inline-block">
                        <x-application-logo class="mx-auto h-16 w-auto transition-transform duration-200 hover:scale-105" />
                    </a>
                    <h2 class="mt-6 text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $heading ?? 'Selamat Datang' }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $subheading ?? 'Silakan masuk ke akun Anda' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white px-6 py-8 shadow-xl dark:border-gray-700 dark:bg-gray-800">
                    {{ $slot }}
                </div>

                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} Selaju. Semua hak dilindungi.
                    </p>
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
