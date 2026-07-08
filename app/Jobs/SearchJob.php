<?php

namespace App\Jobs;

use App\Infrastructure\Factory\PlatformFactory;
use App\Models\Round;
use App\Models\User;
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

            foreach (self::ALLOWED_PROVIDER as $provider) {
                $providerConfig = $conf[$provider] ?? [];

                if (!($providerConfig['enabled'] ?? false)) {
                    continue;
                }

                $adapter = PlatformFactory::make($provider, $user);

                if (!$adapter) {
                    Log::warning("User {$user->id}: provider {$provider} belum terhubung.");
                    continue;
                }

                // Ambil daftar kata kunci
                $keywords = $providerConfig['keyword'] ?? [];

                if (!is_array($keywords) || empty($keywords)) {
                    Log::warning("User {$user->id}: {$provider} tidak memiliki keyword.");
                    continue;
                }

                // Rotasi keyword tunggal berdasarkan round_id (menggunakan modulus)
                $keywordCount = count($keywords);
                $keyword = $keywords[($this->round_id - 1) % $keywordCount];

                // Parameter request GlintsHelper, typo =g> sudah diperbaiki ke =>
                $params = match ($provider) {
                    'jobstreet' => [
                        'keyword'  => $keyword,
                        'pageSize' => $providerConfig['batch'] ?? 1,
                    ],

                    'glints' => [
                        'SearchTerm'  => $keyword,
                        'LocationIds' => $providerConfig['location_ids'] ?? [],
                        'pageSize'    => $providerConfig['batch'] ?? 1,
                    ],

                    default => [],
                };

                $jobs = $adapter->job()->search($params);

                if (empty($jobs)) {
                    Log::info("User {$user->id}: {$provider} tidak menemukan lowongan untuk keyword '{$keyword}'.");
                    log::info("Data : ", $jobs);
                    continue;
                }

                foreach ($jobs as $job) {
                    if(!isset($job['id']) || empty($job['id'])) {
                        Log::warning("User {$user->id}: {$provider} job tidak memiliki ID.", [
                            'job' => $job,
                        ]);
                        continue;
                    }

                    $job = $adapter->job()->loadJob($job['id']);

                    if(!$job || empty($job)) {
                        Log::warning("User {$user->id}: {$provider} job tidak memiliki detail.", [
                            'job' => $job,
                        ]);
                        continue;
                    }

                    Log::info("Job : ", $job);
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
