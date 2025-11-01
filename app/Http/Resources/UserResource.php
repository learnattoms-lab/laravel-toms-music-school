<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Resource
 *
 * API resource for User model.
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'display_name' => $this->display_name,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'instrument' => $this->instrument,
            'skill_level' => $this->skill_level,
            'bio' => $this->bio,
            'profile_picture' => $this->profile_picture,
            'city' => $this->city,
            'country' => $this->country,
            'timezone' => $this->timezone,
            'roles' => $this->roles,
            'is_active' => $this->is_active,
            'email_verified' => $this->email_verified,
            'is_teacher' => $this->is_teacher,
            'teacher_bio' => $this->teacher_bio,
            'teacher_specialties' => $this->teacher_specialties,
            'hourly_rate' => $this->hourly_rate,
            'experience_points' => $this->experience_points,
            'level' => $this->level,
            'rating' => $this->rating,
            'total_lessons' => $this->total_lessons,
            'completed_lessons' => $this->completed_lessons,
            'practice_hours' => $this->practice_hours,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'last_login_at' => $this->last_login_at?->toIso8601String(),

            // Helper methods
            'is_admin' => $this->isAdmin(),
            'is_student' => $this->isStudent(),

            // Include relationships only when loaded (resources will be created in Task 2.17)
            // 'courses' => CourseResource::collection($this->whenLoaded('courses')),
            // 'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
        ];
    }
}

