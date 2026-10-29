<?php

namespace Tests\Feature;

use App\Filament\Widgets\ItemsChart;
use App\Mail\ItemMatchFoundMail;
use App\Models\AppReview;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NewPortalFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_store_requires_photo(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Tanpa gambar -> harus gagal validasi 422
        $response = $this->postJson('/api/items', [
            'title' => 'Dompet Hilang',
            'description' => 'Dompet kulit hitam',
            'type' => 'lost',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Singaraja',
            'item_date' => '2026-10-30',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['image']);

        // Dengan gambar -> sukses 201
        Storage::fake('public');
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => '["dompet", "kulit"]']]]],
                ],
            ], 200),
        ]);

        $validResponse = $this->post('/api/items', [
            'title' => 'Dompet Hilang',
            'description' => 'Dompet kulit hitam',
            'type' => 'lost',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Singaraja',
            'item_date' => '2026-10-30',
            'image' => UploadedFile::fake()->image('dompet.jpg'),
        ]);

        $validResponse->assertStatus(201);
    }

    public function test_user_report_requires_evidence_photo(): void
    {
        $reporter = User::factory()->create();
        $targetUser = User::factory()->create();

        Sanctum::actingAs($reporter);

        // Tanpa bukti foto -> harus gagal 422
        $response = $this->postJson('/api/user-reports', [
            'reported_user_id' => $targetUser->id,
            'reason_category' => 'Meminta Imbalan Paksa / Pemerasan',
            'reason' => 'Meminta imbalan 500rb',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['evidence_image']);

        // Dengan bukti foto -> sukses 201
        Storage::fake('public');
        $validResponse = $this->post('/api/user-reports', [
            'reported_user_id' => $targetUser->id,
            'reason_category' => 'Meminta Imbalan Paksa / Pemerasan',
            'reason' => 'Meminta imbalan 500rb',
            'evidence_image' => UploadedFile::fake()->image('bukti_chat.png'),
        ]);

        $validResponse->assertStatus(201);
        $this->assertDatabaseHas('user_reports', [
            'reporter_id' => $reporter->id,
            'reported_user_id' => $targetUser->id,
        ]);
    }

    public function test_email_notifications_sent_to_seeker_and_finder_when_matchmaking_occurs(): void
    {
        Mail::fake();
        Storage::fake('public');

        $seeker = User::factory()->create(['email' => 'seeker@test.com', 'name' => 'Seeker User']);
        $finder = User::factory()->create(['email' => 'finder@test.com', 'name' => 'Finder User']);

        // 1. Barang hilang dibuat terlebih dahulu oleh seeker
        $lostItem = Item::create([
            'user_id' => $seeker->id,
            'title' => 'Kunci Motor Honda Vario',
            'description' => 'Kunci remote gantungan biru',
            'category' => 'Kunci & Kendaraan',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Pantai',
            'item_date' => now()->format('Y-m-d'),
            'ai_tags' => ['kunci', 'vario', 'smartkey', 'biru'],
        ]);

        // Mock Gemini response untuk barang temuan
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => '["kunci", "vario", "smartkey"]']]]],
                ],
            ], 200),
        ]);

        // 2. Finder mengunggah barang temuan yang cocok
        Sanctum::actingAs($finder);
        $response = $this->post('/api/items', [
            'title' => 'Ditemukan Kunci Smartkey Vario',
            'description' => 'Ditemukan di pasir pantai gantungan biru',
            'category' => 'Kunci & Kendaraan',
            'type' => 'found',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Pantai',
            'item_date' => now()->format('Y-m-d'),
            'image' => UploadedFile::fake()->image('kunci.jpg'),
        ]);

        $response->assertStatus(201);

        // Verifikasi bahwa email notifikasi dikirimkan ke kedua belah pihak
        Mail::assertSent(ItemMatchFoundMail::class, function ($mail) {
            return $mail->hasTo('seeker@test.com') && $mail->userRole === 'seeker';
        });

        Mail::assertSent(ItemMatchFoundMail::class, function ($mail) {
            return $mail->hasTo('finder@test.com') && $mail->userRole === 'finder';
        });
    }

    public function test_public_featured_reviews_only_returns_rating_4_and_5(): void
    {
        $user = User::factory()->create();

        // Review bintang 5 (featured) -> HARUS MUNCUL
        AppReview::create([
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Layanan sangat memuaskan dan cepat!',
            'is_featured' => true,
        ]);

        // Review bintang 4 (featured) -> HARUS MUNCUL
        AppReview::create([
            'user_id' => $user->id,
            'rating' => 4,
            'comment' => 'Bagus sekali, sangat membantu.',
            'is_featured' => true,
        ]);

        // Review bintang 2 (featured sekalipun) -> TIDAK BOLEH MUNCUL DI LANDING PAGE (hanya admin)
        AppReview::create([
            'user_id' => $user->id,
            'rating' => 2,
            'comment' => 'Server sempat lambat.',
            'is_featured' => true,
        ]);

        // Review bintang 5 (unfeatured) -> TIDAK BOLEH MUNCUL
        AppReview::create([
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Belum di-acc admin.',
            'is_featured' => false,
        ]);

        $response = $this->getJson('/api/reviews/featured');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertCount(2, $data);
        foreach ($data as $rev) {
            $this->assertGreaterThanOrEqual(4, $rev['rating']);
            $this->assertNotEquals(2, $rev['rating']);
        }
    }

    public function test_filament_items_chart_widget_computes_real_monthly_data(): void
    {
        $user = User::factory()->create();

        // Buat 1 lost item bulan ini
        Item::create([
            'user_id' => $user->id,
            'title' => 'Lost Item Bulan Ini',
            'description' => 'Test',
            'category' => 'Umum',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Lokasi',
            'item_date' => now(),
        ]);

        // Buat 2 found items bulan ini
        Item::create([
            'user_id' => $user->id,
            'title' => 'Found Item 1',
            'description' => 'Test',
            'category' => 'Umum',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Lokasi',
            'item_date' => now(),
        ]);
        Item::create([
            'user_id' => $user->id,
            'title' => 'Found Item 2',
            'description' => 'Test',
            'category' => 'Umum',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Lokasi',
            'item_date' => now(),
        ]);

        // Panggil widget
        $widget = new class extends ItemsChart {
            public function getPublicData(): array
            {
                return $this->getData();
            }
        };

        $chartData = $widget->getPublicData();

        $this->assertArrayHasKey('datasets', $chartData);
        $this->assertArrayHasKey('labels', $chartData);
        $this->assertCount(6, $chartData['labels']);

        // Data bulan berjalan (elemen terakhir pada array 6 bulan)
        $lostSeries = $chartData['datasets'][0]['data'];
        $foundSeries = $chartData['datasets'][1]['data'];

        $this->assertEquals(1, end($lostSeries));
        $this->assertEquals(2, end($foundSeries));
    }
}
