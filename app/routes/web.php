<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelController;
use Illuminate\Support\Facades\Route;

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
    \Illuminate\Support\Facades\Redis::set('test', 'ok');
    return \Illuminate\Support\Facades\Redis::get('test');
});

// Guest routes
Route::middleware('guest')->group(function() {
    Route::get('/login', fn() => view('login'))->name('login');
    Route::get('/register', fn() => view('register'))->name('register');
});

// Authenticated routes
Route::middleware('auth')->group(function() {
    Route::get('/', fn() => view('dashboard'))->name('dashboard');
    Route::get('/panel', function() {
        $user = auth()->user();
        dump($user);
        return view('panel');
    })->name('panel');

    Route::get('/external', fn() => view('external.index'))->name('external.index');

    Route::prefix('external')->group(function() {
        Route::get('/connect/jobstreet', fn() => view('external.jobstreet'))->name('external.connect.jobstreet');
        Route::get('/connect/glints', fn() => view('external.glints'))->name('external.connect.glints');
    });
});

// Web-based auth actions
Route::prefix('auth')->group(function() {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
