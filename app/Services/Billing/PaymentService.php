<?php

namespace App\Services\Billing;

use App\Infrastructure\Contracts\PaymentGateway;
use App\Infrastructure\Factory\PaymentGatewayFactory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Notifications\BillingNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected PaymentGateway $gateway;

    public function __construct(
        protected PaymentGatewayFactory $factory
    ) {
        $this->gateway = $this->factory->make();
    }

    /**
     * Membuat transaksi pembayaran.
     */
    public function create(Invoice $invoice): array
    {
        return DB::transaction(function () use ($invoice) {

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'gateway' => (string) $this->gateway->name(),
                'method' => 'QRIS',
                'reference' => $invoice->invoice_number,
                'amount' => $invoice->total,
                'status' => Payment::STATUS_PENDING,
            ]);

            $response = $this->gateway->charge($payment);

            $payment->update([
                'gateway_transaction_id' => $response['transaction_id'],
                'method'                 => $response['method'],
                'redirect_url'            => $response['redirect_url'],
            ]);

            return [

                'snap_token' => $response['token'] ?? $response['snap_token'] ?? null,
                'payment' => $payment->fresh(),
                'redirect_url' => $response['redirect_url'],
            ];
        });
    }

    /**
     * Callback dari gateway.
     */
    public function callback(array $payload): void
    {
        Log::info($this->gateway->name().' webhook', $payload);

        if (!$this->gateway->verify($payload)) {
            abort(403);
        }

        Log::info("Webhook verified");
        Log::info('Order ID', [
            'order_id' => $payload['order_id'] ?? null,
        ]);


        $payment = Payment::where(
            'reference',
            $payload['order_id']
        )->firstOrFail();

        Log::info('Payment exists', [
            'exists' => Payment::where('reference', $payload['order_id'] ?? '')->exists(),
        ]);
        if ($payment->status === Payment::STATUS_PAID) {
            return;
        }

        if ((int) round($payload['gross_amount']) !== $payment->amount) {
            abort(400);
        }

        switch ($payload['transaction_status']) {

            case 'settlement':
            case 'capture':

                $this->paid($payment);

                break;

            case 'expire':

                $this->expired($payment);

                break;

            case 'cancel':

                $this->cancelled($payment);

                break;

            case 'deny':
            case 'failure':

                $this->failed($payment);

                break;
        }
    }
    public function paid(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {

            $payment->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
            ]);

            $invoice = $payment->invoice;

            $invoice->update([
                'status' => Invoice::STATUS_PAID,
                'paid_at' => now(),
            ]);
            $invoice->subscription->user->notify(new BillingNotification($invoice));

            $subscription = $invoice->subscription;

            $subscription->update([
                'status' => Subscription::STATUS_ACTIVE,
            ]);
        });
    }

    public function failed(Payment $payment): void
    {
        $payment->update([
            'status' => Payment::STATUS_FAILED,
        ]);
    }

    public function expired(Payment $payment): void
    {
        $payment->update([
            'status' => Payment::STATUS_EXPIRED,
        ]);
    }

    public function cancelled(Payment $payment): void
    {
        $payment->update([
            'status' => Payment::STATUS_CANCELLED,
        ]);
    }

    public function refund(Payment $payment): void
    {
        $this->gateway->refund($payment);

        $payment->update([
            'status' => Payment::STATUS_REFUND,
        ]);
    }

}
