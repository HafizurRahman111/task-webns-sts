<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Ticket routes
    Route::prefix('tickets')->group(function () {
        // Accessible to all authenticated users
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{ticket}', [TicketController::class, 'show']);

        // Update and delete routes (require ownership for non-admins)
        Route::put('/{ticket}', [TicketController::class, 'update']);
        Route::delete('/{ticket}', [TicketController::class, 'destroy']);

        // Comment routes under a ticket
        Route::prefix('/{ticket}/comments')->group(function () {
            // Accessible to all authenticated users
            Route::get('/', [CommentController::class, 'index']); // List all comments for a ticket
            Route::post('/', [CommentController::class, 'store']);

            // Delete a comment (require ownership for non-admins)
            Route::delete('/{comment}', [CommentController::class, 'destroy']);
        });

        // Chat routes under a ticket
        Route::prefix('/{ticket}/chats')->group(function () {
            // Accessible to all authenticated users
            Route::get('/', [ChatController::class, 'index']); // List all chats for a ticket
            Route::post('/', [ChatController::class, 'store']);

            // Delete a chat (require ownership for non-admins)
            Route::delete('/{chat}', [ChatController::class, 'destroy']);
        });
    });

    // Admin routes (require admin role)
    Route::middleware(['role.admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});