<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
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
        // ── 1. Student Positions ─────────────────────────────────────────
        $positions = ['KM', 'Wakil KM', 'Sekretaris', 'Wakil Sekretaris', 'Bendahara', 'Wakil Bendahara', 'Seksi Kebersihan', 'Seksi Keamanan', 'Seksi Pendidikan', 'Seksi Humas', 'Anggota'];
        $positionMap = [];
        foreach ($positions as $pos) {
            $positionMap[$pos] = StudentPosition::firstOrCreate(
                ['slug' => Str::slug($pos)],
                ['name' => $pos, 'slug' => Str::slug($pos)]
            );
        }

        // ── 2. Classrooms (use new English column names) ─────────────────
        $levels = ['10', '11', '12'];
        $majors = ['PPLG', 'TJKT', 'DKV', 'MPLB', 'AKL'];

        $classroomModels = [];
        foreach ($levels as $level) {
            foreach ($majors as $major) {
                $groupCount = rand(1, 2);
                for ($r = 1; $r <= $groupCount; $r++) {
                    $name = "$level $major $r";
                    $classroomModels[] = Classroom::firstOrCreate(
                        ['name' => $name],
                        [
                            'name' => $name,
                            'level' => $level,
                            'major' => $major,
                            'group_number' => (string) $r,
                            'slug' => Str::slug($name),
                            'academic_year' => '2025/2026',
                        ]
                    );
                }
            }
        }

        // ── 3. Subjects ──────────────────────────────────────────────────
        $subjectData = [
            ['name' => 'Pemrograman Web', 'code' => 'PW-01', 'type' => 'vocational'],
            ['name' => 'Basis Data', 'code' => 'BD-01', 'type' => 'vocational'],
            ['name' => 'Pemrograman Mobile', 'code' => 'PM-01', 'type' => 'vocational'],
            ['name' => 'Jaringan Komputer', 'code' => 'JK-01', 'type' => 'vocational'],
            ['name' => 'Desain Grafis', 'code' => 'DG-01', 'type' => 'vocational'],
            ['name' => 'Matematika', 'code' => 'MTK-01', 'type' => 'general'],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG-01', 'type' => 'general'],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN-01', 'type' => 'general'],
            ['name' => 'Pendidikan Agama', 'code' => 'PAI-01', 'type' => 'general'],
            ['name' => 'PKN', 'code' => 'PKN-01', 'type' => 'general'],
        ];
        $subjectModels = [];
        foreach ($subjectData as $s) {
            $subjectModels[] = Subject::firstOrCreate(['code' => $s['code']], $s);
        }

        // ── 4. School & Generation ───────────────────────────────────────
        $school = \DB::table('schools')->first();
        $schoolId = $school ? $school->id : (string) Str::uuid();
        if (! $school) {
            $schoolAccount = Account::create([
                'username' => 'smkn1garut',
                'email' => 'admin@smkn1garut.sch.id',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]);
            \DB::table('schools')->insert([
                'id' => $schoolId,
                'account_id' => $schoolAccount->uuid,
                'slug' => 'smkn-1-garut',
                'name' => 'SMKN 1 Garut',
            ]);
        }

        $gen = \DB::table('generations')->first();
        $genId = $gen ? $gen->id : (string) Str::uuid();
        if (! $gen) {
            \DB::table('generations')->insert([
                'id' => $genId, 'name' => 'Angkatan 2025', 'start_years' => 2025, 'end_years' => 2028, 'is_active' => true,
            ]);
        }

        // ── 5. Teachers (realistic names, matching demo creds) ──────────
        $teacherData = [
            ['username' => 'guru1', 'email' => 'guru1@smkn1garut.sch.id', 'name' => 'Budi Santoso, S.Pd.', 'nip' => '198503152010011001'],
            ['username' => 'guru2', 'email' => 'guru2@smkn1garut.sch.id', 'name' => 'Siti Aminah, M.Pd.', 'nip' => '198712202012042002'],
            ['username' => 'guru3', 'email' => 'guru3@smkn1garut.sch.id', 'name' => 'Dedi Kurniawan, S.Kom.', 'nip' => '199001102015031003'],
            ['username' => 'guru4', 'email' => 'guru4@smkn1garut.sch.id', 'name' => 'Rina Wati, S.Pd.', 'nip' => '198808252013012004'],
            ['username' => 'guru5', 'email' => 'guru5@smkn1garut.sch.id', 'name' => 'Ahmad Fauzi, S.T.', 'nip' => '199205052018011005'],
        ];
        $teachers = [];
        foreach ($teacherData as $t) {
            $acc = Account::firstOrCreate(
                ['username' => $t['username']],
                ['username' => $t['username'], 'email' => $t['email'], 'password' => Hash::make('password123'), 'is_active' => true]
            );
            $teachers[] = Teacher::firstOrCreate(
                ['account_id' => $acc->uuid],
                ['account_id' => $acc->uuid, 'school_id' => $schoolId, 'nip' => $t['nip'], 'name' => $t['name']]
            );
        }

        // ── 6. Students (realistic names, matching demo creds) ──────────
        $studentNames = [
            'Andi Pratama', 'Dewi Sartika', 'Rizki Ramadhan', 'Nadia Putri', 'Fajar Hidayat',
            'Sari Wulandari', 'Muhammad Ilham', 'Ayu Lestari', 'Rendi Saputra', 'Indah Permata',
            'Bagus Setiawan', 'Fitri Handayani', 'Dimas Ardiansyah', 'Lia Amelia', 'Yusuf Maulana',
            'Putri Rahayu', 'Hendra Wijaya', 'Maya Anggraini', 'Kevin Pratama', 'Nur Fadilah',
            'Agus Firmansyah', 'Tika Sari', 'Bayu Nugroho', 'Ratna Dewi', 'Irfan Hakim',
            'Winda Kusuma', 'Arief Rahman', 'Citra Anjani', 'Dani Setiawan', 'Eka Fitriani',
            'Galih Prasetyo', 'Hani Safitri', 'Joko Susilo', 'Kartika Sari', 'Lutfi Habibi',
        ];
        $students = [];
        foreach ($studentNames as $i => $name) {
            $idx = $i + 1;
            $acc = Account::firstOrCreate(
                ['username' => 'siswa'.$idx],
                ['username' => 'siswa'.$idx, 'email' => "siswa{$idx}@smkn1garut.sch.id", 'password' => Hash::make('password123'), 'is_active' => true]
            );
            $students[] = Student::firstOrCreate(
                ['account_id' => $acc->uuid],
                [
                    'account_id' => $acc->uuid,
                    'name' => $name,
                    'student_number' => '2025'.str_pad((string) $idx, 4, '0', STR_PAD_LEFT),
                    'national_id' => '00301'.str_pad((string) $idx, 5, '0', STR_PAD_LEFT),
                    'gender' => $idx % 2 == 0 ? 'P' : 'L',
                    'school_id' => $schoolId,
                    'generation_id' => $genId,
                ]
            );
        }

        // ── 7. Assign students to classrooms ─────────────────────────────
        $targetClass = $classroomModels[0]; // 10 PPLG 1
        $targetClass->update(['teacher_id' => $teachers[0]->id]);

        $syncData = [];
        foreach ($students as $key => $student) {
            $posName = match (true) {
                $key === 0 => 'KM',
                $key === 1 => 'Sekretaris',
                $key === 2 => 'Bendahara',
                $key < 5 => 'Seksi Kebersihan',
                default => 'Anggota',
            };
            $syncData[$student->id] = ['student_position_id' => $positionMap[$posName]->id];
        }
        $targetClass->students()->syncWithoutDetaching($syncData);

        // Also assign first 15 students to second classroom
        if (isset($classroomModels[1])) {
            $class2 = $classroomModels[1];
            $class2->update(['teacher_id' => $teachers[1]->id]);
            $sync2 = [];
            foreach (array_slice($students, 0, 15) as $s) {
                $sync2[$s->id] = ['student_position_id' => $positionMap['Anggota']->id];
            }
            $class2->students()->syncWithoutDetaching($sync2);
        }

        // ── 8. Schedules (5 days, multiple slots) ────────────────────────
        $scheduleData = [
            ['day' => 'Senin', 'start' => '07:00', 'end' => '08:30', 'teacher' => 0, 'subject' => 0, 'class' => 0],
            ['day' => 'Senin', 'start' => '08:45', 'end' => '10:15', 'teacher' => 1, 'subject' => 5, 'class' => 0],
            ['day' => 'Senin', 'start' => '10:30', 'end' => '12:00', 'teacher' => 2, 'subject' => 1, 'class' => 0],
            ['day' => 'Selasa', 'start' => '07:00', 'end' => '08:30', 'teacher' => 3, 'subject' => 6, 'class' => 0],
            ['day' => 'Selasa', 'start' => '08:45', 'end' => '10:15', 'teacher' => 4, 'subject' => 2, 'class' => 0],
            ['day' => 'Rabu', 'start' => '07:00', 'end' => '08:30', 'teacher' => 0, 'subject' => 3, 'class' => 0],
            ['day' => 'Rabu', 'start' => '10:30', 'end' => '12:00', 'teacher' => 1, 'subject' => 7, 'class' => 0],
            ['day' => 'Kamis', 'start' => '07:00', 'end' => '08:30', 'teacher' => 2, 'subject' => 4, 'class' => 0],
            ['day' => 'Kamis', 'start' => '08:45', 'end' => '10:15', 'teacher' => 3, 'subject' => 8, 'class' => 0],
            ['day' => 'Jumat', 'start' => '07:00', 'end' => '08:30', 'teacher' => 4, 'subject' => 9, 'class' => 0],
            ['day' => 'Jumat', 'start' => '08:45', 'end' => '10:15', 'teacher' => 0, 'subject' => 0, 'class' => 0],
        ];

        $rooms = \App\Models\Room::limit(5)->get();
        foreach ($scheduleData as $idx => $sd) {
            Schedule::firstOrCreate(
                ['classroom_id' => $classroomModels[$sd['class']]->id, 'day' => $sd['day'], 'start_time' => $sd['start']],
                [
                    'classroom_id' => $classroomModels[$sd['class']]->id,
                    'teacher_id' => $teachers[$sd['teacher']]->id,
                    'subject_id' => $subjectModels[$sd['subject']]->id,
                    'room_id' => $rooms->isNotEmpty() ? $rooms[$idx % $rooms->count()]->id : null,
                    'day' => $sd['day'],
                    'start_time' => $sd['start'],
                    'end_time' => $sd['end'],
                ]
            );
        }

        // ── 9. Attendance records ────────────────────────────────────────
        $schedules = Schedule::where('classroom_id', $targetClass->id)->get();
        $statuses = ['present', 'present', 'present', 'present', 'late', 'present', 'excused', 'present', 'present', 'absent'];
        foreach ($schedules->take(5) as $sIdx => $schedule) {
            foreach (array_slice($students, 0, 20) as $stIdx => $student) {
                $date = now()->subDays(rand(1, 14))->format('Y-m-d');
                \DB::table('attendances')->insertOrIgnore([
                    'schedule_id' => $schedule->id,
                    'student_id' => $student->id,
                    'status' => $statuses[($sIdx + $stIdx) % count($statuses)],
                    'date' => $date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ── 10. Assignments ──────────────────────────────────────────────
        $assignmentData = [
            ['title' => 'Membuat Landing Page Responsive', 'desc' => 'Buatlah halaman web responsive menggunakan HTML, CSS, dan JavaScript. Gunakan media queries.', 'type' => 'homework', 'max' => 100, 'days' => 7, 'teacher' => 0, 'subject' => 0],
            ['title' => 'Proyek Database Perpustakaan', 'desc' => 'Rancang ERD dan implementasikan database perpustakaan sekolah dengan minimal 5 tabel berelasi.', 'type' => 'project', 'max' => 100, 'days' => 14, 'teacher' => 2, 'subject' => 1],
            ['title' => 'Kuis Jaringan Komputer BAB 3', 'desc' => 'Kuis online mengenai topologi jaringan, subnetting, dan routing dasar.', 'type' => 'quiz', 'max' => 50, 'days' => 3, 'teacher' => 0, 'subject' => 3],
            ['title' => 'UTS Matematika Semester 2', 'desc' => 'Ujian tengah semester meliputi materi trigonometri, logaritma, dan statistika dasar.', 'type' => 'exam', 'max' => 100, 'days' => 1, 'teacher' => 1, 'subject' => 5],
            ['title' => 'Essay Bahasa Indonesia', 'desc' => 'Tulis esai argumentatif tentang dampak teknologi terhadap pendidikan. Minimal 500 kata.', 'type' => 'homework', 'max' => 80, 'days' => 5, 'teacher' => 1, 'subject' => 7],
        ];

        foreach ($assignmentData as $ad) {
            $asg = Assignment::firstOrCreate(
                ['title' => $ad['title'], 'teacher_id' => $teachers[$ad['teacher']]->id],
                [
                    'teacher_id' => $teachers[$ad['teacher']]->id,
                    'classroom_id' => $targetClass->id,
                    'subject_id' => $subjectModels[$ad['subject']]->id,
                    'title' => $ad['title'],
                    'description' => $ad['desc'],
                    'due_date' => now()->addDays($ad['days']),
                    'max_score' => $ad['max'],
                    'type' => $ad['type'],
                    'is_published' => true,
                ]
            );

            // Create some submissions
            foreach (array_slice($students, 0, rand(5, 12)) as $student) {
                AssignmentSubmission::firstOrCreate(
                    ['assignment_id' => $asg->id, 'student_id' => $student->id],
                    [
                        'assignment_id' => $asg->id,
                        'student_id' => $student->id,
                        'notes' => 'Tugas sudah dikerjakan.',
                        'status' => collect(['submitted', 'graded', 'graded'])->random(),
                        'score' => rand(60, 100),
                        'submitted_at' => now()->subDays(rand(0, 3)),
                        'graded_at' => now()->subDay(),
                    ]
                );
            }
        }

        // ── 11. Announcements ────────────────────────────────────────────
        $announcementData = [
            ['title' => 'Jadwal UTS Semester Genap 2025/2026', 'body' => "Ujian Tengah Semester Genap akan dilaksanakan pada tanggal 12-16 Mei 2026.\n\nPastikan seluruh siswa mempersiapkan diri dengan baik. Jadwal detail per mata pelajaran akan diumumkan oleh masing-masing guru.\n\nSemangat belajar!", 'priority' => 'important', 'pinned' => true, 'classroom' => null, 'teacher' => 0],
            ['title' => 'Pengumpulan Tugas Pemrograman Web', 'body' => 'Batas akhir pengumpulan tugas Landing Page Responsive adalah hari Jumat, 9 Mei 2026 pukul 23:59. Kumpulkan melalui menu Tugas di LMS. Keterlambatan akan mengurangi nilai.', 'priority' => 'urgent', 'pinned' => false, 'classroom' => 0, 'teacher' => 0],
            ['title' => 'Libur Hari Raya Waisak', 'body' => 'Sekolah libur pada tanggal 12 Mei 2026 dalam rangka Hari Raya Waisak. Kegiatan belajar mengajar akan dilanjutkan pada 13 Mei 2026.', 'priority' => 'normal', 'pinned' => false, 'classroom' => null, 'teacher' => 1],
            ['title' => 'Perubahan Jadwal Lab Komputer', 'body' => "Mulai minggu depan, jadwal penggunaan Lab Komputer untuk kelas PPLG diubah:\n- Senin: 10 PPLG 1\n- Selasa: 11 PPLG 1\n- Rabu: 12 PPLG 1\n\nHarap menyesuaikan.", 'priority' => 'important', 'pinned' => false, 'classroom' => 0, 'teacher' => 2],
            ['title' => 'Selamat Datang di LMS Melesat!', 'body' => 'Selamat datang di Learning Management System SMKN 1 Garut. Melalui platform ini, kalian dapat mengakses materi, mengerjakan tugas, dan memantau kehadiran secara online.', 'priority' => 'normal', 'pinned' => true, 'classroom' => null, 'teacher' => 0],
        ];

        foreach ($announcementData as $an) {
            Announcement::firstOrCreate(
                ['title' => $an['title']],
                [
                    'author_id' => $teachers[$an['teacher']]->account->uuid,
                    'classroom_id' => $an['classroom'] !== null ? $classroomModels[$an['classroom']]->id : null,
                    'title' => $an['title'],
                    'body' => $an['body'],
                    'priority' => $an['priority'],
                    'is_pinned' => $an['pinned'],
                ]
            );
        }
    }
}
