<?php

declare(strict_types=1);

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Course Request
 *
 * Validation rules for updating a course.
 */
class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only the course owner (teacher) can update it
        $course = $this->route('course');
        return $this->user() && $this->user()->id === $course->teacher_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'instrument' => ['sometimes', 'string', 'max:255'],
            'level' => ['sometimes', 'string', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'price_cents' => ['sometimes', 'integer', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:course_category,id'],
            'cover_file_id' => ['nullable', 'integer', 'exists:stored_file,id'],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
        ];
    }
}

