<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Course Model
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $category_id
 * @property int|null $cover_file_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string $instrument
 * @property string $level
 * @property int $price_cents
 * @property string $status
 * @property \Carbon\Carbon|null $published_at
 * @property \Carbon\Carbon $created_at_utc
 * @property \Carbon\Carbon $updated_at_utc
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Course extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'course';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'teacher_id',
        'category_id',
        'cover_file_id',
        'title',
        'slug',
        'description',
        'instrument',
        'level',
        'price_cents',
        'status',
        'published_at',
        'created_at_utc',
        'updated_at_utc',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'created_at_utc' => 'datetime',
            'updated_at_utc' => 'datetime',
            'price_cents' => 'integer',
            'teacher_id' => 'integer',
            'category_id' => 'integer',
            'cover_file_id' => 'integer',
        ];
    }

    // ============================================================================
    // Relationships
    // ============================================================================

    /**
     * The teacher who created this course.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * The category this course belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    /**
     * The cover file for this course.
     */
    public function coverFile(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class, 'cover_file_id');
    }

    /**
     * Lessons in this course.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Sessions for this course.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Enrollments for this course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Assignments for this course.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Orders for this course.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Certificates issued for this course.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Course offerings for this course.
     */
    public function offerings(): HasMany
    {
        return $this->hasMany(CourseOffering::class);
    }

    // ============================================================================
    // Scopes
    // ============================================================================

    /**
     * Scope a query to only include published courses.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at');
    }

    /**
     * Scope a query to only include courses for a specific instrument.
     */
    public function scopeByInstrument($query, string $instrument)
    {
        return $query->where('instrument', $instrument);
    }

    /**
     * Scope a query to only include courses for a specific level.
     */
    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope a query to only include courses by a specific teacher.
     */
    public function scopeByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope a query to only include courses with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ============================================================================
    // Accessors
    // ============================================================================

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price_cents / 100, 2);
    }

    /**
     * Get the price in dollars.
     */
    public function getPriceDollarsAttribute(): float
    {
        return $this->price_cents / 100;
    }

    /**
     * Check if the course is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at !== null;
    }

    /**
     * Check if the course is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the course is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }
}
