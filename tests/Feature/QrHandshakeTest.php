<?php

namespace Tests\Feature;

use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class QrHandshakeTest extends TestCase
{
    use RefreshDatabase;

    private function createTicketWithUsers(string $adminStatus = 'approved', string $status = 'open'): array
    {
        $reporter = User::factory()->create([
            'name' => 'Budi Pelapor',
            'trust_score' => 5.0,
        ]);

        $finder = User::factory()->create([
            'name' => 'Siti Penemu',
            'trust_score' => 6.0,
        ]);

        $lostItem = Item::create([
            'user_id' => $reporter->id,
            'title' => 'Dompet Kulit Hitam',
            'description' => 'Dompet hilang di perpustakaan',
            'category' => 'Dompet & Tas',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'location_name' => 'Perpustakaan Pusat',
            'item_date' => now()->subDay(),
        ]);

        $foundItem = Item::create([
            'user_id' => $finder->id,
            'title' => 'Dompet Kulit Coklat Kehitaman',
            'description' => 'Ditemukan dompet di meja perpustakaan',
            'category' => 'Dompet & Tas',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'location_name' => 'Perpustakaan Pusat',
            'item_date' => now()->subDay(),
        ]);

        $ticket = ClaimTicket::create([
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $foundItem->id,
            'ai_match_score' => 90,
            'status' => $status,
            'admin_status' => $adminStatus,
            'qr_token' => $adminStatus === 'approved' ? (string) Str::uuid() : null,
        ]);

        return [$ticket, $reporter, $finder, $lostItem, $foundItem];
    }

    public function test_qr_token_auto_generated_when_ticket_approved(): void
    {
        [$ticket] = $this->createTicketWithUsers('pending');
        $this->assertNull($ticket->qr_token);

        $ticket->update(['admin_status' => 'approved']);

        $this->assertNotNull($ticket->fresh()->qr_token);
        $this->assertNotEmpty($ticket->fresh()->qr_token);
    }

    public function test_verify_qr_requires_authentication(): void
    {
        [$ticket] = $this->createTicketWithUsers();

        $response = $this->postJson("/api/claim-tickets/{$ticket->id}/verify-qr", [
            'token' => $ticket->qr_token,
        ]);

        $response->assertStatus(401);
    }

    public function test_verify_qr_rejects_unauthorized_user(): void
    {
        [$ticket] = $this->createTicketWithUsers();
        $stranger = User::factory()->create();

        $response = $this->actingAs($stranger, 'sanctum')->postJson("/api/claim-tickets/{$ticket->id}/verify-qr", [
            'token' => $ticket->qr_token,
        ]);

        $response->assertStatus(403);
    }

    public function test_verify_qr_rejects_invalid_token(): void
    {
        [$ticket, $reporter, $finder] = $this->createTicketWithUsers();

        $response = $this->actingAs($finder, 'sanctum')->postJson("/api/claim-tickets/{$ticket->id}/verify-qr", [
            'token' => 'invalid-token-12345',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Kode QR tidak cocok atau tidak valid. Silakan scan ulang kode QR dari pelapor.',
            ]);

        $this->assertEquals('open', $ticket->fresh()->status);
    }

    public function test_verify_qr_resolves_ticket_and_rewards_trust_score(): void
    {
        [$ticket, $reporter, $finder, $lostItem, $foundItem] = $this->createTicketWithUsers();

        $response = $this->actingAs($finder, 'sanctum')->postJson("/api/claim-tickets/{$ticket->id}/verify-qr", [
            'token' => $ticket->qr_token,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Verifikasi serah terima berhasil! Barang telah diserahkan dan Trust Score meningkat.',
            ]);

        // Verifikasi status tiket
        $this->assertEquals('verified', $ticket->fresh()->status);

        // Verifikasi status kedua barang
        $this->assertEquals('resolved', $lostItem->fresh()->status);
        $this->assertEquals('resolved', $foundItem->fresh()->status);

        // Verifikasi kenaikan trust score masing-masing +0.5
        $this->assertEquals(5.5, (float) $reporter->fresh()->trust_score);
        $this->assertEquals(6.5, (float) $finder->fresh()->trust_score);
    }

    public function test_verify_qr_cannot_resolve_already_verified_ticket(): void
    {
        [$ticket, $reporter, $finder] = $this->createTicketWithUsers('approved', 'verified');

        $response = $this->actingAs($finder, 'sanctum')->postJson("/api/claim-tickets/{$ticket->id}/verify-qr", [
            'token' => $ticket->qr_token,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Tiket klaim ini sudah diselesaikan atau ditutup sebelumnya.',
            ]);
    }
}
