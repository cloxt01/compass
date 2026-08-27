<?php

namespace App\Services\AI;

/**
 * Transform jawaban AI (format kanonik dari AutoAnswerService)
 * ke format submit-payload masing-masing provider.
 */
class AnswerTransformer
{
    /**
     * Convert AI answers ke format Glints `employerScreeningQuestionAnswers`.
     *
     * Setiap normalized question dipetakan ke satu group answer berdasarkan 'name':
     * - NOTICE_PERIOD / WORK_EXPERIENCE_DURATION (radio without sub):
     *   { name: X, answers: [{ resourceId: null, value: MAPPED_VALUE }] }
     * - SKILL_PROFICIENCY (radio_group with sub-questions):
     *   { name: X, answers: [{ resourceId: sub.id, value: MAPPED_VALUE }, ...] }
     * - CUSTOM_PLAIN_TEXT (text): dikumpulkan bersama ke satu group name=CUSTOM_PLAIN_TEXT
     *   { name: CUSTOM_PLAIN_TEXT, answers: [{ resourceId: sub.id, value: TEXT }, ...] }
     * - checkbox/checkbox_group: analog ke radio/radio_group tapi multiple values.
     */
    public static function toGlints(array $normalized, array $aiAnswers): array
    {
        // Kumpulkan CUSTOM_PLAIN_TEXT ke satu group
        $textBag = [];
        $groups = [];

        foreach ($normalized as $i => $q) {
            $name = $q['name'] ?? null;
            $type = $q['type'] ?? null;
            $ans = $aiAnswers[$i] ?? null;
            if (!$name || !is_array($ans)) continue;

            $answer = $ans['answer'] ?? null;

            switch ($type) {
                case 'radio':
                    $value = is_array($answer) ? ($answer['value'] ?? null) : null;
                    if ($value === null || $value === '') break;
                    $groups[] = [
                        'name' => $name,
                        'answers' => [[
                            'resourceId' => null,
                            'value' => $value,
                        ]],
                    ];
                    break;

                case 'radio_group':
                    if (!is_array($answer)) break;
                    $subAnswers = [];
                    foreach ($answer as $row) {
                        if (!is_array($row)) continue;
                        $val = $row['value'] ?? null;
                        if ($val === null || $val === '') continue; // skip yang null
                        $subAnswers[] = [
                            'resourceId' => $row['id'] ?? null,
                            'value' => $val,
                        ];
                    }
                    if (!empty($subAnswers)) {
                        $groups[] = ['name' => $name, 'answers' => $subAnswers];
                    }
                    break;

                case 'checkbox':
                    if (!is_array($answer) || !isset($answer['options'])) break;
                    $subAnswers = [];
                    foreach ($answer['options'] as $opt) {
                        if (!is_array($opt)) continue;
                        $val = $opt['value'] ?? null;
                        if ($val === null || $val === '') continue;
                        $subAnswers[] = ['resourceId' => null, 'value' => $val];
                    }
                    if (!empty($subAnswers)) {
                        $groups[] = ['name' => $name, 'answers' => $subAnswers];
                    }
                    break;

                case 'checkbox_group':
                    if (!is_array($answer)) break;
                    $subAnswers = [];
                    foreach ($answer as $row) {
                        $rid = $row['id'] ?? null;
                        foreach (($row['options'] ?? []) as $opt) {
                            $val = $opt['value'] ?? null;
                            if ($val === null || $val === '') continue;
                            $subAnswers[] = ['resourceId' => $rid, 'value' => $val];
                        }
                    }
                    if (!empty($subAnswers)) {
                        $groups[] = ['name' => $name, 'answers' => $subAnswers];
                    }
                    break;

                case 'text':
                    // Untuk CUSTOM_PLAIN_TEXT, sub_questions punya id.
                    $text = null;
                    if (is_array($answer)) $text = $answer['text'] ?? null;
                    if ($text === null || $text === '') break;
                    $subId = $q['sub_questions'][0]['id'] ?? null;
                    $textBag[] = [
                        'resourceId' => $subId,
                        'value' => (string) $text,
                    ];
                    break;
            }
        }

        if (!empty($textBag)) {
            $groups[] = ['name' => 'CUSTOM_PLAIN_TEXT', 'answers' => $textBag];
        }

        return $groups;
    }
}
