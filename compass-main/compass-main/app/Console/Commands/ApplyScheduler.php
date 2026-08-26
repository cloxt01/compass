<?php

namespace App\Console\Commands;

use App\Jobs\SearchJob;
use App\Models\Round;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApplyScheduler extends Command
{
    protected $signature = 'app:apply-scheduler';
    protected $description = 'Scheduler auto apply jobs untuk semua user aktif';

    public function handle(): int
    {
        $startedAt = now();
        $dispatched = 0;

        try {
            $round = Round::create([
                'status' => 'pending',
                'started_at' => $startedAt,
            ]);
            User::query()
                ->where('automation_paused', false)
                ->where(function ($query) {
                    $query->has('jobstreetAccount')
                        ->orHas('glintsAccount');
                })
                ->chunkById(50, function ($users) use (&$dispatched, $round) {
                    foreach ($users as $user) {
                        SearchJob::dispatch($user->id, $round->id);
                        $dispatched++;
                    }
                });

            Schedule::updateOrInsert(
                [
                    'signature' => $this->signature,
                ],
                [
                    'last_run'   => $startedAt,
                    'next_run'   => $startedAt->copy()->addMinutes(5),
                    'last_status'=> 'success',
                ]
            );

            $duration = round((microtime(true) - LARAVEL_START) * 1000);

            $round->update([
                'status'            => 'processing',
                'total_user'        => $dispatched,
                'total_dispatched'  => $dispatched,
                'duration_ms'       => $duration,
                'finished_at'       => now(),
            ]);

            Log::info('Apply Scheduler completed', [
                'round_id'          => $round->id,
                'users_dispatched'  => $dispatched,
                'duration_ms'       => $duration,
                'started_at'        => $startedAt,
                'finished_at'       => now(),
            ]);


            $this->info(sprintf(
                '[%s] Scheduler selesai | Jobs: %d | Durasi: %ss',
                $startedAt->toDateTimeString(),
                $dispatched,
                $duration
            ));

            return self::SUCCESS;

        } catch (Throwable $e) {

            Schedule::updateOrInsert(
                [
                    'signature' => $this->signature,
                ],
                [
                    'last_run'    => $startedAt,
                    'last_status' => 'failed',
                ]
            );

            Log::error('Apply Scheduler failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
