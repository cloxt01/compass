<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\PlatformController;
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

// Guest routes
Route::middleware('guest')->group(function() {
    Route::get('/login', fn() => view('login'))->name('login');
    Route::get('/register', fn() => view('register'))->name('register');
});

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/apply', [ApplyController::class, 'index'])->name('apply');
    Route::get('/profile', function(){return view('profile');})->name('profile');
});


// Platform routes
Route::prefix('platform')->middleware('auth')->group(function() {
    Route::get('/connect/jobstreet', fn() => view('platform.jobstreet'))->name('platform.connect.jobstreet');
    Route::get('/connect/glints', fn() => view('platform.glints'))->name('platform.connect.glints');
});

// Web-based auth actions
Route::prefix('auth')->group(function() {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
