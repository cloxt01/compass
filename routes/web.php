<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

Route::get('/', function () {
    return view('landing');
});

Route::middleware(['auth','verified'])->group(function() {
    Route::get('/user/queue/status', [UserController::class, 'queue_status'])->name('user.queue.status');
    Route::get('/user/applications', [UserController::class, 'application_stats'])->name('user.applications');

    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/apply', [ApplyController::class, 'index'])->name('apply');

    Route::prefix('settings')->group(function() {
        Route::get('/', [SettingsController::class, 'settings'])->name('settings');
    });
    Route::prefix('connection')->group(function() {
        Route::get('/connect/jobstreet', fn() => view('connection.jobstreet'))->name('connection.jobstreet');
        Route::get('/connect/glints', fn() => view('connection.glints'))->name('connection.glints');
        Route::post('/{provider}/save-token', [ConnectionController::class, 'save_token'])->name('connection.save-token');
        Route::get('/{provider}/disconnect', [ConnectionController::class, 'disconnect'])->name('api.connection.disconnect');
        Route::post('/{provider}/save-config', [ConnectionController::class, 'save_config'])->name('connection.save-config');
    });

});


require __DIR__.'/auth.php';
