<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreMilestoneRequest
 * 
 * Validates creating a milestone (life event tracker).
 */
class StoreMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * stage: Must be one of the defined enum values
     * label: Required descriptor (e.g., "Month 3", "Grade 2")
     * description: Optional narrative
     * photo_id: Optional photo attachment
     */
    public function rules(): array
    {
        return [
            'stage' => ['required', Rule::in(['baby', 'grade_school', 'highschool_college'])],
            'label' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo_id' => ['nullable', 'exists:photos,id'],
        ];
    }
}
