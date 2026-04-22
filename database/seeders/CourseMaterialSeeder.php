<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\CourseMaterial;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class CourseMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first teacher and classroom
        $teacher = Teacher::first();
        $classroom = Classroom::first();

        if (! $teacher || ! $classroom) {
            return; // Skip if no teacher or classroom exists
        }

        // Create sample materials
        CourseMaterial::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'schedule_id' => null,
            'title' => 'Materi Matematika Dasar',
            'description' => 'Pengantar aljabar, geometri, dan trigonometri dasar untuk siswa kelas 10',
            'file_path' => 'materials/sample_matematika.pdf',
            'original_filename' => 'matematika_dasar.pdf',
            'file_size' => 1024000,
            'file_type' => 'application/pdf',
            'is_published' => true,
        ]);

        CourseMaterial::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'schedule_id' => null,
            'title' => 'Video Pembelajaran Fisika',
            'description' => 'Konsep gerak, kecepatan, dan energi dalam fisika klasik',
            'file_path' => 'materials/sample_fisika.mp4',
            'original_filename' => 'fisika_gerak.mp4',
            'file_size' => 52428800,
            'file_type' => 'video/mp4',
            'is_published' => true,
        ]);

        CourseMaterial::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'schedule_id' => null,
            'title' => 'Presentasi Biologi',
            'description' => 'Sel, DNA, dan proses evolusi dalam makhluk hidup',
            'file_path' => 'materials/sample_biologi.pptx',
            'original_filename' => 'biologi_sel.pptx',
            'file_size' => 5242880,
            'file_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'is_published' => true,
        ]);

        CourseMaterial::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'schedule_id' => null,
            'title' => 'Lembar Kerja Bahasa Indonesia',
            'description' => 'Tata bahasa, kosakata, dan penulisan esai',
            'file_path' => 'materials/sample_bahasa.docx',
            'original_filename' => 'bahasa_indonesia.docx',
            'file_size' => 2097152,
            'file_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'is_published' => true,
        ]);
    }
}
