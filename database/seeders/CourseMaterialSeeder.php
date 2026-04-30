<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\CourseMaterial;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CourseMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::limit(5)->get();
        $classroom = Classroom::first();

        if ($teachers->isEmpty() || ! $classroom) {
            return;
        }

        // Ensure storage directories exist
        $disk = Storage::disk('public');
        foreach (['materials/document', 'materials/image', 'materials/video', 'materials/other'] as $dir) {
            $disk->makeDirectory($dir);
        }

        $materials = [
            // ── Documents ────────────────────────────────────────────
            [
                'title' => 'Modul Pemrograman Web - HTML & CSS',
                'description' => 'Modul lengkap pengantar HTML5 dan CSS3 untuk pemula. Mencakup struktur halaman, semantic tags, flexbox, dan grid layout.',
                'original_filename' => 'modul_html_css.pdf',
                'file_path' => 'materials/document/modul_html_css.pdf',
                'file_size' => 2457600,
                'file_type' => 'application/pdf',
                'category' => 'document',
                'teacher' => 0,
            ],
            [
                'title' => 'Catatan Ringkas Jaringan Komputer',
                'description' => 'Rangkuman materi jaringan komputer: OSI Layer, TCP/IP, subnetting, dan routing protocols.',
                'original_filename' => 'catatan_jarkom.txt',
                'file_path' => 'materials/document/catatan_jarkom.txt',
                'file_size' => 0, // will be set after content creation
                'file_type' => 'text/plain',
                'category' => 'document',
                'teacher' => 0,
                'content' => "CATATAN RINGKAS — JARINGAN KOMPUTER\n" .
                    "====================================\n\n" .
                    "1. MODEL OSI (7 Layer)\n" .
                    "   - Application  : HTTP, FTP, DNS, SMTP\n" .
                    "   - Presentation : SSL/TLS, JPEG, MPEG\n" .
                    "   - Session      : NetBIOS, RPC\n" .
                    "   - Transport    : TCP (reliable), UDP (fast)\n" .
                    "   - Network      : IP, ICMP, OSPF, BGP\n" .
                    "   - Data Link    : Ethernet, Wi-Fi (802.11), ARP\n" .
                    "   - Physical     : Kabel, fiber optik, sinyal radio\n\n" .
                    "2. TCP/IP MODEL (4 Layer)\n" .
                    "   - Application  → OSI layer 5-7\n" .
                    "   - Transport    → TCP, UDP\n" .
                    "   - Internet     → IPv4, IPv6\n" .
                    "   - Network      → Ethernet, Wi-Fi\n\n" .
                    "3. SUBNETTING\n" .
                    "   - /24 = 255.255.255.0   → 254 host\n" .
                    "   - /25 = 255.255.255.128 → 126 host\n" .
                    "   - /26 = 255.255.255.192 →  62 host\n" .
                    "   - /27 = 255.255.255.224 →  30 host\n" .
                    "   - /28 = 255.255.255.240 →  14 host\n\n" .
                    "4. ROUTING\n" .
                    "   Static  : Manual, cocok untuk jaringan kecil\n" .
                    "   Dynamic : OSPF, BGP, RIP — otomatis menemukan rute terbaik\n\n" .
                    "5. TOPOLOGI\n" .
                    "   Star : Pusat di switch/hub, paling umum\n" .
                    "   Bus  : Satu jalur utama (kuno)\n" .
                    "   Ring : Token passing (FDDI)\n" .
                    "   Mesh : Setiap node terhubung (paling reliable)\n\n" .
                    "---\n" .
                    "Disusun oleh: Tim RPL SMK Negeri 1 Garut\n" .
                    "Tahun Ajaran: 2025/2026\n",
            ],
            [
                'title' => 'Latihan Soal UTS Matematika',
                'description' => 'Kumpulan soal dan pembahasan UTS Matematika kelas 10 semester genap. 40 soal pilihan ganda + 5 esai.',
                'original_filename' => 'soal_uts_matematika.docx',
                'file_path' => 'materials/document/soal_uts_matematika.docx',
                'file_size' => 856064,
                'file_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'category' => 'document',
                'teacher' => 1,
            ],
            [
                'title' => 'Presentasi Desain UI/UX',
                'description' => 'Slide presentasi materi desain antarmuka pengguna, prinsip UX, wireframing, dan prototyping.',
                'original_filename' => 'desain_uiux.pptx',
                'file_path' => 'materials/document/desain_uiux.pptx',
                'file_size' => 8388608,
                'file_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'category' => 'document',
                'teacher' => 4,
            ],
            [
                'title' => 'Data Nilai Praktikum Semester 1',
                'description' => 'Rekap nilai praktikum basis data dan pemrograman web semester 1 tahun ajaran 2025/2026.',
                'original_filename' => 'rekap_nilai_praktikum.xlsx',
                'file_path' => 'materials/document/rekap_nilai_praktikum.xlsx',
                'file_size' => 524288,
                'file_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'category' => 'document',
                'teacher' => 2,
            ],
            [
                'title' => 'Panduan Penggunaan Git',
                'description' => 'Cheatsheet perintah Git yang sering digunakan dalam workflow pengembangan perangkat lunak.',
                'original_filename' => 'panduan_git.md',
                'file_path' => 'materials/document/panduan_git.md',
                'file_size' => 0,
                'file_type' => 'text/markdown',
                'category' => 'document',
                'teacher' => 2,
                'content' => "# Panduan Git — Cheatsheet\n\n" .
                    "## Setup Awal\n" .
                    "```bash\n" .
                    "git config --global user.name \"Nama Anda\"\n" .
                    "git config --global user.email \"email@contoh.com\"\n" .
                    "```\n\n" .
                    "## Workflow Dasar\n" .
                    "| Perintah | Fungsi |\n" .
                    "|----------|--------|\n" .
                    "| `git init` | Inisialisasi repo baru |\n" .
                    "| `git clone <url>` | Clone repo remote |\n" .
                    "| `git add .` | Stage semua perubahan |\n" .
                    "| `git commit -m \"pesan\"` | Simpan perubahan |\n" .
                    "| `git push origin main` | Upload ke remote |\n" .
                    "| `git pull` | Ambil perubahan terbaru |\n\n" .
                    "## Branching\n" .
                    "```bash\n" .
                    "git branch fitur-baru        # buat branch\n" .
                    "git checkout fitur-baru       # pindah ke branch\n" .
                    "git merge fitur-baru          # gabung ke branch aktif\n" .
                    "git branch -d fitur-baru      # hapus branch\n" .
                    "```\n\n" .
                    "## Tips\n" .
                    "- Commit sering, pesan yang jelas\n" .
                    "- Jangan push ke `main` langsung — gunakan Pull Request\n" .
                    "- Gunakan `.gitignore` untuk exclude file build\n",
            ],

            // ── Images ───────────────────────────────────────────────
            [
                'title' => 'Diagram ERD Sistem Perpustakaan',
                'description' => 'Entity Relationship Diagram untuk tugas proyek basis data perpustakaan sekolah.',
                'original_filename' => 'erd_perpustakaan.png',
                'file_path' => 'materials/image/erd_perpustakaan.png',
                'file_size' => 1048576,
                'file_type' => 'image/png',
                'category' => 'image',
                'teacher' => 2,
            ],
            [
                'title' => 'Infografis Topologi Jaringan',
                'description' => 'Ilustrasi perbandingan topologi star, bus, ring, dan mesh dalam jaringan komputer.',
                'original_filename' => 'topologi_jaringan.jpg',
                'file_path' => 'materials/image/topologi_jaringan.jpg',
                'file_size' => 756432,
                'file_type' => 'image/jpeg',
                'category' => 'image',
                'teacher' => 0,
            ],
            [
                'title' => 'Screenshot Contoh Layout CSS Grid',
                'description' => 'Contoh implementasi CSS Grid untuk layout dashboard modern.',
                'original_filename' => 'contoh_grid.webp',
                'file_path' => 'materials/image/contoh_grid.webp',
                'file_size' => 389120,
                'file_type' => 'image/webp',
                'category' => 'image',
                'teacher' => 0,
            ],

            // ── Videos ───────────────────────────────────────────────
            [
                'title' => 'Tutorial Git & GitHub untuk Pemula',
                'description' => 'Video tutorial lengkap penggunaan Git dan GitHub: init, commit, push, pull, branching, dan merge conflict.',
                'original_filename' => 'tutorial_git.mp4',
                'file_path' => 'materials/video/tutorial_git.mp4',
                'file_size' => 157286400,
                'file_type' => 'video/mp4',
                'category' => 'video',
                'subtitle_path' => 'materials/subtitles/tutorial_git.vtt',
                'teacher' => 2,
            ],
            [
                'title' => 'Demo Deploy Aplikasi ke Server',
                'description' => 'Rekaman demonstrasi deployment aplikasi Laravel ke VPS menggunakan SSH dan Nginx.',
                'original_filename' => 'demo_deploy.mp4',
                'file_path' => 'materials/video/demo_deploy.mp4',
                'file_size' => 89128960,
                'file_type' => 'video/mp4',
                'category' => 'video',
                'teacher' => 2,
            ],
            [
                'title' => 'Penjelasan Algoritma Sorting',
                'description' => 'Visualisasi dan penjelasan bubble sort, selection sort, dan quick sort dengan analisis kompleksitas.',
                'original_filename' => 'sorting_algorithm.webm',
                'file_path' => 'materials/video/sorting_algorithm.webm',
                'file_size' => 52428800,
                'file_type' => 'video/webm',
                'category' => 'video',
                'teacher' => 0,
            ],

            // ── Other ────────────────────────────────────────────────
            [
                'title' => 'Template Proyek HTML Starter',
                'description' => 'Template starter berisi struktur folder, boilerplate HTML, dan config dasar untuk tugas pemrograman web.',
                'original_filename' => 'starter_template.zip',
                'file_path' => 'materials/other/starter_template.zip',
                'file_size' => 2097152,
                'file_type' => 'application/zip',
                'category' => 'other',
                'teacher' => 0,
            ],
            [
                'title' => 'Source Code Contoh REST API',
                'description' => 'Kode sumber contoh implementasi RESTful API menggunakan Express.js dan MongoDB.',
                'original_filename' => 'rest_api_example.zip',
                'file_path' => 'materials/other/rest_api_example.zip',
                'file_size' => 1536000,
                'file_type' => 'application/zip',
                'category' => 'other',
                'teacher' => 2,
            ],
            [
                'title' => 'Konfigurasi Nginx untuk Laravel',
                'description' => 'File konfigurasi Nginx siap pakai untuk deploy aplikasi Laravel dengan HTTPS.',
                'original_filename' => 'nginx_laravel.conf',
                'file_path' => 'materials/document/nginx_laravel.conf',
                'file_size' => 0,
                'file_type' => 'text/plain',
                'category' => 'document',
                'teacher' => 2,
                'content' => "server {\n" .
                    "    listen 80;\n" .
                    "    server_name smkn1garut.sch.id;\n" .
                    "    return 301 https://\$host\$request_uri;\n" .
                    "}\n\n" .
                    "server {\n" .
                    "    listen 443 ssl http2;\n" .
                    "    server_name smkn1garut.sch.id;\n\n" .
                    "    root /var/www/lms/public;\n" .
                    "    index index.php;\n\n" .
                    "    ssl_certificate     /etc/letsencrypt/live/smkn1garut.sch.id/fullchain.pem;\n" .
                    "    ssl_certificate_key /etc/letsencrypt/live/smkn1garut.sch.id/privkey.pem;\n\n" .
                    "    location / {\n" .
                    "        try_files \$uri \$uri/ /index.php?\$query_string;\n" .
                    "    }\n\n" .
                    "    location ~ \\.php\$ {\n" .
                    "        fastcgi_pass unix:/run/php/php8.3-fpm.sock;\n" .
                    "        fastcgi_index index.php;\n" .
                    "        include fastcgi_params;\n" .
                    "        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;\n" .
                    "    }\n" .
                    "}\n",
            ],
        ];

        foreach ($materials as $m) {
            $teacherIdx = $m['teacher'];
            $content = $m['content'] ?? null;
            unset($m['teacher'], $m['content']);

            // Create actual text files on disk for previewable content
            if ($content !== null) {
                $disk->put($m['file_path'], $content);
                $m['file_size'] = strlen($content);
            }

            CourseMaterial::firstOrCreate(
                ['title' => $m['title']],
                array_merge($m, [
                    'teacher_id' => $teachers[$teacherIdx]->id,
                    'classroom_id' => $classroom->id,
                    'is_published' => true,
                ])
            );
        }
    }
}
