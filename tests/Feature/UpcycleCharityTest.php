<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UpcycleCharityTest extends TestCase
{
    use RefreshDatabase;

    public function test_artisan_command_expires_unclaimed_found_items_after_30_days(): void
    {
        $user = User::factory()->create();

        // 1. Barang temuan (found) berumur 35 hari (> 30 hari) berstatus 'searching' -> HARUS expired_charity
        $oldFoundItem = Item::create([
            'user_id' => $user->id,
            'title' => 'Botol Minum Tupperware',
            'description' => 'Tertinggal di kelas 35 hari lalu',
            'category' => 'Lainnya',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Ruang Kuliah B',
            'item_date' => now()->subDays(35),
        ]);
        Item::where('id', $oldFoundItem->id)->update(['created_at' => now()->subDays(35)]);

        // 2. Barang temuan (found) baru 10 hari (< 30 hari) berstatus 'searching' -> TETAP searching
        $recentFoundItem = Item::create([
            'user_id' => $user->id,
            'title' => 'Flashdisk Sandisk 32GB',
            'description' => 'Ditemukan di lab komputer',
            'category' => 'Elektronik',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Lab Komputer',
            'item_date' => now()->subDays(10),
        ]);
        Item::where('id', $recentFoundItem->id)->update(['created_at' => now()->subDays(10)]);

        // 3. Barang hilang (lost) berumur 35 hari -> TETAP searching (bukan temuan yang disumbangkan)
        $oldLostItem = Item::create([
            'user_id' => $user->id,
            'title' => 'Kunci Motor',
            'description' => 'Kehilangan kunci',
            'category' => 'Kunci & Kendaraan',
            'type' => 'lost',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Parkiran',
            'item_date' => now()->subDays(35),
        ]);
        Item::where('id', $oldLostItem->id)->update(['created_at' => now()->subDays(35)]);

        // 4. Barang temuan yang sudah resolved -> TETAP resolved
        $resolvedFoundItem = Item::create([
            'user_id' => $user->id,
            'title' => 'Buku Catatan Kuliah',
            'description' => 'Sudah diambil pemilik',
            'category' => 'Buku & Dokumen',
            'type' => 'found',
            'status' => 'resolved',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Perpustakaan',
            'item_date' => now()->subDays(40),
        ]);
        Item::where('id', $resolvedFoundItem->id)->update(['created_at' => now()->subDays(40)]);

        // Jalankan perintah artisan
        $this->artisan('items:check-expired')
            ->expectsOutputToContain("Berhasil memperbarui 1 barang temuan (> 30 hari) menjadi status 'expired_charity'.")
            ->assertExitCode(0);

        // Verifikasi status pada database
        $this->assertEquals('expired_charity', $oldFoundItem->fresh()->status);
        $this->assertEquals('searching', $recentFoundItem->fresh()->status);
        $this->assertEquals('searching', $oldLostItem->fresh()->status);
        $this->assertEquals('resolved', $resolvedFoundItem->fresh()->status);
    }

    public function test_public_items_index_filters_out_expired_charity_items(): void
    {
        $user = User::factory()->create();

        // Barang aktif
        $activeItem = Item::create([
            'user_id' => $user->id,
            'title' => 'Jaket Hitam Parasut',
            'description' => 'Ditemukan di kantin',
            'category' => 'Pakaian & Aksesoris',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Kantin Kampus',
            'item_date' => now()->subDays(5),
            'created_at' => now()->subDays(5),
        ]);

        // Barang kedaluwarsa charity
        $charityItem = Item::create([
            'user_id' => $user->id,
            'title' => 'Topi Fedora Vintage',
            'description' => 'Sudah lewat 30 hari tidak diambil',
            'category' => 'Pakaian & Aksesoris',
            'type' => 'found',
            'status' => 'expired_charity',
            'latitude' => -8.11,
            'longitude' => 115.08,
            'location_name' => 'Gedung Rektorat',
            'item_date' => now()->subDays(35),
            'created_at' => now()->subDays(35),
        ]);

        // Panggilan API publik: GET /api/items
        $publicResponse = $this->getJson('/api/items');

        $publicResponse->assertStatus(200)
            ->assertJsonFragment(['title' => 'Jaket Hitam Parasut'])
            ->assertJsonMissing(['title' => 'Topi Fedora Vintage']);

        // Panggilan dengan filter user_id: harus mengembalikan barang milik user termasuk expired_charity
        $userResponse = $this->getJson("/api/items?user_id={$user->id}");

        $userResponse->assertStatus(200)
            ->assertJsonFragment(['title' => 'Jaket Hitam Parasut'])
            ->assertJsonFragment(['title' => 'Topi Fedora Vintage']);
    }
}
