<?php

use App\Http\Controllers\AuthController;
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
    Route::get('/user/queue/status', function () {
        $userId = auth()->id();

        $pending = DB::table('jobs')
            ->where('user_id', $userId)
            ->whereNull('reserved_at')
            ->count();

        $processing = DB::table('jobs')
            ->where('user_id', $userId)
            ->whereNotNull('reserved_at')
            ->count();

        $failed = DB::table('failed_jobs')
            ->where('user_id', $userId)
            ->count();

        $recentJobs = DB::table('jobs')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'attempts', 'reserved_at', 'created_at']);

        return response()->json([
            'pending' => $pending,
            'processing' => $processing,
            'failed' => $failed,
            'recent' => $recentJobs,
            'updated_at' => now()->toDateTimeString()
        ]);
    })->name('user.queue.status');
    Route::get('/user/jobs/status', function () {
        $userId = auth()->id();

        // 1. Ambil Statistik Gabungan (Glints + JobStreet) menggunakan Subquery Union
        $stats = DB::table(DB::raw("(
        SELECT status, user_id FROM glints_applications
        UNION ALL
        SELECT status, user_id FROM jobstreet_applications
    ) as combined_apps"))
            ->where('user_id', $userId)
            ->selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(status = 'success'), 0) as success,
            COALESCE(SUM(status = 'applied'), 0) as applied,
            COALESCE(SUM(status = 'linkout'), 0) as linkout,
            COALESCE(SUM(status = 'questionnaire'), 0) as questionnaire
        ")
            ->first();

        $glints = DB::table('glints_applications')
            ->where('user_id', $userId)
            ->select('job_id', 'status', 'updated_at', DB::raw("'glints' as provider"));

        $jobstreet = DB::table('jobstreet_applications')
            ->where('user_id', $userId)
            ->select('job_id', 'status', 'updated_at', DB::raw("'jobstreet' as provider"));

        $recent = $glints->unionAll($jobstreet)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent' => $recent,
            'updated_at' => now()->toDateTimeString(),
        ]);
    })->name('user.jobs.status');

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


// Platform routes

// Web-based auth actions
Route::prefix('auth')->group(function() {
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
