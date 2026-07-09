<?php

namespace App\Providers;


use App\Infrastructure\Contracts\PaymentGateway;
use App\Gateways\MidtransGateway;
use App\Models\GlintsAccount;
use App\Models\JobstreetAccount;
use App\Models\User;
use App\Observers\ProviderAccountObserver;
use App\Observers\ProviderTokenObserver;
use App\Observers\UserObserver;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PaymentGateway::class,
            MidtransGateway::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        GlintsAccount::observe(ProviderAccountObserver::class);
        JobstreetAccount::observe(ProviderAccountObserver::class);
        JobstreetAccount::observe(ProviderTokenObserver::class);

         if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        Queue::after(function (JobProcessed $event) {
            $jobId = $event->job->getJobId();
            DB::table('apply_queue')->where('job_id', $jobId)->delete();
        });

    }
}
