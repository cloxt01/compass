<?php

namespace App\Jobs;

use App\Infrastructure\Factory\PlatformFactory;
use App\Models\Round;
use App\Models\User;
use App\Services\JobDetails;
use App\Services\Queue\ApplicationQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

class SearchJob implements ShouldQueue
{
    use Queueable;

    protected int $user_id;
    protected int $round_id;

    private const ALLOWED_PROVIDER = ['glints', 'jobstreet'];
    /**
     * Create a new job instance.
     */
    public function __construct(int $user_id, int $round_id)
    {
        $this->user_id = $user_id;
        $this->round_id = $round_id;
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("search-user-{$this->user_id}"))->expireAfter(300),
        ];
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $hasSuccess = false;

        try {

            $user = User::find($this->user_id);
            if (! $user) {
                Log::warning('User tidak ditemukan.', [
                    'user_id' => $this->user_id,
                    'round_id' => $this->round_id,
                ]);

                Round::whereKey($this->round_id)->increment('total_failed');

                $this->checkRoundCompleted();

                return;
            }

            $conf = $user->apply_configuration ?? [];
            $selectedProviders = $conf['providers'] ?? [];

            if (empty($selectedProviders)) {
                Log::warning('Belum ada provider yang dipilih.', [
                    'user_id' => $user->id,
                    'round_id' => $this->round_id,
                ]);
                Round::whereKey($this->round_id)->increment('total_failed');

                $this->checkRoundCompleted();
                return;
            }

            foreach ($selectedProviders as $provider) {
                if(!in_array($provider, self::ALLOWED_PROVIDER, true)) {
                    Log::error("User ID : {$user->id}, Provider tidak dikenali '{$provider}'");
                    continue;
                }

                $adapter = PlatformFactory::make($provider, $user);
                if (!$adapter) {
                    Log::warning("User ID : {$user->id}, Belum menghubungkan provider '{$provider}'");
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
                        $job,
                        $this->round_id,
                    );
                }
                $hasSuccess = true;
            }

            if ($hasSuccess) {
                Round::whereKey($this->round_id)->increment('total_success');
            } else {
                Round::whereKey($this->round_id)->increment('total_failed');
            }
            $this->checkRoundCompleted();


        } catch (Throwable $e) {
            if (! $hasSuccess) {
                Round::whereKey($this->round_id)->increment('total_failed');
            }

            Log::error('SearchJob exception', [
                'round_id' => $this->round_id,
                'user_id'  => $this->user_id,
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ]);

            $this->checkRoundCompleted();

            throw $e;
        }

    }
    private function checkRoundCompleted(): void
    {
        Round::query()
            ->whereKey($this->round_id)
            ->where('status', '!=', 'completed')
            ->whereRaw('(total_success + total_failed) >= total_dispatched')
            ->update([
                'status' => 'completed',
                'finished_at' => now(),
            ]);
    }
}
