<?php

namespace Tests\Feature\Api;

use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClassroomControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $nonAdmin;

    private Teacher $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Create super_admin user group and admin user
        $superAdminGroup = UserGroup::factory()->superAdmin()->create();
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'user_group_id' => $superAdminGroup->id,
        ]);

        // Create a non-admin user (no userGroup → role resolves to null → 403)
        $this->nonAdmin = User::factory()->create([
            'email' => 'nonadmin@test.com',
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
            'name' => 'Kelas 12 AKL 1',
            'tingkat' => 'XII',
            'jurusan' => 'AKL',
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
            'name' => 'Kelas 12 AKL 1',
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
            'tingkat' => 'XI',
            'jurusan' => 'MPL',
            'academic_year' => '2025/2026',
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
        $response = $this->actingAs($this->nonAdmin, 'sanctum')
            ->getJson('/api/lms/classrooms');

        $response->assertStatus(403);
    }
}
