<?php

namespace App\Infrastructure\Contracts;

use App\Models\Payment;

interface PaymentGateway
{
    public function name(): string;

    public function charge(Payment $payment): array;

    public function refund(Payment $payment): void;

    public function verify(array $payload): bool;
}
