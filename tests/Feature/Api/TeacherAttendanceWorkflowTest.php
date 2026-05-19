<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Generation;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeacherAttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Account $teacherAccount;

    private Teacher $teacher;

    private Classroom $classroom;

    private Schedule $schedule;

    private School $school;

    private Generation $generation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::create([
            'slug' => 'smk-selaju',
            'name' => 'SMK Selaju',
        ]);

        $this->generation = Generation::create([
            'name' => '2025/2026',
            'start_years' => 2025,
            'end_years' => 2026,
            'is_active' => true,
            'is_current' => true,
        ]);

        $this->teacherAccount = Account::factory()->create();

        $this->teacher = Teacher::create([
            'account_id' => $this->teacherAccount->uuid,
            'school_id' => $this->school->id,
            'nip' => '198801012024001',
            'name' => 'Ibu Guru',
        ]);

        $this->classroom = Classroom::create([
            'name' => '12 PPLG 1',
            'tingkat' => '12',
            'jurusan' => 'PPLG',
            'rombel' => '1',
            'slug' => '12-pplg-1',
            'teacher_id' => $this->teacher->id,
            'academic_year' => '2025/2026',
        ]);

        $subject = Subject::factory()->create([
            'name' => 'Pemrograman Web',
            'code' => 'WEB1',
        ]);

        $this->schedule = Schedule::create([
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $this->teacher->id,
            'subject_id' => $subject->id,
            'day' => 'Senin',
            'start_time' => '08:00',
            'end_time' => '10:00',
        ]);
    }

    #[Test]
    public function teacher_can_view_attendance_sheet_with_existing_statuses(): void
    {
        $studentAlpha = $this->createStudent('Alpha Siswa');
        $studentBeta = $this->createStudent('Beta Siswa');

        $this->classroom->students()->attach([$studentAlpha->id, $studentBeta->id]);

        Attendance::create([
            'schedule_id' => $this->schedule->id,
            'student_id' => $studentAlpha->id,
            'date' => '2026-04-23',
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacherAccount, 'sanctum')
            ->getJson("/api/lms/teacher/schedules/{$this->schedule->id}/attendance-sheet?date=2026-04-23");

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.date', '2026-04-23')
            ->assertJsonPath('data.schedule.id', $this->schedule->id)
            ->assertJsonPath('data.summary.student_count', 2)
            ->assertJsonPath('data.summary.recorded_count', 1)
            ->assertJsonPath('data.students.0.name', 'Alpha Siswa')
            ->assertJsonPath('data.students.0.status', 'present')
            ->assertJsonPath('data.students.1.name', 'Beta Siswa')
            ->assertJsonPath('data.students.1.status', null);
    }

    #[Test]
    public function teacher_can_store_bulk_attendance_for_students_in_owned_classroom(): void
    {
        $studentAlpha = $this->createStudent('Alpha Siswa');
        $studentBeta = $this->createStudent('Beta Siswa');

        $this->classroom->students()->attach([$studentAlpha->id, $studentBeta->id]);

        $payload = [
            'schedule_id' => $this->schedule->id,
            'date' => '2026-04-23',
            'attendances' => [
                [
                    'student_id' => $studentAlpha->id,
                    'status' => 'present',
                ],
                [
                    'student_id' => $studentBeta->id,
                    'status' => 'permit',
                ],
            ],
        ];

        $response = $this->actingAs($this->teacherAccount, 'sanctum')
            ->postJson('/api/lms/teacher/attendances', $payload);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('summary.recorded_count', 2)
            ->assertJsonPath('summary.present_count', 1)
            ->assertJsonPath('summary.permit_count', 1);

        $this->assertSame(
            'present',
            Attendance::query()
                ->where('schedule_id', $this->schedule->id)
                ->where('student_id', $studentAlpha->id)
                ->whereDate('date', '2026-04-23')
                ->value('status')
        );

        $this->assertSame(
            'permit',
            Attendance::query()
                ->where('schedule_id', $this->schedule->id)
                ->where('student_id', $studentBeta->id)
                ->whereDate('date', '2026-04-23')
                ->value('status')
        );
    }

    #[Test]
    public function teacher_can_store_bulk_attendance_with_dmy_date_format(): void
    {
        $studentAlpha = $this->createStudent('Alpha Siswa');
        $studentBeta = $this->createStudent('Beta Siswa');

        $this->classroom->students()->attach([$studentAlpha->id, $studentBeta->id]);

        $payload = [
            'schedule_id' => $this->schedule->id,
            'date' => '18/05/2026',
            'attendances' => [
                [
                    'student_id' => $studentAlpha->id,
                    'status' => 'present',
                ],
                [
                    'student_id' => $studentBeta->id,
                    'status' => 'sick',
                ],
            ],
        ];

        $response = $this->actingAs($this->teacherAccount, 'sanctum')
            ->postJson('/api/lms/teacher/attendances', $payload);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('summary.recorded_count', 2)
            ->assertJsonPath('summary.present_count', 1)
            ->assertJsonPath('summary.sick_count', 1);

        $this->assertSame(
            'present',
            Attendance::query()
                ->where('schedule_id', $this->schedule->id)
                ->where('student_id', $studentAlpha->id)
                ->whereDate('date', '2026-05-18')
                ->value('status')
        );
    }

    #[Test]
    public function teacher_can_store_bulk_attendance_using_students_array(): void
    {
        $studentAlpha = $this->createStudent('Alpha Siswa');
        $studentBeta = $this->createStudent('Beta Siswa');

        $this->classroom->students()->attach([$studentAlpha->id, $studentBeta->id]);

        $payload = [
            'schedule_id' => $this->schedule->id,
            'date' => '2026-05-18',
            'students' => [
                [
                    'id' => $studentAlpha->id,
                    'status' => 'present',
                ],
                [
                    'id' => $studentBeta->id,
                    'status' => 'absent',
                ],
            ],
        ];

        $response = $this->actingAs($this->teacherAccount, 'sanctum')
            ->postJson('/api/lms/teacher/attendances', $payload);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('summary.recorded_count', 2)
            ->assertJsonPath('summary.present_count', 1)
            ->assertJsonPath('summary.absent_count', 1);

        $this->assertSame(
            'present',
            Attendance::query()
                ->where('schedule_id', $this->schedule->id)
                ->where('student_id', $studentAlpha->id)
                ->whereDate('date', '2026-05-18')
                ->value('status')
        );
    }

    #[Test]
    public function teacher_schedule_list_includes_is_attended_flag_for_today(): void
    {
        Carbon::setTestNow('2026-05-18');

        $student = $this->createStudent('Alpha Siswa');
        $this->classroom->students()->attach($student->id);

        Attendance::create([
            'schedule_id' => $this->schedule->id,
            'student_id' => $student->id,
            'date' => '2026-05-18',
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->teacherAccount, 'sanctum')
            ->getJson('/api/lms/teacher/schedules');

        Carbon::setTestNow();

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.is_attended', true);
    }

    #[Test]
    public function teacher_stats_counts_attendance_statuses_case_insensitively(): void
    {
        Carbon::setTestNow('2026-05-18');

        $student = $this->createStudent('Alpha Siswa');
        $this->classroom->students()->attach($student->id);

        Attendance::create([
            'schedule_id' => $this->schedule->id,
            'student_id' => $student->id,
            'date' => '2026-05-18',
            'status' => 'present',
        ]);

        Attendance::create([
            'schedule_id' => $this->schedule->id,
            'student_id' => $student->id,
            'date' => '2026-05-18',
            'status' => 'absent',
        ]);

        Attendance::create([
            'schedule_id' => $this->schedule->id,
            'student_id' => $student->id,
            'date' => '2026-05-18',
            'status' => 'sick',
        ]);

        Attendance::create([
            'schedule_id' => $this->schedule->id,
            'student_id' => $student->id,
            'date' => '2026-05-18',
            'status' => 'permit',
        ]);

        $response = $this->actingAs($this->teacherAccount, 'sanctum')
            ->getJson('/api/lms/teacher/stats');

        Carbon::setTestNow();

        $response->assertOk()
            ->assertJsonPath('data.attendance.present', 1)
            ->assertJsonPath('data.attendance.absent', 1)
            ->assertJsonPath('data.attendance.sick', 1)
            ->assertJsonPath('data.attendance.permit', 1)
            ->assertJsonPath('data.attendance.total', 4);
    }

    #[Test]
    public function teacher_cannot_store_attendance_for_student_outside_schedule_classroom(): void
    {
        $studentInClass = $this->createStudent('Alpha Siswa');
        $studentOutsideClass = $this->createStudent('Gamma Siswa');

        $this->classroom->students()->attach($studentInClass->id);

        $payload = [
            'schedule_id' => $this->schedule->id,
            'date' => '2026-04-23',
            'attendances' => [
                [
                    'student_id' => $studentInClass->id,
                    'status' => 'present',
                ],
                [
                    'student_id' => $studentOutsideClass->id,
                    'status' => 'absent',
                ],
            ],
        ];

        $response = $this->actingAs($this->teacherAccount, 'sanctum')
            ->postJson('/api/lms/teacher/attendances', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Terdapat siswa yang tidak terdaftar pada kelas jadwal ini.');

        $this->assertDatabaseMissing('attendances', [
            'schedule_id' => $this->schedule->id,
            'student_id' => $studentInClass->id,
            'date' => '2026-04-23',
        ]);
    }

    #[Test]
    public function teacher_cannot_access_another_teachers_schedule_sheet(): void
    {
        $anotherTeacherAccount = Account::factory()->create();
        Teacher::create([
            'account_id' => $anotherTeacherAccount->uuid,
            'school_id' => $this->school->id,
            'nip' => '198801012024002',
            'name' => 'Pak Guru Lain',
        ]);

        $response = $this->actingAs($anotherTeacherAccount, 'sanctum')
            ->getJson("/api/lms/teacher/schedules/{$this->schedule->id}/attendance-sheet?date=2026-04-23");

        $response->assertForbidden()
            ->assertJsonPath('message', 'Akses ditolak.');
    }

    private function createStudent(string $name): Student
    {
        return Student::create([
            'account_id' => null,
            'school_id' => $this->school->id,
            'generation_id' => $this->generation->id,
            'name' => $name,
            'student_number' => 'SN-'.Str::upper(Str::random(6)),
            'national_id' => str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'gender' => 'L',
        ]);
    }
}
