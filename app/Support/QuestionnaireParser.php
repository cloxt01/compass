<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class QuestionnaireParser {
    public static function parse($questionnaire) {
        if (empty($questionnaire) || !isset($questionnaire['questions'])) {
            return [];
        }

        $result = [];
        
        foreach ($questionnaire['questions'] as $question) {
            $item = [
                'question_id' => $question['id'] ?? '',
                'question_text' => $question['text'] ?? '',
                'lastAnswer' => '',
                'options' => []
            ];

            if (!empty($question['lastAnswer'])) {
                $item['lastAnswer'] = $question['lastAnswer']['text'] ?? '';
            }

            if (!empty($question['options'])) {
                foreach ($question['options'] as $option) {
                    $item['options'][] = [
                        'id' => $option['id'] ?? '',
                        'text' => $option['text'] ?? '',
                        'uri' => $option['uri'] ?? ''
                    ];
                }
            }

            $result[] = $item;
        }

        return $result;
    }

    public static function display($data) {
        echo "\n" . str_repeat('=', 120) . "\n";
        echo "QUESTIONNAIRE DATA\n";
        echo str_repeat('=', 120) . "\n\n";

        foreach ($data as $idx => $item) {
            echo "[" . ($idx + 1) . "] " . $item['question_id'] . "\n";
            echo "    Question: " . $item['question_text'] . "\n";
            echo "    Selected: " . ($item['lastAnswer'] ?: '(Not answered)') . "\n";
            echo "    Options (" . count($item['options']) . "):\n";
            
            foreach ($item['options'] as $opt) {
                $marker = ($item['lastAnswer'] === $opt['text']) ? '✓' : ' ';
                echo "      [$marker] " . $opt['id'] . " => " . $opt['text'] . "\n";
            }
            echo "\n";
        }
        
        echo str_repeat('=', 120) . "\n\n";
    }

    public static function saveToDatabase($data) {
        try {
            $inserted = 0;
            $skipped = 0;

            foreach ($data as $item) {
                $existing = DB::table('questionnaire')
                    ->where('question_id', $item['question_id'])
                    ->first();
                
                if ($existing) {
                    $skipped++;
                    continue; 
                }

                if (!empty($item['options'])) {
                    $optionsJson = json_encode($item['options']);
                    
                    if (!empty($item['lastAnswer'])) {
                        foreach ($item['options'] as $option) {
                            if ($option['text'] === $item['lastAnswer']) {
                                DB::table('questionnaire')->insert([
                                    'question_id' => $item['question_id'],
                                    'question_text' => $item['question_text'],
                                    'answer_id' => $option['id'],
                                    'answer_text' => $option['text'],
                                    'last_answer' => $item['lastAnswer'],
                                    'options' => $optionsJson
                                ]);

                                $inserted++;
                                break;
                            }
                        }
                    } else {
                        DB::table('questionnaire')->insert([
                            'question_id' => $item['question_id'],
                            'question_text' => $item['question_text'],
                            'answer_id' => null,
                            'answer_text' => null,
                            'last_answer' => null,
                            'options' => $optionsJson
                        ]);

                        $inserted++;
                    }
                }
            }

            echo "\n✓ Inserted $inserted records, Skipped $skipped (already exist)\n";
            
            return true;
        } catch (\Exception $e) {
            echo "\n✗ Error: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public static function exportSQL($data) {
        $statements = [];
        
        foreach ($data as $item) {
            if (!empty($item['options'])) {
                $optionsJson = addslashes(json_encode($item['options']));
                
                if (!empty($item['lastAnswer'])) {
                    // Jika ada jawaban: export hanya option yang dipilih dengan last_answer
                    foreach ($item['options'] as $option) {
                        if ($option['text'] === $item['lastAnswer']) {
                            $q_id = addslashes($item['question_id']);
                            $q_text = addslashes($item['question_text']);
                            $opt_id = addslashes($option['id']);
                            $opt_text = addslashes($option['text']);
                            $last_answer = addslashes($item['lastAnswer']);

                            $stmt = "INSERT INTO questionnaire (question_id, question_text, answer_id, answer_text, last_answer, options) VALUES ('$q_id', '$q_text', '$opt_id', '$opt_text', '$last_answer', '$optionsJson');";
                            $statements[] = $stmt;
                            break;
                        }
                    }
                } else {
                    // Jika belum ada jawaban: export row tunggal dengan semua options
                    $q_id = addslashes($item['question_id']);
                    $q_text = addslashes($item['question_text']);

                    $stmt = "INSERT INTO questionnaire (question_id, question_text, answer_id, answer_text, last_answer, options) VALUES ('$q_id', '$q_text', NULL, NULL, NULL, '$optionsJson');";
                    $statements[] = $stmt;
                }
            }
        }

        return $statements;
    }

    public static function answerQuestions($questionnaire) {
        try {
            $result = [];

            foreach ($questionnaire as $question) {
                $questionId = $question['questionId'] ?? '';
                
                if (empty($questionId)) {
                    $result[] = $question;
                    continue;
                }

                $dbQuestion = DB::table('questionnaire')
                    ->where('question_id', $questionId)
                    ->first();

                if ($dbQuestion) {
                    $dbQuestion = (array) $dbQuestion;
                }

                if ($dbQuestion && !empty($dbQuestion['answer_id'])) {
                    $answers = $question['answers'] ?? [];
                    $foundAnswer = null;

                    foreach ($answers as $answer) {
                        if ($answer['id'] === $dbQuestion['answer_id'] || $answer['text'] === $dbQuestion['answer_text']) {
                            $foundAnswer = $answer;
                            break;
                        }
                    }

                    if ($foundAnswer) {
                        $result[] = [
                            'questionId' => $questionId,
                            'answers' => [$foundAnswer], 
                            'dbInfo' => [
                                'answerId' => $dbQuestion['answer_id'],
                                'answerText' => $dbQuestion['answer_text'],
                                'lastAnswer' => $dbQuestion['last_answer'],
                                'source' => 'database'
                            ]
                        ];
                    } else {
                        $result[] = null;
                    }
                } else {
                    $result[] = $question;
                }
            }

            return $result;

        } catch (\Exception $e) {
            echo "\n✗ Error in answerQuestions: " . $e->getMessage() . "\n";
            return $questionnaire;
        }
    }

    public static function formatForSubmission($answeredQuestions) {
        if (!is_array($answeredQuestions)) {
            return [];
        }
        
        $formatted = [];

        foreach ($answeredQuestions as $question) {
            if (!is_array($question) || !isset($question['questionId'])) {
                continue;
            }

            $questionId = $question['questionId'] ?? null;
            if (empty($questionId)) {
                continue;
            }

            $answers = is_array($question['answers'] ?? null) ? $question['answers'] : [];
            
            $formattedAnswers = [];
            foreach ($answers as $answer) {
                if (!is_array($answer)) {
                    continue;
                }
                
                $id = $answer['id'] ?? null;
                $text = $answer['text'] ?? null;
                
                if (!empty($id) && !empty($text)) {
                    $formattedAnswers[] = [
                        'id' => (string)$id,
                        'text' => (string)$text,
                        'uri' => (string)($answer['uri'] ?? '')
                    ];
                }
            }

            if (!empty($formattedAnswers)) {
                $formatted[] = [
                    'questionId' => (string)$questionId,
                    'answers' => $formattedAnswers
                ];
            }
        }

        return $formatted;
    }

    public static function prepareAndAnswerFromGraphQL($questionnaire) {
        $prepared = [];

        if (empty($questionnaire) || !isset($questionnaire['questions']) || !is_array($questionnaire['questions'])) {
            return [];
        }

        foreach ($questionnaire['questions'] as $q) {
            $qId = $q['id'] ?? '';
            if (empty($qId)) continue;

            $answersOut = [];

            $dbQ = DB::table('questionnaire')
                ->where('question_id', $qId)
                ->first();

            if ($dbQ) {
                $dbQ = (array) $dbQ;
            }

            if ($dbQ && !empty($dbQ['answer_id'])) {
                $found = null;
                if (!empty($q['options']) && is_array($q['options'])) {
                    foreach ($q['options'] as $opt) {
                        if (($opt['id'] ?? '') === $dbQ['answer_id'] || ($opt['text'] ?? '') === $dbQ['answer_text']) {
                            $found = [
                                'id' => $opt['id'] ?? $dbQ['answer_id'],
                                'text' => $opt['text'] ?? $dbQ['answer_text'],
                                'uri' => $opt['uri'] ?? ''
                            ];
                            break;
                        }
                    }
                }

                if ($found) {
                    $answersOut[] = $found;
                } else {
                    $answersOut[] = [
                        'id' => $dbQ['answer_id'],
                        'text' => $dbQ['answer_text'],
                        'uri' => ''
                    ];
                }

            } elseif (!empty($q['lastAnswer']) && is_array($q['lastAnswer'])) {
                $la = $q['lastAnswer'];
                $answersOut[] = [
                    'id' => $la['id'] ?? '',
                    'text' => $la['text'] ?? '',
                    'uri' => $la['uri'] ?? ''
                ];
            } else {
                if (!empty($q['options']) && is_array($q['options'])) {
                    $opts = array_values($q['options']);
                    $randOpt = $opts[array_rand($opts)];
                    $answersOut[] = [
                        'id' => $randOpt['id'] ?? '',
                        'text' => $randOpt['text'] ?? '',
                        'uri' => $randOpt['uri'] ?? ''
                    ];
                }
            }

            if (!empty($answersOut)) {
                $prepared[] = [
                    'questionId' => $qId,
                    'answers' => $answersOut
                ];
            }
        }

        return self::formatForSubmission($prepared);
    }
}
?>