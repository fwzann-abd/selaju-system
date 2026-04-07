<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentPosition;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Data StudentPositions
        $positions = ['KM', 'Wakil KM', 'Sekretaris', 'Wakil Sekretaris', 'Bendahara', 'Wakil Bendahara', 'Seksi Kebersihan', 'Seksi Keamanan', 'Seksi Pendidikan', 'Seksi Humas', 'Anggota'];
        $positionMap = [];
        foreach ($positions as $pos) {
            $positionMap[$pos] = StudentPosition::create([
                'name' => $pos,
                'slug' => Str::slug($pos)
            ]);
        }

        // 2. Master Data Classrooms
        $tingkatArray = ['10', '11', '12'];
        $jurusanArray = ['PPLG', 'TJKT', 'DKV', 'MPLB', 'AKL', 'BDP', 'PM', 'ULW', 'TB', 'PH'];
        
        foreach ($tingkatArray as $tingkat) {
            foreach ($jurusanArray as $jurusan) {
                // Rombel randomizer 1 to 3 (average 2)
                $rombelCount = rand(1, 3);
                for ($r = 1; $r <= $rombelCount; $r++) {
                    $name = "$tingkat $jurusan $r";
                    Classroom::create([
                        'name' => $name,
                        'tingkat' => $tingkat,
                        'jurusan' => $jurusan,
                        'rombel' => (string) $r,
                        'slug' => Str::slug($name),
                        'academic_year' => '2025/2026'
                    ]);
                }
            }
        }

        // 3. Master Data Subjects
        $subjects = ['Matematika', 'Bahasa Inggris', 'Pemrograman Web', 'Jaringan Dasar', 'Desain Grafis'];
        $subjectModels = [];
        foreach ($subjects as $idx => $subj) {
            $subjectModels[] = Subject::create([
                'name' => $subj,
                'code' => 'SUBJ-' . ($idx + 1)
            ]);
        }

        // 4. Dummy Users (5 Teachers, 35 Students)
        $teachers = [];
        for ($i = 1; $i <= 5; $i++) {
            $acc = Account::create([
                'username' => 'guru' . $i,
                'email' => "guru$i@sekolah.com",
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
            $teachers[] = Teacher::create([
                'account_id' => $acc->uuid,
                'nip' => '19800101' . rand(1000, 9999),
                'name' => 'Guru ' . $i,
            ]);
        }

        // 0. Dummy Environment (School & Generation)
        $school = \DB::table('schools')->first();
        $schoolId = $school ? $school->id : Str::uuid();
        if (!$school) {
            \DB::table('schools')->insert(['id' => $schoolId, 'slug' => 'sekolah-dummy', 'name' => 'Sekolah Dummy']);
        }

        $gen = \DB::table('generations')->first();
        $genId = $gen ? $gen->id : Str::uuid();
        if (!$gen) {
            \DB::table('generations')->insert([
                'id' => $genId, 'name' => 'Angkatan 1', 'start_years' => 2024, 'end_years' => 2027, 'is_active' => true
            ]);
        }

        $students = [];
        for ($i = 1; $i <= 35; $i++) {
            $acc = Account::create([
                'username' => 'siswa' . $i,
                'email' => "siswa$i@sekolah.com",
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
            $students[] = Student::create([
                'account_id' => $acc->uuid,
                'name' => 'Siswa ' . $i,
                'student_number' => '100' . str_pad((string)$i, 3, '0', STR_PAD_LEFT),
                'national_id' => '000123' . str_pad((string)$i, 4, '0', STR_PAD_LEFT),
                'gender' => $i % 2 == 0 ? 'L' : 'P',
                'school_id' => $schoolId,
                'generation_id' => $genId,
            ]);
        }

        // 5. Scenario KBM
        $targetClassroom = Classroom::inRandomOrder()->first();
        if (!$targetClassroom) {
            $targetClassroom = Classroom::first();
        }

        // Assign Wali Kelas
        $targetClassroom->update([
            'teacher_id' => $teachers[0]->id
        ]);

        // Attach Students to Pivot
        $syncData = [];
        foreach ($students as $key => $student) {
            if ($key == 0) {
                $syncData[$student->id] = ['student_position_id' => $positionMap['KM']->id];
            } elseif ($key == 1) {
                $syncData[$student->id] = ['student_position_id' => $positionMap['Sekretaris']->id];
            } elseif ($key < 5) {
                $syncData[$student->id] = ['student_position_id' => $positionMap['Seksi Kebersihan']->id];
            } else {
                $syncData[$student->id] = ['student_position_id' => $positionMap['Anggota']->id];
            }
        }
        $targetClassroom->students()->syncWithoutDetaching($syncData);

        // 6. Create 3 Schedules for the Classroom
        $days = ['Senin', 'Selasa', 'Rabu'];
        for ($i = 0; $i < 3; $i++) {
            Schedule::create([
                'classroom_id' => $targetClassroom->id,
                'teacher_id' => $teachers[$i % count($teachers)]->id,
                'subject_id' => $subjectModels[$i % count($subjectModels)]->id,
                'day' => $days[$i],
                'start_time' => '07:30:00',
                'end_time' => '09:00:00',
            ]);
        }
    }
}
