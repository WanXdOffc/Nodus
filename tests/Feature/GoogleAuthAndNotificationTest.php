<?php

namespace Tests\Feature;

use App\Mail\ItemMatchFoundMail;
use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GoogleAuthAndNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_config_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/auth/google/config');

        $response->assertStatus(200)
            ->assertJsonStructure(['configured', 'client_id']);
    }

    public function test_google_auth_fails_without_tokens(): void
    {
        $response = $this->postJson('/api/auth/google', []);

        $response->assertStatus(422);
    }

    public function test_google_auth_fails_when_google_token_is_invalid(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response(['error' => 'invalid_token'], 400),
        ]);

        $response = $this->postJson('/api/auth/google', [
            'id_token' => 'invalid_fake_token',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Verifikasi akun Google gagal. Token tidak sah atau sudah kedaluwarsa.']);
    }

    public function test_google_auth_registers_new_user_with_valid_token(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'email' => 'google.tester@gmail.com',
                'name' => 'Tester Google',
                'sub' => '12345678901234567890',
            ], 200),
        ]);

        $response = $this->postJson('/api/auth/google', [
            'credential' => 'valid_mock_jwt_token',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'access_token',
                'user' => ['id', 'email', 'name', 'trust_score'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'google.tester@gmail.com',
            'name' => 'Tester Google',
            'trust_score' => 5.0,
        ]);
    }

    public function test_google_auth_logs_in_existing_user(): void
    {
        $existing = User::factory()->create([
            'email' => 'existing.google@gmail.com',
            'name' => 'Existing User',
        ]);

        Http::fake([
            'https://www.googleapis.com/oauth2/v3/userinfo*' => Http::response([
                'email' => 'existing.google@gmail.com',
                'name' => 'Existing User',
                'sub' => '987654321',
            ], 200),
        ]);

        $response = $this->postJson('/api/auth/google', [
            'access_token' => 'valid_access_token_123',
        ]);

        $response->assertStatus(200);
        $this->assertEquals($existing->id, $response->json('user.id'));
    }

    public function test_test_email_artisan_command_sends_email(): void
    {
        Mail::fake();

        $this->artisan('nodus:test-email test@nodus.com')
            ->assertSuccessful();

        Mail::assertSent(ItemMatchFoundMail::class, function ($mail) {
            return $mail->hasTo('test@nodus.com');
        });
    }

    public function test_item_match_mailable_renders_properly(): void
    {
        $seeker = User::factory()->create(['name' => 'Putu Seeker', 'email' => 'seeker@example.com']);
        $finder = User::factory()->create(['name' => 'Made Finder', 'email' => 'finder@example.com']);

        $lostItem = Item::create([
            'user_id' => $seeker->id,
            'title' => 'Kunci Motor Vario Hitam',
            'description' => 'Kunci motor vario hilang di kampus',
            'category' => 'Kunci & Kendaraan',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Kampus Undiksha',
            'item_date' => now()->format('Y-m-d'),
        ]);

        $foundItem = Item::create([
            'user_id' => $finder->id,
            'title' => 'Gantungan Kunci Vario',
            'description' => 'Ditemukan di dekat parkiran',
            'category' => 'Kunci & Kendaraan',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Kampus Undiksha',
            'item_date' => now()->format('Y-m-d'),
        ]);

        $ticket = ClaimTicket::create([
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $foundItem->id,
            'ai_match_score' => 95,
            'status' => 'open',
            'admin_status' => 'approved',
        ]);

        $mailable = new ItemMatchFoundMail($seeker, $ticket, 'seeker');

        $mailable->assertHasSubject('[NODUS] Kabar Baik! Kecocokan Barang Ditemukan: Kunci Motor Vario Hitam');
        $mailable->assertSeeInHtml('Halo, Putu Seeker!');
        $mailable->assertSeeInHtml('Kunci Motor Vario Hitam');
        $mailable->assertSeeInHtml('95% Sangat Cocok');
    }
}
