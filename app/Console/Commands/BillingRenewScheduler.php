<?php

namespace App\Console\Commands;

use App\Services\Billing\BillingService;
use Illuminate\Console\Command;

class BillingRenewScheduler extends Command
{
    protected $signature = 'billing:renew';

    protected $description = 'Generate renewal invoices';

    public function handle(
        BillingService $billing
    )
    {
        $billing->renew();

        return self::SUCCESS;
    }
}
