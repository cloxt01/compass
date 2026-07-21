<?php

namespace App\Gateways;

use App\Infrastructure\Contracts\PaymentGateway;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class TripayGateway implements PaymentGateway
{
    protected string $apiKey;
    protected string $privateKey;
    protected string $merchantCode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('tripay.api_key');
        $this->privateKey = config('tripay.private_key');
        $this->merchantCode = config('tripay.merchant_code');

        $this->baseUrl = config('tripay.production')
            ? 'https://tripay.co.id/api'
            : 'https://tripay.co.id/api-sandbox';
    }

    public function name(): string
    {
        return 'tripay';
    }

    public function charge(Payment $payment): array
    {
        $invoice = $payment->invoice;
        $subscription = $invoice->subscription;
        $user = $subscription->user;

        $merchantRef = $invoice->invoice_number;

        $signature = hash_hmac(
            'sha256',
            $this->merchantCode.$merchantRef.$payment->amount,
            $this->privateKey
        );

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
        ])->post($this->baseUrl.'/transaction/create', [

            'method' => $payment->method,
            'merchant_ref'   => $merchantRef,
            'amount'         => $payment->amount,
            'customer_name'  => $user->name,
            'customer_email' => $user->email,

            'order_items' => [
                [
                    'sku'      => $subscription->package->code,
                    'name'     => $subscription->package->name,
                    'price'    => $payment->amount,
                    'quantity' => 1,
                ]
            ],

            'return_url'   => route('payment.finish'),
            'expired_time' => now()->addDay()->timestamp,

            'signature' => $signature,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                $response->json('message') ?? 'Tripay request failed.'
            );
        }

        $data = $response->json('data');

        return [

            'transaction_id' => $data['reference'] ?? null,
            'reference'      => $merchantRef,
            'method'         => 'tripay',

            'redirect_url' => $data['checkout_url'],

            'snap_token' => null,
        ];
    }

    public function callback(array $payload): array
    {
        return [
            'reference' => $payload['merchant_ref'],
            'amount' => (int) $payload['total_amount'],
            'status' => match ($payload['status']) {
                'PAID' => Payment::STATUS_PAID,
                'EXPIRED' => Payment::STATUS_EXPIRED,
                'FAILED' => Payment::STATUS_FAILED,
                default => Payment::STATUS_PENDING,
            },
        ];
    }

    public function verify(array $payload): bool
    {
        $expected = hash_hmac(
            'sha256',
            $payload['merchant_ref'].$payload['status'].$payload['total_amount'],
            $this->privateKey
        );

        return hash_equals(
            $expected,
            $payload['signature']
        );
    }

    public function refund(Payment $payment): void
    {
        //
    }
}