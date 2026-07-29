<?php

namespace App\Gateways;

use App\Infrastructure\Contracts\PaymentGateway;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransGateway implements PaymentGateway
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');

        Config::$clientKey = config('midtrans.client_key');

        Config::$isProduction = config('midtrans.is_production');

        Config::$isSanitized = config('midtrans.is_sanitized');

        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function name(): string
    {
        return 'midtrans';
    }
    public function callback(array $payload): array
    {
        return [
            'reference' => $payload['order_id'],
            'amount' => (int) round($payload['gross_amount']),
            'status' => match ($payload['transaction_status']) {
                'settlement', 'capture' => Payment::STATUS_PAID,
                'expire' => Payment::STATUS_EXPIRED,
                'cancel' => Payment::STATUS_CANCELLED,
                'deny', 'failure' => Payment::STATUS_FAILED,
                default => Payment::STATUS_PENDING,
            },
        ];
    }

    public function charge(Payment $payment): array
    {
        $invoice = $payment->invoice;
        $subscription = $invoice->subscription;
        $user = $subscription->user;

        $params = [

            'transaction_details' => [

                'order_id' => $invoice->invoice_number,
                'gross_amount' => $payment->amount,
            ],
            'callbacks' => [
                'finish' => route('payment.finish'),
            ],
            'customer_details' => [

                'first_name' => $user->name,

                'email' => $user->email,

            ],

            'item_details' => [

                [

                    'id' => $subscription->package_id,

                    'price' => $payment->amount,

                    'quantity' => 1,

                    'name' => $subscription->package->name

                ]

            ]

        ];

        $snap = Snap::createTransaction($params);

        return [

            'order_id' => $payment->reference,

            'transaction_id' => $snap->token,

            'reference' => $payment->reference,

            'method' => 'snap',

            'snap_token' => $snap->token,

            'redirect_url' => $snap->redirect_url,

        ];
    }

    public function verify(array $payload): bool
    {
        $expected = hash(
            'sha512',
            $payload['order_id']
            . $payload['status_code']
            . $payload['gross_amount']
            . config('midtrans.server_key')
        );

        return hash_equals(
            $expected,
            $payload['signature_key']
        );
    }

    public function refund(Payment $payment): void
    {
        //
    }
}
