<?php

namespace App\Jobs;

use App\Clients\Application\UseCase\ApplyUseCase;
use App\Clients\GlintsAPI;
use App\Clients\JobstreetAPI;
use App\Events\JobStatus;
use App\Exceptions\CantApply;
use App\Models\User;
use App\Services\Adapters\Provider\GlintsAdapter;
use App\Services\Adapters\Provider\JobstreetAdapter;
use App\Services\Billing\SubscriptionService;
use App\Services\Billing\UsageService;
use App\Support\ApplicationHelper;
use App\Support\ProviderHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessApplications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $jobData;
    public User $user;

    public int $user_id;

    protected string $job_id;
    protected string $provider;
    private UsageService $usageService;

    public function __construct(User $user, string $provider, array $job)
    {
        $this->usageService = new UsageService(
            new SubscriptionService()
        );

        $this->user = $user;
        $this->user_id = $this->user->id;

        $this->provider = $provider;

        // 2. SIMPAN KE VARIABEL BARU
        $this->jobData = $job;
        $this->job_id = $job['metadata']['id'] ?? '';
        Log::info($this->job_id);
    }

    public function middleware(): array
    {
        return [
            new Middleware\CheckSubscription(),
            new Middleware\CheckProvider()
            // new Middleware\CheckRateLimit()
        ];
    }
    public function handle()
    {
        // Untuk middleware

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
        $providerName = ProviderHelper::who($account);


        $adapter = match ($this->provider) {
            'glints' => new GlintsAdapter(new GlintsAPI($account->access_token, $account->cookie)),
            'jobstreet' => new JobstreetAdapter(new JobstreetAPI($account->access_token)),
        };

        $limit = $adapter->isLimit($this->job_id);

        Log::info("User ID : " . $this->user->id. ', Provider : ' . $this->provider . ', Job ID : ' . $this->job_id . ', Limit Provider : ' . ($limit ? 'Ya' : 'Tidak'));

        if($limit){
            JobStatus::dispatch($this->user->id, $this->jobData, $providerName, 'limit_provider');
            Log::warning('User ID : ' . $this->user->id. ', dilewati karena limit provider tercapai.');
            return;
        }

        $is_already = ApplicationHelper::alreadyApplied($this->user->id, $this->job_id);

        if ($is_already) {
            Log::warning('User ID : ' . $this->user->id. ' - Provider : ' . $this->provider . ' - Job ID : ' . $this->job_id . ', dilewati karena sudah melamar.' . json_encode($this->jobData));
            
            switch ($is_already) {
                case 'applied':
                case 'success':
                    JobStatus::dispatch($this->user->id, $this->jobData, $providerName, 'applied');
                    return;
                case 'expired':
                    JobStatus::dispatch($this->user->id, $this->jobData, $providerName, $is_already);
                    return;
                default:
                    break;
            }
            // if(in_array($is_already, ['applied', 'success'])) {
            //     JobStatus::dispatch($this->user->id, $this->jobData, $providerName, 'applied');
            //     return;
            // } 
            // JobStatus::dispatch($this->user->id, $this->jobData, $providerName, $is_already);
            // return;
        }

        // 4. GUNAKAN $jobData DI SINI
        JobStatus::dispatch($this->user->id, $this->jobData, $providerName, 'start');

        try {
            $result = (new ApplyUseCase($adapter, $account))->apply($this->job_id);


            $subscription = $this->user
                ->getLastActiveSubscription();
            if (!$subscription) {
                Log::warning('Langganan aktif tidak ditemukan, User ID : '. $this->user->id);
            }

            // Usage Subscription
            $usage = $subscription->usages()->whereDate('date', today())->first();
            $apply_count = $usage->apply_count ?? 0;

            Log::info("Usage [B] : ". $apply_count . " , Result [{$result['status']} : ");
            if(in_array($result['status'], ['success', 'applied'])) {
                $this->usageService->increment($subscription);
                $usage->refresh();
            }
            Log::info("Usage [A] : ". $apply_count ?? 0 . " , Result [{$result['status']} : ");

            // Record Application
            JobStatus::dispatch($this->user->id, $this->jobData, $providerName, $result['status']);
            $application = $this->user->applications()->updateOrCreate([
                'job_id' => $result['job']['job_id'] ?? $this->job_id
            ], [
                'job_title' => $result['job']['job_title'] ?? 'Unknown',
                'job_company' => $result['job']['job_company'] ?? 'Unknown',
                'provider' => $result['provider'] ?? 'Unknown',
                'status' => $result['status']
            ]);

            // Link AI answer history ke application (kalau ada)
            $aiHistoryId = $result['ai_answer']['history_id'] ?? null;
            if ($aiHistoryId) {
                \App\Models\ApplicationAiAnswer::where('id', $aiHistoryId)
                    ->update(['application_id' => $application->id]);
            }

            Log::info("ID Lamaran: " . $this->job_id . " Berhasil Dilamar: " . ($result['success'] ? "Ya" : "Tidak"));
        } catch (CantApply $e) {
            if($e->getCode() === 409) {
                JobStatus::dispatch($this->user->id, $this->jobData, $providerName, "resume");
            }
            Log::warning("Code : ". $e->getCode());
            Log::warning($e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            throw $e;
        }
    }
}
