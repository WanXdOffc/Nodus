<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClaimTicket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Memperbarui status tiket klaim (misal: 'verified' atau 'rejected').
     */
    public function update(Request $request, ClaimTicket $claimTicket): JsonResponse
    {
        $userId = $request->user()->id;

        $isOwner = $claimTicket->lostItem->user_id === $userId || $claimTicket->foundItem->user_id === $userId;

        if (! $isOwner && ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak akses untuk mengubah tiket klaim ini.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:open,verified,rejected'],
        ]);

        $claimTicket->update(['status' => $validated['status']]);

        // Jika diverifikasi, tandai kedua barang menjadi resolved
        if ($validated['status'] === 'verified') {
            $claimTicket->lostItem->update(['status' => 'resolved']);
            $claimTicket->foundItem->update(['status' => 'resolved']);
        }

        return response()->json([
            'message' => 'Status tiket klaim berhasil diperbarui.',
            'ticket' => $claimTicket->fresh()->load([
                'lostItem.user:id,name,trust_score',
                'foundItem.user:id,name,trust_score',
            ]),
        ]);
    }

    /**
     * Menyelesaikan tiket klaim (resolusi kepemilikan) dan menambah trust score pengguna.
     */
    public function resolveTicket(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;

        $ticket = ClaimTicket::with(['lostItem.user', 'foundItem.user'])->findOrFail($id);

        // Otorisasi: pemilik barang hilang, pemilik barang temuan, atau admin
        $isOwner = $ticket->lostItem->user_id === $userId || $ticket->foundItem->user_id === $userId;
        if (! $isOwner && ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak akses untuk menyelesaikan tiket klaim ini.',
            ], 403);
        }

        // Pastikan tiket masih 'open'
        if ($ticket->status !== 'open') {
            return response()->json([
                'message' => 'Tiket klaim ini sudah diselesaikan atau ditutup sebelumnya.',
            ], 422);
        }

        // Pastikan tiket sudah disetujui oleh admin (admin_status: approved)
        if ($ticket->admin_status !== 'approved') {
            return response()->json([
                'message' => 'Tiket klaim ini belum disetujui oleh admin untuk dapat diselesaikan.',
            ], 422);
        }

        DB::transaction(function () use ($ticket) {
            // Ubah status tiket menjadi verified
            $ticket->update(['status' => 'verified']);

            // Ubah status kedua barang menjadi resolved
            $ticket->lostItem->update(['status' => 'resolved']);
            $ticket->foundItem->update(['status' => 'resolved']);

            // Sistem Reward: Tambah trust_score masing-masing +0.5 (maksimal skala 10.0)
            $reporter = User::find($ticket->lostItem->user_id);
            $finder = User::find($ticket->foundItem->user_id);

            if ($reporter) {
                $reporterScore = min(10.0, round(((float) $reporter->trust_score) + 0.5, 1));
                $reporter->update(['trust_score' => $reporterScore]);
            }

            if ($finder && $finder->id !== $reporter?->id) {
                $finderScore = min(10.0, round(((float) $finder->trust_score) + 0.5, 1));
                $finder->update(['trust_score' => $finderScore]);
            }
        });

        return response()->json([
            'message' => 'Klaim Berhasil! Trust Score Anda meningkat.',
            'ticket' => $ticket->fresh([
                'lostItem.user:id,name,trust_score',
                'foundItem.user:id,name,trust_score',
            ]),
        ]);
    }
}
