<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Update Profile Request
 *
 * Validation rules for updating user profile.
 */
class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Users can only update their own profile
        return $this->user() && $this->user()->id === (int) $this->route('user', $this->user()->id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'instrument' => ['nullable', 'string', 'max:255'],
            'skill_level' => ['nullable', 'string', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'bio' => ['nullable', 'string', 'max:5000'],
            'profile_picture' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'preferences' => ['nullable', 'array'],
            'teacher_bio' => ['nullable', 'string', 'max:5000'],
            'teacher_specialties' => ['nullable', 'array'],
            'teacher_certifications' => ['nullable', 'array'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'availability' => ['nullable', 'array'],
        ];
    }
}

