<?php

use App\Http\Controllers\PaymentController;

Route::post('payment/webhook', [
    PaymentController::class,
    'webhook'
])->name('payment.webhook');
