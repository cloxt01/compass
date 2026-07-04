<?php

namespace App\Jobs;

use App\Infrastructure\Factory\PlatformFactory;
use App\Models\User;
use App\Services\JobDetails;
use App\Services\Queue\ApplicationQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SearchJob implements ShouldQueue
{
    use Queueable;

    protected int $user_id;

    private const ALLOWED_PROVIDER = ['glints', 'jobstreet'];
    /**
     * Create a new job instance.
     */
    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->user_id);

        if (!$user) {
            Log::warning("User {$this->user_id} tidak ditemukan.");
            return;
        }

        $conf = $user->apply_configuration ?? [];
        $selectedProviders = $conf['providers'] ?? [];

        if (empty($selectedProviders)) {
            Log::warning("User ID : {$user->id}, Belum mengatur konfigurasi provider.");
            return;
        }

        foreach ($selectedProviders as $provider) {
            if(!in_array($provider, self::ALLOWED_PROVIDER)) {
                Log::error("User ID : {$user->id}, Provider tidak dikenali '{$provider}'");
                continue;
            }

            $adapter = PlatformFactory::make($provider, $user);
            if (!$adapter) {
                Log::error("User ID : {$user->id}, Belum menghubungkan provider '{$provider}'");
                continue;
            }

            $params = match ($provider) {
                'jobstreet' => [
                    'keyword' => (string)($conf['keyword'] ?? ''),
                    'pageSize' => (int)($conf['batch'] ?? 0),
                ],
                'glints' => [
                    'SearchTerm' => (string)($conf['keyword'] ?? ''),
                    'LocationIds' => (array)$user->glintsAccount->getConfig('location_ids', []),
                    'pageSize' => (int)($conf['batch'] ?? 1),
                ],
                default => throw new \InvalidArgumentException("Unknown provider '{$provider}'"),
            };
            $jobs = $adapter->job()->search($params);

            $ok = $jobs['ok'] ?? false;
            if(!$ok || !isset($jobs['data'])) {
                Log::warning("User ID : {$user->id}, Provider tidak menampilkan data {$provider}.");
                continue;
            }

            $data = match ($provider) {
                'jobstreet' => $jobs['data']['data'] ?? [],
                'glints' => $jobs['data']['searchJobsV3']['jobsInPage'] ?? [],
            };

            if(empty($data)) {
                Log::warning("User ID : {$user->id}, Tidak ada job yang ditemukan untuk provider '{$provider}'");
                continue;
            }

            foreach ($data as $details) {
                $payload = [
                    'details' => $details,
                ];
                $job = match ($provider) {
                    'jobstreet' => JobDetails::fromJobstreet($payload),
                    'glints'    => JobDetails::fromGlints($payload),
                };

                ApplicationQueue::dispatch(
                    $user,
                    $provider,
                    $job
                );
            }
        }
    }
}
