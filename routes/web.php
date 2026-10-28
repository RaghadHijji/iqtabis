<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

// everyone (guests too) can see the quotes (FR-01)
Route::get('/', [QuoteController::class, 'index'])->name('home');

// one quote on its own page, and "quote me" (a random quote)
Route::get('/random', [QuoteController::class, 'random'])->name('quotes.random');
Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

// switch the site language (en / ar) and go back to the same page
Route::get('/lang/{locale}', function (string $locale) {
    session(['locale' => $locale]);

    return redirect()->back();
})->whereIn('locale', ['en', 'ar'])->name('lang');

// switch light / dark mode and go back to the same page
Route::get('/theme/{mode}', function (string $mode) {
    session(['theme' => $mode]);

    return redirect()->back();
})->whereIn('mode', ['light', 'dark'])->name('theme');

// only guests (not logged in) can open sign up / log in
Route::middleware('guest')->group(function () {
    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->middleware('throttle:10,1');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // forgot password: get a link by email, then choose a new password (FR-20)
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// only logged-in users
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', fn () => redirect()->route('home', ['view' => 'mine']))->name('profile');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('avatar.remove');
    Route::put('/profile/email', [ProfileController::class, 'updateEmail'])->name('account.email');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('account.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('account.destroy');

    Route::put('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])->name('quotes.destroy');
    Route::post('/quotes/{quote}/like', [QuoteController::class, 'toggleLike'])->name('quotes.like');
    Route::post('/quotes/{quote}/favorite', [QuoteController::class, 'toggleFavorite'])->name('quotes.favorite');
    Route::post('/quotes/{quote}/report', [QuoteController::class, 'report'])->name('quotes.report');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/report', [CommentController::class, 'report'])->name('comments.report');

    // notifications under the bell (FR-23)
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/read', [NotificationController::class, 'readAll'])->name('notifications.read');

    // confirm your email (FR-24)
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');

    // sharing a quote or a comment needs a confirmed email
    Route::middleware('verified')->group(function () {
        Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
        Route::post('/quotes/{quote}/comments', [CommentController::class, 'store'])->middleware('throttle:10,1')->name('comments.store');
    });

    // admin dashboard (FR-25)
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/quotes/{quote}/restore', [AdminController::class, 'restore'])->name('restore');
        Route::post('/comments/{comment}/restore', [AdminController::class, 'restoreComment'])->name('comments.restore');
    });
});
