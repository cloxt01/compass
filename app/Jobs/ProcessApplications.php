<?php

namespace App\Jobs;

use App\Clients\GlintsAPI;
use App\Events\JobStatus;
use App\Models\User;
use App\Support\ApplicationHelper;
use App\Support\ProviderHelper;
use Illuminate\Queue\SerializesModels;
use App\Clients\Application\UseCase\ApplyUseCase;
use App\Exceptions\CantApply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Clients\JobstreetAPI;
use App\Services\Adapters\GlintsAdapter;
use App\Services\Adapters\JobstreetAdapter;

class ProcessApplications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 1. UBAH NAMANYA DI SINI
    public array $jobData;
    public User $user;

    protected string $job_id;
    protected string $provider;

    public function __construct(User $user, string $provider, array $job)
    {
        $this->user = $user;
        $this->provider = $provider;

        // 2. SIMPAN KE VARIABEL BARU
        $this->jobData = $job;
        $this->job_id = $job['metadata']['id'] ?? '';
        Log::info($this->job_id);
    }

    public function handle()
    {
        if ($this->user->automation_paused) {
            Log::warning('Automation paused');
            return;
        }

        $account = match ($this->provider) {
            'glints' => $this->user->glintsAccount,
            'jobstreet' => $this->user->jobstreetAccount,
        };

        if (!$account) {
            Log::error("Account tidak ditemukan untuk provider: {$this->provider}");
            return;
        }

        $adapter = match ($this->provider) {
            'glints' => new GlintsAdapter(new GlintsAPI($account->access_token, $account->cookie)),
            'jobstreet' => new JobstreetAdapter(new JobstreetAPI($account->access_token)),
        };

        $providerName = ProviderHelper::who($account);

        $is_already = ApplicationHelper::alreadyApplied($this->user->id, $this->job_id);

        if ($is_already) {
            Log::info('Already applied : ', (array) json_encode($this->jobData));
            if($is_already === 'success') {
                JobStatus::dispatch($this->user->id, $this->jobData, $providerName, 'applied');
                return;
            }
            JobStatus::dispatch($this->user->id, $this->jobData, $providerName, $is_already);
            return;
        }

        // 4. GUNAKAN $jobData DI SINI
        JobStatus::dispatch($this->user->id, $this->jobData, $providerName, 'start');

        try {
            $result = (new ApplyUseCase($adapter, $account))->apply($this->job_id);

            // 5. GUNAKAN $jobData DI SINI
            JobStatus::dispatch($this->user->id, $this->jobData, $providerName, $result['status']);

            $this->user->applications()->create([
                'job_id' => $result['job']['job_id'] ?? $this->job_id,
                'job_title' => $result['job']['job_title'] ?? 'Unknown',
                'job_company' => $result['job']['job_company'] ?? 'Unknown',
                'provider' => $result['provider'] ?? 'Unknown',
                'status' => $result['status']
            ]);

            Log::info("ID Lamaran: " . $this->job_id . " Berhasil Dilamar: " . ($result['success'] ? "Ya" : "Tidak"));
        } catch (CantApply $e) {
            Log::info($e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            throw $e;
        }
    }
}
