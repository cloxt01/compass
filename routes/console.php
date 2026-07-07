<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:apply-scheduler')
    ->everyFiveMinutes();
Schedule::command('billing:renew')
    ->everyTenMinutes();

Schedule::command('billing:grace')
    ->hourly();

Schedule::command('billing:expired')
    ->hourly();
