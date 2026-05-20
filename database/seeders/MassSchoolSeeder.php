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

class MassSchoolSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding full school ecosystem for SMKN 1 Garut...');

        $this->truncateTables([
            'classroom_students',
            'schedules',
            'classrooms',
            'students',
            'teachers',
            'rooms',
            'subjects',
            'student_positions',
            'generations',
        ]);

        $school = School::firstOrCreate([
            'slug' => 'smkn-1-garut',
        ], [
            'id' => (string) Str::uuid(),
            'name' => 'SMKN 1 Garut',
            'account_id' => null,
        ]);

        $generation = Generation::firstOrCreate([
            'name' => 'Angkatan 2025',
        ], [
            'id' => (string) Str::uuid(),
            'start_years' => 2025,
            'end_years' => 2028,
            'is_active' => true,
        ]);

        $positionMap = $this->createStudentPositions();
        $rooms = $this->createRooms();
        $generalSubjects = $this->createGeneralSubjects();
        $vocationalSubjects = $this->createVocationalSubjects();

        $teachers = $this->createTeachers($school->id);
        $this->command->info('Created '.count($teachers).' teacher records.');

        $classrooms = $this->createClassrooms($school->id, $teachers);
        $this->command->info('Created '.count($classrooms).' classrooms.');

        $this->createStudentsPerClass($classrooms, $school->id, $generation->id, $positionMap);
        $this->command->info('Created students and assigned them to each classroom.');

        $this->mapAccountsForTeachers();
        $this->mapAccountsForStudents();
        $this->command->info('Mapped accounts for all teachers and students.');

        $this->createSchedules($classrooms, $generalSubjects, $vocationalSubjects, $teachers, $rooms);
        $this->command->info('Created weekly schedules for all classrooms.');
    }

    private function truncateTables(array $tables): void
    {
        foreach ($tables as $table) {
            DB::statement("TRUNCATE TABLE {$table} CASCADE");
        }
    }

    private function createStudentPositions(): array
    {
        $positionNames = [
            'KM',
            'Wakil KM',
            'Sekretaris',
            'Wakil Sekretaris',
            'Bendahara',
            'Wakil Bendahara',
            'Seksi Kebersihan',
            'Seksi Keamanan',
            'Seksi Pendidikan',
            'Seksi Humas',
            'Anggota',
        ];

        $map = [];
        foreach ($positionNames as $name) {
            $map[$name] = StudentPosition::firstOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
            ]);
        }

        return $map;
    }

    private function createRooms(): array
    {
        return [
            Room::firstOrCreate(['name' => 'Ruang Kelas 1'], ['building' => 'Gedung Utama', 'capacity' => 36]),
            Room::firstOrCreate(['name' => 'Ruang Kelas 2'], ['building' => 'Gedung Utama', 'capacity' => 36]),
            Room::firstOrCreate(['name' => 'Ruang Kelas 3'], ['building' => 'Gedung Utama', 'capacity' => 36]),
            Room::firstOrCreate(['name' => 'Lab Komputer 1'], ['building' => 'Gedung Lab', 'capacity' => 30]),
            Room::firstOrCreate(['name' => 'Lab Komputer 2'], ['building' => 'Gedung Lab', 'capacity' => 30]),
            Room::firstOrCreate(['name' => 'Lab Multimedia'], ['building' => 'Gedung Lab', 'capacity' => 28]),
            Room::firstOrCreate(['name' => 'Workshop'], ['building' => 'Gedung Praktik', 'capacity' => 25]),
            Room::firstOrCreate(['name' => 'Lab Jaringan'], ['building' => 'Gedung Lab', 'capacity' => 28]),
        ];
    }

    private function createGeneralSubjects(): array
    {
        $subjects = [
            ['code' => 'MTK', 'name' => 'Matematika'],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia'],
            ['code' => 'BING', 'name' => 'Bahasa Inggris'],
            ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam'],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani & Olahraga'],
        ];

        $map = [];
        foreach ($subjects as $subject) {
            $map[$subject['code']] = Subject::firstOrCreate([
                'code' => $subject['code'],
            ], [
                'name' => $subject['name'],
                'type' => 'general',
            ]);
        }

        return $map;
    }

    private function createVocationalSubjects(): array
    {
        $subjects = [
            'PPL' => [
                ['PPL-LRV', 'Laravel & Backend Dev'],
                ['PPL-MOB', 'Mobile App Development'],
                ['PPL-API', 'REST API & Database'],
            ],
            'TLG' => [
                ['TLG-TSR', 'Teknik Telekomunikasi'],
                ['TLG-FBR', 'Instalasi Fiber Optik'],
                ['TLG-RDO', 'Teknik Radio'],
            ],
            'TKF' => [
                ['TKF-ELK', 'Elektronika Dasar'],
                ['TKF-MKT', 'Mekatronika'],
                ['TKF-PLC', 'Programmable Logic Controller'],
            ],
            'TJK' => [
                ['TJK-MKT', 'Administrasi Mikrotik'],
                ['TJK-CSC', 'Cisco Networking'],
                ['TJK-SRV', 'Server Administration'],
            ],
            'DKV' => [
                ['DKV-DSN', 'Desain Grafis'],
                ['DKV-ILS', 'Ilustrasi Digital'],
                ['DKV-VDO', 'Videografi & Editing'],
            ],
            'AKL' => [
                ['AKL-AKD', 'Akuntansi Dasar'],
                ['AKL-SPR', 'Spreadsheet Akuntansi'],
                ['AKL-PAJ', 'Perpajakan'],
            ],
            'MPL' => [
                ['MPL-MNG', 'Manajemen Pemasaran'],
                ['MPL-DGT', 'Pemasaran Digital'],
                ['MPL-SLS', 'Teknik Penjualan'],
            ],
            'PM' => [
                ['PM-PKR', 'Pemasaran Kreatif'],
                ['PM-EKM', 'E-Commerce'],
                ['PM-ADV', 'Periklanan Digital'],
            ],
            'TLM' => [
                ['TLM-KIM', 'Kimia Analitik'],
                ['TLM-INS', 'Instrumentasi Laboratorium'],
                ['TLM-KLT', 'Pengendalian Kualitas'],
            ],
            'TGB' => [
                ['TGB-SMR', 'Software Engineering'],
                ['TGB-UIX', 'UI/UX Design'],
                ['TGB-DBA', 'Database Administration'],
            ],
        ];

        $map = [];
        foreach ($subjects as $major => $subjectList) {
            $map[$major] = [];
            foreach ($subjectList as $item) {
                $map[$major][] = Subject::firstOrCreate([
                    'code' => $item[0],
                ], [
                    'name' => $item[1],
                    'type' => 'vocational',
                ]);
            }
        }

        return $map;
    }

    private function createTeachers(string $schoolId): array
    {
        $teacherData = [
            ['name' => 'Budi Santoso', 'nip' => '198503152010011001'],
            ['name' => 'Siti Aminah', 'nip' => '198712202012042002'],
            ['name' => 'Dedi Kurniawan', 'nip' => '199001102015031003'],
            ['name' => 'Rina Wati', 'nip' => '198808252013012004'],
            ['name' => 'Ahmad Fauzi', 'nip' => '199205052018011005'],
            ['name' => 'Dewi Rahayu', 'nip' => '198604112011042003'],
            ['name' => 'Hendra Wijaya', 'nip' => '198901012016031006'],
            ['name' => 'Maya Sari', 'nip' => '199103252019012007'],
            ['name' => 'Rizki Pratama', 'nip' => '199407082020011008'],
            ['name' => 'Tika Handayani', 'nip' => '198806152014012005'],
            ['name' => 'Arief Rahman', 'nip' => '199002202017031009'],
            ['name' => 'Nadia Putri', 'nip' => '199206302021012010'],
            ['name' => 'Yusuf Maulana', 'nip' => '198811102015031011'],
            ['name' => 'Eka Fitriani', 'nip' => '198705012012042012'],
            ['name' => 'Bayu Nugroho', 'nip' => '199304182019011013'],
            ['name' => 'Ratna Dewi', 'nip' => '198903272013012014'],
            ['name' => 'Galih Prasetyo', 'nip' => '199108112018031015'],
            ['name' => 'Intan Permata', 'nip' => '199412052022012016'],
            ['name' => 'Faisal Hakim', 'nip' => '198701032014031017'],
            ['name' => 'Winda Kusuma', 'nip' => '199509162023012018'],
            ['name' => 'Teguh Santoso', 'nip' => '199210122017031019'],
            ['name' => 'Linda Safitri', 'nip' => '198804162011042020'],
            ['name' => 'Ogi Permana', 'nip' => '199506082021031021'],
            ['name' => 'Sri Wahyuni', 'nip' => '198612272010042022'],
            ['name' => 'Fajar Hidayat', 'nip' => '199301202019011023'],
            ['name' => 'Ayu Lestari', 'nip' => '199007142015012024'],
            ['name' => 'Irfan Setiawan', 'nip' => '198803262013031025'],
            ['name' => 'Putri Anjani', 'nip' => '199411052022012026'],
            ['name' => 'Rendra Gunawan', 'nip' => '199205172018031027'],
            ['name' => 'Hasna Suryani', 'nip' => '198907082014012028'],
        ];

        $teachers = [];
        foreach ($teacherData as $teacher) {
            $email = 'guru'.$teacher['nip'].'@smkn1garut.sch.id';
            $username = 'guru'.Str::slug(strtolower($teacher['name']));

            $account = Account::firstOrCreate([
                'email' => $email,
            ], [
                'uuid' => (string) Str::uuid(),
                'nomor_participant' => 'G'.rand(10000000, 99999999),
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $teachers[] = Teacher::create([
                'account_id' => $account->uuid,
                'school_id' => $schoolId,
                'nip' => $teacher['nip'],
                'name' => $teacher['name'].' , S.Pd.',
            ]);
        }

        return $teachers;
    }

    private function createClassrooms(string $schoolId, array $teachers): array
    {
        $departments = ['PPL', 'TLG', 'TKF', 'TJK', 'DKV', 'AKL', 'MPL', 'PM', 'TLM', 'TGB'];
        $levels = ['10', '11', '12'];
        $groups = ['1', '2'];

        $classrooms = [];
        $teacherIndex = 0;

        foreach ($departments as $department) {
            foreach ($levels as $level) {
                foreach ($groups as $group) {
                    $name = "$level $department $group";
                    $classrooms[] = Classroom::create([
                        'school_id' => $schoolId,
                        'name' => $name,
                        'slug' => Str::slug($name),
                        'level' => $level,
                        'major' => $department,
                        'group_number' => $group,
                        'teacher_id' => $teachers[$teacherIndex % count($teachers)]->id,
                        'academic_year' => '2025/2026',
                    ]);

                    $teacherIndex++;
                }
            }
        }

        return $classrooms;
    }

    private function createStudentsPerClass(array $classrooms, string $schoolId, string $generationId, array $positionMap): void
    {
        $studentCounter = 1;

        foreach ($classrooms as $classroom) {
            $studentCount = rand(25, 30);
            $syncData = [];

            for ($index = 0; $index < $studentCount; $index++) {
                $fakeStudent = Student::factory()->make([
                    'school_id' => $schoolId,
                    'generation_id' => $generationId,
                ]);

                $studentNumber = '2025'.str_pad((string) $studentCounter, 4, '0', STR_PAD_LEFT);
                $email = 'siswa'.$studentNumber.'@smkn1garut.sch.id';
                $username = 'siswa'.$studentNumber;

                $account = Account::firstOrCreate([
                    'email' => $email,
                ], [
                    'uuid' => (string) Str::uuid(),
                    'nomor_participant' => 'S'.rand(10000000, 99999999),
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);

                $student = Student::create([
                    'account_id' => $account->uuid,
                    'school_id' => $schoolId,
                    'generation_id' => $generationId,
                    'name' => $fakeStudent->name,
                    'student_number' => $studentNumber,
                    'national_id' => '003'.str_pad((string) ($studentCounter + 10000), 7, '0', STR_PAD_LEFT),
                    'gender' => $fakeStudent->gender,
                ]);

                $positionName = match (true) {
                    $index === 0 => 'KM',
                    $index === 1 => 'Wakil KM',
                    $index === 2 => 'Sekretaris',
                    $index === 3 => 'Bendahara',
                    $index < 6 => 'Seksi Kebersihan',
                    $index < 8 => 'Seksi Keamanan',
                    $index < 10 => 'Seksi Pendidikan',
                    $index < 12 => 'Seksi Humas',
                    default => 'Anggota',
                };

                $syncData[$student->id] = [
                    'student_position_id' => $positionMap[$positionName]->id,
                ];

                $studentCounter++;
            }

            $classroom->students()->syncWithoutDetaching($syncData);
        }
    }

    private function mapAccountsForTeachers(): void
    {
        $teachers = Teacher::whereNull('account_id')->get();

        foreach ($teachers as $teacher) {
            $email = 'guru'.$teacher->nip.'@smkn1garut.sch.id';
            $username = 'guru.'.Str::slug(strtolower($teacher->name));

            $account = Account::firstOrCreate([
                'email' => $email,
            ], [
                'uuid' => (string) Str::uuid(),
                'nomor_participant' => 'G'.rand(10000000, 99999999),
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $teacher->update(['account_id' => $account->uuid]);
        }
    }

    private function mapAccountsForStudents(): void
    {
        $students = Student::whereNull('account_id')->get();

        foreach ($students as $student) {
            $email = 'siswa'.$student->student_number.'@smkn1garut.sch.id';
            $username = 'siswa'.$student->student_number;

            $account = Account::firstOrCreate([
                'email' => $email,
            ], [
                'uuid' => (string) Str::uuid(),
                'nomor_participant' => 'S'.rand(10000000, 99999999),
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $student->update(['account_id' => $account->uuid]);
        }
    }

    private function createSchedules(array $classrooms, array $generalSubjects, array $vocationalSubjects, array $teachers, array $rooms): void
    {
        $generalSlots = [
            ['day' => 'Senin', 'start' => '07:00', 'end' => '08:30', 'subject' => 'MTK'],
            ['day' => 'Senin', 'start' => '08:45', 'end' => '10:15', 'subject' => 'BIND'],
            ['day' => 'Selasa', 'start' => '07:00', 'end' => '08:30', 'subject' => 'BING'],
            ['day' => 'Selasa', 'start' => '08:45', 'end' => '10:15', 'subject' => 'PAI'],
            ['day' => 'Jumat', 'start' => '07:00', 'end' => '08:30', 'subject' => 'PJOK'],
        ];

        $generalTeacherMap = [
            'MTK' => 0,
            'BIND' => 1,
            'BING' => 2,
            'PAI' => 3,
            'PJOK' => 4,
        ];

        $vocationalSlots = [
            ['day' => 'Rabu', 'start' => '07:00', 'end' => '08:30', 'index' => 0],
            ['day' => 'Rabu', 'start' => '08:45', 'end' => '10:15', 'index' => 1],
            ['day' => 'Kamis', 'start' => '07:00', 'end' => '08:30', 'index' => 2],
            ['day' => 'Kamis', 'start' => '08:45', 'end' => '10:15', 'index' => 0],
            ['day' => 'Jumat', 'start' => '08:45', 'end' => '10:15', 'index' => 1],
        ];

        foreach ($classrooms as $classroomIndex => $classroom) {
            $roomCount = count($rooms);
            $roomPointer = $classroomIndex % $roomCount;
            $dept = $classroom->major;
            $subjects = $vocationalSubjects[$dept] ?? $vocationalSubjects['PPL'];
            $teacherOffset = $classroomIndex % count($teachers);

            foreach ($generalSlots as $slot) {
                $subjectCode = $slot['subject'];
                $teacherIndex = ($teacherOffset + $generalTeacherMap[$subjectCode]) % count($teachers);

                Schedule::create([
                    'classroom_id' => $classroom->id,
                    'teacher_id' => $teachers[$teacherIndex]->id,
                    'subject_id' => $generalSubjects[$subjectCode]->id,
                    'room_id' => $rooms[$roomPointer]->id,
                    'day' => $slot['day'],
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                ]);
                $roomPointer = ($roomPointer + 1) % $roomCount;
            }

            foreach ($vocationalSlots as $slot) {
                $subject = $subjects[$slot['index'] % count($subjects)];
                $teacherIndex = ($teacherOffset + 5 + $slot['index']) % count($teachers);

                Schedule::create([
                    'classroom_id' => $classroom->id,
                    'teacher_id' => $teachers[$teacherIndex]->id,
                    'subject_id' => $subject->id,
                    'room_id' => $rooms[$roomPointer]->id,
                    'day' => $slot['day'],
                    'start_time' => $slot['start'],
                    'end_time' => $slot['end'],
                ]);
                $roomPointer = ($roomPointer + 1) % $roomCount;
            }
        }
    }
}
