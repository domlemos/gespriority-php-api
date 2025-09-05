<?php

use App\Models\User;
use App\Http\Controllers\SanctumAuthUserController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/auth/login', [SanctumAuthUserController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/auth/logout', [SanctumAuthUserController::class, 'logout']);
    Route::post('/auth/logout-all', [SanctumAuthUserController::class, 'logoutAll']);
});

// Password reset routes
Route::middleware(['throttle:3,1'])->group(function () {
    Route::post('/auth/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [PasswordResetController::class, 'resetPassword']);
});

// Token verification (less restrictive throttle)
Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/auth/verify-reset-token/{token}/{email}', [PasswordResetController::class, 'verifyToken']);
});
