<?php


namespace App\Jobs;

use App\Http\Controllers\JobController;
use App\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Infrastructure\Contracts\PlatformAdapter;
use App\Infrastructure\Contracts\PlatformAccount;
use App\Clients\JobstreetAPI;
use App\Services\Adapters\JobstreetAdapter;
use App\Models\JobstreetAccount;

use App\Application\UseCase\ApplyUseCase;

use App\Exceptions\CantApply;


class ProcessApplications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;
    public $backoff = [30, 60, 120];

    protected string $job_id;
    protected int $account_id;

    public function __construct(PlatformAdapter $adapter, PlatformAccount $account, string $job_id)
    {
        // Simpan hanya ID, bukan object
        $this->job_id = $job_id;
        $this->account_id = $account->id;
        error_log("[ProcessApplications] Job created: job_id={$job_id}, account_id={$account->id}");
    }

    public function handle()
    {
        error_log("[ProcessApplications] Handle called: job_id={$this->job_id}, account_id={$this->account_id}");
        Log::info("Memproses Lamaran ID: " . $this->job_id);

        try {
            // Ambil account dari database
            $account = JobstreetAccount::find($this->account_id);
            if (!$account) {
                Log::error("Account ID {$this->account_id} tidak ditemukan");
                return;
            }

            // Buat adapter baru dari account yang terbaru
            $adapter = new JobstreetAdapter(
                new JobstreetAPI($account->access_token),
                $account
            );

            $result = (new ApplyUseCase($adapter, $account))->apply($this->job_id);
            if($result){
                Log::info("Lamaran ID: " . $this->job_id . " berhasil dilamar. Mencatat statistik...");
                $user = $account->user;
                $userStat = $user->stats()->firstOrCreate(
                    ['date' => now()->toDateString()],
                    ['total_applied' => 0]
                );
                $userStat->increment('total_applied');
                Log::info("Statistik berhasil dicatat untuk user ID: " . $user->id);
            }
            Log::info("ID Lamaran: " . $this->job_id . " Berhasil Dilamar: " . ($result ? "Ya" : "Tidak"));
        } catch (CantApply $e){
            Log::warning("Tidak dapat melamar job {$this->job_id}: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage(), ['exception' => $e]);
            throw $e; 
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("ProcessApplications job FAILED setelah retry habis", [
            'job_id' => $this->job_id,
            'account_id' => $this->account_id,
            'error' => $exception->getMessage()
        ]);
    }
}