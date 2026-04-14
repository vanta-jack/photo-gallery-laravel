<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreAlbumRequest
 *
 * Validates creating a new album.
 * Albums group photos with optional privacy settings.
 */
class StoreAlbumRequest extends FormRequest
{
    /**
     * Only authenticated users can create albums
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Validation rules:
     * - title: Required, max 255 chars
     * - description: Optional text
     * - is_private: Optional boolean, defaults to false in model
     * - cover_photo_id: Optional, must exist in photos table if provided
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_private' => ['nullable', 'boolean'],
            'cover_photo_id' => [
                'nullable',
                'integer',
                Rule::exists('photos', 'id')->where(static fn ($query) => $query->where('user_id', $userId)),
            ],
            'photo_ids' => ['nullable', 'array'],
            'photo_ids.*' => [
                'integer',
                Rule::exists('photos', 'id')->where(static fn ($query) => $query->where('user_id', $userId)),
            ],
            'main_photo_pick' => ['nullable', 'string', 'regex:/^(existing:\d+|upload:\d+)$/'],
            'photo' => ['nullable', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
            'photos' => ['nullable', 'array', 'min:1'],
            'photos.*' => ['required', 'string', 'regex:/^data:image\/(webp|png|jpeg|jpg);base64,/'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $prepared = [];
        $coverPhotoId = $this->input('cover_photo_id');

        if (
            ($coverPhotoId !== null && $coverPhotoId !== '')
            && (! $this->has('photo_ids') || ! is_array($this->input('photo_ids')))
        ) {
            $prepared['photo_ids'] = [(int) $coverPhotoId];
        }

        if (($coverPhotoId !== null && $coverPhotoId !== '') && ! $this->filled('main_photo_pick')) {
            $prepared['main_photo_pick'] = 'existing:'.(int) $coverPhotoId;
        }

        if ($prepared !== []) {
            $this->merge($prepared);
        }
    }
}
