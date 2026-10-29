<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClaimTicketController;
use App\Http\Controllers\API\ItemController;
use App\Http\Controllers\API\PublicApiController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\TicketMessageController;
use App\Http\Controllers\API\UserReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Public Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public Items Browsing & History
Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/history', [ItemController::class, 'resolvedHistory']);
Route::get('/items/{item}', [ItemController::class, 'show']);

// Public Featured Reviews
Route::get('/reviews/featured', [ReviewController::class, 'indexFeatured']);

// Public Interoperability API
Route::get('/public/items', [PublicApiController::class, 'items']);

// Protected Routes (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // User Profile & Logout
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/request-developer', [PublicApiController::class, 'requestAccess']);

    // Store New Item (Lost / Found) with Gemini AI & Matchmaking
    Route::post('/items', [ItemController::class, 'store']);

    // Claim Tickets
    Route::get('/claim-tickets', [ClaimTicketController::class, 'index']);
    Route::get('/claim-tickets/{claimTicket}', [ClaimTicketController::class, 'show']);
    Route::post('/claim-tickets/{id}/resolve', [ClaimTicketController::class, 'resolveTicket']);
    Route::post('/claim-tickets/{id}/verify-qr', [ClaimTicketController::class, 'verifyQr']);
    Route::patch('/claim-tickets/{claimTicket}', [ClaimTicketController::class, 'update']);
    Route::put('/claim-tickets/{claimTicket}', [ClaimTicketController::class, 'update']);

    // Secure Claim Ticket Chat Messages
    Route::get('/claim-tickets/{claimTicket}/messages', [TicketMessageController::class, 'index']);
    Route::post('/claim-tickets/{claimTicket}/messages', [TicketMessageController::class, 'store']);
    Route::delete('/claim-tickets/{claimTicket}/messages/{ticketMessage}', [TicketMessageController::class, 'destroy']);

    // User Reports
    Route::post('/user-reports', [UserReportController::class, 'store']);

    // App Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
});
