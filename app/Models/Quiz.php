<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Quiz Model
 */
class Quiz extends Model
{
    use HasFactory;

    protected $table = 'quiz';

    protected $fillable = [
        'lesson_id',
        'questions',
        'pass_mark',
        'instructions',
        'time_limit',
        'allow_retakes',
        'max_attempts',
        'shuffle_questions',
        'show_correct_answers',
    ];

    protected function casts(): array
    {
        return [
            'questions' => 'array',
            'pass_mark' => 'integer',
            'time_limit' => 'integer',
            'allow_retakes' => 'boolean',
            'max_attempts' => 'integer',
            'shuffle_questions' => 'boolean',
            'show_correct_answers' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function getTotalPointsAttribute(): int
    {
        if (!$this->questions) {
            return 0;
        }

        return array_sum(array_map(fn($q) => $q['points'] ?? 0, $this->questions));
    }

    public function canUserRetake(User $user): bool
    {
        if (!$this->allow_retakes) {
            return false;
        }

        $attemptsCount = $this->attempts()->where('student_id', $user->id)->count();

        return $attemptsCount < $this->max_attempts;
    }
}
