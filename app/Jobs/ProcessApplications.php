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
use App\Clients\GlintsAPI;

use App\Application\UseCase\ApplyUseCase;

use App\Exceptions\CantApply;


class ProcessApplications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $job_id;
    protected PlatformAdapter $adapter;
    public function __construct(PlatformAdapter $adapter, string $job_id)
    {
        $this->adapter = $adapter;
        $this->job_id = $job_id;
    }

    public function handle()
    {
        Log::info("Memproses Lamaran ID: " . $this->job_id);

        try {
            $result = (new ApplyUseCase($this->adapter))->apply($this->job_id);
            Log::info("ID Lamaran: " . $this->job_id . " Berhasil Dilamar: " . ($result ? "Ya" : "Tidak"));
        } catch (CantApply $e){
            Log::info($e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            throw $e; 
        }
    }
}