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
Route::prefix('apply')->middleware(['web', 'auth'])->group(function() {
    Route::post('/start', [ApplyController::class, 'start'])->name('apply.start');
});

// --------------------
// External account connection API
// --------------------
Route::prefix('platform')->middleware(['web', 'auth'])->group(function() {
    Route::get('/{provider}/disconnect', [PlatformController::class, 'disconnect'])->name('api.platform.disconnect');
    Route::post('/{provider}/save-token', [PlatformController::class, 'save_token'])->name('api.platform.save-token');
    Route::post('/{provider}/passwordless-login', [PlatformController::class, 'passwordless_login'])->name('api.platform.passwordless-login');
    Route::post('/{provider}/verify-otp', [PlatformController::class, 'verify_otp'])->name('api.platform.verify-otp');

});


