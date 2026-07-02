<?php


namespace App\Jobs;

use App\Events\JobStatus;
use App\Models\GlintsAccount;
use App\Models\User;
use App\Support\ApplicationHelper;
use App\Support\ProviderHelper;
use Illuminate\Queue\SerializesModels;
use App\Clients\Application\UseCase\ApplyUseCase;
use App\Exceptions\CantApply;
use App\Infrastructure\Contracts\PlatformAccount;
use App\Infrastructure\Contracts\PlatformAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;


class ProcessApplications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $job_id;
    protected PlatformAdapter $adapter;
    protected PlatformAccount $account;
    public User $user;
    public function __construct(User $user, PlatformAdapter $adapter, PlatformAccount $account, string $job_id)
    {
        $this->adapter = $adapter;
        $this->account = $account;
        $this->job_id = $job_id;
        $this->user = $user;
    }



    public function handle()
    {
        if($this->user->automation_paused){
            Log::warning('Automation paused');
            return;
        }

        $is_already = ApplicationHelper::alreadyApplied($this->user->id, $this->job_id);

        if($is_already){
            JobStatus::dispatch($this->user->id, $this->job_id, ProviderHelper::who($this->account), $is_already);
            return;
        }

        JobStatus::dispatch($this->user->id, $this->job_id, ProviderHelper::who($this->account), 'start');

        try {
            $result = (new ApplyUseCase($this->adapter, $this->account))->apply($this->job_id);
            JobStatus::dispatch($this->user->id, $this->job_id, ProviderHelper::who($this->account), $result['status']);
            $this->user->applications()->create(
                [
                    'job_id' => $result['job']['job_id'] ?? $this->job_id,
                    'job_title' => $result['job']['job_title'] ?? 'Unknown',
                    'job_company' => $result['job']['job_company'] ?? 'Unknown',
                    'provider' => $result['provider'] ?? 'Unknown',
                    'status' => $result['status']
                ]
            );

            Log::info("ID Lamaran: " . $this->job_id . " Berhasil Dilamar: " . ($result ? "Ya" : "Tidak"));
        } catch (CantApply $e){
            Log::info($e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            throw $e;
        }
    }
}
