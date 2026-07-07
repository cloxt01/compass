<?php

use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


// --------------------
// Panel API (requires authentication token)
// --------------------
// routes/web.php
Route::middleware([
    'web',
    'auth',
    'subscription',
    'verified',
])->group(function () {
    Route::prefix('/apply')->group(function () {
        Route::post('/save', [ApplyController::class, 'save'])->name('apply.save');
        Route::post('/stop', [ApplyController::class, 'stop'])->name('apply.stop');
        Route::post('/resume', [ApplyController::class, 'resume'])->name('apply.resume');
    });
});
Route::middleware([
    'web',
    'auth',
    'verified'
])->group(function () {
    Route::prefix('/settings')->group(function () {
        Route::put('/profile/update', [SettingsController::class, 'upsert_user'])->name('profile.update');
        Route::post('/profile/toggle-automation', [SettingsController::class, 'toggle_automation'])->name('profile.toggle-automation');
    });

    Route::post('/{provider}/search-location', [\App\Http\Controllers\Api\Glints\api::class, 'searchLocation'])->name('api.search.location');
    Route::prefix('connection')->group(function() {
        Route::post('/{provider}/login', [ConnectionController::class, 'login'])->name('api.connection.login');
        Route::post('/{provider}/passwordless-login', [ConnectionController::class, 'passwordless_login'])->name('api.connection.passwordless-login');
        Route::post('/{provider}/verify-otp', [ConnectionController::class, 'verify_otp'])->name('api.connection.verify-otp');
    });
});
// --------------------
// External account connection API
// --------------------



