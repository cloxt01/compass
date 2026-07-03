<?php

namespace App\Console\Commands;

use App\Models\ApplyQueue;
use App\Models\Schedule;
use App\Models\User;
use App\Clients\GlintsAPI;
use App\Clients\JobstreetAPI;
use App\Jobs\ProcessApplications;
use App\Models\SchedulerLog;
use App\Services\Adapters\GlintsAdapter;
use App\Services\Adapters\JobstreetAdapter;
use App\Services\JobDetails;
use App\Support\ApplicationHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Util\PHP\Job;

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
                    $conf = $user->apply_configuration ?? [];
                    $selectedProviders = $conf['providers'] ?? [];

                    if (empty($conf) || empty($selectedProviders)) {
                        Log::warning('User ID : '. $user->id. ' belum mengatur konfigurasi');
                        continue;
                    }

                    $accounts = [
                        'glints'    => $user->glintsAccount,
                        'jobstreet' => $user->jobstreetAccount,
                    ];

                    foreach ($accounts as $provider => $account) {
                        if ($account && in_array($provider, $selectedProviders)) {

                            try {
                                $adapter = match ($provider) {
                                    'glints'    => new GlintsAdapter(new GlintsAPI($account->access_token, $account->cookie)),
                                    'jobstreet' => new JobstreetAdapter(new JobstreetAPI($account->access_token)),
                                };
                            } catch (\UnhandledMatchError $e) {
                                SchedulerLog::create([
                                    'message'   => 'Unknown provider: ' . $provider,
                                    'exception' => $e->getMessage(),
                                ]);
                                continue;
                            }

                            $params = match ($provider) {
                                'jobstreet' => [
                                    'keyword'  => (string) ($conf['keyword'] ?? ''),
                                    'pageSize' => (int) ($conf['batch'] ?? 5),
                                ],
                                'glints' => [
                                    'SearchTerm'  => (string) ($conf['keyword'] ?? ''),
                                    'LocationIds' => (array) $account->getConfig('location_ids', []),
                                    'pageSize'    => (int) ($conf['batch'] ?? 5),
                                ]
                            };

                            try {
                                $jobs = $adapter->job()->search($params);

                                $data = match ($provider) {
                                    'jobstreet' => $jobs['data']['data'] ?? [],
                                    'glints'    => $jobs['data']['searchJobsV3']['jobsInPage'] ?? [],
                                };

                                foreach ($data as $job) {
                                    $raw['details'] = $job;
                                    $job = match($provider) {
                                        'jobstreet' => JobDetails::fromJobstreet($raw),
                                        'glints' => JobDetails::fromGlints($raw),
                                    };
                                    Log::info('Scheduler JOB : ');
                                    Log::info(json_encode($job));

                                    $queueId = Queue::connection('database')->push(new ProcessApplications($user, $provider, $job));

                                    ApplyQueue::create([
                                        'job_id' => $queueId,
                                        'user_id' => $user->id,
                                    ]);
                                }

                            } catch (\Exception $e) {
                                Schedule::updateOrInsert(
                                    [
                                        'signature' => $this->signature
                                    ],
                                    [
                                        'last_run' => now(),
                                        'next_run' => now()->addMinutes(10),
                                        'last_status' => 'failed',
                                    ]);
                                Log::error("Scheduler Error untuk User ID {$user->id} di {$provider}: " . $e->getMessage());
                            }
                        }
                    }
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
        $this->info('Scheduler auto apply berhasil dijalankan.');
    }
}
