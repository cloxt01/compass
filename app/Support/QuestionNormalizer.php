<?php

namespace App\Support;

use InvalidArgumentException;

class QuestionNormalizer
{
    public static function normalize(string $provider, array $questionnaire): array
    {
        return collect($questionnaire)
            ->map(function ($question) use ($provider) {
                return [
                    'name' => $question['name'],
                    'question' => $question['label'],
                    'type' => self::normalizeType(
                        $provider,
                        $question['type']),
                    'sub_questions' => collect($question['sub_questions'])
                        ->map(function ($sub) {
                            return [
                                'id' => $sub['id'],
                                'label' => $sub['sub_label'],
                                'options' => collect($sub['options'])
                                    ->map(fn ($option) => [
                                        'label' => $option['value'],
                                        'value' => $option['mapped_value'],
                                    ])
                                    ->values()
                                    ->all(),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    protected static function normalizeGlintsType(string $type): string
    {
        return match ($type) {

            'CUSTOM_PLAIN_TEXT'
            => 'text',

            'SINGLE_CHOICE_WITHOUT_SUBQUESTIONS'
            => 'radio',

            'SINGLE_CHOICE_WITH_SUBQUESTIONS'
            => 'radio_group',

            'MULTIPLE_CHOICE_WITHOUT_SUBQUESTIONS'
            => 'checkbox',

            'MULTIPLE_CHOICE_WITH_SUBQUESTIONS'
            => 'checkbox_group',

            default => throw new InvalidArgumentException(
                "Unknown Glints type [$type]"
            ),
        };
    }
    public static function normalizeType(string $provider, string $type): string
    {
        return match ($provider) {
            'glints' => self::normalizeGlintsType($type),
//            'jobstreet' => self::normalizeJobstreetType($type),

            default => throw new InvalidArgumentException(
                "Unsupported provider: {$provider}"
            ),
        };
    }

    public static function build(array $question): array
    {

        return match ($question['type']) {

            'text' => self::buildText($question),

            'radio' => self::buildRadio($question),

            'checkbox' => self::buildCheckbox($question),

//            'boolean' => self::buildBoolean($question),

            default => throw new InvalidArgumentException(
                "Unknown question type {$question['type']}"
            ),
        };
    }


}
