<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Course Category Model
 */
class CourseCategory extends Model
{
    use HasFactory;

    protected $table = 'course_category';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
