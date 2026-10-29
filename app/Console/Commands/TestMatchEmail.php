<?php

namespace App\Console\Commands;

use App\Mail\ItemMatchFoundMail;
use App\Models\ClaimTicket;
use App\Models\Item;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMatchEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nodus:test-email {email : Alamat email tujuan pengetesan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email uji coba notifikasi AI Match ke alamat email nyata untuk memverifikasi konfigurasi SMTP';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetEmail = $this->argument('email');

        $this->info("Memulai uji coba pengiriman email ke: {$targetEmail}");
        $this->info("Mailer saat ini: " . config('mail.default'));
        $this->info("Host: " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port'));
        $this->info("Username: " . (config('mail.mailers.smtp.username') ?: '(kosong/null)'));
        $this->info("From: " . config('mail.from.address') . " (" . config('mail.from.name') . ")");

        try {
            // Ambil atau buat data mock user & ticket
            $user = User::first() ?? new User([
                'name' => 'Pengguna Uji Coba',
                'email' => $targetEmail,
            ]);

            $ticket = ClaimTicket::with(['lostItem', 'foundItem'])->first();

            if (! $ticket) {
                $lostItem = new Item([
                    'title' => 'Dompet Kulit Cokelat',
                    'category' => 'Dompet & Tas',
                    'location_name' => 'Perpustakaan Kampus Undiksha',
                ]);
                $foundItem = new Item([
                    'title' => 'Dompet Kulit Braun Buffel',
                    'category' => 'Dompet & Tas',
                    'location_name' => 'Kantin Kampus Undiksha',
                ]);
                $ticket = new ClaimTicket([
                    'ai_match_score' => 96,
                    'status' => 'open',
                    'admin_status' => 'approved',
                ]);
                $ticket->setRelation('lostItem', $lostItem);
                $ticket->setRelation('foundItem', $foundItem);
            }

            Mail::to($targetEmail)->send(new ItemMatchFoundMail($user, $ticket, 'seeker'));

            $this->info("✅ Berhasil! Email notifikasi kecocokan barang terkirim ke {$targetEmail}.");
            $this->info("Silakan periksa kotak masuk (Inbox) atau folder Spam pada email Anda.");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Gagal mengirim email: " . $e->getMessage());
            $this->line("Detail pesan error:");
            $this->line($e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
