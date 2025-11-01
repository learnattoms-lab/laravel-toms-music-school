<?php

declare(strict_types=1);

namespace App\Http\Requests\Session;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update Session Request
 *
 * Validation rules for updating a session.
 */
class UpdateSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only the session tutor can update it
        $session = $this->route('session');
        return $this->user() && $this->user()->id === $session->tutor_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['sometimes', 'integer', 'exists:course,id'],
            'lesson_id' => ['nullable', 'integer', 'exists:lesson,id'],
            'offering_id' => ['nullable', 'integer', 'exists:course_offerings,id'],
            'start_at_utc' => ['sometimes', 'date'],
            'end_at_utc' => ['sometimes', 'date', 'after:start_at_utc'],
            'max_students' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', 'string', 'in:scheduled,ongoing,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'materials_json' => ['nullable', 'array'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:user,id'],
        ];
    }
}

