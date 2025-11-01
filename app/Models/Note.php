<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Note Model
 */
class Note extends Model
{
    use HasFactory;

    protected $table = 'note';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'body',
        'tags',
        'is_public',
        'word_count',
        'character_count',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_public' => 'boolean',
            'word_count' => 'integer',
            'character_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function isPublic(): bool
    {
        return $this->is_public;
    }
}
