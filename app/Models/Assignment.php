<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Assignment Model
 */
class Assignment extends Model
{
    use HasFactory;

    protected $table = 'assignment';

    protected $fillable = [
        'course_id',
        'lesson_id',
        'session_id',
        'title',
        'description',
        'instructions_html',
        'due_at',
        'max_points',
        'rubric',
        'attachments',
        'is_required',
        'allow_late_submission',
        'late_penalty',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'max_points' => 'integer',
            'attachments' => 'array',
            'is_required' => 'boolean',
            'allow_late_submission' => 'boolean',
            'late_penalty' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_at', '<', now())->whereNotNull('due_at');
    }

    public function isOverdue(): bool
    {
        return $this->due_at && $this->due_at->isPast();
    }
}
