<?php

namespace App\Services;

class JobQuestion
{


    public static function fromJobstreet($raw){

        return [
        ];
    }
    public static function fromGlints($raw): array {
        $result = [];

        foreach ($raw['data']['getJobHiringQuestions']['predefinedQuestions'] as $question) {
            $result['predefinedQuestions'][] = [
                'name' => $question['name'],
                'type' => $question['type'],
                'required' => $question['required']
            ];
        }
        foreach ($raw['data']['getJobHiringQuestions']['employerScreeningQuestions'] as $question) {
            $result['employerScreeningQuestions'][] = [
                'name' => $question['name'],
                'label' => $question['label'],
                'type' => $question['questionType'],
                'options' => $question['questions'][0]['responseOptions'] ?? [],
            ];
        }
        return $result;
    }
}
