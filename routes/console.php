<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Apply scheduler
Schedule::command('app:apply-scheduler')
    ->everyFiveMinutes();

// Biling scheduler
Schedule::command('billing:renew')
    ->everyTenMinutes();
Schedule::command('billing:grace')
    ->hourly();
Schedule::command('billing:expired')
    ->hourly();
