<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\Provider\JobstreetAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CareerMatchController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

Route::get('/', function () {
    return view('landing');
});
Route::get('terms', function () {
    return view('terms');
})->name('terms');
Route::get('privacy', function () {
    return view('privacy');
})->name('privacy');

Route::middleware(['auth','verified'])->group(function() {
    Route::get('/user/queue/status', [UserController::class, 'queue_status'])->name('user.queue.status');
    Route::get('/user/applications', [UserController::class, 'application_stats'])->name('user.applications');

    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/apply', [ApplyController::class, 'index'])->name('apply');
    Route::get('/career-match', [CareerMatchController::class, 'index'])->name('career-match');
    Route::post('/career-match/weights', [CareerMatchController::class, 'saveWeights'])->name('career-match.weights');
    Route::post('/career-match/answer', [CareerMatchController::class, 'answer'])->name('career-match.answer');
    Route::post('/career-match/score', [CareerMatchController::class, 'score'])->name('career-match.score');
    Route::get('/career-match/history', [CareerMatchController::class, 'historyIndex'])->name('career-match.history');
    Route::delete('/career-match/history/{id}', [CareerMatchController::class, 'historyDestroy'])->whereNumber('id')->name('career-match.history.destroy');

    Route::prefix('settings')->group(function() {
        Route::get('/', [SettingsController::class, 'settings'])->name('settings');
        Route::post('/ai-provider', [SettingsController::class, 'saveAiProvider'])->name('settings.ai-provider.save');
        Route::post('/ai-provider/test', [SettingsController::class, 'testAiProvider'])->name('settings.ai-provider.test');
    });
    Route::prefix('products')->group(function() {
       Route::get('/compass-link', [ProductController::class, 'compass_link'])->name('products.compass-link');
    });
    Route::prefix('connection')->group(function() {
        Route::get('/connect/jobstreet', fn() => view('connection.jobstreet'))->name('connection.jobstreet');
        Route::get('/connect/glints', fn() => view('connection.glints'))->name('connection.glints');
        Route::post('/{provider}/save-token', [ConnectionController::class, 'save_token'])->name('connection.save-token');
        Route::get('/{provider}/connect', [ConnectionController::class, 'connect'])->name('connection.connect');
        Route::delete('/{provider}/disconnect', [ConnectionController::class, 'disconnect'])->name('api.connection.disconnect');
        Route::post('/{provider}/save-config', [ConnectionController::class, 'save_config'])->name('connection.save-config');
    });

});

require __DIR__.'/auth.php';
