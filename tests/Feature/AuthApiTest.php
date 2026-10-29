<?php

namespace Tests\Feature;

use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\UserReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_via_api(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'trust_score',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);
    }

    public function test_user_can_login_via_api(): void
    {
        $user = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'password' => 'secret123',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'siti@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user',
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Email atau kata sandi tidak valid.',
            ]);
    }

    public function test_authenticated_user_can_get_profile_and_logout(): void
    {
        $user = User::create([
            'name' => 'Agus Pratama',
            'email' => 'agus@example.com',
            'password' => 'password123',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        // Get profile
        $profileResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $profileResponse->assertStatus(200)
            ->assertJsonPath('user.email', 'agus@example.com');

        // Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson([
                'message' => 'Logout berhasil.',
            ]);

        // Token should be deleted from DB
        $this->assertDatabaseCount('personal_access_tokens', 0);

        // Reset auth guards cache in test runner
        $this->app['auth']->forgetGuards();

        // Attempting to access protected route after logout should be unauthorized
        $afterLogoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $afterLogoutResponse->assertStatus(401);
    }

    public function test_models_and_relationships(): void
    {
        $user1 = User::create([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $user2 = User::create([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 4.8,
        ]);

        // Lost Item
        $lostItem = Item::create([
            'user_id' => $user1->id,
            'type' => 'lost',
            'title' => 'Dompet Kulit Hitam',
            'description' => 'Berisi KTP dan kartu mahasiswa',
            'category' => 'Dompet',
            'latitude' => -6.20880000,
            'longitude' => 106.84560000,
            'location_name' => 'Gedung Perpustakaan Lantai 2',
            'item_date' => '2026-10-28',
            'status' => 'searching',
            'image_path' => 'items/wallet.jpg',
            'ai_tags' => ['dompet', 'kulit', 'hitam'],
        ]);

        // Found Item
        $foundItem = Item::create([
            'user_id' => $user2->id,
            'type' => 'found',
            'title' => 'Dompet Hitam Ditemukan',
            'description' => 'Ditemukan di dekat rak buku',
            'category' => 'Dompet',
            'latitude' => -6.20885000,
            'longitude' => 106.84565000,
            'location_name' => 'Perpustakaan Utama',
            'item_date' => '2026-10-28',
            'status' => 'searching',
            'image_path' => null,
            'ai_tags' => ['dompet', 'hitam'],
        ]);

        // Claim Ticket
        $ticket = ClaimTicket::create([
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $foundItem->id,
            'ai_match_score' => 92,
            'status' => 'open',
        ]);

        // Ticket Message
        $msg = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user1->id,
            'message' => 'Halo, apakah di dalam dompet ada kartu tanda mahasiswa atas nama Budi?',
        ]);

        // User Report
        $report = UserReport::create([
            'reporter_id' => $user1->id,
            'reported_user_id' => $user2->id,
            'reason' => 'Pengguna tidak merespons klaim',
            'status' => 'pending',
        ]);

        // Assert relationships
        $this->assertEquals($user1->id, $lostItem->user->id);
        $this->assertCount(1, $user1->items);
        $this->assertEquals($lostItem->id, $ticket->lostItem->id);
        $this->assertEquals($foundItem->id, $ticket->foundItem->id);
        $this->assertCount(1, $ticket->messages);
        $this->assertEquals($user1->id, $msg->user->id);
        $this->assertEquals($ticket->id, $msg->ticket->id);
        $this->assertEquals($user1->id, $report->reporter->id);
        $this->assertEquals($user2->id, $report->reportedUser->id);
        $this->assertCount(1, $user1->filedReports);
        $this->assertCount(1, $user2->receivedReports);
    }
}
