<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

Route::get('/debug/test', function() {
    return response()->json([
        'ok' => true,
        'session_id' => session()->getId(),
        'user' => auth()->user()?->id,
    ]);
});

Route::get('/debug/routes', function() {
    return collect(\Route::getRoutes())->map(function($route) {
        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'methods' => $route->methods(),
            'middleware' => $route->middleware(),
        ];
    });
});
Route::get('/debug/redis', function () {
    Redis::select(1);
    Redis::set('test', 'ok');
    Redis::rpush('queue:test', 'item1');
    $list = Redis::lrange('queue:test', 0, -1);
    return $list;
});

Route::middleware('auth')->group(function() {
    Route::get('/user/queue/status', [UserController::class, 'queue_status'])->name('user.queue.status');
    Route::get('/user/applications', [UserController::class, 'application_stats'])->name('user.applications');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/apply', [ApplyController::class, 'index'])->name('apply');
    Route::get('/profile', function(){return view('profile');})->name('profile');

    Route::prefix('platform')->group(function() {
        Route::get('/connect/jobstreet', fn() => view('platform.jobstreet'))->name('platform.connect.jobstreet');
        Route::get('/connect/glints', fn() => view('platform.glints'))->name('platform.connect.glints');
        Route::get('/{provider}/disconnect', [PlatformController::class, 'disconnect'])->name('platform.disconnect');
        Route::post('/{provider}/save-token', [PlatformController::class, 'save_token'])->name('platform.save-token');
        Route::post('/{provider}/save-config', [PlatformController::class, 'save_config'])->name('platform.save-config');
    });

});


require __DIR__.'/auth.php';
