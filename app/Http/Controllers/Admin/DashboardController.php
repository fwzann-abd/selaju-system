<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard overview.
     */
    public function index(): View
    {
        $metrics = [
            [
                'label' => 'Total Pengguna',
                'value' => User::count(),
                'icon' => 'users',
                'description' => 'Pengguna yang sudah terdaftar di sistem.',
            ],
            [
                'label' => 'Ticket Aktif',
                'value' => 5,
                'icon' => 'clipboard-list',
                'description' => 'Isu layanan internal yang masih dipantau.',
            ],
            [
                'label' => 'Integrasi Aktif',
                'value' => 3,
                'icon' => 'cog',
                'description' => 'Modul atau integrasi yang berjalan stabil.',
            ],
        ];

        $quickActions = [
            [
                'title' => 'Lengkapi profil organisasi',
                'description' => 'Pastikan informasi utama tampil lengkap di halaman publik.',
            ],
            [
                'title' => 'Perbarui konten landing page',
                'description' => 'Gunakan highlight terbaru agar pengunjung selalu up-to-date.',
            ],
            [
                'title' => 'Tinjau role pengguna',
                'description' => 'Pastikan hak akses admin sudah sesuai kebutuhan tim.',
            ],
        ];

        $systemNotes = [
            [
                'title' => 'Backup terakhir',
                'description' => '12 Januari 2024 • 22:15 WIB',
            ],
            [
                'title' => 'Status server',
                'description' => 'Semua layanan berjalan normal.',
            ],
            [
                'title' => 'Pengingat',
                'description' => 'Jadwalkan onboarding admin baru pekan depan.',
            ],
        ];

        $recentActivities = [
            [
                'title' => '3 pengguna baru terverifikasi',
                'timestamp' => 'Baru saja',
                'status' => 'success',
                'status_label' => 'Selesai',
            ],
            [
                'title' => 'Draft konten promosi siap ditinjau',
                'timestamp' => '15 menit lalu',
                'status' => 'info',
                'status_label' => 'Review',
            ],
            [
                'title' => '2 ticket dukungan membutuhkan respon',
                'timestamp' => '1 jam lalu',
                'status' => 'warning',
                'status_label' => 'Butuh aksi',
            ],
        ];

        return view('dashboard', compact('metrics', 'quickActions', 'systemNotes', 'recentActivities'));
    }
}
