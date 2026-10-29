<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Mengembalikan status konfigurasi dan Client ID Google OAuth publik.
     */
    public function config(): JsonResponse
    {
        $clientId = config('services.google.client_id');

        return response()->json([
            'configured' => ! empty($clientId),
            'client_id' => $clientId ?: null,
        ]);
    }

    /**
     * Memverifikasi Google Token (id_token / access_token / credential)
     * dan melakukan login atau registrasi pengguna secara otomatis.
     */
    public function authenticate(Request $request): JsonResponse
    {
        $request->validate([
            'id_token' => 'nullable|string',
            'access_token' => 'nullable|string',
            'credential' => 'nullable|string',
        ]);

        $idToken = $request->input('id_token') ?: $request->input('credential');
        $accessToken = $request->input('access_token');

        if (! $idToken && ! $accessToken) {
            return response()->json([
                'message' => 'Token autentikasi Google tidak ditemukan.',
            ], 422);
        }

        $googleProfile = null;

        // 1. Verifikasi via Google TokenInfo (JWT ID Token)
        if ($idToken) {
            try {
                $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);

                if ($response->successful()) {
                    $payload = $response->json();
                    if (! empty($payload['email'])) {
                        $googleProfile = [
                            'email' => $payload['email'],
                            'name' => $payload['name'] ?? explode('@', $payload['email'])[0],
                            'sub' => $payload['sub'] ?? null,
                            'picture' => $payload['picture'] ?? null,
                        ];
                    }
                } else {
                    Log::warning('Google ID Token info check failed: ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error('Google ID Token verification exception: ' . $e->getMessage());
            }
        }

        // 2. Verifikasi via Google UserInfo API (Access Token)
        if (! $googleProfile && $accessToken) {
            try {
                $response = Http::withToken($accessToken)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo');

                if ($response->successful()) {
                    $payload = $response->json();
                    if (! empty($payload['email'])) {
                        $googleProfile = [
                            'email' => $payload['email'],
                            'name' => $payload['name'] ?? explode('@', $payload['email'])[0],
                            'sub' => $payload['sub'] ?? null,
                            'picture' => $payload['picture'] ?? null,
                        ];
                    }
                } else {
                    Log::warning('Google UserInfo check failed: ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error('Google Access Token verification exception: ' . $e->getMessage());
            }
        }

        if (! $googleProfile) {
            return response()->json([
                'message' => 'Verifikasi akun Google gagal. Token tidak sah atau sudah kedaluwarsa.',
            ], 401);
        }

        // Cari atau buat pengguna di database
        $user = User::where('email', $googleProfile['email'])->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleProfile['name'],
                'email' => $googleProfile['email'],
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
                'trust_score' => 5.0,
                'is_banned' => false,
                'is_developer' => false,
            ]);
        } elseif ($user->is_banned) {
            return response()->json([
                'message' => 'Akun Anda dinonaktifkan oleh administrator.',
            ], 403);
        }

        $token = $user->createToken('google-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Berhasil masuk dengan akun Google!',
            'token' => $token,
            'access_token' => $token,
            'user' => $user,
        ]);
    }
}
