<?php

declare(strict_types=1);

namespace App\Http\Requests\Session;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store Session Request
 *
 * Validation rules for creating a new session.
 */
class StoreSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only teachers can create sessions
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
            'course_id' => ['required', 'integer', 'exists:course,id'],
            'lesson_id' => ['nullable', 'integer', 'exists:lesson,id'],
            'offering_id' => ['nullable', 'integer', 'exists:course_offerings,id'],
            'start_at_utc' => ['required', 'date', 'after:now'],
            'end_at_utc' => ['required', 'date', 'after:start_at_utc'],
            'max_students' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', 'string', 'in:scheduled,ongoing,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'materials_json' => ['nullable', 'array'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:user,id'],
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
            'course_id.required' => 'Course is required.',
            'start_at_utc.required' => 'Start time is required.',
            'start_at_utc.after' => 'Start time must be in the future.',
            'end_at_utc.required' => 'End time is required.',
            'end_at_utc.after' => 'End time must be after start time.',
        ];
    }
}

