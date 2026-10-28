<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// public: register / login give you a token
// at most 5 tries a minute, so nobody can guess passwords (NFR-12)
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// public: anyone can read quotes
Route::get('/quotes', [QuoteController::class, 'index']);
Route::get('/quotes/{quote}', [QuoteController::class, 'show']);

// needs a token: Authorization: Bearer <token>
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware('throttle:6,1');

    // sharing a quote needs a confirmed email, same as the site (FR-24) -> 403 until then
    Route::post('/quotes', [QuoteController::class, 'store'])->middleware('verified');
    Route::put('/quotes/{quote}', [QuoteController::class, 'update']);
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy']);
    Route::post('/quotes/{quote}/like', [QuoteController::class, 'toggleLike']);
    Route::post('/quotes/{quote}/favorite', [QuoteController::class, 'toggleFavorite']);
    Route::post('/quotes/{quote}/report', [QuoteController::class, 'report']);
    Route::get('/favorites', [QuoteController::class, 'favorites']);
});
