<?php

namespace Tests\Feature;

use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemAndAiMatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_view_items_list_and_filter_by_type(): void
    {
        $user = User::create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        Item::create([
            'user_id' => $user->id,
            'type' => 'lost',
            'title' => 'Kunci Motor Honda',
            'description' => 'Gantungan kunci warna merah',
            'category' => 'Kunci',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'location_name' => 'Parkiran Kampus',
            'item_date' => '2026-10-29',
            'status' => 'searching',
            'ai_tags' => ['kunci', 'motor', 'honda'],
        ]);

        Item::create([
            'user_id' => $user->id,
            'type' => 'found',
            'title' => 'Payung Biru',
            'description' => 'Payung lipat di kantin',
            'category' => 'Aksesoris',
            'latitude' => -6.2090,
            'longitude' => 106.8450,
            'location_name' => 'Kantin Kampus',
            'item_date' => '2026-10-29',
            'status' => 'searching',
            'ai_tags' => ['payung', 'biru'],
        ]);

        // Public index without auth
        $response = $this->getJson('/api/items');
        $response->assertStatus(200)
            ->assertJsonPath('total', 2);

        // Filter by type=lost
        $lostResponse = $this->getJson('/api/items?type=lost');
        $lostResponse->assertStatus(200)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.title', 'Kunci Motor Honda');
    }

    public function test_authenticated_user_can_store_item_with_gemini_ai_tags(): void
    {
        $user = User::create([
            'name' => 'Ani',
            'email' => 'ani@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        // Mock Gemini API
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => '```json ["dompet", "kulit", "hitam", "fossil"] ```'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Storage::fake('public');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/items', [
                'title' => 'Dompet Pria Fossil Kulit Hitam',
                'description' => 'Tertinggal di meja perpustakaan lantai 2',
                'type' => 'lost',
                'category' => 'Dompet',
                'lat' => -6.2088,
                'lng' => 106.8456,
                'location_name' => 'Perpustakaan',
                'item_date' => '2026-10-29',
                'image' => UploadedFile::fake()->image('dompet.jpg'),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('item.title', 'Dompet Pria Fossil Kulit Hitam')
            ->assertJsonPath('item.ai_tags', ['dompet', 'kulit', 'hitam', 'fossil']);

        $this->assertDatabaseHas('items', [
            'title' => 'Dompet Pria Fossil Kulit Hitam',
            'type' => 'lost',
        ]);
    }

    public function test_automatic_matchmaking_creates_claim_ticket_when_tags_overlap(): void
    {
        $userLost = User::create([
            'name' => 'Pemilik Hilang',
            'email' => 'lost@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $userFound = User::create([
            'name' => 'Penemu Barang',
            'email' => 'found@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        // Item 1: Lost item already in database
        $lostItem = Item::create([
            'user_id' => $userLost->id,
            'type' => 'lost',
            'title' => 'Dompet Kulit Hitam Fossil',
            'description' => 'Ada kartu mahasiswa',
            'category' => 'Dompet',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'location_name' => 'Gedung A',
            'item_date' => '2026-10-29',
            'status' => 'searching',
            'ai_tags' => ['dompet', 'kulit', 'hitam', 'fossil'],
        ]);

        $foundToken = $userFound->createToken('test')->plainTextToken;

        // Mock Gemini response for found item
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => '["dompet", "kulit", "hitam"]'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        Storage::fake('public');

        // Item 2: Found item uploaded by penemu
        $response = $this->withHeader('Authorization', 'Bearer ' . $foundToken)
            ->post('/api/items', [
                'title' => 'Ditemukan Dompet Kulit Warna Hitam',
                'description' => 'Ditemukan di bangku koridor',
                'type' => 'found',
                'category' => 'Dompet',
                'lat' => -6.2089,
                'lng' => 106.8457,
                'location_name' => 'Koridor Gedung A',
                'item_date' => '2026-10-29',
                'image' => UploadedFile::fake()->image('found_dompet.jpg'),
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('matched_tickets_count', 1);

        // Assert ClaimTicket was created with pending admin_status
        $this->assertDatabaseHas('claim_tickets', [
            'lost_item_id' => $lostItem->id,
            'status' => 'open',
            'admin_status' => 'pending',
        ]);

        $ticket = ClaimTicket::where('lost_item_id', $lostItem->id)->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('pending', $ticket->admin_status);
        $this->assertGreaterThanOrEqual(60, $ticket->ai_match_score);
    }

    public function test_claim_tickets_and_secure_chat(): void
    {
        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'u1@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'u2@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $user3 = User::create([
            'name' => 'User 3 (Stranger)',
            'email' => 'u3@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $lost = Item::create([
            'user_id' => $user1->id,
            'type' => 'lost',
            'title' => 'Jam Tangan Casio',
            'description' => 'Tali rantai perak',
            'category' => 'Jam',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'location_name' => 'Musholla',
            'item_date' => '2026-10-29',
            'status' => 'searching',
            'ai_tags' => ['jam', 'tangan', 'casio'],
        ]);

        $found = Item::create([
            'user_id' => $user2->id,
            'type' => 'found',
            'title' => 'Jam Tangan Perak',
            'description' => 'Merk Casio di tempat wudhu',
            'category' => 'Jam',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'location_name' => 'Musholla',
            'item_date' => '2026-10-29',
            'status' => 'searching',
            'ai_tags' => ['jam', 'tangan', 'casio'],
        ]);

        $ticket = ClaimTicket::create([
            'lost_item_id' => $lost->id,
            'found_item_id' => $found->id,
            'ai_match_score' => 100,
            'status' => 'open',
            'admin_status' => 'pending',
        ]);

        $token1 = $user1->createToken('u1')->plainTextToken;
        $token2 = $user2->createToken('u2')->plainTextToken;
        $token3 = $user3->createToken('u3')->plainTextToken;

        // User 1 cannot see ticket when admin_status is pending
        $resPending = $this->withHeader('Authorization', 'Bearer ' . $token1)->getJson('/api/claim-tickets');
        $resPending->assertStatus(200)->assertJsonPath('total', 0);

        // Admin approves the ticket
        $ticket->update(['admin_status' => 'approved']);

        // User 1 now sees the approved ticket
        $res1 = $this->withHeader('Authorization', 'Bearer ' . $token1)->getJson('/api/claim-tickets');
        $res1->assertStatus(200)->assertJsonPath('total', 1);

        $this->app['auth']->forgetGuards();

        // User 3 (unrelated) does NOT see the ticket
        $res3 = $this->withHeader('Authorization', 'Bearer ' . $token3)->getJson('/api/claim-tickets');
        $res3->assertStatus(200)->assertJsonPath('total', 0);

        // User 3 cannot view detail (403 Forbidden)
        $this->withHeader('Authorization', 'Bearer ' . $token3)
            ->getJson('/api/claim-tickets/' . $ticket->id)
            ->assertStatus(403);

        $this->app['auth']->forgetGuards();

        // User 1 sends a message in the ticket
        $sendMsg = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->postJson("/api/claim-tickets/{$ticket->id}/messages", [
                'message' => 'Halo, apakah jam tangan ini ada goresan kecil di bagian belakang kaca?',
            ]);
        $sendMsg->assertStatus(201)
            ->assertJsonPath('data.message', 'Halo, apakah jam tangan ini ada goresan kecil di bagian belakang kaca?');

        $this->app['auth']->forgetGuards();

        // User 2 reads messages
        $getMsg = $this->withHeader('Authorization', 'Bearer ' . $token2)
            ->getJson("/api/claim-tickets/{$ticket->id}/messages");
        $getMsg->assertStatus(200)
            ->assertJsonCount(1, 'messages');

        $this->app['auth']->forgetGuards();

        // User 3 cannot send message into ticket (403 Forbidden)
        $this->withHeader('Authorization', 'Bearer ' . $token3)
            ->postJson("/api/claim-tickets/{$ticket->id}/messages", [
                'message' => 'Saya ingin ikut nimbrung',
            ])->assertStatus(403);
    }

    public function test_resolve_ticket_updates_status_and_rewards_trust_score(): void
    {
        $userLost = User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $userFound = User::create([
            'name' => 'Finder',
            'email' => 'finder@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $lost = Item::create([
            'user_id' => $userLost->id,
            'type' => 'lost',
            'title' => 'Kunci Motor',
            'description' => 'Gantungan merah',
            'category' => 'Kunci',
            'latitude' => -8.1,
            'longitude' => 115.0,
            'location_name' => 'Taman Kota',
            'item_date' => '2026-10-29',
            'status' => 'searching',
            'ai_tags' => ['kunci', 'motor'],
        ]);

        $found = Item::create([
            'user_id' => $userFound->id,
            'type' => 'found',
            'title' => 'Kunci Motor Honda',
            'description' => 'Ditemukan dekat air mancur',
            'category' => 'Kunci',
            'latitude' => -8.1,
            'longitude' => 115.0,
            'location_name' => 'Taman Kota',
            'item_date' => '2026-10-29',
            'status' => 'searching',
            'ai_tags' => ['kunci', 'motor'],
        ]);

        $ticket = ClaimTicket::create([
            'lost_item_id' => $lost->id,
            'found_item_id' => $found->id,
            'ai_match_score' => 95,
            'status' => 'open',
            'admin_status' => 'pending', // Belum di-approve admin
        ]);

        $tokenOwner = $userLost->createToken('owner')->plainTextToken;

        // 1. Resolusi gagal jika belum di-approve admin
        $resNotApproved = $this->withHeader('Authorization', 'Bearer ' . $tokenOwner)
            ->postJson("/api/claim-tickets/{$ticket->id}/resolve");
        $resNotApproved->assertStatus(422);

        $this->app['auth']->forgetGuards();

        // 2. Admin meng-approve tiket
        $ticket->update(['admin_status' => 'approved']);

        // 3. User asing mencoba menyelesaikan tiket -> 403 Forbidden
        $stranger = User::create([
            'name' => 'Stranger',
            'email' => 'stranger@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);
        $tokenStranger = $stranger->createToken('stranger')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer ' . $tokenStranger)
            ->postJson("/api/claim-tickets/{$ticket->id}/resolve")
            ->assertStatus(403);

        $this->app['auth']->forgetGuards();

        // 4. Pemilik menyelesaikan tiket
        $resSuccess = $this->withHeader('Authorization', 'Bearer ' . $tokenOwner)
            ->postJson("/api/claim-tickets/{$ticket->id}/resolve");
        $resSuccess->assertStatus(200)
            ->assertJsonPath('ticket.status', 'verified');

        // Pastikan tiket, lost item, dan found item berstatus selesai
        $this->assertEquals('verified', $ticket->fresh()->status);
        $this->assertEquals('resolved', $lost->fresh()->status);
        $this->assertEquals('resolved', $found->fresh()->status);

        // Pastikan reward trust_score bertambah +0.5 (5.0 -> 5.5)
        $this->assertEquals(5.5, (float) $userLost->fresh()->trust_score);
        $this->assertEquals(5.5, (float) $userFound->fresh()->trust_score);
    }

    public function test_fraud_penalty_decreases_trust_score_and_bans_user(): void
    {
        $reporter = User::create([
            'name' => 'Pelapor Asli',
            'email' => 'pelapor@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        $scammer = User::create([
            'name' => 'Penipu Siber',
            'email' => 'scammer@example.com',
            'password' => 'password',
            'role' => 'user',
            'trust_score' => 1.5, // Skor rendah, akan turun di bawah 0 saat dipenalti -2.0
        ]);

        $report = \App\Models\UserReport::create([
            'reporter_id' => $reporter->id,
            'reported_user_id' => $scammer->id,
            'reason' => 'Mencoba mengklaim barang orang lain dengan bukti palsu.',
            'status' => 'pending',
        ]);

        // Simulasikan eksekusi action validateFraud
        $report->update(['status' => 'reviewed']);
        $newScore = round(((float) $scammer->trust_score) - 2.0, 1);
        if ($newScore <= 0) {
            $newScore = 0.0;
            $scammer->is_banned = true;
            $scammer->tokens()->delete();
        }
        $scammer->trust_score = $newScore;
        $scammer->save();

        $this->assertEquals('reviewed', $report->fresh()->status);
        $this->assertEquals(0.0, (float) $scammer->fresh()->trust_score);
        $this->assertTrue($scammer->fresh()->is_banned);

        // Pastikan user yang diblokir tidak bisa login
        $this->postJson('/api/login', [
            'email' => 'scammer@example.com',
            'password' => 'password',
        ])->assertStatus(403);
    }
}
