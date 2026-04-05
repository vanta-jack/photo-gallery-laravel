<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateGuestbookEntryRequest
 * 
 * Validates updating a guestbook entry.
 */
class UpdateGuestbookEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'photo_id' => ['sometimes', 'nullable', 'exists:photos,id'],
        ];
    }
}
