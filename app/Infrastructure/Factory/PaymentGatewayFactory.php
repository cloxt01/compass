<?php

namespace App\Infrastructure\Factory;

use App\Gateways\MidtransGateway;
use App\Gateways\TripayGateway;
use App\Infrastructure\Contracts\PaymentGateway;

class PaymentGatewayFactory
{
    public function make(): PaymentGateway
    {
        return match (config('payment.default')) {
            'midtrans' => app(MidtransGateway::class),
            'tripay'   => app(TripayGateway::class),

            default => throw new \RuntimeException('Gateway tidak didukung'),
        };
    }
}