<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * UpdateMilestoneRequest
 * 
 * Validates updating a milestone.
 */
class UpdateMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'stage' => ['sometimes', Rule::in(['baby', 'grade_school', 'highschool_college'])],
            'label' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'photo_id' => ['sometimes', 'nullable', 'exists:photos,id'],
        ];
    }
}
