<?php


namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use App\Clients\Application\UseCase\ApplyUseCase;
use App\Exceptions\CantApply;
use App\Http\Controllers\JobController;
use App\Infrastructure\Contracts\PlatformAccount;
use App\Infrastructure\Contracts\PlatformAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;


class ProcessApplications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $job_id;
    protected PlatformAdapter $adapter;
    protected PlatformAccount $account;
    public int $user_id;
    public function __construct(PlatformAdapter $adapter, PlatformAccount $account, string $job_id)
    {
        $this->adapter = $adapter;
        $this->account = $account;
        $this->job_id = $job_id;
        $this->user_id = $this->account->user->id;
    }



    public function handle()
    {
        if (RateLimiter::tooManyAttempts('glints-apply', 20)) {
            Log::warning("Job {$this->job_id} kena rate limit, di-release...");
            $this->release(RateLimiter::availableIn('glints-apply'));
            return;
        }

        RateLimiter::hit('glints-apply', 600);

        Log::info("Memproses Lamaran ID: " . $this->job_id . " - User ID : " . $this->account->user->id . " - Platform : " . get_class($this->adapter));

        try {
            $result = (new ApplyUseCase($this->adapter, $this->account))->apply($this->job_id);
            if($result){
                $this->adapter->db()->upsert_job($this->account->user->id, $this->job_id, 'success');
                $this->account->user->stats()->firstOrCreate(
                    ['date' => now()->toDateString()],
                    ['total_applied' => 0]
                )->increment('total_applied');
            }
            Log::info("ID Lamaran: " . $this->job_id . " Berhasil Dilamar: " . ($result ? "Ya" : "Tidak"));
        } catch (CantApply $e){
            Log::info($e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            throw $e;
        }
    }
}
