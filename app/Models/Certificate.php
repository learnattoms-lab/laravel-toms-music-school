<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Certificate Model
 */
class Certificate extends Model
{
    use HasFactory;

    protected $table = 'certificate';

    protected $fillable = [
        'user_id',
        'course_id',
        'issued_at',
        'certificate_url',
        'serial',
        'final_score',
        'grade',
        'notes',
        'metadata',
        'is_valid',
        'revoked_at',
        'revocation_reason',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'revoked_at' => 'datetime',
            'final_score' => 'decimal:2',
            'is_valid' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    public function scopeRevoked($query)
    {
        return $query->where('is_valid', false)->whereNotNull('revoked_at');
    }

    public function isRevoked(): bool
    {
        return !$this->is_valid && $this->revoked_at !== null;
    }

    public function revoke(string $reason): void
    {
        $this->is_valid = false;
        $this->revoked_at = now();
        $this->revocation_reason = $reason;
        $this->save();
    }
}
