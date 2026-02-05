<?php

use Illuminate\Http\Request;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ExternalAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/request/{id}', [RequestController::class, 'request_info'])->name('api.request.info');

// --------------------
// Panel API (requires authentication token)
// --------------------
Route::prefix('panel')->group(function() {
    Route::post('/start', [PanelController::class, 'start'])->name('panel.start');
});

// --------------------
// External account connection API
// --------------------
Route::prefix('external')->middleware(['web', 'auth'])->group(function() {
    Route::get('/{provider}/disconnect', [ExternalAccountController::class, 'disconnect'])->name('api.external.disconnect');
    Route::get('/{provider}/add-token', [ExternalAccountController::class, 'add_token'])->name('api.external.add-token');
    Route::post('/{provider}/passwordless-login', [ExternalAccountController::class, 'passwordless_login'])->name('api.external.passwordless-login');
    Route::post('/{provider}/verify-otp', [ExternalAccountController::class, 'verify_otp'])->name('api.external.verify-otp');

});


