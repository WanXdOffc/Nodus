<?php

namespace Tests\Feature;

use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationObfuscationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_items_coordinates_are_obfuscated_with_noise(): void
    {
        $user = User::factory()->create();

        $exactLat = -8.112000;
        $exactLng = 115.088000;

        $item = Item::create([
            'user_id' => $user->id,
            'title' => 'Tas Ransel Eiger',
            'description' => 'Tertinggal di dekat perpustakaan',
            'category' => 'Dompet & Tas',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => $exactLat,
            'longitude' => $exactLng,
            'location_name' => 'Perpustakaan Pusat',
            'item_date' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/items');

        $response->assertStatus(200);

        $returnedItem = $response->json('data.0');
        $returnedLat = (float) $returnedItem['latitude'];
        $returnedLng = (float) $returnedItem['longitude'];

        // Koordinat publik TIDAK BOLEH persis sama dengan koordinat asli di database
        $this->assertNotEquals($exactLat, $returnedLat, 'Latitude publik tidak boleh sama persis dengan database asli.');
        $this->assertNotEquals($exactLng, $returnedLng, 'Longitude publik tidak boleh sama persis dengan database asli.');

        // Jarak perbedaan noise harus berada di rentang wajar (+/- 0.003 derajat)
        $diffLat = abs($returnedLat - $exactLat);
        $diffLng = abs($returnedLng - $exactLng);

        $this->assertLessThanOrEqual(0.003, $diffLat, 'Perbedaan latitude harus dalam rentang noise yang ditentukan.');
        $this->assertLessThanOrEqual(0.003, $diffLng, 'Perbedaan longitude harus dalam rentang noise yang ditentukan.');

        // Pastikan nilai di database tetap asli dan tidak berubah
        $freshItem = $item->fresh();
        $this->assertEquals($exactLat, (float) $freshItem->latitude);
        $this->assertEquals($exactLng, (float) $freshItem->longitude);
    }

    public function test_claim_ticket_detail_returns_exact_unobfuscated_coordinates(): void
    {
        $reporter = User::factory()->create();
        $finder = User::factory()->create();

        $exactLostLat = -8.113500;
        $exactLostLng = 115.089200;
        $exactFoundLat = -8.113400;
        $exactFoundLng = 115.089100;

        $lostItem = Item::create([
            'user_id' => $reporter->id,
            'title' => 'Dompet Kulit',
            'description' => 'Hilang di kantin',
            'category' => 'Dompet & Tas',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => $exactLostLat,
            'longitude' => $exactLostLng,
            'location_name' => 'Kantin Kampus',
            'item_date' => now()->subDay(),
        ]);

        $foundItem = Item::create([
            'user_id' => $finder->id,
            'title' => 'Dompet Cokelat',
            'description' => 'Ditemukan di bangku kantin',
            'category' => 'Dompet & Tas',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => $exactFoundLat,
            'longitude' => $exactFoundLng,
            'location_name' => 'Kantin Kampus',
            'item_date' => now()->subDay(),
        ]);

        $ticket = ClaimTicket::create([
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $foundItem->id,
            'ai_match_score' => 92,
            'status' => 'open',
            'admin_status' => 'approved',
            'qr_token' => 'test-token-uuid',
        ]);

        // Panggil endpoint detail tiket klaim oleh pelapor
        $response = $this->actingAs($reporter, 'sanctum')->getJson("/api/claim-tickets/{$ticket->id}");

        $response->assertStatus(200);

        $ticketData = $response->json('ticket');

        // Koordinat pada detail tiket klaim harus ASLI dan PERSIS sesuai database
        $this->assertEquals($exactLostLat, (float) $ticketData['lost_item']['latitude']);
        $this->assertEquals($exactLostLng, (float) $ticketData['lost_item']['longitude']);
        $this->assertEquals($exactFoundLat, (float) $ticketData['found_item']['latitude']);
        $this->assertEquals($exactFoundLng, (float) $ticketData['found_item']['longitude']);
    }
}
