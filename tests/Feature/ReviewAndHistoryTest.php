<?php

namespace Tests\Feature;

use App\Models\AppReview;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewAndHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_review(): void
    {
        $response = $this->postJson('/api/reviews', [
            'rating' => 5,
            'comment' => 'Aplikasi sangat membantu!',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_submit_review(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'rating' => 5,
            'comment' => 'Dompet saya yang hilang di kampus berhasil kembali dalam 2 jam!',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Terima kasih atas ulasan Anda! Ulasan Anda telah berhasil disimpan.',
            ]);

        $this->assertDatabaseHas('app_reviews', [
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Dompet saya yang hilang di kampus berhasil kembali dalam 2 jam!',
            'is_featured' => false,
        ]);
    }

    public function test_review_validation_rules(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/reviews', [
            'rating' => 6, // invalid > 5
            'comment' => 'ab', // invalid < 5 chars
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating', 'comment']);
    }

    public function test_can_fetch_only_featured_reviews(): void
    {
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);

        AppReview::create([
            'user_id' => $user1->id,
            'rating' => 5,
            'comment' => 'Featured review test',
            'is_featured' => true,
        ]);

        AppReview::create([
            'user_id' => $user2->id,
            'rating' => 3,
            'comment' => 'Non-featured review',
            'is_featured' => false,
        ]);

        $response = $this->getJson('/api/reviews/featured');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'comment' => 'Featured review test',
            ])
            ->assertJsonMissing([
                'comment' => 'Non-featured review',
            ]);
    }

    public function test_can_fetch_resolved_items_history(): void
    {
        $user = User::factory()->create();

        $resolvedItem = Item::create([
            'user_id' => $user->id,
            'title' => 'Dompet Kulit Hitam',
            'description' => 'Dompet kulit hitam merk Eiger berisi KTP',
            'category' => 'Dompet & Tas',
            'type' => 'lost',
            'status' => 'resolved',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'location_name' => 'Gedung Perpustakaan',
            'item_date' => now()->subDays(2),
        ]);

        $activeItem = Item::create([
            'user_id' => $user->id,
            'title' => 'Kunci Motor Honda',
            'description' => 'Kunci motor dengan gantungan pita merah',
            'category' => 'Kunci & Kendaraan',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -6.21,
            'longitude' => 106.81,
            'location_name' => 'Area Parkir Barat',
            'item_date' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/items/history');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $resolvedItem->id,
                'title' => 'Dompet Kulit Hitam',
                'status' => 'resolved',
            ])
            ->assertJsonMissing([
                'id' => $activeItem->id,
                'title' => 'Kunci Motor Honda',
            ]);
    }
}
