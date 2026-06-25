<?php

namespace App\Services;

class JobInspector
{
    public static function fromJobstreet($job){
        $issues = [];
        if($job['eligibility']['expired'] !== false){
            $issues[] = [
                'type' => 'expired',
                'level' => 'hard',
                'message' => 'Job sudah expired'
            ];
        }
        if($job['eligibility']['linkout']){
            $issues[] = [
                'type' => 'linkout',
                'level' => 'hard',
                'message' => 'Job adalah link keluar'
            ];
        }
        if($job['eligibility']['applied']){
            $issues[] = [
                'type' => 'applied',
                'level' => 'hard',
                'message' => "Anda sudah melamar pekerjaan ini"
            ];
        }

        if(!empty($job['products']['questionnaire'])){
            $issues[] = [
                'type' => 'questionnaire',
                'level' => 'soft',
                'message' => 'Anda harus menjawab pertanyaan untuk melamar pekerjaan ini'
            ];
        }

        usort($issues, function ($a, $b) {
            if ($a['level'] === $b['level']) {
                return 0;
            }
            return $a['level'] === 'hard' ? -1 : 1;
        });


        return [
            'canApply' => empty(array_filter($issues, fn($x) => $x['level'] === 'hard')),
            'issues' => $issues
        ];
    }
    public static function fromGlints($job){
//        $issues = [];
//        if(isset($job['hiring_question']['employerScreeningQuestions'])){
//            $issues[] = [
//                'type' => 'questionnaire',
//                'level' => 'soft',
//                'message' => 'Anda harus menjawab pertanyaan untuk melamar pekerjaan ini'
//            ];
//            return [
//                'canApply' => false,
//                'issues' => $issues
//            ];
//        }
//        return [
//            'canApply' => $job['details']['canUserApplyWithReason']['canApply'],
//            'issues' => $job['details']['canUserApplyWithReason']['reason']
//        ];
        $issues = [];
        if($job['eligibility']['expired'] !== false){
            $issues[] = [
                'type' => 'expired',
                'level' => 'hard',
                'message' => 'Job sudah expired'
            ];
        }
        if($job['eligibility']['linkout']){
            $issues[] = [
                'type' => 'linkout',
                'level' => 'hard',
                'message' => 'Job adalah link keluar'
            ];
        }
        if($job['eligibility']['applied']){
            $issues[] = [
                'type' => 'applied',
                'level' => 'soft',
                'message' => "Anda sudah melamar pekerjaan ini"
            ];
        }

        if(!empty($job['products']['questionnaire'])){
            $issues[] = [
                'type' => 'questionnaire',
                'level' => 'hard',
                'message' => 'Anda harus menjawab pertanyaan untuk melamar pekerjaan ini'
            ];
        }

        usort($issues, function ($a, $b) {
            if ($a['level'] === $b['level']) {
                return 0;
            }
            return $a['level'] === 'hard' ? -1 : 1;
        });


        return [
            'canApply' => empty(array_filter($issues, fn($x) => $x['level'] === 'hard')),
            'issues' => $issues
        ];
    }
}
