<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Lesson Model
 */
class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lesson';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order_index',
        'content_html',
        'video_url',
        'materials_json',
        'resources',
        'summary',
        'duration_min',
        'learning_objectives',
        'is_required',
        'created_at_utc',
        'updated_at_utc',
    ];

    protected function casts(): array
    {
        return [
            'order_index' => 'integer',
            'materials_json' => 'array',
            'resources' => 'array',
            'learning_objectives' => 'array',
            'duration_min' => 'integer',
            'is_required' => 'boolean',
            'created_at_utc' => 'datetime',
            'updated_at_utc' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId)->orderBy('order_index');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }
}
