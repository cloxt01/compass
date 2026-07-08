<?php

namespace App\Console\Commands;

use App\Services\Billing\BillingService;
use Illuminate\Console\Command;

class BillingGraceScheduler extends Command
{
    protected $signature = 'billing:grace';

    protected $description = 'Grace invoices overdue';

    public function handle(
        BillingService $billing
    )
    {
        $billing->grace();

        return self::SUCCESS;
    }
}
