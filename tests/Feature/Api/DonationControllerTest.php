<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DonationControllerTest extends TestCase
{
    use RefreshDatabase;

    private Account $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Account::factory()->create();
    }

    #[Test]
    public function can_list_paid_donations_leaderboard(): void
    {
        Donation::factory()->paid()->count(3)->create();
        Donation::factory()->create(['payment_status' => 'pending']);

        $response = $this->getJson('/api/donations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'donations' => [
                        '*' => ['id', 'donor_name', 'amount', 'message', 'paid_at'],
                    ],
                    'statistics' => ['total_amount', 'total_donors'],
                ],
            ]);

        // Only paid donations in the leaderboard
        $this->assertCount(3, $response->json('data.donations'));
        $this->assertEquals(3, $response->json('data.statistics.total_donors'));
    }

    #[Test]
    public function leaderboard_respects_limit(): void
    {
        Donation::factory()->paid()->count(5)->create();

        $response = $this->getJson('/api/donations?limit=2');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.donations'));
    }

    #[Test]
    public function can_show_single_donation(): void
    {
        $donation = Donation::factory()->create();

        $response = $this->getJson("/api/donations/{$donation->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $donation->id);
    }

    #[Test]
    public function show_returns_404_for_nonexistent(): void
    {
        $fakeId = '00000000-0000-0000-0000-000000000000';

        $response = $this->getJson("/api/donations/{$fakeId}");

        $response->assertStatus(404);
    }

    #[Test]
    public function can_get_available_banks(): void
    {
        $response = $this->getJson('/api/donations/banks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['code', 'name', 'available'],
                ],
            ]);
    }

    #[Test]
    public function donation_history_requires_authentication(): void
    {
        $response = $this->getJson('/api/donations/history');

        $response->assertStatus(401);
    }

    #[Test]
    public function can_get_donation_history(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/donations/history');

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }

    #[Test]
    public function creating_donation_requires_auth(): void
    {
        $response = $this->postJson('/api/donations', [
            'donor_name' => 'Test',
            'amount' => 50000,
            'payment_method' => 'va',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function manual_transfer_requires_auth(): void
    {
        $response = $this->postJson('/api/donations/manual-transfer', [
            'donor_name' => 'Test',
            'amount' => 50000,
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function payment_callback_accepts_post(): void
    {
        $response = $this->postJson('/api/payment/callback', [
            'order' => ['invoice_number' => 'TEST-001'],
            'transaction' => ['status' => 'SUCCESS'],
        ]);

        // Should not be 405 (Method Not Allowed) — the endpoint exists
        $this->assertNotEquals(405, $response->status());
    }
}
