<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ConnectionController;



Route::middleware([
    'web',
    'auth',
    'verified'
])->group(function () {

    Route::post('/{provider}/search-location', [\App\Http\Controllers\Api\Glints\api::class, 'searchLocation'])->name('api.search.location');
    Route::prefix('connection')->group(function() {
        Route::post('/{provider}/login', [ConnectionController::class, 'login'])->name('api.connection.login');
        Route::post('/{provider}/passwordless-login', [ConnectionController::class, 'passwordless_login'])->name('api.connection.passwordless-login');
        Route::post('/{provider}/verify-otp', [ConnectionController::class, 'verify_otp'])->name('api.connection.verify-otp');
    });
});



