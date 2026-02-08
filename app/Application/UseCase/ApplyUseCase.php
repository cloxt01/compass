<?php

class ApplyUseCase {
    protected JobstreetService|GlintsService $service;
    protected ApplicationExecutor $executor;
    protected PayloadBuilder $payloadBuilder;
    protected JobDetailsReader $jobDetailsReader;

    public function __construct(
        JobstreetService|GlintsService $service,
        JobDetailsReader $jobDetailsReader,
        DecisionService $decision,
        PayloadBuilder $payloadBuilder,
        ApplicationExecutor $executor,
    ) {
        $this->service = $service;
        $this->jobDetailsReader = $jobDetailsReader;
        $this->decision = $decision;
        $this->payloadBuilder = $payloadBuilder($this->service);
        $this->executor = $executor;
    }

    public function apply(string $jobId): bool {

        if (!$this->jobDetailsReader->load($jobId)) {
            throw new \App\Exceptions\CantApply("Job details could not be loaded.");
        }
        $decision = $this->decision->canApply($this->jobDetailsReader);
        if(!$decision->canApply()['canApply']){
            Log::info("Cannot apply for job $jobId: ".json_encode($decision->canApply()['issues']));
            throw new \App\Exceptions\CantApply("You are not eligible to apply for this job.");
        }
        
        $payload = $this->payloadBuilder->build($this->jobDetailsReader);
        return $this->executor->execute($payload);
    }
}