<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAiMatchmaking;
use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestAiMatchmaking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nodus:test-ai {--title=Dompet Kulit Hitam : Judul barang untuk pengujian AI} {--clean : Hapus item pengujian setelah selesai agar database tetap bersih}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uji coba pemrosesan AI Gemini 1.5 Flash Vision & matchmaking otomatis ke database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $title = $this->option('title') ?: 'Dompet Kulit Hitam';

        $this->info("=================================================");
        $this->info("   NODUS AI VISION & MATCHMAKING TEST SCRIPT     ");
        $this->info("=================================================");
        $this->info("Target Uji Coba: {$title}");
        $model = config('services.gemini.model', 'gemini-3.5-flash-lite');
        $this->info("Model AI       : {$model} (Fast Multimodal Vision)");
        $this->info("Membaca API Key: " . (config('services.gemini.api_key') ? 'Tersedia (OK)' : 'Tidak ditemukan'));

        // 1. Siapkan dummy image fisik di storage publik
        $dummyImageBytes = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $imagePath = 'items/test_dummy_wallet.jpg';
        Storage::disk('public')->put($imagePath, $dummyImageBytes);
        $this->info("Dummy image berhasil dibuat: storage/app/public/{$imagePath}");

        // 2. Ambil user untuk pengujian
        $user = User::first() ?? User::create([
            'name' => 'Tester AI',
            'email' => 'tester.ai@nodus.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'trust_score' => 5.0,
        ]);

        // 3. Buat item pengujian
        $item = Item::create([
            'user_id' => $user->id,
            'title' => $title,
            'description' => 'Dompet lipat warna hitam bahan kulit asli tertinggal di bangku taman',
            'category' => 'Dompet & Tas',
            'type' => 'found',
            'status' => 'searching',
            'latitude' => -8.114700,
            'longitude' => 115.087600,
            'location_name' => 'Taman Kota Singaraja',
            'item_date' => now()->format('Y-m-d'),
            'image_path' => $imagePath,
            'ai_tags' => null,
        ]);

        $this->info("Item ID #{$item->id} tersimpan di database. Status awal ai_tags: (null)");

        // 4. Picu eksekusi Job ProcessAiMatchmaking
        $this->info("Memicu Job ProcessAiMatchmaking dengan Gemini 1.5 Flash...");
        $job = new ProcessAiMatchmaking($item);
        $job->handle();

        // 5. Muat ulang data terbaru dari database
        $freshItem = $item->fresh();
        $tags = $freshItem->ai_tags ?? [];

        $this->info("\n--- HASIL EKSTRAKSI GEMINI AI ---");
        $this->info("Ekstraksi Kata Kunci (AI Tags): " . json_encode($tags, JSON_UNESCAPED_UNICODE));

        // 6. Cek apakah tiket kecocokan (ClaimTicket) berhasil dibuat
        $matchedTicket = ClaimTicket::where('lost_item_id', $freshItem->id)
            ->orWhere('found_item_id', $freshItem->id)
            ->latest('id')
            ->first();

        if ($matchedTicket) {
            $this->info("\n--- HASIL MATCHMAKING ---");
            $this->info("✅ Tiket Klaim Berhasil Dibuat!");
            $this->info("Tiket ID        : #{$matchedTicket->id}");
            $this->info("Match Score     : {$matchedTicket->ai_match_score}% Cocok");
            $this->info("Status Tiket    : {$matchedTicket->status}");
            $this->info("Status Admin    : {$matchedTicket->admin_status}");
            $this->info("Lost Item ID    : #{$matchedTicket->lost_item_id} ({$matchedTicket->lostItem?->title})");
            $this->info("Found Item ID   : #{$matchedTicket->found_item_id} ({$matchedTicket->foundItem?->title})");
        } else {
            $this->info("\n--- HASIL MATCHMAKING ---");
            $this->info("ℹ️ Tidak ada barang lawan dengan skor >= 60% yang cocok saat ini.");
        }

        if ($this->option('clean')) {
            $item->delete();
            $this->info("🧹 Item pengujian #{$item->id} telah dihapus (--clean aktif).");
        }

        $this->info("=================================================");
        $this->info("   TES AI BERHASIL DIEKSEKUSI TANPA ERROR        ");
        $this->info("=================================================\n");

        return Command::SUCCESS;
    }
}
