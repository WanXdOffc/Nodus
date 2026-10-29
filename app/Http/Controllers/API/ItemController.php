<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAiMatchmaking;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        // Filter status ('searching', 'matched', 'resolved', 'expired_charity')
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        } elseif (! $request->filled('user_id') && ! $request->boolean('include_expired')) {
            // Pastikan memfilter keluar barang berstatus 'expired_charity' agar hilang dari peta dan feed publik
            $query->where('status', '!=', 'expired_charity');
        }

        // Filter berdasarkan user_id (misal untuk halaman Laporanku)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
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

        $items->getCollection()->transform(function (Item $item) {
            return $this->obfuscateCoordinates($item);
        });

        return response()->json($items);
    }

    /**
     * Menampilkan detail satu barang.
     */
    public function show(Item $item): JsonResponse
    {
        $item->load('user:id,name,trust_score');

        return response()->json([
            'item' => $this->obfuscateCoordinates($item),
        ]);
    }

    /**
     * Menambahkan Smart Directional Noise (maksimal 300m / ~0.0025 derajat) untuk menyamarkan koordinat publik.
     */
    private function obfuscateCoordinates(Item $item): Item
    {
        if ($item->latitude !== null && $item->longitude !== null) {
            $max_noise = 0.0025; // ~300 meter (0.0025 derajat koordinat)
            $x = (mt_rand(-100, 100) / 100) * $max_noise; // Acak Barat-Timur
            $y = (mt_rand(10, 100) / 100) * $max_noise; // Hanya positif (Selatan / daratan)
            if (abs($x) < 0.0003) {
                $x = 0.0008;
            }

            $item->latitude = round((float) $item->latitude - $y, 6); // Geser ke Selatan (daratan)
            $item->longitude = round((float) $item->longitude + $x, 6);
        }

        return $item;
    }

    /**
     * Menyimpan laporan barang baru (lost / found) dan mendelegasikan AI Vision ke Background Job.
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
            'image' => ['required', 'image', 'max:5120'], // maks 5MB foto wajib diunggah
        ]);

        // Simpan gambar ke storage publik jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        // Simpan data item ke database secara instan
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
            'ai_tags' => null,
        ]);

        // Delegasikan pembacaan Gemini AI Vision dan Matchmaking ke Background Job
        ProcessAiMatchmaking::dispatch($item);
        $item->refresh();

        $matchedTicketsCount = \App\Models\ClaimTicket::where('lost_item_id', $item->id)
            ->orWhere('found_item_id', $item->id)
            ->count();

        return response()->json([
            'message' => 'Laporan berhasil dibuat.',
            'item' => $item->load('user:id,name,trust_score'),
            'matched_tickets_count' => $matchedTicketsCount,
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
}
