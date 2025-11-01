<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Course Offering Model
 */
class CourseOffering extends Model
{
    use HasFactory;

    protected $table = 'course_offerings';

    protected $fillable = [
        'course_id',
        'tutor_id',
        'title',
        'timezone',
        'start_at_utc',
        'end_at_utc',
        'capacity',
        'price_cents_override',
        'status',
        'rrule',
        'exdates_json',
        'duration_minutes',
        'sessions_count',
        'created_at_utc',
        'updated_at_utc',
    ];

    protected function casts(): array
    {
        return [
            'start_at_utc' => 'datetime',
            'end_at_utc' => 'datetime',
            'created_at_utc' => 'datetime',
            'updated_at_utc' => 'datetime',
            'capacity' => 'integer',
            'price_cents_override' => 'integer',
            'duration_minutes' => 'integer',
            'sessions_count' => 'integer',
            'exdates_json' => 'array',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, 'offering_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'offering_id');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at_utc', '>', now());
    }

    public function isUpcoming(): bool
    {
        return $this->start_at_utc && $this->start_at_utc->isFuture();
    }
}
