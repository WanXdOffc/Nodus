<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun Admin Nodus
        $admin = User::updateOrCreate(
            ['email' => 'admin@nodus.com'],
            [
                'name' => 'Admin Nodus',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'trust_score' => 5.0,
            ]
        );

        // Buat akun User Penemu
        $finder = User::updateOrCreate(
            ['email' => 'finder@nodus.com'],
            [
                'name' => 'Made Aryasuta',
                'password' => Hash::make('password'),
                'role' => 'user',
                'trust_score' => 4.9,
            ]
        );

        // 1. Barang Hilang (Matched)
        $lostWallet = \App\Models\Item::updateOrCreate(
            ['title' => 'Dompet Kulit Braun Buffel Hitam'],
            [
                'user_id' => $admin->id,
                'type' => 'lost',
                'description' => 'Dompet kulit lipat hitam merk Braun Buffel, berisi KTP atas nama Admin Nodus dan kartu mahasiswa.',
                'category' => 'Dompet & Aksesoris',
                'latitude' => -8.1145,
                'longitude' => 115.0875,
                'location_name' => 'Taman Kota Singaraja',
                'item_date' => now()->subDay()->format('Y-m-d'),
                'status' => 'matched',
                'ai_tags' => ['dompet', 'kulit', 'hitam', 'braun buffel', 'ktp'],
            ]
        );

        // 2. Barang Temuan (Matched)
        $foundWallet = \App\Models\Item::updateOrCreate(
            ['title' => 'Dompet Lipat Kulit Pria'],
            [
                'user_id' => $finder->id,
                'type' => 'found',
                'description' => 'Ditemukan dompet lipat kulit warna hitam di bangku utara Taman Kota Singaraja dekat air mancur.',
                'category' => 'Dompet & Aksesoris',
                'latitude' => -8.1147,
                'longitude' => 115.0876,
                'location_name' => 'Taman Kota Singaraja',
                'item_date' => now()->subDay()->format('Y-m-d'),
                'status' => 'matched',
                'ai_tags' => ['dompet', 'kulit', 'hitam', 'pria', 'taman kota'],
            ]
        );

        // 3. Claim Ticket dengan AI Match 95%
        $ticket = \App\Models\ClaimTicket::updateOrCreate(
            [
                'lost_item_id' => $lostWallet->id,
                'found_item_id' => $foundWallet->id,
            ],
            [
                'ai_match_score' => 95,
                'status' => 'open',
            ]
        );

        // 4. Ticket Messages
        \App\Models\TicketMessage::updateOrCreate(
            [
                'ticket_id' => $ticket->id,
                'user_id' => $finder->id,
                'message' => 'Halo! Saya menemukan dompet kulit hitam di area Taman Kota kemarin sore. Apakah ada ciri khusus di dalamnya?',
            ]
        );

        \App\Models\TicketMessage::updateOrCreate(
            [
                'ticket_id' => $ticket->id,
                'user_id' => $admin->id,
                'message' => 'Halo Bli Made, betul sekali. Di bagian slot kartu kedua ada KTP dan kartu identitas saya.',
            ]
        );

        \App\Models\TicketMessage::updateOrCreate(
            [
                'ticket_id' => $ticket->id,
                'user_id' => $finder->id,
                'message' => 'Cocok dan terkonfirmasi! Silakan verifikasi tiket klaim ini melalui tombol sistem agar bisa saya serahkan langsung.',
            ]
        );

        // 5. Barang Tambahan di sekitar Singaraja
        \App\Models\Item::updateOrCreate(
            ['title' => 'Kunci Motor Honda Vario Smartkey'],
            [
                'user_id' => $admin->id,
                'type' => 'lost',
                'description' => 'Kunci remote smartkey Honda Vario dengan gantungan boneka biru, terjatuh di area parkiran pantai.',
                'category' => 'Kunci & Kendaraan',
                'latitude' => -8.1065,
                'longitude' => 115.0682,
                'location_name' => 'Pantai Penimbangan, Singaraja',
                'item_date' => now()->subDays(2)->format('Y-m-d'),
                'status' => 'searching',
                'ai_tags' => ['kunci', 'smartkey', 'vario', 'gantungan biru'],
            ]
        );

        \App\Models\Item::updateOrCreate(
            ['title' => 'Tumbler Stainless Corkcicle Mint'],
            [
                'user_id' => $finder->id,
                'type' => 'found',
                'description' => 'Tertinggal di meja kantin barat Kampus Tengah Undiksha, kondisi mulus warna mint.',
                'category' => 'Barang Pribadi',
                'latitude' => -8.1182,
                'longitude' => 115.0931,
                'location_name' => 'Kampus Tengah Undiksha',
                'item_date' => now()->subDay()->format('Y-m-d'),
                'status' => 'searching',
                'ai_tags' => ['tumbler', 'corkcicle', 'mint', 'stainless'],
            ]
        );

        \App\Models\Item::updateOrCreate(
            ['title' => 'iPhone 13 Midnight Case Silikon'],
            [
                'user_id' => $admin->id,
                'type' => 'lost',
                'description' => 'iPhone 13 warna midnight dengan casing silikon gelap, hilang saat berfoto di jembatan pelabuhan tua.',
                'category' => 'Elektronik & Gadget',
                'latitude' => -8.1042,
                'longitude' => 115.0912,
                'location_name' => 'Eks Pelabuhan Buleleng',
                'item_date' => now()->format('Y-m-d'),
                'status' => 'searching',
                'ai_tags' => ['iphone', 'midnight', 'silikon', 'smartphone'],
            ]
        );

        \App\Models\Item::updateOrCreate(
            ['title' => 'Tas Ransel Eiger 25L Abu-abu'],
            [
                'user_id' => $finder->id,
                'type' => 'found',
                'description' => 'Tas ransel Eiger warna abu-abu gelap ditemukan di ruang baca lantai 2 perpustakaan.',
                'category' => 'Dompet & Tas',
                'latitude' => -8.1165,
                'longitude' => 115.0895,
                'location_name' => 'Perpustakaan Daerah Buleleng',
                'item_date' => now()->format('Y-m-d'),
                'status' => 'searching',
                'ai_tags' => ['tas', 'ransel', 'eiger', 'abu-abu'],
            ]
        );
    }
}
