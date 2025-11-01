<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Course Resource
 *
 * API resource for Course model.
 */
class CourseResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'instrument' => $this->instrument,
            'level' => $this->level,
            'price_cents' => $this->price_cents,
            'formatted_price' => $this->formatted_price,
            'price_dollars' => $this->price_dollars,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Relationships
            'teacher' => new UserResource($this->whenLoaded('teacher')),
            'category' => $this->whenLoaded('category'),
            'cover_file' => $this->whenLoaded('coverFile'),

            // Counts (if loaded)
            'lessons_count' => $this->when(isset($this->lessons_count), $this->lessons_count),
            'enrollments_count' => $this->when(isset($this->enrollments_count), $this->enrollments_count),
        ];
    }
}

