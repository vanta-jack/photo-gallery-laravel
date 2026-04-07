<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateUserRequest
 * 
 * Validates updating user profile.
 */
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Allow updating name, email, profile photo, and CV fields
     * Email must be unique except for current user
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($this->user()->id)],
            'profile_photo_id' => ['sometimes', 'nullable', 'exists:photos,id'],
            
            // CV fields - simple
            'bio' => ['nullable', 'string', 'max:5000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_public' => ['nullable', 'boolean'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'orcid_id' => ['nullable', 'string', 'max:50'],
            'github' => ['nullable', 'url', 'max:255'],
            
            // CV fields - JSON arrays
            'academic_history' => ['nullable', 'array'],
            'academic_history.*.degree' => ['required_with:academic_history', 'string', 'max:255'],
            'academic_history.*.institution' => ['required_with:academic_history', 'string', 'max:255'],
            'academic_history.*.graduation_date' => ['required_with:academic_history', 'date_format:Y-m-d'],
            
            'professional_experience' => ['nullable', 'array'],
            'professional_experience.*.title' => ['required_with:professional_experience', 'string', 'max:255'],
            'professional_experience.*.company' => ['required_with:professional_experience', 'string', 'max:255'],
            'professional_experience.*.start_date' => ['required_with:professional_experience', 'date_format:Y-m-d'],
            'professional_experience.*.end_date' => ['nullable', 'date_format:Y-m-d'],
            'professional_experience.*.description' => ['nullable', 'string', 'max:2000'],
            
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:100'],
            
            'certifications' => ['nullable', 'array'],
            'certifications.*.name' => ['required_with:certifications', 'string', 'max:255'],
            'certifications.*.issuer' => ['nullable', 'string', 'max:255'],
            'certifications.*.awarded_on' => ['required_with:certifications', 'date_format:Y-m-d'],
            'certifications.*.photo_id' => ['nullable', 'exists:photos,id'],
            
            'other_links' => ['nullable', 'array'],
            'other_links.*.label' => ['required_with:other_links', 'string', 'max:100'],
            'other_links.*.url' => ['required_with:other_links', 'url', 'max:255'],
        ];
    }
}
