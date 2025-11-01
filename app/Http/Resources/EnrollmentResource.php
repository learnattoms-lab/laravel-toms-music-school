<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Enrollment Resource
 *
 * API resource for Enrollment model.
 */
class EnrollmentResource extends JsonResource
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
            'student_id' => $this->student_id,
            'course_id' => $this->course_id,
            'offering_id' => $this->offering_id,
            'enrolled_at' => $this->enrolled_at?->toIso8601String(),
            'status' => $this->status,
            'completed_at' => $this->completed_at?->toIso8601String(),
            'progress_pct' => $this->progress_pct,
            'last_accessed_at' => $this->last_accessed_at?->toIso8601String(),
            'lessons_completed' => $this->lessons_completed,
            'total_lessons' => $this->total_lessons,
            'completed_lessons' => $this->completed_lessons,
            'quiz_scores' => $this->quiz_scores,
            'assignment_scores' => $this->assignment_scores,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Helper methods
            'is_active' => $this->isActive(),
            'is_completed' => $this->isCompleted(),

            // Relationships
            'student' => new UserResource($this->whenLoaded('student')),
            'course' => new CourseResource($this->whenLoaded('course')),
        ];
    }
}

