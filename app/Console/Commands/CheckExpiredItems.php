<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;

class CheckExpiredItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek barang temuan (found) yang belum diklaim lebih dari 30 hari dan ubah statusnya menjadi expired_charity untuk disumbangkan.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiryDate = now()->subDays(30);

        $expiredItems = Item::where('type', 'found')
            ->where('status', 'searching')
            ->where('created_at', '<=', $expiryDate)
            ->get();

        $count = $expiredItems->count();

        if ($count === 0) {
            $this->info('Tidak ada barang temuan yang melebihi batas 30 hari.');
            return Command::SUCCESS;
        }

        foreach ($expiredItems as $item) {
            $item->update([
                'status' => 'expired_charity',
            ]);
        }

        $this->info("Berhasil memperbarui {$count} barang temuan (> 30 hari) menjadi status 'expired_charity'.");

        return Command::SUCCESS;
    }
}
