<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment gateway that will be used
    | by the application. You may set this to any of the gateways
    | defined in your application.
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'midtrans'),
];
