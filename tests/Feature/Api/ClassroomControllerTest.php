<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClassroomControllerTest extends TestCase
{
    use RefreshDatabase;

    private Account $admin;

    private Teacher $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin super_admin account
        $this->admin = Account::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'super_admin',
        ]);

        // Create a teacher
        $this->teacher = Teacher::factory()->create();
    }

    #[Test]
    public function can_list_classrooms(): void
    {
        Classroom::factory()->count(3)->create(['teacher_id' => $this->teacher->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/lms/classrooms');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'tingkat',
                        'jurusan',
                        'teacher',
                    ],
                ],
                'total',
            ]);
    }

    #[Test]
    public function can_create_classroom(): void
    {
        $data = [
            'name' => 'Kelas 12 IPA 1',
            'tingkat' => '12',
            'jurusan' => 'IPA',
            'rombel' => '1',
            'teacher_id' => $this->teacher->id,
            'academic_year' => '2025/2026',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/lms/classrooms', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'tingkat',
                    'jurusan',
                    'teacher',
                ],
            ]);

        $this->assertDatabaseHas('classrooms', [
            'name' => 'Kelas 12 IPA 1',
        ]);
    }

    #[Test]
    public function can_get_single_classroom(): void
    {
        $classroom = Classroom::factory()->create(['teacher_id' => $this->teacher->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/lms/classrooms/{$classroom->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'tingkat',
                    'jurusan',
                    'teacher',
                    'students',
                    'schedules',
                ],
            ]);
    }

    #[Test]
    public function can_update_classroom(): void
    {
        $classroom = Classroom::factory()->create(['teacher_id' => $this->teacher->id]);

        $data = [
            'name' => 'Updated Classroom Name',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/lms/classrooms/{$classroom->id}", $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                ],
            ]);

        $this->assertDatabaseHas('classrooms', [
            'id' => $classroom->id,
            'name' => 'Updated Classroom Name',
        ]);
    }

    #[Test]
    public function can_delete_classroom(): void
    {
        $classroom = Classroom::factory()->create(['teacher_id' => $this->teacher->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/lms/classrooms/{$classroom->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseMissing('classrooms', [
            'id' => $classroom->id,
        ]);
    }

    #[Test]
    public function returns_404_for_non_existent_classroom(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/lms/classrooms/non-existent-id');

        $response->assertStatus(404)
            ->assertJsonStructure(['message']);
    }

    #[Test]
    public function requires_authentication(): void
    {
        $response = $this->getJson('/api/lms/classrooms');

        $response->assertStatus(401);
    }

    #[Test]
    public function requires_super_admin_role(): void
    {
        $teacher = Account::factory()->create([
            'role' => 'teacher',
        ]);

        $response = $this->actingAs($teacher, 'sanctum')
            ->getJson('/api/lms/classrooms');

        $response->assertStatus(403);
    }
}
