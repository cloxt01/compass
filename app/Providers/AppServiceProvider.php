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
       
    }
}
