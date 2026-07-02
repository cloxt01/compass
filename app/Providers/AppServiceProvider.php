<?php

namespace App\Providers;


use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobQueued;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
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

         if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        Queue::after(function (JobProcessed $event) {
            $jobId = $event->job->getJobId();
            DB::table('apply_queue')->where('job_id', $jobId)->delete();
        });
    }
}
