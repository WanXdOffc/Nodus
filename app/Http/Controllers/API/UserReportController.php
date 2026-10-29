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
            'reason_category' => ['nullable', 'string', 'max:100'],
            'reason' => ['required', 'string'],
            'evidence_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $fullReason = $validated['reason'];
        if (! empty($validated['reason_category'])) {
            $fullReason = '[' . $validated['reason_category'] . '] ' . $fullReason;
        }

        $imagePath = null;
        if ($request->hasFile('evidence_image')) {
            $imagePath = $request->file('evidence_image')->store('user_reports', 'public');
        }

        $report = UserReport::create([
            'reporter_id' => $request->user()->id,
            'reported_user_id' => $validated['reported_user_id'],
            'reason' => $fullReason,
            'evidence_image_path' => $imagePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Laporan pengguna beserta bukti berhasil dikirim dan akan segera ditinjau oleh tim moderator.',
            'report' => $report->load(['reporter:id,name', 'reportedUser:id,name']),
        ], 201);
    }
}
