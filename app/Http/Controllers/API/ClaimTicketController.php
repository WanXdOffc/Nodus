<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClaimTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimTicketController extends Controller
{
    /**
     * Menampilkan daftar tiket klaim milik user yang sedang terautentikasi
     * (baik sebagai pemilik barang hilang maupun penemu barang).
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $tickets = ClaimTicket::where('admin_status', 'approved')
            ->where(function ($query) use ($userId) {
                $query->whereHas('lostItem', fn ($q) => $q->where('user_id', $userId))
                    ->orWhereHas('foundItem', fn ($q) => $q->where('user_id', $userId));
            })
            ->with([
                'lostItem.user:id,name,trust_score',
                'foundItem.user:id,name,trust_score',
            ])
            ->withCount('messages')
            ->latest()
            ->paginate($request->query('per_page', 15));

        return response()->json($tickets);
    }

    /**
     * Menampilkan detail satu tiket klaim beserta item-itemnya dan riwayat pesan.
     */
    public function show(Request $request, ClaimTicket $claimTicket): JsonResponse
    {
        $userId = $request->user()->id;

        // Otorisasi: hanya pemilik lost_item, pemilik found_item, atau admin yang boleh mengakses
        $isOwner = $claimTicket->lostItem->user_id === $userId || $claimTicket->foundItem->user_id === $userId;

        if (! $isOwner && ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak akses untuk melihat tiket klaim ini.',
            ], 403);
        }

        if ($claimTicket->admin_status !== 'approved' && ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Tiket klaim ini belum disetujui atau masih dalam proses moderasi admin.',
            ], 403);
        }

        $claimTicket->load([
            'lostItem.user:id,name,trust_score',
            'foundItem.user:id,name,trust_score',
            'messages.user:id,name',
        ]);

        return response()->json([
            'ticket' => $claimTicket,
        ]);
    }
}
