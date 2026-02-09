<?php

namespace App\Services;

class JobInspector
{
    

    public static function fromJobstreet($details){
        $issues = [];
        if($details['eligibility']['expired'] !== false){
            $issues[] = [
                'type' => 'expired',
                'level' => 'hard',
                'message' => 'Job sudah expired'
            ];
        }
        if($details['eligibility']['linkout']){
            $issues[] = [
                'type' => 'linkout',
                'level' => 'hard',
                'message' => 'Job adalah link keluar'
            ];
        }
        if($details['eligibility']['applied']){
            $issues[] = [
                'type' => 'applied',
                'level' => 'hard',
                'message' => "Anda sudah melamar pekerjaan ini"
            ];
        }

        if(!empty($details['products']['questionnaire'])){
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
    public static function fromGlints($details){
        return [];
    }
}