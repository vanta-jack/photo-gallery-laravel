<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateAlbumRequest
 * 
 * Validates updating an existing album.
 * All fields are optional since user might update just one field.
 */
class UpdateAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * 'sometimes' = only validate if field is present in request
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_private' => ['sometimes', 'boolean'],
            'cover_photo_id' => ['sometimes', 'nullable', 'exists:photos,id'],
        ];
    }
}
