<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ClaimTicket;
use App\Models\TicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketMessageController extends Controller
{
    /**
     * Mengambil daftar pesan pada tiket klaim tertentu (Secure Claim Ticket chat).
     */
    public function index(Request $request, ClaimTicket $claimTicket): JsonResponse
    {
        $userId = $request->user()->id;

        // Pastikan hanya pihak yang terlibat dalam tiket yang dapat melihat percakapan
        $isParticipant = $claimTicket->lostItem->user_id === $userId || $claimTicket->foundItem->user_id === $userId;

        if (! $isParticipant && ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak memiliki hak akses pada tiket klaim ini.',
            ], 403);
        }

        $messages = $claimTicket->messages()
            ->with('user:id,name,role')
            ->oldest()
            ->get();

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Mengirim pesan baru ke dalam tiket klaim.
     */
    public function store(Request $request, ClaimTicket $claimTicket): JsonResponse
    {
        $userId = $request->user()->id;

        $isParticipant = $claimTicket->lostItem->user_id === $userId || $claimTicket->foundItem->user_id === $userId;

        if (! $isParticipant && ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak berhak mengirim pesan pada tiket klaim ini.',
            ], 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = $claimTicket->messages()->create([
            'user_id' => $userId,
            'message' => $validated['message'],
        ]);

        return response()->json([
            'message' => 'Pesan berhasil dikirim.',
            'data' => $message->load('user:id,name,role'),
        ], 201);
    }

    /**
     * Menghapus pesan tertentu (opsional CRUD).
     */
    public function destroy(Request $request, ClaimTicket $claimTicket, TicketMessage $ticketMessage): JsonResponse
    {
        $userId = $request->user()->id;

        // Pastikan pesan adalah milik tiket ini
        if ($ticketMessage->ticket_id !== $claimTicket->id) {
            return response()->json([
                'message' => 'Pesan tidak ditemukan pada tiket ini.',
            ], 404);
        }

        // Hanya pengirim pesan atau admin yang boleh menghapus
        if ($ticketMessage->user_id !== $userId && ! $request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus pesan ini.',
            ], 403);
        }

        $ticketMessage->delete();

        return response()->json([
            'message' => 'Pesan berhasil dihapus.',
        ]);
    }
}
