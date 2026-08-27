<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAiAnswer extends Model
{
    protected $table = 'application_ai_answers';

    protected $fillable = [
        'user_id',
        'application_id',
        'job_id',
        'job_title',
        'provider',
        'model',
        'questionnaire',
        'profile',
        'prompt',
        'raw_response',
        'final_answers',
        'per_question',
        'match_score',
        'unanswered_count',
        'total_questions',
        'tokens_prompt',
        'tokens_completion',
        'tokens_total',
        'duration_ms',
        'status',
        'error_message',
    ];

    protected $casts = [
        'questionnaire' => 'array',
        'profile' => 'array',
        'prompt' => 'array',
        'raw_response' => 'array',
        'final_answers' => 'array',
        'per_question' => 'array',
        'match_score' => 'integer',
        'unanswered_count' => 'integer',
        'total_questions' => 'integer',
        'tokens_prompt' => 'integer',
        'tokens_completion' => 'integer',
        'tokens_total' => 'integer',
        'duration_ms' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
