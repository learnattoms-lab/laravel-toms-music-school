<?php

declare(strict_types=1);

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Store Course Request
 *
 * Validation rules for creating a new course.
 */
class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only teachers can create courses
        return $this->user() && $this->user()->isTeacher();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'instrument' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'price_cents' => ['required', 'integer', 'min:0'],
            'category_id' => ['nullable', 'integer', 'exists:course_category,id'],
            'cover_file_id' => ['nullable', 'integer', 'exists:stored_file,id'],
            'status' => ['nullable', 'string', Rule::in(['draft', 'published', 'archived'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Course title is required.',
            'instrument.required' => 'Instrument is required.',
            'level.required' => 'Skill level is required.',
            'price_cents.required' => 'Price is required.',
            'price_cents.min' => 'Price must be at least 0.',
        ];
    }
}

