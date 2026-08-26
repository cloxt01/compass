<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAnswerHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'question',
        'candidate_context',
        'job_context',
        'answer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
