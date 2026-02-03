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
    Route::post('/{provider}/send-otp', [ExternalAccountController::class, 'send_otp'])->name('api.external.send-otp');
    Route::get('/{provider}/verify-otp', [ExternalAccountController::class, 'verify_otp'])->name('api.external.verify-otp');

});


