<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ParticipantAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::factory()->create([
            'email' => 'participant@test.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }

    #[Test]
    public function can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'participant@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email', 'username', 'email_verified_at'],
            ]);
    }

    #[Test]
    public function login_fails_with_wrong_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'participant@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    #[Test]
    public function login_fails_with_nonexistent_email(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nobody@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    #[Test]
    public function login_fails_for_disabled_account(): void
    {
        $this->account->update(['is_active' => false]);

        $response = $this->postJson('/api/login', [
            'email' => 'participant@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson(['message' => 'Account is disabled']);
    }

    #[Test]
    public function login_validates_email_required(): void
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[Test]
    public function login_enforces_single_session(): void
    {
        // First login
        $response1 = $this->postJson('/api/login', [
            'email' => 'participant@test.com',
            'password' => 'password123',
        ]);
        $token1 = $response1->json('token');

        // Second login — should invalidate first token
        $response2 = $this->postJson('/api/login', [
            'email' => 'participant@test.com',
            'password' => 'password123',
        ]);
        $token2 = $response2->json('token');

        $this->assertNotEquals($token1, $token2);

        // First token should no longer work
        $this->getJson('/api/me', ['Authorization' => "Bearer $token1"])
            ->assertStatus(401);

        // Second token should work
        $this->getJson('/api/me', ['Authorization' => "Bearer $token2"])
            ->assertStatus(200);
    }

    #[Test]
    public function can_get_authenticated_profile(): void
    {
        $response = $this->actingAs($this->account, 'sanctum')
            ->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => 'participant@test.com']);
    }

    #[Test]
    public function me_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    #[Test]
    public function can_update_profile(): void
    {
        $response = $this->actingAs($this->account, 'sanctum')
            ->patchJson('/api/me', [
                'username' => 'newusername',
                'name' => 'New Name',
                'email' => 'newemail@test.com',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('accounts', [
            'uuid' => $this->account->uuid,
            'username' => 'newusername',
        ]);
    }

    #[Test]
    public function update_profile_validates_unique_email(): void
    {
        $other = Account::factory()->create(['email' => 'taken@test.com']);

        $response = $this->actingAs($this->account, 'sanctum')
            ->patchJson('/api/me', [
                'username' => 'newuser',
                'name' => 'Name',
                'email' => 'taken@test.com',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    #[Test]
    public function can_logout(): void
    {
        $token = $this->account->createToken('default')->plainTextToken;

        // Verify token exists
        $this->assertCount(1, $this->account->tokens);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out']);

        // Verify token was deleted from database
        $this->assertCount(0, $this->account->fresh()->tokens);
    }

    #[Test]
    public function can_check_username_availability(): void
    {
        $response = $this->actingAs($this->account, 'sanctum')
            ->getJson('/api/username/check?username=totally_unique_name');

        $response->assertStatus(200)
            ->assertJson(['available' => true]);
    }

    #[Test]
    public function username_check_returns_unavailable_for_taken(): void
    {
        $other = Account::factory()->create(['username' => 'taken_name']);

        $response = $this->actingAs($this->account, 'sanctum')
            ->getJson('/api/username/check?username=taken_name');

        $response->assertStatus(200)
            ->assertJson(['available' => false]);
    }

    #[Test]
    public function username_check_allows_own_username(): void
    {
        // The check uses Participant::where('id', '!=', $user->id)
        // Account's PK is 'uuid', so $user->id maps to uuid. This means
        // checking own username should show available.
        $ownUsername = $this->account->username;

        $response = $this->actingAs($this->account, 'sanctum')
            ->getJson('/api/username/check?username='.urlencode($ownUsername));

        $response->assertStatus(200);
        // Since Account PK is uuid and check uses ->id, the exclusion works
        $this->assertTrue($response->json('available'));
    }

    #[Test]
    public function send_verification_email_for_unverified_user(): void
    {
        // Create a fresh unverified account
        $unverified = Account::factory()->create([
            'email' => 'unverified@test.com',
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($unverified, 'sanctum')
            ->postJson('/api/email/verification-notification');

        $response->assertStatus(200)
            ->assertJson(['status' => 'verification-link-sent']);
    }

    #[Test]
    public function send_verification_email_returns_already_verified(): void
    {
        $response = $this->actingAs($this->account, 'sanctum')
            ->postJson('/api/email/verification-notification');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Already verified']);
    }
}
