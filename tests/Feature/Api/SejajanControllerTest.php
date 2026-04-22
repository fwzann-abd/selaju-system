<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Sejajan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SejajanControllerTest extends TestCase
{
    use RefreshDatabase;

    private Account $owner;

    private Account $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = Account::factory()->create();
        $this->otherUser = Account::factory()->create();
    }

    #[Test]
    public function can_list_all_stores(): void
    {
        Sejajan::factory()->count(3)->create();

        $response = $this->getJson('/api/sejajans');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'description', 'is_active'],
                ],
                'path',
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function can_search_stores_by_name(): void
    {
        Sejajan::factory()->create(['name' => 'Warung Makan Enak']);
        Sejajan::factory()->create(['name' => 'Toko Buku ABC']);

        $response = $this->getJson('/api/sejajans?q=Warung');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Warung Makan Enak', $response->json('data.0.name'));
    }

    #[Test]
    public function can_show_single_store(): void
    {
        $store = Sejajan::factory()->create();

        $response = $this->getJson("/api/sejajans/{$store->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'slug', 'products'],
                'path',
            ]);
    }

    #[Test]
    public function can_create_store_when_authenticated(): void
    {
        $response = $this->actingAs($this->owner, 'sanctum')
            ->postJson('/api/sejajans', [
                'name' => 'Kantin Sehat',
                'description' => 'Menjual makanan sehat',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Kantin Sehat');

        $this->assertDatabaseHas('sejajans', [
            'name' => 'Kantin Sehat',
            'account_id' => $this->owner->uuid,
        ]);
    }

    #[Test]
    public function store_creation_requires_authentication(): void
    {
        $response = $this->postJson('/api/sejajans', [
            'name' => 'Test Store',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function store_creation_validates_name_required(): void
    {
        $response = $this->actingAs($this->owner, 'sanctum')
            ->postJson('/api/sejajans', [
                'description' => 'No name provided',
            ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function auto_generates_slug_from_name(): void
    {
        $response = $this->actingAs($this->owner, 'sanctum')
            ->postJson('/api/sejajans', [
                'name' => 'Warung Makan Segar',
            ]);

        $response->assertStatus(201);
        $this->assertEquals('warung-makan-segar', $response->json('data.slug'));
    }

    #[Test]
    public function can_update_own_store(): void
    {
        $store = Sejajan::factory()->create(['account_id' => $this->owner->uuid]);

        $response = $this->actingAs($this->owner, 'sanctum')
            ->putJson("/api/sejajans/{$store->id}", [
                'name' => 'Updated Store Name',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Store Name');
    }

    #[Test]
    public function cannot_update_others_store(): void
    {
        $store = Sejajan::factory()->create(['account_id' => $this->owner->uuid]);

        $response = $this->actingAs($this->otherUser, 'sanctum')
            ->putJson("/api/sejajans/{$store->id}", [
                'name' => 'Hijacked',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function can_delete_own_store(): void
    {
        $store = Sejajan::factory()->create(['account_id' => $this->owner->uuid]);

        $response = $this->actingAs($this->owner, 'sanctum')
            ->deleteJson("/api/sejajans/{$store->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Deleted']);

        $this->assertDatabaseMissing('sejajans', ['id' => $store->id]);
    }

    #[Test]
    public function cannot_delete_others_store(): void
    {
        $store = Sejajan::factory()->create(['account_id' => $this->owner->uuid]);

        $response = $this->actingAs($this->otherUser, 'sanctum')
            ->deleteJson("/api/sejajans/{$store->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('sejajans', ['id' => $store->id]);
    }

    #[Test]
    public function can_list_my_stores(): void
    {
        Sejajan::factory()->count(2)->create(['account_id' => $this->owner->uuid]);
        Sejajan::factory()->create(); // belongs to another user

        $response = $this->actingAs($this->owner, 'sanctum')
            ->getJson('/api/sejajans/my-stores');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data'));
    }
}
