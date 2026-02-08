<?php

namespace App\Domain\Jobstreet;

use App\Domain\Jobstreet\JobDetailsReader;


class JobEligibilityEvaluator {
    
    protected JobDetailsReader $details;

    public function __construct(JobDetailsReader $details) {
        $this->details = $details;
    }

    public function evaluate(): array {
        $issues = [];

        if($this->details->eligibility()['expired'] !== false){
            $issues[] = [
                'type' => 'expired',
                'level' => 'hard',
                'message' => 'Job sudah expired'
            ];
        }
        if($this->details->eligibility()['linkout']){
            $issues[] = [
                'type' => 'linkout',
                'level' => 'hard',
                'message' => 'Job adalah link keluar'
            ];
        }
        if($this->details->eligibility()['applied']){
            $issues[] = [
                'type' => 'applied',
                'level' => 'hard',
                'message' => "Anda sudah melamar pekerjaan ini pada tanggal ".$this->details->eligibility()['applied']['shortAbsoluteLabel']
            ];
        }

        if(!empty($this->details->products()['questionnaire'])){
            $issues[] = [
                'type' => 'questionnaire',
                'level' => 'soft',
                'message' => 'Anda harus menjawab pertanyaan untuk melamar pekerjaan ini'
            ];
        }
        return [
            'canApply' => empty(array_filter($issues, fn($x) => $x['level'] === 'hard')),
            'issues' => $issues
        ];
    }
}