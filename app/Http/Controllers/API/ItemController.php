<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClaimTicket;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    /**
     * Menampilkan daftar barang dengan filter tipe, status, kategori, dan radius koordinat (Haversine).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Item::with('user:id,name,trust_score');

        // Filter berdasarkan type ('lost' atau 'found')
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        // Filter status ('searching', 'matched', 'resolved')
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        // Pencarian teks judul, deskripsi, lokasi
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location_name', 'like', "%{$search}%");
            });
        }

        // Filter radius geografis menggunakan rumus Haversine (dalam kilometer)
        $lat = $request->query('latitude') ?? $request->query('lat');
        $lng = $request->query('longitude') ?? $request->query('lng');
        $radius = (float) ($request->query('radius') ?? 25); // default radius 25 km

        if ($lat !== null && $lng !== null && is_numeric($lat) && is_numeric($lng)) {
            $lat = (float) $lat;
            $lng = (float) $lng;

            // Rumus Haversine: 6371 * acos(cos(radians(lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians(lng)) + sin(radians(lat)) * sin(radians(latitude)))
            $haversineSql = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

            $query->selectRaw("items.*, {$haversineSql} AS distance", [$lat, $lng, $lat])
                ->havingRaw("distance <= ?", [$radius])
                ->orderBy('distance', 'asc');
        } else {
            $query->latest();
        }

        $items = $query->paginate($request->query('per_page', 15));

        return response()->json($items);
    }

    /**
     * Menampilkan detail satu barang.
     */
    public function show(Item $item): JsonResponse
    {
        return response()->json([
            'item' => $item->load('user:id,name,trust_score'),
        ]);
    }

    /**
     * Menyimpan laporan barang baru (lost / found), ekstraksi AI tags dengan Gemini, dan matchmaking otomatis.
     */
    public function store(Request $request): JsonResponse
    {
        // Normalisasi parameter koordinat jika dikirim sebagai 'lat' atau 'lng'
        if ($request->has('lat') && ! $request->has('latitude')) {
            $request->merge(['latitude' => $request->input('lat')]);
        }
        if ($request->has('lng') && ! $request->has('longitude')) {
            $request->merge(['longitude' => $request->input('lng')]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'string', 'in:lost,found'],
            'category' => ['nullable', 'string', 'max:100'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'location_name' => ['required', 'string', 'max:255'],
            'item_date' => ['required', 'date'],
            'image' => ['nullable', 'image', 'max:5120'], // maks 5MB
        ]);

        // Simpan gambar ke storage publik jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        // Ekstraksi kata kunci dengan Gemini AI
        $aiTags = $this->extractAiTags($validated['title'], $validated['description']);

        // Simpan data item ke database
        $item = Item::create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'] ?? 'Umum',
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'location_name' => $validated['location_name'],
            'item_date' => $validated['item_date'],
            'status' => 'searching',
            'image_path' => $imagePath,
            'ai_tags' => $aiTags,
        ]);

        // Jalankan pencocokan otomatis (Matchmaking)
        $createdTickets = $this->runMatchmaking($item);

        return response()->json([
            'message' => 'Laporan barang berhasil disimpan.',
            'item' => $item->load('user:id,name,trust_score'),
            'matched_tickets_count' => count($createdTickets),
        ], 201);
    }

    /**
     * Mengambil daftar riwayat barang yang telah berhasil diselesaikan (resolved).
     */
    public function resolvedHistory(): JsonResponse
    {
        $items = Item::with('user:id,name,trust_score')
            ->where('status', 'resolved')
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'category' => $item->category,
                    'type' => $item->type,
                    'status' => $item->status,
                    'location_name' => $item->location_name,
                    'image_path' => $item->image_path,
                    'item_date' => $item->item_date instanceof \DateTimeInterface ? $item->item_date->format('Y-m-d') : (string) $item->item_date,
                    'resolved_at' => $item->updated_at?->format('Y-m-d H:i') ?? (string) $item->updated_at,
                    'resolved_human' => $item->updated_at?->diffForHumans() ?? 'Baru saja',
                    'user' => $item->user,
                ];
            });

        return response()->json($items);
    }

    /**
     * Memanggil REST API Gemini untuk mengekstrak kata kunci utama menjadi JSON array.
     */
    private function extractAiTags(string $title, string $description): array
    {
        $apiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');

        if (empty($apiKey)) {
            Log::warning('GEMINI_API_KEY tidak dikonfigurasi. Menggunakan ekstraksi fallback.');
            return $this->fallbackTagExtraction($title, $description);
        }

        $prompt = "Ekstrak kata kunci utama dari judul dan deskripsi barang ini dalam format JSON array (contoh: ['dompet', 'kulit', 'hitam', 'fossil']). Hanya berikan output JSON array tanpa penjelasan lainnya. Judul: {$title}, Deskripsi: {$description}";

        // Coba model yang diminta (gemini-1.5-flash), lalu fallback ke model flash aktif (gemini-3.5-flash)
        $models = ['gemini-1.5-flash', 'gemini-3.5-flash', 'gemini-flash-latest'];

        foreach ($models as $model) {
            try {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                $response = Http::timeout(10)->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                    ],
                ]);

                if ($response->successful()) {
                    $rawText = $response->json('candidates.0.content.parts.0.text');

                    if ($rawText) {
                        // Bersihkan pembungkus markdown ```json ... ``` jika ada
                        $cleanJson = preg_replace('/```(?:json)?\s*(.*?)\s*```/s', '$1', trim($rawText));
                        $decoded = json_decode($cleanJson, true);

                        if (is_array($decoded)) {
                            $tags = array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $decoded)))));
                            if (! empty($tags)) {
                                return $tags;
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini model {$model} request exception: " . $e->getMessage());
            }
        }

        return $this->fallbackTagExtraction($title, $description);
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

                $createdTickets[] = $ticket;
            }
        }

        return $createdTickets;
    }
}
