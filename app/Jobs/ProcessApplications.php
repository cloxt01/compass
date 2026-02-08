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

use App\Clients\JobstreetAPI;
use App\Services\JobstreetService;
use App\Domain\Jobstreet\JobDetailsReader;
use App\Domain\Jobstreet\JobEligibilityEvaluator;
use App\Domain\Jobstreet\JobApplicationProcess;
use App\Domain\Jobstreet\ApplicationPayloadBuilder;
use App\Domain\Jobstreet\ApplicationExecutor;
use App\Exceptions\CantApply;


class ProcessApplications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected JobstreetAPI $client;
    protected string $job_id;
    protected JobstreetService $service;

    protected JobDetailsReader $reader;

    public function __construct(JobstreetAPI $client, string $job_id)
    {
        $this->client = $client;
        $this->job_id = $job_id;
        $this->service = new JobstreetService($client);
        $this->reader = new JobDetailsReader($client);
    }

    public function handle()
    {
        Log::info("Memproses Lamaran ID: " . $this->job_id);

        try {
            if($this->reader->load($this->job_id)){
                $evaluate = (new JobEligibilityEvaluator($this->reader))->evaluate();
                if(!$evaluate['canApply']){
                    throw (new CantApply($evaluate['issues'][0]['message']));
                }
                $questionnaire = (new JobApplicationProcess($this->client))->load($this->job_id)->questionnaire();
                $payload = (new ApplicationPayloadBuilder($this->client, $this->service))->build($this->reader->metadata(), $questionnaire);
                $result = (new ApplicationExecutor($this->client))->apply($payload);
            }

            if ($result['success']) {
                event(new JobProcessed($this->job_id, 'Sukses Melamar!'));
                Log::info("Aplikasi ID " . $this->job_id . " berhasil diproses.");
            } else {
                throw new \Exception("Aplikasi ID " . $this->job_id. " gagal diproses: " . ($result['message'] ?? 'Unknown error'));
            }
        } catch (CantApply $e){
            Log::info($e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            throw $e; 
        }
    }
}