<?php

namespace App\Providers;

use App\Models\JobstreetAccount;
use App\Observers\JobstreetAccountObserver;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JobstreetAccount::observe(JobstreetAccountObserver::class);

         if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        // Ensure `routes/api.php` is loaded (project didn't have RouteServiceProvider)
        if (file_exists(base_path('routes/api.php'))) {
            Route::prefix('api-v1')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        }
        if (file_exists(base_path('routes/web.php'))) {
            Route::group(['middleware' => ['web']], function () {
                require base_path('routes/web.php');
            });
        }
    }
}
