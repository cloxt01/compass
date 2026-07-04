<?php

namespace App\Console\Commands;

use App\Jobs\SearchJob;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Console\Command;

class ApplyScheduler extends Command
{
    protected $signature = 'app:apply-scheduler';
    protected $description = 'Scheduler auto apply jobs untuk semua user aktif';

    public function handle()
    {
        User::with(['glintsAccount', 'jobstreetAccount'])
            ->where('automation_paused', '!=', 1)
            ->chunk(50, function ($users) {
                foreach ($users as $user) {
                    SearchJob::dispatch($user->id);
                }
            });

        Schedule::updateOrInsert(
            [
                'signature' => $this->signature
            ],
            [
            'last_run' => now(),
            'next_run' => now()->addMinutes(5),
            'last_status' => 'success',
        ]);
        $this->info("[".now()->toDateTimeString()."]".'Scheduler auto apply berhasil dijalankan.');
    }
}
