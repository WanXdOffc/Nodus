<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeveloperPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_developer_column_defaults_to_false_and_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->assertFalse((bool) $user->is_developer);

        $user->update(['is_developer' => true]);
        $this->assertTrue((bool) $user->fresh()->is_developer);
    }

    public function test_request_developer_access_requires_authentication(): void
    {
        $response = $this->postJson('/api/user/request-developer');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_request_developer_access(): void
    {
        $user = User::factory()->create([
            'is_developer' => false,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/user/request-developer');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'is_developer',
                ],
            ]);
    }

    public function test_public_items_endpoint_returns_aggregate_statistics_and_resolved_items_without_personal_data(): void
    {
        $userA = User::factory()->create([
            'email' => 'personal_secret_a@campus.edu',
        ]);
        $userB = User::factory()->create([
            'email' => 'personal_secret_b@campus.edu',
        ]);

        // 1. Resolved lost item
        Item::create([
            'user_id' => $userA->id,
            'title' => 'Dompet Hitam Kulit',
            'description' => 'Berisi KTP dan kartu mahasiswa rahasia',
            'category' => 'Dompet & Tas',
            'type' => 'lost',
            'status' => 'resolved',
            'latitude' => -8.1123,
            'longitude' => 115.0845,
            'location_name' => 'Kantin Pusat',
            'item_date' => now()->subDays(2),
            'resolved_at' => now()->subDay(),
        ]);

        // 2. Resolved found item
        Item::create([
            'user_id' => $userB->id,
            'title' => 'Kunci Motor Honda',
            'description' => 'Ditemukan di parkiran dengan gantungan biru',
            'category' => 'Kunci & Kendaraan',
            'type' => 'found',
            'status' => 'resolved',
            'latitude' => -8.1130,
            'longitude' => 115.0850,
            'location_name' => 'Parkiran Timur',
            'item_date' => now()->subDays(3),
            'resolved_at' => now()->subDay(),
        ]);

        // 3. Unresolved item (still searching) -> Should NOT appear in public resolved items list
        Item::create([
            'user_id' => $userA->id,
            'title' => 'Laptop Asus ROG Rahasia',
            'description' => 'Masih dicari, jangan diekspos sebagai resolved',
            'category' => 'Elektronik',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -8.1135,
            'longitude' => 115.0855,
            'location_name' => 'Perpustakaan',
            'item_date' => now(),
        ]);

        // 4. Expired charity item
        Item::create([
            'user_id' => $userB->id,
            'title' => 'Tumbler Minum Biru',
            'description' => 'Sudah 30 hari disumbangkan',
            'category' => 'Lainnya',
            'type' => 'found',
            'status' => 'expired_charity',
            'latitude' => -8.1140,
            'longitude' => 115.0860,
            'location_name' => 'Gedung C',
            'item_date' => now()->subDays(35),
        ]);

        $response = $this->getJson('/api/public/items');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'api_version',
                'endpoint',
                'documentation',
                'statistics' => [
                    'total_resolved',
                    'resolved_lost_items',
                    'resolved_found_items',
                    'total_charity_items',
                    'resolution_rate_percent',
                ],
                'items_count',
                'items',
                'data',
            ]);

        $data = $response->json('data');
        // Total 3 item aktif (2 resolved + 1 searching, exclude expired_charity)
        $this->assertCount(3, $data);

        // Pastikan tidak ada data sensitif seperti user_id, email, phone, dll.
        foreach ($data as $item) {
            $this->assertArrayNotHasKey('user_id', $item);
            $this->assertArrayNotHasKey('user', $item);
            $this->assertArrayNotHasKey('email', $item);
            $this->assertArrayNotHasKey('latitude', $item);
            $this->assertArrayNotHasKey('longitude', $item);
        }

        // Pastikan expired charity tidak ada dalam respon
        $response->assertJsonMissing(['title' => 'Tumbler Minum Biru']);
        $response->assertJsonMissing(['personal_secret_a@campus.edu']);
        $response->assertJsonMissing(['personal_secret_b@campus.edu']);

        // Uji filter khusus status=resolved
        $resolvedOnly = $this->getJson('/api/public/items?status=resolved');
        $resolvedOnly->assertStatus(200);
        $this->assertCount(2, $resolvedOnly->json('data'));
        foreach ($resolvedOnly->json('data') as $resolvedItem) {
            $this->assertEquals('resolved', $resolvedItem['status']);
        }

        // Verifikasi agregat statistik
        $this->assertEquals(2, $response->json('statistics.total_resolved'));
        $this->assertEquals(1, $response->json('statistics.resolved_lost_items'));
        $this->assertEquals(1, $response->json('statistics.resolved_found_items'));
        $this->assertEquals(1, $response->json('statistics.total_charity_items'));
    }

    public function test_public_items_endpoint_supports_filters_and_limits(): void
    {
        $user = User::factory()->create();

        // Buat 2 resolved items
        Item::create([
            'user_id' => $user->id,
            'title' => 'Dompet A',
            'description' => 'Dompet hitam kulit',
            'category' => 'Dompet & Tas',
            'type' => 'lost',
            'status' => 'resolved',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Area 1',
            'item_date' => now(),
            'resolved_at' => now(),
        ]);
        Item::create([
            'user_id' => $user->id,
            'title' => 'Kunci B',
            'description' => 'Kunci motor Honda',
            'category' => 'Kunci & Kendaraan',
            'type' => 'found',
            'status' => 'resolved',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Area 2',
            'item_date' => now(),
            'resolved_at' => now(),
        ]);

        // Filter type=found
        $foundResponse = $this->getJson('/api/public/items?type=found');
        $foundResponse->assertStatus(200);
        $this->assertCount(1, $foundResponse->json('data'));
        $this->assertEquals('Kunci B', $foundResponse->json('data.0.title'));

        // Filter limit=1
        $limitResponse = $this->getJson('/api/public/items?limit=1');
        $limitResponse->assertStatus(200);
        $this->assertCount(1, $limitResponse->json('data'));
    }
}
