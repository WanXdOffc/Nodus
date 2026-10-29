<?php

namespace Tests\Feature;

use App\Jobs\ProcessAiMatchmaking;
use App\Mail\ItemMatchFoundMail;
use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AiMatchmakingJobAndOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_store_dispatches_process_ai_matchmaking_job(): void
    {
        Queue::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->post('/api/items', [
            'title' => 'Kacamata Hitam Rayban',
            'description' => 'Tertinggal di meja kantin Undiksha',
            'type' => 'lost',
            'category' => 'Barang Pribadi Lainnya',
            'latitude' => -8.112,
            'longitude' => 115.088,
            'location_name' => 'Kantin Kampus',
            'item_date' => now()->format('Y-m-d'),
            'image' => UploadedFile::fake()->image('kacamata.jpg'),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Laporan berhasil dibuat.');

        Queue::assertPushed(ProcessAiMatchmaking::class, function ($job) {
            return $job->item->title === 'Kacamata Hitam Rayban';
        });
    }

    public function test_process_ai_matchmaking_job_calls_gemini_1_5_flash_and_creates_ticket(): void
    {
        Mail::fake();

        $seeker = User::factory()->create(['name' => 'Kadek Seeker', 'email' => 'seeker@test.com']);
        $finder = User::factory()->create(['name' => 'Wayan Finder', 'email' => 'finder@test.com']);

        // Item 1: Barang kehilangan yang sudah ada di database
        $lostItem = Item::create([
            'user_id' => $seeker->id,
            'title' => 'Helm KYT Kuning Fluo',
            'description' => 'Helm full face warna kuning',
            'category' => 'Kunci & Kendaraan',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -8.112,
            'longitude' => 115.088,
            'location_name' => 'Parkiran Kampus',
            'item_date' => now()->format('Y-m-d'),
            'ai_tags' => ['helm', 'kyt', 'kuning'],
        ]);

        // Item 2: Barang temuan baru dilaporkan
        $foundItem = Item::create([
            'user_id' => $finder->id,
            'title' => 'Ditemukan Helm KYT Kuning',
            'description' => 'Helm KYT warna kuning cerah di parkiran',
            'category' => 'Kunci & Kendaraan',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -8.112,
            'longitude' => 115.088,
            'location_name' => 'Parkiran Kampus',
            'item_date' => now()->format('Y-m-d'),
            'ai_tags' => null,
        ]);

        // Mock Gemini API menggunakan model gemini-1.5-flash
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => '["helm", "kyt", "kuning"]'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Jalankan Job secara sinkron
        $job = new ProcessAiMatchmaking($foundItem);
        $job->handle();

        // 1. Pastikan ai_tags terisi pada barang temuan
        $freshFound = $foundItem->fresh();
        $this->assertEquals(['helm', 'kyt', 'kuning'], $freshFound->ai_tags);

        // 2. Pastikan ClaimTicket dibuat dengan status pending dan match score tinggi
        $ticket = ClaimTicket::where('lost_item_id', $lostItem->id)
            ->where('found_item_id', $foundItem->id)
            ->first();

        $this->assertNotNull($ticket);
        $this->assertEquals('open', $ticket->status);
        $this->assertEquals('pending', $ticket->admin_status);
        $this->assertEquals(100, $ticket->ai_match_score);

        // 3. Pastikan email notifikasi dikirim ke seeker dan finder
        Mail::assertSent(ItemMatchFoundMail::class, function ($mail) {
            return $mail->hasTo('seeker@test.com');
        });
        Mail::assertSent(ItemMatchFoundMail::class, function ($mail) {
            return $mail->hasTo('finder@test.com');
        });
    }

    public function test_smart_directional_noise_scale_does_not_exceed_500m(): void
    {
        $user = User::factory()->create();

        $origLat = -8.112000;
        $origLng = 115.088000;

        Item::create([
            'user_id' => $user->id,
            'title' => 'Jam Tangan Casio',
            'description' => 'Hilang di lab komputer',
            'category' => 'Perhiasan & Jam',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => $origLat,
            'longitude' => $origLng,
            'location_name' => 'Lab Komputer',
            'item_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->getJson('/api/items');
        $response->assertStatus(200);

        $itemData = $response->json('data.0');
        $newLat = (float) $itemData['latitude'];
        $newLng = (float) $itemData['longitude'];

        // Periksa bahwa koordinat digeser ke Selatan (latitude berkurang)
        $this->assertLessThan($origLat, $newLat, 'Latitude harus digeser ke Selatan (daratan).');

        // Periksa bahwa jarak noise tidak melebihi 0.0045 derajat (~500 meter)
        $diffLat = abs($newLat - $origLat);
        $diffLng = abs($newLng - $origLng);

        $this->assertLessThanOrEqual(0.0045, $diffLat, 'Pergeseran latitude maksimal 500m (0.0045 derajat).');
        $this->assertLessThanOrEqual(0.0045, $diffLng, 'Pergeseran longitude maksimal 500m (0.0045 derajat).');
    }

    public function test_eager_loading_with_user_relationship(): void
    {
        $user = User::factory()->create(['name' => 'Gede Developer']);

        Item::create([
            'user_id' => $user->id,
            'title' => 'Buku Pemrograman Web',
            'description' => 'Tertinggal di ruang kuliah',
            'category' => 'Dokumen & Kartu',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -8.112,
            'longitude' => 115.088,
            'location_name' => 'Ruang Kuliah',
            'item_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->getJson('/api/items?user_id=' . $user->id);

        $response->assertStatus(200);
        $this->assertEquals('Gede Developer', $response->json('data.0.user.name'));
    }
}
