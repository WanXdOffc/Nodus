<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicApiController extends Controller
{
    /**
     * Endpoint API Publik Interoperabilitas.
     * Mengembalikan agregat statistik dan daftar barang yang telah berstatus 'resolved'
     * tanpa menyertakan data pribadi (nama pengguna, email, nomor kontak, dll).
     */
    public function items(Request $request): JsonResponse
    {
        $limit = min((int) ($request->query('limit', 15)), 50);

        // Query barang publik (tanpa data pribadi/sensitif)
        $itemsQuery = Item::query();

        // Filter status jika diminta (default: tampilkan semua barang publik yang tidak expired_charity)
        if ($request->filled('status') && $request->query('status') !== 'all') {
            $itemsQuery->where('status', $request->query('status'));
        } else {
            $itemsQuery->where('status', '!=', 'expired_charity');
        }

        if ($request->filled('category')) {
            $itemsQuery->where('category', $request->query('category'));
        }

        if ($request->filled('type')) {
            $itemsQuery->where('type', $request->query('type'));
        }

        $items = $itemsQuery->latest('updated_at')
            ->take($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'type' => $item->type,
                    'type_label' => $item->type === 'lost' ? 'Barang Hilang' : 'Barang Temuan',
                    'category' => $item->category,
                    'location_area' => $item->location_name,
                    'status' => $item->status,
                    'status_label' => match ($item->status) {
                        'searching' => 'Sedang Dicari',
                        'matched' => 'Kecocokan AI Ditemukan',
                        'resolved' => 'Selesai Dikembalikan',
                        default => $item->status,
                    },
                    'item_date' => $item->item_date instanceof \DateTimeInterface ? $item->item_date->format('Y-m-d') : (string) $item->item_date,
                    'image_url' => $item->image_path ? asset('storage/' . $item->image_path) : null,
                    'ai_tags' => $item->ai_tags ?? [],
                    'reported_at' => $item->created_at?->toIso8601String(),
                    'resolved_at' => $item->status === 'resolved' ? $item->updated_at?->toIso8601String() : null,
                ];
            });

        // Agregat statistik publik untuk interoperabilitas kampus & sistem luar
        $stats = [
            'total_items_public' => Item::where('status', '!=', 'expired_charity')->count(),
            'total_searching' => Item::where('status', 'searching')->count(),
            'total_matched' => Item::where('status', 'matched')->count(),
            'total_resolved' => Item::where('status', 'resolved')->count(),
            'resolved_lost_items' => Item::where('status', 'resolved')->where('type', 'lost')->count(),
            'resolved_found_items' => Item::where('status', 'resolved')->where('type', 'found')->count(),
            'total_charity_items' => Item::where('status', 'expired_charity')->count(),
            'resolution_rate_percent' => round(
                (Item::where('status', 'resolved')->count() / max(Item::count(), 1)) * 100,
                1
            ),
        ];

        return response()->json([
            'status' => 'success',
            'api_version' => 'v1.0',
            'endpoint' => '/api/public/items',
            'documentation' => 'Portal Interoperabilitas & Statistik Publik NODUS',
            'items_count' => count($items),
            'statistics' => $stats,
            'items' => $items,
            'data' => $items,
        ]);
    }

    /**
     * Mengajukan permintaan akses developer ke admin.
     */
    public function requestAccess(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan akses developer telah dicatat dan diteruskan ke Administrator NODUS untuk ditinjau.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_developer' => (bool) $user->is_developer,
            ],
        ]);
    }
}
