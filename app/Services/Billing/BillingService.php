<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Package;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected InvoiceService $invoiceService,
        protected PaymentService $paymentService,
        protected UsageService $usageService,
    ) {
    }

    public function subscribe(
        User $user,
        Package $package
    ): array
    {
        return DB::transaction(function () use (
            $user,
            $package
        ) {

            $subscription = $this->subscriptionService
                ->create($user, $package);
            $usage = $this->usageService->create($subscription);

            $invoice = $this->invoiceService
                ->generate($subscription);

            $payment = $this->paymentService
                ->create($invoice);


            return [

                'subscription' => $subscription,

                'invoice' => $invoice,

                'payment' => $payment['payment'],

                'snap_token' => $payment['snap_token'],

                'redirect_url' => $payment['redirect_url'],

            ];

        });
    }

    public function renew(): void
    {
        Subscription::query()

            ->where('status', 'active')

            ->whereHas('package', function ($query) {
                $query->where('code', '!=', Package::CODE_FREE);
            })

            ->where('auto_renew', true)

            ->where('next_billing_at', '<=', now())

            ->chunkById(100, function ($subscriptions) {

                foreach ($subscriptions as $subscription) {

                    DB::transaction(function () use ($subscription) {

                        $invoice = $this->invoiceService
                            ->generate($subscription);

                        $this->paymentService
                            ->create($invoice);

                        $subscription->update([

                            'next_billing_at' => $subscription
                                ->next_billing_at
                                ->addDays(
                                     ((int) $subscription->package->duration_days)
                                )

                        ]);

                    });

                }

            });
    }

    public function grace(): void
    {
        Invoice::query()

            ->whereIn('status', [
                'pending',
                'overdue',
            ])

            ->whereHas('subscription', function ($query) {
                $query->whereHas('package', function ($query) {
                    $query->where('code', '!=', Package::CODE_FREE);
                });
            })
            ->whereDate('due_date', '<', today())

            ->chunkById(100, function ($invoices) {

                foreach ($invoices as $invoice) {

                    DB::transaction(function () use ($invoice) {


                        $invoice->update([

                            'status' => Invoice::STATUS_OVERDUE,

                        ]);

                        $invoice
                            ->subscription
                            ->update([
                                'status' => Subscription::STATUS_GRACE,
                            ]);

                    });

                }

            });
    }
    public function expired(): void
    {
        Invoice::query()

            ->where('status', 'overdue')

            ->whereHas('subscription', function ($query) {
                $query->whereHas('package', function ($query) {
                    $query->where('code', '!=', Package::CODE_FREE);
                });
            })
            ->whereDate(
                'due_date',
                '<=',
                now()->subDays(7)
            )

            ->chunkById(100, function ($invoices) {

                foreach ($invoices as $invoice) {

                    DB::transaction(function () use ($invoice) {

                        $invoice
                            ->subscription
                            ->update([

                                'status' => Subscription::STATUS_EXPIRED,

                            ]);

                    });

                }

            });
    }


}
