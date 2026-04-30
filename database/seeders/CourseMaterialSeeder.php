<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\CourseMaterial;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class CourseMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::limit(5)->get();
        $classroom = Classroom::first();

        if ($teachers->isEmpty() || ! $classroom) {
            return;
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
                'title' => 'Catatan Ringkas Jaringan Komputer',
                'description' => 'Rangkuman materi jaringan komputer: OSI Layer, TCP/IP, subnetting, dan routing protocols.',
                'original_filename' => 'catatan_jarkom.txt',
                'file_path' => 'materials/document/catatan_jarkom.txt',
                'file_size' => 45056,
                'file_type' => 'text/plain',
                'category' => 'document',
                'teacher' => 0,
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
        ];

        foreach ($materials as $m) {
            $teacherIdx = $m['teacher'];
            unset($m['teacher']);

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
