<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    /**
     * Membuat laporan terhadap pengguna lain.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reported_user_id' => ['required', 'exists:users,id'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $report = UserReport::create([
            'reporter_id' => $request->user()->id,
            'reported_user_id' => $validated['reported_user_id'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Laporan pengguna berhasil dikirim dan akan ditinjau oleh tim moderator.',
            'report' => $report,
        ], 201);
    }
}
