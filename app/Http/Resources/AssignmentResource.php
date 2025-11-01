<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Assignment Resource
 *
 * API resource for Assignment model.
 */
class AssignmentResource extends JsonResource
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
            'course_id' => $this->course_id,
            'lesson_id' => $this->lesson_id,
            'session_id' => $this->session_id,
            'title' => $this->title,
            'description' => $this->description,
            'instructions_html' => $this->instructions_html,
            'due_at' => $this->due_at?->toIso8601String(),
            'max_points' => $this->max_points,
            'attachments' => $this->attachments,
            'is_required' => $this->is_required,
            'allow_late_submission' => $this->allow_late_submission,
            'late_penalty' => $this->late_penalty,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Relationships
            'course' => new CourseResource($this->whenLoaded('course')),
            'lesson' => $this->whenLoaded('lesson'),
            'session' => new SessionResource($this->whenLoaded('session')),
        ];
    }
}

