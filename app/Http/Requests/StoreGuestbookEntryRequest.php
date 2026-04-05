<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreGuestbookEntryRequest
 * 
 * Validates creating a guestbook entry.
 * GuestbookEntry wraps a Post with optional photo.
 */
class StoreGuestbookEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Title and description for the underlying Post
     * Optional photo attachment
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'photo_id' => ['nullable', 'exists:photos,id'],
        ];
    }
}
