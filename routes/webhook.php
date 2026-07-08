<?php

use App\Http\Controllers\PaymentController;

Route::post('payment/webhook', [
    PaymentController::class,
    'webhook'
])->name('payment.webhook');
Route::get('payment/finish', [
    PaymentController::class,
    'finish'
])->name('payment.finish');
