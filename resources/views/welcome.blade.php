<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Selaju System') }} — Platform Digital Sekolah</title>
    <meta name="description" content="Selaju System adalah platform terintegrasi untuk ekosistem digital sekolah. Marketplace pelajar, perpustakaan digital, LMS, dan manajemen kegiatan dalam satu sistem.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }

        .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 70%, #1e1b4b 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #818cf8, #c084fc, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(129, 140, 248, 0.3);
            transform: translateY(-4px);
        }
        .icon-gradient {
            background: linear-gradient(135deg, #818cf8, #6366f1);
        }
        .glow { box-shadow: 0 0 60px rgba(99, 102, 241, 0.15); }
        .float-animation { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .fade-in { opacity: 0; transform: translateY(20px); animation: fadeIn 0.8s ease forwards; }
        .fade-in-delay-1 { animation-delay: 0.2s; }
        .fade-in-delay-2 { animation-delay: 0.4s; }
        .fade-in-delay-3 { animation-delay: 0.6s; }
        @keyframes fadeIn {
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.05));
            border: 1px solid rgba(99, 102, 241, 0.15);
        }
    </style>
</head>
<body class="gradient-bg text-white min-h-screen">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-card">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl icon-gradient shadow-lg">
                    <span class="text-lg font-bold text-white">S</span>
                </div>
                <span class="text-xl font-bold">Selaju<span class="text-indigo-400">System</span></span>
            </div>
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-sm text-slate-300 hover:text-white transition">Fitur</a>
                <a href="#modules" class="text-sm text-slate-300 hover:text-white transition">Modul</a>
                <a href="#stats" class="text-sm text-slate-300 hover:text-white transition">Statistik</a>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/admin') }}" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-500 shadow-lg shadow-indigo-500/25">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-slate-300 hover:text-white transition px-4 py-2">
                        Login
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-500 shadow-lg shadow-indigo-500/25">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center px-6 pt-20 overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl float-animation"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl float-animation" style="animation-delay: 3s;"></div>

        <div class="relative max-w-5xl mx-auto text-center fade-in">
            <div class="inline-flex items-center gap-2 rounded-full glass-card px-4 py-2 text-sm text-indigo-300 mb-8">
                <i class="fa-solid fa-rocket"></i>
                <span>Platform Digital Terintegrasi untuk Sekolah</span>
            </div>

            <h1 class="text-5xl md:text-7xl font-extrabold leading-tight mb-6">
                Ekosistem <span class="gradient-text">Digital</span><br>
                untuk Pelajar Indonesia
            </h1>

            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Selaju System menyatukan marketplace pelajar, perpustakaan digital, LMS, manajemen ekstrakurikuler, dan sistem pelanggaran dalam satu platform terpadu.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a href="{{ url('/admin') }}" class="rounded-2xl bg-indigo-600 px-8 py-4 text-base font-semibold text-white transition hover:bg-indigo-500 shadow-xl shadow-indigo-500/30">
                        <i class="fa-solid fa-arrow-right mr-2"></i> Masuk Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-2xl bg-indigo-600 px-8 py-4 text-base font-semibold text-white transition hover:bg-indigo-500 shadow-xl shadow-indigo-500/30">
                        <i class="fa-solid fa-arrow-right mr-2"></i> Mulai Sekarang
                    </a>
                @endauth
                <a href="#features" class="rounded-2xl glass-card px-8 py-4 text-base font-semibold text-slate-300 transition hover:text-white hover:bg-white/10">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Satu Platform, <span class="gradient-text">Banyak Solusi</span></h2>
                <p class="text-slate-400 max-w-xl mx-auto">Selaju System dirancang untuk mendigitalisasi seluruh aspek kegiatan sekolah secara terintegrasi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="feature-card rounded-2xl p-8 fade-in fade-in-delay-1">
                    <div class="w-12 h-12 rounded-xl icon-gradient flex items-center justify-center mb-5 shadow-lg shadow-indigo-500/20">
                        <i class="fa-solid fa-bolt text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Real-time</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Notifikasi pesanan, update status, dan broadcasting langsung menggunakan WebSocket melalui Laravel Reverb.</p>
                </div>

                <div class="feature-card rounded-2xl p-8 fade-in fade-in-delay-2">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-500 flex items-center justify-center mb-5 shadow-lg shadow-purple-500/20">
                        <i class="fa-solid fa-shield-halved text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Aman & Terverifikasi</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Autentikasi token Sanctum, role-based access control, dan enkripsi data untuk keamanan penuh.</p>
                </div>

                <div class="feature-card rounded-2xl p-8 fade-in fade-in-delay-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center mb-5 shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-mobile-screen text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">API-First</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">RESTful API yang siap dikonsumsi oleh aplikasi mobile dan web SPA dengan dokumentasi lengkap.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section id="modules" class="py-24 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">8 Modul <span class="gradient-text">Terintegrasi</span></h2>
                <p class="text-slate-400 max-w-xl mx-auto">Setiap modul dirancang untuk saling terhubung dan memberikan pengalaman digital yang menyeluruh.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @php
                    $modules = [
                        ['icon' => 'fa-store', 'name' => 'Sejajan', 'desc' => 'Marketplace pelajar — toko, produk, keranjang, pesanan', 'color' => 'from-orange-500 to-amber-500'],
                        ['icon' => 'fa-hand-holding-heart', 'name' => 'Donasi', 'desc' => 'Galang dana & donasi dengan integrasi DOKU QRIS', 'color' => 'from-pink-500 to-rose-500'],
                        ['icon' => 'fa-book-open', 'name' => 'Perpossagar', 'desc' => 'Perpustakaan digital — katalog, kategori, bahasa', 'color' => 'from-cyan-500 to-blue-500'],
                        ['icon' => 'fa-graduation-cap', 'name' => 'LMS Melesat', 'desc' => 'Learning Management — jadwal, materi, absensi', 'color' => 'from-indigo-500 to-violet-500'],
                        ['icon' => 'fa-people-group', 'name' => 'Webex Ekskul', 'desc' => 'Manajemen ekstrakurikuler — peserta, presensi', 'color' => 'from-emerald-500 to-green-500'],
                        ['icon' => 'fa-gavel', 'name' => 'Eplin', 'desc' => 'Sistem pelanggaran — petugas, pencatatan, poin', 'color' => 'from-red-500 to-orange-500'],
                        ['icon' => 'fa-newspaper', 'name' => 'Artikel', 'desc' => 'Manajemen konten — kategori, penerbitan, editor', 'color' => 'from-sky-500 to-blue-500'],
                        ['icon' => 'fa-users-gear', 'name' => 'Akun & Peran', 'desc' => 'Autentikasi, RBAC, profil, sesi management', 'color' => 'from-slate-500 to-gray-500'],
                    ];
                @endphp

                @foreach ($modules as $m)
                    <div class="feature-card rounded-2xl p-6 group cursor-default">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $m['color'] }} flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fa-solid {{ $m['icon'] }} text-white"></i>
                        </div>
                        <h3 class="text-base font-bold mb-1">{{ $m['name'] }}</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">{{ $m['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-24 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Dibangun dengan <span class="gradient-text">Teknologi Modern</span></h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <div class="stat-card rounded-2xl p-6 text-center">
                    <div class="text-3xl font-extrabold text-indigo-400 mb-1">8</div>
                    <div class="text-xs text-slate-400 uppercase tracking-wide">Modul</div>
                </div>
                <div class="stat-card rounded-2xl p-6 text-center">
                    <div class="text-3xl font-extrabold text-purple-400 mb-1">50+</div>
                    <div class="text-xs text-slate-400 uppercase tracking-wide">API Endpoint</div>
                </div>
                <div class="stat-card rounded-2xl p-6 text-center">
                    <div class="text-3xl font-extrabold text-emerald-400 mb-1">49</div>
                    <div class="text-xs text-slate-400 uppercase tracking-wide">Tests Passing</div>
                </div>
                <div class="stat-card rounded-2xl p-6 text-center">
                    <div class="text-3xl font-extrabold text-amber-400 mb-1">L12</div>
                    <div class="text-xs text-slate-400 uppercase tracking-wide">Laravel</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 py-12 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg icon-gradient">
                    <span class="text-sm font-bold text-white">S</span>
                </div>
                <span class="text-sm font-semibold text-slate-400">Selaju System</span>
            </div>
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Selaju System — Platform Digital Sekolah. Project ROJANA.</p>
            <div class="flex items-center gap-4">
                <a href="{{ url('/admin') }}" class="text-xs text-slate-500 hover:text-slate-300 transition">Admin Panel</a>
            </div>
        </div>
    </footer>

</body>
</html>
