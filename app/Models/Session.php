<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Session Model
 */
class Session extends Model
{
    use HasFactory;

    protected $table = 'session';

    protected $fillable = [
        'course_id',
        'lesson_id',
        'tutor_id',
        'offering_id',
        'start_at_utc',
        'end_at_utc',
        'join_url',
        'google_meet_link',
        'google_event_id',
        'materials_json',
        'recording_url',
        'status',
        'max_students',
        'notes',
        'created_at_utc',
        'updated_at_utc',
    ];

    protected function casts(): array
    {
        return [
            'start_at_utc' => 'datetime',
            'end_at_utc' => 'datetime',
            'materials_json' => 'array',
            'max_students' => 'integer',
            'created_at_utc' => 'datetime',
            'updated_at_utc' => 'datetime',
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

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'offering_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Students enrolled in this session.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'session_students', 'session_id', 'user_id');
    }

    /**
     * Get session title for Google Calendar.
     */
    public function getSessionTitle(): string
    {
        $course = $this->course;
        $lesson = $this->lesson;

        if ($course && $lesson) {
            return "{$course->title} - {$lesson->title}";
        }

        if ($course) {
            return $course->title;
        }

        return 'Music Lesson Session';
    }

    /**
     * Get enrolled students collection.
     */
    public function getEnrolledStudents()
    {
        return $this->students;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at_utc', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('start_at_utc', '<', now());
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function isUpcoming(): bool
    {
        return $this->start_at_utc && $this->start_at_utc->isFuture();
    }

    public function isPast(): bool
    {
        return $this->start_at_utc && $this->start_at_utc->isPast();
    }
}
