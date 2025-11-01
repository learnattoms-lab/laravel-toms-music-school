<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Session Resource
 *
 * API resource for Session model.
 */
class SessionResource extends JsonResource
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
            'tutor_id' => $this->tutor_id,
            'offering_id' => $this->offering_id,
            'start_at_utc' => $this->start_at_utc?->toIso8601String(),
            'end_at_utc' => $this->end_at_utc?->toIso8601String(),
            'join_url' => $this->join_url,
            'google_meet_link' => $this->google_meet_link,
            'google_event_id' => $this->google_event_id,
            'status' => $this->status,
            'max_students' => $this->max_students,
            'notes' => $this->notes,
            'materials_json' => $this->materials_json,
            'recording_url' => $this->recording_url,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Helper methods
            'is_upcoming' => $this->isUpcoming(),
            'is_past' => $this->isPast(),
            'session_title' => $this->getSessionTitle(),

            // Relationships
            'course' => new CourseResource($this->whenLoaded('course')),
            'lesson' => $this->whenLoaded('lesson'),
            'tutor' => new UserResource($this->whenLoaded('tutor')),
            'students' => UserResource::collection($this->whenLoaded('students')),
        ];
    }
}

