<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdatePhotoCommentRequest
 *
 * Validates updating a comment.
 */
class UpdatePhotoCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'body' => ['sometimes', 'string', 'max:1000'],
        ];
    }
}
