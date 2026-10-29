<?php

namespace Database\Seeders;

use App\Models\AppReview;
use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with verified scenario data.
     */
    public function run(): void
    {
        // ========================================================
        // 1. Tiga User Utama (Budi: Developer & Admin, Andi, Citra)
        // ========================================================
        $budi = User::updateOrCreate(
            ['email' => 'budi@nodus.com'],
            [
                'name' => 'Budi Developer',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_developer' => true,
                'trust_score' => 5.0,
            ]
        );

        // Akun admin alias untuk kemudahan demo login
        User::updateOrCreate(
            ['email' => 'admin@nodus.com'],
            [
                'name' => 'Budi Developer (Admin)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_developer' => true,
                'trust_score' => 5.0,
            ]
        );

        $andi = User::updateOrCreate(
            ['email' => 'andi@nodus.com'],
            [
                'name' => 'Andi Pratama',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_developer' => false,
                'trust_score' => 4.9,
            ]
        );

        $citra = User::updateOrCreate(
            ['email' => 'citra@nodus.com'],
            [
                'name' => 'Citra Lestari',
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_developer' => false,
                'trust_score' => 5.0,
            ]
        );

        // ========================================================
        // 2. Tepat 3 Laporan Barang (Items)
        // ========================================================

        // Laporan 1: Kunci Motor Honda Vario 160 Smartkey (type: lost), lokasi Pantai Penimbangan, status: searching (belum ada yang menemukan)
        $itemKunci = Item::updateOrCreate(
            ['title' => 'Kunci Motor Honda Vario 160 Smartkey'],
            [
                'user_id' => $budi->id,
                'type' => 'lost',
                'description' => 'Kunci remote smartkey Honda Vario 160 warna hitam dengan gantungan karet biru, terjatuh di area parkiran Pantai Penimbangan.',
                'category' => 'Kunci & Kendaraan',
                'latitude' => -8.106500,
                'longitude' => 115.068200,
                'location_name' => 'Pantai Penimbangan, Singaraja',
                'item_date' => now()->subDays(2)->format('Y-m-d'),
                'status' => 'searching',
                'ai_tags' => ['kunci', 'vario', 'smartkey', 'honda', 'pantai penimbangan'],
            ]
        );

        // Laporan 2: Dompet Kulit Braun Buffel Hitam (type: lost) milik Andi
        $itemDompetAndi = Item::updateOrCreate(
            ['title' => 'Dompet Kulit Braun Buffel Hitam'],
            [
                'user_id' => $andi->id,
                'type' => 'lost',
                'description' => 'Dompet lipat bahan kulit warna hitam merk Braun Buffel, berisi KTP dan kartu identitas mahasiswa atas nama Andi Pratama.',
                'category' => 'Dompet & Tas',
                'latitude' => -8.114500,
                'longitude' => 115.087500,
                'location_name' => 'Taman Kota Singaraja',
                'item_date' => now()->subDay()->format('Y-m-d'),
                'status' => 'matched',
                'ai_tags' => ['dompet', 'kulit', 'hitam', 'braun buffel', 'kartu identitas'],
            ]
        );

        // Laporan 3: Dompet Lipat Kulit Pria (type: found) milik Citra
        $itemDompetCitra = Item::updateOrCreate(
            ['title' => 'Dompet Lipat Kulit Pria'],
            [
                'user_id' => $citra->id,
                'type' => 'found',
                'description' => 'Ditemukan dompet lipat kulit warna hitam di bangku utara Taman Kota Singaraja dekat air mancur.',
                'category' => 'Dompet & Tas',
                'latitude' => -8.114700,
                'longitude' => 115.087600,
                'location_name' => 'Taman Kota Singaraja',
                'item_date' => now()->subDay()->format('Y-m-d'),
                'status' => 'matched',
                'ai_tags' => ['dompet', 'kulit', 'hitam', 'lipat', 'pria', 'taman kota'],
            ]
        );

        // ========================================================
        // 3. Skenario AI Match: Hubungkan Laporan 2 & 3 di claim_tickets
        // ========================================================
        $ticket = ClaimTicket::updateOrCreate(
            [
                'lost_item_id' => $itemDompetAndi->id,
                'found_item_id' => $itemDompetCitra->id,
            ],
            [
                'ai_match_score' => 95,
                'status' => 'open',
                'admin_status' => 'approved',
                'qr_token' => (string) Str::uuid(),
            ]
        );

        // Percakapan Verifikasi Aman di dalam Tiket Klaim
        TicketMessage::updateOrCreate(
            [
                'ticket_id' => $ticket->id,
                'user_id' => $citra->id,
                'message' => 'Halo Kak Andi! Saya menemukan dompet kulit Braun Buffel hitam ini di bangku Taman Kota kemarin sore. Apakah ada kartu identitas di dalamnya?',
            ]
        );

        TicketMessage::updateOrCreate(
            [
                'ticket_id' => $ticket->id,
                'user_id' => $andi->id,
                'message' => 'Halo Citra, betul sekali! Di bagian slot kartu kedua ada KTP dan kartu mahasiswa atas nama Andi Pratama.',
            ]
        );

        TicketMessage::updateOrCreate(
            [
                'ticket_id' => $ticket->id,
                'user_id' => $citra->id,
                'message' => 'Data cocok dan tiket sudah di-approve Admin! Nanti saat bertemu silakan tunjukkan QR Code untuk verifikasi serah terima.',
            ]
        );

        // ========================================================
        // 4. Ulasan Komunitas (AppReview) untuk Testimoni Landing Page
        // ========================================================
        AppReview::updateOrCreate(
            ['comment' => 'Sistem pencocokan AI NODUS sangat akurat! Dompet saya yang hilang di Singaraja bisa terlacak dan terverifikasi secara aman.'],
            [
                'user_id' => $budi->id,
                'rating' => 5,
                'is_featured' => true,
            ]
        );

        AppReview::updateOrCreate(
            ['comment' => 'Fitur Secure Claim Ticket dan verifikasi QR melindungi privasi pelapor dan penemu. Sangat direkomendasikan untuk kampus!'],
            [
                'user_id' => $andi->id,
                'rating' => 5,
                'is_featured' => true,
            ]
        );
    }
}
