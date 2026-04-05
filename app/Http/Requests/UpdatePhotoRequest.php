<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdatePhotoRequest
 * 
 * Handles validation for updating an existing photo.
 * Note: 'photo' is optional here since users might only update text fields.
 */
class UpdatePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization should be handled by Policy in production
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // 'sometimes' means only validate if the field is present
            'photo' => ['sometimes', 'image', 'max:5120'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}