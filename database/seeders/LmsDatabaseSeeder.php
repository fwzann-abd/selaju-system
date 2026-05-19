<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Classroom;
use App\Models\Generation;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentPosition;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LmsDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── CLEAR EXISTING DATA ──────────────────────────────────────────────
        $this->command->info('Clearing existing LMS data...');
        $tablesToTruncate = [
            'attendances', 'schedules', 'classroom_students',
            'classrooms', 'students', 'teachers', 'rooms',
            'subjects', 'student_positions', 'generations',
        ];
        foreach ($tablesToTruncate as $table) {
            DB::statement("TRUNCATE TABLE {$table} CASCADE");
        }
        DB::table('schools')->where('slug', 'smkn-1-garut')->delete();

        // ── 1. SCHOOL ────────────────────────────────────────────────────────
        $this->command->info('Creating school...');
        $school = School::create([
            'id' => (string) Str::uuid(),
            'account_id' => null,
            'slug' => 'smkn-1-garut',
            'name' => 'SMKN 1 Garut',
        ]);

        // ── 2. GENERATION ────────────────────────────────────────────────────
        $generation = Generation::create([
            'id' => (string) Str::uuid(),
            'name' => 'Angkatan 2025',
            'start_years' => 2025,
            'end_years' => 2028,
            'is_active' => true,
        ]);

        // ── 3. STUDENT POSITIONS ─────────────────────────────────────────────
        $this->command->info('Creating student positions...');
        $positionNames = [
            'KM', 'Wakil KM', 'Sekretaris', 'Wakil Sekretaris',
            'Bendahara', 'Wakil Bendahara', 'Seksi Kebersihan',
            'Seksi Keamanan', 'Seksi Pendidikan', 'Seksi Humas', 'Anggota',
        ];
        $positionMap = [];
        foreach ($positionNames as $pos) {
            $positionMap[$pos] = StudentPosition::create([
                'name' => $pos,
                'slug' => Str::slug($pos),
            ]);
        }
        $memberPositionId = $positionMap['Anggota']->id;

        // ── 4. ROOMS ─────────────────────────────────────────────────────────
        $this->command->info('Creating rooms...');
        $generalRooms = [];
        for ($i = 1; $i <= 20; $i++) {
            $generalRooms[] = Room::create([
                'name' => 'Ruang Kelas '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'building' => 'Gedung Utama',
                'capacity' => 36,
            ]);
        }
        $labKomputer = Room::create(['name' => 'Lab Komputer 1', 'building' => 'Gedung Lab', 'capacity' => 30]);
        $labKomputer2 = Room::create(['name' => 'Lab Komputer 2', 'building' => 'Gedung Lab', 'capacity' => 30]);
        $workshop = Room::create(['name' => 'Workshop TKF', 'building' => 'Gedung Praktik', 'capacity' => 25]);
        $labAkuntansi = Room::create(['name' => 'Lab Akuntansi', 'building' => 'Gedung Lab', 'capacity' => 30]);
        $labJaringan = Room::create(['name' => 'Lab Jaringan', 'building' => 'Gedung Lab', 'capacity' => 28]);
        $labMultimedia = Room::create(['name' => 'Lab Multimedia', 'building' => 'Gedung Lab', 'capacity' => 28]);

        // ── 5. SUBJECTS ──────────────────────────────────────────────────────
        $this->command->info('Creating subjects...');

        // General subjects (Week A)
        $generalSubjects = [];
        foreach ([
            ['MTK', 'Matematika'],
            ['BIND', 'Bahasa Indonesia'],
            ['BING', 'Bahasa Inggris'],
            ['PAI', 'Pendidikan Agama Islam'],
            ['PJOK', 'Pendidikan Jasmani & Olahraga'],
        ] as $s) {
            $generalSubjects[$s[0]] = Subject::create(['name' => $s[1], 'code' => $s[0], 'type' => 'general']);
        }

        // Vocational subjects per department (Week B)
        $vocationalSubjects = [];
        $vocationalData = [
            'AKL' => [['AKL-AKD', 'Akuntansi Dasar'], ['AKL-SPR', 'Spreadsheet Akuntansi'], ['AKL-PAJ', 'Perpajakan']],
            'MPL' => [['MPL-MNG', 'Manajemen Pemasaran'], ['MPL-DGT', 'Pemasaran Digital'], ['MPL-SLS', 'Teknik Penjualan']],
            'TLG' => [['TLG-TSR', 'Teknik Telekomunikasi'], ['TLG-FBR', 'Instalasi Fiber Optik'], ['TLG-RDO', 'Teknik Radio']],
            'PM' => [['PM-PKR', 'Pemasaran Kreatif'], ['PM-EKM', 'E-Commerce'], ['PM-ADV', 'Periklanan Digital']],
            'TKF' => [['TKF-ELK', 'Elektronika Dasar'], ['TKF-MKT', 'Mekatronika'], ['TKF-PLC', 'Programmable Logic Controller']],
            'TLM' => [['TLM-KIM', 'Kimia Analitik'], ['TLM-INS', 'Instrumentasi Lab'], ['TLM-KLT', 'Pengendalian Kualitas']],
            'PPL' => [['PPL-LRV', 'Laravel & Backend Dev'], ['PPL-MOB', 'Mobile App Development'], ['PPL-API', 'REST API & Database']],
            'TJK' => [['TJK-MKT', 'Administrasi Mikrotik'], ['TJK-CSC', 'Cisco Networking'], ['TJK-SRV', 'Server Administration']],
            'TET' => [['TET-ELK', 'Instalasi Tenaga Listrik'], ['TET-PGK', 'Pengukuran Listrik'], ['TET-SWT', 'Sistem Kontrol']],
            'DKV' => [['DKV-DSN', 'Desain Grafis'], ['DKV-ILS', 'Ilustrasi Digital'], ['DKV-VDO', 'Videografi & Editing']],
        ];
        foreach ($vocationalData as $dept => $subjects) {
            $vocationalSubjects[$dept] = [];
            foreach ($subjects as $s) {
                $vocationalSubjects[$dept][] = Subject::create(['name' => $s[1], 'code' => $s[0], 'type' => 'vocational']);
            }
        }

        // ── 6. TEACHERS ──────────────────────────────────────────────────────
        $this->command->info('Creating teachers...');

        // Demo teacher first
        $budiAccount = Account::create([
            'uuid' => (string) Str::uuid(),
            'nomor_participant' => 'P'.rand(10000000, 99999999),
            'username' => 'budi.santoso',
            'email' => 'budi.santoso@smkn1garut.sch.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $budiTeacher = Teacher::create([
            'account_id' => $budiAccount->uuid,
            'school_id' => $school->id,
            'nip' => '198503152010011001',
            'name' => 'Budi Santoso, S.Pd.',
        ]);

        // Other teachers
        $teacherData = [
            ['username' => 'siti.aminah',     'email' => 'siti.aminah@smkn1garut.sch.id',     'name' => 'Siti Aminah, M.Pd.',       'nip' => '198712202012042002'],
            ['username' => 'dedi.kurniawan',  'email' => 'dedi.kurniawan@smkn1garut.sch.id',  'name' => 'Dedi Kurniawan, S.Kom.',    'nip' => '199001102015031003'],
            ['username' => 'rina.wati',       'email' => 'rina.wati@smkn1garut.sch.id',       'name' => 'Rina Wati, S.Pd.',         'nip' => '198808252013012004'],
            ['username' => 'ahmad.fauzi',     'email' => 'ahmad.fauzi@smkn1garut.sch.id',     'name' => 'Ahmad Fauzi, S.T.',        'nip' => '199205052018011005'],
            ['username' => 'dewi.rahayu',     'email' => 'dewi.rahayu@smkn1garut.sch.id',     'name' => 'Dewi Rahayu, S.Pd.',       'nip' => '198604112011042003'],
            ['username' => 'hendra.wijaya',   'email' => 'hendra.wijaya@smkn1garut.sch.id',   'name' => 'Hendra Wijaya, S.T.',      'nip' => '198901012016031006'],
            ['username' => 'maya.sari',       'email' => 'maya.sari@smkn1garut.sch.id',       'name' => 'Maya Sari, M.Kom.',        'nip' => '199103252019012007'],
            ['username' => 'rizki.pratama',   'email' => 'rizki.pratama@smkn1garut.sch.id',   'name' => 'Rizki Pratama, S.Kom.',    'nip' => '199407082020011008'],
            ['username' => 'tika.handayani',  'email' => 'tika.handayani@smkn1garut.sch.id',  'name' => 'Tika Handayani, S.Pd.',    'nip' => '198806152014012005'],
            ['username' => 'arief.rahman',    'email' => 'arief.rahman@smkn1garut.sch.id',    'name' => 'Arief Rahman, S.T.',       'nip' => '199002202017031009'],
            ['username' => 'nadia.putri',     'email' => 'nadia.putri@smkn1garut.sch.id',     'name' => 'Nadia Putri, S.Pd.',       'nip' => '199206302021012010'],
            ['username' => 'yusuf.maulana',   'email' => 'yusuf.maulana@smkn1garut.sch.id',   'name' => 'Yusuf Maulana, S.T.',      'nip' => '198811102015031011'],
            ['username' => 'eka.fitriani',    'email' => 'eka.fitriani@smkn1garut.sch.id',    'name' => 'Eka Fitriani, M.Pd.',      'nip' => '198705012012042012'],
            ['username' => 'bayu.nugroho',    'email' => 'bayu.nugroho@smkn1garut.sch.id',    'name' => 'Bayu Nugroho, S.Kom.',     'nip' => '199304182019011013'],
            ['username' => 'ratna.dewi',      'email' => 'ratna.dewi@smkn1garut.sch.id',      'name' => 'Ratna Dewi, S.Pd.',        'nip' => '198903272013012014'],
            ['username' => 'galih.prasetyo',  'email' => 'galih.prasetyo@smkn1garut.sch.id',  'name' => 'Galih Prasetyo, S.T.',     'nip' => '199108112018031015'],
            ['username' => 'intan.permata',   'email' => 'intan.permata@smkn1garut.sch.id',   'name' => 'Intan Permata, S.Pd.',     'nip' => '199412052022012016'],
            ['username' => 'faisal.hakim',    'email' => 'faisal.hakim@smkn1garut.sch.id',    'name' => 'Faisal Hakim, M.T.',       'nip' => '198701032014031017'],
            ['username' => 'winda.kusuma',    'email' => 'winda.kusuma@smkn1garut.sch.id',    'name' => 'Winda Kusuma, S.Pd.',      'nip' => '199509162023012018'],
            ['username' => 'teguh.santoso',   'email' => 'teguh.santoso@smkn1garut.sch.id',   'name' => 'Teguh Santoso, S.Kom.',    'nip' => '199210122017031019'],
            ['username' => 'linda.safitri',   'email' => 'linda.safitri@smkn1garut.sch.id',   'name' => 'Linda Safitri, M.Pd.',     'nip' => '198804162011042020'],
            ['username' => 'ogi.permana',     'email' => 'ogi.permana@smkn1garut.sch.id',     'name' => 'Ogi Permana, S.T.',        'nip' => '199506082021031021'],
            ['username' => 'sri.wahyuni',     'email' => 'sri.wahyuni@smkn1garut.sch.id',     'name' => 'Sri Wahyuni, S.Pd.',       'nip' => '198612272010042022'],
            ['username' => 'fajar.hidayat',   'email' => 'fajar.hidayat@smkn1garut.sch.id',   'name' => 'Fajar Hidayat, S.Kom.',    'nip' => '199301202019011023'],
            ['username' => 'ayu.lestari',     'email' => 'ayu.lestari@smkn1garut.sch.id',     'name' => 'Ayu Lestari, S.Pd.',       'nip' => '199007142015012024'],
            ['username' => 'irfan.setiawan',  'email' => 'irfan.setiawan@smkn1garut.sch.id',  'name' => 'Irfan Setiawan, S.T.',     'nip' => '198803262013031025'],
            ['username' => 'putri.anjani',    'email' => 'putri.anjani@smkn1garut.sch.id',    'name' => 'Putri Anjani, M.Kom.',     'nip' => '199411052022012026'],
            ['username' => 'rendra.gunawan',  'email' => 'rendra.gunawan@smkn1garut.sch.id',  'name' => 'Rendra Gunawan, S.T.',     'nip' => '199205172018031027'],
            ['username' => 'hasna.suryani',   'email' => 'hasna.suryani@smkn1garut.sch.id',   'name' => 'Hasna Suryani, S.Pd.',     'nip' => '198907082014012028'],
        ];

        $otherTeachers = [];
        foreach ($teacherData as $t) {
            $acc = Account::create([
                'uuid' => (string) Str::uuid(),
                'nomor_participant' => 'P'.rand(10000000, 99999999),
                'username' => $t['username'],
                'email' => $t['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
            $otherTeachers[] = Teacher::create([
                'account_id' => $acc->uuid,
                'school_id' => $school->id,
                'nip' => $t['nip'],
                'name' => $t['name'],
            ]);
        }

        $allTeachers = array_merge([$budiTeacher], $otherTeachers);
        $this->command->info('Created '.count($allTeachers).' teachers.');

        // ── 7. CLASSROOMS & STUDENTS ─────────────────────────────────────────
        $this->command->info('Creating 60 classrooms and 1,500 students...');

        $departments = ['AKL', 'MPL', 'TLG', 'PM', 'TKF', 'TLM', 'PPL', 'TJK', 'TET', 'DKV'];
        $levels = [10, 11, 12];
        $groups = [1, 2];

        $allClassrooms = [];
        $teacherIndex = 0;

        foreach ($departments as $dept) {
            foreach ($levels as $level) {
                foreach ($groups as $group) {
                    $name = "{$level} {$dept} {$group}";
                    $homeTeacher = $allTeachers[$teacherIndex % count($allTeachers)];
                    $teacherIndex++;

                    $classroom = Classroom::create([
                        'school_id' => $school->id,
                        'name' => $name,
                        'level' => (string) $level,
                        'major' => $dept,
                        'group_number' => (string) $group,
                        'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
                        'teacher_id' => $homeTeacher->id,
                        'academic_year' => '2025/2026',
                    ]);

                    // Generate 25 students per classroom
                    $students = Student::factory(25)->create([
                        'school_id' => $school->id,
                        'generation_id' => $generation->id,
                    ]);

                    // Assign students to classroom with positions
                    $syncData = [];
                    foreach ($students as $idx => $student) {
                        $posName = match (true) {
                            $idx === 0 => 'KM',
                            $idx === 1 => 'Wakil KM',
                            $idx === 2 => 'Sekretaris',
                            $idx === 3 => 'Bendahara',
                            $idx < 6 => 'Seksi Kebersihan',
                            default => 'Anggota',
                        };
                        $syncData[$student->id] = ['student_position_id' => $positionMap[$posName]->id];
                    }
                    $classroom->students()->syncWithoutDetaching($syncData);

                    $allClassrooms["{$dept}_{$level}_{$group}"] = $classroom;
                }
            }
        }

        $this->command->info('Created 60 classrooms with 1,500 students.');

        // ── 8. SCHEDULES ─────────────────────────────────────────────────────
        $this->command->info('Creating schedules...');

        // Week A = Senin/Selasa (general subjects)
        // Week B = Rabu/Kamis (vocational subjects)
        // Jumat = shared

        $generalTeacherMap = [
            'MTK' => 1,  // Siti Aminah
            'BIND' => 2,  // Dedi Kurniawan
            'BING' => 3,  // Rina Wati
            'PAI' => 4,  // Ahmad Fauzi
            'PJOK' => 5,  // Dewi Rahayu
        ];

        $generalSlots = [
            ['day' => 'Senin',  'start' => '07:00', 'end' => '08:30', 'subj_key' => 'MTK'],
            ['day' => 'Senin',  'start' => '08:45', 'end' => '10:15', 'subj_key' => 'BIND'],
            ['day' => 'Selasa', 'start' => '07:00', 'end' => '08:30', 'subj_key' => 'BING'],
            ['day' => 'Selasa', 'start' => '08:45', 'end' => '10:15', 'subj_key' => 'PAI'],
            ['day' => 'Jumat',  'start' => '07:00', 'end' => '08:30', 'subj_key' => 'PJOK'],
        ];

        $vocationalSlots = [
            ['day' => 'Rabu',  'start' => '07:00', 'end' => '08:30',  'subj_idx' => 0],
            ['day' => 'Rabu',  'start' => '08:45', 'end' => '10:15',  'subj_idx' => 0],
            ['day' => 'Kamis', 'start' => '07:00', 'end' => '08:30',  'subj_idx' => 1],
            ['day' => 'Kamis', 'start' => '08:45', 'end' => '10:15',  'subj_idx' => 2],
            ['day' => 'Jumat', 'start' => '08:45', 'end' => '10:15',  'subj_idx' => 1],
        ];

        $roomIndex = 0;

        foreach ($departments as $dept) {
            foreach ($levels as $level) {
                foreach ($groups as $group) {
                    $classroom = $allClassrooms["{$dept}_{$level}_{$group}"];
                    $deptTeacherOffset = array_search($dept, $departments) * 3;

                    // --- General (Week A) schedules ---
                    foreach ($generalSlots as $slot) {
                        $teacherForSlot = $allTeachers[$generalTeacherMap[$slot['subj_key']] % count($allTeachers)];
                        Schedule::create([
                            'classroom_id' => $classroom->id,
                            'teacher_id' => $teacherForSlot->id,
                            'subject_id' => $generalSubjects[$slot['subj_key']]->id,
                            'room_id' => $generalRooms[$roomIndex % count($generalRooms)]->id,
                            'day' => $slot['day'],
                            'start_time' => $slot['start'],
                            'end_time' => $slot['end'],
                        ]);
                    }

                    // --- Vocational (Week B) schedules ---
                    $deptSubjects = $vocationalSubjects[$dept];
                    $vocationRooms = $this->getVocationalRoom(
                        $dept, $labKomputer, $labKomputer2, $workshop, $labAkuntansi, $labJaringan, $labMultimedia
                    );

                    foreach ($vocationalSlots as $slot) {
                        $subjIdx = $slot['subj_idx'];
                        $vocSubject = $deptSubjects[min($subjIdx, count($deptSubjects) - 1)];
                        $vocTeacher = $allTeachers[($deptTeacherOffset + $subjIdx + 6) % count($allTeachers)];

                        Schedule::create([
                            'classroom_id' => $classroom->id,
                            'teacher_id' => $vocTeacher->id,
                            'subject_id' => $vocSubject->id,
                            'room_id' => $vocationRooms->id,
                            'day' => $slot['day'],
                            'start_time' => $slot['start'],
                            'end_time' => $slot['end'],
                        ]);
                    }

                    $roomIndex++;
                }
            }
        }

        // ── 9. BUDI SANTOSO — PPL classes ────────────────────────────────────
        $this->command->info('Assigning Budi Santoso to PPL classes...');
        $ppl1 = $allClassrooms['PPL_10_1'];
        $ppl2 = $allClassrooms['PPL_10_2'];

        $budiSubjects = $vocationalSubjects['PPL'];

        $budiSlots = [
            ['day' => 'Senin',  'start' => '10:30', 'end' => '12:00', 'subj_idx' => 0],
            ['day' => 'Rabu',   'start' => '10:30', 'end' => '12:00', 'subj_idx' => 1],
            ['day' => 'Kamis',  'start' => '10:30', 'end' => '12:00', 'subj_idx' => 2],
        ];

        foreach ([$ppl1, $ppl2] as $pplClass) {
            foreach ($budiSlots as $slot) {
                Schedule::firstOrCreate(
                    [
                        'classroom_id' => $pplClass->id,
                        'day' => $slot['day'],
                        'start_time' => $slot['start'],
                    ],
                    [
                        'teacher_id' => $budiTeacher->id,
                        'subject_id' => $budiSubjects[$slot['subj_idx']]->id,
                        'room_id' => $labKomputer->id,
                        'end_time' => $slot['end'],
                    ]
                );
            }
        }

        $this->command->info('✅ LmsDatabaseSeeder complete!');
        $this->command->info('   School:     SMKN 1 Garut');
        $this->command->info('   Departments: '.count($departments));
        $this->command->info('   Classrooms:  60 (10 dept × 3 level × 2 group)');
        $this->command->info('   Students:    1,500 (25 per class)');
        $this->command->info('   Teachers:    '.count($allTeachers));
        $this->command->info('   Demo login:  budi.santoso / password');
    }

    /**
     * Returns the appropriate Room for a department's vocational lab.
     */
    private function getVocationalRoom(
        string $dept,
        Room $labKomputer,
        Room $labKomputer2,
        Room $workshop,
        Room $labAkuntansi,
        Room $labJaringan,
        Room $labMultimedia
    ): Room {
        return match ($dept) {
            'PPL', 'PM', 'MPL' => $labKomputer,
            'TJK' => $labJaringan,
            'DKV' => $labMultimedia,
            'AKL' => $labAkuntansi,
            'TKF', 'TET' => $workshop,
            default => $labKomputer2,
        };
    }
}
