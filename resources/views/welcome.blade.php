<!DOCTYPE html>
<html lang="id" class="scroll-smooth" x-data x-bind:class="{ 'dark': $store.layout.theme === 'dark' }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Selaju System') }} — Platform Digital Sekolah</title>
    <meta name="description" content="Selaju System adalah platform terintegrasi untuk ekosistem digital sekolah. Marketplace pelajar, perpustakaan digital, LMS, dan manajemen kegiatan dalam satu sistem.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script>
        (function() {
            const t = localStorage.getItem('theme');
            if (t === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>

    <style>
        body { font-family: 'Lexend', system-ui, sans-serif; }

        /* Light mode gradients */
        .hero-bg { background: linear-gradient(135deg, #f0f4ff 0%, #e0ecff 40%, #dbeafe 70%, #eff6ff 100%); }
        .dark .hero-bg { background: linear-gradient(135deg, #0a0f1e 0%, #0d1a3a 40%, #112a5c 70%, #0d1a3a 100%); }

        .gradient-text { background: linear-gradient(135deg, #136dec, #2563eb, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .dark .gradient-text { background: linear-gradient(135deg, #136dec, #38bdf8, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }

        /* Glass cards */
        .glass-light { background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); border: 1px solid rgba(0,0,0,0.06); }
        .dark .glass-light { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); }

        .glow-blue { box-shadow: 0 0 60px rgba(19,109,236,0.08); }
        .dark .glow-blue { box-shadow: 0 0 80px rgba(19,109,236,0.12); }

        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        @keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
        .float { animation: float 6s ease-in-out infinite; }
        .fade-up { animation: fadeUp 0.7s ease both; }
        .delay-1 { animation-delay: .15s; }
        .delay-2 { animation-delay: .3s; }
        .delay-3 { animation-delay: .45s; }
    </style>
</head>
<body class="hero-bg text-slate-800 antialiased transition-colors duration-300 dark:text-white">

    {{-- ═══════════════════ NAVBAR ═══════════════════ --}}
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/60 bg-white/70 backdrop-blur-xl dark:border-white/5 dark:bg-white/[0.04]">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center gap-3">
                <x-application-logo class="h-10 w-auto" />
                <span class="text-xl font-bold tracking-tight text-slate-800 dark:text-white">Selaju<span class="text-[#136dec] dark:text-[#38bdf8]">System</span></span>
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="#features" class="text-sm text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Fitur</a>
                <a href="#modules" class="text-sm text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Modul</a>
                <a href="#tech" class="text-sm text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Teknologi</a>
            </div>

            <div class="flex items-center gap-3">
                <x-theme-toggle class="text-slate-500 hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white" />
                @auth
                    @if(auth()->user()->userGroup && auth()->user()->userGroup->name === 'Super Admin')
                        <a href="{{ url('/admin') }}"
                           class="rounded-xl bg-[#136dec] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition hover:bg-[#1a7fff]">
                            Dashboard
                        </a>
                    @else
                        <span class="px-3 py-2 text-sm font-medium text-slate-700 dark:text-white">
                            {{ auth()->user()->name }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="rounded-xl border border-slate-300 px-4 py-2 text-sm text-slate-600 transition hover:bg-slate-100 dark:border-white/20 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">
                                Logout
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-slate-600 transition hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="rounded-xl bg-[#136dec] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition hover:bg-[#1a7fff]">
                            Daftar
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    {{-- ═══════════════════ HERO ═══════════════════ --}}
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 pt-20">
        {{-- Orbs --}}
        <div class="pointer-events-none absolute left-1/4 top-1/4 h-96 w-96 rounded-full bg-[#136dec]/8 blur-3xl float dark:bg-[#136dec]/10"></div>
        <div class="pointer-events-none absolute bottom-1/4 right-1/4 h-80 w-80 rounded-full bg-sky-400/6 blur-3xl float dark:bg-sky-400/8" style="animation-delay:3s"></div>

        <div class="relative mx-auto max-w-5xl text-center fade-up">
            <div class="mb-8 inline-flex items-center gap-2 rounded-full glass-light px-5 py-2.5 text-sm text-[#136dec] dark:text-sky-300">
                <i class="fa-solid fa-rocket"></i>
                <span>Platform Digital Terintegrasi untuk Sekolah</span>
            </div>

            <h1 class="mb-6 text-5xl font-extrabold leading-tight tracking-tight text-slate-900 md:text-7xl dark:text-white">
                Ekosistem <span class="gradient-text">Digital</span><br>
                untuk Pelajar Indonesia
            </h1>

            <p class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-slate-500 md:text-xl dark:text-slate-400">
                Selaju System menyatukan marketplace pelajar, perpustakaan digital, LMS, manajemen ekstrakurikuler, dan sistem pelanggaran dalam satu platform terpadu.
            </p>

            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                @auth
                    <a href="{{ url('/admin') }}"
                       class="rounded-2xl bg-[#136dec] px-8 py-4 text-base font-semibold text-white shadow-xl shadow-blue-500/30 transition hover:bg-[#1a7fff] hover:shadow-blue-500/40">
                        <i class="fa-solid fa-arrow-right mr-2"></i> Masuk Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="rounded-2xl bg-[#136dec] px-8 py-4 text-base font-semibold text-white shadow-xl shadow-blue-500/30 transition hover:bg-[#1a7fff] hover:shadow-blue-500/40">
                        <i class="fa-solid fa-arrow-right mr-2"></i> Mulai Sekarang
                    </a>
                @endauth
                <a href="#features"
                   class="rounded-2xl glass-light px-8 py-4 text-base font-semibold text-slate-600 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </section>

    {{-- ═══════════════════ FEATURES ═══════════════════ --}}
    <section id="features" class="px-6 py-24">
        <div class="mx-auto max-w-7xl">
            <div class="mb-16 text-center fade-up">
                <h2 class="mb-4 text-3xl font-bold text-slate-900 md:text-4xl dark:text-white">Satu Platform, <span class="gradient-text">Banyak Solusi</span></h2>
                <p class="mx-auto max-w-xl text-slate-500 dark:text-slate-400">Selaju System dirancang untuk mendigitalisasi seluruh aspek kegiatan sekolah secara terintegrasi.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                @php
                    $features = [
                        ['icon' => 'fa-bolt', 'title' => 'Real-time', 'desc' => 'Notifikasi pesanan, update status, dan broadcasting langsung menggunakan WebSocket melalui Laravel Reverb.', 'from' => '#136dec', 'to' => '#38bdf8'],
                        ['icon' => 'fa-shield-halved', 'title' => 'Aman & Terverifikasi', 'desc' => 'Autentikasi token Sanctum, role-based access control, dan enkripsi data untuk keamanan penuh.', 'from' => '#8b5cf6', 'to' => '#c084fc'],
                        ['icon' => 'fa-mobile-screen', 'title' => 'API-First', 'desc' => 'RESTful API yang siap dikonsumsi oleh aplikasi mobile dan web SPA dengan dokumentasi lengkap.', 'from' => '#10b981', 'to' => '#34d399'],
                    ];
                @endphp

                @foreach ($features as $i => $f)
                    <div class="glass-light rounded-2xl p-8 transition hover:-translate-y-1 hover:shadow-lg dark:hover:border-[#136dec]/30 dark:hover:bg-white/[0.06] fade-up delay-{{ $i + 1 }}">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl shadow-lg"
                             style="background: linear-gradient(135deg, {{ $f['from'] }}, {{ $f['to'] }}); box-shadow: 0 8px 24px {{ $f['from'] }}33;">
                            <i class="fa-solid {{ $f['icon'] }} text-lg text-white"></i>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-slate-800 dark:text-white">{{ $f['title'] }}</h3>
                        <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════ MODULES ═══════════════════ --}}
    <section id="modules" class="px-6 py-24">
        <div class="mx-auto max-w-7xl">
            <div class="mb-16 text-center fade-up">
                <h2 class="mb-4 text-3xl font-bold text-slate-900 md:text-4xl dark:text-white">8 Modul <span class="gradient-text">Terintegrasi</span></h2>
                <p class="mx-auto max-w-xl text-slate-500 dark:text-slate-400">Setiap modul dirancang untuk saling terhubung dan memberikan pengalaman digital yang menyeluruh.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @php
                    $modules = [
                        ['icon' => 'fa-store',              'name' => 'Sejajan',        'desc' => 'Marketplace pelajar — toko, produk, keranjang, pesanan',       'from' => '#f97316', 'to' => '#fbbf24'],
                        ['icon' => 'fa-hand-holding-heart',  'name' => 'Donasi',         'desc' => 'Galang dana & donasi dengan integrasi DOKU QRIS',             'from' => '#ec4899', 'to' => '#f43f5e'],
                        ['icon' => 'fa-book-open',           'name' => 'Perpossagar',    'desc' => 'Perpustakaan digital — katalog, kategori, bahasa',            'from' => '#06b6d4', 'to' => '#3b82f6'],
                        ['icon' => 'fa-graduation-cap',      'name' => 'LMS Melesat',    'desc' => 'Learning Management — jadwal, materi, absensi',               'from' => '#136dec', 'to' => '#818cf8'],
                        ['icon' => 'fa-people-group',        'name' => 'Webex Ekskul',   'desc' => 'Manajemen ekstrakurikuler — peserta, presensi',               'from' => '#10b981', 'to' => '#22c55e'],
                        ['icon' => 'fa-gavel',               'name' => 'Eplin',          'desc' => 'Sistem pelanggaran — petugas, pencatatan, poin',              'from' => '#ef4444', 'to' => '#f97316'],
                        ['icon' => 'fa-newspaper',           'name' => 'Artikel',        'desc' => 'Manajemen konten — kategori, penerbitan, editor',             'from' => '#0ea5e9', 'to' => '#3b82f6'],
                        ['icon' => 'fa-users-gear',          'name' => 'Akun & Peran',   'desc' => 'Autentikasi, RBAC, profil, sesi management',                  'from' => '#64748b', 'to' => '#475569'],
                    ];
                @endphp

                @foreach ($modules as $m)
                    <div class="group glass-light cursor-default rounded-2xl p-6 transition hover:-translate-y-1 hover:shadow-lg dark:hover:border-[#136dec]/20 dark:hover:bg-white/[0.06]">
                        <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl shadow-lg transition-transform group-hover:scale-110"
                             style="background: linear-gradient(135deg, {{ $m['from'] }}, {{ $m['to'] }});">
                            <i class="fa-solid {{ $m['icon'] }} text-white"></i>
                        </div>
                        <h3 class="mb-1 text-base font-bold text-slate-800 dark:text-white">{{ $m['name'] }}</h3>
                        <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ $m['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════ TECH STATS ═══════════════════ --}}
    <section id="tech" class="px-6 py-24">
        <div class="mx-auto max-w-5xl">
            <div class="mb-12 text-center fade-up">
                <h2 class="mb-4 text-3xl font-bold text-slate-900 md:text-4xl dark:text-white">Dibangun dengan <span class="gradient-text">Teknologi Modern</span></h2>
            </div>

            <div class="grid grid-cols-2 gap-5 md:grid-cols-4">
                @php
                    $stats = [
                        ['value' => '8',   'label' => 'Modul',        'color' => '#136dec'],
                        ['value' => '50+', 'label' => 'API Endpoint', 'color' => '#8b5cf6'],
                        ['value' => '49',  'label' => 'Tests Passing','color' => '#10b981'],
                        ['value' => 'L12', 'label' => 'Laravel',      'color' => '#f59e0b'],
                    ];
                @endphp

                @foreach ($stats as $s)
                    <div class="glow-blue glass-light rounded-2xl p-6 text-center">
                        <div class="mb-1 text-3xl font-extrabold" style="color: {{ $s['color'] }}">{{ $s['value'] }}</div>
                        <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $s['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════════════════ CTA ═══════════════════ --}}
    <section class="px-6 py-24">
        <div class="mx-auto max-w-4xl">
            <div class="glow-blue glass-light rounded-3xl px-8 py-16 text-center md:px-16">
                <h2 class="mb-4 text-3xl font-bold text-slate-900 md:text-4xl dark:text-white">Siap <span class="gradient-text">Transformasi Digital</span> Sekolah Anda?</h2>
                <p class="mx-auto mb-8 max-w-lg text-slate-500 dark:text-slate-400">Bergabunglah dengan ekosistem Selaju System dan wujudkan sekolah digital yang terintegrasi.</p>
                @guest
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-[#136dec] px-8 py-4 text-base font-semibold text-white shadow-xl shadow-blue-500/30 transition hover:bg-[#1a7fff]">
                        <i class="fa-solid fa-rocket"></i> Daftar Sekarang
                    </a>
                @else
                    <a href="{{ url('/admin') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-[#136dec] px-8 py-4 text-base font-semibold text-white shadow-xl shadow-blue-500/30 transition hover:bg-[#1a7fff]">
                        <i class="fa-solid fa-arrow-right"></i> Masuk Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- ═══════════════════ FOOTER ═══════════════════ --}}
    <footer class="border-t border-slate-200/60 px-6 py-12 dark:border-white/5">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 md:flex-row">
            <div class="flex items-center gap-3">
                <x-application-logo class="h-8 w-auto" />
                <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">Selaju System</span>
            </div>
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Selaju System — Platform Digital Sekolah.</p>
            <a href="{{ url('/admin') }}" class="text-xs text-slate-500 transition hover:text-slate-700 dark:hover:text-slate-300">Admin Panel</a>
        </div>
    </footer>

</body>
</html>
