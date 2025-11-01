<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lesson Resource
 *
 * API resource for Lesson model.
 */
class LessonResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'order_index' => $this->order_index,
            'content_html' => $this->content_html,
            'video_url' => $this->video_url,
            'materials_json' => $this->materials_json,
            'resources' => $this->resources,
            'summary' => $this->summary,
            'duration_min' => $this->duration_min,
            'learning_objectives' => $this->learning_objectives,
            'is_required' => $this->is_required,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Relationships
            'course' => new CourseResource($this->whenLoaded('course')),
        ];
    }
}

