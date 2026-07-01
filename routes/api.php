<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/request/{id}', [RequestController::class, 'request_info'])->name('api.request.info');

// --------------------
// Panel API (requires authentication token)
// --------------------
// routes/web.php
Route::middleware(['web','auth','token'])->group(function () {
    Route::post('/apply/push', [ApplyController::class, 'push'])->name('apply.push');
    Route::post('/apply/stop', [ApplyController::class, 'stop'])->name('apply.stop');
    Route::post('/apply/resume', [ApplyController::class, 'resume'])->name('apply.resume');
});


// --------------------
// External account connection API
// --------------------
Route::post('/{provider}/search-location', [\App\Http\Controllers\Api\Glints\api::class, 'searchLocation'])->name('api.search.location');
Route::prefix('platform')->group(function() {
    Route::post('/{provider}/search-location', [PlatformController::class, 'locationInfo'])->name('api.platform.locationInfo');
    Route::post('/{provider}/login', [PlatformController::class, 'login'])->name('api.platform.login');
    Route::post('/{provider}/passwordless-login', [PlatformController::class, 'passwordless_login'])->name('api.platform.passwordless-login');
    Route::post('/{provider}/verify-otp', [PlatformController::class, 'verify_otp'])->name('api.platform.verify-otp');
});


