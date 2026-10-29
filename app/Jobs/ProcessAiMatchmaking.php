<?php

namespace App\Jobs;

use App\Mail\ItemMatchFoundMail;
use App\Models\ClaimTicket;
use App\Models\Item;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProcessAiMatchmaking implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Item $item
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $item = $this->item->fresh();
        if (! $item) {
            return;
        }

        // 1. Ekstraksi kata kunci dengan Gemini 1.5 Flash (Model AI Vision Tercepat)
        $aiTags = $this->extractAiTags($item);
        $item->update(['ai_tags' => $aiTags]);

        // 2. Jalankan algoritma matchmaking ke barang bertipe berlawanan
        $this->runMatchmaking($item);
    }

    /**
     * Memanggil REST API Gemini 1.5 Flash untuk ekstraksi kata kunci / vision.
     */
    private function extractAiTags(Item $item): array
    {
        $apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            Log::warning('GEMINI_API_KEY tidak terkonfigurasi. Menggunakan fallback tag extraction.');
            return $this->fallbackTagExtraction($item->title, $item->description);
        }

        $prompt = "Ekstrak kata kunci utama dari judul dan deskripsi barang ini dalam format JSON array (contoh: ['dompet', 'kulit', 'hitam', 'fossil']). Hanya berikan output JSON array tanpa penjelasan lainnya. Judul: {$item->title}, Deskripsi: {$item->description}";

        // Model tercepat dengan latensi rendah untuk AI Vision & text understanding
        $model = 'gemini-1.5-flash';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $parts = [
            ['text' => $prompt],
        ];

        // Jika terdapat gambar fisik, sertakan dalam payload vision inlineData
        if (! empty($item->image_path) && Storage::disk('public')->exists($item->image_path)) {
            try {
                $imageContent = Storage::disk('public')->get($item->image_path);
                $mimeType = Storage::disk('public')->mimeType($item->image_path) ?: 'image/jpeg';
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => base64_encode($imageContent),
                    ],
                ];
            } catch (\Throwable $e) {
                Log::warning('Gagal membaca gambar untuk Gemini Vision: ' . $e->getMessage());
            }
        }

        try {
            $response = Http::timeout(15)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => $parts,
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                ],
            ]);

            if ($response->successful()) {
                $rawText = $response->json('candidates.0.content.parts.0.text');

                if ($rawText) {
                    $cleanJson = preg_replace('/```(?:json)?\s*(.*?)\s*```/s', '$1', trim($rawText));
                    $decoded = json_decode($cleanJson, true);

                    if (is_array($decoded)) {
                        $tags = array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $decoded)))));
                        if (! empty($tags)) {
                            return $tags;
                        }
                    }
                }
            } else {
                Log::warning("Gemini {$model} returned error status: " . $response->status() . " Body: " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::warning("Gemini {$model} request exception: " . $e->getMessage());
        }

        return $this->fallbackTagExtraction($item->title, $item->description);
    }

    /**
     * Ekstraksi kata kunci sederhana jika panggilan API mengalami kendala.
     */
    private function fallbackTagExtraction(string $title, string $description): array
    {
        $text = strtolower($title . ' ' . $description);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $stopWords = ['dan', 'di', 'ke', 'dari', 'yang', 'pada', 'untuk', 'dengan', 'ada', 'ini', 'itu', 'adalah', 'saya', 'bisa'];
        $filtered = array_filter($words, fn ($w) => strlen($w) >= 3 && ! in_array($w, $stopWords, true));

        return array_values(array_unique($filtered));
    }

    /**
     * Mencari item dengan tipe berlawanan yang statusnya 'searching'.
     * Menghitung kecocokan ai_tags. Jika >= 60%, buat record di claim_tickets.
     */
    private function runMatchmaking(Item $newItem): array
    {
        $newTags = $newItem->ai_tags ?? [];
        if (empty($newTags)) {
            return [];
        }

        // Tipe berlawanan: lost <-> found
        $oppositeType = $newItem->type === 'lost' ? 'found' : 'lost';

        $candidates = Item::where('type', $oppositeType)
            ->where('status', 'searching')
            ->where('id', '!=', $newItem->id)
            ->whereNotNull('ai_tags')
            ->get();

        $createdTickets = [];

        foreach ($candidates as $candidate) {
            $candidateTags = $candidate->ai_tags ?? [];
            if (empty($candidateTags)) {
                continue;
            }

            // Normalisasi tag ke huruf kecil
            $normNewTags = array_map('strtolower', $newTags);
            $normCandidateTags = array_map('strtolower', $candidateTags);

            // Irisan kata kunci
            $intersection = array_intersect($normNewTags, $normCandidateTags);
            $intersectionCount = count($intersection);

            if ($intersectionCount === 0) {
                continue;
            }

            // Hitung persentase kecocokan
            $unionCount = count(array_unique(array_merge($normNewTags, $normCandidateTags)));
            $score = $unionCount > 0 ? (int) round(($intersectionCount / $unionCount) * 100) : 0;

            // Jika kecocokan mencapai ambang batas 60%
            if ($score >= 60) {
                $lostItemId = $newItem->type === 'lost' ? $newItem->id : $candidate->id;
                $foundItemId = $newItem->type === 'found' ? $newItem->id : $candidate->id;

                $ticket = ClaimTicket::firstOrCreate(
                    [
                        'lost_item_id' => $lostItemId,
                        'found_item_id' => $foundItemId,
                    ],
                    [
                        'ai_match_score' => $score,
                        'status' => 'open',
                        'admin_status' => 'pending',
                    ]
                );

                if ($ticket->wasRecentlyCreated) {
                    $this->sendMatchNotifications($ticket);
                }

                $createdTickets[] = $ticket;
            }
        }

        return $createdTickets;
    }

    /**
     * Mengirim notifikasi email kepada pencari (seeker) dan penemu (finder) saat barang terdeteksi cocok.
     */
    private function sendMatchNotifications(ClaimTicket $ticket): void
    {
        try {
            $ticket->load(['lostItem.user', 'foundItem.user']);

            $seeker = $ticket->lostItem?->user;
            $finder = $ticket->foundItem?->user;

            if ($seeker && ! empty($seeker->email)) {
                Mail::to($seeker->email)->send(
                    new ItemMatchFoundMail($seeker, $ticket, 'seeker')
                );
            }

            if ($finder && ! empty($finder->email)) {
                Mail::to($finder->email)->send(
                    new ItemMatchFoundMail($finder, $ticket, 'finder')
                );
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengirimkan email notifikasi kecocokan barang: ' . $e->getMessage());
        }
    }
}
