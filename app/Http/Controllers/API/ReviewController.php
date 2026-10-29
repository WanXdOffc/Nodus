<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan baru dari pengguna yang sedang login.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $review = AppReview::create([
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_featured' => false,
        ]);

        return response()->json([
            'message' => 'Terima kasih atas ulasan Anda! Ulasan Anda telah berhasil disimpan.',
            'review' => $review->load('user:id,name,trust_score'),
        ], 201);
    }

    /**
     * Mengambil daftar ulasan yang ditandai sebagai featured oleh admin untuk ditampilkan di landing page.
     */
    public function indexFeatured(): JsonResponse
    {
        $reviews = AppReview::with('user:id,name,trust_score')
            ->where('is_featured', true)
            ->latest()
            ->take(10)
            ->get();

        return response()->json($reviews);
    }
}
