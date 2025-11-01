<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Quiz Resource
 *
 * API resource for Quiz model.
 */
class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $showAnswers = $this->show_correct_answers ?? false;
        $isTeacher = $request->user() && $request->user()->isTeacher();

        return [
            'id' => $this->id,
            'lesson_id' => $this->lesson_id,
            'questions' => $this->when(
                $isTeacher || $showAnswers,
                $this->questions,
                $this->maskAnswers($this->questions)
            ),
            'pass_mark' => $this->pass_mark,
            'instructions' => $this->instructions,
            'time_limit' => $this->time_limit,
            'allow_retakes' => $this->allow_retakes,
            'max_attempts' => $this->max_attempts,
            'shuffle_questions' => $this->shuffle_questions,
            'show_correct_answers' => $this->show_correct_answers,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Relationships
            'lesson' => $this->whenLoaded('lesson'),
        ];
    }

    /**
     * Mask correct answers in questions for students.
     */
    private function maskAnswers(array $questions): array
    {
        return array_map(function ($question) {
            $masked = $question;
            unset($masked['correct_answer']);
            return $masked;
        }, $questions);
    }
}

