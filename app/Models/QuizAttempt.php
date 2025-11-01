<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Quiz Attempt Model
 */
class QuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempt';

    protected $fillable = [
        'quiz_id',
        'student_id',
        'score',
        'passed',
        'submitted_at',
        'responses',
        'started_at',
        'completed_at',
        'time_spent',
        'question_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'responses' => 'array',
            'question_order' => 'array',
            'score' => 'integer',
            'passed' => 'boolean',
            'time_spent' => 'integer',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }
}
