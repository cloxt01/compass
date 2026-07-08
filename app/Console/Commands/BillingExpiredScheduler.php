<?php

namespace App\Console\Commands;

use App\Services\Billing\BillingService;
use Illuminate\Console\Command;

class BillingExpiredScheduler extends Command
{
    protected $signature = 'billing:expired';

    protected $description = 'Invoice expired process';

    public function handle(
        BillingService $billing
    )
    {
        $billing->expired();

        return self::SUCCESS;
    }
}
