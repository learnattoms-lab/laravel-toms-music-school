<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Assignment Submission Model
 */
class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $table = 'assignment_submission';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'graded_by_id',
        'submission_text',
        'submitted_file_id',
        'files',
        'status',
        'submitted_at',
        'graded_at',
        'grade',
        'grade_points',
        'feedback',
        'feedback_html',
        'notes',
        'is_late',
        'late_penalty_applied',
    ];

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'grade' => 'decimal:2',
            'grade_points' => 'integer',
            'is_late' => 'boolean',
            'late_penalty_applied' => 'integer',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by_id');
    }

    public function submittedFile(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class, 'submitted_file_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'submission_id');
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('graded_at');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function isGraded(): bool
    {
        return $this->graded_at !== null;
    }

    public function isLate(): bool
    {
        return $this->is_late;
    }
}
