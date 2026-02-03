<?php

use Illuminate\Http\Request;
use App\Jobs\Job;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ExternalAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/job-status/{jobId}', [Job::class, 'job_status'])->name('api.job_status');
// --------------------
// Internal API (stateless, JSON only)
// --------------------
Route::prefix('internal')->group(function() {
    Route::post('/login/send-otp', [InternalController::class, 'send_login_otp'])->name('internal.send_login_otp');
    Route::post('/login/check-otp', [InternalController::class, 'check_login_otp'])->name('internal.check_login_otp');
    Route::post('/login/update-otp-consumed', [InternalController::class, 'update_otp_consumed'])->name('internal.update_otp_consumed');
    Route::post('/login/update-status', [InternalController::class, 'update_login_status'])->name('internal.update_login_status');
    Route::post('/add-token', [InternalController::class, 'add_token'])->name('internal.add_token');
});

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
    Route::post('/{provider}/send-otp', [ExternalAccountController::class, 'send_otp'])->name('api.external.send_otp');
    Route::get('/{provider}/verify-otp', [AuthController::class, 'verify_otp'])->name('api.external.verify_otp');

});


