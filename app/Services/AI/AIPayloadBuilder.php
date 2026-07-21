<?php

namespace App\Services\AI;

use InvalidArgumentException;

class AIPayloadBuilder
{
    public static function build(array $questions): array
    {
        return collect($questions)
            ->map(fn ($question) => self::buildQuestion($question))
            ->values()
            ->all();
    }

    protected static function buildText(array $question): array
    {
        return [
            'type' => 'text',
            'question' => $question['question'],
        ];
    }

    protected static function buildRadio(array $question): array
    {
        return [
            'type' => 'radio',
            'question' => $question['question'],
            'options' => $question['options'] ?? [],
        ];
    }

    protected static function buildRadioGroup(array $question): array
    {
        return [
            'type' => 'radio_group',
            'question' => $question['question'],
            'sub_questions' => collect($question['sub_questions'] ?? [])
                ->map(fn ($sub) => [
                    'id' => $sub['id'] ?? null,
                    'label' => $sub['label'] ?? null,
                    'options' => $sub['options'] ?? [],
                ])
                ->values()
                ->all(),
        ];
    }

    protected static function buildCheckbox(array $question): array
    {
        return [
            'type' => 'checkbox',
            'question' => $question['question'],
            'options' => $question['options'] ?? [],
        ];
    }

    protected static function buildCheckboxGroup(array $question): array
    {
        return [
            'type' => 'checkbox_group',
            'question' => $question['question'],
            'sub_questions' => collect($question['sub_questions'] ?? [])
                ->map(fn ($sub) => [
                    'id' => $sub['id'] ?? null,
                    'label' => $sub['label'] ?? null,
                    'options' => $sub['options'] ?? [],
                ])
                ->values()
                ->all(),
        ];
    }

    protected static function buildBoolean(array $question): array
    {
        return [
            'type' => 'boolean',
            'question' => $question['question'],
            'options' => [
                [
                    'label' => 'Yes',
                    'value' => true,
                ],
                [
                    'label' => 'No',
                    'value' => false,
                ],
            ],
        ];
    }

    protected static function buildQuestion(array $question): array
    {
        return match ($question['type']) {
            'text'
            => self::buildText($question),

            'radio'
            => self::buildRadio($question),

            'radio_group'
            => self::buildRadioGroup($question),

            'checkbox'
            => self::buildCheckbox($question),

            'checkbox_group'
            => self::buildCheckboxGroup($question),

            'boolean'
            => self::buildBoolean($question),

            default => throw new InvalidArgumentException(
                "Unknown question type {$question['type']}"
            ),
        };
    }
}
