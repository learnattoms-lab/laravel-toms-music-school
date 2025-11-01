<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

/**
 * User Model
 *
 * @property int $id
 * @property string $email
 * @property array $roles
 * @property string|null $password
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $phone
 * @property \Carbon\Carbon|null $date_of_birth
 * @property string|null $instrument
 * @property string|null $skill_level
 * @property string|null $bio
 * @property string|null $profile_picture
 * @property string|null $city
 * @property string|null $country
 * @property string|null $timezone
 * @property array|null $preferences
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon|null $last_login_at
 * @property bool $is_active
 * @property bool $email_verified
 * @property string|null $google_id
 * @property string|null $apple_id
 * @property string|null $facebook_id
 * @property int $experience_points
 * @property int $level
 * @property array|null $achievements
 * @property array|null $badges
 * @property float|null $rating
 * @property int $total_lessons
 * @property int $completed_lessons
 * @property int $practice_hours
 * @property \Carbon\Carbon|null $last_practice_at
 * @property array|null $learning_goals
 * @property array|null $progress_data
 * @property string|null $notes
 * @property bool $is_teacher
 * @property string|null $teacher_bio
 * @property array|null $teacher_specialties
 * @property array|null $teacher_certifications
 * @property float|null $hourly_rate
 * @property array|null $availability
 * @property array|null $student_reviews
 * @property int $total_students
 * @property int $active_students
 * @property int $failed_login_attempts
 * @property \Carbon\Carbon|null $last_failed_login_at
 * @property string|null $last_failed_login_ip
 * @property bool $is_locked
 * @property \Carbon\Carbon|null $locked_until
 * @property \Carbon\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'roles',
        'password',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'instrument',
        'skill_level',
        'bio',
        'profile_picture',
        'city',
        'country',
        'timezone',
        'preferences',
        'last_login_at',
        'is_active',
        'email_verified',
        'google_id',
        'apple_id',
        'facebook_id',
        'experience_points',
        'level',
        'achievements',
        'badges',
        'rating',
        'total_lessons',
        'completed_lessons',
        'practice_hours',
        'last_practice_at',
        'learning_goals',
        'progress_data',
        'notes',
        'is_teacher',
        'teacher_bio',
        'teacher_specialties',
        'teacher_certifications',
        'hourly_rate',
        'availability',
        'student_reviews',
        'total_students',
        'active_students',
        'failed_login_attempts',
        'last_failed_login_at',
        'last_failed_login_ip',
        'is_locked',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'roles' => 'array',
            'preferences' => 'array',
            'achievements' => 'array',
            'badges' => 'array',
            'learning_goals' => 'array',
            'progress_data' => 'array',
            'teacher_specialties' => 'array',
            'teacher_certifications' => 'array',
            'availability' => 'array',
            'student_reviews' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_practice_at' => 'datetime',
            'last_failed_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',
            'is_teacher' => 'boolean',
            'is_locked' => 'boolean',
            'rating' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'experience_points' => 'integer',
            'level' => 'integer',
            'total_lessons' => 'integer',
            'completed_lessons' => 'integer',
            'practice_hours' => 'integer',
            'total_students' => 'integer',
            'active_students' => 'integer',
            'failed_login_attempts' => 'integer',
        ];
    }

    // ============================================================================
    // Relationships
    // ============================================================================

    /**
     * Courses created by this user as a teacher.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /**
     * Enrollments where this user is a student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * Sessions where this user is a tutor.
     */
    public function taughtSessions(): HasMany
    {
        return $this->hasMany(Session::class, 'tutor_id');
    }

    /**
     * Assignment submissions by this user as a student.
     */
    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    /**
     * Assignments graded by this user.
     */
    public function gradedSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'graded_by_id');
    }

    /**
     * Quiz attempts by this user as a student.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }

    /**
     * Orders placed by this user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Certificates earned by this user.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * OAuth credentials for this user.
     */
    public function oauthCredentials(): HasMany
    {
        return $this->hasMany(OAuthCredential::class);
    }

    /**
     * Files uploaded by this user.
     */
    public function storedFiles(): HasMany
    {
        return $this->hasMany(StoredFile::class, 'uploader_id');
    }

    /**
     * Notes created by this user.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Comments authored by this user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    // ============================================================================
    // Accessors
    // ============================================================================

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    /**
     * Get the user's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: $this->email;
    }

    // ============================================================================
    // Scopes
    // ============================================================================

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include teachers.
     */
    public function scopeTeachers($query)
    {
        return $query->where('is_teacher', true);
    }

    /**
     * Scope a query to only include students.
     */
    public function scopeStudents($query)
    {
        return $query->where('is_teacher', false);
    }

    /**
     * Scope a query to only include verified users.
     */
    public function scopeVerified($query)
    {
        return $query->where('email_verified', true);
    }

    /**
     * Scope a query to only include users with a specific role.
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->whereJsonContains('roles', $role);
    }

    // ============================================================================
    // Helper Methods
    // ============================================================================

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? [], true);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('ROLE_ADMIN');
    }

    /**
     * Check if the user is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->is_teacher || $this->hasRole('ROLE_TEACHER');
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return !$this->is_teacher && ($this->hasRole('ROLE_USER') || empty($this->roles));
    }

    /**
     * Add a role to the user.
     */
    public function addRole(string $role): void
    {
        $roles = $this->roles ?? [];
        if (!in_array($role, $roles, true)) {
            $roles[] = $role;
            $this->roles = $roles;
        }
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string $role): void
    {
        $roles = $this->roles ?? [];
        $this->roles = array_values(array_filter($roles, fn($r) => $r !== $role));
    }

    /**
     * Check if the account is locked.
     */
    public function isLocked(): bool
    {
        if (!$this->is_locked) {
            return false;
        }

        if ($this->locked_until && $this->locked_until->isPast()) {
            $this->is_locked = false;
            $this->locked_until = null;
            $this->save();

            return false;
        }

        return true;
    }

    /**
     * Check if the account can be locked.
     */
    public function canBeLocked(): bool
    {
        return !$this->isLocked() && $this->is_active;
    }

    /**
     * Lock the user account.
     */
    public function lock(int $minutes = 30): void
    {
        $this->is_locked = true;
        $this->locked_until = now()->addMinutes($minutes);
        $this->save();
    }

    /**
     * Unlock the user account.
     */
    public function unlock(): void
    {
        $this->is_locked = false;
        $this->locked_until = null;
        $this->failed_login_attempts = 0;
        $this->save();
    }

    /**
     * Record a failed login attempt.
     */
    public function recordFailedLogin(string $ip): void
    {
        $this->failed_login_attempts++;
        $this->last_failed_login_at = now();
        $this->last_failed_login_ip = $ip;
        $this->save();

        // Auto-lock after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->lock(30);
        }
    }

    /**
     * Reset failed login attempts.
     */
    public function resetFailedLoginAttempts(): void
    {
        $this->failed_login_attempts = 0;
        $this->last_failed_login_at = null;
        $this->last_failed_login_ip = null;
        $this->save();
    }

    /**
     * Get enrolled courses for this user.
     */
    public function enrolledCourses(): Collection
    {
        return $this->enrollments()->with('course')->get()->pluck('course')->filter();
    }
}
