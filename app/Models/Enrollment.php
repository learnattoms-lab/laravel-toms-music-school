<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Enrollment Model
 */
class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollment';

    protected $fillable = [
        'student_id',
        'course_id',
        'offering_id',
        'enrolled_at',
        'status',
        'completed_at',
        'progress_pct',
        'last_accessed_at',
        'lessons_completed',
        'total_lessons',
        'completed_lessons',
        'quiz_scores',
        'assignment_scores',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_accessed_at' => 'datetime',
            'progress_pct' => 'decimal:2',
            'lessons_completed' => 'integer',
            'total_lessons' => 'integer',
            'completed_lessons' => 'array',
            'quiz_scores' => 'array',
            'assignment_scores' => 'array',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'offering_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
